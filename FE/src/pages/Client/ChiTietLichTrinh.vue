
<template>
  <div class="tlt-page">
    <div class="container tlt-body mt-4">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-brand" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="mt-3 text-muted fs-5">Đang tải lịch trình...</div>
      </div>
      <div v-else-if="!trip" class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
        <i class="bi bi-calendar-x text-danger display-1 mb-3"></i>
        <h3 class="text-danger fw-bold">Không tìm thấy chuyến đi</h3>
        <p class="text-muted mb-4">Chuyến đi này không tồn tại hoặc đã bị xóa.</p>
        <router-link to="/lich-trinh-cua-toi" class="btn-brand-lg mt-3 text-decoration-none d-inline-block px-4 py-2 text-white" style="border-radius: 999px"><i class="bi bi-arrow-left me-2"></i>Quay lại danh sách</router-link>
      </div>
      <div v-else class="tlt-section animate-in bg-white rounded-4 p-4 shadow-sm" style="border-radius: 1.5rem;">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 flex-wrap gap-3">
          <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-map me-2 text-brand"></i>{{ trip.ten_chuyen_di }}</h2>
            <div class="text-muted mt-2 d-flex gap-3 flex-wrap">
              <span class="badge border bg-light text-dark py-2 px-3 fw-normal" style="border-radius: 8px;"><i class="bi bi-calendar3 me-2 text-primary"></i>{{ formatDateFull(trip.ngay_bat_dau) }}</span>
              <span class="badge border bg-light text-dark py-2 px-3 fw-normal" style="border-radius: 8px;"><i class="bi bi-people me-2 text-info"></i>{{ trip.so_nguoi || 1 }} thành viên</span>
              <span class="badge border bg-light text-dark py-2 px-3 fw-normal" style="border-radius: 8px;"><i class="bi bi-sun me-2 text-warning"></i>{{ formatDuration(trip.so_ngay) }}</span>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold d-flex align-items-center" @click="$router.push('/lich-trinh-cua-toi')">
              <i class="bi bi-arrow-left me-2"></i>Quay lại
            </button>
            <button v-if="trip.is_leader && !isFinalized" class="btn btn-danger rounded-pill px-4 py-2 border-0 fw-bold shadow-sm d-flex align-items-center" @click="openFinalizeModal">
              <i class="bi bi-lock-fill me-2"></i> Lưu & kết thúc
            </button>
            <button v-if="trip.is_leader && isFinalized" class="btn btn-warning rounded-pill px-4 py-2 border-0 fw-bold shadow-sm d-flex align-items-center" @click="openUnfinalizeModal">
              <i class="bi bi-unlock-fill me-2"></i> Mở lại để sửa
            </button>
            <button class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold d-flex align-items-center" @click="exportExcel" :disabled="exportingExcel">
              <span v-if="exportingExcel" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-file-earmark-excel-fill me-2 text-success"></i> Xuất Excel
            </button>
            <!-- AI Optimization Button -->
            <button v-if="!isFinalized && (trip.is_member || trip.is_owner)" 
              class="btn btn-outline-dark rounded-pill px-3 py-2 fw-bold d-flex align-items-center" 
              @click="reorderWithAi" 
              :disabled="loadingAI">
              <i v-if="loadingAI" class="spinner-border spinner-border-sm me-2"></i>
              <i v-else class="bi bi-robot me-2 text-primary"></i>
              AI Tối ưu lại
            </button>
            <button class="btn btn-primary rounded-pill px-4 py-2 border-0 fw-bold shadow-sm d-flex align-items-center position-relative overflow-hidden share-btn" @click="showShareModal = true" style="background: linear-gradient(135deg, #10b981, #0ea5e9);">
              <i class="bi bi-share-fill me-2"></i> Gửi vào nhóm
            </button>
          </div>
        </div>

        <div class="d-flex mb-4 gap-3 bg-light p-2 rounded-pill d-inline-flex">
           <button class="btn rounded-pill px-4" :class="activeMainTab === 'lichTrinh' ? 'btn-primary shadow text-white': 'text-muted'" @click="activeMainTab = 'lichTrinh'" style="font-weight: 500;">
             <i class="bi bi-clock-history me-2"></i>Lịch trình
           </button>
           <button class="btn rounded-pill px-4" :class="activeMainTab === 'chiPhi' ? 'btn-primary shadow text-white': 'text-muted'" @click="activeMainTab = 'chiPhi'" style="font-weight: 500;">
             <i class="bi bi-wallet2 me-2"></i>Quản lý chi phí
           </button>
        </div>

        <div v-if="isFinalized" class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; background: #fffbeb; border-color: #fde68a; color: #92400e;">
            <i class="bi bi-lock-fill fs-4 me-3 text-warning"></i>
            <div>
                <strong>Lịch trình đã được chốt!</strong> Trưởng nhóm đã khóa lịch trình này, không ai có thể chỉnh sửa thêm.
            </div>
        </div>

        <div v-if="tongChiPhiDuKien > trip.ngan_sach && trip.ngan_sach > 0" class="alert alert-danger d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
            <div>
                <strong class="text-danger">Cảnh báo ngân sách!</strong> Lịch trình hiện tại và chi phí phát sinh đã vượt quá ngân sách dự kiến của bạn.
                <br/>
                <span>Số tiền vượt: <b>{{ formatCurrency(tongChiPhiDuKien - trip.ngan_sach) }}</b></span>
            </div>
        </div>

        <div v-else-if="trip.ngan_sach > 0" class="alert alert-success d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
            <div>
                <strong class="text-success">Ngân sách an toàn!</strong> Lịch trình hiện tại đang nằm trong phạm vi ngân sách của bạn.(Chi phí chưa bao gồm khách sạn)
            </div>
        </div>

        <!-- Budget tracker -->
        <div class="budget-tracker">
          <div class="budget-info">
            <span>Tổng chi phí (Vé + Phát sinh)</span>
            <strong>{{ formatCurrency(tongChiPhiDuKien) }}</strong>
          </div>
          <div class="budget-info">
            <span>Giá vé dự kiến</span>
            <strong>{{ formatCurrency(tongGiaVe) }}</strong>
          </div>
          <div v-if="trip.ngan_sach > 0" class="budget-info">
            <span>Ngân sách</span>
            <strong :class="tongChiPhiDuKien > trip.ngan_sach ? 'over' : 'ok'">
              {{ formatCurrency(trip.ngan_sach) }}
            </strong>
          </div>
          <div v-if="trip.ngan_sach > 0" class="budget-bar-wrap">
            <div class="budget-bar-fill" :class="tongChiPhiDuKien > trip.ngan_sach ? 'over' : ''"
              :style="{ width: Math.min(100, (tongChiPhiDuKien / trip.ngan_sach) * 100) + '%' }"></div>
          </div>
        </div>

        <!-- SECTION: LỊCH TRÌNH -->
        <div v-show="activeMainTab === 'lichTrinh'" class="lich-trinh-section">
          <!-- Day tabs -->
          <div class="day-tabs">
          <button v-for="n in (trip.so_ngay || 1)" :key="n" class="day-tab" :class="{ active: activeDayTab === n }" @click="activeDayTab = n">
            <span class="day-tab-num">Ngày {{ n }}</span>
            <span class="day-tab-date">{{ formatDateDate(trip.ngay_bat_dau, n - 1) }}</span>
            <span class="day-tab-cost fw-bold d-block mt-1" style="font-size: 0.8rem;">
              <i class="bi bi-cash-coin me-1"></i>{{ formatCurrency(chiPhiTheoNgay(n - 1)) }}
            </span>
          </button>
        </div>

        <!-- 2-col layout: Timeline + Map -->
        <div class="step3-layout mt-4">
          <!-- LEFT: Timeline -->
          <div class="step3-left">
            <div class="timeline" v-if="lichTrinhTheoNgay[activeDayTab - 1] && lichTrinhTheoNgay[activeDayTab - 1].length > 0">
              <div v-for="(item, idx) in lichTrinhTheoNgay[activeDayTab - 1]" :key="item.id || idx" class="timeline-item">
                <div class="timeline-time">
                  <span class="time-badge">{{ item.gio_bat_dau || item.gio || '08:00' }}</span>
                  <span v-if="item.gio_ket_thuc" class="time-end-badge">{{ item.gio_ket_thuc }}</span>
                  <span v-if="item.thoi_luong_phut" class="duration-badge">{{ item.thoi_luong_phut }}p</span>
                  <div class="timeline-line" v-if="idx < lichTrinhTheoNgay[activeDayTab - 1].length - 1"></div>
                </div>
                <div class="timeline-card" :style="(!isFinalized && (trip.is_member || trip.is_owner)) ? 'cursor: grab;' : ''" :title="(!isFinalized && (trip.is_member || trip.is_owner)) ? 'Kéo thả để thay đổi thứ tự' : ''">
                  <div class="tc-header">
                    <div class="tc-img-wrap">
                      <img
                        :src="item.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&q=70'"
                        class="tc-img" loading="lazy" />
                    </div>
                    <div class="tc-meta">
                      <h5>{{ item.ten_dia_diem }}</h5>
                      <p><i class="bi bi-pin-map me-1"></i>{{ item.dia_chi || 'Đà Nẵng' }}</p>
                      <p v-if="item.gia_ve > 0">
                        <i class="bi bi-ticket-perforated me-1"></i>{{ formatCurrency(item.gia_ve) }} / người
                      </p>
                    </div>
                    <!-- Action buttons (only when not finalized and is member/owner) -->
                    <div v-if="!isFinalized && (trip.is_member || trip.is_owner)" class="ms-auto align-self-start d-flex flex-column gap-1">
                      <button v-if="item.id_dia_diem"
                        class="btn btn-sm btn-outline-primary rounded-circle shadow-sm"
                        @click="swapPlace(item)"
                        title="Đổi địa điểm khác"
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-repeat"></i>
                      </button>
                      <button
                        class="btn btn-sm btn-outline-danger rounded-circle shadow-sm"
                        @click="confirmRemovePlace(item, activeDayTab - 1, idx)"
                        title="Xóa địa điểm này"
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </div>
                  </div>
                  <div v-if="item.ghi_chu" class="tc-note mt-3 p-2 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.85rem; color: #475569;">
                    <i class="bi bi-pencil-square me-1"></i><span style="font-style: italic;">{{ item.ghi_chu }}</span>
                  </div>
                  <div class="tc-tips mt-2 p-2 rounded"
                    style="background: #fff8e1; border-left: 3px solid #ffc107;">
                    <small class="d-block mb-1 font-weight-bold" style="color: #856404;">
                      <i class="bi bi-lightbulb-fill me-1"></i>Gợi ý chuyên gia:
                    </small>
                    <span style="font-size: 0.85rem; color: #555;">
                      {{ item.travel_tips || 'Nên mang theo kem chống nắng, nước uống và sạc dự phòng để có trải nghiệm tốt nhất tại đây.' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <!-- Empty State for Day -->
            <div v-else class="empty-day-placeholder py-5 text-center rounded-4" style="background: #f8fafc;">
              <div class="empty-icon-wrap mb-3">
                <i class="bi bi-calendar-x text-muted fs-1"></i>
              </div>
              <h5 class="text-muted">Ngày này chưa có lịch trình</h5>
            </div>

            <!-- ─── Thêm địa điểm vào ngày (chỉ khi chưa chốt) ─── -->
            <div v-if="!isFinalized && (trip.is_member || trip.is_owner)" class="add-place-section mt-4">
              <button
                class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 w-100 justify-content-center"
                @click="toggleAddPlacePanel(activeDayTab - 1)"
                style="border-style: dashed; border-width: 2px; background: rgba(16,185,129,0.04);">
                <i class="bi bi-plus-circle-fill text-success"></i>
                <span>Thêm địa điểm vào Ngày {{ activeDayTab }}</span>
              </button>

              <!-- Panel chọn địa điểm thêm vào -->
              <div v-if="addPlacePanel.show && addPlacePanel.dayIdx === activeDayTab - 1" class="add-place-panel mt-3 p-3 rounded-4 border shadow-sm" style="background:#f8fbff;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="fw-bold mb-0"><i class="bi bi-search me-2 text-primary"></i>Tìm & chọn địa điểm</h6>
                  <button class="btn-close btn-sm" @click="addPlacePanel.show = false"></button>
                </div>

                <!-- Search box -->
                <div class="input-group mb-3">
                  <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                  <input type="text" class="form-control" v-model="addPlacePanel.keyword"
                    placeholder="Tìm theo tên địa điểm..." @input="filterAddPlaces">
                </div>

                <!-- Loading -->
                <div v-if="addPlacePanel.loading" class="text-center py-3">
                  <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                  <span class="ms-2 text-muted small">Đang tải danh sách...</span>
                </div>

                <!-- Place list -->
                <div v-else class="add-place-list">
                  <div v-if="addPlacePanel.filtered.length === 0" class="text-center text-muted py-3 small">
                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>Không tìm thấy địa điểm phù hợp
                  </div>
                  <div v-for="place in addPlacePanel.filtered.slice(0, 20)" :key="place.id"
                    class="add-place-item d-flex align-items-center gap-2 p-2 rounded-3 mb-2"
                    :class="{ 'already-added': isPlaceAlreadyInDay(place.id, addPlacePanel.dayIdx) }"
                    style="background:#fff; border:1px solid #e2e8f0; cursor:pointer; transition:all 0.2s;"
                    @click="!isPlaceAlreadyInDay(place.id, addPlacePanel.dayIdx) && addPlaceToDay(place, addPlacePanel.dayIdx)">
                    <img :src="place.hinh_anh_chinh || place.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=60&q=60'"
                      style="width:48px;height:40px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                    <div class="flex-grow-1 min-w-0">
                      <div class="fw-bold text-dark" style="font-size:0.88rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ place.ten_dia_diem }}</div>
                      <div class="text-muted" style="font-size:0.76rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ place.dia_chi }}</div>
                    </div>
                    <span v-if="isPlaceAlreadyInDay(place.id, addPlacePanel.dayIdx)" class="badge bg-secondary" style="font-size:0.68rem;">Đã có</span>
                    <button v-else class="btn btn-success btn-sm rounded-pill px-2 py-1 flex-shrink-0" style="font-size:0.76rem;"
                      @click.stop="addPlaceToDay(place, addPlacePanel.dayIdx)"
                      :disabled="addPlacePanel.addingId === place.id">
                      <span v-if="addPlacePanel.addingId === place.id" class="spinner-border spinner-border-sm"></span>
                      <i v-else class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT: Map + Route Info -->
          <div class="step3-right">
            <div class="map-panel">
              <div class="map-panel-head">
                <i class="bi bi-map me-2"></i>Bản đồ vị trí ngày {{ activeDayTab }}
              </div>
              <div id="trip-map" style="height: 500px"></div>
              <!-- Route info panel -->
              <div v-if="routeInfo.distance" class="d-flex align-items-center gap-3 p-2 px-3" style="background:#f0f9ff;border-top:1px solid #e0f2fe;">
                <span class="small text-primary fw-bold"><i class="bi bi-signpost-2 me-1"></i>Tổng quãng đường: {{ routeInfo.distance }}</span>
                <span class="small text-muted"><i class="bi bi-clock me-1"></i>Thời gian di chuyển: {{ routeInfo.duration }}</span>
              </div>
            </div>
          </div>
        </div>
        </div>
        <!-- End Lich Trinh Section -->

        <!-- SECTION: QUẢN LÝ CHI PHÍ -->
        <div v-show="activeMainTab === 'chiPhi'" class="chi-phi-section animate-in mt-4">
          
          <!-- Banner khóa khi đã chốt -->
          <div v-if="isFinalized" class="alert d-flex align-items-center mb-4 shadow-sm" role="alert"
            style="border-radius: 12px; background: #fefce8; border: 1px solid #fde047; color: #92400e;">
            <i class="bi bi-lock-fill fs-4 me-3 text-warning"></i>
            <div>
              <strong>Tính năng đã bị khóa.</strong> Lịch trình đã được chốt, không thể thêm/sửa/xóa chi phí phát sinh.
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Danh sách phát sinh</h4>
            <button v-if="!isFinalized" class="btn btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center" @click="openExpenseModal()" style="background: linear-gradient(135deg, #10b981, #0ea5e9); border: none;">
              <i class="bi bi-plus-circle me-2"></i> Thêm chi phí
            </button>
          </div>

          <div v-if="loadingExpenses" class="text-center py-5">
            <div class="spinner-border text-brand" role="status"></div>
          </div>
          <div v-else-if="incurredExpenses.length === 0" class="empty-day-placeholder py-5 text-center rounded-4" style="background: #f8fafc;">
            <div class="empty-icon-wrap mb-3 mx-auto" style="width: 60px; height: 60px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-receipt text-muted fs-3"></i>
            </div>
            <h5 class="text-muted">Chưa có chi phí phát sinh nào</h5>
            <p class="text-secondary small">Hãy ghi lại các hóa đơn ăn uống, di chuyển để dễ dàng quản lý ngân sách.</p>
          </div>
          <div v-else class="expense-list row g-3">
            <div v-for="exp in incurredExpenses" :key="exp.id" class="col-12 col-md-6">
              <div class="expense-card p-3 rounded-4 bg-white border shadow-sm position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge" :class="getExpenseBadgeColor(exp.loai_chi_phi)">{{ exp.loai_chi_phi || 'Khác' }}</span>
                    <span class="text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ formatExpenseDate(exp.ngay_chi) }}</span>
                  </div>
                  <div v-if="!isFinalized" class="dropdown">
                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                      <li><a class="dropdown-item" href="#" @click.prevent="openExpenseModal(exp)"><i class="bi bi-pencil me-2 text-primary"></i>Sửa</a></li>
                      <li><a class="dropdown-item text-danger" href="#" @click.prevent="deleteExpense(exp.id)"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                    </ul>
                  </div>
                </div>
                <h5 class="fw-bold mb-1 text-dark fs-6">{{ exp.noi_dung }}</h5>
                <h4 class="text-danger fw-bold mb-3">{{ formatCurrency(exp.tong_chi_phi) }}</h4>
                <div class="d-flex align-items-center mb-1 bg-light p-2 rounded-3">
                  <img :src="exp.nguoi_tra?.anh_dai_dien ? getFullAvatar(exp.nguoi_tra.anh_dai_dien) : 'https://ui-avatars.com/api/?name=' + (exp.nguoi_tra?.ten || 'U')" class="rounded-circle me-2 object-fit-cover" width="24" height="24">
                  <small class="text-muted">Được trả bởi: <strong class="text-dark">{{ exp.nguoi_tra?.ten || 'Không rõ' }}</strong></small>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ─── Modal xác nhận xóa địa điểm ─── -->
    <div v-if="removePlaceModal.show" class="schedule-confirm-overlay" @click.self="removePlaceModal.show = false">
      <div class="schedule-confirm-box">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="removePlaceModal.show = false"></button>
        <div class="schedule-confirm-icon danger">
          <i class="bi bi-trash3-fill"></i>
        </div>
        <h4 class="schedule-confirm-title">Xóa địa điểm</h4>
        <p class="schedule-confirm-message">
          Bạn có chắc muốn xóa <strong>"{{ removePlaceModal.item?.ten_dia_diem }}"</strong> khỏi lịch trình?
        </p>
        <div class="schedule-confirm-actions">
          <button class="schedule-confirm-cancel" @click="removePlaceModal.show = false">Hủy</button>
          <button class="schedule-confirm-submit danger" @click="doRemovePlace" :disabled="removePlaceModal.removing">
            <span v-if="removePlaceModal.removing" class="spinner-border spinner-border-sm me-1"></span>
            Xóa địa điểm
          </button>
        </div>
      </div>
    </div>

    <!-- Modal chọn nhóm chia sẻ -->
    <div
      v-if="scheduleConfirmModal.show"
      class="schedule-confirm-overlay"
      @click.self="closeScheduleConfirmModal"
    >
      <div class="schedule-confirm-box">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="closeScheduleConfirmModal"></button>
        <div class="schedule-confirm-icon" :class="scheduleConfirmModal.variant">
          <i :class="scheduleConfirmModal.icon"></i>
        </div>
        <h4 class="schedule-confirm-title">{{ scheduleConfirmModal.title }}</h4>
        <p class="schedule-confirm-message">{{ scheduleConfirmModal.message }}</p>
        <div class="schedule-confirm-actions">
          <button class="schedule-confirm-cancel" @click="closeScheduleConfirmModal">Hủy</button>
          <button
            class="schedule-confirm-submit"
            :class="scheduleConfirmModal.variant"
            @click="confirmScheduleAction"
          >
            {{ scheduleConfirmModal.confirmText }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="showShareModal" class="share-modal-overlay d-flex align-items-center justify-content-center" @click.self="showShareModal = false">
      <div class="share-modal-box bg-white p-5 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 440px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="showShareModal = false"></button>
        <div class="text-center mb-4 mt-2">
          <div class="share-icon-wrap mb-3 mx-auto shadow-sm">
             <i class="bi bi-send-check-fill fs-2 text-white"></i>
          </div>
          <h4 class="fw-bold mb-1 text-dark">Chia sẻ lịch trình</h4>
          <p class="text-muted mb-0" style="font-size: 0.95rem;">Chọn nhóm để gửi chuyến đi này nhé!</p>
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

    <!-- Modal Form Chi phí -->
    <div v-if="showExpenseModal" class="share-modal-overlay d-flex align-items-center justify-content-center" @click.self="showExpenseModal = false">
      <div class="share-modal-box bg-white p-4 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 500px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="showExpenseModal = false"></button>
        <h4 class="fw-bold mb-4">{{ expenseForm.id ? 'Sửa chi phí' : 'Thêm chi phí phát sinh' }}</h4>
        
        <form @submit.prevent="saveExpense">
          <div class="mb-3">
            <label class="form-label fw-bold small">Mô tả / Nội dung chi</label>
            <input type="text" class="form-control rounded-3" v-model="expenseForm.noi_dung" required placeholder="Ví dụ: Ăn hải sản tối, Thuê xe máy...">
          </div>
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label fw-bold small">Số tiền (VNĐ)</label>
              <input type="number" class="form-control rounded-3" v-model="expenseForm.tong_chi_phi" required min="0" placeholder="100000">
            </div>
            <div class="col-6">
              <label class="form-label fw-bold small">Ngày chi</label>
              <input type="date" class="form-control rounded-3" v-model="expenseForm.ngay_chi">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label fw-bold small">Loại chi phí</label>
            <select class="form-select rounded-3" v-model="expenseForm.loai_chi_phi">
              <option v-for="cat in expenseCategories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <button type="submit" class="btn w-100 text-white rounded-pill fw-bold py-2 shadow-sm" style="background: linear-gradient(135deg, #10b981, #0ea5e9);" :disabled="submittingExpense">
            <span v-if="submittingExpense" class="spinner-border spinner-border-sm me-2"></span>
            {{ submittingExpense ? 'Đang lưu...' : 'Lưu lại' }}
          </button>
        </form>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════
         MODAL ĐÁNH GIÁ MỨC ĐỘ HÀI LÒNG
         ═══════════════════════════════════════════ -->
    <transition name="rating-modal">
      <div v-if="showRatingModal" class="rating-modal-overlay" @click.self="skipRating">
        <div class="rating-modal-box">
          <div class="confetti-wrap">
            <span v-for="n in 12" :key="n" class="confetti-dot" :style="confettiStyle(n)"></span>
          </div>
          <div class="rm-header">
            <div class="rm-success-icon"><i class="bi bi-check-circle-fill"></i></div>
            <h2 class="rm-title">🎉 Lịch trình đã được chốt!</h2>
            <p class="rm-subtitle">Bạn cảm thấy thế nào về hệ thống lập lịch trình của chúng tôi?</p>
          </div>
          <div class="rm-faces">
            <button v-for="face in ratingFaces" :key="face.value" class="rm-face-btn"
              :class="{ selected: selectedRating === face.value }" @click="selectedRating = face.value"
              :title="face.label">
              <span class="rm-face-emoji"></span>
              <span class="rm-face-icon">{{ face.icon }}</span>
              <span class="rm-face-label">{{ face.label }}</span>
            </button>
          </div>
          <transition name="fade-slide">
            <p v-if="selectedRating" class="rm-selected-text">
              {{ratingFaces.find(f => f.value === selectedRating)?.feedback}}
            </p>
          </transition>
          <div class="rm-feedback-wrap">
            <label class="rm-feedback-label"><i class="bi bi-chat-heart me-1"></i>Để lại đóng góp của bạn (không bắt buộc)</label>
            <textarea v-model="ratingFeedback" class="rm-textarea" rows="3" placeholder="Ví dụ: Giao diện dễ dùng, AI gợi ý rất hữu ích..."></textarea>
          </div>
          <div class="rm-actions">
            <button class="rm-btn-skip" @click="skipRating">Bỏ qua</button>
            <button class="rm-btn-submit" :disabled="!selectedRating || submittingRating" @click="submitRating">
              <span v-if="submittingRating"><i class="bi bi-hourglass-split me-1"></i>Đang gửi...</span>
              <span v-else><i class="bi bi-send-fill me-1"></i>Gửi đánh giá</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
    <!-- ═══════════════════════════════════════════
         HIDDEN PDF TEMPLATE (Chỉ dùng để render file PDF)
         ═══════════════════════════════════════════ -->
    <div v-if="trip" id="pdf-summary-content" style="display: none;">
       <div style="padding: 20px 30px; font-family: 'Inter', sans-serif; color: #1e2d44; background: #fff;">
          <h1 style="color: #10b981; text-align: center; margin-bottom: 5px; font-size: 26px;">{{ trip.ten_chuyen_di }}</h1>
          <p style="text-align: center; color: #64748b; margin-bottom: 25px; font-size: 14px;">
             Từ {{ formatDateFull(trip.ngay_bat_dau) }} &nbsp;•&nbsp; {{ trip.so_nguoi || 1 }} thành viên &nbsp;•&nbsp; {{ formatDuration(trip.so_ngay) }}
          </p>
          
          <div style="display: flex; justify-content: space-between; margin-bottom: 25px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
             <div><strong style="color: #475569;">Ngân sách: </strong> <span style="font-weight: bold; color: #10b981;">{{ formatCurrency(trip.ngan_sach) }}</span></div>
             <div><strong style="color: #475569;">Dự kiến: </strong> <span style="font-weight: bold; color: #f59e0b;">{{ formatCurrency(tongChiPhiDuKien) }}</span></div>
          </div>

          <h3 style="border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; font-size: 18px; color: #0f172a;">Lịch trình chi tiết</h3>
          
          <div v-for="(day, di) in lichTrinhTheoNgay" :key="'pdf-day-'+di" style="margin-bottom: 25px; page-break-inside: avoid;">
             <div style="background: #e0f2fe; padding: 10px 15px; border-radius: 8px 8px 0 0; font-weight: bold; color: #0369a1; font-size: 15px;">
                Ngày {{ di + 1 }} - {{ formatDateDate(trip.ngay_bat_dau, di) }}
             </div>
             <div style="border: 1px solid #e0f2fe; border-top: none; padding: 15px; border-radius: 0 0 8px 8px; background: #fff;">
                <table style="width: 100%; border-collapse: collapse;">
                   <tr v-for="(item, idx) in day" :key="'pdf-item-'+idx">
                      <td style="width: 75px; padding: 10px 0; font-weight: 600; color: #475569; vertical-align: top; font-size: 14px; border-bottom: 1px dashed #e2e8f0;">
                         {{ item.gio_bat_dau || item.gio || '08:00' }}
                      </td>
                      <td style="padding: 10px 0; vertical-align: top; border-bottom: 1px dashed #e2e8f0;">
                         <div style="font-weight: 600; color: #0f172a; font-size: 15px; margin-bottom: 3px;">{{ item.ten_dia_diem }}</div>
                         <div style="font-size: 13px; color: #64748b;"><i class="bi bi-geo-alt me-1"></i>{{ item.dia_chi }}</div>
                         <div v-if="item.ghi_chu" style="font-size: 13px; color: #059669; margin-top: 5px; background: #ecfdf5; padding: 4px 8px; border-radius: 4px; display: inline-block;"><i>Ghi chú: {{ item.ghi_chu }}</i></div>
                      </td>
                   </tr>
                   <tr v-if="!day || day.length === 0">
                      <td colspan="2" style="padding: 10px 0; color: #94a3b8; font-style: italic;">Chưa có hoạt động nào trong ngày này.</td>
                   </tr>
                </table>
             </div>
          </div>
       </div>
    </div>

  </div>
</template>

<script>
import { axiosExternal } from '../../services/api';
import api from '../../services/api';

export default {
  name: 'ChiTietLichTrinh',
  data() {
    return {
      tripId: this.$route.params.id,
      token: localStorage.getItem('client_token'),
      loading: true,
      trip: null,
      rawPlaces: [],
      lichTrinhTheoNgay: [],
      activeDayTab: 1,
      activeMainTab: 'lichTrinh',

      incurredExpenses: [],
      loadingExpenses: false,
      showExpenseModal: false,
      expenseForm: { id: null, noi_dung: '', tong_chi_phi: '', loai_chi_phi: 'Khác', ngay_chi: '' },
      submittingExpense: false,
      expenseCategories: ['Ăn uống', 'Di chuyển', 'Chỗ ở', 'Vé tham quan', 'Mua sắm', 'Khác'],

      // Modal chia sẻ
      showShareModal: false,
      myJoinedGroups: [],
      selectedGroupToShare: null,
      sendingShare: false,
      scheduleConfirmModal: {
        show: false,
        action: null,
        title: '',
        message: '',
        confirmText: '',
        icon: '',
        variant: 'danger',
      },
      
      showRatingModal: false,
      selectedRating: null,
      ratingFeedback: '',
      submittingRating: false,
      ratingFaces: [
        { value: 1, icon: '😞', label: 'Rất tệ', feedback: 'Rất tiếc khi nghe điều này. Chúng tôi sẽ cố gắng cải thiện!' },
        { value: 2, icon: '😕', label: 'Tệ', feedback: 'Cảm ơn bạn đã phản hồi. Ý kiến của bạn rất có giá trị!' },
        { value: 3, icon: '😐', label: 'Bình thường', feedback: 'Cảm ơn! Chúng tôi đang nỗ lực để làm tốt hơn.' },
        { value: 4, icon: '😊', label: 'Tốt', feedback: 'Tuyệt vời! Rất vui vì bạn hài lòng với trải nghiệm.' },
        { value: 5, icon: '🤩', label: 'Rất tốt', feedback: 'Cảm ơn bạn rất nhiều! Điều này thật sự truyền cảm hứng cho chúng tôi! 🚀' },
      ],

      mapInstance: null,
      mapLayers: [],
      mapRouteLine: null,
      routeInfo: { distance: '', duration: '' },
      exportingExcel: false,
      loadingAI: false,
      socketChannel: null,

      // ─── Xóa địa điểm modal ───
      removePlaceModal: {
        show: false,
        item: null,
        dayIdx: -1,
        itemIdx: -1,
        removing: false,
      },

      // ─── Panel thêm địa điểm ───
      addPlacePanel: {
        show: false,
        dayIdx: -1,
        keyword: '',
        loading: false,
        allPlaces: [],
        filtered: [],
        addingId: null,
      },
    };
  },

  computed: {
    tongGiaVe() {
      if (!this.trip) return 0;
      return this.rawPlaces.reduce((sum, item) => sum + (Number(item.gia_ve) || 0) * (this.trip.so_nguoi || 1), 0);
    },
    tongChiPhiPhatSinh() {
      return this.incurredExpenses.reduce((sum, exp) => sum + Number(exp.tong_chi_phi), 0);
    },
    tongChiPhiDuKien() {
      return this.tongGiaVe + this.tongChiPhiPhatSinh;
    },
    isFinalized() {
      return this.trip && this.trip.trang_thai === 2;
    }
  },

  watch: {
    activeDayTab(newVal) {
      if (this.trip) {
        this.$nextTick(() => {
          this.renderMapForDay(newVal - 1);
          this.waitAndInitSortable();
        });
      }
    },
    showShareModal(newVal) {
      if (newVal && this.myJoinedGroups.length === 0) {
        this.fetchMyGroups();
      }
    }
  },

  async mounted() {
    if (!document.getElementById('leaflet-css')) {
      const link = document.createElement('link');
      link.id = 'leaflet-css';
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }
    if (!window.L) {
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      document.head.appendChild(script);
    }
    if (!window.Sortable) {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
      script.id = 'sortable-js';
      document.head.appendChild(script);
    }
    if (!window.html2pdf) {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
      document.head.appendChild(script);
    }
    // Load SheetJS for Excel export
    if (!window.XLSX) {
      const script = document.createElement('script');
      script.src = 'https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js';
      document.head.appendChild(script);
    }

    if (!this.token) {
      this.$toast.warning("Vui lòng đăng nhập để xem lịch trình.");
      this.$router.push('/client/dang-nhap');
      return;
    }
    await this.fetchTripData();
    this.initSocket();
  },
  beforeUnmount() {
    if (this.socketChannel) {
        import('../../composables/useNhomChatSocket').then(({ getEcho }) => {
            getEcho().leave(this.socketChannel);
        });
    }
  },

  methods: {
    openFinalizeModal() {
      this.scheduleConfirmModal = {
        show: true,
        action: 'finalize',
        title: 'Chốt lịch trình',
        message: 'Bạn có chắc chắn muốn chốt lịch trình này? Các thành viên sẽ không thể chỉnh sửa sau khi chốt.',
        confirmText: 'Chốt lịch trình',
        icon: 'bi bi-lock-fill',
        variant: 'danger',
      };
    },

    openUnfinalizeModal() {
      this.scheduleConfirmModal = {
        show: true,
        action: 'unfinalize',
        title: 'Mở lại lịch trình',
        message: 'Bạn có muốn mở lại lịch trình để chỉnh sửa?',
        confirmText: 'Mở lại',
        icon: 'bi bi-unlock-fill',
        variant: 'warning',
      };
    },

    closeScheduleConfirmModal() {
      this.scheduleConfirmModal.show = false;
    },

    confirmScheduleAction() {
      const action = this.scheduleConfirmModal.action;
      this.closeScheduleConfirmModal();
      if (action === 'finalize') this.finalizeTrip();
      if (action === 'unfinalize') this.unfinalizeTrip();
    },

    async fetchTripData() {
      this.loading = true;
      try {
        // Lấy chi tiết chuyến đi
        const res = await api.get(`/chuyen-dis/${this.tripId}`);
        const json = res.data;
        
        if (json.status === 'success' && json.data) {
          this.trip = json.data;
          if(!this.trip.so_ngay) this.trip.so_ngay = 1;

          // Lấy danh sách địa điểm theo chuyến đi
          const res2 = await api.get(`/chuyen-di/${this.tripId}/dia-diems`);
          const json2 = res2.data;
          this.rawPlaces = json2.data || [];

          // Parse vào lịch trình theo ngày
          this.buildSchedule();

          // Lấy chi phí phát sinh
          await this.fetchExpenses();
        }
      } catch (e) {
        console.error("Lỗi khi tải chuyến đi:", e);
      } finally {
        this.loading = false;
        this.$nextTick(() => {
          this.initMap();
          this.renderMapForDay(this.activeDayTab - 1);
          this.waitAndInitSortable();
        });
      }
    },

    initSocket() {
      if (!this.trip?.id_nhom_du_lich) return;
      try {
          // Import dynamic to avoid SSR/Initial load issues
          import('../../composables/useNhomChatSocket').then(({ getEcho }) => {
              const echo = getEcho();
              const channelName = `nhom-chat.${this.trip.id_nhom_du_lich}`;
              if (this.socketChannel) echo.leave(this.socketChannel);
              
              this.socketChannel = channelName;
              echo.private(channelName).listen('.itinerary.reordered', (e) => {
                  console.log('📡 Itinerary updated via socket:', e);
                  this.$toast.info('Lịch trình đã được cập nhật bởi thành viên khác.');
                  this.fetchTripData(); // Reload data
              });
          });
      } catch(err) {
          console.warn('Realtime socket not available');
      }
    },

    async fetchExpenses() {
      this.loadingExpenses = true;
      try {
        const res = await api.get(`/chuyen-dis/${this.tripId}/chi-phis`);
        const json = res.data;
        if (json.status === 'success') {
          this.incurredExpenses = json.data || [];
        }
      } catch (e) {
        console.error("Lỗi tải chi phí", e);
      } finally {
        this.loadingExpenses = false;
      }
    },

    openExpenseModal(exp = null) {
      if (exp) {
        this.expenseForm = { ...exp };
        if (this.expenseForm.ngay_chi) {
          this.expenseForm.ngay_chi = this.expenseForm.ngay_chi.split('T')[0];
        }
      } else {
        this.expenseForm = { id: null, noi_dung: '', tong_chi_phi: '', loai_chi_phi: 'Khác', ngay_chi: new Date().toISOString().split('T')[0] };
      }
      this.showExpenseModal = true;
    },

    async saveExpense() {
      if (!this.expenseForm.noi_dung || !this.expenseForm.tong_chi_phi) {
        this.$toast.warning("Vui lòng nhập đủ thông tin chi phí (Nội dung, Tổng tiền).");
        return;
      }
      
      this.submittingExpense = true;
      const isEdit = !!this.expenseForm.id;

      // Chuyển đổi kiểu dữ liệu đúng để tránh lỗi validation
      const clientId = parseInt(localStorage.getItem('client_id')) || null;
      const payload = {
        id_chuyen_di: parseInt(this.tripId),
        noi_dung: this.expenseForm.noi_dung,
        tong_chi_phi: parseFloat(this.expenseForm.tong_chi_phi),
        loai_chi_phi: this.expenseForm.loai_chi_phi || 'Khác',
        ngay_chi: this.expenseForm.ngay_chi || null,
        id_nguoi_tra: clientId,
      };

      try {
        const res = isEdit
          ? await api.put(`/chi-phi-phat-sinhs/${this.expenseForm.id}`, payload)
          : await api.post('/chi-phi-phat-sinhs', payload);
        const json = res.data;
        if (json.status === 'success') {
          this.$toast.success(isEdit ? "Cập nhật chi phí thành công!" : "Thêm chi phí thành công!");
          this.showExpenseModal = false;
          await this.fetchExpenses();
        } else {
          // Hiển thị lỗi validation chi tiết từ server
          const errMsg = json.errors ? Object.values(json.errors).flat().join(', ') : (json.message || "Lỗi lưu chi phí");
          this.$toast.error(errMsg);
          console.error('Validation errors:', json);
        }
      } catch (e) {
        const json = e.response?.data;
        if (json) {
          const errMsg = json.errors ? Object.values(json.errors).flat().join(', ') : (json.message || "Lỗi lưu chi phí");
          this.$toast.error(errMsg);
          console.error('Validation errors:', json);
          return;
        }
        this.$toast.error("Lỗi kết nối đến máy chủ.");
        console.error(e);
      } finally {
        this.submittingExpense = false;
      }
    },

    async deleteExpense(id) {
      if (!confirm("Bạn có chắc muốn xóa chi phí này?")) return;
      try {
        const res = await api.delete(`/chi-phi-phat-sinhs/${id}`);
        const json = res.data;
        if (json.status === 'success') {
          this.$toast.success("Xóa thành công");
          await this.fetchExpenses();
        } else {
          this.$toast.error("Xóa thất bại");
        }
      } catch (e) {
        this.$toast.error("Lỗi kết nối");
      }
    },

    getExpenseBadgeColor(type) {
      const colors = {
        'Ăn uống': 'bg-warning text-dark',
        'Di chuyển': 'bg-info text-dark',
        'Chỗ ở': 'bg-primary text-white',
        'Vé tham quan': 'bg-success text-white',
        'Mua sắm': 'bg-danger text-white',
        'Khác': 'bg-secondary text-white'
      };
      return colors[type] || colors['Khác'];
    },

    getFullAvatar(avatar) {
      if (!avatar) return '';
      if (avatar.startsWith('http')) return avatar;
      return `${(import.meta.env.VITE_BACKEND_URL || '').replace(/\/+$/, '')}/storage/${avatar}`;
    },

    buildSchedule() {
      const soNgay = this.trip.so_ngay || 1;
      const schedule = Array.from({ length: soNgay }, () => []);

      this.rawPlaces.forEach(item => {
        let dayIdx = 0;
        // thu_tu_tham_quan = dayIndex * 100 + posInDay + 1
        if (item.thu_tu_tham_quan && item.thu_tu_tham_quan > 0) {
          dayIdx = Math.floor((item.thu_tu_tham_quan - 1) / 100);
        }
        if (dayIdx < 0) dayIdx = 0;
        if (dayIdx >= soNgay) dayIdx = soNgay - 1;

        // Bóc tách AI tips từ ghi_chu nếu có
        if (item.ghi_chu && item.ghi_chu.includes('|AI_TIPS|')) {
          const parts = item.ghi_chu.split('|AI_TIPS|');
          item.ghi_chu = parts[0];
          item.travel_tips = parts[1];
        }

        schedule[dayIdx].push(item);
      });

      // Sắp xếp từng ngày theo vị trí trong ngày (thu_tu_tham_quan % 100)
      schedule.forEach(day => {
        day.sort((a, b) => {
          const posA = a.thu_tu_tham_quan ? (a.thu_tu_tham_quan - 1) % 100 : 0;
          const posB = b.thu_tu_tham_quan ? (b.thu_tu_tham_quan - 1) % 100 : 0;
          // Fallback: sort theo gio_bat_dau nếu cùng vị trí
          if (posA !== posB) return posA - posB;
          return (a.gio_bat_dau || a.gio || '').localeCompare(b.gio_bat_dau || b.gio || '');
        });
      });

      this.lichTrinhTheoNgay = schedule;
    },

    extractTime(t) {
        if (!t) return '';
        // Nếu t là HH:MM trực tiếp, trả về luôn
        if (/^\d{1,2}:\d{2}$/.test(String(t).trim())) return String(t).trim();
        // Fallback: parse chuỗi cũ "Ngày X – HH:MM"
        let parts = String(t).split(/[-\u2013]/);
        if (parts.length > 1) return parts[1].trim();
        return String(t).replace(/Ngày \d+\s*[-\u2013]\s*/i, '').trim();
    },

    chiPhiTheoNgay(dayIndex) {
      if (!this.lichTrinhTheoNgay[dayIndex]) return 0;
      return this.lichTrinhTheoNgay[dayIndex].reduce((sum, item) => {
        return sum + (Number(item.gia_ve) || 0) * (this.trip.so_nguoi || 1);
      }, 0);
    },

    formatExpenseDate(dateStr) {
      if (!dateStr) return 'Chưa rõ';
      try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
      } catch { return dateStr; }
    },

    formatCurrency(val) {
      if (!val) return '0đ';
      return Number(val).toLocaleString('vi-VN') + 'đ';
    },

    formatDateFull(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString('vi-VN', { 
        weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit' 
      });
    },

    formatDateDate(baseDate, addDays) {
      if (!baseDate) return '';
      const d = new Date(baseDate);
      d.setDate(d.getDate() + addDays);
      return d.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit' });
    },

    formatDuration(soNgay) {
      if (!soNgay) return '?';
      return `${soNgay} ngày ${Math.max(0, soNgay - 1)} đêm`;
    },

    // ─── Modal & Chia sẻ ────────────────────────────────
    async finalizeTrip() {
        try {
            const res = await api.post(`/client/chuyen-di/${this.tripId}/chot-lich-trinh`);
            const data = res.data;
            if (data.status) {
                this.$toast.success('Đã chốt lịch trình thành công!');
                this.trip.trang_thai = 2; // update local state
                setTimeout(() => {
                    this.showRatingModal = true;
                }, 800);
            } else {
                this.$toast.error(data.message || 'Lỗi chốt lịch trình');
            }
        } catch (e) {
            this.$toast.error('Lỗi kết nối khi chốt lịch trình.');
        }
    },

    async unfinalizeTrip() {
        try {
            const res = await api.post(`/client/chuyen-di/${this.tripId}/mo-lich-trinh`);
            const data = res.data;
            if (data.status) {
                this.$toast.success('Đã mở lại lịch trình. Các thành viên có thể tiếp tục chỉnh sửa.');
                this.trip.trang_thai = 1; // update local state
                // Re-init sortable since it might have been disabled
                this.$nextTick(() => {
                    this.waitAndInitSortable();
                });
            } else {
                this.$toast.error(data.message || 'Lỗi mở lại lịch trình');
            }
        } catch (e) {
            this.$toast.error('Lỗi kết nối khi mở lại lịch trình.');
        }
    },

    async reorderWithAi() {
      if (this.loadingAI) return;
      this.loadingAI = true;
      this.$toast.info('AI đang phân tích và tối ưu lại lịch trình của bạn...');

      try {
        const res = await api.post(`/client/ai/reorder-itinerary/${this.tripId}`);
        const json = res.data;
        if (json.status === 'success') {
          this.$toast.success('AI đã tối ưu lại lịch trình thành công!');
          await this.fetchTripData(); // Reload UI
        } else {
          this.$toast.error(json.message || 'Lỗi khi gọi AI');
        }
      } catch (err) {
        this.$toast.error('Lỗi kết nối máy chủ.');
      } finally {
        this.loadingAI = false;
      }
    },

    async swapPlace(item) {
        if (!item.id) {
            this.$toast.error('Không thể đổi địa điểm này.');
            return;
        }
        
        try {
            const btn = document.activeElement;
            const originalHTML = btn ? btn.innerHTML : '';
            if(btn && btn.tagName === 'BUTTON') btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            const res = await api.post(`/lich-trinh-dia-diems/${item.id}/swap`);
            const json = res.data;
            if (json.status) {
                this.$toast.success('Đã thay đổi địa điểm mới!');
                await this.fetchTripData(); // Tải lại chuyến đi
            } else {
                this.$toast.error(json.message || 'Không tìm thấy địa điểm thay thế!');
                if(btn && btn.tagName === 'BUTTON') btn.innerHTML = originalHTML;
            }
        } catch (error) {
            this.$toast.error('Lỗi khi đổi địa điểm!');
            console.error(error);
        }
    },

    // ─── Xóa địa điểm khỏi lịch trình ──────────────────
    confirmRemovePlace(item, dayIdx, itemIdx) {
      this.removePlaceModal = {
        show: true,
        item,
        dayIdx,
        itemIdx,
        removing: false,
      };
    },

    async doRemovePlace() {
      const { item, dayIdx, itemIdx } = this.removePlaceModal;
      if (!item) return;
      this.removePlaceModal.removing = true;

      try {
        // Xóa bản ghi lich_trinh_dia_diem khỏi DB
        if (item.id) {
          await api.delete(`/lich-trinh-dia-diems/${item.id}`);
        }

        // Xóa khỏi local state
        this.lichTrinhTheoNgay[dayIdx].splice(itemIdx, 1);
        // Cập nhật rawPlaces
        this.rawPlaces = this.rawPlaces.filter(p => p.id !== item.id);

        this.$toast.success(`Đã xóa "${item.ten_dia_diem}" khỏi lịch trình.`);
        this.removePlaceModal.show = false;

        // Cập nhật lại bản đồ
        this.$nextTick(() => {
          this.renderMapForDay(dayIdx);
        });
      } catch (e) {
        this.$toast.error('Lỗi khi xóa địa điểm. Vui lòng thử lại.');
        console.error(e);
      } finally {
        this.removePlaceModal.removing = false;
      }
    },

    // ─── Thêm địa điểm vào ngày ──────────────────────────
    async toggleAddPlacePanel(dayIdx) {
      if (this.addPlacePanel.show && this.addPlacePanel.dayIdx === dayIdx) {
        this.addPlacePanel.show = false;
        return;
      }

      this.addPlacePanel.show = true;
      this.addPlacePanel.dayIdx = dayIdx;
      this.addPlacePanel.keyword = '';

      if (this.addPlacePanel.allPlaces.length === 0) {
        this.addPlacePanel.loading = true;
        try {
          const res = await api.get('/client/dia-diem/get-data');
          const json = res.data;
          this.addPlacePanel.allPlaces = json.data || [];
        } catch (e) {
          this.$toast.error('Không thể tải danh sách địa điểm.');
        } finally {
          this.addPlacePanel.loading = false;
        }
      }

      this.filterAddPlaces();
    },

    filterAddPlaces() {
      const kw = (this.addPlacePanel.keyword || '').toLowerCase().trim();
      const places = this.addPlacePanel.allPlaces;
      this.addPlacePanel.filtered = kw
        ? places.filter(p =>
            (p.ten_dia_diem || '').toLowerCase().includes(kw) ||
            (p.dia_chi || '').toLowerCase().includes(kw)
          )
        : places;
    },

    isPlaceAlreadyInDay(placeId, dayIdx) {
      const day = this.lichTrinhTheoNgay[dayIdx] || [];
      return day.some(item => item.id_dia_diem === placeId || item.id_dia_diem === String(placeId));
    },

    async addPlaceToDay(place, dayIdx) {
      if (this.addPlacePanel.addingId === place.id) return;
      this.addPlacePanel.addingId = place.id;

      try {
        // Tính giờ bắt đầu dựa trên item cuối cùng của ngày
        const dayItems = this.lichTrinhTheoNgay[dayIdx] || [];
        let startMin = 8 * 60; // Mặc định 08:00
        if (dayItems.length > 0) {
          const lastItem = dayItems[dayItems.length - 1];
          const endTime = lastItem.gio_ket_thuc || lastItem.gio_bat_dau || '08:00';
          const [h, m] = endTime.split(':').map(Number);
          startMin = h * 60 + m + 30; // +30 phút di chuyển
        }
        const fmt = m => `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;
        const thuTu = (dayIdx * 100) + dayItems.length + 1;

        const payload = {
          id_chuyen_di: parseInt(this.tripId),
          id_dia_diem: place.id,
          gio_bat_dau: fmt(startMin),
          gio_ket_thuc: fmt(startMin + 60),
          thu_tu_tham_quan: thuTu,
          ghi_chu: '',
        };

        const res = await api.post('/lich-trinh-dia-diems', payload);
        const json = res.data;

        if (json.status === 'success' || json.id || json.data?.id) {
          this.$toast.success(`Đã thêm "${place.ten_dia_diem}" vào Ngày ${dayIdx + 1}!`);

          // Thêm ngay vào local state để UI cập nhật tức thì
          const newItem = {
            id: json.data?.id || json.id,
            id_dia_diem: place.id,
            ten_dia_diem: place.ten_dia_diem,
            dia_chi: place.dia_chi,
            hinh_anh: place.hinh_anh_chinh || place.hinh_anh,
            gia_ve: place.gia_ve || 0,
            gio_bat_dau: fmt(startMin),
            gio_ket_thuc: fmt(startMin + 60),
            thoi_luong_phut: 60,
            thu_tu_tham_quan: thuTu,
            vi_do: place.vi_do,
            kinh_do: place.kinh_do,
            travel_tips: '',
            ghi_chu: '',
          };

          this.lichTrinhTheoNgay[dayIdx].push(newItem);
          this.rawPlaces.push(newItem);

          this.$nextTick(() => {
            this.renderMapForDay(dayIdx);
          });
        } else {
          this.$toast.error(json.message || 'Không thể thêm địa điểm.');
        }
      } catch (e) {
        const errMsg = e.response?.data?.message || 'Lỗi khi thêm địa điểm.';
        this.$toast.error(errMsg);
        console.error(e);
      } finally {
        this.addPlacePanel.addingId = null;
      }
    },


    async fetchMyGroups() {
      try {
        const [joinedRes, ownedRes] = await Promise.all([
          api.get('/client/nhom-du-lich/get-joined'),
          api.get('/client/nhom-du-lich/get-my-groups')
        ]);
        const jData = joinedRes.data;
        const oData = ownedRes.data;
        
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
            message: JSON.stringify({ type: 'itinerary', id: this.tripId, title: this.trip.ten_chuyen_di })
        };

        const { data: res } = await api.post('/nhom-chats', payload);
        
        if (res.status) {
          this.$toast.success('Gửi lịch trình thành công!');
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

    async exportExcel() {
      if (!this.trip) return;
      this.exportingExcel = true;

      try {
        // Đảm bảo SheetJS đã được tải
        if (!window.XLSX) {
          this.$toast.info('Vui lòng chờ vài giây rồi thử lại...');
          await new Promise(resolve => setTimeout(resolve, 1500));
          if (!window.XLSX) {
            this.$toast.error('Không thể tải thư viện xuất Excel. Vui lòng thử lại.');
            return;
          }
        }

        const XLSX = window.XLSX;
        const wb = XLSX.utils.book_new();

        // === Sheet 1: Thông tin chuyến đi ===
        const tripInfo = [
          ['THÔNG TIN CHUYẾN ĐI', ''],
          ['Tên chuyến đi', this.trip.ten_chuyen_di || ''],
          ['Ngày bắt đầu', this.formatDateFull(this.trip.ngay_bat_dau)],
          ['Thời gian', this.formatDuration(this.trip.so_ngay)],
          ['Số thành viên', this.trip.so_nguoi || 1],
          ['Ngân sách dự kiến', this.formatCurrency(this.trip.ngan_sach)],
          ['Tổng giá vé dự kiến', this.formatCurrency(this.tongGiaVe)],
          ['Tổng chi phí phát sinh', this.formatCurrency(this.tongChiPhiPhatSinh)],
          ['Tổng chi phí', this.formatCurrency(this.tongChiPhiDuKien)],
          ['Trạng thái', this.isFinalized ? 'Đã chốt' : 'Đang lên kế hoạch'],
        ];
        const ws1 = XLSX.utils.aoa_to_sheet(tripInfo);
        ws1['!cols'] = [{ wch: 28 }, { wch: 40 }];
        XLSX.utils.book_append_sheet(wb, ws1, 'Thông tin');

        // === Sheet 2: Lịch trình theo ngày ===
        const scheduleData = [['NGÀY', 'NGÀY/GIờ', 'THỜI GIAN BẮT ĐẦU', 'THỜI GIAN KẾT THÚC', 'THỜI LƯỢNG', 'ĐỊA ĐIỂM', 'ĐỊA CHỈ', 'GIÁ VÉ (VNĐ)', 'GHI CHÚ']];
        this.lichTrinhTheoNgay.forEach((day, di) => {
          if (day.length === 0) {
            scheduleData.push([`Ngày ${di + 1}`, this.formatDateDate(this.trip.ngay_bat_dau, di), '', '', '', 'Chưa có lịch trình', '', '', '']);
          } else {
            day.forEach((item, idx) => {
              scheduleData.push([
                idx === 0 ? `Ngày ${di + 1}` : '',
                idx === 0 ? this.formatDateDate(this.trip.ngay_bat_dau, di) : '',
                item.gio_bat_dau || item.gio || '',
                item.gio_ket_thuc || '',
                item.thoi_luong_phut ? `${item.thoi_luong_phut} phút` : '',
                item.ten_dia_diem || '',
                item.dia_chi || 'Đà Nẵng',
                Number(item.gia_ve) || 0,
                item.ghi_chu || '',
              ]);
            });
          }
        });
        const ws2 = XLSX.utils.aoa_to_sheet(scheduleData);
        ws2['!cols'] = [
          { wch: 10 }, { wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 12 },
          { wch: 30 }, { wch: 30 }, { wch: 16 }, { wch: 30 }
        ];
        XLSX.utils.book_append_sheet(wb, ws2, 'Lịch trình');

        // === Sheet 3: Chi phí phát sinh ===
        if (this.incurredExpenses.length > 0) {
          const expData = [['STT', 'NỘI DUNG', 'LOẠI CHI PHÍ', 'NGÀY CHI', 'SỐ TIỀN (VNĐ)', 'NGƯỜI THỰC HIỆN']];
          this.incurredExpenses.forEach((exp, i) => {
            expData.push([
              i + 1,
              exp.noi_dung || '',
              exp.loai_chi_phi || 'Khác',
              this.formatExpenseDate(exp.ngay_chi),
              Number(exp.tong_chi_phi) || 0,
              exp.nguoi_tra?.ten || 'Không rõ',
            ]);
          });
          expData.push(['', 'TỔNG CỘNG', '', '', this.tongChiPhiPhatSinh, '']);
          const ws3 = XLSX.utils.aoa_to_sheet(expData);
          ws3['!cols'] = [{ wch: 6 }, { wch: 30 }, { wch: 18 }, { wch: 14 }, { wch: 18 }, { wch: 22 }];
          XLSX.utils.book_append_sheet(wb, ws3, 'Chi phí phát sinh');
        }

        // Xuất file
        const fileName = `LichTrinh_${(this.trip.ten_chuyen_di || 'chuyen-di').replace(/\s+/g, '_')}.xlsx`;
        XLSX.writeFile(wb, fileName);
        this.$toast.success('Xuất Excel thành công!');

      } catch (err) {
        console.error('Lỗi xuất Excel:', err);
        this.$toast.error('Không thể xuất file Excel. Vui lòng thử lại.');
      } finally {
        this.exportingExcel = false;
      }
    },

    confettiStyle(n) {
      const colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa'];
      return {
        left: `${Math.random() * 100}%`,
        backgroundColor: colors[Math.floor(Math.random() * colors.length)],
        width: `${Math.random() * 8 + 4}px`,
        height: `${Math.random() * 8 + 4}px`,
        borderRadius: Math.random() > 0.5 ? '50%' : '2px',
        animationDelay: `${Math.random() * 0.4}s`
      };
    },
    skipRating() {
      this.showRatingModal = false;
    },
    async submitRating() {
      if (!this.selectedRating) return;
      this.submittingRating = true;
      try {
        await api.post('/client/danh-gia-he-thong', {
          muc_do_hai_long: this.selectedRating,
          dong_gop: this.ratingFeedback
        });
        this.$toast.success('Cảm ơn bạn đã để lại đánh giá!');
      } catch (err) {
        console.error(err);
        this.$toast.error('Lỗi khi gửi đánh giá');
      } finally {
        this.submittingRating = false;
        this.showRatingModal = false;
      }
    },

    // ─── Map Methods ───────────────────────────────────────────
    initMap() {
      if (this.mapInstance) {
        this.mapInstance.remove();
        this.mapInstance = null;
      }
      const checkAndInit = () => {
        const L = window.L;
        if (!L) {
            setTimeout(checkAndInit, 300);
            return;
        }
        const el = document.getElementById('trip-map');
        if (!el) {
            setTimeout(checkAndInit, 300);
            return;
        }
        this.mapInstance = L.map('trip-map').setView([16.0544, 108.2022], 13);
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
          attribution: '&copy; Google Maps',
          maxZoom: 19,
        }).addTo(this.mapInstance);
      };
      
      checkAndInit();
    },

    clearMapLayers() {
      if (!this.mapInstance) return;
      this.mapLayers.forEach(layer => this.mapInstance.removeLayer(layer));
      this.mapLayers = [];
      if (this.mapRouteLine) {
        this.mapInstance.removeLayer(this.mapRouteLine);
        this.mapRouteLine = null;
      }
    },

    async renderMapForDay(dayIndex) {
      const waitForMap = () => {
        const L = window.L;
        if (!L || !this.mapInstance) {
          setTimeout(waitForMap, 300);
          return;
        }

        this.clearMapLayers();
        this.routeInfo = { distance: '', duration: '' };

        const dayItems = this.lichTrinhTheoNgay[dayIndex] || [];
        const validItems = dayItems.filter(item => item.vi_do && item.kinh_do);

        if (validItems.length === 0) return;

        const bounds = [];
        validItems.forEach((item, idx) => {
          const lat = parseFloat(item.vi_do);
          const lng = parseFloat(item.kinh_do);
          if (isNaN(lat) || isNaN(lng)) return;

          bounds.push([lat, lng]);

          // Màu: đầu=xanh lá, giữa=xanh dương, cuối=đỏ
          const colors = ['#10b981', '#0ea5e9', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#14b8a6', '#6366f1'];
          const bgColor = idx === 0 ? '#10b981' : idx === validItems.length - 1 ? '#ef4444' : colors[idx % colors.length];

          const icon = L.divIcon({
            className: '',
            html: `<div style="
              background: ${bgColor}; color: #fff; border-radius: 50%;
              width: 34px; height: 34px; display: flex; align-items: center;
              justify-content: center; font-weight: 800; font-size: 14px;
              border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
              position: relative; top: -17px; left: -17px;
            ">${idx + 1}</div>`,
            iconSize: [0, 0],
            iconAnchor: [0, 0],
          });

          const marker = L.marker([lat, lng], { icon })
            .bindPopup(`
              <div style="min-width:190px; font-family: 'Inter', sans-serif;">
                <strong style="font-size:15px; color: #1e293b;">${item.ten_dia_diem}</strong><br>
                <small style="color:#64748b"><i class="bi bi-geo-alt me-1"></i>${item.dia_chi || 'Đà Nẵng'}</small><br>
                 <div class="mt-2 text-primary fw-bold"><i class="bi bi-clock me-1"></i>${item.gio_bat_dau || item.gio || ''}</div>
              </div>
            `, { maxWidth: 230 })
            .addTo(this.mapInstance);

          this.mapLayers.push(marker);
        });

        if (bounds.length > 0) {
          this.mapInstance.fitBounds(bounds, { padding: [50, 50] });
        }
        
        // Vẽ tuyến đường thực tế qua OSRM routing API
        if (bounds.length > 1) {
          const coordinates = bounds.map(([lat, lng]) => `${lng},${lat}`).join(';');
          const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson`;
          
          axiosExternal.get(osrmUrl).then(routeRes => {
            const routeData = routeRes.data;
            if (routeData.routes && routeData.routes[0]) {
              const route = routeData.routes[0];
              const routeCoords = route.geometry.coordinates.map(([lng, lat]) => [lat, lng]);
              
              // Hiển thị thông tin tuyến đường
              const distKm = (route.distance / 1000).toFixed(1);
              const durMin = Math.round(route.duration / 60);
              this.routeInfo = {
                distance: `${distKm} km`,
                duration: durMin >= 60 ? `${Math.floor(durMin/60)}h ${durMin%60}p` : `${durMin} phút`
              };

              const routeLine = L.polyline(routeCoords, {
                color: '#0ea5e9',
                weight: 5,
                opacity: 0.9,
                lineJoin: 'round',
                lineCap: 'round',
              }).addTo(this.mapInstance);
              this.mapLayers.push(routeLine);
            } else {
              // Fallback
              const fallbackLine = L.polyline(bounds, { color: '#0ea5e9', weight: 4, opacity: 0.7, dashArray: '10, 8' }).addTo(this.mapInstance);
              this.mapLayers.push(fallbackLine);
            }
          }).catch(() => {
            const fallbackLine = L.polyline(bounds, { color: '#0ea5e9', weight: 4, opacity: 0.7, dashArray: '10, 8' }).addTo(this.mapInstance);
            this.mapLayers.push(fallbackLine);
          });
        }
      };

      if (!this.mapInstance) {
        this.initMap();
      }
      waitForMap();
    },

    initSortable() {
      // Allow drag for members/owners as long as not finalized
      const canEdit = !this.isFinalized && (this.trip.is_member || this.trip.is_owner);
      if (!canEdit) return;
      const el = document.querySelector('.timeline');
      if (!el) return;
      if (!window.Sortable) {
        // Sortable not yet loaded, retry after a delay
        setTimeout(() => this.initSortable(), 300);
        return;
      }

      if (this.sortableInstance) this.sortableInstance.destroy();

      this.sortableInstance = new window.Sortable(el, {
        animation: 150,
        handle: '.timeline-card',
        ghostClass: 'sortable-ghost',
        onEnd: async (evt) => {
          if (evt.oldIndex === evt.newIndex) return;

          const dayIdx = this.activeDayTab - 1;
          const dayItems = [...this.lichTrinhTheoNgay[dayIdx]];
          const movedItem = dayItems.splice(evt.oldIndex, 1)[0];
          dayItems.splice(evt.newIndex, 0, movedItem);

          // Tính lại giờ dựa trên Haversine (giống bên Tạo lịch trình)
          const haversine = (lat1, lon1, lat2, lon2) => {
            if (!lat1 || !lon1 || !lat2 || !lon2) return 5;
            const R = 6371, dL = (lat2 - lat1) * Math.PI / 180, dN = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dL/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dN/2)**2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
          };
          const fmt = m => `${String(Math.floor(m/60)).padStart(2,'0')}:${String(m%60).padStart(2,'0')}`;

          let curMin = 6*60+30; // Mặc định bắt đầu từ 6:30 sáng
          let prevLat = null, prevLng = null;
          
          dayItems.forEach((it, idx) => {
            const dur = it.thoi_luong_phut || 60;
            let travelMin = 12;
            if (prevLat !== null && it.vi_do && it.kinh_do) {
              const dist = haversine(prevLat, prevLng, parseFloat(it.vi_do), parseFloat(it.kinh_do));
              travelMin = Math.max(5, Math.min(40, Math.ceil(dist / 20 * 60) + 5));
            }
            const startMin = curMin + (prevLat !== null ? travelMin : 0);
            const endMin = startMin + dur;
            
            it.gio_bat_dau = fmt(startMin);
            it.gio_ket_thuc = fmt(endMin);
            it.thu_tu_tham_quan = dayIdx * 100 + idx + 1;
            
            curMin = endMin;
            prevLat = parseFloat(it.vi_do) || prevLat;
            prevLng = parseFloat(it.kinh_do) || prevLng;
          });

          this.lichTrinhTheoNgay.splice(dayIdx, 1, dayItems);
          this.$nextTick(() => { this.renderMapForDay(dayIdx); });
          
          // Save to DB
          const apiPayload = dayItems.map(it => ({
              id: it.id,
              thu_tu: it.thu_tu_tham_quan,
              gio_bat_dau: it.gio_bat_dau,
              gio_ket_thuc: it.gio_ket_thuc
          }));

          try {
              this.$toast.info('Đang cập nhật thứ tự...');
              await api.post('/lich-trinh-dia-diems/reorder', { items: apiPayload });
              
              // AI tối ưu lại sau khi di chuyển
              this.$toast.info('AI đang tối ưu lại lịch trình chuyên sâu...');
              this.reorderWithAi(); 
              
          } catch(err) {
              this.$toast.error('Lỗi khi lưu lịch trình');
          }
        }
      });
    },

    waitAndInitSortable() {
      // Poll until Sortable.js is available AND .timeline is in DOM
      const attempt = (tries = 0) => {
        const el = document.querySelector('.timeline');
        if (window.Sortable && el) {
          this.initSortable();
        } else if (tries < 30) {
          setTimeout(() => attempt(tries + 1), 200);
        }
      };
      attempt();
    },
  },
};
</script>

<style scoped>
.tlt-page {
  min-height: 100vh;
  background: #f0f7ff;
  font-family: 'Inter', sans-serif;
  color: #1e2d44;
  padding-bottom: 5rem;
}

.text-brand { color: #10b981 !important; }
.bg-brand { background-color: #10b981 !important; }

.animate-in {
  animation: fadeUp 0.45s ease both;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(22px); }
  to { opacity: 1; transform: translateY(0); }
}

.share-btn:hover { background: linear-gradient(135deg, #0ea5e9, #10b981) !important; transform: translateY(-2px); transition: 0.3s; }

/* Budget Tracker */
.budget-tracker {
  background: #fff;
  border-radius: 1.2rem;
  padding: 1rem 1.4rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
  border: 1px solid #e8edf5;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 14px rgba(30, 45, 68, 0.06);
}
.budget-info { display: flex; flex-direction: column; font-size: 0.85rem; }
.budget-info span { color: #7a8ea0; }
.budget-info strong { font-size: 1.1rem; color: #1e2d44; font-weight: 800; }
.budget-info strong.over { color: #f43f5e; }
.budget-info strong.ok { color: #10b981; }
.budget-bar-wrap { flex: 1; min-width: 120px; height: 8px; background: #e8edf5; border-radius: 999px; overflow: hidden; }
.budget-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #0ea5e9); border-radius: 999px; transition: width 0.4s ease; }
.budget-bar-fill.over { background: linear-gradient(90deg, #f43f5e, #fb923c); }

/* Day tabs */
.day-tabs {
  display: flex; gap: 0.55rem; flex-wrap: wrap; margin-bottom: 1.5rem; border: none; padding-bottom: 0;
}
.day-tab {
  display: flex; flex-direction: column; align-items: center; padding: 0.55rem 1.2rem;
  border-radius: 0.9rem; border: 1.5px solid #dbe3f0; background: #fff;
  cursor: pointer; transition: all 0.2s; min-width: 80px;
}
.day-tab.active { background: linear-gradient(135deg, #10b981, #0ea5e9); border-color: transparent; color: #fff; box-shadow: 0 6px 16px rgba(16, 185, 129, 0.28); }
.day-tab-num { font-weight: 700; font-size: 0.88rem; }
.day-tab.active .day-tab-cost { color: #fff !important; }
.day-tab-date { font-size: 0.75rem; opacity: 0.8; }

/* Timeline area */
.step3-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
@media(max-width: 900px) { .step3-layout { grid-template-columns: 1fr; } }

.step3-left { padding-right: 1rem; }
.timeline { display: flex; flex-direction: column; gap: 0; }
.timeline-item { display: flex; gap: 1rem; }
.timeline-time { width: 72px; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 0.2rem; gap: 0.2rem; }
.time-badge { background: linear-gradient(135deg, #10b981, #0ea5e9); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.45rem; border-radius: 0.55rem; white-space: nowrap; }
.time-end-badge { background: #e0f2fe; color: #0369a1; font-size: 0.65rem; font-weight: 600; padding: 0.18rem 0.4rem; border-radius: 0.45rem; white-space: nowrap; }
.duration-badge { background: #fef9c3; color: #854d0e; font-size: 0.62rem; font-weight: 700; padding: 0.15rem 0.38rem; border-radius: 0.45rem; white-space: nowrap; }
.timeline-line { flex: 1; width: 3px; background: linear-gradient(to bottom, #10b981, #0ea5e9, transparent); margin: 0.3rem 0; min-height: 40px; border-radius: 99px; opacity: 0.4; }

.timeline-card { flex: 1; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 1.25rem; border: 1px solid rgba(255, 255, 255, 0.5); padding: 1.2rem; margin-bottom: 1.2rem; box-shadow: 0 10px 30px rgba(30, 45, 68, 0.08); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.timeline-card:hover { transform: translateX(8px); box-shadow: 0 15px 40px rgba(30, 45, 68, 0.12); border-color: #10b981; }

.tc-header { display: flex; gap: 0.9rem; align-items: flex-start; }
.tc-img-wrap { flex-shrink: 0; }
.tc-img { width: 70px; height: 60px; object-fit: cover; border-radius: 0.7rem; display: block; }
.tc-meta { flex: 1; }
.tc-meta h5 { font-size: 0.93rem; font-weight: 700; margin-bottom: 0.2rem; color: #1e2d44; }
.tc-meta p { font-size: 0.8rem; color: #7a8ea0; margin-bottom: 0.15rem; }

/* Map Area */
.map-panel {
  background: #fff;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(30, 45, 68, 0.08);
  border: 1px solid rgba(15, 23, 42, 0.05);
  position: sticky;
  top: 90px;
}
.map-panel-head {
  padding: 1.2rem 1.5rem;
  background: linear-gradient(135deg, #1e293b, #0f172a);
  color: #fff;
  font-weight: 700;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
}

/* Modal chia sẻ */
.schedule-confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 1080;
  background: rgba(15, 23, 42, 0.62);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
.schedule-confirm-box {
  width: 100%;
  max-width: 460px;
  background: #fff;
  border-radius: 1.5rem;
  padding: 2rem 1.75rem 1.5rem;
  position: relative;
  box-shadow: 0 28px 70px rgba(15, 23, 42, 0.28);
  animation: modalBounceIn 0.35s ease both;
}
.schedule-confirm-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 1rem;
  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
}
.schedule-confirm-icon.danger {
  color: #b91c1c;
  background: linear-gradient(135deg, #fee2e2, #fecaca);
}
.schedule-confirm-icon.warning {
  color: #a16207;
  background: linear-gradient(135deg, #fef3c7, #fde68a);
}
.schedule-confirm-title {
  margin-bottom: 0.65rem;
  text-align: center;
  font-size: 1.35rem;
  font-weight: 800;
  color: #0f172a;
}
.schedule-confirm-message {
  margin: 0;
  text-align: center;
  color: #475569;
  font-size: 0.97rem;
  line-height: 1.6;
}
.schedule-confirm-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}
.schedule-confirm-cancel,
.schedule-confirm-submit {
  border: none;
  border-radius: 999px;
  padding: 0.78rem 1.2rem;
  font-weight: 700;
  font-size: 0.93rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.schedule-confirm-cancel {
  background: #f1f5f9;
  color: #334155;
}
.schedule-confirm-submit {
  color: #fff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
}
.schedule-confirm-submit.danger {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}
.schedule-confirm-submit.warning {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}
.schedule-confirm-cancel:hover,
.schedule-confirm-submit:hover {
  transform: translateY(-1px);
}

.share-modal-overlay {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 1050; backdrop-filter: blur(8px);
}
.share-icon-wrap {
  width: 76px; height: 76px; border-radius: 50%;
  background: #10b981;
  display: flex; align-items: center; justify-content: center;
}
.modal-select:focus {
  border-color: #4f46e5 !important;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
  background-color: #fff !important;
}

/* ══════════════════════════════════════════
   Rating Modal Styles
   ══════════════════════════════════════════ */
.rating-modal-overlay {
  position: fixed; inset: 0; background: rgba(10, 20, 40, 0.65); backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;
}
.rating-modal-box {
  background: #fff; border-radius: 2rem; padding: 2.5rem 2.2rem 2rem; max-width: 540px; width: 100%;
  position: relative; overflow: hidden; box-shadow: 0 32px 80px rgba(10, 20, 60, 0.28);
  animation: modalBounceIn 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
@keyframes modalBounceIn {
  from { opacity: 0; transform: scale(0.82) translateY(30px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.confetti-wrap { position: absolute; top: -6px; left: 0; width: 100%; height: 60px; overflow: hidden; pointer-events: none; }
.confetti-dot { position: absolute; top: 0; opacity: 0; animation: confettiFall 1.4s ease-out forwards; }
@keyframes confettiFall {
  0% { opacity: 0; transform: translateY(-10px) rotate(0deg); }
  20% { opacity: 1; }
  100% { opacity: 0; transform: translateY(72px) rotate(180deg); }
}
.rm-header { text-align: center; margin-bottom: 1.8rem; }
.rm-success-icon { font-size: 3rem; color: #10b981; margin-bottom: 0.6rem; animation: bounceScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both; }
@keyframes bounceScale { from { transform: scale(0); } to { transform: scale(1); } }
.rm-title { font-size: 1.55rem; font-weight: 800; color: #1e2d44; margin-bottom: 0.35rem; }
.rm-subtitle { font-size: 0.97rem; color: #627289; margin: 0; }
.rm-faces { display: flex; justify-content: center; gap: 0.6rem; margin-bottom: 1rem; flex-wrap: wrap; }
.rm-face-btn { display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.7rem 0.9rem; border: 2.5px solid #e2e8f0; border-radius: 1.1rem; background: #f8faff; cursor: pointer; transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1); min-width: 72px; }
.rm-face-btn:hover { border-color: #10b981; background: #f0fdf8; transform: translateY(-4px) scale(1.07); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.18); }
.rm-face-btn.selected { border-color: #10b981; background: linear-gradient(135deg, #d1fae5, #e0f2fe); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.25); transform: translateY(-4px) scale(1.1); }
.rm-face-icon { font-size: 2rem; line-height: 1; transition: transform 0.2s; }
.rm-face-btn:hover .rm-face-icon, .rm-face-btn.selected .rm-face-icon { transform: scale(1.18); }
.rm-face-label { font-size: 0.72rem; font-weight: 600; color: #5a7080; white-space: nowrap; }
.rm-face-btn.selected .rm-face-label { color: #065f46; }
.rm-selected-text { text-align: center; font-size: 0.9rem; font-weight: 500; color: #065f46; background: #d1fae5; border-radius: 0.7rem; padding: 0.55rem 1rem; margin: 0 0 1.2rem; animation: fadeUp 0.3s ease both; }
.rm-feedback-wrap { margin-bottom: 1.5rem; }
.rm-feedback-label { display: block; font-size: 0.87rem; font-weight: 600; color: #3d5166; margin-bottom: 0.4rem; }
.rm-textarea { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #dbe3f0; border-radius: 0.9rem; font-size: 0.95rem; font-family: inherit; color: #1e2d44; background: #f8fbff; resize: vertical; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
.rm-textarea:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12); }
.rm-actions { display: flex; gap: 0.8rem; justify-content: flex-end; }
.rm-btn-skip { padding: 0.65rem 1.4rem; border: 1.5px solid #dbe3f0; border-radius: 0.9rem; background: #fff; color: #627289; font-size: 0.92rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.rm-btn-skip:hover { background: #f0f4f8; border-color: #b0bec9; }
.rm-btn-submit { padding: 0.65rem 1.6rem; border: none; border-radius: 0.9rem; background: linear-gradient(135deg, #10b981, #0ea5e9); color: #fff; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: all 0.22s; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3); }
.rm-btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(16, 185, 129, 0.38); }
.rm-btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
.rating-modal-enter-active, .rating-modal-leave-active { transition: opacity 0.3s ease; }
.rating-modal-enter-from, .rating-modal-leave-to { opacity: 0; }
.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.3s ease; }
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateY(-8px); }

/* ─── Add Place Panel ─── */
.add-place-section { margin-top: 1.25rem; }
.add-place-panel {
  border: 1px solid #e0f2fe !important;
  animation: fadeInDown 0.25s ease;
}
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}
.add-place-list {
  max-height: 340px;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.add-place-list::-webkit-scrollbar { width: 5px; }
.add-place-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.add-place-item:hover:not(.already-added) {
  border-color: #10b981 !important;
  background: #f0fdf4 !important;
  transform: translateX(2px);
}
.add-place-item.already-added {
  opacity: 0.55;
  cursor: default !important;
}

/* ─── Trash button animation ─── */
.btn-outline-danger:hover {
  animation: shake 0.35s ease;
}
@keyframes shake {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-12deg); }
  75% { transform: rotate(12deg); }
}

</style>

