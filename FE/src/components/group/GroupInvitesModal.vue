<template>
  <div v-if="open" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-box">
      <button class="modal-close" @click="$emit('close')">
        <i class="bi bi-x-lg"></i>
      </button>

      <div class="modal-icon-head invite-icon">
        <i class="bi bi-envelope-open-heart-fill"></i>
      </div>

      <h3>Lời mời tham gia nhóm</h3>

      <div v-if="loading" class="ndl-loading compact">
        <div class="spinner"></div>
        <span>Đang tải lời mời...</span>
      </div>

      <div v-else-if="error" class="save-alert error">{{ error }}</div>

      <div v-else-if="invites.length === 0" class="ndl-empty small-empty">
        <i class="bi bi-inbox"></i>
        <p>Không có lời mời nào đang chờ.</p>
      </div>

      <div v-else class="invite-list">
        <div v-for="invite in invites" :key="invite.id_thanh_vien" class="invite-item">
          <!-- Avatar hiển thị -->
          <div class="invite-avatar">
            <img v-if="getAvatarUrl(invite)" :src="getAvatarUrl(invite)" alt="avt" class="avt-img" />
            <div v-else class="avt-letter" :style="{ background: groupColor(invite.id_nhom_du_lich || invite.id_thanh_vien) }">
              {{ invite.ten_nhom ? invite.ten_nhom.charAt(0).toUpperCase() : '?' }}
            </div>
          </div>

          <div class="invite-info">
            <strong>{{ invite.ten_nhom }}</strong>
            <span>Được mời bởi: {{ invite.nguoi_tao }}</span>
            <span v-if="invite.mo_ta" class="invite-desc">{{ invite.mo_ta }}</span>
          </div>

          <div class="invite-btns">
            <button
              class="btn-accept"
              :disabled="invite.responding"
              title="Chấp nhận"
              @click="$emit('respond', invite, true)"
            >
              <i v-if="!invite.responding" class="bi bi-check-lg"></i>
              <i v-else class="bi bi-hourglass-split"></i>
            </button>
            <button
              class="btn-reject"
              :disabled="invite.responding"
              title="Từ chối"
              @click="$emit('respond', invite, false)"
            >
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GroupInvitesModal',
  props: {
    open: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    error: {
      type: String,
      default: '',
    },
    invites: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close', 'respond'],
  methods: {
    getAvatarUrl(invite) {
      const path = invite.anh_dai_dien || invite.hinh_anh || invite.hinh_dai_dien || invite.nguoi_gui?.anh_dai_dien || invite.nguoi_gui?.hinh_anh || invite.nguoi_gui?.hinh_dai_dien;
      if (!path) return '';
      if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) {
        return path;
      }
      const cleanPath = path.startsWith('/') ? path : `/${path}`;
      return `http://127.0.0.1:8000${cleanPath}`;
    },
    groupColor(id) {
      const colors = ['#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#ec4899', '#f97316'];
      return colors[(Number(id) || 0) % colors.length];
    }
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 500;
  backdrop-filter: blur(4px);
}

.modal-box {
  background: #fff;
  border-radius: 1.5rem;
  padding: 2rem;
  max-width: 440px;
  width: 92%;
  position: relative;
  animation: popIn 0.28s ease both;
  box-shadow: 0 30px 60px rgba(15, 23, 42, 0.18);
}

@keyframes popIn {
  from {
    transform: scale(0.88);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1.5px solid #e2e8f0;
  background: #f8fafc;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #5a6d80;
  font-size: 0.9rem;
  transition: all 0.18s;
}

.modal-close:hover {
  background: #f43f5e;
  border-color: #f43f5e;
  color: #fff;
}

.modal-icon-head {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, #dcfce7, #dbeafe);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  color: #10b981;
  margin-bottom: 0.75rem;
}

.invite-icon {
  background: linear-gradient(135deg, #fef9c3, #fce7f3);
  color: #f59e0b;
}

.modal-box h3 {
  font-size: 1.2rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 1.2rem;
}

.ndl-loading {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 3rem;
  color: #7a8ea0;
  font-size: 0.92rem;
}

.ndl-loading.compact {
  padding: 1rem 0;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e8edf5;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.save-alert.error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.7rem 1rem;
  border-radius: 0.7rem;
  font-size: 0.88rem;
  font-weight: 600;
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.ndl-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.7rem;
  padding: 4rem 1rem;
  text-align: center;
  color: #7a8ea0;
}

.small-empty {
  padding: 1.5rem;
}

.small-empty i {
  font-size: 1.8rem;
}

.invite-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 340px;
  overflow-y: auto;
  margin-bottom: 0.5rem;
}

.invite-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  background: #f8fbff;
  border-radius: 0.9rem;
  padding: 0.9rem 1rem;
  border: 1px solid #e6ecf5;
}

.invite-avatar {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avt-img {
  width: 100%; height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #fff;
}

.avt-letter {
  width: 100%; height: 100%; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-weight: 700; font-size: 1.1rem;
  border: 2px solid #fff;
}

.invite-info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  flex: 1; /* Thêm để đẩy nút qua phải */
}

.invite-info strong {
  font-size: 0.94rem;
  color: #1e2d44;
}

.invite-info span {
  font-size: 0.8rem;
  color: #7a8ea0;
}

.invite-desc {
  font-style: italic;
}

.invite-btns {
  display: flex;
  gap: 0.4rem;
  flex-shrink: 0;
}

.btn-accept,
.btn-reject {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.18s;
}

.btn-accept {
  background: #dcfce7;
  color: #15803d;
}

.btn-accept:hover:not(:disabled) {
  background: #16a34a;
  color: #fff;
}

.btn-reject {
  background: #fff1f2;
  color: #e11d48;
}

.btn-reject:hover:not(:disabled) {
  background: #e11d48;
  color: #fff;
}

.btn-accept:disabled,
.btn-reject:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}
</style>
