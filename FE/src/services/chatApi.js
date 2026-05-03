import api from './api';
import { getClientAccessToken } from '../utils/clientAuth';

export async function sendNhomChatMessage(payload) {
  const { id_nhom_du_lich, id_chi_tiet_nhom, message } = payload || {};

  const response = await api.post(
    '/nhom-chats',
    {
      id_nhom_du_lich,
      id_chi_tiet_nhom,
      message,
    }
  );

  return response.data;
}
