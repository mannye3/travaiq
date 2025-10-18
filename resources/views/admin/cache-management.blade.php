@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cache Management</h4>
                    <p class="text-muted">Manage temporary travel plan cache</p>
                </div>
                <div class="card-body">
                    <!-- Cache Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Cache Status</h6>
                                    <div id="cacheStatus">
                                        <p>Loading cache status...</p>
                                    </div>
                                    <button class="btn btn-sm btn-info" onclick="checkCacheStatus()">
                                        <i class="fas fa-sync"></i> Refresh Status
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Actions</h6>
                                    <button class="btn btn-warning btn-sm mb-2" onclick="clearExpiredCache()">
                                        <i class="fas fa-trash"></i> Clear Expired Cache
                                    </button>
                                    <br>
                                    <button class="btn btn-danger btn-sm mb-2" onclick="clearAllCache()">
                                        <i class="fas fa-bomb"></i> Clear All Cache
                                    </button>
                                    <br>
                                    <button class="btn btn-info btn-sm" onclick="showUserCacheInfo()">
                                        <i class="fas fa-user"></i> User Cache Info
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cache Details -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Cached Travel Plans</h6>
                                </div>
                                <div class="card-body">
                                    <div id="cacheDetails">
                                        <p>Loading cache details...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cache Expiration Modal -->
<div class="modal fade" id="cacheExpirationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Cache Expiration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cacheExpirationForm">
                    <div class="mb-3">
                        <label for="hours" class="form-label">Expiration Time (hours)</label>
                        <input type="number" class="form-control" id="hours" name="hours" min="1" max="24" value="1">
                        <div class="form-text">Set how long the cache should last (1-24 hours)</div>
                    </div>
                    <input type="hidden" id="referenceCode" name="referenceCode">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="setCacheExpiration()">Set Expiration</button>
            </div>
        </div>
    </div>
</div>

<!-- User Cache Info Modal -->
<div class="modal fade" id="userCacheInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Cache Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="userId" class="form-label">User ID</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="userId" placeholder="Enter user ID">
                        <button class="btn btn-primary" onclick="getUserCacheInfo()">
                            <i class="fas fa-search"></i> Get Info
                        </button>
                    </div>
                </div>
                <div id="userCacheInfo">
                    <p class="text-muted">Enter a user ID to view their cache information.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let cacheData = [];

function checkCacheStatus() {
    fetch('/check-cache-status')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showAlert('Error: ' + data.error, 'danger');
                return;
            }
            
            cacheData = data.cache_status || [];
            displayCacheStatus(data);
            displayCacheDetails();
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to check cache status', 'danger');
        });
}

function displayCacheStatus(data) {
    const statusDiv = document.getElementById('cacheStatus');
    statusDiv.innerHTML = `
        <div class="row">
            <div class="col-4">
                <strong>Total Cached Plans:</strong><br>
                <span class="badge bg-primary">${data.total_cached_plans}</span>
            </div>
            <div class="col-4">
                <strong>Temp Plans:</strong><br>
                <span class="badge bg-info">${data.temp_plans}</span>
            </div>
            <div class="col-4">
                <strong>Auth Plans:</strong><br>
                <span class="badge bg-success">${data.auth_plans}</span>
            </div>
        </div>
    `;
}

function displayCacheDetails() {
    const detailsDiv = document.getElementById('cacheDetails');
    
    if (cacheData.length === 0) {
        detailsDiv.innerHTML = '<p class="text-muted">No cached travel plans found.</p>';
        return;
    }
    
            let html = '<div class="table-responsive"><table class="table table-striped">';
        html += '<thead><tr><th>Cache Key</th><th>Type</th><th>Status</th><th>Data Keys</th><th>Actions</th></tr></thead><tbody>';
        
        cacheData.forEach(item => {
            const statusBadge = item.has_data ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-warning">Expired</span>';
            
            const typeBadge = item.type === 'authenticated' ? 
                '<span class="badge bg-primary">Auth</span>' : 
                '<span class="badge bg-info">Temp</span>';
            
            const dataKeys = item.data_keys ? item.data_keys.join(', ') : 'None';
            
            html += `
                <tr>
                    <td><code>${item.key}</code></td>
                    <td>${typeBadge}</td>
                    <td>${statusBadge}</td>
                    <td><small>${dataKeys}</small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="refreshCache('${item.key}')">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="openExpirationModal('${item.key}')">
                            <i class="fas fa-clock"></i> Expire
                        </button>
                    </td>
                </tr>
            `;
        });
    
    html += '</tbody></table></div>';
    detailsDiv.innerHTML = html;
}

