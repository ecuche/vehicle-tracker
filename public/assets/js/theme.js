/**
 * Vehicle Tracker - Theme Manager
 * Handles dark/light mode switching and theme persistence
 */

class ThemeManager {
    constructor() {
        this.currentTheme = 'dark'; // Default theme
        this.themes = ['dark', 'light'];
        this.init();
    }

    init() {
        this.loadTheme();
        this.setupEventListeners();
        this.applyTheme();
    }

    /**
     * Load theme from localStorage or system preference
     */
    loadTheme() {
        // Try to load from localStorage first
        const savedTheme = localStorage.getItem('vehicle-tracker-theme');
        
        if (savedTheme && this.themes.includes(savedTheme)) {
            this.currentTheme = savedTheme;
        } else {
            // Check system preference
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.currentTheme = systemPrefersDark ? 'dark' : 'light';
        }
    }

    /**
     * Save theme to localStorage
     */
    saveTheme() {
        localStorage.setItem('vehicle-tracker-theme', this.currentTheme);
    }

    /**
     * Apply current theme to document
     */
    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        this.updateThemeToggleButton();
        this.dispatchThemeChangeEvent();
    }

    /**
     * Switch to specific theme
     */
    switchTheme(theme) {
        if (!this.themes.includes(theme)) {
            console.warn(`Invalid theme: ${theme}`);
            return;
        }

        this.currentTheme = theme;
        this.saveTheme();
        this.applyTheme();
        
        // Show feedback
        this.showThemeSwitchFeedback(theme);
    }

    /**
     * Toggle between dark and light themes
     */
    toggleTheme() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.switchTheme(newTheme);
    }

    /**
     * Update theme toggle button state
     */
    updateThemeToggleButton() {
        const toggleButtons = document.querySelectorAll('.theme-toggle');
        
        toggleButtons.forEach(button => {
            const sunIcon = button.querySelector('.bi-sun');
            const moonIcon = button.querySelector('.bi-moon');
            const themeText = button.querySelector('.theme-text');
            
            if (sunIcon && moonIcon) {
                if (this.currentTheme === 'dark') {
                    sunIcon.style.display = 'inline-block';
                    moonIcon.style.display = 'none';
                    if (themeText) themeText.textContent = 'Light Mode';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'inline-block';
                    if (themeText) themeText.textContent = 'Dark Mode';
                }
            }
            
            // Update aria-label for accessibility
            button.setAttribute('aria-label', `Switch to ${this.currentTheme === 'dark' ? 'light' : 'dark'} mode`);
        });
    }

    /**
     * Show feedback when theme is switched
     */
    showThemeSwitchFeedback(theme) {
        const themeNames = { dark: 'Dark Mode', light: 'Light Mode' };
        
        if (typeof window.VehicleTrackerApp?.showToast === 'function') {
            window.VehicleTrackerApp.showToast(`Switched to ${themeNames[theme]}`, 'info', 2000);
        }
    }

    /**
     * Dispatch theme change event for other components to listen to
     */
    dispatchThemeChangeEvent() {
        const event = new CustomEvent('themeChanged', {
            detail: {
                theme: this.currentTheme
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Setup event listeners for theme switching
     */
    setupEventListeners() {
        // Theme toggle buttons
        document.addEventListener('click', (e) => {
            const toggleButton = e.target.closest('.theme-toggle');
            if (toggleButton) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Listen for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
            // Only auto-switch if user hasn't explicitly set a preference
            if (!localStorage.getItem('vehicle-tracker-theme')) {
                this.currentTheme = e.matches ? 'dark' : 'light';
                this.applyTheme();
            }
        });

        // Listen for theme change events from other components
        document.addEventListener('themeChanged', (e) => {
        });
    }

    /**
     * Get current theme
     */
    getCurrentTheme() {
        return this.currentTheme;
    }

    /**
     * Check if dark mode is active
     */
    isDarkMode() {
        return this.currentTheme === 'dark';
    }

    /**
     * Check if light mode is active
     */
    isLightMode() {
        return this.currentTheme === 'light';
    }

    /**
     * Add custom theme
     */
    addTheme(themeName, themeConfig) {
        if (this.themes.includes(themeName)) {
            console.warn(`Theme ${themeName} already exists`);
            return;
        }

        this.themes.push(themeName);
        
        // Add CSS variables for the new theme
        this.injectThemeCSS(themeName, themeConfig);
    }

    /**
     * Inject CSS variables for custom theme
     */
    injectThemeCSS(themeName, themeConfig) {
        const styleId = `theme-${themeName}-variables`;
        let styleElement = document.getElementById(styleId);
        
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = styleId;
            document.head.appendChild(styleElement);
        }

        let css = `[data-theme="${themeName}"] {\n`;
        
        for (const [property, value] of Object.entries(themeConfig)) {
            css += `  --${property}: ${value};\n`;
        }
        
        css += '}';
        styleElement.textContent = css;
    }

    /**
     * Remove custom theme
     */
    removeTheme(themeName) {
        if (!this.themes.includes(themeName) || ['dark', 'light'].includes(themeName)) {
            console.warn(`Cannot remove theme ${themeName}`);
            return;
        }

        this.themes = this.themes.filter(theme => theme !== themeName);
        
        // Remove CSS variables
        const styleElement = document.getElementById(`theme-${themeName}-variables`);
        if (styleElement) {
            styleElement.remove();
        }

        // If current theme is being removed, switch to dark mode
        if (this.currentTheme === themeName) {
            this.switchTheme('dark');
        }
    }

    /**
     * Export theme configuration
     */
    exportThemeConfig(themeName) {
        if (!this.themes.includes(themeName)) {
            console.warn(`Theme ${themeName} not found`);
            return null;
        }

        // This would need to be implemented based on how you store theme configurations
        // For now, return basic theme info
        return {
            name: themeName,
            isActive: this.currentTheme === themeName,
            isSystem: !localStorage.getItem('vehicle-tracker-theme') && 
                     ((themeName === 'dark' && window.matchMedia('(prefers-color-scheme: dark)').matches) ||
                      (themeName === 'light' && window.matchMedia('(prefers-color-scheme: light)').matches))
        };
    }

    /**
     * Reset to system preference
     */
    resetToSystemPreference() {
        localStorage.removeItem('vehicle-tracker-theme');
        this.loadTheme();
        this.applyTheme();
        
        if (typeof VehicleTrackerApp !== 'undefined') {
            VehicleTrackerApp.showToast('Theme reset to system preference', 'info', 2000);
        }
    }

    /**
     * Initialize theme-specific components
     */
    initializeThemeComponents() {
        // Update charts for current theme
        this.updateChartsTheme();
        
        // Update map themes if any
        this.updateMapsTheme();
        
        // Update any other theme-dependent components
        this.updateComponentThemes();
    }

    /**
     * Update charts for current theme
     */
    updateChartsTheme() {
        // This would be implemented based on your charting library
        // Example for Chart.js:
        /*
        const charts = Chart.instances;
        Object.values(charts).forEach(chart => {
            chart.options.plugins.legend.labels.color = getComputedStyle(document.documentElement)
                .getPropertyValue('--text-primary');
            chart.update();
        });
        */
    }

    /**
     * Update maps for current theme
     */
    updateMapsTheme() {
        // Implement based on your mapping library
    }

    /**
     * Update other theme-dependent components
     */
    updateComponentThemes() {
        // Update any other components that need theme awareness
        const event = new CustomEvent('themeComponentsUpdate', {
            detail: { theme: this.currentTheme }
        });
        document.dispatchEvent(event);
    }

    /**
     * Get contrast color for current theme
     */
    getContrastColor() {
        return this.currentTheme === 'dark' ? '#ffffff' : '#000000';
    }

    /**
     * Get background color for current theme
     */
    getBackgroundColor() {
        return getComputedStyle(document.documentElement)
            .getPropertyValue('--bg-primary')
            .trim();
    }

    /**
     * Get text color for current theme
     */
    getTextColor() {
        return getComputedStyle(document.documentElement)
            .getPropertyValue('--text-primary')
            .trim();
    }
}

// Create global instance
const themeManager = new ThemeManager();


/**
 * Utility function to check if user has explicit theme preference
 */
function hasExplicitThemePreference() {
    return localStorage.getItem('vehicle-tracker-theme') !== null;
}

/**
 * Utility function to get system theme preference
 */
function getSystemThemePreference() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

/**
 * Utility function to watch for system theme changes
 */
function watchSystemTheme(callback) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    
    const handler = (e) => {
        callback(e.matches ? 'dark' : 'light');
    };
    
    mediaQuery.addEventListener('change', handler);
    
    // Return cleanup function
    return () => mediaQuery.removeEventListener('change', handler);
}



// Make available globally
window.ThemeManager = themeManager;


// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeManager;
}