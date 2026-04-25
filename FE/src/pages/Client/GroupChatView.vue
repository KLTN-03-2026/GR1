<template>
  <div class="chat-page">
    <div class="container chat-container py-4">
      <div class="chat-wrapper">
        <!-- HEADER -->
        <div class="chat-header">
          <div class="d-flex align-items-center">
            <button class="btn-back me-3" @click="$router.push('/nhom-du-lich')">
              <i class="bi bi-arrow-left"></i>
            </button>
            <div class="chat-group-info" v-if="group.ten_nhom">
              <div class="avatar-placeholder" :style="{ background: groupColor(groupId) }">
                {{ group.ten_nhom.charAt(0).toUpperCase() }}
              </div>
              <div class="info-text">
                <h2 class="mb-0">{{ group.ten_nhom }}</h2>
                <span class="status-online"><span class="dot"></span>Phòng Chat Trực Tuyến</span>
              </div>
            </div>
            <div class="chat-group-info" v-else>
              <div class="info-text">
                <h2 class="mb-0">Đang tải...</h2>
              </div>
            </div>
          </div>
          <div class="header-actions">
            <span class="text-muted small">{{ group.so_thanh_vien ? `${group.so_thanh_vien} thành viên` : '' }}</span>
          </div>
        </div>

        <!-- MESSAGES -->
        <div class="chat-body" ref="chatBody">
          <div v-if="loading" class="chat-loading">
            <div class="spinner-border text-brand" role="status"></div>
            <span class="ms-2 text-muted">Đang tải tin nhắn...</span>
          </div>
          
          <div v-else-if="processedMessages.length === 0" class="chat-empty">
            <i class="bi bi-chat-heart text-muted mb-3" style="font-size: 3rem;"></i>
            <h5 class="text-muted">Chưa có tin nhắn nào</h5>
            <p class="text-secondary small">Hãy là người đầu tiên bắt đầu cuộc trò chuyện!</p>
          </div>

          <div v-else class="message-list">
            <div v-for="(msg, idx) in processedMessages" :key="msg.id || msg.id_tin_nhan || idx" class="msg-row" :class="{ 'msg-mine': msg.type !== 'leave' && isMine(msg.id_nguoi_gui) }">
              
              <!-- Leave Message System -->
              <div v-if="msg.type === 'leave'" class="leave-message w-100 text-center my-2">
                <span class="small text-muted fst-italic">{{ msg.message }}</span>
              </div>

              <!-- Normal Chat Message -->
              <template v-else>
                <div class="msg-avatar" v-if="!isMine(msg.id_nguoi_gui) && (!processedMessages[idx-1] || processedMessages[idx-1].id_nguoi_gui !== msg.id_nguoi_gui || processedMessages[idx-1].type === 'leave')">
                  <img v-if="getAvatarUrl(msg)" :src="getAvatarUrl(msg)" alt="avatar" class="avt-img" />
                  <div v-else class="avt-letter" :style="{ background: groupColor(msg.id_nguoi_gui) }">{{ msg.ten_nguoi_gui ? msg.ten_nguoi_gui.charAt(0).toUpperCase() : '?' }}</div>
                </div>
                <!-- Vẫn giữ khoảng trống nếu ng gửi liên tiếp là 1 người (ẩn avatar) -->
                <div class="msg-avatar empty" v-else-if="!isMine(msg.id_nguoi_gui)"></div>

                <div class="msg-bubble-wrap">
                  <div class="msg-sender" v-if="!isMine(msg.id_nguoi_gui) && (!processedMessages[idx-1] || processedMessages[idx-1].id_nguoi_gui !== msg.id_nguoi_gui || processedMessages[idx-1].type === 'leave')">
                    {{ msg.ten_nguoi_gui || 'Ẩn danh' }}
                  </div>
                  <div class="msg-bubble p-0 overflow-hidden" v-if="isItineraryShare(msg.message)" style="background: transparent; border: none; box-shadow: none;">
                    <div class="d-flex flex-column bg-white border rounded-4 shadow-sm iti-card">
                      <div class="d-flex align-items-center gap-3 p-3" style="cursor:pointer;" @click="openLink(extractItineraryData(msg.message).link)">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px; background: linear-gradient(135deg, #10b981, #0ea5e9);">
                          <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div>
                          <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.2;">{{ extractItineraryData(msg.message).title }}</div>
                          <div class="text-primary fw-semibold" style="font-size: 0.8rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Xem chi tiết bản đồ</div>
                        </div>
                      </div>
                      <div class="border-top p-2 text-center" v-if="isLeader && extractItineraryData(msg.message).id && tripStatuses[extractItineraryData(msg.message).id] !== 2">
                        <button class="btn btn-sm btn-outline-danger rounded-pill fw-bold" @click.stop="finalizeTrip(extractItineraryData(msg.message).id)">
                          <i class="bi bi-lock-fill me-1"></i> Lưu & kết thúc lịch trình
                        </button>
                      </div>
                      <div class="border-top p-2 text-center bg-light" v-else-if="extractItineraryData(msg.message).id && tripStatuses[extractItineraryData(msg.message).id] === 2">
                        <span class="text-success fw-bold small"><i class="bi bi-check-circle-fill me-1"></i>Đã chốt lịch trình</span>
                      </div>
                    </div>
                  </div>
                  <div class="msg-bubble p-0 overflow-hidden" v-else-if="isPlaceShare(msg.message)" style="cursor:pointer; background: transparent; border: none; box-shadow: none;" @click="$router.push('/chi-tiet-dia-diem/' + extractPlaceData(msg.message).id)">
                    <div class="d-flex align-items-center gap-3 p-3 bg-white border rounded-4 shadow-sm iti-card">
                      <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:60px;height:60px; overflow: hidden; background: #eee;">
                        <img :src="extractPlaceData(msg.message).image || 'https://via.placeholder.com/60'" style="width: 100%; height: 100%; object-fit: cover;" />
                      </div>
                      <div>
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.2;">{{ extractPlaceData(msg.message).title }}</div>
                        <div class="text-success fw-semibold" style="font-size: 0.8rem;"><i class="bi bi-geo-alt-fill me-1"></i>Địa điểm gợi ý</div>
                      </div>
                    </div>
                  </div>
                  <div class="msg-bubble" v-else>
                    {{ msg.message }}
                  </div>
                  <div class="msg-time">{{ formatTime(msg.created_at) }}</div>
                </div>
              </template>

            </div>
          </div>
        </div>

        <!-- INPUT -->
        <div class="chat-footer">
          <form @submit.prevent="handleSend" class="chat-form">
            <input 
              v-model="draft" 
              type="text" 
              class="form-control chat-input" 
              placeholder="Nhập tin nhắn..." 
              :disabled="sending"
              ref="inputField"
            />
            <button type="submit" class="btn-send" :disabled="!draft.trim() || sending">
              <i class="bi bi-send-fill" v-if="!sending"></i>
              <span class="spinner-border spinner-border-sm mt-1" v-else></span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useNhomChatSocket } from '../../composables/useNhomChatSocket';
