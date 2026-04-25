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
            <button class="btn btn-sm btn-outline-primary rounded-pill me-3 fw-bold shadow-sm" @click="goToGroupItinerary" title="Xem lịch trình chính thức của nhóm">
              <i class="bi bi-map-fill me-1"></i> Lịch trình nhóm
            </button>
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
                          <div class="text-primary fw-semibold" style="font-size: 0.8rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Xem chi tiết lịch trình</div>
                        </div>
                      </div>
                      <div class="border-top p-2 text-center" v-if="group.id_chuyen_di == extractItineraryData(msg.message).id">
                        <span class="text-primary fw-bold small d-block mb-1 mt-1"><i class="bi bi-geo-alt-fill me-1"></i>Lịch trình chính thức của nhóm</span>
                      </div>
                      <div class="border-top p-2 text-center d-flex flex-column gap-2" v-if="isLeader && extractItineraryData(msg.message).id">
                        <button v-if="group.id_chuyen_di != extractItineraryData(msg.message).id" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" @click.stop="setGroupItinerary(extractItineraryData(msg.message).id)">
                          <i class="bi bi-geo-alt-fill me-1"></i> Đặt làm lịch trình nhóm
                        </button>
                        <button v-if="tripStatuses[extractItineraryData(msg.message).id] !== 2" class="btn btn-sm btn-outline-danger rounded-pill fw-bold" @click.stop="finalizeTrip(extractItineraryData(msg.message).id)">
                          <i class="bi bi-lock-fill me-1"></i> Lưu & kết thúc lịch trình
                        </button>
                      </div>
                      <div class="border-top p-2 text-center bg-light" v-else-if="extractItineraryData(msg.message).id && tripStatuses[extractItineraryData(msg.message).id] === 2 && (!isLeader)">
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
                  <div class="msg-bubble p-0 overflow-hidden" v-else-if="isLocationShare(msg.message)" style="background: transparent; border: none; box-shadow: none;">
                    <div class="d-flex flex-column bg-white border rounded-4 shadow-sm iti-card">
                      <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px; background: #ef4444;">
                          <i class="bi bi-pin-map-fill fs-5"></i>
                        </div>
                        <div>
                          <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.2;">Vị trí của tôi</div>
                          <div class="text-danger fw-semibold" style="font-size: 0.75rem;">{{ extractLocationData(msg.message).lat.toFixed(4) }}, {{ extractLocationData(msg.message).lng.toFixed(4) }}</div>
                        </div>
                      </div>
                      <div class="p-0 position-relative" style="height: 150px; background: #e5e7eb;">
                        <img :src="`https://static-maps.yandex.ru/1.x/?lang=en-US&ll=${extractLocationData(msg.message).lng},${extractLocationData(msg.message).lat}&z=14&l=map&size=400,150&pt=${extractLocationData(msg.message).lng},${extractLocationData(msg.message).lat},pm2rdm`" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/400x150?text=Map+Unavailable'" />
                      </div>
                      <div class="p-2 text-center bg-light">
                        <a :href="`https://www.google.com/maps?q=${extractLocationData(msg.message).lat},${extractLocationData(msg.message).lng}`" target="_blank" class="text-decoration-none fw-bold small text-primary"><i class="bi bi-box-arrow-up-right me-1"></i>Mở trên Google Maps</a>
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
            <button type="button" class="btn-location me-2" @click="sendLocation" :disabled="sending || locating" title="Gửi vị trí của tôi">
              <i class="bi bi-geo-alt-fill text-danger fs-5" v-if="!locating"></i>
              <span class="spinner-border spinner-border-sm text-danger" v-else></span>
            </button>
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
    <!-- ══════════════════════════════════════════════
         MODAL – Xác nhận đặt Lịch trình nhóm
    ══════════════════════════════════════════════ -->
    <transition name="rating-modal">
      <div v-if="showConfirmModal" class="rating-modal-overlay" @click.self="showConfirmModal = false">
        <div class="confirm-modal-box">
          <div class="text-center mb-3">
            <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#10b981,#0ea5e9);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
              <i class="bi bi-geo-alt-fill text-white fs-3"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Đặt lịch trình chính thức</h5>
            <p class="text-muted" style="font-size:0.95rem;">Bạn có muốn đặt lịch trình này làm <strong>lịch trình chính thức</strong> của nhóm? Mọi thành viên sẽ thấy được lịch trình này.</p>
          </div>
          <div class="d-flex gap-2 justify-content-end">
            <button class="rm-btn-skip" @click="showConfirmModal = false">Hủy</button>
            <button class="rm-btn-submit" @click="confirmSetItinerary" :disabled="confirmingSet">
              <span v-if="confirmingSet"><i class="bi bi-hourglass-split me-1"></i>Đang xử lý...</span>
              <span v-else><i class="bi bi-check2-circle me-1"></i>Xác nhận</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ══════════════════════════════════════════════
         MODAL – Đánh giá Hệ thống
    ══════════════════════════════════════════════ -->
    <transition name="rating-modal">
      <div v-if="showRatingModal" class="rating-modal-overlay" @click.self="skipRating">
        <div class="rating-modal-box">
          <!-- Confetti particles -->
          <div class="confetti-wrap">
            <span v-for="n in 12" :key="n" class="confetti-dot" :style="confettiStyle(n)"></span>
          </div>

          <!-- Header -->
          <div class="rm-header">
            <div class="rm-success-icon">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="rm-title">🎉 Lịch trình đã được lưu!</h2>
            <p class="rm-subtitle">Bạn cảm thấy thế nào về hệ thống lập lịch trình của chúng tôi?</p>
          </div>

          <!-- Face rating icons -->
          <div class="rm-faces">
            <button v-for="face in ratingFaces" :key="face.value" class="rm-face-btn"
              :class="{ selected: selectedRating === face.value }" @click="selectedRating = face.value"
              :title="face.label">
              <span class="rm-face-emoji"></span>
              <span class="rm-face-icon">{{ face.icon }}</span>
              <span class="rm-face-label">{{ face.label }}</span>
            </button>
          </div>

          <!-- Selected rating feedback text -->
          <transition name="fade-slide">
            <p v-if="selectedRating" class="rm-selected-text">
              {{ratingFaces.find(f => f.value === selectedRating)?.feedback}}
            </p>
          </transition>

          <!-- Feedback textarea -->
          <div class="rm-feedback-wrap">
            <label class="rm-feedback-label">
              <i class="bi bi-chat-heart me-1"></i>Để lại đóng góp của bạn (không bắt buộc)
            </label>
            <textarea v-model="ratingFeedback" class="rm-textarea" rows="3"
              placeholder="Ví dụ: Giao diện dễ dùng, AI gợi ý rất hữu ích..."></textarea>
          </div>

          <!-- Actions -->
          <div class="rm-actions">
            <button class="rm-btn-skip" @click="skipRating">
              Bỏ qua
            </button>
            <button class="rm-btn-submit" :disabled="!selectedRating || submittingRating" @click="submitRating">
              <span v-if="submittingRating"><i class="bi bi-hourglass-split me-1"></i>Đang gửi...</span>
              <span v-else><i class="bi bi-send-fill me-1"></i>Gửi đánh giá</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useNhomChatSocket } from '../../composables/useNhomChatSocket';
