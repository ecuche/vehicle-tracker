<?php
$title = "Driver Dashboard";
ob_start();
?>
<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $total_vehicles; ?></div>
            <div class="stats-label">Total Vehicles</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $pending_requests; ?></div>
            <div class="stats-label">Pending Transfers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $incoming_requests; ?></div>
            <div class="stats-label">Incoming Transfer</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $sold_vehicles; ?></div>
            <div class="stats-label">Sold Vehicles</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Vehicles -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Vehicles</h5>
                <a href="<?= $_ENV['APP_URL'] ?>/vehicles" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($vehicles)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-truck display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Vehicles Registered</h4>
                    <p class="text-muted">You haven't registered any vehicles yet.</p>
                    <a href="<?= $_ENV['APP_URL'] ?>/vehicles/register" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Register Your First Vehicle
                    </a>
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach (array_slice($vehicles, 0, 6) as $vehicle): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card vehicle-card h-100">
                            <div class="vehicle-image">
                                <?php if ($vehicle->image_count > 0): ?>
                                <img src="<?= $_ENV['APP_URL'] ?>/assets/uploads/vehicles/images/<?= $vehicle->primary_image; ?>" 
                                     alt="<?= e($vehicle->make); ?> <?= e($vehicle->model); ?>" 
                                     class="card-img-top" 
                                     style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="bi bi-truck display-4 text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="vehicle-status">
                                    <?= vehicle_status_badge($vehicle->current_status); ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><?= e($vehicle->make); ?> <?= e($vehicle->model); ?></h6>
                                <p class="card-text small mb-1">
                                    <strong>VIN:</strong> <code><?= e($vehicle->vin); ?></code><br>
                                    <strong>Plate:</strong> <?= e($vehicle->current_plate_number); ?><br>
                                    <strong>Year:</strong> <?= e($vehicle->year); ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100">
                                    <a href="<?= $_ENV['APP_URL'] ?>/vehicles/details/<?= $vehicle->id; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#transferModal<?= $vehicle->id; ?>">
                                        <i class="bi bi-arrow-left-right"></i> Transfer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pending Transfers & Recent Activity -->
    <div class="col-lg-4">
        <!-- Pending Transfers -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Transfers</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pending_transfers)): ?>
                <p class="text-muted text-center mb-0">No pending transfers</p>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pending_transfers as $transfer): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Transfer Request</h6>
                            <small class="text-muted"><?= relative_time($transfer->created_at); ?></small>
                        </div>
                        <p class="mb-1 small">
                            <strong>Vehicle:</strong> <?= e($transfer->make); ?> <?= e($transfer->model); ?><br>
                            <strong>From:</strong> <?= e($transfer->from_user_email); ?>
                        </p>
                        <div class="btn-group btn-group-sm mt-2">
                            <form action="<?= $_ENV['APP_URL'] ?>/vehicles/handle-transfer" method="POST" class="d-inline">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="transfer_id" value="<?= $transfer->id; ?>">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                            </form>
                            <form action="<?= $_ENV['APP_URL'] ?>/vehicles/handle-transfer" method="POST" class="d-inline">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="transfer_id" value="<?= $transfer->id; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_transfers)): ?>
                <p class="text-muted text-center mb-0">No recent activity</p>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_transfers as $transfer): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= transfer_status_badge($transfer->status); ?></h6>
                            <small class="text-muted"><?= relative_time($transfer->created_at); ?></small>
                        </div>
                        <p class="mb-1 small">
                            <?= e($transfer->make); ?> <?= e($transfer->model); ?><br>
                            <small class="text-muted">
                                <?php if ($transfer->status === 'accepted'): ?>
                                Transferred to <?= e($transfer->to_user_email); ?>
                                <?php elseif ($transfer->status === 'rejected'): ?>
                                Transfer rejected
                                <?php else: ?>
                                Waiting for response from <?= e($transfer->to_user_email); ?>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="<?= $_ENV['APP_URL'] ?>/vehicles/register" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-plus-circle display-6 d-block mb-2"></i>
                            Register Vehicle
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= $_ENV['APP_URL'] ?>/vehicles" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-truck display-6 d-block mb-2"></i>
                            My Vehicles
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= $_ENV['APP_URL'] ?>/profile" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-person display-6 d-block mb-2"></i>
                            My Profile
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= $_ENV['APP_URL'] ?>/search" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-search display-6 d-block mb-2"></i>
                            Search Vehicles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Refresh stats every 30 seconds
    setInterval(() => {
        Ajax.getDashboardStats()
            .then(stats => {
                // Update stats cards if needed
                console.log('Stats updated', stats);
            })
            .catch(error => {
                console.error('Failed to update stats', error);
            });
    }, 30000);
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>