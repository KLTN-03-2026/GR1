import axios from 'axios';
import { clearClientSession } from '../utils/clientAuth';

const DEFAULT_API_BASE_URL = 'http://localhost:8000/api';

export const CLIENT_API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || DEFAULT_API_BASE_URL;

const clientApi = axios.create({
  baseURL: CLIENT_API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

clientApi.interceptors.request.use((config) => {
  const token = localStorage.getItem('client_token');
  const headers = config.headers || {};

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  } else {
    headers.Authorization = undefined;
  }

  config.headers = headers;
  return config;
});

clientApi.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      clearClientSession();
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
    return 'Yêu cầu không còn hợp lệ hoặc lời mời đã được xử lý trước đó.';
  }

  if (error?.code === 'ERR_NETWORK') {
    return 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
  }

  return fallbackMessage || 'Đã xảy ra lỗi. Vui lòng thử lại.';
}

export default clientApi;