import { CLIENT_API_BASE_URL } from '../../services/clientApi';
import { getClientAccessToken } from '../../utils/clientAuth';
import { useToast } from 'vue-toast-notification';

const route = useRoute();
const groupId = route.params.id;

const group = ref({});
const members = ref([]);
const loading = ref(true);
const error = ref('');
const draft = ref('');
const chatBody = ref(null);
const inputField = ref(null);
const locating = ref(false);

const token = getClientAccessToken();
const currentUserId = Number(localStorage.getItem('client_id') || 0);
const toast = useToast();

// Confirm modal for setGroupItinerary
const showConfirmModal = ref(false);
const confirmModalTripId = ref(null);
const confirmingSet = ref(false);

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
            showRatingModal.value = true; // Show rating modal
        } else {
            alert(data.message);
        }
    } catch(err) {
        console.error(err);
    }
};

// ── Rating Modal Variables ──
const showRatingModal = ref(false);
const selectedRating = ref(null);
const ratingFeedback = ref('');
const submittingRating = ref(false);
const ratingFaces = [
  { value: 1, icon: '😞', label: 'Rất tệ', feedback: 'Rất tiếc khi nghe điều này. Chúng tôi sẽ cố gắng cải thiện!' },
  { value: 2, icon: '😕', label: 'Tệ', feedback: 'Cảm ơn bạn đã phản hồi. Ý kiến của bạn rất có giá trị!' },
  { value: 3, icon: '😐', label: 'Bình thường', feedback: 'Cảm ơn! Chúng tôi đang nỗ lực để làm tốt hơn.' },
  { value: 4, icon: '😊', label: 'Tốt', feedback: 'Tuyệt vời! Rất vui vì bạn hài lòng với trải nghiệm.' },
  { value: 5, icon: '🤩', label: 'Rất tốt', feedback: 'Cảm ơn bạn rất nhiều! Điều này thật sự truyền cảm hứng cho chúng tôi! 🚀' },
];

