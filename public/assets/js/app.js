class VehicleTrackerApp {
    constructor() {
        this.init();
    }

    // app.js
    init() {
        document.addEventListener('DOMContentLoaded', () => {

            this.initializeForms();
            this.initializeNavigation();
            this.initializeNotifications();
            this.initializeSessionTimer();
            this.initializeGlobalEventListeners();
            this.initializeResponsiveBehavior();
            
            window.VehicleTrackerApp = this; 
            window.App = this; 
            
        });
    }

    /**
     * Initialize theme system
     */
    initializeTheme() {
        if (typeof themeManager !== 'undefined') {
            themeManager.init();
        }
    }

    /**
     * Initialize form handling
     */
    initializeForms() {
        // Auto-disable submit buttons to prevent double submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="loading-spinner"></span> Processing...';
                    
                    // Re-enable button after 10 seconds in case of error
                    setTimeout(() => {
                        if (submitButton.disabled) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Submit';
                        }
                    }, 10000);
                }
            });
        });

        // Real-time validation
        this.initializeRealTimeValidation();

        // File upload enhancements
        this.initializeFileUploads();

        // Phone number formatting
        this.initializePhoneFormatting();
    }

    /**
     * Initialize real-time form validation
     */
    initializeRealTimeValidation() {
        const validatedFields = document.querySelectorAll('[data-validation]');

        validatedFields.forEach(field => {
            // Skip fields in forms that use the new validation system
            const form = field.closest('form');
            if (form && form.hasAttribute('data-validate')) {
                return;
            }

            field.addEventListener('blur', () => {
                this.validateField(field);
            });

            field.addEventListener('input', () => {
                this.clearFieldValidation(field);
            });
        });
    }

    /**
     * Validate a single field
     */
    validateField(field) {
        const value = field.value.trim();
        const validationType = field.getAttribute('data-validation');
        
        if (!value) return;

        // Show loading state
        field.classList.add('validating');

        // Simulate API call for validation
        setTimeout(() => {
            field.classList.remove('validating');
            
            let isValid = false;
            let errorMessage = '';

            switch (validationType) {
                case 'email':
                    isValid = this.validateEmail(value);
                    errorMessage = isValid ? '' : 'Please enter a valid email address';
                    break;
                case 'phone':
                    isValid = this.validatePhone(value);
                    errorMessage = isValid ? '' : 'Please enter a valid Nigerian phone number';
                    break;
                case 'name':
                    isValid = this.validateName(value);
                    errorMessage = isValid ? '' : 'Please enter a valid Full Name';
                    break;
                case 'nin':
                    isValid = this.validateNIN(value);
                    errorMessage = isValid ? '' : 'NIN must be 11 digits';
                    break;
                case 'vin':
                    isValid = this.validateVIN(value);
                    errorMessage = isValid ? '' : 'VIN must be 17 alphanumeric characters';
                    break;
                case 'password':
                    isValid = this.validatePassword(value);
                    errorMessage = isValid ? '' : 'Password must be at least 8 characters with uppercase, lowercase and number.';
                    break;
            }

            this.updateFieldValidationState(field, isValid, errorMessage);
        }, 500);
    }

    /**
     * Clear field validation state
     */
    clearFieldValidation(field) {
        field.classList.remove('is-valid', 'is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    /**
     * Update field validation UI
     */
    updateFieldValidationState(field, isValid, message) {
        this.clearFieldValidation(field);
        
        if (isValid) {
            field.classList.add('is-valid');
        } else {
            field.classList.add('is-invalid');
            if (message) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = message;
                field.parentNode.appendChild(feedback);
            }
        }
    }

    /**
     * Validation functions
     */
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validateName(name) {
        name = name.trim();
        const re = /^[A-Za-z'-]{2,}(?: [A-Za-z'-]{2,})+$/;
        return re.test(name);
    }

    validatePhone(phone) {
        const normalized = phone.replace(/[ -]/g, '');
        const re = /^(?:\+?234|0)[789][01]\d{8}$/;
        return re.test(normalized);
    }


    validateNIN(nin) {
        return /^\d{11}$/.test(nin);
    }

    validateVIN(vin) {
        return /^[A-HJ-NPR-Z0-9]{17}$/i.test(vin);
    }

    validatePassword(password) {
        const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
        return re.test(password);
    }


    /**
     * Initialize file upload functionality
     */
    initializeFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            if (input.multiple || input.accept.includes('image')) {
                input.addEventListener('change', (e) => {
                    this.handleFileSelection(e.target);  // ← bound to correct instance
                });
            }

            // Drag and drop
            const container = input.closest('.file-upload') || input.parentNode;
            if (container.classList.contains('file-upload')) {
                this.initializeDragAndDrop(container, input);
            }
        });
    }

    /**
     * Handle file selection and preview
     */
    handleFileSelection(input) {
        if (!input || !input.files) return;

        const files = input.files;
        const previewContainer = input.parentNode.querySelector('.file-preview');

        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        if (files.length === 0) {
            previewContainer.style.display = 'none';
            return;
        }

        previewContainer.style.display = 'block';

        Array.from(files).forEach(file => {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    previewItem.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi bi-file-earmark';
                previewItem.appendChild(icon);
            }

            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            fileInfo.innerHTML = `
                <div class="file-name">${file.name}</div>
                <div class="file-size">${this.formatFileSize(file.size)}</div>
            `;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger ms-auto';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = () => this.removeFilePreview(input, previewItem, file);

            previewItem.appendChild(fileInfo);
            previewItem.appendChild(removeBtn);
            previewContainer.appendChild(previewItem);
        });
    }

    /**
     * Initialize drag and drop for file uploads
     */
    initializeDragAndDrop(container, input) {
        container.addEventListener('dragover', (e) => {
            e.preventDefault();
            container.classList.add('dragover');
        });

        container.addEventListener('dragleave', (e) => {
            e.preventDefault();
            container.classList.remove('dragover');
        });

        container.addEventListener('drop', (e) => {
            e.preventDefault();
            container.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                this.handleFileSelection(input);
            }
        });
    }

    /**
     * Remove file from preview
     */
    removeFilePreview(input, previewItem, file) {
        previewItem.remove();
        
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        const updatedFiles = files.filter(f => f !== file);
        
        updatedFiles.forEach(f => dt.items.add(f));
        input.files = dt.files;

        if (updatedFiles.length === 0) {
            const previewContainer = input.parentNode.querySelector('.file-preview');
            if (previewContainer) {
                previewContainer.style.display = 'none';
            }
        }
    }

    /**
     * Format file size for display
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Initialize phone number formatting
     */

    initializePhoneFormatting() {
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[data-validation="phone"]');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, ''); // only digits

            // ----- Nigerian Local Format (11 digits) -----
            if (value.startsWith('0')) {
                value = value.slice(0, 11);

                // Format: 0801 234 5678
                if (value.length > 4) {
                    value = value.slice(0, 4) + ' ' + value.slice(4);
                }
                if (value.length > 8) {
                    value = value.slice(0, 8) + ' ' + value.slice(8);
                }
            }

            // ----- Nigerian International Format (13 digits) -----
            else if (value.startsWith('234')) {
                value = value.slice(0, 13);

                // Format: 234 801 234 5678
                if (value.length > 3) {
                    value = value.slice(0, 3) + ' ' + value.slice(3);
                }
                if (value.length > 7) {
                    value = value.slice(0, 7) + ' ' + value.slice(7);
                }
                if (value.length > 11) {
                    value = value.slice(0, 11) + ' ' + value.slice(11);
                }
            }

            e.target.value = value;
        });
    });
}


    /**
     * Initialize navigation functionality
     */
    initializeNavigation() {
        // Mobile menu toggle
        const navbarToggler = document.querySelector('.navbar-toggler');
        const sidebar = document.querySelector('.sidebar');
        
        if (navbarToggler && sidebar) {
            navbarToggler.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && sidebar && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !navbarToggler.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Active navigation highlighting
        this.highlightActiveNavigation();
    }

    /**
     * Highlight active navigation item
     */
    highlightActiveNavigation() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.startsWith(href) && href !== '/') {
                link.classList.add('active');
            } else if (currentPath === '/' && href === '/dashboard') {
                link.classList.add('active');
            }
        });
    }

    /**
     * Initialize notification system
     */
    initializeNotifications() {
        // Auto-hide flash messages after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (!alert.classList.contains('alert-dismissible')) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });

        // Toast notifications
        this.initializeToastNotifications();
    }

    /**
     * Initialize toast notification system
     */
    initializeToastNotifications() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');
        const toastId = 'toast-' + Date.now();
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { delay: duration });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    /**
     * Initialize session timer
     */
    initializeSessionTimer() {
        const timeout = parseInt(document.body.getAttribute('data-session-timeout') || '3600');
        
        if (timeout > 0) {
            let idleTime = 0;
            
            const resetIdleTime = () => {
                idleTime = 0;
            };
            
            const idleInterval = setInterval(() => {
                idleTime++;
                
                // Warn user 1 minute before timeout
                if (idleTime === timeout - 60) {
                    this.showSessionWarning();
                }
                
                // Redirect to logout after timeout
                if (idleTime >= timeout) {
                    clearInterval(idleInterval);
                    this.handleSessionTimeout();
                }
            }, 1000);
            
            // Reset idle time on user activity
            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetIdleTime, true);
            });
        }
    }

    /**
     * Show session timeout warning
     */
    showSessionWarning() {
        const warningToast = this.showToast(
            'Your session will expire in 1 minute. Click to extend.',
            'warning',
            60000
        );
        
        // Allow user to extend session by clicking the toast
        const toastElement = document.querySelector('.toast:last-child');
        if (toastElement) {
            toastElement.style.cursor = 'pointer';
            toastElement.addEventListener('click', () => {
                this.extendSession();
                toastElement.querySelector('.btn-close').click();
            });
        }
    }

    /**
     * Extend user session
     */
    extendSession() {
        fetch('/api/session/extend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showToast('Session extended', 'success', 3000);
            }
        })
        .catch(error => {
            console.error('Error extending session:', error);
        });
    }

    /**
     * Handle session timeout
     */
    handleSessionTimeout() {
        this.showToast('Session expired. Redirecting to login...', 'warning', 3000);
        
        setTimeout(() => {
            window.location.href = '/login?timeout=1';
        }, 3000);
    }

    /**
     * Initialize global event listeners
     */
    initializeGlobalEventListeners() {
        // Confirm actions for dangerous operations
        document.addEventListener('click', (e) => {
            const confirmable = e.target.closest('[data-confirm]');
            if (confirmable) {
                const message = confirmable.getAttribute('data-confirm') || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            }
        });

        // Print functionality
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-print]')) {
                window.print();
            }
        });

        // Copy to clipboard
        document.addEventListener('click', (e) => {
            const copyButton = e.target.closest('[data-copy]');
            if (copyButton) {
                const textToCopy = copyButton.getAttribute('data-copy');
                this.copyToClipboard(textToCopy);
            }
        });
    }

    /**
     * Copy text to clipboard
     */
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showToast('Copied to clipboard', 'success', 2000);
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showToast('Copied to clipboard', 'success', 2000);
        });
    }

    /**
     * Initialize responsive behavior
     */
    initializeResponsiveBehavior() {
        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleWindowResize();
            }, 250);
        });

        // Handle orientation change
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                this.handleWindowResize();
            }, 500);
        });
    }

    /**
     * Handle window resize events
     */
    handleWindowResize() {
        const sidebar = document.querySelector('.sidebar');
        
        if (window.innerWidth >= 768) {
            sidebar?.classList.remove('show');
        }
    }

    /**
     * Utility function to debounce method calls
     */
    debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }

    /**
     * Utility function to format dates
     */
    formatDate(date, format = 'medium') {
        const dateObj = new Date(date);
        const options = {
            short: { year: 'numeric', month: 'short', day: 'numeric' },
            medium: { year: 'numeric', month: 'long', day: 'numeric' },
            long: { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' },
            time: { hour: '2-digit', minute: '2-digit' }
        };
        
        return dateObj.toLocaleDateString('en-NG', options[format] || options.medium);
    }

    formatNumber(number, decimals = 0) {
        return new Intl.NumberFormat('en-NG', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }
    
    validateNIN(nin){
        var ninReg = /^[1-9]\d{10}$/;
        return ninReg.test(nin);
    }

    validatePhone(phone){
       var phoneReg = /^(\+234|0)[789][01]\d{8}$/;
        return phoneReg.test(phone);
    }

    validatePassword(password){
        var passwordReg = /^(?=.[a-z])(?=.[A-Z])(?=.*\d)[a-zA-Z\d\S]{8,}$/;
        return passwordReg.test(password);
    }

    validateVIN(vin){
        var vinReg = /^[A-HJ-NPR-Za-hj-npr-z\d]{8}[\dX][A-HJ-NPR-Za-hj-npr-z\d]{2}\d{6}$/;
        return vinReg.test(vin);
    }

    validatePlateNumber(plate){
        var plateReg = /^[A-Za-z]{3}-?\d{3}[A-Za-z]{2}$/;
        return plateReg.test(plate);
    }
}

// Initialize the application
const App = new VehicleTrackerApp();

// Make app available globally
window.VehicleTrackerApp = App;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VehicleTrackerApp;
}