import { CLIENT_API_BASE_URL } from '../../services/clientApi';
import { getClientAccessToken } from '../../utils/clientAuth';

const route = useRoute();
const groupId = route.params.id;

const group = ref({});
const members = ref([]);
const loading = ref(true);
const error = ref('');
const draft = ref('');
const chatBody = ref(null);
const inputField = ref(null);

const token = getClientAccessToken();
const currentUserId = Number(localStorage.getItem('client_id') || 0);

// Fetch group info
const fetchGroupInfo = async () => {
  try {
    const response = await fetch(`${CLIENT_API_BASE_URL}/client/nhom-du-lich/${groupId}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
    });
    const data = await response.json();
    if (data.status) {
      group.value = data.data;
    } else {
      error.value = data.message || 'Không thể tải thông tin nhóm';
    }
  } catch (err) {
    error.value = 'Lỗi kết nối';
  }
};

// Fetch members
const fetchMembers = async () => {
  try {
    const response = await fetch(`${CLIENT_API_BASE_URL}/client/nhom-du-lich/members/${groupId}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
    });
    const data = await response.json();
    if (data.status) {
      members.value = data.data || [];
    }
  } catch (err) {
    // Ignore
  }
};

// Load initial messages
const loadInitialMessages = async () => {
  try {
    const response = await fetch(`${CLIENT_API_BASE_URL}/nhom-chats?id_nhom_du_lich=${groupId}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
    });
    const data = await response.json();
    if (data.status) {
      return data.data || [];
    }
    return [];
  } catch (err) {
    return [];
  }
};

// Use socket
const { messages, sending, sendMessage, appendMessage } = useNhomChatSocket(ref(groupId), []);

const processedMessages = computed(() => {
  const msgs = [...messages.value];
  const activeUserIds = new Set(members.value.map(m => Number(m.id_nguoi_dung)));
  const leaveMessages = [];

  // Group messages by user
  const userMessages = {};
  msgs.forEach(msg => {
    if (!userMessages[msg.id_nguoi_gui]) userMessages[msg.id_nguoi_gui] = [];
    userMessages[msg.id_nguoi_gui].push(msg);
  });

  // For each user, if not active, add leave message after last message
  Object.keys(userMessages).forEach(userId => {
    if (!activeUserIds.has(Number(userId))) {
      const lastMsg = userMessages[userId][userMessages[userId].length - 1];
      if (lastMsg) {
        leaveMessages.push({
          id: `leave-${userId}`,
          type: 'leave',
          message: lastMsg.ten_nguoi_gui ? `${lastMsg.ten_nguoi_gui} đã rời khỏi nhóm` : `(Người dùng ẩn) đã rời khỏi nhóm`,
          created_at: lastMsg.created_at,
        });
      }
    }
  });

  // Sort all messages by created_at
  return [...msgs, ...leaveMessages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
});

const isLeader = computed(() => {
  const member = members.value.find(m => Number(m.id_nguoi_dung) === currentUserId);
  return member && member.vai_tro === 'truong_nhom';
});

const tripStatuses = ref({});

const finalizeTrip = async (tripId) => {
    if (!tripId) return;
    try {
        const res = await fetch(`${CLIENT_API_BASE_URL}/client/chuyen-di/${tripId}/chot-lich-trinh`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.status) {
            tripStatuses.value[tripId] = 2; // mark as finalized
            alert('Đã chốt lịch trình thành công!');
        } else {
            alert(data.message);
        }
    } catch(err) {
        console.error(err);
    }
};

watch(processedMessages, (newMsgs) => {
    newMsgs.forEach(msg => {
        if (isItineraryShare(msg.message)) {
            const data = extractItineraryData(msg.message);
            if (data.id && tripStatuses.value[data.id] === undefined) {
                tripStatuses.value[data.id] = 1; // Default
                fetch(`${CLIENT_API_BASE_URL}/client/chuyen-di/${data.id}`, {
                    headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
                }).then(r => r.json()).then(d => {
                    if (d.status && d.data) {
                        tripStatuses.value[data.id] = d.data.trang_thai;
                    }
                }).catch(() => {});
            }
        }
    });
}, { immediate: true, deep: true });

const isMine = (id_nguoi_gui) => {
  return Number(id_nguoi_gui) === currentUserId;
};

const getAvatarUrl = (msg) => {
  const path = msg.anh_dai_dien || msg.hinh_anh || msg.hinh_dai_dien || msg.nguoi_gui?.anh_dai_dien || msg.nguoi_gui?.hinh_anh || msg.nguoi_gui?.hinh_dai_dien;
  if (!path) return '';
  if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) {
    return path;
  }
  const cleanPath = path.startsWith('/') ? path : `/${path}`;
  return `http://127.0.0.1:8000${cleanPath}`;
};

const scrollToBottom = async () => {
  await nextTick();
  if (chatBody.value) {
    chatBody.value.scrollTop = chatBody.value.scrollHeight;
  }
};

watch(messages, () => {
  scrollToBottom();
}, { deep: true });

const groupColor = (id) => {
  const colors = [
    'linear-gradient(135deg,#10b981,#0ea5e9)',
    'linear-gradient(135deg,#8b5cf6,#ec4899)',
    'linear-gradient(135deg,#f59e0b,#ef4444)',
    'linear-gradient(135deg,#06b6d4,#3b82f6)',
    'linear-gradient(135deg,#84cc16,#10b981)',
    'linear-gradient(135deg,#f97316,#eab308)',
  ];
  return colors[Number(id) % colors.length];
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  const date = new Date(isoString);
  return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
};

const isItineraryShare = (text) => {
  if (!text) return false;
  if (typeof text === 'object' && text.type === 'itinerary') return true;
  if (typeof text === 'string' && text.includes('"type":"itinerary"')) return true;
  try {
    const obj = JSON.parse(text);
    if (obj && obj.type === 'itinerary') return true;
  } catch (e) {}
  return /Tôi vừa chia sẻ một lịch trình thú vị: "([^"]+)".*Click vào đây để xem chi tiết: (http\S+)/s.test(String(text));
};

