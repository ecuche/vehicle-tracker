class AjaxHandler {
    constructor() {
        this.csrfToken = this.getCSRFToken();
        this.setupGlobalAjaxHandlers();
    }

    /**
     * Get CSRF token from meta tag or form input
     */
    getCSRFToken() {
        // Try to get from meta tag first
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            return metaToken.getAttribute('content');
        }

        // Try to get from form input
        const formToken = document.querySelector('input[name="csrf_token"]');
        if (formToken) {
            return formToken.value;
        }

        console.warn('CSRF token not found');
        return null;
    }

    /**
     * Setup global AJAX request and response handlers
     */
    setupGlobalAjaxHandlers() {
        // Intercept all AJAX requests to add CSRF token
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            let [url, options = {}] = args;

            // Add CSRF token to headers for non-GET requests
            if (options.method && options.method !== 'GET' && this.csrfToken) {
                options.headers = {
                    ...options.headers,
                    'X-CSRF-Token': this.csrfToken
                };
            }

            // Add JSON content type if not specified and data is being sent
            if (options.body && !options.headers?.['Content-Type']) {
                options.headers = {
                    ...options.headers,
                    'Content-Type': 'application/json'
                };
            }

            return originalFetch(url, options);
        };
    }

    /**
     * Make AJAX request
     */
    async request(url, data = {}, method = 'POST', options = {}) {
        url = appUrl + url;
        const config = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        };

        // Add CSRF token
        if (this.csrfToken && method !== 'GET') {
            config.headers['X-CSRF-Token'] = this.csrfToken;
        }

        // Add data to request
        if (method !== 'GET' && Object.keys(data).length > 0) {
            config.body = JSON.stringify(data);
        } else if (method === 'GET' && Object.keys(data).length > 0) {
            const params = new URLSearchParams(data);
            const separator = url.includes('?') ? '&' : '?';
            url += separator + params.toString();
        }

        try {
            this.showLoading();
            
            const response = await fetch(url, config);
            const result = await this.handleResponse(response);
            
            this.hideLoading();
            return result;
            
        } catch (error) {
            this.hideLoading();
            this.handleError(error, url);
            throw error;
        }
    }

    /**
     * Handle AJAX response
     */
    async handleResponse(response) {
        const contentType = response.headers.get('content-type');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            
            // Handle API-specific error formats
            if (data && data.success === false) {
                throw new Error(data.message || 'Request failed');
            }
            
            return data;
        } else {
            return await response.text();
        }
    }

    /**
     * Handle AJAX errors
     */
    handleError(error, url) {
        console.error('AJAX Error:', error);
        
        let userMessage = 'An error occurred while processing your request.';
        
        if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
            userMessage = 'Network error. Please check your internet connection.';
        } else if (error.message.includes('HTTP error')) {
            const status = error.message.match(/\d+/)?.[0];
            userMessage = this.getErrorMessageForStatus(status);
        }
        
        this.showError(userMessage);
        
        // Log error for debugging
        this.logError(error, url);
    }

    /**
     * Get user-friendly error message for HTTP status
     */
    getErrorMessageForStatus(status) {
        const messages = {
            400: 'Bad request. Please check your input.',
            401: 'Please log in to continue.',
            403: 'You do not have permission to perform this action.',
            404: 'The requested resource was not found.',
            422: 'Validation error. Please check your input.',
            429: 'Too many requests. Please try again later.',
            500: 'Server error. Please try again later.',
            503: 'Service temporarily unavailable. Please try again later.'
        };
        
        return messages[status] || 'An unexpected error occurred.';
    }

    /**
     * Show error message to user
     */
    showError(message, title = 'Error') {
        if (typeof App !== 'undefined') {
            App.showToast(message, 'danger');
        } else {
            // Fallback to alert
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Show success message
     */
    showSuccess(message, title = 'Success') {
        if (typeof App !== 'undefined') {
            App.showToast(message, 'success');
        } else {
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        // You can implement a global loading indicator here
        document.body.style.cursor = 'wait';
    }

    /**
     * Hide loading indicator
     */
    hideLoading() {
        document.body.style.cursor = 'default';
    }

    /**
     * Log error for debugging
     */
    logError(error, url) {
        const errorData = {
            url: url,
            error: error.message,
            stack: error.stack,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent
        };
        
        // In a real application, you might want to send this to your error logging service
        console.error('Error logged:', errorData);
    }

    /**
     * Search vehicles by VIN or plate number
     */
    async searchVehicle(searchTerm, type = 'vin') {
        return await this.request('/api/search/vehicle', {
            term: searchTerm,
            type: type
        }, 'GET');
    }

    /**
     * Search users by identifier (email, phone, NIN)
     */
    async searchUser(identifier) {
        return await this.request('/vehicles/search-user', {
            q: identifier
        }, 'GET');
    }

    /**
     * Register new vehicle
     */
    async registerVehicle(vehicleData) {
        return await this.request('/vehicles/register', vehicleData, 'POST');
    }

    /**
     * Transfer vehicle ownership
     */
    async transferVehicle(vehicleId, userIdentifier) {
        return await this.request('/vehicles/transfer', {
            vehicle_id: vehicleId,
            user_identifier: userIdentifier
        }, 'POST');
    }

    /**
     * Handle transfer request (accept/reject)
     */
    async handleTransfer(transferId, action) {
        return await this.request('/vehicles/handle-transfer', {
            transfer_id: transferId,
            action: action
        }, 'POST');
    }

    /**
     * Assign new plate number to vehicle
     */
    async assignPlateNumber(vehicleId, plateNumber) {
        return await this.request('/vehicles/assign-plate', {
            vehicle_id: vehicleId,
            plate_number: plateNumber
        }, 'POST');
    }

    /**
     * Update vehicle status (admin only)
     */
    async updateVehicleStatus(vehicleId, status) {
        return await this.request('/admin/update-vehicle-status', {
            vehicle_id: vehicleId,
            status: status
        }, 'POST');
    }

    /**
     * Update user role (admin only)
     */
    async updateUserRole(userId, role) {
        return await this.request('/admin/update-role', {
            user_id: userId,
            role: role
        }, 'POST');
    }

    /**
     * Ban/unban user (admin only)
     */
    async toggleUserBan(userId, action) {
        return await this.request('/admin/toggle-ban', {
            user_id: userId,
            action: action
        }, 'POST');
    }

    /**
     * Get user profile data
     */
    async getUserProfile(identifier) {
        return await this.request(`/profile/user/${encodeURIComponent(identifier)}`, {}, 'GET');
    }

    /**
     * Get vehicle details
     */
    async getVehicleDetails(vehicleId) {
        return await this.request(`/api/vehicles/details/${vehicleId}`, {}, 'GET');
    }

    /**
     * Get dashboard statistics
     */
    async getDashboardStats() {
        return await this.request('/dashboard/stats', {}, 'GET');
    }

    /**
     * Get admin statistics
     */
    async getAdminStats() {
        return await this.request('/admin/stats', {}, 'GET');
    }

    /**
     * Export audit trail to CSV
     */
    async exportAuditToCSV(filters = {}) {
        const params = new URLSearchParams(filters);
        window.open(`/admin/export-audit?${params.toString()}`, '_blank');
    }

    /**
     * Export search results to CSV
     */
    async exportSearchResults(filters = {}) {
        const params = new URLSearchParams(filters);
        window.open(`/search/export?${params.toString()}`, '_blank');
    }

    /**
     * Upload files with progress tracking
     */
    async uploadFiles(url, formData, onProgress = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            if (onProgress) {
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        onProgress(percentComplete);
                    }
                });
            }
            
            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        resolve(xhr.responseText);
                    }
                } else {
                    reject(new Error(`Upload failed with status ${xhr.status}`));
                }
            });
            
            xhr.addEventListener('error', () => {
                reject(new Error('Upload failed'));
            });
            
            xhr.open('POST', url);
            
            // Add CSRF token to headers
            if (this.csrfToken) {
                xhr.setRequestHeader('X-CSRF-Token', this.csrfToken);
            }
            
            xhr.send(formData);
        });
    }

    /**
     * Real-time validation for forms
     */
    async validateField(fieldName, value, rules) {
        return await this.request('/api/validate', {
            field: fieldName,
            value: value,
            rules: rules
        }, 'POST');
    }

    /**
     * Check if value is unique in database
     */
    async checkUnique(field, value, table, excludeId = null) {
        const data = { field, value, table };
        if (excludeId) data.exclude_id = excludeId;
        
        return await this.request('/api/check-unique', data, 'POST');
    }

    /**
     * Get paginated data
     */
    async getPaginatedData(url, page = 1, perPage = 10, filters = {}) {
        const params = {
            page: page,
            per_page: perPage,
            ...filters
        };
        
        return await this.request(url, params, 'GET');
    }

    /**
     * Refresh CSRF token
     */
    async refreshCSRFToken() {
        try {
            const response = await this.request('/api/csrf-token', {}, 'GET');
            if (response.token) {
                this.csrfToken = response.token;
                
                // Update meta tag and form inputs
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', response.token);
                }
                
                const formInputs = document.querySelectorAll('input[name="csrf_token"]');
                formInputs.forEach(input => {
                    input.value = response.token;
                });
            }
        } catch (error) {
            console.warn('Failed to refresh CSRF token:', error);
        }
    }

    /**
     * Setup automatic token refresh
     */
    setupTokenRefresh() {
        // Refresh token every 30 minutes
        setInterval(() => {
            this.refreshCSRFToken();
        }, 30 * 60 * 1000);
    }
}

// Create global instance
const Ajax = new AjaxHandler();

// Make available globally
window.AjaxHandler = Ajax;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AjaxHandler;
}