const confettiStyle = (n) => {
  const colors = ['#10b981', '#0ea5e9', '#f59e0b', '#f43f5e', '#8b5cf6', '#ec4899'];
  const left = ((n - 1) * (100 / 12)) + '%';
  const delay = (n * 0.15) + 's';
  const color = colors[n % colors.length];
  const size = (8 + (n % 4) * 4) + 'px';
  return {
    left,
    animationDelay: delay,
    background: color,
    width: size,
    height: size,
    borderRadius: n % 2 === 0 ? '50%' : '3px',
  };
};

const submitRating = async () => {
  if (!selectedRating.value) return;
  submittingRating.value = true;
  try {
    await fetch(`${CLIENT_API_BASE_URL}/client/danh-gia-he-thong`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify({
        muc_do_hai_long: selectedRating.value,
        noi_dung: ratingFeedback.value,
      }),
    }).catch(() => { });
  } finally {
    submittingRating.value = false;
    showRatingModal.value = false;
    toast.success('Cảm ơn bạn đã đánh giá hệ thống!', { position: 'top-right' });
  }
};

const skipRating = () => {
  showRatingModal.value = false;
};

const setGroupItinerary = (tripId) => {
    if (!tripId) return;
    confirmModalTripId.value = tripId;
    showConfirmModal.value = true;
};

const confirmSetItinerary = async () => {
    confirmingSet.value = true;
    try {
        const res = await fetch(`${CLIENT_API_BASE_URL}/client/nhom-du-lich/${groupId}/set-lich-trinh`, {
            method: 'POST',
            headers: { 
              Authorization: `Bearer ${token}`,
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_chuyen_di: confirmModalTripId.value })
        });
        const data = await res.json();
        showConfirmModal.value = false;
        if (data.status) {
            group.value.id_chuyen_di = confirmModalTripId.value;
            toast.success('Đã cập nhật lịch trình nhóm thành công!', { position: 'top-right' });
        } else {
            toast.error(data.message || 'Có lỗi xảy ra.', { position: 'top-right' });
        }
    } catch(err) {
        console.error(err);
        toast.error('Lỗi kết nối.', { position: 'top-right' });
        showConfirmModal.value = false;
    } finally {
        confirmingSet.value = false;
    }
};

