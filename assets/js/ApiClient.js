// assets/js/ApiClient.js
class ApiClient {
    constructor() {
        this.baseUrl = '/api';
    }

    // Récupère le token depuis localStorage
    getToken() {
        return localStorage.getItem('jwt_token');
    }

    async request(endpoint, options = {}) {
        const token = localStorage.getItem('jwt_token');

        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers,
            },
            ...options
        };

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        const response = await fetch(this.baseUrl + endpoint, config);

        if (response.status === 401) {
            localStorage.removeItem('jwt_token');
            window.location.href = '/login';
            throw new Error('Session expirée');
        }

        return response;
    }

    get(endpoint) { return this.request(endpoint, { method: 'GET' }); }
    post(endpoint, data) { return this.request(endpoint, { method: 'POST', body: JSON.stringify(data) }); }
    put(endpoint, data) { return this.request(endpoint, { method: 'PUT', body: JSON.stringify(data) }); }
    delete(endpoint) { return this.request(endpoint, { method: 'DELETE' }); }
}

// Utilisation globale
window.apiClient = new ApiClient();


// utilisation exemple
/*
const response = await window.apiClient.get('/profile');
const data = await response.json();

console.log ("await response and data");
*/

