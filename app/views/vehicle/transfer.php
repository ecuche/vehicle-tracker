<?php
$title = "Transfer Vehicle - " . ($vehicle['make'] ?? '') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=  url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles') ?>">My Vehicles</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles/view').$vehicle['vin'] ?>">Vehicle Details</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transfer Vehicle</li>
        </ol>
    </nav>

    <!-- Transfer Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">
                                <i class="bi bi-arrow-left-right me-2 text-primary"></i>Transfer Vehicle
                            </h1>
                            <p class="text-muted mb-0">
                                Transfer ownership of your vehicle to another user
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="vehicle-badge">
                                <span class="badge bg-dark fs-6"><?= e($vehicle['vin']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transfer Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>Transfer Details
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Vehicle Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Vehicle Information</h6>
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="vehicle-icon">
                                        <i class="bi bi-truck display-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-sm-6 mb-2">
                                            <strong>Vehicle:</strong><br>
                                            <?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?> (<?= e($vehicle['year']); ?>)
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <strong>VIN:</strong><br>
                                            <code><?= e($vehicle['vin']); ?></code>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <strong>Current Plate:</strong><br>
                                            <?= e($vehicle['current_plate_number'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <strong>Current Owner:</strong><br>
                                            <span class="text-success">You</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Form -->
                    <form id="transferForm" method="POST" action="<?= url('api/vehicle/transfer-ownership/'.$vehicle['vin']) ?>">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Recipient Information</h6>
                            </div>
                        </div>

                        <!-- Recipient Search -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="recipient_identifier" class="form-label">
                                        <strong>Find Recipient</strong>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg" 
                                               id="recipient_identifier" name="recipient_identifier"
                                               placeholder="Enter recipient's email, phone number, or NIN"
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="clearRecipientSearch()">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Enter the email address, phone number, or 11-digit NIN of the person you want to transfer this vehicle to.
                                    </div>
                                </div>

                                <!-- Search Results -->
                                <div id="searchResults" class="d-none">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0">Search Results</h6>
                                        </div>
                                        <div class="card-body" id="searchResultsContent">
                                            <!-- Results will be populated here -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Recipient -->
                                <div id="selectedRecipient" class="d-none">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="card-title mb-0">
                                                <i class="bi bi-check-circle me-2"></i>Selected Recipient
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="recipientDetails"></div>
                                            <input type="hidden" id="recipient_id" name="recipient_id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Transfer Ownership</h6>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Name</strong>
                                    </label>
                                    <input class="form-control bg-dark" id="new_owner_name" name="new_owner_name" placeholder="Full Name" readonly>
                                    <input type="hidden" name="vin" value="<?= $vehicle['vin'] ?>" readonly>
                                    <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>" readonly>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner NIN</strong>
                                    </label>
                                    <input class="form-control bg-dark" id="new_owner_nin" name="new_owner_nin" placeholder="NIN" readonly>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Phone Number</strong>
                                    </label>
                                    <input class="form-control bg-dark" id="new_owner_phone" name="new_owner_phone" placeholder="New Owner Phone" readonly>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Email</strong>
                                    </label>
                                    <input class="form-control bg-dark" id="new_owner_email" name="new_owner_email" placeholder="Email" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Transfer Details</h6>
                                
                                <div class="mb-3">
                                    <label for="transfer_type" class="form-label">
                                        <strong>Transfer Type</strong>
                                    </label>
                                    <select class="form-select" id="transfer_type" name="transfer_type" required>
                                        <option value="" selected disabled>Select transfer type</option>
                                        <option value="sale">Sale</option>
                                        <option value="gift">Gift</option>
                                        <option value="inheritance">Inheritance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="transfer_amount" class="form-label">
                                        <strong>Transfer Amount (₦)</strong>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₦</span>
                                        <input type="number" class="form-control" id="transfer_amount" 
                                               name="transfer_amount" placeholder="0">
                                    </div>
                                    <div class="form-text">
                                        Enter the amount if this is a sale. Leave empty for gifts or other transfers.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="transfer_note" class="form-label">
                                        <strong>Transfer Notes</strong>
                                    </label>
                                    <textarea class="form-control" id="transfer_note" name="transfer_note" 
                                              rows="4" placeholder="Add any additional notes about this transfer..."></textarea>
                                    <div class="form-text">
                                        Optional: Add any relevant information about this transfer.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Important Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="confirm_irreversible" name="confirm_irreversible" required>
                                            <label class="form-check-label" for="confirm_irreversible">
                                                I understand that this transfer is <strong>irreversible</strong> once accepted by the recipient
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="confirm_ownership" name="confirm_ownership" required>
                                            <label class="form-check-label" for="confirm_ownership">
                                                I confirm that I am the legal owner of this vehicle and have the right to transfer it
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="confirm_accurate" name="confirm_accurate" required>
                                            <label class="form-check-label" for="confirm_accurate">
                                                I confirm that all information provided is accurate and complete
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/vehicles/<?= $vehicle['id']; ?>" class="btn btn-secondary me-md-2">
                                        <i class="bi bi-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="bi bi-send me-1"></i> Initiate Transfer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Guide -->
        <div class="col-lg-4">
            <!-- Transfer Process -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Transfer Process
                    </h5>
                </div>
                <div class="card-body">
                    <div class="transfer-steps">
                        <div class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <strong>Initiate Transfer</strong>
                                <small class="text-muted">You start the transfer process</small>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Recipient Notification</strong>
                                <small class="text-muted">Recipient gets email and app notification</small>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <strong>Recipient Response</strong>
                                <small class="text-muted">Recipient accepts or rejects the transfer</small>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <strong>Transfer Complete</strong>
                                <small class="text-muted">Ownership is transferred upon acceptance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-exclamation me-2"></i>Important Notes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Vehicle Status:</strong><br>
                        <?php
                        $statusMessages = [
                            'none' => 'This vehicle can be transferred normally.',
                            'stolen' => 'This vehicle is marked as STOLEN and cannot be transferred.',
                            'no_customs_duty' => 'This vehicle has customs issues. Transfer may be restricted.',
                            'changed_engine' => 'Engine change recorded. Transfer may require additional verification.',
                            'changed_color' => 'Color change recorded. Transfer is allowed.'
                        ];
                        echo $statusMessages[$vehicle['current_status']] ?? 'Transfer status unknown.';
                        ?>
                    </div>

                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Plate numbers remain with the vehicle
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Ownership history is preserved
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Vehicle documents are transferred
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-x-circle text-danger me-2"></i>
                            Transfer cannot be cancelled once initiated
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-x-circle text-danger me-2"></i>
                            Stolen vehicles cannot be transferred
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-headset me-2"></i>Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        If you encounter any issues during the transfer process, please contact our support team.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="/support" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope me-1"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Final Confirmation Required</strong>
                </div>
                <p>You are about to transfer ownership of:</p>
                <div class="card bg-light">
                    <div class="card-body">
                        <strong><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?> (<?= e($vehicle['year']); ?>)</strong><br>
                        <small class="text-muted">VIN: <?= e($vehicle['vin']); ?></small>
                    </div>
                </div>
                <p class="mt-3 mb-0">
                    <strong>Recipient:</strong> <span id="confirmRecipientName"></span><br>
                    <strong>Transfer Type:</strong> <span id="confirmTransferType"></span><br>
                    <span id="confirmAmount"></span>
                </p>
                <p class="mt-3 text-danger">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        This action cannot be undone. The recipient will be notified and must accept the transfer.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="finalConfirmBtn">
                    <i class="bi bi-check-circle me-1"></i> Confirm Transfer
                </button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.transfer-steps {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.step.completed .step-number {
    background-color: #198754;
    color: white;
}

.step-content {
    flex: 1;
}

.step-content strong {
    display: block;
    font-size: 0.875rem;
}

.step-content small {
    font-size: 0.75rem;
}

.vehicle-icon {
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
}

#searchResults .list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

#searchResults .list-group-item:hover {
    background-color: #f8f9fa;
}

#searchResults .list-group-item.selected {
    background-color: #e7f1ff;
    border-color: #0d6efd;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

/* Loading animation */
.loading-dots:after {
    content: '';
    animation: dots 1.5s steps(5, end) infinite;
}

@keyframes dots {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeTransferForm();
});

function initializeTransferForm() {
    const form = document.getElementById('transferForm');
    const recipientInput = document.getElementById('recipient_identifier');
    const submitBtn = document.getElementById('submitBtn');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    // Real-time recipient search
    recipientInput.addEventListener('input', debounce(searchRecipient, 500));

    // Form validation
    form.addEventListener('input', validateForm);
    
    // Checkbox validation
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateForm);
    });

    // Transfer type change handler
    document.getElementById('transfer_type').addEventListener('change', function() {
        const amountField = document.getElementById('transfer_amount');
        if (this.value === 'sale') {
            amountField.required = true;
            amountField.parentElement.style.display = '';
        } else {
            amountField.required = false;
            amountField.value = '';
            amountField.parentElement.style.display = 'none';
        }
        validateForm();
    });

    // Form submission handler
    form.addEventListener('submit', handleFormSubmission);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function searchRecipient() {
    const identifier = document.getElementById('recipient_identifier').value.trim();
    const searchResults = document.getElementById('searchResults');
    const resultsContent = document.getElementById('searchResultsContent');
    const selectedRecipient = document.getElementById('selectedRecipient');

    // Clear previous selection
    selectedRecipient.classList.add('d-none');
    document.getElementById('recipient_id').value = '';
    validateForm();

    if (!App.validateEmail(identifier) && !App.validateNIN(identifier) && !App.validatePhone(identifier)) {
        searchResults.classList.add('d-none');
        $('#new_owner_name').val("Full Name");
        $('#new_owner_email').val("Email");
        $('#new_owner_nin').val("NIN");
        $('#new_owner_phone').val("Phone Number");
        return;
    }

    // Show loading state
    resultsContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ms-2">Searching<span class="loading-dots"></span></span>
        </div>
    `;
    searchResults.classList.remove('d-none');

    $.ajax({
        type: "POST",
        url: appUrl + "/api/search/user",
        data: {
            value:identifier
        },
        success: response => {
            users = JSON.parse(response);
            
            if (users.length === 0 || Object.hasOwn(users, 'error')) {
                resultsContent.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-search display-6 text-muted"></i>
                        <p class="text-muted mt-2">No users found</p>
                        <small class="text-muted">Try a different email, phone number, or NIN</small>
                    </div>
                `;
                return;
            }

            let resultsHtml = '<div class="list-group list-group-flush">';
            
            users.forEach(user => {
                // Don't show current user in results
                if (user.id === <?= $current_user_id ?? 'null'; ?>) {
                    return;
                }

                const userBadge = user.role === 'driver' ? 'bg-primary' : 
                                 user.role === 'searcher' ? 'bg-info' : 'bg-secondary';
                
                resultsHtml += `
                    <div class="list-group-item list-group-item-action" 
                         onclick="selectRecipient(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', '${escapeHtml(user.phone || 'N/A')}', '${user.role}', '${user.nin}')">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${user.name}</h6>
                                <p class="mb-1 small text-muted">
                                    <i class="bi bi-envelope me-1"></i>${user.email}<br>
                                    <i class="bi bi-phone me-1"></i>${user.phone || 'N/A'}
                                </p>
                            </div>
                            <div>
                                <span class="badge ${userBadge}">${user.role}</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            resultsHtml += '</div>';
            resultsContent.innerHTML = resultsHtml;
        },
        error: error => {
            console.error('Search error:', error);
            resultsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to search for users. Please try again.
                </div>
            `;
        }
    });
}

