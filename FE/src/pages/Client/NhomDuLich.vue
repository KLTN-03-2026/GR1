<template>
  <div class="ndl-page">
    <div class="container ndl-container">

      <!-- ── HEADER ── -->
      <div class="ndl-header">
        <div>
          <h1><i class="bi bi-people-fill me-2 text-brand"></i>Nhóm du lịch</h1>
          <p>Quản lý nhóm đồng hành, mời bạn bè cùng lên kế hoạch chuyến đi.</p>
        </div>
        <div class="header-actions">
          <!-- Bell invite badge -->
          <button class="bell-btn" @click="showInvites = true" :class="{ pulse: pendingInvites.length > 0 }">
            <i class="bi bi-bell-fill"></i>
            <span v-if="pendingInvites.length > 0" class="bell-badge">{{ pendingInvites.length }}</span>
          </button>
          <button class="btn-brand-lg" @click="openCreateModal">
            <i class="bi bi-plus-circle me-2"></i>Tạo nhóm mới
          </button>
        </div>
      </div>

      <!-- ── Not logged in ── -->
      <div v-if="!token" class="ndl-empty">
        <i class="bi bi-lock-fill"></i>
        <h4>Bạn chưa đăng nhập</h4>
        <p>Vui lòng đăng nhập để sử dụng tính năng nhóm du lịch.</p>
        <router-link to="/client-login" class="btn-brand-lg">Đăng nhập ngay</router-link>
      </div>

      <template v-else>
        <!-- ── TABS ── -->
        <div class="ndl-tabs">
          <button class="ndl-tab" :class="{ active: activeTab === 'joined' }" @click="activeTab = 'joined'">
            <i class="bi bi-person-check me-2"></i>Đang tham gia
            <span class="tab-count">{{ joinedGroups.length }}</span>
          </button>
          <button class="ndl-tab" :class="{ active: activeTab === 'mine' }" @click="activeTab = 'mine'">
            <i class="bi bi-person-workspace me-2"></i>Nhóm tôi tạo
            <span class="tab-count">{{ myGroups.length }}</span>
          </button>
        </div>

        <!-- ── LOADING ── -->
        <div v-if="loading" class="ndl-loading">
          <div class="spinner"></div><span>Đang tải...</span>
        </div>

        <!-- ── JOINED TAB ── -->
        <div v-else-if="activeTab === 'joined'">
          <div v-if="joinedGroups.length === 0" class="ndl-empty">
            <i class="bi bi-people"></i>
            <h4>Bạn chưa tham gia nhóm nào</h4>
            <p>Hãy tạo nhóm mới hoặc chờ được mời tham gia.</p>
          </div>
          <div v-else class="groups-grid">
            <div v-for="g in joinedGroups" :key="g.id" class="group-card">
              <div class="gc-top">
                <div class="gc-avatar" :style="{ background: groupColor(g.id) }">
                  {{ g.ten_nhom.charAt(0).toUpperCase() }}
                </div>
                <div class="gc-role-badge" :class="g.la_truong_nhom ? 'leader' : 'member'">
                  {{ g.la_truong_nhom ? 'Trưởng nhóm' : 'Thành viên' }}
                </div>
              </div>
              <h4 class="gc-title">{{ g.ten_nhom }}</h4>
              <p class="gc-desc">{{ g.mo_ta || 'Chưa có mô tả.' }}</p>
              <div class="gc-stats">
                <span><i class="bi bi-people me-1"></i>{{ g.so_thanh_vien }} thành viên</span>
                <span v-if="g.id_chuyen_di"><i class="bi bi-map me-1"></i>Có chuyến đi</span>
              </div>
              <div class="gc-actions">
                <button class="btn-sm-outline" @click="openMembersPanel(g)">
                  <i class="bi bi-person-lines-fill me-1"></i>Thành viên
                </button>
                <button
                  v-if="!g.la_truong_nhom"
                  class="btn-sm-danger"
                  @click="leaveGroup(g)"
                >
                  <i class="bi bi-box-arrow-right me-1"></i>Rời nhóm
                </button>
                <button v-else class="btn-sm-outline" @click="openInviteModal(g)">
                  <i class="bi bi-person-plus me-1"></i>Mời
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ── MY GROUPS TAB ── -->
        <div v-else>
          <div v-if="myGroups.length === 0" class="ndl-empty">
            <i class="bi bi-diagram-3"></i>
            <h4>Bạn chưa tạo nhóm nào</h4>
            <p>Nhấn "Tạo nhóm mới" để bắt đầu!</p>
          </div>
          <div v-else class="groups-grid">
            <div v-for="g in myGroups" :key="g.id" class="group-card leader-card">
              <div class="gc-top">
                <div class="gc-avatar" :style="{ background: groupColor(g.id) }">
                  {{ g.ten_nhom.charAt(0).toUpperCase() }}
                </div>
                <div class="gc-leader-icon"><i class="bi bi-star-fill"></i></div>
              </div>
              <h4 class="gc-title">{{ g.ten_nhom }}</h4>
              <p class="gc-desc">{{ g.mo_ta || 'Chưa có mô tả.' }}</p>
              <div class="gc-stats">
                <span><i class="bi bi-people me-1"></i>{{ g.so_thanh_vien }} thành viên</span>
                <span v-if="g.id_chuyen_di"><i class="bi bi-map me-1"></i>Có chuyến đi</span>
              </div>
              <div class="gc-actions">
                <button class="btn-sm-outline" @click="openMembersPanel(g)">
                  <i class="bi bi-person-lines-fill me-1"></i>Thành viên
                </button>
                <button class="btn-sm-outline" @click="openInviteModal(g)">
                  <i class="bi bi-person-plus me-1"></i>Mời
                </button>
                <button class="btn-sm-danger" @click="confirmDissolve(g)">
                  <i class="bi bi-trash3 me-1"></i>Giải tán
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- ══════════════════════════════════════════════
         MODAL – Tạo nhóm
    ══════════════════════════════════════════════ -->
    <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
      <div class="modal-box">
        <button class="modal-close" @click="showCreate = false"><i class="bi bi-x-lg"></i></button>
        <div class="modal-icon-head"><i class="bi bi-people-fill"></i></div>
        <h3>Tạo nhóm mới</h3>

        <div class="mform-group">
          <label>Tên nhóm <span class="req">*</span></label>
          <input v-model="createForm.ten_nhom" class="mform-input" placeholder="Ví dụ: Đà Nẵng cùng lớp K45" />
          <span v-if="createErr.ten_nhom" class="err-msg">{{ createErr.ten_nhom }}</span>
        </div>
        <div class="mform-group">
          <label>Mô tả nhóm</label>
          <textarea v-model="createForm.mo_ta" class="mform-input" rows="3" placeholder="Chuyến đi dự kiến, mục tiêu nhóm..."></textarea>
        </div>
        <div class="mform-group">
          <label>Gắn với chuyến đi <span class="optional">(tuỳ chọn)</span></label>
          <select v-model="createForm.id_chuyen_di" class="mform-input">
            <option :value="null">— Không gắn chuyến đi —</option>
            <option v-for="cd in myTrips" :key="cd.id" :value="cd.id">{{ cd.ten_chuyen_di }}</option>
          </select>
        </div>

        <div v-if="createMsg" class="save-alert" :class="createMsgType">{{ createMsg }}</div>

        <button class="btn-brand-lg w100" @click="doCreate" :disabled="creating">
          <span v-if="creating"><i class="bi bi-hourglass-split me-2"></i>Đang tạo...</span>
          <span v-else><i class="bi bi-check2-circle me-2"></i>Tạo nhóm</span>
        </button>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════
         MODAL – Lời mời chờ xác nhận
    ══════════════════════════════════════════════ -->
    <div v-if="showInvites" class="modal-overlay" @click.self="showInvites = false">
      <div class="modal-box">
        <button class="modal-close" @click="showInvites = false"><i class="bi bi-x-lg"></i></button>
        <div class="modal-icon-head invite-icon"><i class="bi bi-envelope-open-heart-fill"></i></div>
        <h3>Lời mời tham gia nhóm</h3>

        <div v-if="pendingInvites.length === 0" class="ndl-empty small-empty">
          <i class="bi bi-inbox"></i>
          <p>Không có lời mời nào đang chờ.</p>
        </div>

        <div v-else class="invite-list">
          <div v-for="inv in pendingInvites" :key="inv.id_thanh_vien" class="invite-item">
            <div class="invite-info">
              <strong>{{ inv.ten_nhom }}</strong>
              <span>Được mời bởi: {{ inv.nguoi_tao }}</span>
              <span v-if="inv.mo_ta" class="invite-desc">{{ inv.mo_ta }}</span>
            </div>
            <div class="invite-btns">
              <button class="btn-accept" @click="respond(inv, true)" :disabled="inv.responding">
                <i class="bi bi-check-lg"></i>
              </button>
              <button class="btn-reject" @click="respond(inv, false)" :disabled="inv.responding">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════
         MODAL – Mời thành viên
    ══════════════════════════════════════════════ -->
    <div v-if="showInvite" class="modal-overlay" @click.self="showInvite = false">
      <div class="modal-box">
        <button class="modal-close" @click="showInvite = false"><i class="bi bi-x-lg"></i></button>
        <div class="modal-icon-head"><i class="bi bi-person-plus-fill"></i></div>
        <h3>Mời thành viên vào "{{ inviteTarget?.ten_nhom }}"</h3>
        <div class="mform-group">
          <label>Email người dùng</label>
          <div class="invite-input-row">
            <input
              v-model="inviteEmail"
              class="mform-input"
              type="email"
              placeholder="email@example.com"
              @keyup.enter="doInvite"
            />
            <button class="btn-brand-lg" @click="doInvite" :disabled="inviting">
              <span v-if="inviting"><i class="bi bi-hourglass-split"></i></span>
              <span v-else>Gửi</span>
            </button>
          </div>
        </div>
        <div v-if="inviteMsg" class="save-alert" :class="inviteMsgType">{{ inviteMsg }}</div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════
         PANEL – Danh sách thành viên (slide-in)
    ══════════════════════════════════════════════ -->
    <div v-if="showMembers" class="panel-overlay" @click.self="showMembers = false">
      <div class="panel-drawer">
        <div class="panel-header">
          <div>
            <h3>Thành viên nhóm</h3>
            <p>{{ panelGroup?.ten_nhom }}</p>
          </div>
          <button class="modal-close" @click="showMembers = false"><i class="bi bi-x-lg"></i></button>
        </div>

        <div v-if="loadingMembers" class="ndl-loading"><div class="spinner"></div></div>

        <div v-else class="member-list">
          <div v-for="m in members" :key="m.id_thanh_vien" class="member-item">
            <div class="member-avatar" :style="{ background: groupColor(m.id_nguoi_dung) }">
              {{ (m.ten || '?').charAt(0).toUpperCase() }}
            </div>
            <div class="member-info">
              <strong>{{ m.ten }}</strong>
              <span>{{ m.email }}</span>
            </div>
            <div class="member-meta">
              <span class="role-chip" :class="m.vai_tro">
                {{ m.vai_tro === 'truong_nhom' ? '★ Trưởng nhóm' : 'Thành viên' }}
              </span>
              <span class="status-chip" :class="m.trang_thai == 1 ? 'ok' : m.trang_thai == 0 ? 'pending' : 'rejected'">
                {{ m.trang_thai == 1 ? 'Đã tham gia' : m.trang_thai == 0 ? 'Chờ xác nhận' : 'Đã từ chối' }}
              </span>
            </div>
            <!-- Kick button (only leader, not self, not other leader) -->
            <button
              v-if="panelGroup?.la_truong_nhom && m.vai_tro !== 'truong_nhom'"
              class="btn-kick"
              @click="kickMember(m)"
              title="Xóa khỏi nhóm"
            ><i class="bi bi-person-dash-fill"></i></button>
          </div>
          <div v-if="members.length === 0" class="ndl-empty small-empty">
            <i class="bi bi-people"></i><p>Chưa có thành viên nào.</p>
          </div>
        </div>

        <!-- Invite from panel if leader -->
        <div v-if="panelGroup?.la_truong_nhom" class="panel-invite">
          <label>Mời thêm qua email</label>
          <div class="invite-input-row">
            <input
              v-model="panelInviteEmail"
              class="mform-input"
              type="email"
              placeholder="email@example.com"
              @keyup.enter="doInviteFromPanel"
            />
            <button class="btn-brand-lg" @click="doInviteFromPanel" :disabled="panelInviting">
              <span v-if="panelInviting"><i class="bi bi-hourglass-split"></i></span>
              <span v-else><i class="bi bi-send"></i></span>
            </button>
          </div>
          <div v-if="panelMsg" class="save-alert mt-1" :class="panelMsgType">{{ panelMsg }}</div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════
         MODAL – Xác nhận giải tán
    ══════════════════════════════════════════════ -->
    <div v-if="dissolveTarget" class="modal-overlay" @click.self="dissolveTarget = null">
      <div class="modal-box">
        <div class="modal-icon warning-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <h4>Giải tán nhóm?</h4>
        <p>Bạn có chắc muốn giải tán nhóm <strong>"{{ dissolveTarget.ten_nhom }}"</strong>? Hành động này không thể hoàn tác.</p>
        <div class="modal-actions">
          <button class="btn-ghost" @click="dissolveTarget = null">Hủy</button>
          <button class="btn-danger" @click="doDissolve" :disabled="dissolving">
            <span v-if="dissolving">Đang giải tán...</span>
            <span v-else><i class="bi bi-trash3 me-1"></i>Giải tán</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
