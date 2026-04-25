import os
import re

files = [
    'CheckIn.vue',
    'GiaiTri.vue',
    'AmThuc.vue',
    'TamLinh.vue'
]

base_dir = '/Applications/XAMPP/xamppfiles/htdocs/KLTN_2/FE_AI_TRAVEL/src/pages/Client/DiaDiem/'

btn_html = """              <button
                class="btn w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center mt-3 text-white"
                style="background: linear-gradient(135deg, #10b981 0%, #0ea5e9 100%); border: none;"
                @click="openShareModal">
                <i class="bi bi-send-fill me-2 fs-5"></i> Gửi địa điểm vào nhóm
              </button>
"""

modal_html = """
    <!-- Modal chọn nhóm chia sẻ -->
    <div v-if="showShareModal" class="share-modal-overlay d-flex align-items-center justify-content-center" @click.self="showShareModal = false" style="position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); z-index: 9999;">
      <div class="share-modal-box bg-white p-5 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 440px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="showShareModal = false"></button>
        <div class="text-center mb-4 mt-2">
          <div class="share-icon-wrap mb-3 mx-auto shadow-sm">
             <i class="bi bi-send-check-fill fs-2 text-primary"></i>
          </div>
          <h4 class="fw-bold mb-1 text-dark">Chia sẻ địa điểm</h4>
          <p class="text-muted mb-0" style="font-size: 0.95rem;">Chọn nhóm để gửi địa điểm này nhé!</p>
        </div>
        
        <div class="form-group mb-4">
          <label class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">Bạn muốn gửi vào nhóm nào?</label>
          <select v-model="selectedGroupToShare" class="form-select border-2 shadow-none modal-select text-dark" style="padding: 0.9rem 1rem; border-radius: 12px; font-weight: 500; cursor: pointer; border-color: #e2e8f0; background-color: #f8fafc;">
            <option :value="null">-- Click để chọn nhóm --</option>
            <option v-for="g in myJoinedGroups" :key="g.id" :value="g">{{ g.ten_nhom }}</option>
          </select>
        </div>

        <button class="btn w-100 rounded-pill fw-bold text-white shadow-sm mt-3" @click="shareToGroup" :disabled="!selectedGroupToShare || sendingShare" style="background: #10b981; padding: 0.9rem; font-size: 1.05rem; transition: all 0.2s;">
          <span v-if="sendingShare" class="d-flex align-items-center justify-content-center"><span class="spinner-border spinner-border-sm me-2"></span> Đang gửi...</span>
          <span v-else class="d-flex align-items-center justify-content-center"><i class="bi bi-chat-heart-fill me-2 fs-5"></i> Chia sẻ ngay</span>
        </button>
      </div>
    </div>
"""

data_add = """
      showShareModal: false,
      myJoinedGroups: [],
      selectedGroupToShare: null,
      sendingShare: false,
"""

methods_add = """
    openShareModal() {
      if (!this.isLoggedIn) {
        this.$toast.warning('Vui lòng đăng nhập để chia sẻ!');
        return;
      }
      this.showShareModal = true;
      if (this.myJoinedGroups.length === 0) {
        this.fetchMyGroups();
      }
    },
    async fetchMyGroups() {
      try {
        const [joinedRes, ownedRes] = await Promise.all([
          fetch(`${BASE}/client/nhom-du-lich/get-joined`, { headers: { Authorization: `Bearer ${this.token}` } }),
          fetch(`${BASE}/client/nhom-du-lich/get-my-groups`, { headers: { Authorization: `Bearer ${this.token}` } })
        ]);
        const jData = await joinedRes.json();
        const oData = await ownedRes.json();
        
        const groups = [];
        if (jData.status && jData.data) groups.push(...jData.data);
        if (oData.status && oData.data) groups.push(...oData.data);
        
        this.myJoinedGroups = groups;
      } catch (e) {
        console.error("Lỗi lấy danh sách nhóm", e);
      }
    },
    async shareToGroup() {
      if (!this.selectedGroupToShare) return;
      this.sendingShare = true;
      try {
        const payload = {
            id_nhom_du_lich: this.selectedGroupToShare.id,
            id_chi_tiet_nhom: this.selectedGroupToShare.id_chi_tiet_nhom,
            message: JSON.stringify({ type: 'place', id: this.selectedPlace.id, title: this.selectedPlace.ten_dia_diem, image: this.selectedPlace.image })
        };

        const r = await fetch(`${BASE}/nhom-chats`, {
          method: 'POST', 
          headers: { 
            'Content-Type': 'application/json', 
            Authorization: `Bearer ${this.token}` 
          },
          body: JSON.stringify(payload)
        });
        const res = await r.json();
        
        if (res.status) {
          this.$toast.success('Gửi địa điểm thành công!');
          this.showShareModal = false;
        } else {
          this.$toast.error('Gửi thất bại: ' + res.message);
        }
      } catch (e) {
        this.$toast.error('Lỗi khi chia sẻ.');
        console.error(e);
      } finally {
        this.sendingShare = false;
      }
    },
"""

for f in files:
    path = os.path.join(base_dir, f)
    with open(path, 'r') as file:
        content = file.read()
    
    # Add button
    if "Gửi địa điểm vào nhóm" not in content:
        content = content.replace(
            "Thêm vào lịch trình\n              </button>",
            "Thêm vào lịch trình\n              </button>\n" + btn_html
        )
    
    # Add modal
    if "Modal chọn nhóm chia sẻ" not in content:
        content = content.replace("  </div>\n</template>", modal_html + "  </div>\n</template>")

    # Add data
    if "showShareModal: false" not in content:
        content = content.replace("submittingReview: false,", "submittingReview: false,\n" + data_add)

    # Add methods
    if "openShareModal()" not in content:
        content = content.replace("methods: {", "methods: {\n" + methods_add)

    with open(path, 'w') as file:
        file.write(content)

print("Patched all 4 files!")
