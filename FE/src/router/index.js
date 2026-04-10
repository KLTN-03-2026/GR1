import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save

const routes = [
    {
        path: '/',
        component: () => import('../layout/wrapper/index.vue')
    },
    {
        path: "/tao-lich-trinh",
        component: () => import("../pages/Client/TaoLichTrinh.vue"),
        meta: { layout: "client" },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

export default router