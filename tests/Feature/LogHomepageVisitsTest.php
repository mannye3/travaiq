<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LogPageVisitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_visit_creates_new_user_and_session()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('visitor_logs', [
            'ip_address' => '127.0.0.1',
            'path' => '/',
            'is_new_user' => true
        ]);
        
        // Should have client_id and session_id
        $log = VisitorLog::first();
        $this->assertNotNull($log->client_id);
        $this->assertNotNull($log->session_id);
        $this->assertTrue($log->is_new_user);
    }

    public function test_same_session_does_not_log_again()
    {
        // First visit
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 1);

        // Visit other pages in same session
        $this->get('/about');
        $this->get('/contact');
        $this->assertDatabaseCount('visitor_logs', 1); // Should still be 1
    }

    public function test_new_session_after_30_minutes_inactivity()
    {
        // First visit
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 1);
        $firstLog = VisitorLog::first();

        // Travel 31 minutes into the future (simulating session timeout)
        $this->travel(31)->minutes();

        // Visit again (new session)
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 2);
        
        $secondLog = VisitorLog::orderBy('id', 'desc')->first();
        $this->assertEquals($firstLog->client_id, $secondLog->client_id); // Same user
        $this->assertNotEquals($firstLog->session_id, $secondLog->session_id); // Different session
        $this->assertFalse($secondLog->is_new_user); // Returning user
    }

    public function test_new_browser_creates_new_user()
    {
        // First visit
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 1);
        $firstLog = VisitorLog::first();

        // Simulate new browser by clearing cookies
        $this->withoutCookie('_ga_client_id');

        // Visit again (new browser)
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 2);
        
        $secondLog = VisitorLog::orderBy('id', 'desc')->first();
        $this->assertNotEquals($firstLog->client_id, $secondLog->client_id); // Different client
        $this->assertTrue($secondLog->is_new_user); // New user
    }

    public function test_post_requests_are_not_logged()
    {
        $this->post('/');
        $this->assertDatabaseCount('visitor_logs', 0);
    }

    public function test_asset_files_are_not_logged()
    {
        $this->get('/css/app.css');
        $this->get('/js/app.js');
        $this->get('/favicon.ico');
        $this->assertDatabaseCount('visitor_logs', 0);
    }

    public function test_client_id_persistence_across_requests()
    {
        // First visit
        $this->get('/');
        $firstLog = VisitorLog::first();
        $clientId = $firstLog->client_id;

        // Second visit (same session)
        $this->get('/about');
        
        // Third visit (new session after timeout)
        $this->travel(31)->minutes();
        $this->get('/contact');
        
        $allLogs = VisitorLog::all();
        $this->assertEquals(2, $allLogs->count());
        
        // All logs should have same client_id
        foreach ($allLogs as $log) {
            $this->assertEquals($clientId, $log->client_id);
        }
    }

    public function test_session_activity_updates()
    {
        // First visit
        $this->get('/');
        $this->assertDatabaseCount('visitor_logs', 1);

        // Visit multiple pages within session
        $this->get('/about');
        $this->get('/contact');
        $this->get('/');
        
        // Should still only have 1 log entry
        $this->assertDatabaseCount('visitor_logs', 1);
        
        // But session should be updated with activity
        $this->assertTrue(session()->has('ga_session_' . VisitorLog::first()->client_id));
    }

    public function test_ga4_style_client_id_format()
    {
        $this->get('/');
        $log = VisitorLog::first();
        
        // Should match GA4 format: GA1.2.{random_id}.{timestamp}
        $this->assertMatchesRegularExpression('/^GA1\.2\.\d{9}\.\d{10}$/', $log->client_id);
    }
} 