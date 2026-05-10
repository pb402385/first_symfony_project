class AuthManager {
    constructor() {
        this.token = localStorage.getItem('jwt_token');
        this.init();
    }

    init() {
        this.updateNavbar();
        this.setupInterceptors();
    }

    // Vérifie si l'utilisateur est connecté
    isLoggedIn() {
        return !!this.token;
    }

    // Met à jour la barre de navigation
    updateNavbar() {
        const navAuth = document.getElementById('nav-auth');
        if (!navAuth) return;

        if (this.isLoggedIn()) {
            navAuth.innerHTML = `
                    <button class="logout-txt-link" onclick="window.auth.logout()">Déconnexion</button>
                `;
        } else {
            navAuth.innerHTML = `
                    <button onclick="window.location.href='/login'"
                    class="px-5 py-2 text-indigo-600 hover:bg-indigo-50 rounded-xl font-medium transition auth-login">
                Connexion
            </button>
            <button onclick="window.location.href='/register'"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition auth.register">
                Inscription gratuite
            </button>
                `;
        }
    }

    // Ajoute automatiquement le token à toutes les requêtes fetch
    setupInterceptors() {
        const originalFetch = window.fetch;
        window.fetch = async (url, options = {}) => {
            const token = localStorage.getItem('jwt_token');

            if (token) {
                options.headers = options.headers || {};
                options.headers.Authorization = `Bearer ${token}`;
            }

            const response = await originalFetch(url, options);

            // Token expiré ou invalide
            if (response.status === 401) {
                localStorage.removeItem('jwt_token');
                this.token = null;
                this.updateNavbar();
                alert('Votre session a expiré. Veuillez vous reconnecter.');
                window.location.href = '/login';
            }

            return response;
        };
    }

    // Sauvegarde le token après connexion
    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
        this.updateNavbar();
    }

    // Déconnexion
    logout() {
        localStorage.removeItem('jwt_token');
        this.token = null;
        this.updateNavbar();
        window.location.href = '/login';
    }
}

// Initialisation globale
window.auth = new AuthManager();
