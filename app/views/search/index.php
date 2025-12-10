<?php
$title = "Vehicle Search";
ob_start();
?>

<div class="container-fluid">
    <!-- Search Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-search me-2"></i>Vehicle Search
                    </h1>
                    <p class="text-muted mb-0">Search vehicles by VIN or plate number</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Search</h5>
                </div>
                <div class="card-body">
                    <form id="quickSearchForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="searchType" class="form-label">Search Type</label>
                                    <select class="form-select" id="searchType" name="searchType" required>
                                        <option value="">Select search type</option>
                                        <option value="vin">VIN Number</option>
                                        <option value="plate">Plate Number</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="searchTerm" class="form-label">Search Term</label>
                                    <input type="text" class="form-control" id="searchTerm" name="searchTerm" 
                                           placeholder="Enter VIN or plate number" required>
                                    <div class="form-text" id="searchHelp">
                                        Enter the 17-character VIN or vehicle plate number
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100" id="searchBtn">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Search Results</h5>
                </div>
                <div class="card-body">
                    <div id="searchResults">
                        <div class="text-center py-5">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No search performed yet</h5>
                            <p class="text-muted">Use the search forms above to find vehicles</p>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Search results pagination" id="paginationContainer" class="d-none">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Search History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Search History</h5>
                </div>
                <div class="card-body ">
                    <?php if(isset($search_history) && $search_history): ?>  
                        <div class="list-group list-group-flush">
                            <?php foreach($search_history as $history): ?>
                                <div class="list-group-item bg-dark">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <span class="badge bg-secondary me-2"><?= $history['search_type'] ?></span>
                                            <?= $history['search_term'] ?>
                                        </h6>
                                        <small class="text-muted"><?= $history['created_at'] ?></small>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        Vehicle found • <a href="<?= url('search/vehicle-profile/'.$history['vin']) ?>" class="text-primary">View Vehicle</a>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <!-- If pagination exists with multiple pages -->
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            Showing 5 of <?= $search_count ?> searches
                        </small>
                    </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-clock-history display-1 text-muted"></i>
                            <p class="text-muted mt-2">No search history found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Details Modal -->
<div class="modal fade" id="vehicleDetailsModal" tabindex="-1" aria-labelledby="vehicleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleDetailsModalLabel">Vehicle Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="vehicleDetailsContent">
                <!-- Vehicle details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="viewFullProfileBtn">View Full Profile</a>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
});

function initializeSearch() {
    const quickSearchForm = document.getElementById('quickSearchForm');
    const searchType = document.getElementById('searchType');
    const searchTerm = document.getElementById('searchTerm');

    // Quick search form handler
    quickSearchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performQuickSearch();
    });



    // Search type change handler
    searchType.addEventListener('change', function() {
        updateSearchHelpText();
    });


    // Real-time validation for VIN
    searchTerm.addEventListener('input', function() {
        if (searchType.value === 'vin') {
            validateVIN(this.value);
        }
    });
}

function updateSearchHelpText() {
    const searchType = document.getElementById('searchType').value;
    const helpText = document.getElementById('searchHelp');
    
    if (searchType === 'vin') {
        helpText.textContent = 'Enter the 17-character Vehicle Identification Number';
    } else if (searchType === 'plate') {
        helpText.textContent = 'Enter the vehicle plate number';
    } else {
        helpText.textContent = 'Enter the VIN or plate number';
    }
}

function validateVIN(vin) {
    // Basic VIN validation - 17 alphanumeric characters
    const vinRegex = /^[A-HJ-NPR-Z0-9]{17}$/i;
    const isValid = vinRegex.test(vin);
    
    const searchTerm = document.getElementById('searchTerm');
    if (vin.length > 0) {
        if (isValid) {
            searchTerm.classList.remove('is-invalid');
            searchTerm.classList.add('is-valid');
        } else {
            searchTerm.classList.remove('is-valid');
            searchTerm.classList.add('is-invalid');
        }
    } else {
        searchTerm.classList.remove('is-valid', 'is-invalid');
    }
    
    return isValid;
}