const goToGroupItinerary = () => {
    if (group.value.id_chuyen_di) {
        window.location.href = `/lich-trinh/${group.value.id_chuyen_di}`;
    } else {
        toast.warning('Trưởng nhóm chưa thiết lập lịch trình chính thức cho nhóm này.', { position: 'top-right' });
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

const isLocationShare = (msg) => {
  if (!msg) return false;
  return msg.startsWith('[LOCATION_SHARE]');
};

const extractLocationData = (msg) => {
  try {
    const jsonStr = msg.replace('[LOCATION_SHARE]', '');
    return JSON.parse(jsonStr);
  } catch (e) {
    return { lat: 0, lng: 0 };
  }
};

const openLink = (url) => {
  if(url) window.location.href = url;
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
      message: text,
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

const sendLocation = () => {
  if (!navigator.geolocation) {
    alert('Trình duyệt của bạn không hỗ trợ định vị.');
    return;
  }
  locating.value = true;
  navigator.geolocation.getCurrentPosition(
    (position) => {
      locating.value = false;
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      const payload = `[LOCATION_SHARE]${JSON.stringify({ lat, lng })}`;
      sendMessage({
        id_chi_tiet_nhom: members.value.find(m => Number(m.id_nguoi_dung) === currentUserId)?.id_thanh_vien,
        message: payload
      });
    },
    (error) => {
      locating.value = false;
      alert('Không thể lấy vị trí hiện tại. Vui lòng kiểm tra quyền truy cập.');
    }
  );
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
  align-items: center;
  background: #f8f9fa;
  border-radius: 2rem;
  padding: 0.5rem 0.5rem 0.5rem 1rem;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

.btn-location {
  background: #fff;
  border: 1px solid #e2e8f0;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-location:hover:not(:disabled) {
  background: #fee2e2;
  border-color: #fca5a5;
  transform: scale(1.05);
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

/* ══════════════════════════════════════════
   Rating Modal
   ══════════════════════════════════════════ */
.rating-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 20, 40, 0.65);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.rating-modal-box {
  background: #fff;
  border-radius: 2rem;
  padding: 2.5rem 2.2rem 2rem;
  max-width: 540px;
  width: 100%;
  position: relative;
  overflow: hidden;
  box-shadow: 0 32px 80px rgba(10, 20, 60, 0.28);
  animation: modalBounceIn 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

.confirm-modal-box {
  background: #fff;
  border-radius: 1.5rem;
  padding: 2rem 1.8rem 1.5rem;
  max-width: 420px;
  width: 100%;
  box-shadow: 0 24px 60px rgba(10, 20, 60, 0.22);
  animation: modalBounceIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

@keyframes modalBounceIn {
  from {
    opacity: 0;
    transform: scale(0.82) translateY(30px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Confetti */
.confetti-wrap {
  position: absolute;
  top: -6px;
  left: 0;
  width: 100%;
  height: 60px;
  overflow: hidden;
  pointer-events: none;
}

.confetti-dot {
  position: absolute;
  top: 0;
  opacity: 0;
  animation: confettiFall 1.4s ease-out forwards;
}

@keyframes confettiFall {
  0% { opacity: 0; transform: translateY(-10px) rotate(0deg); }
  20% { opacity: 1; }
  100% { opacity: 0; transform: translateY(72px) rotate(180deg); }
}

/* Header */
.rm-header {
  text-align: center;
  margin-bottom: 1.8rem;
}

.rm-success-icon {
  font-size: 3rem;
  color: #10b981;
  margin-bottom: 0.6rem;
  animation: bounceScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
}

@keyframes bounceScale {
  from { transform: scale(0); }
  to { transform: scale(1); }
}

.rm-title {
  font-size: 1.55rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 0.35rem;
}

.rm-subtitle {
  font-size: 0.97rem;
  color: #627289;
  margin: 0;
}

/* Face buttons */
.rm-faces {
  display: flex;
  justify-content: center;
  gap: 0.6rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.rm-face-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.3rem;
  padding: 0.7rem 0.9rem;
  border: 2.5px solid #e2e8f0;
  border-radius: 1.1rem;
  background: #f8faff;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
  min-width: 72px;
}

.rm-face-btn:hover {
  border-color: #10b981;
  background: #f0fdf8;
  transform: translateY(-4px) scale(1.07);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.18);
}

.rm-face-btn.selected {
  border-color: #10b981;
  background: linear-gradient(135deg, #d1fae5, #e0f2fe);
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.25);
  transform: translateY(-4px) scale(1.1);
}

.rm-face-icon {
  font-size: 2rem;
  line-height: 1;
  transition: transform 0.2s;
}

.rm-face-btn:hover .rm-face-icon,
.rm-face-btn.selected .rm-face-icon {
  transform: scale(1.18);
}

.rm-face-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: #5a7080;
  white-space: nowrap;
}

.rm-face-btn.selected .rm-face-label {
  color: #065f46;
}

/* Selected rating feedback text */
.rm-selected-text {
  text-align: center;
  font-size: 0.9rem;
  font-weight: 500;
  color: #065f46;
  background: #d1fae5;
  border-radius: 0.7rem;
  padding: 0.55rem 1rem;
  margin: 0 0 1.2rem;
  animation: fadeUp 0.3s ease both;
}

/* Feedback textarea */
.rm-feedback-wrap {
  margin-bottom: 1.5rem;
}

.rm-feedback-label {
  display: block;
  font-size: 0.87rem;
  font-weight: 600;
  color: #3d5166;
  margin-bottom: 0.4rem;
}

.rm-textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.9rem;
  font-size: 0.95rem;
  font-family: inherit;
  color: #1e2d44;
  background: #f8fbff;
  resize: vertical;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.rm-textarea:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}

/* Actions */
.rm-actions {
  display: flex;
  gap: 0.8rem;
  justify-content: flex-end;
}

.rm-btn-skip {
  padding: 0.65rem 1.4rem;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.9rem;
  background: #fff;
  color: #627289;
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.rm-btn-skip:hover {
  background: #f0f4f8;
  border-color: #b0bec9;
}

.rm-btn-submit {
  padding: 0.65rem 1.6rem;
  border: none;
  border-radius: 0.9rem;
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-size: 0.92rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.22s;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}

.rm-btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(16, 185, 129, 0.38);
}

.rm-btn-submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Transitions */
.rating-modal-enter-active,
.rating-modal-leave-active {
  transition: opacity 0.3s ease;
}

.rating-modal-enter-from,
.rating-modal-leave-to {
  opacity: 0;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>