<?php
$title = "My Vehicles";
$actions = '
    <a href="'.url('vehicles/register').'" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Register New Vehicle
    </a>
';

ob_start();
?>
<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="<?= url('vehicles') ?>">
            <div class="card stats-card">
                <div class="stats-number"><?= $total_vehicles; ?></div>
                <div class="stats-label">Total Vehicles</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('vehicles/outgoing-transfers') ?>">
            <div class="card stats-card">
                <div class="stats-number"><?= $pending_requests; ?></div>
                <div class="stats-label">Outgoing Transfers</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('vehicles/incoming-transfers') ?>">
            <div class="card stats-card">
                <div class="stats-number"><?= $incoming_requests; ?></div>
                <div class="stats-label">Incoming Transfers</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('vehicles/completed-transfers') ?>">
            <div class="card stats-card">
                <div class="stats-number"><?= $sold_vehicles; ?></div>
                <div class="stats-label">Sold Vehicles</div>
            </div>
        </a>
    </div>
</div>
<!-- Vehicles List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Registered Vehicles</h5>
        
        <!-- Pagination Controls -->
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted">Show:</span>
            <select class="form-select form-select-sm me-2" style="width: auto;" id="perPageSelect">
                <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="15" <?= $pagination['per_page'] == 15 ? 'selected' : ''; ?>>15</option>
                <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            
            <span class="text-muted">
                <?= ($pagination['page'] - 1) * $pagination['per_page'] + 1; ?> - 
                <?= min($pagination['page'] * $pagination['per_page'], $pagination['total']); ?> 
                of <?= $pagination['total']; ?>
            </span>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($vehicles)): ?>
        <div class="text-center py-5">
            <i class="bi bi-truck display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No Vehicles Registered</h4>
            <p class="text-muted">You haven't registered any vehicles yet.</p>
            <a href="<?= $_ENV["APP_URL"] ?>/vehicles/register" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Register Your First Vehicle
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>VIN</th>
                        <th>Plate Number</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($vehicle['image_count'] > 0): ?>
                                <img src="<?= $_ENV["APP_URL"] ?>/public/assets/images/primary-image.png" 
                                     alt="<?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?>" 
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
                                    <strong><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code><?= e($vehicle['vin']); ?></code>
                        </td>
                        <td>
                            <span class="badge bg-dark"><?= e($vehicle['current_plate_number']); ?></span>
                        </td>
                        <td><?= e($vehicle['year']); ?></td>
                        <td>
                            <?= vehicle_status_badge($vehicle['current_status']); ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= relative_time($vehicle['created_at']); ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('vehicles/view/'.$vehicle['vin']) ?>" 
                                   class="btn btn-outline-primary" 
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <?= pagination_links($pagination['page'], $pagination['total_pages'], $_ENV["APP_URL"].'/vehicles', $pagination['per_page']); ?>
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

    document.addEventListener('DOMContentLoaded', function() {
    // User search for transfer - FIXED VERSION (no blinking)
    const userIdentifierInputs = document.querySelectorAll('input[name="user_identifier"]');
    
    userIdentifierInputs.forEach(input => {
        let resultsContainer = null;
        let currentResults = [];

        const createResultsContainer = () => {
            if (resultsContainer) return resultsContainer;

            resultsContainer = document.createElement('div');
            resultsContainer.className = 'list-group mt-2 user-search-results';
            resultsContainer.style.cssText = `
                max-height: 200px;
                overflow-y: auto;
                position: relative;
                z-index: 1056;
                display: none;
                border: 1px solid var(--bs-border-color);
                border-radius: 0.375rem;
                background: var(--bs-body-bg);
            `;
            
            // Append to modal body immediately but keep hidden
            input.closest('.modal-body').appendChild(resultsContainer);
            return resultsContainer;
        };

        const showResults = (users) => {
            createResultsContainer(); // Ensure container exists
            
            // Clear previous results
            resultsContainer.innerHTML = '';

            if (users.length === 0) {
                const item = document.createElement('div');
                item.className = 'list-group-item text-muted small';
                item.textContent = 'No users found';
                resultsContainer.appendChild(item);
            } else {
                users.forEach(user => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.style.cssText = 'border: none; border-bottom: 1px solid var(--bs-border-color);';
                    item.innerHTML = `
                        <strong>${user.email}</strong><br>
                        <small class="text-muted">${user.phone || 'No phone'} • ${user.nin || 'No NIN'}</small>
                    `;
                    item.onclick = (e) => {
                        e.preventDefault();
                        input.value = user.email;
                        hideResults();
                        input.focus();
                    };
                    resultsContainer.appendChild(item);
                });
            }

            // Show container with smooth transition
            resultsContainer.style.display = 'block';
            resultsContainer.style.opacity = '0';
            setTimeout(() => {
                resultsContainer.style.opacity = '1';
            }, 10);
        };

        const hideResults = () => {
            if (resultsContainer) {
                // Smooth hide
                resultsContainer.style.opacity = '0';
                setTimeout(() => {
                    resultsContainer.style.display = 'none';
                }, 150);
            }
        };

        input.addEventListener('input', function(e) {
            const value = e.target.value.trim();

            clearTimeout(input.searchTimeout);

            if (value.length < 3) {
                hideResults();
                return;
            }

            // Show loading state
            createResultsContainer();
            resultsContainer.innerHTML = '<div class="list-group-item text-muted small">Searching...</div>';
            resultsContainer.style.display = 'block';
            resultsContainer.style.opacity = '1';

            input.searchTimeout = setTimeout(() => {
                Ajax.searchUser(value)
                    .then(users => {
                        currentResults = users;
                        showResults(users);
                    })
                    .catch(err => {
                        console.error(err);
                        hideResults();
                    });
            }, 400);
        });

        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (resultsContainer && 
                !input.contains(e.target) && 
                !resultsContainer.contains(e.target)) {
                hideResults();
            }
        });

        // Hide on modal close
        const modal = input.closest('.modal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                if (resultsContainer) {
                    resultsContainer.style.display = 'none';
                    resultsContainer.style.opacity = '1';
                }
            });
        }

        // Also hide when input loses focus (with small delay to allow clicking results)
        input.addEventListener('blur', (e) => {
            setTimeout(() => {
                if (!resultsContainer?.contains(document.activeElement)) {
                    hideResults();
                }
            }, 200);
        });
    });
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>