function clearExpiredCache() {
    if (!confirm('Are you sure you want to clear expired cache?')) return;
    
    fetch('/clear-expired-cache')
        .then(response => {
            if (response.ok) {
                showAlert('Expired cache cleared successfully', 'success');
                setTimeout(() => checkCacheStatus(), 1000);
            } else {
                showAlert('Failed to clear expired cache', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to clear expired cache', 'danger');
        });
}

function clearAllCache() {
    if (!confirm('Are you sure you want to clear ALL cache? This will affect all users.')) return;
    
    fetch('/clear-expired-cache')
        .then(response => {
            if (response.ok) {
                showAlert('All cache cleared successfully', 'success');
                setTimeout(() => checkCacheStatus(), 1000);
            } else {
                showAlert('Failed to clear all cache', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to clear all cache', 'danger');
        });
}

function refreshCache(cacheKey) {
    if (!confirm('Are you sure you want to refresh this cache?')) return;
    
    // Extract reference code from cache key
    const referenceCode = cacheKey.replace('temp_travel_plan_', '');
    
    fetch(`/refresh-cache/${referenceCode}`)
        .then(response => {
            if (response.ok) {
                showAlert('Cache refreshed successfully', 'success');
                setTimeout(() => checkCacheStatus(), 1000);
            } else {
                showAlert('Failed to refresh cache', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to refresh cache', 'danger');
        });
}

function openExpirationModal(cacheKey) {
    const referenceCode = cacheKey.replace('temp_travel_plan_', '');
    document.getElementById('referenceCode').value = referenceCode;
    new bootstrap.Modal(document.getElementById('cacheExpirationModal')).show();
}

function setCacheExpiration() {
    const referenceCode = document.getElementById('referenceCode').value;
    const hours = document.getElementById('hours').value;
    
    fetch(`/set-cache-expiration/${referenceCode}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ hours: parseInt(hours) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('cacheExpirationModal')).hide();
            setTimeout(() => checkCacheStatus(), 1000);
        } else {
            showAlert(data.message, 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to set cache expiration', 'danger');
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showUserCacheInfo() {
    new bootstrap.Modal(document.getElementById('userCacheInfoModal')).show();
}

function getUserCacheInfo() {
    const userId = document.getElementById('userId').value;
    if (!userId) {
        showAlert('Please enter a user ID', 'warning');
        return;
    }
    
    fetch(`/user-cache-info/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showAlert('Error: ' + data.error, 'danger');
                return;
            }
            
            displayUserCacheInfo(data);
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to get user cache info', 'danger');
        });
}

function displayUserCacheInfo(data) {
    const infoDiv = document.getElementById('userCacheInfo');
    
    if (data.total_cached_plans === 0) {
        infoDiv.innerHTML = '<p class="text-muted">No cached plans found for this user.</p>';
        return;
    }
    
    let html = `
        <div class="alert alert-info">
            <strong>User ID:</strong> ${data.user_id}<br>
            <strong>Total Cached Plans:</strong> ${data.total_cached_plans}
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Duration</th>
                        <th>Traveler</th>
                        <th>Budget</th>
                        <th>Cached At</th>
                        <th>TTL Remaining</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.cached_plans.forEach(plan => {
        html += `
            <tr>
                <td>${plan.location}</td>
                <td>${plan.duration}</td>
                <td>${plan.traveler}</td>
                <td>${plan.budget}</td>
                <td>${plan.cached_at}</td>
                <td>${plan.ttl_remaining}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    infoDiv.innerHTML = html;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    checkCacheStatus();
    
    // Auto-refresh every 30 seconds
    setInterval(checkCacheStatus, 30000);
});
</script>
@endpush 