function selectRecipient(userId, name, email, phone, role, nin) {
    const searchResults = document.getElementById('searchResults');
    const selectedRecipient = document.getElementById('selectedRecipient');
    const recipientDetails = document.getElementById('recipientDetails');
    const recipientInput = document.getElementById('recipient_identifier');

    // Update recipient details
    const roleBadge = role === 'driver' ? 'bg-primary' : 
                     role === 'searcher' ? 'bg-info' : 'bg-secondary';

    recipientDetails.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h6 class="mb-2">${name}</h6>
                <div class="row small">
                    <div class="col-md-6 mb-1">
                        <i class="bi bi-envelope me-1 text-muted"></i>${email}
                    </div>
                    <div class="col-md-6 mb-1">
                        <i class="bi bi-phone me-1 text-muted"></i>${phone}
                    </div>
                    <div class="col-md-6 mb-1">
                        <i class="bi bi-person-badge me-1 text-muted"></i>
                        <span class="badge ${roleBadge}">${role}</span>
                    </div>
                    <div class="col-md-6 mb-1">
                        <i class="bi bi-check-circle me-1 text-success"></i>
                        User verified
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#new_owner_name').val(name);
    $('#new_owner_email').val(email);
    $('#new_owner_nin').val(nin);
    $('#new_owner_phone').val(phone);

    // Set hidden field
    document.getElementById('recipient_id').value = userId;

    // Update UI
    searchResults.classList.add('d-none');
    selectedRecipient.classList.remove('d-none');
    recipientInput.value = `${name} (${email})`;

    validateForm();
}

