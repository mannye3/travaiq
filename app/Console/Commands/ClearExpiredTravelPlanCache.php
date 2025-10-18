<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearExpiredTravelPlanCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-expired-travel-plans {--force : Force clear all cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired temporary travel plan cache entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        if ($force) {
            $this->info('Force clearing all travel plan cache...');
            $this->clearAllCache();
            return 0;
        }

        $this->info('Checking for expired travel plan cache...');
        
        try {
            $keys = Cache::get('temp_travel_plan_cache_keys', []);
            $clearedCount = 0;
            $activeCount = 0;
            
            if (empty($keys)) {
                $this->info('No cached travel plans found.');
                return 0;
            }
            
            foreach ($keys as $key) {
                $data = Cache::get($key);
                if ($data) {
                    $activeCount++;
                } else {
                    $clearedCount++;
                }
            }
            
            // Clear expired entries
            $this->clearExpiredCache();
            
            $this->info("Cache cleanup completed:");
            $this->info("- Active cache entries: {$activeCount}");
            $this->info("- Expired entries cleared: {$clearedCount}");
            
            Log::info('Expired travel plan cache cleared', [
                'active_count' => $activeCount,
                'cleared_count' => $clearedCount
            ]);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error clearing expired cache: ' . $e->getMessage());
            Log::error('Failed to clear expired travel plan cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Clear expired cache entries
     */
    private function clearExpiredCache()
    {
        // Clear temp travel plan cache
        $tempKeys = Cache::get('temp_travel_plan_cache_keys', []);
        $activeTempKeys = [];
        
        foreach ($tempKeys as $key) {
            $data = Cache::get($key);
            if ($data) {
                $activeTempKeys[] = $key;
            } else {
                Cache::forget($key);
            }
        }
        
        // Update the temp keys list with only active entries
        Cache::put('temp_travel_plan_cache_keys', $activeTempKeys, 3600);
        
        // Clear authenticated user cache
        $authKeys = Cache::get('auth_travel_plan_cache_keys', []);
        $activeAuthKeys = [];
        
        foreach ($authKeys as $key) {
            $data = Cache::get($key);
            if ($data) {
                $activeAuthKeys[] = $key;
            } else {
                Cache::forget($key);
            }
        }
        
        // Update the auth keys list with only active entries
        Cache::put('auth_travel_plan_cache_keys', $activeAuthKeys, 7200);
    }

    /**
     * Clear all cache entries
     */
    private function clearAllCache()
    {
        // Clear temp travel plan cache
        $tempKeys = Cache::get('temp_travel_plan_cache_keys', []);
        foreach ($tempKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget('temp_travel_plan_cache_keys');
        
        // Clear authenticated user cache
        $authKeys = Cache::get('auth_travel_plan_cache_keys', []);
        foreach ($authKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget('auth_travel_plan_cache_keys');
        
        $this->info('All travel plan cache (temp and authenticated) cleared successfully.');
        
        Log::info('All travel plan cache cleared manually');
    }
} 