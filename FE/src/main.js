import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
import axios from 'axios'

// Global Axios Interceptor for Laravel 422 Errors
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response?.status === 422) {
            const apiErrors = error.response.data.errors || {};
            error.response.data.validationErrorsRaw = apiErrors;
            const parsedErrors = {};
            for (const key in apiErrors) {
                // Return first string of the array if it is an array formed by Laravel
                parsedErrors[key] = Array.isArray(apiErrors[key]) ? apiErrors[key][0] : apiErrors[key];
            }
            // Bind single string parsed errors back to the response
            error.response.data.errors = parsedErrors;
        }
        return Promise.reject(error);
    }
);

// Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Toast notification
import ToastPlugin from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'

// Bootstrap Icons
const link = document.createElement('link')
link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'
link.rel = 'stylesheet'
document.head.appendChild(link)

import VueApexCharts from 'vue3-apexcharts'

const app = createApp(App)

app.use(router)
app.use(VueApexCharts)
app.use(ToastPlugin, {
    position: 'top-right',
    duration: 3200,
    dismissible: true,
    pauseOnHover: true,
    queue: true,
})

router.isReady().then(() => {
    app.mount("#app")
})
