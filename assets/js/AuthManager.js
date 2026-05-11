class AuthManager {
    constructor() {
        this.token = localStorage.getItem('jwt_token');
        this.init();
    }

    init() {
        this.updateNavbar();
        this.setupInterceptors();
    }

    isLoggedIn() {
        return !!this.token;
    }

    updateNavbar() {
        const navAuth = document.getElementById('nav-auth');
        if (!navAuth) return;

        if (this.isLoggedIn()) {
            navAuth.innerHTML = `
                <button class="logout-txt-link" onclick="window.auth.logout()">
                    Déconnexion
                </button>
            `;
        } else {
            navAuth.innerHTML = `
                <button onclick="window.location.href='/login'" class="px-5 py-2 text-indigo-600 hover:bg-indigo-50 rounded-xl font-medium transition">
                    Connexion
                </button>
                <button onclick="window.location.href='/register'" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition">
                    Inscription gratuite
                </button>
            `;
        }
    }

    // ==================== INTERCEPTEUR FETCH ====================
    setupInterceptors() {
        const originalFetch = window.fetch;

        window.fetch = async (url, options = {}) => {
            const token = localStorage.getItem('jwt_token');

            console.log(`[FETCH] Appel vers : ${url}`);

            // Important : toujours créer un nouvel objet options
            const fetchOptions = { ...options };

            // Force la création du header Authorization
            fetchOptions.headers = {
                ...(options.headers || {}),           // garde les headers existants
                'Content-Type': 'application/json',
            };

            if (token) {
                fetchOptions.headers.Authorization = `Bearer ${token}`;
                console.log('→ Token ajouté au header');
            } else {
                console.log('→ Aucun token trouvé');
            }

            // Appel final
            const response = await originalFetch(url, fetchOptions);

            console.log(`[FETCH] Status reçu : ${response.status}`);

            if (response.status === 401) {
                localStorage.removeItem('jwt_token');
                this.token = null;
                this.updateNavbar();
                alert('Session expirée');
                window.location.href = '/login';
            } else {
                let self = this;
                window.setTimeout(function() {
                    self.updateNavbar();
                }, 100);
                console.log('FETCH ' + response);
            }

            return response;
        };
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
        this.updateNavbar();
    }

    logout() {
        localStorage.removeItem('jwt_token');
        this.token = null;
        this.updateNavbar();
        window.location.href = '/login';
    }
}

// Initialisation
window.auth = new AuthManager();

console.log('AuthManager chargé avec token:', window.auth.isLoggedIn());
