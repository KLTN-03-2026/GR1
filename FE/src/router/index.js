import { createRouter, createWebHistory } from "vue-router";

const routes = [
  // =================== Client routes =================
  {
    path: "/",
    component: () => import("../pages/Client/Home.vue"),
    meta: { layout: "client" },
  },
  // {
  //   path: "/client/dang-nhap",
  //   component: () => import("../pages/Client/DangNhap.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/auth/callback",
  //   alias: "/client/auth/callback",
  //   component: () => import("../pages/Client/GoogleAuthCallback.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/auth/error",
  //   alias: "/client/auth/error",
  //   component: () => import("../pages/Client/AuthError.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/client/dang-ky",
  //   component: () => import("../pages/Client/DangKy.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/client/quen-mat-khau",
  //   component: () => import("../pages/Client/QuenMatKhau.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/client/dat-lai-mat-khau/:key",
  //   component: () => import("../pages/Client/DatLaiMatKhau.vue"),
  //   meta: { layout: "blank" },
  // },
  // {
  //   path: "/client/kich-hoat/:hash_active",
  //   component: () => import("../pages/Client/kichHoat.vue"),
  //   meta: { layout: "blank" },
  // },
  {
    path: "/tao-lich-trinh",
    component: () => import("../pages/Client/TaoLichTrinh.vue"),
    meta: { layout: "client" },
  },
  // {
  //   path: "/lich-trinh-cua-toi",
  //   component: () => import("../pages/Client/LichTrinhCuaToi.vue"),
  //   meta: { layout: "client" },
  // },
  {
    path: "/nhom-du-lich",
    component: () => import("../pages/Client/NhomDuLich.vue"),
    meta: { layout: "client" },
  },
  // {
  //   path: "/profile",
  //   component: () => import("../pages/Client/profile.vue"),
  //   meta: { layout: "client" },
  // },
  // Địa điểm
  {
    path: "/check-in",
    component: () => import("../pages/Client/DiaDiem/CheckIn.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/giai-tri",
    component: () => import("../pages/Client/DiaDiem/GiaiTri.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/am-thuc",
    component: () => import("../pages/Client/DiaDiem/AmThuc.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/tam-linh",
    component: () => import("../pages/Client/DiaDiem/TamLinh.vue"),
    meta: { layout: "client" },
  },
  //================== Admin routes ==================
  // {
  //   path: "/admin/dang-nhap",
  //   component: () => import("../pages/Admin/DangNhap.vue"),
  //   meta: { layout: "blank" },
  // },

  // {
  //   path: "/admin/dashboard",
  //   component: () => import("../pages/Admin/Dashboard.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/users",
  //   component: () => import("../pages/Admin/Users.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/danh-sach-nhan-vien",
  //   component: () => import("../pages/Admin/DanhSachNhanVien.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/phan-quyen",
  //   component: () => import("../pages/Admin/PhanQuyen.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/am-thuc",
  //   component: () => import("../pages/Admin/Amthuc.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/tam-linh",
  //   component: () => import("../pages/Admin/TamLinh.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/giai-tri",
  //   component: () => import("../pages/Admin/GiaiTri.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/check-in",
  //   component: () => import("../pages/Admin/Checkin.vue"),
  //   meta: { layout: "default" },
  // },

  // {
  //   path: "/admin/quan-ly-danh-gia-phan-hoi",
  //   component: () => import("../pages/Admin/QuanLyDanhGia.vue"),
  //   meta: { layout: "default" },
  // },
  // {
  //   path: "/admin/reports",
  //   component: () => import("../pages/Admin/Reports.vue"),
  //   meta: { layout: "default" },
  // },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

export default router;
