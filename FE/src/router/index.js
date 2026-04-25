import { createRouter, createWebHistory } from "vue-router";

const routes = [
  // =================== Client routes =================
  {
    path: "/",
    component: () => import("../pages/Client/Home.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/client/dang-nhap",
    component: () => import("../pages/Client/DangNhap.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/auth/callback",
    alias: "/client/auth/callback",
    component: () => import("../pages/Client/GoogleAuthCallback.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/auth/error",
    alias: "/client/auth/error",
    component: () => import("../pages/Client/AuthError.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/client/dang-ky",
    component: () => import("../pages/Client/DangKy.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/client/quen-mat-khau",
    component: () => import("../pages/Client/QuenMatKhau.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/client/dat-lai-mat-khau/:key",
    component: () => import("../pages/Client/DatLaiMatKhau.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/client/kich-hoat/:hash_active",
    component: () => import("../pages/Client/kichHoat.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/tao-lich-trinh",
    component: () => import("../pages/Client/TaoLichTrinh.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/lich-trinh-cua-toi",
    component: () => import("../pages/Client/LichTrinhCuaToi.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/lich-trinh/:id",
    component: () => import("../pages/Client/ChiTietLichTrinh.vue"),
    meta: { layout: "client" }
  },
  {
    path: "/chi-tiet-dia-diem/:id",
    component: () => import("../pages/Client/DiaDiem/ChiTietDiaDiem.vue"),
    meta: { layout: "client" }
  },
  {
    path: "/nhom-du-lich",
    component: () => import("../pages/Client/NhomDuLich.vue"),
    meta: { layout: "client" },
  },
  {

    path: "/nhom-du-lich/:id/chat",
    component: () => import("../pages/Client/GroupChatView.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/yeu-thich",
    component: () => import("../pages/Client/YeuThich.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/profile",
    component: () => import("../pages/Client/profile.vue"),
    meta: { layout: "client" },
  },
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
  {
    path: "/admin/dang-nhap",
    component: () => import("../pages/Admin/DangNhap.vue"),
    meta: { layout: "blank" },
  },

  {
    path: "/admin/dashboard",
    component: () => import("../pages/Admin/Dashboard.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/profile",
    component: () => import("../pages/Admin/Profile.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/users",
    component: () => import("../pages/Admin/Users.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/danh-sach-nhan-vien",
    component: () => import("../pages/Admin/DanhSachNhanVien.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/phan-quyen",
    component: () => import("../pages/Admin/PhanQuyen.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/am-thuc",
    component: () => import("../pages/Admin/Amthuc.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/tam-linh",
    component: () => import("../pages/Admin/TamLinh.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/giai-tri",
    component: () => import("../pages/Admin/GiaiTri.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/check-in",
    component: () => import("../pages/Admin/Checkin.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/danh-muc",
    component: () => import("../pages/Admin/DanhMuc.vue"),
    meta: { layout: "default" },
  },


  {
    path: "/admin/quan-ly-danh-gia-phan-hoi",
    component: () => import("../pages/Admin/QuanLyDanhGia.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/danh-gia-hai-long",
    component: () => import("../pages/Admin/DanhGiaHaiLong.vue"),
    meta: { layout: "default" },
  },
  {
    path: "/admin/reports",
    component: () => import("../pages/Admin/Reports.vue"),
    meta: { layout: "default" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

router.beforeEach((to, from, next) => {
  // Chỉ kiểm tra các route trong khu vực Admin (ngoại trừ trang đăng nhập)
  if (to.path.startsWith('/admin') && to.path !== '/admin/dang-nhap') {
    const raw = localStorage.getItem('admin_data');
    if (!raw) {
      return next('/admin/dang-nhap');
    }

    try {
      const adminData = JSON.parse(raw);
      const isSuperAdmin = Number(adminData?.id_chuc_vu || adminData?.chuc_vu) === 1;
      
      // Nếu là super admin thì qua thoải mái
      if (isSuperAdmin) {
        return next();
      }

      // Logic ánh xạ: Path -> Mã quyền cần thiết
      const routePermissions = {
        '/admin/dashboard': 'dashboard_view',
        '/admin/users': 'user_manage',
        '/admin/danh-muc': 'category_manage',
        '/admin/am-thuc': 'place_amthuc_manage',
        '/admin/tam-linh': 'place_tamlinh_manage',
        '/admin/giai-tri': 'place_giaitri_manage',
        '/admin/check-in': 'place_checkin_manage',
        '/admin/quan-ly-danh-gia-phan-hoi': 'review_manage',
        '/admin/danh-gia-hai-long': 'report_view',
        '/admin/reports': 'report_view'
      };

      const requiredPermission = Object.keys(routePermissions).find(path => to.path.startsWith(path)) 
                                 ? routePermissions[Object.keys(routePermissions).find(path => to.path.startsWith(path))] 
                                 : null;

      if (requiredPermission) {
        const chucNangs = adminData?.chuc_vu?.chuc_nangs || adminData?.chucVu?.chucNangs || [];
        const hasPerm = chucNangs.some(p => p.ma_chuc_nang === requiredPermission);
        
        if (!hasPerm) {
          // Ngăn chặn truy cập nếu không có quyền
          console.warn(`Truy cập bị từ chối: Cần quyền ${requiredPermission} cho đường dẫn ${to.path}`);
          
          // Redirect về một trang khác an toàn nếu có thể (Ví dụ: login hoặc trang báo lỗi)
          // Để đơn giản, cho fallback về login và thông báo
          alert('Bạn không có quyền truy cập trang này!');
          if (from.path && from.path !== '/') {
             return next(false);
          }
          return next('/admin/dang-nhap');
        }
      }
    } catch(e) {
      return next('/admin/dang-nhap');
    }
  }
  
  next();
});

export default router;
