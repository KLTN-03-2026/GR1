import { ref } from 'vue';
import {
  getInvites,
  inviteMember,
  respondInvite,
} from '../services/groupInvitation.service';

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function useGroupInvites(toast) {
  const pendingInvites = ref([]);
  const invitesLoading = ref(false);
  const inviteSubmitting = ref(false);
  const inviteError = ref('');

  function normalizeInvite(invite) {
    return {
      ...invite,
      responding: false,
    };
  }

  async function fetchInvites() {
    invitesLoading.value = true;
    inviteError.value = '';

    try {
      const response = await getInvites();
      pendingInvites.value = (response.data || []).map(normalizeInvite);
      return response;
    } catch (error) {
      const message = error.message || 'Không thể tải danh sách lời mời.';
      inviteError.value = message;
      pendingInvites.value = [];
      throw error;
    } finally {
      invitesLoading.value = false;
    }
  }

  function validateInviteEmail(email) {
    const normalizedEmail = String(email || '').trim().toLowerCase();

    if (!normalizedEmail) {
      return 'Vui lòng nhập email tài khoản thành viên.';
    }

    if (!EMAIL_PATTERN.test(normalizedEmail)) {
      return 'Email chưa đúng định dạng.';
    }

    return '';
  }

  async function submitInvite(idNhom, email) {
    const normalizedEmail = String(email || '').trim().toLowerCase();
    const validationError = validateInviteEmail(normalizedEmail);

    if (validationError) {
      throw new Error(validationError);
    }

    inviteSubmitting.value = true;

    try {
      const response = await inviteMember(idNhom, normalizedEmail);

      if (!response?.status) {
        throw new Error(response?.message || 'Không thể gửi lời mời lúc này.');
      }

      toast?.success(response.message || 'Mời thành viên thành công.');
      return response;
    } catch (error) {
      const message = error.message || 'Không thể gửi lời mời lúc này.';
      toast?.error(message);
      throw new Error(message);
    } finally {
      inviteSubmitting.value = false;
    }
  }

  async function handleInviteResponse(invite, chapNhan) {
    invite.responding = true;

    try {
      const response = await respondInvite(invite.id_thanh_vien, chapNhan);

      if (!response?.status) {
        throw new Error(response?.message || 'Không thể xử lý lời mời lúc này.');
      }

      pendingInvites.value = pendingInvites.value.filter(
        (item) => item.id_thanh_vien !== invite.id_thanh_vien
      );

      toast?.success(
        response.message || (chapNhan ? 'Đã tham gia nhóm.' : 'Đã từ chối lời mời.')
      );

      return response;
    } catch (error) {
      const message = error.message || 'Không thể xử lý lời mời lúc này.';
      toast?.error(message);
      throw new Error(message);
    } finally {
      invite.responding = false;
    }
  }

  return {
    pendingInvites,
    invitesLoading,
    inviteSubmitting,
    inviteError,
    fetchInvites,
    submitInvite,
    handleInviteResponse,
    validateInviteEmail,
  };
}
