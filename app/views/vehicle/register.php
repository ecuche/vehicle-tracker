<?php
$title = "Register Vehicle";
$actions = '
    <a href="'.$_ENV["APP_URL"].'/vehicles" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Vehicles
    </a>
';

ob_start();
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Vehicle Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= $_ENV["APP_URL"] ?>/vehicles/register" method="POST" enctype="multipart/form-data" id="vehicleForm">
                    <?php csrf_field(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vin" class="form-label">Vehicle Identification Number (VIN)</label>
                            <input type="text" 
                                   class="form-control <?= has_error('vin') ? 'is-invalid' : ''; ?>" 
                                   id="vin" 
                                   name="vin" 
                                   value="<?= old('vin'); ?>" 
                                   data-validation="vin"
                                   maxlength="17"
                                   placeholder="17-character VIN"
                                   required>
                            <?php if (has_error('vin')): ?>
                            <div class="invalid-feedback"><?= get_error('vin'); ?></div>
                            <?php endif; ?>
                            <div class="form-text invalid-feedback">Enter the 17-character VIN</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="plate_number" class="form-label">Plate Number</label>
                            <input type="text" 
                                   class="form-control <?= has_error('plate_number') ? 'is-invalid' : ''; ?>" 
                                   id="plate_number" 
                                   name="plate_number" 
                                   value="<?= old('plate_number'); ?>" 
                                   data-validation="plate_number"
                                   placeholder="e.g., ABC123"
                                   required>
                            <?php if (has_error('plate_number')): ?>
                            <div class="invalid-feedback"><?= get_error('plate_number'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_make" class="form-label">Vehicle Make</label>
                            <select class="form-select <?= has_error('vehicle_make') ? 'is-invalid' : ''; ?>" 
                                    id="vehicle_make" name="vehicle_make" required>
                                <option value="">Select Make</option>
                                <?php foreach ($vehicle_makes as $make): ?>
                                <option value="<?= $make['make']; ?>" 
                                        <?= old('vehicle_make') == $make['make'] ? 'selected' : ''; ?>>
                                    <?= e($make['make']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('vehicle_make')): ?>
                            <div class="invalid-feedback"><?= get_error('vehicle_make'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="vehicle_model" class="form-label">Vehicle Model</label>
                            <select class="form-select <?= has_error('vehicle_model') ? 'is-invalid' : ''; ?>" 
                                    id="vehicle_model" name="vehicle_model_id" required>
                                <option value="">Select Model</option>
                            </select>
                            <?php if (has_error('vehicle_model')): ?>
                            <div class="invalid-feedback"><?= get_error('vehicle_model'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="year" class="form-label">Manufacturing Year</label>
                            <select class="form-select <?= has_error('year') ? 'is-invalid' : ''; ?>" 
                                    id="year" 
                                    name="year" 
                                    required>
                                <option value="">Select Year</option>
                                <?php for ($y = date('Y') + 1; $y >= 1900; $y--): ?>
                                <option value="<?= $y; ?>" 
                                        <?= old('year') == $y ? 'selected' : ''; ?>>
                                    <?= $y; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                            <?php if (has_error('year')): ?>
                            <div class="invalid-feedback"><?= get_error('year'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">Vehicle Color</label>
                            <input type="text" 
                                   class="form-control <?= has_error('color') ? 'is-invalid' : ''; ?>" 
                                   id="color" 
                                   name="color" 
                                   value="<?= old('color'); ?>" 
                                   placeholder="e.g., Black"
                                   required>
                            <?php if (has_error('color')): ?>
                            <div class="invalid-feedback"><?= get_error('color'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehicle Images</label>
                        <div class="file-upload <?= has_error('vehicle_images') ? 'is-invalid' : ''; ?>">
                            <input type="file" 
                                   class="form-control" 
                                   id="vehicle_images" 
                                   name="vehicle_images[]" 
                                   accept="image/*"
                                   multiple>
                            <div class="text-center mt-2">
                                <i class="bi bi-cloud-upload display-4 text-muted"></i>
                                <p class="text-muted">Click to upload or drag and drop</p>
                                <p class="small text-muted">PNG, JPG, GIF up to 5MB each</p>
                            </div>
                        </div>
                        <?php if (has_error('vehicle_images')): ?>
                        <div class="invalid-feedback"><?= get_error('vehicle_images'); ?></div>
                        <?php endif; ?>
                        <div class="file-preview mt-3" style="display: none;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehicle Documents</label>
                        <div class="file-upload <?= has_error('vehicle_documents') ? 'is-invalid' : ''; ?>">
                            <input type="file" 
                                   class="form-control" 
                                   id="vehicle_documents" 
                                   name="vehicle_documents[]" 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   multiple>
                            <div class="text-center mt-2">
                                <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                                <p class="text-muted">Click to upload or drag and drop</p>
                                <p class="small text-muted">PDF, DOC, JPG, PNG up to 10MB each</p>
                            </div>
                        </div>
                        <?php if (has_error('vehicle_documents')): ?>
                        <div class="invalid-feedback"><?= get_error('vehicle_documents'); ?></div>
                        <?php endif; ?>
                        <div class="file-preview mt-3" style="display: none;"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> All vehicle information will be verified. 
                        Providing false information may result in account suspension.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Register Vehicle
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Registration Guidelines -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Registration Guidelines</h5>
            </div>
            <div class="card-body">
                <h6>Required Information:</h6>
                <ul class="small">
                    <li>Valid 17-character VIN</li>
                    <li>Current plate number</li>
                    <li>Vehicle make and model</li>
                    <li>Manufacturing year</li>
                </ul>

                <h6>Supported Documents:</h6>
                <ul class="small">
                    <li>Vehicle registration certificate</li>
                    <li>Insurance documents</li>
                    <li>Purchase receipts</li>
                    <li>Customs papers (for imported vehicles)</li>
                </ul>

                <h6>Image Requirements:</h6>
                <ul class="small">
                    <li>Clear photos of the vehicle</li>
                    <li>Front, back, and side views</li>
                    <li>Engine compartment</li>
                    <li>Interior dashboard</li>
                </ul>

                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle"></i>
                    Ensure all information is accurate. Incorrect information may lead to rejection.
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('vehicleForm');
    
    // Real-time validation
    const fields = form.querySelectorAll('[data-validation]');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
        
        field.addEventListener('input', function() {
            FormValidation.clearFieldValidation(this);
        });
    });
    
    // File upload handling
    const imageInput = document.getElementById('vehicle_images');
    const docInput = document.getElementById('vehicle_documents');
    
    // VIN validation
    const vinField = document.getElementById('vin');
    if (vinField) {
        vinField.addEventListener('blur', function() {
            const vin = this.value.toUpperCase();
            this.value = vin;
            
            if (vin.length === 17) {
                // Check if VIN is already registered
                $.post(appUrl + '/api/check-vin',{vin: vin}, function(data, status){
                    if(data === "false"){
                        const feedback = document.createElement('div');
                        const vinField = document.getElementById("vin");
                        feedback.className = 'valid-feedback';
                        feedback.textContent = 'This VIN is available.';
                        vinField.appendChild(feedback);
                    }else if(status !== "success"){
                        console.log("error in request:" + status);
                    }
                });
            }
        });
    }

    // Get Model AJAX
    const makeField = document.getElementById('vehicle_make');
    if (makeField) {
        makeField.addEventListener('change', function() {
            const vehicle_make = this.value;
            if (vehicle_make.length) {
                $.post(appUrl + '/api/vehicle/get-models',{make: vehicle_make}, function(data, status){
                    if(data !== "false"){
                        const modelField = document.getElementById("vehicle_model");
                        modelData = '';
                        var data = JSON.parse(data);
                        data.forEach(item => {
                            modelData += '<option value="' + item.id + '">' + item.model + '</option>';
                        });
                        modelField.innerHTML = "";
                        modelField.innerHTML += modelData;
                    }else if(status !== "success"){
                        console.log("error in request:" + status);
                    }
                });
            }
        });
    }
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>