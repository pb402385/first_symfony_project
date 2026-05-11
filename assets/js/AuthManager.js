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
            // === DEBUG : Voir exactement quelles URLs sont appelées ===
            console.log(`[FETCH] ${options.method || 'GET'} → ${url}`);

            const token = localStorage.getItem('jwt_token');

            if (token) {
                options.headers = options.headers || {};
                options.headers.Authorization = `Bearer ${token}`;
            }

            try {
                const response = await originalFetch(url, options);

                // Token expiré ou invalide
                if (response.status === 401) {
                    console.warn('[AUTH] Token expiré → déconnexion');
                    localStorage.removeItem('jwt_token');
                    this.token = null;
                    this.updateNavbar();
                    alert('Votre session a expiré. Veuillez vous reconnecter.');
                    window.location.href = '/login';
                } else {
                    let self = this;
                    window.setTimeout(function() {
                        self.updateNavbar();
                    }, 100);

                }

                return response;

            } catch (error) {
                console.error('[FETCH ERROR]', error);
                throw error;
            }
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
