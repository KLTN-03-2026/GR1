<template>
  <div v-if="open" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-box">
      <button class="modal-close" @click="$emit('close')">
        <i class="bi bi-x-lg"></i>
      </button>

      <div class="modal-icon-head">
        <i class="bi bi-person-plus-fill"></i>
      </div>

      <h3>Mời thành viên vào "{{ groupName }}"</h3>

      <div class="mform-group">
        <label>Email tài khoản thành viên</label>
        <div class="invite-input-row">
          <input
            :value="email"
            class="mform-input"
            type="email"
            placeholder="email@example.com"
            :disabled="loading"
            @input="$emit('update:email', $event.target.value)"
            @keyup.enter="$emit('submit')"
          />
          <button class="btn-brand-lg" @click="$emit('submit')" :disabled="loading">
            <span v-if="loading"><i class="bi bi-hourglass-split"></i></span>
            <span v-else>Mời</span>
          </button>
        </div>
        <span v-if="error" class="err-msg">{{ error }}</span>
      </div>

      <div v-if="message" class="save-alert" :class="messageType">{{ message }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GroupInviteModal',
  props: {
    open: {
      type: Boolean,
      default: false,
    },
    groupName: {
      type: String,
      default: '',
    },
    email: {
      type: String,
      default: '',
    },
    loading: {
      type: Boolean,
      default: false,
    },
    error: {
      type: String,
      default: '',
    },
    message: {
      type: String,
      default: '',
    },
    messageType: {
      type: String,
      default: 'success',
    },
  },
  emits: ['close', 'submit', 'update:email'],
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

.modal-box h3 {
  font-size: 1.2rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 1.2rem;
}

.mform-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  margin-bottom: 1rem;
}

.mform-group label {
  font-size: 0.86rem;
  font-weight: 600;
  color: #3d5166;
}

.mform-input {
  padding: 0.65rem 1rem;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.85rem;
  font-size: 0.95rem;
  background: #fff;
  color: #1e2d44;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  width: 100%;
}

.mform-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}

.invite-input-row {
  display: flex;
  gap: 0.5rem;
}

.invite-input-row .mform-input {
  flex: 1;
}

.btn-brand-lg {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.72rem 1.6rem;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
  transition: all 0.2s;
}

.btn-brand-lg:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.err-msg {
  font-size: 0.8rem;
  color: #f43f5e;
}

.save-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.7rem 1rem;
  border-radius: 0.7rem;
  font-size: 0.88rem;
  font-weight: 600;
}

.save-alert.success {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.save-alert.error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}
</style>
