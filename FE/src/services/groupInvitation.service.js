import clientApi, { getApiErrorMessage } from './clientApi';

/**
 * @typedef {Object} ApiStatusResponse
 * @property {boolean} status
 * @property {string} message
 */

/**
 * @typedef {Object} GroupInviteItem
 * @property {number} id_thanh_vien
 * @property {number} id_nhom
 * @property {string} ten_nhom
 * @property {string | null} mo_ta
 * @property {string} nguoi_tao
 */

/**
 * @typedef {Object} GroupInvitesResponse
 * @property {boolean} status
 * @property {GroupInviteItem[]} data
 */

export async function inviteMember(idNhom, email) {
  try {
    const response = await clientApi.post('/client/nhom-du-lich/invite', {
      id_nhom: idNhom,
      email,
    });

    return response.data;
  } catch (error) {
    throw new Error(getApiErrorMessage(error, 'Không thể gửi lời mời lúc này.'));
  }
}

export async function getInvites() {
  try {
    const response = await clientApi.get('/client/nhom-du-lich/get-invites');
    return response.data;
  } catch (error) {
    throw new Error(getApiErrorMessage(error, 'Không thể tải danh sách lời mời.'));
  }
}

export async function respondInvite(idThanhVien, chapNhan) {
  try {
    const response = await clientApi.post('/client/nhom-du-lich/accept-invite', {
      id_thanh_vien: idThanhVien,
      chap_nhan: chapNhan,
    });

    return response.data;
  } catch (error) {
    throw new Error(getApiErrorMessage(error, 'Không thể xử lý lời mời lúc này.'));
  }
}
