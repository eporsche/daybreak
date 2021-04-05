window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pikaday = require('pikaday');

async function registerSW() {
  if ('serviceWorker' in navigator) {
    try {
      await navigator.serviceWorker.register('/sw.js',  { scope: '/' });
    } catch (e) {
        console.log('ServiceWorker registration failed. Sorry about that.');
        console.log(e)
    }
  } else {
    console.log('No Service worker support. Site might not be served via https?');
  }
}

window.addEventListener('load', e => {
  registerSW();
});
