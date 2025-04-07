import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import { useAuthStore } from './stores/auth';

// Setup axios defaults
axios.defaults.baseURL = '/';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);

// Initialize auth store with token from localStorage
const authStore = useAuthStore(pinia);
authStore.initializeAuth();

app.use(router);
app.mount('#app');