const isPlaceShare = (text) => {
  if (!text) return false;
  if (typeof text === 'object' && text.type === 'place') return true;
  if (typeof text === 'string' && text.includes('"type":"place"')) return true;
  try {
    const obj = JSON.parse(text);
    if (obj && obj.type === 'place') return true;
  } catch (e) {}
  return false;
};

const extractPlaceData = (text) => {
  if (!text) return { title: '', image: '', id: '' };
  if (typeof text === 'object' && text.type === 'place') return text;
  try {
    const obj = JSON.parse(text);
    if (obj && obj.type === 'place') return obj;
  } catch (e) {}
  
  if (typeof text === 'string' && text.includes('"type":"place"')) {
     try {
         let clean = text.replace(/\\"/g, '"');
         const obj = JSON.parse(clean);
         if (obj && obj.type === 'place') return obj;
     } catch(ex){}
  }
  return { title: '', image: '', id: '' };
};

const extractItineraryData = (text) => {
  if (!text) return { title: '', link: '', id: null };
  if (typeof text === 'object' && text.type === 'itinerary') {
    return { title: text.title || 'Lịch trình', link: `/lich-trinh/${text.id}`, id: text.id };
  }
  try {
    const obj = JSON.parse(text);
    if (obj && obj.type === 'itinerary') {
      return { title: obj.title || 'Lịch trình', link: `/lich-trinh/${obj.id}`, id: obj.id };
    }
  } catch (e) {}
  
  if (typeof text === 'string' && text.includes('"type":"itinerary"')) {
     try {
         let clean = text.replace(/\\"/g, '"');
         const obj = JSON.parse(clean);
         if (obj && obj.type === 'itinerary') return { title: obj.title || 'Lịch trình', link: `/lich-trinh/${obj.id}`, id: obj.id };
     } catch(ex){}
  }

  const match = String(text).match(/Tôi vừa chia sẻ một lịch trình thú vị: "([^"]+)".*Click vào đây để xem chi tiết: (http\S+)/s);
  if (match) {
    const linkMatch = match[2].match(/\/lich-trinh\/(\d+)/);
    const id = linkMatch ? linkMatch[1] : null;
    return { title: match[1], link: match[2], id };
  }
  return { title: '', link: '', id: null };
};

const openLink = (url) => {
  if(url) window.location.href = url;
};

const handleSend = async () => {
  const text = draft.value.trim();
  if (!text) return;

  const member = members.value.find(m => Number(m.id_nguoi_dung) === currentUserId);
  
  // Nếu API backend tạo lỗi thì dừng
  if (!member) {
    this.$toast.warning('Bạn không phải thành viên của nhóm này');
    return;
  }

  try {
    await sendMessage({
      id_chi_tiet_nhom: member.id_thanh_vien || member.id_chi_tiet_nhom,
      message: text, // Sử dụng đúng syntax map cho Socket (message text)
    });
    draft.value = '';
    scrollToBottom();
    nextTick(() => {
      if (inputField.value) inputField.value.focus();
    });
  } catch (err) {
    console.error('Send failed:', err);
  }
};

onMounted(async () => {
  await Promise.all([fetchGroupInfo(), fetchMembers()]);
  
  const initialMessages = await loadInitialMessages();
  if (Array.isArray(initialMessages)) {
    initialMessages.forEach((message) => appendMessage(message));
  }
  
  loading.value = false;
  scrollToBottom();
});
</script>

<style scoped>
/* Reset & Fonts */
* { box-sizing: border-box; }

.chat-page {
  min-height: 100vh;
  background-color: #f4f7fb;
  background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
  background-size: 24px 24px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1e2d44;
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

.chat-container {
  width: 100%;
  max-width: 1040px;
  height: calc(100vh - 80px);
  display: flex;
  flex-direction: column;
}

.chat-wrapper {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-radius: 1.5rem;
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.6);
  display: flex;
  flex-direction: column;
  flex: 1;
  overflow: hidden;
  transition: all 0.3s ease;
}

/* Header */
.chat-header {
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(15, 23, 42, 0.05);
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
  z-index: 10;
}

.btn-back {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 50%;
  width: 42px; height: 42px;
  display: flex; align-items: center; justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.btn-back:hover { 
  background: #10b981; 
  color: #ffffff; 
  transform: translateY(-2px);
  border-color: #10b981;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
}

.chat-group-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.avatar-placeholder {
  width: 48px; height: 48px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.3rem; font-weight: 700;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.info-text h2 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.01em;
  margin-bottom: 0.2rem;
  line-height: 1.2;
}

.status-online {
  font-size: 0.78rem;
  font-weight: 600;
  color: #10b981;
  display: flex; align-items: center; gap: 0.4rem;
}
.dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
  70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Body */
.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem 10% 2rem 10%;
  display: flex;
  flex-direction: column;
  scroll-behavior: smooth;
}

@media (max-width: 1200px) {
  .chat-body { padding: 1.5rem 5% 2rem 5%; }
}
@media (max-width: 768px) {
  .chat-body { padding: 1rem; }
}

.chat-body::-webkit-scrollbar {
  width: 6px;
}
.chat-body::-webkit-scrollbar-thumb {
  background-color: rgba(15, 23, 42, 0.1);
  border-radius: 10px;
}
.chat-body::-webkit-scrollbar-track {
  background: transparent;
}

.chat-loading, .chat-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  height: 100%;
}
.text-brand { color: #10b981 !important; }

/* Messages */
.message-list {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.msg-row {
  display: flex;
  gap: 0.8rem;
  max-width: 80%;
  animation: fadeInMsg 0.3s ease forwards;
  opacity: 0;
  transform: translateY(15px);
}
@keyframes fadeInMsg {
  to { opacity: 1; transform: translateY(0); }
}

.msg-mine {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.msg-avatar {
  width: 40px; height: 40px;
  flex-shrink: 0;
  display: flex; align-items: flex-end;
}
.msg-avatar.empty { width: 40px; height: 1px; }

.avt-img {
  width: 100%; height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #ffffff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}

.avt-letter {
  width: 100%; height: 100%; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-weight: 700; font-size: 0.95rem;
  border: 2px solid #ffffff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}

.msg-bubble-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  max-width: 100%;
}
.msg-mine .msg-bubble-wrap {
  align-items: flex-end;
}

.msg-sender {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.2rem;
  margin-left: 0.5rem;
}
.msg-mine .msg-sender { display: none; }

.msg-bubble {
  padding: 0.8rem 1.2rem;
  font-size: 0.96rem;
  line-height: 1.5;
  background: #ffffff;
  color: #334155;
  border: 1px solid #e2e8f0;
  border-radius: 1.2rem 1.2rem 1.2rem 0.25rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
  position: relative;
  word-break: break-word;
}

.msg-mine .msg-bubble {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #ffffff;
  border: none;
  border-radius: 1.2rem 1.2rem 0.25rem 1.2rem;
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
}

.msg-time {
  font-size: 0.72rem;
  font-weight: 500;
  color: #94a3b8;
  margin-top: 0.4rem;
  margin-right: 0.5rem; margin-left: 0.5rem;
}

/* Footer */
.chat-footer {
  padding: 1.5rem 10%;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid rgba(15, 23, 42, 0.05);
  box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.02);
}

@media (max-width: 1200px) {
  .chat-footer { padding: 1.5rem 5%; }
}
@media (max-width: 768px) {
  .chat-footer { padding: 1rem; }
}

.chat-form {
  display: flex;
  gap: 1rem;
  align-items: center;
  background: #ffffff;
  padding: 0.5rem;
  border-radius: 2.5rem;
  box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
  border: 1px solid #e2e8f0;
  transition: all 0.3s;
}

.chat-form:focus-within {
  border-color: #10b981;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), 0 4px 15px rgba(15, 23, 42, 0.04);
}

.chat-input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 0.7rem 1.2rem;
  font-size: 1rem;
  color: #1e2d44;
  outline: none;
  box-shadow: none;
}
.chat-input::placeholder { color: #94a3b8; }
.chat-input:focus {
  box-shadow: none;
}

.btn-send {
  width: 48px; height: 48px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  flex-shrink: 0;
}
.btn-send:hover:not(:disabled) { 
  transform: translateY(-2px) scale(1.05); 
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
}
.btn-send:active:not(:disabled) {
  transform: translateY(0) scale(0.95);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.btn-send:disabled { 
  background: #f1f5f9;
  color: #94a3b8;
  box-shadow: none; 
  cursor: not-allowed; 
  transform: none;
}

.leave-message {
  padding: 0.5rem 0;
  display: block;
  text-align: center;
  margin: 0.8rem auto;
  background: rgba(241, 245, 249, 0.8);
  border-radius: 1rem;
  width: fit-content;
  padding: 0.4rem 1rem;
  backdrop-filter: blur(4px);
}
.leave-message span {
  font-weight: 500;
  color: #64748b;
  font-size: 0.8rem;
}

@media (max-width: 768px) {
  .chat-container {
    height: 100vh;
    padding: 0;
  }
  .chat-wrapper {
    border-radius: 0;
    border: none;
  }
  .msg-row {
    max-width: 95%;
  }
  .chat-body, .chat-footer {
    padding: 1rem;
  }
}

.iti-card {
  transition: transform 0.2s, box-shadow 0.2s;
  min-width: 280px;
}
.iti-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}
.msg-mine .iti-card {
  border-color: rgba(255,255,255, 0.2) !important;
  background: rgba(255,255,255, 0.95) !important;
}
</style>