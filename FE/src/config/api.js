/**
 * API Configuration - Tập trung quản lý URL backend
 * Thay đổi port tại đây nếu cần (mặc định: 8000)
 */
export const API_PORT = 8000;
export const BACKEND_URL = import.meta.env.VITE_BACKEND_URL || `http://localhost:${API_PORT}`;
export const BASE = `${BACKEND_URL}/api`;


export default { BACKEND_URL, BASE };
