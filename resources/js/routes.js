/**
 * Laravel Route Helper for JavaScript
 * 
 * This module provides a safe way to use Laravel routes in JavaScript
 * without mixing PHP syntax with JavaScript syntax.
 */

class RouteHelper {
    constructor() {
        this.routes = {};
    }

    /**
     * Set routes from Laravel backend
     * @param {Object} routes - Routes object from Laravel
     */
    setRoutes(routes) {
        this.routes = routes;
    }

    /**
     * Generate URL for a named route
     * @param {string} name - Route name
     * @param {Object} params - Route parameters
     * @returns {string} - Generated URL
     */
    route(name, params = {}) {
        if (!this.routes[name]) {
            console.error(`Route '${name}' not found`);
            return '#';
        }

        let url = this.routes[name];
        
        // Replace parameters in URL
        Object.keys(params).forEach(key => {
            const placeholder = `{${key}}`;
            if (url.includes(placeholder)) {
                url = url.replace(placeholder, params[key]);
            } else {
                // Handle query parameters
                const separator = url.includes('?') ? '&' : '?';
                url += `${separator}${key}=${encodeURIComponent(params[key])}`;
            }
        });

        return url;
    }

    /**
     * Navigate to a route
     * @param {string} name - Route name
     * @param {Object} params - Route parameters
     */
    navigate(name, params = {}) {
        const url = this.route(name, params);
        window.location.href = url;
    }

    /**
     * Open route in new tab
     * @param {string} name - Route name
     * @param {Object} params - Route parameters
     */
    openInNewTab(name, params = {}) {
        const url = this.route(name, params);
        window.open(url, '_blank');
    }
}

// Create global instance
window.RouteHelper = new RouteHelper();

// Export for module usage
export default RouteHelper;