/**
 * Vehicle Tracker - Validation Module (Refactored)
 * Cleaner, modular, extensible validation class
 */

class Validation {
    constructor() {
        this.rules = {};
        this.messages = {};

        document.addEventListener("DOMContentLoaded", () => {
            this.initialize();
        });
    }

    // -------------------------------
    // Initialization
    // -------------------------------
    initialize() {
        this.setupRules();
        this.setupMessages();
        this.activateValidation();
    }

    // -------------------------------
    // Validation Rules
    // -------------------------------
    setupRules() {
        const PATTERNS = {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            phone: /^(\+234|0)[789][01]\d{8}$/,
            nin: /^\d{11}$/,
            vin: /^[A-HJ-NPR-Z0-9]{17}$/i
        };

        this.rules = {
            required: (v) => (typeof v === "string" ? v.trim() : v),
            name: (v) => (typeof v === "string" ? v.trim() : v),
            email: (v) => PATTERNS.email.test(v),
            phone: (v) => PATTERNS.phone.test(v.replace(/\s/g, "")),
            nin: (v) => PATTERNS.nin.test(v),
            vin: (v) =>  PATTERNS.vin.test(v),

            plate_number: (v) => {
                if (!v) return true;
                const clean = v.replace(/\s/g, "").toUpperCase();
                const patterns = [
                    /^[A-Z]{2}\d{4}[A-Z]{0,2}$/i,
                    /^[A-Z]{3}\d{3,4}[A-Z]{0,2}$/i,
                    /^[A-Z]{1,2}\d{1,5}[A-Z]{0,2}$/i,
                ];
                return patterns.some(p => p.test(clean));
            },

            min: (v, p) => (typeof v === "string" ? v.length >= +p : +v >= +p),
            max: (v, p) => (typeof v === "string" ? v.length <= +p : +v <= +p),
            numeric: (v) => !isNaN(Number(v)),
            integer: (v) => Number.isInteger(Number(v)),
            matches: (v, p, form) => v === form[p],

            password_strength: (v) => {
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(v);
            },

            year: (v) => {
                if (!v) return true;
                const year = +v;
                const now = new Date().getFullYear();
                return year >= 1900 && year <= now + 1;
            },

            file_type: (file, accepted) => !file || accepted.split(",").includes(file.type),
            file_size: (file, maxSize) => !file || file.size <= this.parseFileSize(maxSize),
        };
    }

    // -------------------------------
    // Validation Messages
    // -------------------------------
    setupMessages() {
        this.messages = {
            required: "This field is required.",
            email: "Please enter a valid email.",
            phone: "Please enter a valid Nigerian phone number.",
            name: "Full Name is required.",
            nin: "NIN must be 11 digits.",
            vin: "VIN must be 17 characters.",
            plate_number: "Invalid plate number format.",
            min: "Minimum length is {param}.",
            max: "Maximum length is {param}.",
            numeric: "Only numbers allowed.",
            integer: "Value must be an integer.",
            matches: "This field must match {param}.",
            password_strength: "Password must include uppercase, lowercase, number.",
            year: "Please enter a valid year.",
            file_type: "Invalid file type.",
            file_size: "File must be smaller than {param}.",
        };
    }

    // -------------------------------
    // Activate Listeners
    // -------------------------------
    activateValidation() {
        this.setupRealtimeValidation();
        this.setupFormSubmission();
    }

    setupRealtimeValidation() {
        const fields = document.querySelectorAll("[data-validation]");

        fields.forEach(field => {
            field.addEventListener("blur", () => this.validateField(field));
            field.addEventListener("input", () => this.clearFieldState(field));
            if (field.tagName === "SELECT") {
                field.addEventListener("change", () => this.validateField(field));
            }
        });
    }

    setupFormSubmission() {
        const forms = document.querySelectorAll("form[data-validate]");

        forms.forEach(form => {
            form.addEventListener("submit", (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });
        });
    }

