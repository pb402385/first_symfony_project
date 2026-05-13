import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/user.css';
import './styles/document.css';
import './styles/category.css';
import './styles/contact.css';
import './styles/auth.css';
import './styles/register.css';


import './js/AuthManager.js'

//FORCE l'envoit du CSRF TOKEN
document.addEventListener('turbo:before-fetch-request', (event) => {
    const url = event.detail.url.toString();

    // Pour le formulaire de login
    if (url.includes('/login')) {
        const csrfToken = document.querySelector('input[name="_csrf_token"]')?.value;

        if (csrfToken) {
            event.detail.fetchOptions.headers['X-CSRF-TOKEN'] = csrfToken;
        }
    }
    
});
