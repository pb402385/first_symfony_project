class AuthManager {
    constructor() {
        this.token = localStorage.getItem('jwt_token');
        this.init();
    }

    init() {

        this.setupInterceptors();
    }

    isLoggedIn() {
        return !!this.token;
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
                alert('Session expirée');
                window.location.href = '/login';
            } else {
                console.log('FETCH ' + response);
            }

            return response;
        };
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
    }

    logout() {
        this.logoutApi().then(r => {
            localStorage.removeItem('jwt_token');
            this.token = null;
            window.location.href = '/logout';
        });
    }

    async logoutApi(){
        console.log("logoutApi exec");
        try {
            const response = await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer '+this.token,
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                alert("Erreur, il faut être authentifié pour accéder à l'API response status:"+response?.status);
                console.error("Erreur, il faut être authentifié pour accéder à l'API response:", response);
                return;
            }

            const data = await response.json();

            if(response.ok){
                alert("Deconnexion réussie: "+data?.message);
            }
        } catch (error) {
            console.error('Erreur réseau:', error);
            alert("Erreur réseau ou autre: erreur->"+error);
        }
    }
}

// Initialisation
window.auth = new AuthManager();

console.log('AuthManager chargé avec token:', window.auth.isLoggedIn());
