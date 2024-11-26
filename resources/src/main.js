import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Laravel Echo
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'a4f0e74d0120fbbe5bf0',
    cluster: 'eu',
    forceTLS: true,
});

const app = createApp(App);
app.use(router);
app.mount('#app');