const BASE = 'http://localhost:8000/api/client/nhom-du-lich';
const BASE_CD = 'http://localhost:8000/api/client/chuyen-di';

export default {
  name: 'NhomDuLich',

  data() {
    return {
      token: localStorage.getItem('client_token'),
      activeTab: 'joined',
      loading: false,

      // Data
      joinedGroups: [],
      myGroups: [],
      myTrips: [],
      pendingInvites: [],
      members: [],

      // Modals
      showCreate: false,
      showInvites: false,
      showInvite: false,
      showMembers: false,

      // Create form
      createForm: { ten_nhom: '', mo_ta: '', id_chuyen_di: null },
      createErr: {},
      createMsg: '',
      createMsgType: 'success',
      creating: false,

      // Invite modal
      inviteTarget: null,
      inviteEmail: '',
      inviteMsg: '',
      inviteMsgType: 'success',
      inviting: false,

      // Members panel
      panelGroup: null,
      loadingMembers: false,
      panelInviteEmail: '',
      panelMsg: '',
      panelMsgType: 'success',
      panelInviting: false,

      // Dissolve
      dissolveTarget: null,
      dissolving: false,
    };
  },

  mounted() {
    if (this.token) {
      this.fetchAll();
      this.fetchMyTrips();
    }
  },

  methods: {
    // ─── Helpers ────────────────────────────────────────
    h() { return { Authorization: `Bearer ${this.token}` }; },
    hJson() { return { 'Content-Type': 'application/json', Authorization: `Bearer ${this.token}` }; },

    groupColor(id) {
      const colors = [
        'linear-gradient(135deg,#10b981,#0ea5e9)',
        'linear-gradient(135deg,#8b5cf6,#ec4899)',
        'linear-gradient(135deg,#f59e0b,#ef4444)',
        'linear-gradient(135deg,#06b6d4,#3b82f6)',
        'linear-gradient(135deg,#84cc16,#10b981)',
        'linear-gradient(135deg,#f97316,#eab308)',
      ];
      return colors[id % colors.length];
    },

    // ─── Fetch ───────────────────────────────────────────
    async fetchAll() {
      this.loading = true;
      await Promise.all([this.fetchJoined(), this.fetchMine(), this.fetchInvites()]);
      this.loading = false;
    },

    async fetchJoined() {
      try {
        const r = await fetch(`${BASE}/get-joined`, { headers: this.h() });
        this.joinedGroups = (await r.json()).data || [];
      } catch { this.joinedGroups = []; }
    },

    async fetchMine() {
      try {
        const r = await fetch(`${BASE}/get-my-groups`, { headers: this.h() });
        this.myGroups = (await r.json()).data || [];
      } catch { this.myGroups = []; }
    },

    async fetchInvites() {
      try {
        const r = await fetch(`${BASE}/get-invites`, { headers: this.h() });
        this.pendingInvites = ((await r.json()).data || []).map(i => ({ ...i, responding: false }));
      } catch { this.pendingInvites = []; }
    },

    async fetchMyTrips() {
      try {
        const r = await fetch(`${BASE_CD}/get-data`, { headers: this.h() });
        this.myTrips = (await r.json()).data || [];
      } catch { this.myTrips = []; }
    },

    async fetchMembers(id) {
      this.loadingMembers = true;
      try {
        const r = await fetch(`${BASE}/members/${id}`, { headers: this.h() });
        this.members = (await r.json()).data || [];
      } catch { this.members = []; }
      this.loadingMembers = false;
    },

    // ─── Create group ────────────────────────────────────
    openCreateModal() {
      this.createForm = { ten_nhom: '', mo_ta: '', id_chuyen_di: null };
      this.createErr = {};
      this.createMsg = '';
      this.showCreate = true;
    },

    async doCreate() {
      this.createErr = {};
      if (!this.createForm.ten_nhom.trim()) {
        this.createErr.ten_nhom = 'Vui lòng nhập tên nhóm.';
        return;
      }
      this.creating = true;
      try {
        const r = await fetch(`${BASE}/create`, {
          method: 'POST', headers: this.hJson(),
          body: JSON.stringify(this.createForm),
        });
        const j = await r.json();
        this.createMsg = j.message;
        this.createMsgType = j.status ? 'success' : 'error';
        if (j.status) {
          setTimeout(() => { this.showCreate = false; }, 1200);
          await this.fetchAll();
          this.activeTab = 'mine';
        }
      } catch { this.createMsg = 'Có lỗi xảy ra.'; this.createMsgType = 'error'; }
      this.creating = false;
    },

    // ─── Invite ──────────────────────────────────────────
    openInviteModal(g) {
      this.inviteTarget = g;
      this.inviteEmail = '';
      this.inviteMsg = '';
      this.showInvite = true;
    },

    async doInvite() {
      if (!this.inviteEmail) return;
      this.inviting = true;
      const r = await fetch(`${BASE}/invite`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id_nhom: this.inviteTarget.id, email: this.inviteEmail }),
      });
      const j = await r.json();
      this.inviteMsg = j.message;
      this.inviteMsgType = j.status ? 'success' : 'error';
      if (j.status) this.inviteEmail = '';
      this.inviting = false;
    },

    // ─── Accept / Reject invite ──────────────────────────
    async respond(inv, accept) {
      inv.responding = true;
      const r = await fetch(`${BASE}/accept-invite`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id_thanh_vien: inv.id_thanh_vien, chap_nhan: accept }),
      });
      const j = await r.json();
      if (j.status) {
        this.pendingInvites = this.pendingInvites.filter(i => i.id_thanh_vien !== inv.id_thanh_vien);
        await this.fetchJoined();
      }
      inv.responding = false;
    },

    // ─── Members panel ───────────────────────────────────
    async openMembersPanel(g) {
      this.panelGroup = g;
      this.panelInviteEmail = '';
      this.panelMsg = '';
      this.showMembers = true;
      await this.fetchMembers(g.id);
    },

    async kickMember(m) {
      if (!confirm(`Xóa "${m.ten}" khỏi nhóm?`)) return;
      const r = await fetch(`${BASE}/remove-member`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id_nhom: this.panelGroup.id, id_nguoi_dung: m.id_nguoi_dung }),
      });
      const j = await r.json();
      if (j.status) {
        await this.fetchMembers(this.panelGroup.id);
        await this.fetchAll();
      }
    },

    async doInviteFromPanel() {
      if (!this.panelInviteEmail) return;
      this.panelInviting = true;
      const r = await fetch(`${BASE}/invite`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id_nhom: this.panelGroup.id, email: this.panelInviteEmail }),
      });
      const j = await r.json();
      this.panelMsg = j.message;
      this.panelMsgType = j.status ? 'success' : 'error';
      if (j.status) { this.panelInviteEmail = ''; await this.fetchMembers(this.panelGroup.id); }
      this.panelInviting = false;
    },

    // ─── Leave ───────────────────────────────────────────
    async leaveGroup(g) {
      if (!confirm(`Rời nhóm "${g.ten_nhom}"?`)) return;
      const r = await fetch(`${BASE}/leave`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id_nhom: g.id }),
      });
      const j = await r.json();
      if (j.status) await this.fetchJoined();
    },

    // ─── Dissolve ────────────────────────────────────────
    confirmDissolve(g) { this.dissolveTarget = g; },

    async doDissolve() {
      this.dissolving = true;
      const r = await fetch(`${BASE}/delete`, {
        method: 'POST', headers: this.hJson(),
        body: JSON.stringify({ id: this.dissolveTarget.id }),
      });
      const j = await r.json();
      if (j.status) {
        this.dissolveTarget = null;
        await this.fetchAll();
      }
      this.dissolving = false;
    },
  },
};
</script>