function clearRecipientSearch() {
    document.getElementById('recipient_identifier').value = '';
    document.getElementById('searchResults').classList.add('d-none');
    document.getElementById('selectedRecipient').classList.add('d-none');
    document.getElementById('recipient_id').value = '';
    validateForm();
}

function validateForm() {
    const submitBtn = document.getElementById('submitBtn');
    const recipientId = document.getElementById('recipient_id').value;
    const transferType = document.getElementById('transfer_type').value;
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    
    let isValid = true;

    // Check recipient
    if (!recipientId) {
        isValid = false;
    }

    // Check transfer type
    if (!transferType) {
        isValid = false;
    }

    // Check amount for sales
    if (transferType === 'sale') {
        const amount = document.getElementById('transfer_amount').value;
        if (!amount || parseFloat(amount) <= 0) {
            isValid = false;
        }
    }

    // Check all checkboxes
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    if (!allChecked) {
        isValid = false;
    }

    submitBtn.disabled = !isValid;
}

function handleFormSubmission(e) {
    e.preventDefault();
    
    // Show confirmation modal
    const recipientName = document.querySelector('#recipientDetails h6').textContent;
    const transferType = document.getElementById('transfer_type');
    const transferTypeText = transferType.options[transferType.selectedIndex].text;
    const amount = document.getElementById('transfer_amount').value;

    document.getElementById('confirmRecipientName').textContent = recipientName;
    document.getElementById('confirmTransferType').textContent = transferTypeText;
    
    if (amount && parseFloat(amount) > 0) {
        document.getElementById('confirmAmount').innerHTML = `<strong>Amount:</strong> ₦${parseFloat(amount).toLocaleString()}`;
    } else {
        document.getElementById('confirmAmount').innerHTML = '';
    }

    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();

    // Set up final confirmation
    document.getElementById('finalConfirmBtn').onclick = function() {
        submitTransferForm();
    };
}

function submitTransferForm() {
    const form = document.getElementById('transferForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');
    const finalConfirmBtn = document.getElementById('finalConfirmBtn');

    // Disable buttons and show loading
    submitBtn.disabled = true;
    finalConfirmBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Processing...';
    finalConfirmBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Processing...';

    fetch(form.action, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            App.showToast('Transfer initiated successfully!', 'success');
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
            // Redirect to transfers page after delay
            setTimeout(() => {
                window.location.href = appUrl + '/vehicles';
            }, 1500);
        } else {
            throw new Error(data.error || 'Transfer initiation failed');
        }
    })
    .catch(error => {
        console.error('Transfer error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable buttons
        submitBtn.disabled = false;
        finalConfirmBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Initiate Transfer';
        finalConfirmBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Confirm Transfer';
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
    });
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Vehicle status validation
function validateVehicleStatus() {
    const status = '<?= $vehicle['current_status']; ?>';
    if (status === 'stolen') {
        App.showToast('Cannot transfer a stolen vehicle', 'error');
        window.location.href = '/vehicles/<?= $vehicle['id']; ?>';
        return false;
    }
    return true;
}

// Check vehicle status on page load
if (!validateVehicleStatus()) {
    // Redirect will happen in the function
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>