function performQuickSearch() {
    const searchType = document.getElementById('searchType').value;
    const searchTerm = document.getElementById('searchTerm').value.trim();
    const searchBtn = document.getElementById('searchBtn');
    
    if (!searchType || !searchTerm) {
        App.showToast('Please select search type and enter search term', 'error');
        return;
    }
    
    // Validate VIN if searching by VIN
    if (searchType === 'vin' && !validateVIN(searchTerm)) {
        App.showToast('Please enter a valid 17-character VIN', 'error');
        return;
    }
    
    // Disable search button
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Searching...';
    
    // Show loading state
    const resultsContainer = document.getElementById('searchResults');
    resultsContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner"></div>
            <h5 class="text-muted mt-3">Searching...</h5>
            <p class="text-muted">Please wait while we search for the vehicle</p>
        </div>
    `;
    
    // Hide pagination
    document.getElementById('paginationContainer').classList.add('d-none');
    
    // Perform AJAX search
    Ajax.searchVehicle(searchTerm, searchType)
        .then(vehicle => {
            displayQuickSearchResults(vehicle);
        })
        .catch(error => {
            console.error('Search error:', error);
            resultsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                    <h5 class="text-danger mt-3">Search Failed</h5>
                    <p class="text-muted">${error.message || 'An error occurred while searching'}</p>
                    <button class="btn btn-primary mt-2" onclick="performQuickSearch()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Try Again
                    </button>
                </div>
            `;
        })
        .finally(() => {
            // Re-enable search button
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="bi bi-search me-1"></i> Search';
        });
}

function displayQuickSearchResults(vehicle) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (vehicle.error) {
        resultsContainer.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h5 class="text-muted mt-3">Vehicle Not Found</h5>
                <p class="text-muted">No vehicle found with the specified criteria</p>
                <button class="btn btn-primary mt-2" onclick="toggleAdvancedSearch()">
                    <i class="bi bi-search me-1"></i> Try Advanced Search
                </button>
            </div>
        `;
        return;
    }
    
    const statusBadges = {
        'none': 'bg-success',
        'stolen': 'bg-danger',
        'no_customs_duty': 'bg-warning',
        'changed_engine': 'bg-info',
        'changed_color': 'bg-secondary'
    };
    
    const statusLabels = {
        'none': 'Normal',
        'stolen': 'Stolen',
        'no_customs_duty': 'No Customs Duty',
        'changed_engine': 'Changed Engine',
        'changed_color': 'Changed Color'
    };
    
    resultsContainer.innerHTML = `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">
                            ${vehicle.vehicle?.make} ${vehicle.vehicle?.model} (${vehicle.vehicle?.year})
                            <span class="badge ${statusBadges[vehicle.vehicle?.current_status] || 'bg-secondary'} ms-2">
                                ${statusLabels[vehicle.vehicle?.current_status] || vehicle.vehicle?.current_status}
                            </span>
                        </h5>
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <strong>VIN:</strong> ${vehicle.vehicle?.vin}
                            </div>
                            <div class="col-sm-6">
                                <strong>Current Plate:</strong> ${vehicle.vehicle?.current_plate_number || 'N/A'}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <strong>Make:</strong> ${vehicle.vehicle?.make}
                            </div>
                            <div class="col-sm-6">
                                <strong>Model:</strong> ${vehicle.vehicle?.model}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <strong>Year:</strong> ${vehicle.vehicle?.year}
                            </div>
                            <div class="col-sm-6">
                                <strong>Registered:</strong> ${new Date(vehicle.vehicle?.created_at).toLocaleDateString()}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <a href="${appUrl}/search/vehicle-profile/${vehicle.vehicle?.vin}" class="btn btn-outline-primary">
                                <i class="bi bi-info-circle me-1"></i> Full Profile
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Current Owner Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2">Current Owner</h6>
                        <div class="row">
                            <div class="col-sm-4">
                                <strong>Name:</strong> ${vehicle.current_owner?.name || 'N/A'}
                            </div>
                            <div class="col-sm-4">
                                <strong>Email:</strong> 
                                ${vehicle.current_owner?.email ? 
                                    `<a href="mailto:${vehicle.current_owner.email}">${vehicle.current_owner.email}</a>` : 
                                    'N/A'}
                            </div>
                            <div class="col-sm-4">
                                <strong>Phone:</strong> 
                                ${vehicle.current_owner?.phone ? 
                                    `<a href="tel:${vehicle.current_owner.phone}">${vehicle.current_owner.phone}</a>` : 
                                    'N/A'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>