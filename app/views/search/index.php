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

    <!-- Advanced Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Advanced Search</h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#advancedSearchCollapse">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse" id="advancedSearchCollapse">
                    <div class="card-body">
                        <form id="advancedSearchForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="vin" class="form-label">VIN Number</label>
                                        <input type="text" class="form-control" id="vin" name="vin" 
                                               placeholder="Enter VIN">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="plate_number" class="form-label">Plate Number</label>
                                        <input type="text" class="form-control" id="plate_number" name="plate_number" 
                                               placeholder="Enter plate number">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="make" class="form-label">Make</label>
                                        <input type="text" class="form-control" id="make" name="make" 
                                               placeholder="Vehicle make">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="model" name="model" 
                                               placeholder="Vehicle model">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Year</label>
                                        <input type="number" class="form-control" id="year" name="year" 
                                               placeholder="Manufacture year" min="1900" max="<?= date('Y'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All Status</option>
                                            <option value="none">Normal</option>
                                            <option value="stolen">Stolen</option>
                                            <option value="no_customs_duty">No Customs Duty</option>
                                            <option value="changed_engine">Changed Engine</option>
                                            <option value="changed_color">Changed Color</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="owner_identifier" class="form-label">Owner (Email/Phone/NIN)</label>
                                        <input type="text" class="form-control" id="owner_identifier" name="owner_identifier" 
                                               placeholder="Search by owner">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-outline-primary" id="advancedSearchBtn">
                                                <i class="bi bi-search me-1"></i> Advanced Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <select class="form-select form-select-sm" id="resultsPerPage">
                                <option value="10">10 per page</option>
                                <option value="15">15 per page</option>
                                <option value="20">20 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-outline-success" id="exportResultsBtn">
                            <i class="bi bi-download me-1"></i> Export CSV
                        </button>
                    </div>
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
                    <button class="btn btn-sm btn-outline-secondary" id="refreshHistoryBtn">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="searchHistory">
                        <div class="text-center py-3">
                            <div class="loading-spinner"></div>
                            <p class="text-muted mt-2">Loading search history...</p>
                        </div>
                    </div>
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
    
    // Load search history
    loadSearchHistory();
});

function initializeSearch() {
    const quickSearchForm = document.getElementById('quickSearchForm');
    const advancedSearchForm = document.getElementById('advancedSearchForm');
    const searchType = document.getElementById('searchType');
    const searchTerm = document.getElementById('searchTerm');
    const resultsPerPage = document.getElementById('resultsPerPage');
    const exportResultsBtn = document.getElementById('exportResultsBtn');
    const refreshHistoryBtn = document.getElementById('refreshHistoryBtn');

    // Quick search form handler
    quickSearchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performQuickSearch();
    });

    // Advanced search form handler
    advancedSearchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performAdvancedSearch();
    });

    // Search type change handler
    searchType.addEventListener('change', function() {
        updateSearchHelpText();
    });

    // Results per page change handler
    resultsPerPage.addEventListener('change', function() {
        if (window.currentSearchType === 'advanced') {
            performAdvancedSearch(1, this.value);
        }
    });

    // Export results handler
    exportResultsBtn.addEventListener('click', function() {
        exportSearchResults();
    });

    // Refresh history handler
    refreshHistoryBtn.addEventListener('click', function() {
        loadSearchHistory();
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
                            <button class="btn btn-primary" onclick="viewVehicleDetails(${vehicle.vehicle?.id})">
                                <i class="bi bi-eye me-1"></i> View Details
                            </button>
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

function toggleAdvancedSearch() {
    const collapse = document.getElementById('advancedSearchCollapse');
    const bsCollapse = new bootstrap.Collapse(collapse, {
        toggle: true
    });
}

function performAdvancedSearch(page = 1, per_page = null) {
    const form = document.getElementById('advancedSearchForm');
    const formData = new FormData(form);
    const searchBtn = document.getElementById('advancedSearchBtn');
    
    // Disable search button
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Searching...';
    
    // Show loading state
    const resultsContainer = document.getElementById('searchResults');
    resultsContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner"></div>
            <h5 class="text-muted mt-3">Searching...</h5>
            <p class="text-muted">Please wait while we search for vehicles</p>
        </div>
    `;
    
    // Build search parameters
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        if (value.trim()) {
            params.append(key, value.trim());
        }
    }
    params.append('page', page);
    params.append('per_page', per_page || document.getElementById('resultsPerPage').value);
    
    // Set current search type
    window.currentSearchType = 'advanced';
    
    // Perform AJAX search
    Ajax.advancedSearch(params.toString())
        .then(response => {
            displayAdvancedSearchResults(response);
        })
        .catch(error => {
            console.error('Advanced search error:', error);
            resultsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                    <h5 class="text-danger mt-3">Search Failed</h5>
                    <p class="text-muted">${error.message || 'An error occurred while searching'}</p>
                    <button class="btn btn-primary mt-2" onclick="performAdvancedSearch()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Try Again
                    </button>
                </div>
            `;
        })
        .finally(() => {
            // Re-enable search button
            searchBtn.disabled = false;
            searchBtn.innerHTML = '<i class="bi bi-search me-1"></i> Advanced Search';
        });
}

