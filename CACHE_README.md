# Travel Plan Cache System

This document explains how the cache system works for temporary travel plans in the Travaiq application.

## Overview

The cache system stores travel plan data for both temporary users (not logged in) and authenticated users, preventing repeated API calls to external services (Google Places, Agoda) when refreshing the page or generating similar plans.

## How It Works

### 1. Cache Storage
- **Temporary Users Cache Key Format**: `temp_travel_plan_{md5_hash_of_reference_code}`
- **Authenticated Users Cache Key Format**: `auth_travel_plan_{user_id}_{md5_hash_of_location_duration_traveler_budget}`
- **TTL**: 
  - Temporary plans: 1 hour (3600 seconds)
  - Authenticated plans: 2 hours (7200 seconds)
- **Storage**: Uses Laravel's cache system (Redis recommended for production)

### 2. Cache Lifecycle
1. User generates a travel plan
2. Data is stored in session and cache
3. On page refresh or similar plan generation, cache is checked first
4. If cache exists, data is served immediately
5. If no cache, data is regenerated and cached

**For Authenticated Users:**
- Cache is based on location, duration, traveler type, and budget combination
- Same user can generate multiple plans for different locations/parameters
- Cache is shared across sessions for the same user

### 3. What Gets Cached
- Trip details
- Location overview
- Security advice
- Hotel recommendations
- Itineraries with activities
- Additional information
- Google Places images
- Agoda city ID

## Benefits

- **Faster Page Loads**: No repeated API calls on refresh
- **Better User Experience**: Instant page loads after initial generation
- **Reduced API Costs**: Fewer external API requests
- **Better Performance**: Reduced server load
- **Smart Caching**: Authenticated users benefit from location-based caching
- **Session Independence**: Cache persists across browser sessions for logged-in users

## Cache Management

### Admin Interface
Access the cache management interface at: `/admin/cache-management`

Features:
- View cache status
- Monitor cached plans
- Clear expired cache
- Set custom expiration times
- Refresh individual cache entries

### Artisan Commands

#### Check Cache Status
```bash
php artisan cache:clear-expired-travel-plans
```

#### Force Clear All Cache
```bash
php artisan cache:clear-expired-travel-plans --force
```

#### Check User Cache Info
```bash
# Via API endpoint
GET /user-cache-info/{userId}
```

### Scheduled Tasks
The system automatically clears expired cache every hour using Laravel's task scheduler.

## API Endpoints

### Cache Status
- **GET** `/check-cache-status` - Get cache status and details

### Cache Management
- **GET** `/clear-expired-cache` - Clear expired cache entries
- **GET** `/refresh-cache/{referenceCode}` - Refresh specific cache entry
- **POST** `/set-cache-expiration/{referenceCode}` - Set custom expiration time
- **GET** `/user-cache-info/{userId}` - Get cache information for specific user
- **GET** `/refresh-auth-cache/{userId}/{location}/{duration}/{traveler}/{budget}` - Refresh authenticated user cache

## Configuration

### Cache Driver
The system uses Laravel's default cache driver. For production, configure Redis:

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Cache TTL
Default TTL is 1 hour. You can modify this in the `TravelController::showTemp()` method:

```php
Cache::put($cacheKey, $viewData, 3600); // 1 hour
```

## Monitoring

### Logs
Cache operations are logged with the following context:
- Cache hits/misses
- Cache creation
- Cache expiration
- Cache clearing operations

### Metrics
Track cache performance:
- Cache hit rate
- Average response time
- Memory usage
- Expired entries count

## Troubleshooting

### Common Issues

1. **Cache Not Working**
   - Check cache driver configuration
   - Verify Redis connection (if using Redis)
   - Check cache permissions

2. **Memory Issues**
   - Reduce cache TTL
   - Implement cache size limits
   - Use cache tags for better organization

3. **Performance Issues**
   - Monitor cache hit rates
   - Check for cache key conflicts
   - Optimize cache key generation

### Debug Commands

```bash
# Check cache driver
php artisan config:show cache

# Clear all application cache
php artisan cache:clear

# Check Redis connection (if using Redis)
redis-cli ping
```

## Best Practices

1. **Cache Key Naming**: Use descriptive, unique keys
2. **TTL Management**: Set appropriate expiration times
3. **Memory Monitoring**: Monitor cache memory usage
4. **Regular Cleanup**: Schedule regular cache cleanup
5. **Error Handling**: Implement fallbacks for cache failures

## Future Enhancements

- Cache compression for large datasets
- Cache warming strategies
- Distributed cache support
- Cache analytics dashboard
- Automatic cache optimization

## Support

For issues or questions about the cache system, check:
1. Laravel cache documentation
2. Redis documentation (if using Redis)
3. Application logs
4. Cache management interface 