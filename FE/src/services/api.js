import axios from "axios";
import { clearClientSession } from '../utils/clientAuth';

// Đảm bảo BASE_URL có hậu tố /api
let envUrl = (import.meta.env.VITE_BACKEND_URL || "").replace(/\/+$/, "");
if (!envUrl.endsWith('/api')) {
  envUrl += '/api';
}
const BASE_URL = envUrl;

const api = axios.create({
  baseURL: BASE_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  },
});

api.interceptors.request.use((config) => {
  const clientToken = localStorage.getItem('client_token');
  const headers = config.headers || {};

  if (!headers.Authorization) {
    if (config.url && config.url.startsWith('/admin')) {
      const adminToken = localStorage.getItem('key_admin');
      if (adminToken) {
        headers.Authorization = `Bearer ${adminToken}`;
      }
    } else {
      if (clientToken) {
        headers.Authorization = `Bearer ${clientToken}`;
      }
    }
  }

  // Remove Content-Type if sending FormData so browser sets the boundary automatically
  if (config.data instanceof FormData) {
    delete headers['Content-Type'];
  }

  config.headers = headers;
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      if (error.config.url && !error.config.url.startsWith('/admin')) {
        clearClientSession();
      }
    }
    return Promise.reject(error);
  }
);

export function getApiErrorMessage(error, fallbackMessage) {
  const status = error?.response?.status;
  const responseMessage = error?.response?.data?.message;

  if (responseMessage) {
    return responseMessage;
  }

  if (status === 401) {
    return 'Phiên đăng nhập đã hết hạn hoặc bạn chưa đăng nhập.';
  }

  if (status === 403) {
    return 'Bạn không có quyền thực hiện thao tác này.';
  }

  if (status === 422) {
    return 'Yêu cầu không còn hợp lệ hoặc lỗi dữ liệu.';
  }

  if (error?.code === 'ERR_NETWORK') {
    return 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
  }

  return fallbackMessage || 'Đã xảy ra lỗi. Vui lòng thử lại.';
}

/**
 * axiosExternal: dùng để gọi các API bên ngoài (không có baseURL backend).
 * Ví dụ: OSRM routing, OpenWeather, v.v.
 * Không tự động gắn Authorization header.
 */
export const axiosExternal = axios.create();

export default api;