<?php
$title = "Vehicle Management";
$actions = '
    <div class="input-group" style="width: 400px;">
        <input type="text" class="form-control" placeholder="Search vehicles..." id="searchInput" value="' . e($search) . '">
        <select class="form-select" style="width: auto;" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="none" ' . ($status === 'none' ? 'selected' : '') . '>Normal</option>
            <option value="stolen" ' . ($status === 'stolen' ? 'selected' : '') . '>Stolen</option>
            <option value="no_customs_duty" ' . ($status === 'no_customs_duty' ? 'selected' : '') . '>No Customs Duty</option>
            <option value="changed_engine" ' . ($status === 'changed_engine' ? 'selected' : '') . '>Changed Engine</option>
            <option value="changed_color" ' . ($status === 'changed_color' ? 'selected' : '') . '>Changed Color</option>
        </select>
        <button class="btn btn-outline-secondary" type="button" id="searchButton">
            <i class="bi bi-search"></i>
        </button>
    </div>
';

ob_start();
?>
<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number"><?= $pagination['total']; ?></div>
            <div class="stats-label">Total Vehicles</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-success"><?= $normal_count; ?></div>
            <div class="stats-label">Normal</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-danger"><?= $stolen_count; ?></div>
            <div class="stats-label">Stolen</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-warning"><?= $customs_count; ?></div>
            <div class="stats-label">No Customs</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-info"><?= $engine_count; ?></div>
            <div class="stats-label">Changed Engine</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-primary"><?= $color_count; ?></div>
            <div class="stats-label">Changed Color</div>
        </div>
    </div>
</div>

<!-- Vehicles Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Vehicle Database</h5>
        
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted">Show:</span>
            <select class="form-select form-select-sm me-2" style="width: auto;" id="perPageSelect">
                <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="15" <?= $pagination['per_page'] == 15 ? 'selected' : ''; ?>>15</option>
                <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            
            <button type="button" class="btn btn-success btn-sm" onclick="exportVehicles()">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($vehicles)): ?>
        <div class="text-center py-5">
            <i class="bi bi-truck display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No Vehicles Found</h4>
            <p class="text-muted">No vehicles match your search criteria.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>VIN</th>
                        <th>Plate</th>
                        <th>Owner</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (empty([])): ?>
                                <img src=<?= url('public/assets/images/primary-image.png') ?> 
                                     alt="<?= e($vehicle->make); ?> <?= e($vehicle->model); ?>" 
                                     class="rounded me-3" 
                                     width="40" 
                                     height="40"
                                     style="object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-truck text-light"></i>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <strong><?= e($vehicle->make); ?> <?= e($vehicle->model); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="<?= $_ENV['APP_URL']."/admin/manage-vehicle/" . $vehicle->vin ?>">
                                <strong><?= e($vehicle->vin); ?></strong>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-dark"><?= e($vehicle->current_plate_number); ?></span>
                        </td>
                        <td>
                            <div>
                                <a href="<?= $_ENV['APP_URL']."/admin/manage-user/" . $vehicle->owner_email ?>">
                                    <div><?= e($vehicle->owner_email); ?></div>
                                    <small class="text-muted"><?= format_phone($vehicle->owner_phone); ?></small>
                                </a>
                            </div>
                        </td>
                        <td><?= e($vehicle->year); ?></td>
                        <td>
                            <?= vehicle_status_badge($vehicle->current_status); ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= relative_time($vehicle->created_at); ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <?= pagination_links($pagination['page'], $pagination['total_pages'], '/admin/vehicles', $pagination['per_page']); ?>
            </div>
            <div class="text-muted">
                Page <?= $pagination['page']; ?> of <?= $pagination['total_pages']; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>

function exportVehicles() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const filters = {};
    if (search) filters.search = search;
    if (status) filters.status = status;
    
    Ajax.exportSearchResults(filters);
}

document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const searchButton = document.getElementById('searchButton');
    
    function performSearch() {
        const search = searchInput.value.trim();
        const status = statusFilter.value;
        const url = new URL(window.location);
        
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }
    
    if (searchButton) {
        searchButton.addEventListener('click', performSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', performSearch);
    }
    
    // Pagination per page change
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    }
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>