function displayAdvancedSearchResults(response) {
    const resultsContainer = document.getElementById('searchResults');
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    
    if (!response.vehicles || response.vehicles.length === 0) {
        resultsContainer.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Vehicles Found</h5>
                <p class="text-muted">No vehicles match your search criteria</p>
                <button class="btn btn-primary mt-2" onclick="clearAdvancedSearch()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Clear Filters
                </button>
            </div>
        `;
        paginationContainer.classList.add('d-none');
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
    
    let resultsHtml = '';
    
    response.vehicles.forEach(vehicle => {
        resultsHtml += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">
                                ${vehicle.make} ${vehicle.model} (${vehicle.year})
                                <span class="badge ${statusBadges[vehicle.current_status] || 'bg-secondary'} ms-2">
                                    ${statusLabels[vehicle.current_status] || vehicle.current_status}
                                </span>
                            </h5>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <strong>VIN:</strong> ${vehicle.vin}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Plate:</strong> ${vehicle.current_plate_number || 'N/A'}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <strong>Owner:</strong> ${vehicle.owner_name || 'N/A'}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Registered:</strong> ${new Date(vehicle.created_at).toLocaleDateString()}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" onclick="viewVehicleDetails(${vehicle.id})">
                                    <i class="bi bi-eye me-1"></i> Quick View
                                </button>
                                <a href="/search/vehicle/${vehicle.id}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i> Full Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    resultsContainer.innerHTML = resultsHtml;
    
    // Update pagination
    if (response.pagination.total_pages > 1) {
        generatePagination(response.pagination);
        paginationContainer.classList.remove('d-none');
    } else {
        paginationContainer.classList.add('d-none');
    }
}

function generatePagination(pagination) {
    const paginationElement = document.getElementById('pagination');
    const currentPage = pagination.page;
    const totalPages = pagination.total_pages;
    
    let paginationHtml = '';
    
    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="performAdvancedSearch(${currentPage - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="performAdvancedSearch(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Next button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="performAdvancedSearch(${currentPage + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    paginationElement.innerHTML = paginationHtml;
}

function clearAdvancedSearch() {
    document.getElementById('advancedSearchForm').reset();
    document.getElementById('searchResults').innerHTML = `
        <div class="text-center py-5">
            <i class="bi bi-search display-1 text-muted"></i>
            <h5 class="text-muted mt-3">No search performed yet</h5>
            <p class="text-muted">Use the search forms above to find vehicles</p>
        </div>
    `;
    document.getElementById('paginationContainer').classList.add('d-none');
}

function viewVehicleDetails(vehicleId) {
    // This would typically fetch and display vehicle details in a modal
    const modal = new bootstrap.Modal(document.getElementById('vehicleDetailsModal'));
    const modalContent = document.getElementById('vehicleDetailsContent');
    const viewFullProfileBtn = document.getElementById('viewFullProfileBtn');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="loading-spinner"></div>
            <p class="text-muted mt-2">Loading vehicle details...</p>
        </div>
    `;
    
    // Set full profile link
    viewFullProfileBtn.href = appUrl + `/search/vehicle-profile/${vehicleId}`;
    
    // Fetch vehicle details (simulated)
    setTimeout(() => {
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-info-circle display-1 text-primary"></i>
                <h5 class="mt-3">Vehicle Details</h5>
                <p class="text-muted">Full vehicle details are available in the complete profile view.</p>
                <p class="text-muted">Click "View Full Profile" to see ownership history, plate history, and detailed information.</p>
            </div>
        `;
    }, 2000);
    
    modal.show();
}

function loadSearchHistory() {
    const historyContainer = document.getElementById('searchHistory');
    
    Ajax.getSearchHistory()
        .then(response => {
            displaySearchHistory(response);
        })
        .catch(error => {
            console.error('Failed to load search history:', error);
            historyContainer.innerHTML = `
                <div class="text-center py-3">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <p class="text-danger mt-2">Failed to load search history</p>
                </div>
            `;
        });
}

function displaySearchHistory(response) {
    const historyContainer = document.getElementById('searchHistory');
    
    if (!response.search_history || response.search_history.length === 0) {
        historyContainer.innerHTML = `
            <div class="text-center py-3">
                <i class="bi bi-clock-history display-1 text-muted"></i>
                <p class="text-muted mt-2">No search history found</p>
            </div>
        `;
        return;
    }
    
    let historyHtml = '<div class="list-group list-group-flush">';
    
    response.search_history.forEach(search => {
        const searchDate = new Date(search.created_at).toLocaleString();
        
        historyHtml += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">
                        <span class="badge bg-secondary me-2">${search.search_type.toUpperCase()}</span>
                        ${search.search_term}
                    </h6>
                    <small class="text-muted">${searchDate}</small>
                </div>
                <p class="mb-1 small text-muted">
                    ${search.vehicle_found ? 'Vehicle found' : 'Vehicle not found'}
                    ${search.vehicle_id ? ` • <a href="/search/vehicle/${search.vehicle_id}" class="text-primary">View Vehicle</a>` : ''}
                </p>
            </div>
        `;
    });
    
    historyHtml += '</div>';
    
    // Add pagination if needed
    if (response.pagination.total_pages > 1) {
        historyHtml += `
            <div class="mt-3 text-center">
                <small class="text-muted">
                    Showing ${response.search_history.length} of ${response.pagination.total} searches
                </small>
            </div>
        `;
    }
    
    historyContainer.innerHTML = historyHtml;
}

function exportSearchResults() {
    const form = document.getElementById('advancedSearchForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value.trim()) {
            params.append(key, value.trim());
        }
    }
    
    App.showToast('Preparing export...', 'info');
    
    // Trigger download
    window.location.href = `/search/export?${params.toString()}`;
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>