    // -------------------------------
    // Validation Execution
    // -------------------------------
    validateForm(form) {
        const fields = form.querySelectorAll("[data-validation]");
        let valid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) valid = false;
        });

        return valid;
    }

    validateField(field) {
        const rules = field.dataset.validation?.split("|") || [];
        const value = this.getValue(field);
        const form = this.getFormData(field.form);

        for (let rule of rules) {
            const [name, param] = rule.split(":");

            const validator = this.rules[name];
            if (!validator) continue;

            if (!validator(value, param, form)) {
                this.showInvalid(field, this.formatMessage(name, param));
                return false;
            }
        }

        this.showValid(field);
        return true;
    }

    // -------------------------------
    // DOM Helpers
    // -------------------------------
    getValue(field) {
        if (field.type === "checkbox") return field.checked;
        if (field.type === "radio")
            return field.closest("form")?.querySelector(`input[name="${field.name}"]:checked`)?.value || null;
        if (field.type === "file") return field.files[0] || null;
        return field.value;
    }

    getFormData(form) {
        const fd = new FormData(form);
        return Object.fromEntries(fd.entries());
    }

    showInvalid(field, msg) {
        this.clearFieldState(field);
        field.classList.add("is-invalid");

        if (msg) {
            const fb = document.createElement("div");
            fb.className = "invalid-feedback";
            fb.textContent = msg;
            field.parentNode.appendChild(fb);
        }

        this.toggleSubmitButton(field.form, true);
    }

    showValid(field) {
        this.clearFieldState(field);
        field.classList.add("is-valid");
        this.toggleSubmitButton(field.form, false);
    }

    clearFieldState(field) {
        field.classList.remove("is-valid", "is-invalid");
        field.parentNode.querySelector(".invalid-feedback")?.remove();
    }

    toggleSubmitButton(form, disabled) {
        const btn = form?.querySelector('[type="submit"]');
        if (btn) btn.disabled = disabled;
    }

    showFormErrors(form) {
        const first = form.querySelector(".is-invalid");
        if (first) {
            first.scrollIntoView({ behavior: "smooth", block: "center" });
            first.focus();
        }

        window.VehicleTrackerApp?.showToast("Please fix the errors.", "danger");
    }

    // -------------------------------
    // Utilities
    // -------------------------------
    formatMessage(rule, param) {
        return (this.messages[rule] || "").replace("{param}", param);
    }

    parseFileSize(str) {
        const map = { B: 1, KB: 1024, MB: 1048576, GB: 1073741824 };
        const match = /^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)$/i.exec(str);
        return match ? +match[1] * map[match[2].toUpperCase()] : 0;
    }

    // External utility hooks
    addRule(name, fn, message) {
        this.rules[name] = fn;
        if (message) this.messages[name] = message;
    }

    removeRule(name) {
        delete this.rules[name];
        delete this.messages[name];
    }

    resetForm(form) {
        form.querySelectorAll("[data-validation]").forEach(f => this.clearFieldState(f));
        this.toggleSubmitButton(form, false);
    }

    clearFieldValidation(field) {
        field.classList.remove('is-valid', 'is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

        // Add this method inside the Validation class
    validatePasswordWithFeedback(password) {
        const feedback = [];

        if (!password) {
            feedback.push("Password is required.");
            return { valid: false, feedback };
        }

        // Check length
        if (password.length < 8) {
            feedback.push("Password must be at least 8 characters long.");
        }

        // Check lowercase
        if (!/[a-z]/.test(password)) {
            feedback.push("Password must include a lowercase letter.");
        }

        // Check uppercase
        if (!/[A-Z]/.test(password)) {
            feedback.push("Password must include an uppercase letter.");
        }

        // Check digit
        if (!/\d/.test(password)) {
            feedback.push("Password must include a number.");
        }

        // Overall valid
        const valid = feedback.length === 0;

        return { valid, feedback };
    }

    ValidateEmail(email){
        var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailReg.test(email);
    }

    validateNIN(nin){
        var ninReg = /^\d{11}$/;
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
    
}

// Create global instance
window.FormValidation = new Validation();
if (typeof module !== "undefined") module.exports = Validation;
