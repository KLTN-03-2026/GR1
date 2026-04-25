import axios from 'axios';
import { getClientAccessToken } from '../utils/clientAuth';

const DEFAULT_BACKEND_URL = 'http://localhost:8000';

const BACKEND_URL = (import.meta.env.VITE_BACKEND_URL || DEFAULT_BACKEND_URL).replace(/\/+$/, '');

export async function sendNhomChatMessage(payload) {
  const token = getClientAccessToken();
  const { id_nhom_du_lich, id_chi_tiet_nhom, message } = payload || {};

  const response = await axios.post(
    `${BACKEND_URL}/api/nhom-chats`,
    {
      id_nhom_du_lich,
      id_chi_tiet_nhom,
      message,
    },
    {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: token ? `Bearer ${token}` : undefined,
      },
    }
  );

  return response.data;
}