<style scoped>
/* ─────────────── Base ─────────────── */
.ndl-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0f7ff 0%, #fafbff 60%, #f0fff8 100%);
  padding-bottom: 5rem;
  font-family: 'Inter', sans-serif;
  color: #1e2d44;
}

.ndl-container {
  max-width: 1040px;
  padding-top: 3rem;
}

/* ─────────────── Header ─────────────── */
.ndl-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}
.ndl-header h1 { font-size: 2rem; font-weight: 800; color: #1e2d44; margin-bottom: 0.2rem; }
.ndl-header p { color: #627289; font-size: 0.95rem; margin: 0; }
.text-brand { color: #10b981; }

.header-actions { display: flex; align-items: center; gap: 0.75rem; }

/* Bell */
.bell-btn {
  position: relative;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: 1.5px solid #dbe3f0;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  color: #5a6d80;
  cursor: pointer;
  transition: all 0.2s;
}
.bell-btn:hover { border-color: #10b981; color: #10b981; }
.bell-btn.pulse { border-color: #10b981; color: #10b981; animation: bellPulse 1.8s ease-in-out infinite; }
@keyframes bellPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.3); }
  50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
}
.bell-badge {
  position: absolute;
  top: -4px; right: -4px;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #f43f5e;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid #fff;
}

/* ─────────────── Tabs ─────────────── */
.ndl-tabs { display: flex; gap: 0.6rem; margin-bottom: 2rem; }
.ndl-tab {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  border: 1.5px solid #dbe3f0;
  background: #fff;
  font-size: 0.9rem;
  font-weight: 600;
  color: #5a6d80;
  cursor: pointer;
  transition: all 0.2s;
}
.ndl-tab.active { background: linear-gradient(135deg,#10b981,#0ea5e9); border-color: transparent; color: #fff; box-shadow: 0 6px 16px rgba(16,185,129,0.25); }
.tab-count {
  background: rgba(255,255,255,0.25);
  border-radius: 999px;
  padding: 0.08rem 0.45rem;
  font-size: 0.78rem;
}
.ndl-tab:not(.active) .tab-count { background: #f0f4fb; color: #7a8ea0; }

/* ─────────────── Groups grid ─────────────── */
.groups-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 1.3rem; }

.group-card {
  background: #fff;
  border-radius: 1.3rem;
  padding: 1.4rem 1.4rem 1.2rem;
  border: 1px solid #e6ecf5;
  box-shadow: 0 6px 22px rgba(30,45,68,0.07);
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  transition: all 0.22s;
}
.group-card:hover { transform: translateY(-3px); box-shadow: 0 14px 35px rgba(30,45,68,0.12); }
.leader-card { border-color: rgba(16,185,129,0.25); }

.gc-top { display: flex; justify-content: space-between; align-items: center; }
.gc-avatar {
  width: 52px; height: 52px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem;
  font-weight: 800;
  color: #fff;
}
.gc-role-badge {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.22rem 0.7rem;
  border-radius: 999px;
}
.gc-role-badge.leader { background: #dcfce7; color: #15803d; }
.gc-role-badge.member { background: #e0f2fe; color: #0369a1; }

.gc-leader-icon {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg,#fbbf24,#f97316);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-size: 0.8rem;
}

.gc-title { font-size: 1.05rem; font-weight: 800; color: #1e2d44; margin: 0; }
.gc-desc { font-size: 0.86rem; color: #627289; margin: 0; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.gc-stats { display: flex; gap: 0.8rem; font-size: 0.82rem; color: #7a8ea0; flex-wrap: wrap; }
.gc-stats i { color: #10b981; }

.gc-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: auto; padding-top: 0.4rem; border-top: 1px solid #f0f4fb; }

.btn-sm-outline {
  padding: 0.38rem 0.85rem;
  border-radius: 999px;
  border: 1.5px solid #d5dde8;
  background: #fff;
  font-size: 0.8rem;
  font-weight: 600;
  color: #3d5166;
  cursor: pointer;
  transition: all 0.18s;
}
.btn-sm-outline:hover { border-color: #10b981; color: #10b981; background: #f0fff8; }

.btn-sm-danger {
  padding: 0.38rem 0.85rem;
  border-radius: 999px;
  border: 1.5px solid #fecdd3;
  background: #fff1f2;
  font-size: 0.8rem;
  font-weight: 600;
  color: #e11d48;
  cursor: pointer;
  transition: all 0.18s;
}
.btn-sm-danger:hover { background: #e11d48; color: #fff; border-color: #e11d48; }

/* ─────────────── Loading / Empty ─────────────── */
.ndl-loading { display: flex; align-items: center; gap: 1rem; padding: 3rem; color: #7a8ea0; font-size: 0.92rem; }
.spinner { width: 32px; height: 32px; border: 3px solid #e8edf5; border-top-color: #10b981; border-radius: 50%; animation: spin 0.7s linear infinite; flex-shrink: 0; }
@keyframes spin { to { transform: rotate(360deg); } }

.ndl-empty { display: flex; flex-direction: column; align-items: center; gap: 0.7rem; padding: 4rem 1rem; text-align: center; color: #7a8ea0; }
.ndl-empty i { font-size: 3rem; color: #c0cedf; }
.ndl-empty h4 { font-size: 1.2rem; font-weight: 700; color: #3d5166; margin: 0; }
.ndl-empty p { font-size: 0.92rem; margin: 0; }
.small-empty { padding: 1.5rem; }
.small-empty i { font-size: 1.8rem; }

/* ─────────────── Buttons ─────────────── */
.btn-brand-lg {
  background: linear-gradient(135deg,#10b981,#0ea5e9);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.72rem 1.6rem;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  box-shadow: 0 8px 20px rgba(16,185,129,0.25);
  transition: all 0.2s;
}
.btn-brand-lg:hover:not(:disabled) { transform: translateY(-2px); color: #fff; }
.btn-brand-lg:disabled { opacity: 0.55; cursor: not-allowed; }
.w100 { width: 100%; justify-content: center; }

.btn-ghost { background: transparent; color: #5a6d80; border: 1.5px solid #d5dde8; border-radius: 999px; padding: 0.62rem 1.4rem; font-size: 0.92rem; font-weight: 600; cursor: pointer; transition: all 0.18s; }
.btn-ghost:hover { background: #f0f4fb; }
.btn-danger { background: linear-gradient(135deg,#e11d48,#f97316); color: #fff; border: none; border-radius: 999px; padding: 0.62rem 1.4rem; font-size: 0.92rem; font-weight: 700; cursor: pointer; box-shadow: 0 6px 16px rgba(225,29,72,0.28); transition: all 0.18s; }
.btn-danger:hover:not(:disabled) { transform: translateY(-1px); }
.btn-danger:disabled { opacity: 0.55; cursor: not-allowed; }

/* ─────────────── Modal ─────────────── */
.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.55); display: flex; align-items: center; justify-content: center; z-index: 500; backdrop-filter: blur(4px); }
.modal-box {
  background: #fff;
  border-radius: 1.5rem;
  padding: 2rem;
  max-width: 440px;
  width: 92%;
  position: relative;
  animation: popIn 0.28s ease both;
  box-shadow: 0 30px 60px rgba(15,23,42,0.18);
}
@keyframes popIn { from { transform: scale(0.88); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.modal-close { position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #e2e8f0; background: #f8fafc; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #5a6d80; font-size: 0.9rem; transition: all 0.18s; }
.modal-close:hover { background: #f43f5e; border-color: #f43f5e; color: #fff; }
.modal-box h3 { font-size: 1.2rem; font-weight: 800; color: #1e2d44; margin-bottom: 1.2rem; }
.modal-box h4 { font-size: 1.1rem; font-weight: 800; color: #1e2d44; margin-bottom: 0.5rem; }
.modal-box p { font-size: 0.92rem; color: #5a6d80; margin-bottom: 1.2rem; }
.modal-actions { display: flex; gap: 0.75rem; justify-content: center; }

.modal-icon-head {
  width: 52px; height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg,#dcfce7,#dbeafe);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.35rem;
  color: #10b981;
  margin-bottom: 0.75rem;
}
.invite-icon { background: linear-gradient(135deg,#fef9c3,#fce7f3); color: #f59e0b; }
.modal-icon.warning-icon { width: 52px; height: 52px; border-radius: 50%; background: #fff1f2; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #e11d48; margin: 0 auto 1rem; }

/* ─────────────── Form ─────────────── */
.mform-group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
.mform-group label { font-size: 0.86rem; font-weight: 600; color: #3d5166; }
.optional { font-weight: 400; color: #a0adbf; }
.req { color: #f43f5e; }
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
  resize: vertical;
}
.mform-input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.12); }
.err-msg { font-size: 0.8rem; color: #f43f5e; }

.invite-input-row { display: flex; gap: 0.5rem; }
.invite-input-row .mform-input { flex: 1; }

/* ─────────────── Save alert ─────────────── */
.save-alert { display: flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1rem; border-radius: 0.7rem; font-size: 0.88rem; font-weight: 600; margin-bottom: 0.8rem; }
.save-alert.success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.save-alert.error { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.mt-1 { margin-top: 0.5rem; }

/* ─────────────── Invite list ─────────────── */
.invite-list { display: flex; flex-direction: column; gap: 0.75rem; max-height: 340px; overflow-y: auto; margin-bottom: 0.5rem; }
.invite-item { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; background: #f8fbff; border-radius: 0.9rem; padding: 0.9rem 1rem; border: 1px solid #e6ecf5; }
.invite-info { display: flex; flex-direction: column; gap: 0.15rem; }
.invite-info strong { font-size: 0.94rem; color: #1e2d44; }
.invite-info span { font-size: 0.8rem; color: #7a8ea0; }
.invite-desc { font-style: italic; }
.invite-btns { display: flex; gap: 0.4rem; flex-shrink: 0; }
.btn-accept { width: 34px; height: 34px; border-radius: 50%; border: none; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: all 0.18s; }
.btn-accept:hover:not(:disabled) { background: #16a34a; color: #fff; }
.btn-reject { width: 34px; height: 34px; border-radius: 50%; border: none; background: #fff1f2; color: #e11d48; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: all 0.18s; }
.btn-reject:hover:not(:disabled) { background: #e11d48; color: #fff; }

/* ─────────────── Slide-in panel ─────────────── */
.panel-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.45); z-index: 400; backdrop-filter: blur(3px); }
.panel-drawer {
  position: fixed;
  top: 0; right: 0;
  width: min(440px, 95vw);
  height: 100vh;
  background: #fff;
  box-shadow: -20px 0 60px rgba(15,23,42,0.15);
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease both;
  overflow: hidden;
}
@keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

.panel-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 1.5rem; border-bottom: 1px solid #e8edf5; }
.panel-header h3 { font-size: 1.1rem; font-weight: 800; color: #1e2d44; margin: 0 0 0.2rem; }
.panel-header p { font-size: 0.85rem; color: #627289; margin: 0; }

.member-list { flex: 1; overflow-y: auto; padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: 0.7rem; }
.member-item { display: flex; align-items: center; gap: 0.85rem; background: #f8fbff; border-radius: 0.9rem; padding: 0.75rem 0.9rem; border: 1px solid #e6ecf5; }
.member-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; color: #fff; flex-shrink: 0; }
.member-info { display: flex; flex-direction: column; gap: 0.1rem; flex: 1; min-width: 0; }
.member-info strong { font-size: 0.9rem; color: #1e2d44; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-info span { font-size: 0.78rem; color: #7a8ea0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-meta { display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-end; flex-shrink: 0; }

.role-chip { font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 999px; }
.role-chip.truong_nhom { background: #fef9c3; color: #92400e; }
.role-chip.thanh_vien { background: #e0f2fe; color: #0369a1; }

.status-chip { font-size: 0.68rem; font-weight: 600; padding: 0.12rem 0.45rem; border-radius: 999px; }
.status-chip.ok { background: #dcfce7; color: #15803d; }
.status-chip.pending { background: #fef3c7; color: #b45309; }
.status-chip.rejected { background: #ffe4e6; color: #be123c; }

.btn-kick { width: 30px; height: 30px; border-radius: 50%; border: 1.5px solid #fecdd3; background: #fff1f2; color: #e11d48; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; transition: all 0.18s; flex-shrink: 0; }
.btn-kick:hover { background: #e11d48; color: #fff; border-color: #e11d48; }

.panel-invite { padding: 1rem 1.5rem 1.5rem; border-top: 1px solid #e8edf5; }
.panel-invite label { font-size: 0.85rem; font-weight: 600; color: #3d5166; display: block; margin-bottom: 0.5rem; }

@media (max-width: 640px) {
  .groups-grid { grid-template-columns: 1fr; }
  .ndl-tabs { flex-wrap: wrap; }
}
</style>
