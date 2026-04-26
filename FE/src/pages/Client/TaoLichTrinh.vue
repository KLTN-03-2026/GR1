<template>
  <div class="tlt-page">
    <!-- ───── Header wizard bar ───── -->
    <div class="tlt-wizard-bar">
      <div class="container">
        <div class="wizard-steps">
          <div v-for="(s, i) in steps" :key="i" class="wizard-step"
            :class="{ active: step === i + 1, done: step > i + 1 }">
            <div class="step-icon">
              <i v-if="step > i + 1" class="bi bi-check-lg"></i>
              <span v-else>{{ i + 1 }}</span>
            </div>
            <span class="step-label">{{ s }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="container tlt-body">

      <!-- ══════════════════════════════
           BƯỚC 1 – Thông tin chuyến đi
           ══════════════════════════════ -->
      <div v-if="step === 1" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-calendar-heart me-2 text-brand"></i>Thông tin chuyến đi</h2>
          <p>Nhập các thông tin cơ bản để chúng tôi gợi ý lịch trình phù hợp nhất cho bạn.</p>
        </div>

        <div class="form-grid">
          <div class="form-group full-col">
            <label>Tên chuyến đi <span class="req">*</span></label>
            <input v-model="form.ten_chuyen_di" type="text" class="tlt-input"
              placeholder="Ví dụ: Đà Nẵng cùng gia đình 3N2Đ" />
            <span v-if="errors.ten_chuyen_di" class="err-msg">{{ errors.ten_chuyen_di }}</span>
          </div>

          <div class="form-group">
            <label>Ngày bắt đầu <span class="req">*</span></label>
            <input v-model="form.ngay_bat_dau" type="date" class="tlt-input" :min="today" />
            <span v-if="errors.ngay_bat_dau" class="err-msg">{{ errors.ngay_bat_dau }}</span>
          </div>

          <div class="form-group">
            <label>Ngày kết thúc <span class="req">*</span></label>
            <input v-model="form.ngay_ket_thuc" type="date" class="tlt-input" :min="form.ngay_bat_dau || today" />
            <span v-if="errors.ngay_ket_thuc" class="err-msg">{{ errors.ngay_ket_thuc }}</span>
          </div>

          <div class="form-group">
            <label>Số thành viên</label>
            <div class="number-input">
              <button type="button"
                @click="form.so_luong_thanh_vien = Math.max(1, form.so_luong_thanh_vien - 1)">−</button>
              <span>{{ form.so_luong_thanh_vien }}</span>
              <button type="button" @click="form.so_luong_thanh_vien++">+</button>
            </div>
          </div>

          <div class="form-group">
            <label>Ngân sách dự kiến (VNĐ)</label>
            <input v-model.number="form.ngan_sach_du_kien" type="number" class="tlt-input" placeholder="0" min="0" />
          </div>

          <div class="form-group full-col">
            <label>Sở thích & Yêu cầu đặc biệt</label>
            <div class="d-flex flex-wrap gap-2 mb-2">
              <span v-for="tag in noteSuggestionTags" :key="tag" 
                    @click="toggleNoteTag(tag)" 
                    class="note-tag badge rounded-pill px-3 py-2"
                    :class="{ 'selected': selectedNoteTags.includes(tag) }">
                {{ tag }}
              </span>
            </div>
            <textarea v-model="chu_thich_custom" class="tlt-input" rows="2"
              placeholder="Hoặc nhập yêu cầu khác của bạn tại đây..."></textarea>
          </div>
        </div>

        <!-- Trip summary chip -->
        <div v-if="soNgay > 0" class="trip-chip">
          <i class="bi bi-sun me-2"></i>
          <strong>{{ soNgay }} ngày {{ soNgay - 1 }} đêm</strong> &nbsp;·&nbsp;
          {{ form.so_luong_thanh_vien }} người
          <span v-if="form.ngan_sach_du_kien > 0">
            &nbsp;·&nbsp; Ngân sách ~{{ formatCurrency(form.ngan_sach_du_kien) }}
          </span>
        </div>

        <!-- Weather Widget Removed per User Request -->
        <div class="tlt-actions d-flex flex-column gap-3">
          <button class="btn-brand-lg w-100" @click="goStep2">
            Tiếp tục – Chọn địa điểm tự túc <i class="bi bi-arrow-right ms-2"></i>
          </button>
          <div class="ai-gen-wrapper">
            <button class="btn-outline-brand-lg w-100" @click="generateByAI" :disabled="loadingAI">
              <span v-if="loadingAI" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-magic me-2"></i> Tạo lịch trình thông minh
            </button>
            <div v-if="loadingAI" class="ai-progress-status mt-3 p-3 rounded"
              style="background: #f0f7ff; border: 1px solid #cce5ff;">
              <h6 class="mb-2" style="color: #004085;"><i class="bi bi-cpu-fill me-2"></i>Đang xử lý hệ thống lai...
              </h6>
              <ul class="list-unstyled mb-0" style="font-size: 0.9rem;">
                <li v-for="(log, i) in generationLog" :key="i" class="mb-1">
                  <i class="bi bi-check2-circle text-success me-2"></i>{{ log }}
                </li>
                <li v-if="aiStage === 3" class="text-primary animate-pulse">
                  <i class="bi bi-stars me-2"></i>Gemini AI đang hoàn thiện mô tả...
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════
           BƯỚC 2 – Chọn địa điểm
           ══════════════════════════════ -->
      <div v-if="step === 2" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-geo-alt me-2 text-brand"></i>Chọn địa điểm muốn ghé thăm</h2>
          <p>Thêm những nơi bạn muốn đến – hệ thống sẽ tự động sắp xếp lịch trình hợp lý.</p>
        </div>

        <!-- Category filter tabs -->
        <div class="filter-tabs">
          <button class="filter-tab" :class="{ active: filterCat === null }" @click="filterCat = null">Tất cả</button>
          <button v-for="cat in categories" :key="cat.id" class="filter-tab" :class="{ active: filterCat === cat.id }"
            @click="filterCat = cat.id">{{ cat.ten_danh_muc }}</button>
        </div>

        <!-- Search bar -->
        <div class="search-bar-wrap mb-4">
          <div class="search-bar">
            <i class="bi bi-search"></i>
            <input v-model="searchQ" type="text" placeholder="Tìm địa điểm..." />
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loadingDiaDiem" class="tlt-loading">
          <div class="spinner"></div>
          <span>Đang tải địa điểm...</span>
        </div>

        <!-- Grid -->
        <div v-else class="dd-grid">
          <div v-for="dd in filteredDiaDiem" :key="dd.id" class="dd-card" :class="{ selected: isSelected(dd.id) }">
            <div class="dd-img-wrap">
              <img :src="dd.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=80'"
                :alt="dd.ten_dia_diem" class="dd-img" />
              <div class="dd-badge" v-if="dd.gia_ve > 0">{{ formatCurrency(dd.gia_ve) }}</div>
              <button class="dd-select-btn" :class="{ remove: isSelected(dd.id) }" @click="toggleSelect(dd)">
                <i :class="isSelected(dd.id) ? 'bi bi-check-circle-fill' : 'bi bi-plus-circle'"></i>
                {{ isSelected(dd.id) ? 'Đã chọn' : 'Thêm' }}
              </button>
            </div>
            <div class="dd-info">
              <h5>{{ dd.ten_dia_diem }}</h5>
              <p class="dd-addr"><i class="bi bi-pin-map me-1"></i>{{ dd.dia_chi || 'Đà Nẵng' }}</p>
              <div class="dd-meta">
                <span v-if="dd.gio_mo_cua">
                  <i class="bi bi-clock me-1"></i>{{ dd.gio_mo_cua }} – {{ dd.gio_dong_cua || '21:00' }}
                </span>
                <span v-if="dd.danh_gia_trung_binh">
                  <i class="bi bi-star-fill text-warning me-1"></i>{{ Number(dd.danh_gia_trung_binh).toFixed(1) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!loadingDiaDiem && filteredDiaDiem.length === 0" class="tlt-empty">
          Không tìm thấy địa điểm phù hợp.
        </div>

        <!-- Bottom bar -->
        <div class="tlt-bottom-bar">
          <div class="tlt-selection-info">
            <span class="sel-count">{{ selectedDiaDiem.length }}</span> địa điểm đã chọn
          </div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 1"><i class="bi bi-arrow-left me-1"></i>Quay lại</button>
            <button class="btn-brand-lg" :disabled="selectedDiaDiem.length === 0" @click="goStep3">
              Tạo lịch trình <i class="bi bi-magic ms-2"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- BƯỚC 3 – Lịch trình tự động -->
      <div v-if="step === 3" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-calendar-week me-2 text-brand"></i>Lịch trình gợi ý</h2>
          <p>
            Hệ thống đã sắp xếp <strong>{{ tongSoDiaDiem }} địa điểm</strong> vào
            <strong>{{ lichTrinhTheoNgay.length }} ngày</strong>. Bạn có thể điều chỉnh thứ tự và thêm ghi chú.
          </p>
          <div class="alert alert-success d-inline-flex align-items-center py-2 px-3 mt-2 mb-0"
            v-if="form.ngan_sach_du_kien > 0" style="border-radius: 20px;">
            <i class="bi bi-piggy-bank me-2 fs-5"></i>
            <span>
              Chi phí dự kiến (<i class="bi bi-people-fill mx-1"></i>{{ form.so_luong_thanh_vien }} người):
              <strong>{{ formatCurrency(tongChiPhiDuKien) }}</strong> /
              Ngân sách: <strong>{{ formatCurrency(form.ngan_sach_du_kien) }}</strong>
              <span v-if="tongChiPhiDuKien <= form.ngan_sach_du_kien">✅</span>
              <span v-else class="text-danger">❌</span>
            </span>
          </div>
        </div>

        <!-- Budget tracker -->
        <div class="budget-tracker">
          <div class="budget-info">
            <span>Chi phí vé ước tính</span>
            <strong>{{ formatCurrency(tongGiaVe) }}</strong>
          </div>
          <div v-if="form.ngan_sach_du_kien > 0" class="budget-info">
            <span>Ngân sách</span>
            <strong :class="tongGiaVe > form.ngan_sach_du_kien ? 'over' : 'ok'">
              {{ formatCurrency(form.ngan_sach_du_kien) }}
            </strong>
          </div>
          <div v-if="form.ngan_sach_du_kien > 0" class="budget-bar-wrap">
            <div class="budget-bar-fill" :class="tongGiaVe > form.ngan_sach_du_kien ? 'over' : ''"
              :style="{ width: Math.min(100, (tongGiaVe / form.ngan_sach_du_kien) * 100) + '%' }"></div>
          </div>
        </div>

        <!-- Day tabs -->
        <div class="day-tabs">
          <button v-for="n in soNgay" :key="n" class="day-tab" :class="{ active: activeDayTab === n }"
            @click="activeDayTab = n">
            <span class="day-tab-num">Ngày {{ n }}</span>
            <span class="day-tab-date">{{ formatDate(form.ngay_bat_dau, n - 1) }}</span>
            <span class="day-tab-cost text-success fw-bold d-block mt-1" style="font-size: 0.8rem;">
              <i class="bi bi-cash-coin me-1"></i>{{ formatCurrency(chiPhiTheoNgay(n - 1)) }}
            </span>
          </button>
        </div>

        <!-- 2-col layout: Timeline + Map -->
        <div class="step3-layout">

          <!-- LEFT: Timeline -->
          <div class="step3-left">
            <div class="timeline"
              v-if="lichTrinhTheoNgay[activeDayTab - 1] && lichTrinhTheoNgay[activeDayTab - 1].length > 0">
              <div v-for="(item, idx) in lichTrinhTheoNgay[activeDayTab - 1]" :key="item.id_dia_diem + '-' + idx"
                class="timeline-item">
                <div class="timeline-time">
                  <span class="time-badge">{{ item.gio_bat_dau || item.gio }}</span>
                  <span v-if="item.gio_ket_thuc" class="time-end-badge">{{ item.gio_ket_thuc }}</span>
                  <span v-if="item.thoi_luong_phut" class="duration-badge">{{ item.thoi_luong_phut }}p</span>
                  <div class="timeline-line" v-if="idx < lichTrinhTheoNgay[activeDayTab - 1].length - 1"></div>
                </div>
                <div class="timeline-card">
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
                      <!-- Weather badge theo giờ -->
                      <div v-if="getWeatherAtTime(activeDayTab - 1, item.gio_bat_dau || item.gio)" class="weather-inline-badge">
                        <span class="wib-icon">{{ getWeatherAtTime(activeDayTab - 1, item.gio_bat_dau || item.gio).icon }}</span>
                        <span class="wib-temp">{{ getWeatherAtTime(activeDayTab - 1, item.gio_bat_dau || item.gio).temp }}°C</span>
                        <span class="wib-label">{{ getWeatherAtTime(activeDayTab - 1, item.gio_bat_dau || item.gio).label }}</span>
                      </div>
                    </div>
                    <div class="tc-order-btns">
                      <button class="btn-remove" @click="removeItem(activeDayTab - 1, idx)" title="Xóa">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </div>
                  </div>
                  <div class="tc-note">
                    <i class="bi bi-pencil-square me-1"></i>
                    <input v-model="item.ghi_chu" type="text" placeholder="Thêm ghi chú cho địa điểm này..."
                      class="note-input" />
                  </div>
                  <div v-if="item.travel_tips" class="tc-tips mt-2 p-2 rounded"
                    style="background: #fff8e1; border-left: 3px solid #ffc107;">
                    <small class="d-block mb-1 font-weight-bold" style="color: #856404;">
                      <i class="bi bi-lightbulb-fill me-1"></i>Mẹo từ chuyên gia AI:
                    </small>
                    <span style="font-size: 0.85rem; color: #555;">{{ item.travel_tips }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State for Day -->
            <div v-else class="empty-day-placeholder py-5 text-center">
              <div class="empty-icon-wrap mb-3">
                <i class="bi bi-calendar-x"></i>
              </div>
              <h5>Ngày này chưa có lịch trình</h5>
              <p class="text-muted">Quay lại Bước 2 để chọn thêm địa điểm hoặc sử dụng quy hoạch AI.</p>
              <button class="btn-brand-lg mt-2" @click="step = 2">
                <i class="bi bi-plus-circle me-1"></i> Thêm địa điểm
              </button>
            </div>
          </div>

          <!-- RIGHT: Map + Route Info -->
          <div class="step3-right">
            <div class="map-panel">
              <div class="map-panel-head">
                <i class="bi bi-map me-2"></i>Bản đồ vị trí ngày {{ activeDayTab }}
              </div>
              <div id="trip-map" style="height: 500px"></div>
            </div>
          </div>
        </div>

        <div class="tlt-bottom-bar">
          <div></div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 2"><i class="bi bi-arrow-left me-1"></i>Quay lại</button>
            <button class="btn btn-outline-primary fw-bold px-4" style="border-radius: 99px; height: 3.2rem" @click="openShareModal">
               <i class="bi bi-share-fill me-1"></i> Chia sẻ nhóm
            </button>
            <button class="btn-brand-lg" @click="step = 4">
              Xem tóm tắt &amp; Lưu <i class="bi bi-check2-circle ms-2"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════
           BƯỚC 4 – Xác nhận & Lưu
           ══════════════════════════════ -->
      <div v-if="step === 4" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-clipboard-check me-2 text-brand"></i>Xác nhận & Lưu lịch trình</h2>
          <p>Kiểm tra lại thông tin trước khi lưu.</p>
        </div>

        <div class="summary-grid">
          <div class="summary-card-big">
            <span class="sum-label">Tên chuyến đi</span>
            <h3>{{ form.ten_chuyen_di }}</h3>
            <div class="sum-chips">
              <span><i class="bi bi-calendar3 me-1"></i>{{ form.ngay_bat_dau }} → {{ form.ngay_ket_thuc }}</span>
              <span><i class="bi bi-people me-1"></i>{{ form.so_luong_thanh_vien }} người</span>
              <span><i class="bi bi-sun me-1"></i>{{ soNgay }} ngày {{ soNgay - 1 }} đêm</span>
            </div>
          </div>

          <div class="summary-stat">
            <i class="bi bi-geo-alt-fill"></i>
            <strong>{{ tongSoDiaDiem }}</strong>
            <span>địa điểm</span>
          </div>
          <div class="summary-stat">
            <i class="bi bi-ticket-perforated-fill"></i>
            <strong>{{ formatCurrency(tongGiaVe) }}</strong>
            <span>Chi phí vé ước tính</span>
          </div>
          <div class="summary-stat" v-if="form.ngan_sach_du_kien > 0">
            <i class="bi bi-wallet2"></i>
            <strong>{{ formatCurrency(form.ngan_sach_du_kien) }}</strong>
            <span>Ngân sách dự kiến</span>
          </div>
        </div>

        <!-- Day-by-day mini summary -->
        <div class="mini-days">
          <div v-for="(day, di) in lichTrinhTheoNgay" :key="di" class="mini-day">
            <div class="mini-day-head">
              <strong>Ngày {{ di + 1 }}</strong>
              <span>{{ formatDate(form.ngay_bat_dau, di) }}</span>
            </div>
            <ul>
              <li v-for="item in day" :key="item.id_dia_diem">
                <i class="bi bi-clock me-1"></i>{{ item.gio }} – {{ item.ten_dia_diem }}
              </li>
              <li v-if="day.length === 0" class="text-muted"><em>Không có địa điểm</em></li>
            </ul>
          </div>
        </div>

        <!-- Save error/success -->
        <div v-if="saveMsg" class="save-alert" :class="saveMsgType">
          <i :class="saveMsgType === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-circle-fill'"></i>
          {{ saveMsg }}
        </div>

        <div class="tlt-bottom-bar">
          <div></div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 3" :disabled="saving">
              <i class="bi bi-arrow-left me-1"></i>Quay lại
            </button>
            <button class="btn-brand-lg" @click="saveLichTrinh" :disabled="saving || saveSuccess">
              <span v-if="saving"><i class="bi bi-hourglass-split me-2"></i>Đang lưu...</span>
              <span v-else-if="saveSuccess"><i class="bi bi-check-lg me-2"></i>Đã lưu!</span>
              <span v-else><i class="bi bi-floppy2 me-2"></i>Lưu lịch trình</span>
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- ═══════════════════════════════════════════
         MODAL CHIA SẺ VÀO NHÓM
         ═══════════════════════════════════════════ -->
    <div v-if="showShareModal" class="rating-modal-overlay d-flex align-items-center justify-content-center" @click.self="showShareModal = false" style="z-index: 1000">
      <div class="bg-white p-4 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 440px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="showShareModal = false"></button>
        <div class="text-center mb-4 mt-2">
          <div class="mb-3 mx-auto shadow-sm" style="width:55px; height:55px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
             <i class="bi bi-send-check-fill fs-3 text-white"></i>
          </div>
          <h5 class="fw-bold mb-1 text-dark">Lưu & Chia sẻ lịch trình</h5>
          <p class="text-muted mb-0" style="font-size: 0.9rem;">Chọn nhóm để gửi chuyến đi này nhé!</p>
        </div>
        
        <div class="form-group mb-4">
          <label class="fw-bold mb-2 text-dark" style="font-size: 0.9rem;">Bạn muốn gửi vào nhóm nào?</label>
          <select v-model="form.id_nhom_du_lich" class="form-select border-2 shadow-none modal-select text-dark" style="padding: 0.8rem 1rem; border-radius: 12px; font-weight: 500; cursor: pointer; border-color: #e2e8f0; background-color: #f8fafc;">
            <option :value="null">-- Click để chọn nhóm --</option>
            <option v-for="g in myJoinedGroups" :key="g.id" :value="g">{{ g.ten_nhom }}</option>
          </select>
        </div>

        <button class="btn w-100 rounded-pill fw-bold text-white shadow-sm mt-2" @click="confirmShare" :disabled="!form.id_nhom_du_lich || saving" style="background: #10b981; padding: 0.8rem; font-size: 1rem; transition: all 0.2s;">
          <span v-if="saving" class="d-flex align-items-center justify-content-center"><span class="spinner-border spinner-border-sm me-2"></span> Đang lưu và gửi...</span>
          <span v-else class="d-flex align-items-center justify-content-center"><i class="bi bi-chat-heart-fill me-2 fs-5"></i> Chia sẻ ngay</span>
        </button>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════
         MODAL ĐÁNH GIÁ MỨC ĐỘ HÀI LÒNG
         ═══════════════════════════════════════════ -->
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

<script>
const BASE = 'http://localhost:8000/api';

export default {
  name: 'TaoLichTrinh',

  data() {
    return {
      step: 1,
      steps: ['Thông tin', 'Chọn địa điểm', 'Lịch trình', 'Xác nhận'],
      activeDayTab: 1,

      today: new Date().toISOString().split('T')[0],

      form: {
        ten_chuyen_di: '',
        ngay_bat_dau: '',
        ngay_ket_thuc: '',
        so_luong_thanh_vien: 2,
        ngan_sach_du_kien: 0,
        chu_thich: '',
        id_nhom_du_lich: null,
      },
      errors: {},

      myJoinedGroups: [],

      // ── Địa điểm ──
      allDiaDiem: [],
      loadingDiaDiem: false,
      selectedDiaDiem: [],
      filterCat: null,
      categories: [],
      searchQ: '',



      // ── Lịch trình ──
      lichTrinhTheoNgay: [], // Array<Array<item>>

      // ── Lưu ──
      saving: false,
      saveSuccess: false,
      saveMsg: '',
      saveMsgType: 'success',

      // ── Map ──
      mapInstance: null,
      mapLayers: [],

      loadingAI: false,
      aiStage: 0, // 0: Idle, 1: Filtering, 2: Scheduling, 3: AI Refinement
      generationLog: [],

      // ── Thời tiết ──
      weatherForecast: [],
      weatherHourly: {}, // { 'YYYY-MM-DD': { '09': { temp, icon, label }, ... } }
      loadingWeather: false,

      showShareModal: false,

      // ── Rating Modal ──
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

      // Tags gợi ý
      noteSuggestionTags: [
        'Âm nhạc', 'Streetfood', 'Quán ăn ngon', 'Thích không gian yên tĩnh', 
        'Đi cùng người cao tuổi', 'Có trẻ nhỏ', 'Ưu tiên tiết kiệm', 
        'Di chuyển bằng taxi', 'Thích chụp ảnh sống ảo', 'Đam mê hải sản', 
        'Khám phá văn hóa lịch sử', 'Du lịch nghỉ dưỡng'
      ],
      selectedNoteTags: [],
      chu_thich_custom: '',
    };
  },

  mounted() {
    // Load Leaflet CSS dynamically
    if (!document.getElementById('leaflet-css')) {
      const link = document.createElement('link');
      link.id = 'leaflet-css';
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }
    // Load Leaflet JS dynamically (if not already loaded)
    if (!window.L) {
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      document.head.appendChild(script);
    }
    // Load Sortable.js for Drag-and-Drop
    if (!window.Sortable) {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
      document.head.appendChild(script);
    }
    this.fetchMyGroups();
  },

  computed: {
    soNgay() {
      if (!this.form.ngay_bat_dau || !this.form.ngay_ket_thuc) return 0;
      const ms = new Date(this.form.ngay_ket_thuc) - new Date(this.form.ngay_bat_dau);
      return Math.max(1, Math.round(ms / 86400000) + 1);
    },

    filteredDiaDiem() {
      let list = this.allDiaDiem;
      if (this.filterCat !== null) {
        list = list.filter(d => d.id_danh_muc === this.filterCat);
      }
      if (this.searchQ.trim()) {
        const q = this.searchQ.toLowerCase();
        list = list.filter(
          d =>
            d.ten_dia_diem.toLowerCase().includes(q) ||
            (d.dia_chi && d.dia_chi.toLowerCase().includes(q))
        );
      }
      return list;
    },

    tongGiaVe() {
      return this.lichTrinhTheoNgay.flat().reduce((sum, item) => {
        return sum + (Number(item.gia_ve) || 0) * this.form.so_luong_thanh_vien;
      }, 0);
    },

    tongSoDiaDiem() {
      return this.lichTrinhTheoNgay.flat().length;
    },
    tongChiPhiDuKien() {
      return this.lichTrinhTheoNgay.flat().reduce((sum, item) => {
        return sum + (Number(item.gia_ve) || 0) * (this.form.so_luong_thanh_vien || 1);
      }, 0);
    }
  },

  watch: {
    selectedNoteTags: {
      handler() { this.updateChuThich(); },
      deep: true
    },
    chu_thich_custom() { this.updateChuThich(); },
    activeDayTab(newVal) {
      if (this.step === 3) {
        this.$nextTick(() => {
          this.renderMapForDay(newVal - 1);
          this.initSortable();
        });
      }
    },
    // Auto-fetch thời tiết khi user thay đổi ngày
    'form.ngay_bat_dau'() { this.autoLoadWeather(); },
    'form.ngay_ket_thuc'() { this.autoLoadWeather(); },
  },

  methods: {
    toggleNoteTag(tag) {
      if (this.selectedNoteTags.includes(tag)) {
        this.selectedNoteTags = this.selectedNoteTags.filter(t => t !== tag);
      } else {
        this.selectedNoteTags.push(tag);
      }
    },
    
    updateChuThich() {
      this.form.chu_thich = [...this.selectedNoteTags, this.chu_thich_custom].filter(Boolean).join('. ');
    },
    async fetchMyGroups() {
      const token = localStorage.getItem('client_token');
      if (!token) return;
      try {
        const [joinedRes, ownedRes] = await Promise.all([
          fetch(`${BASE}/client/nhom-du-lich/get-joined`, { headers: { Authorization: `Bearer ${token}` } }),
          fetch(`${BASE}/client/nhom-du-lich/get-my-groups`, { headers: { Authorization: `Bearer ${token}` } })
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
    // ─── Utilities ───────────────────────────────
    chiPhiTheoNgay(dayIndex) {
      if (!this.lichTrinhTheoNgay[dayIndex]) return 0;
      return this.lichTrinhTheoNgay[dayIndex].reduce((sum, item) => {
        return sum + (Number(item.gia_ve) || 0) * (this.form.so_luong_thanh_vien || 1);
      }, 0);
    },
    formatCurrency(val) {
      if (!val) return '0đ';
      return Number(val).toLocaleString('vi-VN') + 'đ';
    },

    formatWeatherDate(dateStr) {
      const d = new Date(dateStr);
      return d.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit' });
    },
    weatherIcon(condition) {
      const map = { sunny: '☀️', partly_cloudy: '⛅', rain: '🌧️', cloudy: '☁️' };
      return map[condition] || '🌤';
    },
    weatherLabel(condition) {
      const map = { sunny: 'Nắng đẹp', partly_cloudy: 'Ít mây', rain: 'Có mưa', cloudy: 'Nhiều mây' };
      return map[condition] || 'Không rõ';
    },
    weatherTip(condition, temp) {
      if (condition === 'rain') return 'Ưu tiên cafe, bảo tàng';
      if (condition === 'sunny' && temp >= 35) return 'Tránh biển trưa, mang kem chống nắng';
      if (condition === 'sunny') return 'Tuyệt vời cho biển & check-in';
      if (condition === 'partly_cloudy') return 'Thời tiết dễ chịu';
      return 'Mang áo khoác nhẹ';
    },

    async autoLoadWeather() {
      if (!this.form.ngay_bat_dau || !this.form.ngay_ket_thuc) return;
      if (this.form.ngay_ket_thuc < this.form.ngay_bat_dau) return;
      this.loadingWeather = true;
      const result = await this.fetchWeatherData();
      this.weatherForecast = result.daily;
      this.weatherHourly   = result.hourly;
      this.loadingWeather = false;
    },

    // Trả về thời tiết tại khung giờ cụ thể của ngày trong lịch trình
    getWeatherAtTime(dayIndex, timeStr) {
      if (!timeStr || !this.form.ngay_bat_dau) return null;
      const d = new Date(this.form.ngay_bat_dau);
      d.setDate(d.getDate() + dayIndex);
      const dateKey = d.toISOString().split('T')[0];
      const hourKey = (timeStr || '').substring(0, 2); // Lấy 2 chữ số giờ
      return this.weatherHourly?.[dateKey]?.[hourKey] || null;
    },

    formatDate(baseDate, addDays) {
      if (!baseDate) return '';
      const d = new Date(baseDate);
      d.setDate(d.getDate() + addDays);
      return d.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit' });
    },

    isSelected(id) {
      return this.selectedDiaDiem.some(d => d.id === id);
    },

    toggleSelect(dd) {
      const idx = this.selectedDiaDiem.findIndex(d => d.id === dd.id);
      if (idx === -1) {
        this.selectedDiaDiem.push(dd);
      } else {
        this.selectedDiaDiem.splice(idx, 1);
      }
    },



    // ─── Validation bước 1 ───────────────────────
    validateStep1() {
      this.errors = {};
      if (!this.form.ten_chuyen_di.trim()) this.errors.ten_chuyen_di = 'Vui lòng nhập tên chuyến đi.';
      if (!this.form.ngay_bat_dau) {
        this.errors.ngay_bat_dau = 'Vui lòng chọn ngày bắt đầu.';
      } else if (this.form.ngay_bat_dau < this.today) {
        this.errors.ngay_bat_dau = 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại.';
      }

      if (!this.form.ngay_ket_thuc) {
        this.errors.ngay_ket_thuc = 'Vui lòng chọn ngày kết thúc.';
      } else if (this.form.ngay_bat_dau && this.form.ngay_ket_thuc <= this.form.ngay_bat_dau) {
        this.errors.ngay_ket_thuc = 'Ngày kết thúc phải lớn hơn ngày bắt đầu.';
      }
      return Object.keys(this.errors).length === 0;
    },

    goStep2() {
      if (!this.validateStep1()) return;
      this.step = 2;
      if (this.allDiaDiem.length === 0) this.fetchDiaDiem();
    },

    // ─── Fetch địa điểm ─────────────────────────
    async fetchDiaDiem() {
      this.loadingDiaDiem = true;
      try {
        const res = await fetch(`${BASE}/dia-diems/`);
        const json = await res.json();

        this.allDiaDiem = (json.data || []).map(d => ({
          ...d,
          // Đảm bảo có id_danh_muc để lọc
          id_danh_muc: d.id_danh_muc || d.danh_muc || 'other',
          ten_danh_muc: d.loai_dia_diem || 'Khác'
        }));

        // Thu thập các danh mục duy nhất
        const catMap = {};
        this.allDiaDiem.forEach(d => {
          if (d.id_danh_muc && !catMap[d.id_danh_muc]) {
            catMap[d.id_danh_muc] = { id: d.id_danh_muc, ten_danh_muc: d.ten_danh_muc };
          }
        });
        this.categories = Object.values(catMap);
      } catch {
        this.allDiaDiem = [];
      } finally {
        this.loadingDiaDiem = false;
      }
    },

    // ─── Thuật toán phân bổ lịch trình ──────────
    buildSchedule() {
      const days = this.soNgay;
      if (days <= 0 || this.selectedDiaDiem.length === 0) {
        this.lichTrinhTheoNgay = Array.from({ length: days }, () => []);
        return;
      }

      // ── Hàm Haversine tính khoảng cách (km) ──
      const haversine = (lat1, lon1, lat2, lon2) => {
        if (!lat1 || !lon1 || !lat2 || !lon2) return 8;
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      };

      // ── Phân loại slot theo từng loại địa điểm ──
      const SLOT_CONFIGS = [
        { key: 'breakfast', time: '07:00', dur: 35,  label: 'Ăn sáng',         kw: ['bún', 'phở', 'mì', 'bánh mì', 'xôi', 'cháo', 'ăn sáng', 'quán ăn sáng'] },
        { key: 'cafe',      time: '08:00', dur: 45,  label: 'Cafe/Check-in',    kw: ['cafe', 'cà phê', 'check-in', 'sống ảo'] },
        { key: 'morning',   time: '09:30', dur: 100, label: 'Tham quan sáng',   kw: ['biển', 'chùa', 'bảo tàng', 'di tích', 'tâm linh', 'công viên', 'tham quan', 'vui chơi'] },
        { key: 'lunch',     time: '12:00', dur: 55,  label: 'Ăn trưa',          kw: ['nhà hàng', 'cơm', 'hải sản', 'quán ăn', 'đặc sản'] },
        { key: 'afternoon', time: '14:30', dur: 100, label: 'Tham quan chiều',  kw: ['biển', 'chùa', 'bảo tàng', 'di tích', 'công viên', 'tham quan', 'vui chơi'] },
        { key: 'snack',     time: '16:30', dur: 35,  label: 'Ăn vặt chiều',    kw: ['kem', 'chè', 'trà sữa', 'bánh ngọt', 'ăn vặt'] },
        { key: 'dinner',    time: '18:00', dur: 70,  label: 'Ăn tối',           kw: ['nhà hàng', 'hải sản', 'lẩu', 'nướng', 'bbq', 'ẩm thực'] },
        { key: 'pub',       time: '19:30', dur: 75,  label: 'Quán nhậu/Streetfood', kw: ['nhậu', 'bia', 'quán nhậu', 'streetfood', 'ăn vặt', 'hải sản vỉa hè'] },
        { key: 'night',     time: '21:00', dur: 60,  label: 'Dạo tối',          kw: ['cầu', 'chợ đêm', 'phố đi bộ', 'bar', 'pub', 'acoustic', 'giải trí đêm'] },
      ];

      const matchScore = (place, kws) => {
        const meta = ((place.ten_dia_diem || '') + ' ' + (place.loai_dia_diem || '') + ' ' + (place.mo_ta || '')).toLowerCase();
        return kws.some(k => meta.includes(k)) ? 1 : 0;
      };

      // ── Phân chia địa điểm vào slots theo loại ──
      const schedule = Array.from({ length: days }, () => []);
      const used = new Set();

      const centerDaNang = { vi_do: 16.0544, kinh_do: 108.2022 };

      for (let d = 0; d < days; d++) {
        let curLat = centerDaNang.vi_do;
        let curLng = centerDaNang.kinh_do;
        let curMin = 6 * 60 + 30; // 06:30

        for (const slot of SLOT_CONFIGS) {
          // Tìm địa điểm phù hợp nhất (chưa dùng, gần nhất, đúng loại)
          let best = null, bestScore = -Infinity;

          for (const dd of this.selectedDiaDiem) {
            if (used.has(dd.id)) continue;
            const dist = haversine(curLat, curLng, parseFloat(dd.vi_do) || curLat, parseFloat(dd.kinh_do) || curLng);
            const match = matchScore(dd, slot.kw);
            const sc = match * 50 - dist * 5;
            if (sc > bestScore) { bestScore = sc; best = dd; }
          }

          if (!best) continue;
          used.add(best.id);

          // Tính giờ bắt đầu thực tế (sau điểm trước + thời gian di chuyển)
          const dist = haversine(curLat, curLng, parseFloat(best.vi_do) || curLat, parseFloat(best.kinh_do) || curLng);
          const travelMin = Math.max(5, Math.min(40, Math.ceil(dist / 20 * 60) + 5));
          const slotMin   = slot.time.split(':').reduce((a, b, i) => i === 0 ? +b * 60 : a + +b, 0);
          const startMin  = Math.max(slotMin, curMin + travelMin);
          const endMin    = startMin + slot.dur;
          const fmt = m => `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;

          schedule[d].push({
            id_dia_diem:     best.id,
            ten_dia_diem:    best.ten_dia_diem,
            dia_chi:         best.dia_chi,
            hinh_anh:        best.hinh_anh,
            gia_ve:          best.gia_ve || 0,
            gio:             fmt(startMin),
            gio_bat_dau:     fmt(startMin),
            gio_ket_thuc:    fmt(endMin),
            thoi_luong_phut: slot.dur,
            ghi_chu:         '',
            vi_do:           best.vi_do || null,
            kinh_do:         best.kinh_do || null,
          });

          curLat = parseFloat(best.vi_do) || curLat;
          curLng = parseFloat(best.kinh_do) || curLng;
          curMin = endMin;
        }
      }

      // Địa điểm còn lại chưa được chọn → chia đều round-robin
      const remaining = this.selectedDiaDiem.filter(d => !used.has(d.id));
      remaining.forEach((dd, i) => {
        const dayIdx = i % days;
        const lastItem = schedule[dayIdx][schedule[dayIdx].length - 1];
        const lastEnd = lastItem ? lastItem.gio_ket_thuc.split(':').reduce((a, b, i) => i === 0 ? +b * 60 : a + +b, 0) + 20 : 8 * 60;
        const fmt = m => `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;
        schedule[dayIdx].push({
          id_dia_diem: dd.id, ten_dia_diem: dd.ten_dia_diem, dia_chi: dd.dia_chi,
          hinh_anh: dd.hinh_anh, gia_ve: dd.gia_ve || 0,
          gio: fmt(lastEnd), gio_bat_dau: fmt(lastEnd), gio_ket_thuc: fmt(lastEnd + 60), thoi_luong_phut: 60,
          ghi_chu: '', vi_do: dd.vi_do || null, kinh_do: dd.kinh_do || null,
        });
      });

      this.lichTrinhTheoNgay = schedule;
      this.activeDayTab = 1;
    },


    // ─── Fetch thời tiết từ Open-Meteo (miễn phí, không cần API key) ──────
    async fetchWeatherData() {
      if (!this.form.ngay_bat_dau || !this.form.ngay_ket_thuc) return { daily: [], hourly: {} };
      try {
        // Đà Nẵng: lat=16.0544, lon=108.2022
        const url = `https://api.open-meteo.com/v1/forecast?latitude=16.0544&longitude=108.2022`
          + `&daily=weathercode,temperature_2m_max,precipitation_sum`
          + `&hourly=temperature_2m,weathercode`
          + `&timezone=Asia%2FHo_Chi_Minh`
          + `&start_date=${this.form.ngay_bat_dau}&end_date=${this.form.ngay_ket_thuc}`;
        const res = await fetch(url);
        const json = await res.json();
        if (!json.daily) return { daily: [], hourly: {} };

        // WMO Weather Code → condition
        const getCondition = (code) => {
          if (code === 0 || code === 1) return 'sunny';
          if (code <= 3) return 'partly_cloudy';
          if (code <= 67) return 'rain';
          return 'cloudy';
        };

        // — Daily (cho weather widget) —
        const daily = json.daily.time.map((date, i) => ({
          date,
          condition: getCondition(json.daily.weathercode[i]),
          temp_max: json.daily.temperature_2m_max[i],
          precipitation: json.daily.precipitation_sum[i],
          weathercode: json.daily.weathercode[i],
        }));

        // — Hourly (cho badge trong card) —
        // Cấu trúc: { 'YYYY-MM-DD': { '06': { icon, temp, label }, '07': ... } }
        const hourly = {};
        if (json.hourly) {
          json.hourly.time.forEach((isoTime, i) => {
            const [dateStr, timeStr] = isoTime.split('T');
            const hourKey = timeStr.substring(0, 2);
            if (!hourly[dateStr]) hourly[dateStr] = {};
            const condition = getCondition(json.hourly.weathercode[i]);
            const iconMap  = { sunny: '☀️', partly_cloudy: '⛅', rain: '🌧️', cloudy: '☁️' };
            const labelMap = { sunny: 'Nắng', partly_cloudy: 'Ít mây', rain: 'Mưa', cloudy: 'Nhiều mây' };
            hourly[dateStr][hourKey] = {
              icon : iconMap[condition]  || '🌤',
              label: labelMap[condition] || '',
              temp : Math.round(json.hourly.temperature_2m[i]),
              condition,
            };
          });
        }

        return { daily, hourly };
      } catch (e) {
        console.warn('Không lấy được dữ liệu thời tiết:', e.message);
        return { daily: [], hourly: {} };
      }
    },

    async generateByAI() {
      if (!this.validateStep1()) return;

      const token = localStorage.getItem('client_token');
      if (!token) {
        this.$toast.warning('Vui lòng đăng nhập để sử dụng tính năng AI.');
        return;
      }

      this.loadingAI = true;
      this.aiStage = 1;
      this.generationLog = ['Đang khởi tạo thuật toán...'];

      try {
        // Lấy dữ liệu thời tiết (dùng lại nếu đã có, nếu không thì fetch mới)
        this.generationLog.push('🌤 Đang tải dự báo thời tiết Đà Nẵng...');
        const result = this.weatherForecast.length > 0
          ? { daily: this.weatherForecast, hourly: this.weatherHourly }
          : await this.fetchWeatherData();
        if (!this.weatherForecast.length) {
          this.weatherForecast = result.daily;
          this.weatherHourly   = result.hourly;
        }
        const weatherData = result.daily;
        if (weatherData.length > 0) {
          const summary = weatherData.map(w => `${w.date}: ${w.condition}, ${w.temp_max}°C`).join(' | ');
          this.generationLog.push(`✅ Thời tiết: ${summary}`);
        }

        // Stage 2
        await new Promise(r => setTimeout(r, 600));
        this.aiStage = 2;
        this.generationLog.push('Đang tối ưu quãng đường di chuyển (Haversine)...');

        const payload = {
          ...this.form,
          weather_data: weatherData,  // ← Gửi kèm thời tiết cho AI
        };

        const res = await fetch(`${BASE}/client/ai/generate-itinerary`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(payload)
        });

        if (res.status === 401) {
          localStorage.removeItem('client_token');
          localStorage.removeItem('client_user');
          this.$toast.error('Phiên đăng nhập đã hết hạn. V vui lòng đăng nhập lại.');
          this.loadingAI = false;
          this.aiStage = 0;
          return;
        }

        let json;
        try {
          json = await res.json();
        } catch (err) {
          throw new Error('Lỗi từ máy chủ: Không thể đọc dữ liệu trả về.');
        }

        if (json.status === 'success') {
          this.aiStage = 3;
          this.generationLog.push('AI đang thêm lời khuyên du lịch chuyên sâu...');
          await new Promise(r => setTimeout(r, 600));

          if (this.allDiaDiem.length === 0) await this.fetchDiaDiem();

          this.lichTrinhTheoNgay = json.data.map(day => {
            return day.map(item => {
              const original = this.allDiaDiem.find(d => d.id === item.id_dia_diem);
              return {
                ...item,
                ten_dia_diem: item.ten_dia_diem || original?.ten_dia_diem || 'Địa điểm không tên',
                dia_chi: item.dia_chi || original?.dia_chi || 'Đà Nẵng',
                hinh_anh: item.image || original?.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600',
                vi_do: item.vi_do || original?.vi_do || null,
                kinh_do: item.kinh_do || original?.kinh_do || null,
                gia_ve: item.gia_ve || original?.gia_ve || 0,
                ghi_chu: item.ghi_chu || '',
                travel_tips: item.travel_tips || (json.is_technical_only ? 'Nên mang theo nước và kem chống nắng.' : '')
              };
            });
          });

          this.step = 3;
          this.activeDayTab = 1;

          this.$nextTick(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
              this.initMap();
              this.renderMapForDay(0);
              this.initSortable();
            }, 500);
          });
        } else {
          this.$toast.error('Hệ thống gặp sự cố: ' + (json.message || 'Vui lòng thử lại sau.'));
        }
      } catch (e) {
        this.$toast.error('Lỗi kết nối máy chủ: ' + e.message);
      } finally {
        this.loadingAI = false;
        this.aiStage = 0;
      }
    },

    goStep3() {
      if (this.selectedDiaDiem.length === 0) return;
      this.buildSchedule();
      this.step = 3;
      this.$nextTick(() => {
        this.initMap();
        this.renderMapForDay(0);
        this.initSortable();
      });
    },

    initSortable() {
      const el = document.querySelector('.timeline');
      if (!el || !window.Sortable) return;

      // Cleanup previous instance if any
      if (this.sortableInstance) this.sortableInstance.destroy();

      this.sortableInstance = new window.Sortable(el, {
        animation: 150,
        handle: '.timeline-card',
        ghostClass: 'sortable-ghost',
        onEnd: (evt) => {
          const dayIdx = this.activeDayTab - 1;
          const dayItems = [...this.lichTrinhTheoNgay[dayIdx]];
          const item = dayItems.splice(evt.oldIndex, 1)[0];
          dayItems.splice(evt.newIndex, 0, item);

          // Tính lại giờ dựa trên Haversine
          const haversine = (lat1, lon1, lat2, lon2) => {
            if (!lat1 || !lon1 || !lat2 || !lon2) return 5;
            const R = 6371, dL = (lat2 - lat1) * Math.PI / 180, dN = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dL/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dN/2)**2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
          };
          const fmt = m => `${String(Math.floor(m/60)).padStart(2,'0')}:${String(m%60).padStart(2,'0')}`;

          let curMin = 6*60+30;
          let prevLat = null, prevLng = null;
          dayItems.forEach((it) => {
            const dur = it.thoi_luong_phut || 60;
            let travelMin = 12;
            if (prevLat !== null && it.vi_do && it.kinh_do) {
              const dist = haversine(prevLat, prevLng, parseFloat(it.vi_do), parseFloat(it.kinh_do));
              travelMin = Math.max(5, Math.min(40, Math.ceil(dist / 20 * 60) + 5));
            }
            const startMin = curMin + (prevLat !== null ? travelMin : 0);
            const endMin = startMin + dur;
            it.gio = fmt(startMin);
            it.gio_bat_dau = fmt(startMin);
            it.gio_ket_thuc = fmt(endMin);
            curMin = endMin;
            prevLat = parseFloat(it.vi_do) || prevLat;
            prevLng = parseFloat(it.kinh_do) || prevLng;
          });

          this.lichTrinhTheoNgay[dayIdx] = dayItems;
          this.$nextTick(() => { this.renderMapForDay(dayIdx); });
        }
      });
    },

    // ─── Xóa item trong lịch trình ──
    removeItem(dayIdx, itemIdx) {
      this.lichTrinhTheoNgay[dayIdx].splice(itemIdx, 1);
    },

    // ─── Lưu lịch trình ─────────────────────────
    async saveLichTrinh(skipRating = false) {
      const token = localStorage.getItem('client_token');
      if (!token) {
        this.saveMsg = 'Bạn cần đăng nhập để lưu lịch trình!';
        this.saveMsgType = 'error';
        return;
      }

      this.saving = true;
      this.saveMsg = '';

      try {
        // 1. Tạo chuyến đi
        const payload = { ...this.form, id_nhom_du_lich: this.form.id_nhom_du_lich?.id || null };
        const r1 = await fetch(`${BASE}/client/chuyen-di/create`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
          body: JSON.stringify(payload),
        });
        const j1 = await r1.json();
        if (!j1.status) throw new Error(j1.message || 'Lỗi tạo chuyến đi');

        const newTripId = j1.data?.id;
        if (!newTripId) throw new Error('Không lấy được ID chuyến đi mới.');

        // 3. Bulk create chi tiết
        const items = this.lichTrinhTheoNgay.flatMap((day, di) =>
          day.map((item, idx) => ({
            id_dia_diem:      item.id_dia_diem,
            thu_tu_tham_quan: di * 100 + idx + 1,
            gio_bat_dau:      item.gio_bat_dau || item.gio || null,
            gio_ket_thuc:     item.gio_ket_thuc     || null,
            thoi_luong_phut:  item.thoi_luong_phut  || null,
            ghi_chu: (item.ghi_chu || '') + (item.travel_tips ? `|AI_TIPS|${item.travel_tips}` : ''),
          }))
        );

        const r3 = await fetch(`${BASE}/client/chi-tiet-chuyen-di/bulk-create`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
          body: JSON.stringify({ id_chuyen_di: newTripId, items }),
        });
        const j3 = await r3.json();
        if (!j3.status) throw new Error(j3.message || 'Lỗi lưu chi tiết');

        this.saveSuccess = true;
        this.saveMsg = '🎉 Lịch trình đã được lưu thành công!';
        this.saveMsgType = 'success';

        if (this.form.id_nhom_du_lich) {
           await this.shareToGroup(newTripId);
        }

        if (skipRating) {
           this.$router.push('/nhom-du-lich');
        } else {
           // Show rating modal after short delay
           setTimeout(() => {
             this.showRatingModal = true;
           }, 800);
        }
      } catch (err) {
        this.saveMsg = err.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
        this.saveMsgType = 'error';
      } finally {
        this.saving = false;
      }
    },

    openShareModal() {
      this.showShareModal = true;
    },
    async confirmShare() {
      if (!this.form.id_nhom_du_lich) return;
      await this.saveLichTrinh(true);
      this.showShareModal = false;
    },
    async shareToGroup(tripId) {
       const group = this.form.id_nhom_du_lich;
       if (!group) return;
       try {
           const payload = {
               id_nhom_du_lich: group.id,
               id_chi_tiet_nhom: group.id_chi_tiet_nhom,
               message: JSON.stringify({ type: 'itinerary', id: tripId, title: this.form.ten_chuyen_di })
           };
           await fetch(`${BASE}/nhom-chats`, {
               method: 'POST', 
               headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${localStorage.getItem('client_token')}` },
               body: JSON.stringify(payload)
           });
       } catch (e) {
           console.error('Lỗi khi chia sẻ.', e);
       }
    },

    // ─── Map Methods ───────────────────────────────────────────
    initMap() {
      if (this.mapInstance) {
        this.mapInstance.remove();
        this.mapInstance = null;
      }
      const L = window.L;
      if (!L) return;
      this.mapInstance = L.map('trip-map').setView([16.0544, 108.2022], 13);
      L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Maps',
        maxZoom: 19,
      }).addTo(this.mapInstance);
    },

    clearMapLayers() {
      if (!this.mapInstance) return;
      this.mapLayers.forEach(layer => this.mapInstance.removeLayer(layer));
      this.mapLayers = [];
    },

    async renderMapForDay(dayIndex) {
      const L = window.L;
      if (!L) {
        // Leaflet may still be loading; retry after a short delay
        setTimeout(() => this.renderMapForDay(dayIndex), 500);
        return;
      }
      if (!this.mapInstance) this.initMap();
      if (!this.mapInstance) return;

      this.clearMapLayers();

      const dayItems = this.lichTrinhTheoNgay[dayIndex] || [];
      const validItems = dayItems.filter(item => item.vi_do && item.kinh_do);

      if (validItems.length === 0) return;

      // Create numbered markers for each location
      const bounds = [];
      validItems.forEach((item, idx) => {
        const lat = parseFloat(item.vi_do);
        const lng = parseFloat(item.kinh_do);
        if (isNaN(lat) || isNaN(lng)) return;

        bounds.push([lat, lng]);

        const icon = L.divIcon({
          className: '',
          html: `<div style="
            background: #5a67d8; color: #fff; border-radius: 50%;
            width: 32px; height: 32px; display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 14px;
            border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
          ">${idx + 1}</div>`,
          iconSize: [32, 32],
          iconAnchor: [16, 16],
        });

        const imgUrl = item.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&q=60';
        const marker = L.marker([lat, lng], { icon })
          .bindPopup(`
            <div style="min-width:180px">
              <img src="${imgUrl}" style="width:100%;height:90px;object-fit:cover;border-radius:6px;margin-bottom:6px">
              <strong style="font-size:14px">${item.ten_dia_diem}</strong><br>
              <small style="color:#666">${item.dia_chi || 'Đà Nẵng'}</small><br>
              <small>🕐 ${item.gio}</small>
            </div>
          `, { maxWidth: 220 })
          .addTo(this.mapInstance);

        this.mapLayers.push(marker);
      });

      if (bounds.length > 0) {
        this.mapInstance.fitBounds(bounds, { padding: [40, 40] });
      }
    },

    // ─── Rating Modal Methods ─────────────────────
    confettiStyle(n) {
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
    },

    async submitRating() {
      if (!this.selectedRating) return;
      this.submittingRating = true;

      try {
        const token = localStorage.getItem('client_token');
        // Attempt to save rating to backend (non-blocking – ignore errors gracefully)
        await fetch(`${BASE}/client/danh-gia-he-thong`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
          body: JSON.stringify({
            muc_do_hai_long: this.selectedRating,
            noi_dung: this.ratingFeedback,
          }),
        }).catch(() => { }); // Silently ignore if endpoint doesn't exist yet
      } finally {
        this.submittingRating = false;
        this.showRatingModal = false;
        this.$router.push('/lich-trinh-cua-toi');
      }
    },

    skipRating() {
      this.showRatingModal = false;
      this.$router.push('/lich-trinh-cua-toi');
    },
  },
};
</script>

<style scoped>
/* ──────────── Weather Widget ──────────── */
.weather-widget {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e0eaff;
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(14, 165, 233, 0.08);
}

.weather-header {
  display: flex;
  align-items: center;
  font-weight: 700;
  font-size: 0.95rem;
  color: #0369a1;
  margin-bottom: 1rem;
}

.weather-days {
  display: flex;
  gap: 0.75rem;
  overflow-x: auto;
  padding-bottom: 0.25rem;
}

.weather-day-card {
  min-width: 110px;
  border-radius: 14px;
  padding: 0.9rem 0.75rem;
  text-align: center;
  flex-shrink: 0;
  transition: transform 0.2s;
  cursor: default;
}
.weather-day-card:hover { transform: translateY(-3px); }

.weather-sunny    { background: linear-gradient(135deg, #fff7e6, #ffe4a0); border: 1px solid #fbbf24; }
.weather-partly_cloudy { background: linear-gradient(135deg, #f0f9ff, #bae6fd); border: 1px solid #7dd3fc; }
.weather-rain     { background: linear-gradient(135deg, #eff6ff, #bfdbfe); border: 1px solid #60a5fa; }
.weather-cloudy   { background: linear-gradient(135deg, #f8fafc, #e2e8f0); border: 1px solid #94a3b8; }

.weather-date  { font-size: 0.72rem; font-weight: 600; color: #64748b; margin-bottom: 0.4rem; }
.weather-icon  { font-size: 2rem; margin-bottom: 0.3rem; line-height: 1; }
.weather-temp  { font-size: 1.3rem; font-weight: 800; color: #1e2d44; }
.weather-label { font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem; color: #475569; }
.weather-tip   { font-size: 0.68rem; color: #64748b; margin-top: 0.4rem; line-height: 1.3; }

/* ──────────── Weather Inline Badge (trong card địa điểm) ──────────── */
.weather-inline-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  margin-top: 0.4rem;
  padding: 0.25rem 0.6rem;
  border-radius: 20px;
  background: rgba(14, 165, 233, 0.08);
  border: 1px solid rgba(14, 165, 233, 0.2);
  font-size: 0.78rem;
}
.wib-icon  { font-size: 1rem; line-height: 1; }
.wib-temp  { font-weight: 700; color: #0369a1; }
.wib-label { color: #64748b; font-size: 0.72rem; }

/* ──────────── Base ──────────── */
.tlt-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0f7ff 0%, #fafbff 60%, #f0fff8 100%);
  font-family: 'Inter', sans-serif;
  color: #1e2d44;
}

.tlt-body {
  padding: 2.5rem 1rem 6rem;
  max-width: 1040px;
}

/* ──────────── Wizard bar ──────────── */
.tlt-wizard-bar {
  background: #fff;
  border-bottom: 1px solid rgba(30, 45, 68, 0.07);
  padding: 1.1rem 0;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 4px 24px rgba(30, 45, 68, 0.06);
}

.wizard-steps {
  display: flex;
  gap: 0;
  align-items: center;
  justify-content: center;
}

.wizard-step {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0 1.5rem;
  position: relative;
  color: #a0adbf;
  transition: color 0.3s;
}

.wizard-step+.wizard-step::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 1px;
  height: 24px;
  background: #e2e8f0;
}

.wizard-step.active {
  color: #10b981;
}

.wizard-step.done {
  color: #22c55e;
}

.step-icon {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: #ecf0f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.3s;
}

.wizard-step.active .step-icon {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
}

.wizard-step.done .step-icon {
  background: #22c55e;
  color: #fff;
}

.step-label {
  font-size: 0.85rem;
  font-weight: 600;
  display: none;
}

@media (min-width: 576px) {
  .step-label {
    display: block;
  }
}

/* ──────────── animate-in ──────────── */
.animate-in {
  animation: fadeUp 0.45s ease both;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(22px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ──────────── Section head ──────────── */
.section-head {
  margin-bottom: 2rem;
}

.section-head h2 {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 0.4rem;
}

.section-head p {
  color: #627289;
  margin-bottom: 0;
  font-size: 0.97rem;
}

.text-brand {
  color: #10b981;
}

/* ──────────── Form bước 1 ──────────── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.full-col {
  grid-column: 1 / -1;
}

label {
  font-size: 0.87rem;
  font-weight: 600;
  color: #3d5166;
}

.req {
  color: #f43f5e;
}

.tlt-input {
  padding: 0.7rem 1rem;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.85rem;
  font-size: 0.96rem;
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  color: #1e2d44;
  width: 100%;
}

.tlt-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}

.err-msg {
  font-size: 0.8rem;
  color: #f43f5e;
}

.number-input {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.85rem;
  overflow: hidden;
  background: #fff;
}

.number-input button {
  width: 42px;
  height: 46px;
  background: #f5f8fe;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #3d5166;
  transition: background 0.18s;
}

.number-input button:hover {
  background: #e4edfc;
}

.number-input span {
  flex: 1;
  text-align: center;
  font-weight: 700;
  font-size: 1rem;
  color: #1e2d44;
}

.trip-chip {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(90deg, #dcfce7, #d1faf4);
  border: 1px solid #a7f3d0;
  border-radius: 999px;
  padding: 0.55rem 1.25rem;
  font-size: 0.9rem;
  color: #065f46;
  margin-top: 1.2rem;
}

/* ──────────── Actions ──────────── */
.tlt-actions {
  margin-top: 2rem;
}

.tlt-bottom-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e8edf5;
}

.tlt-actions-row {
  display: flex;
  gap: 0.9rem;
  align-items: center;
}

.btn-brand-lg {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.75rem 1.9rem;
  font-size: 0.97rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
}

.btn-brand-lg:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(16, 185, 129, 0.35);
}

.btn-brand-lg:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.btn-outline-brand-lg {
  background: white;
  border: 1.5px solid #10b981;
  color: #10b981;
  border-radius: 999px;
  padding: 0.75rem 1.9rem;
  font-size: 0.97rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline-brand-lg:hover:not(:disabled) {
  background: rgba(16, 185, 129, 0.05);
  border-color: #0ea5e9;
  color: #0ea5e9;
  transform: translateY(-2px);
}

.btn-ghost {
  background: transparent;
  color: #5a6d80;
  border: 1.5px solid #d5dde8;
  border-radius: 999px;
  padding: 0.72rem 1.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s;
}

.btn-ghost:hover:not(:disabled) {
  background: #f0f4fb;
}

.btn-ghost:disabled {
  opacity: 0.5;
}

/* ──────────── Filter tabs ──────────── */
.filter-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.2rem;
}

.filter-tab {
  padding: 0.42rem 1rem;
  border-radius: 999px;
  border: 1.5px solid #d5dde8;
  background: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  color: #5a6d80;
  cursor: pointer;
  transition: all 0.18s;
}

.filter-tab.active,
.filter-tab:hover {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

/* ──────────── Search ──────────── */
.search-bar-wrap {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.search-bar-wrap .search-bar {
  flex: 1;
  margin-bottom: 0 !important;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background: #fff;
  border: 1.5px solid #dbe3f0;
  border-radius: 999px;
  padding: 0.55rem 1.2rem;
}

.search-bar i {
  color: #a0adbf;
  font-size: 1rem;
}

.search-bar input {
  border: none;
  outline: none;
  font-size: 0.95rem;
  flex: 1;
  color: #1e2d44;
  background: transparent;
}

/* ──────────── DiaDiem grid ──────────── */
.dd-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.2rem;
  margin-bottom: 1rem;
}

.dd-card {
  background: #fff;
  border-radius: 1.2rem;
  border: 2px solid transparent;
  box-shadow: 0 6px 20px rgba(30, 45, 68, 0.07);
  overflow: hidden;
  transition: all 0.22s;
}

.dd-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(30, 45, 68, 0.12);
}

.dd-card.selected {
  border-color: #10b981;
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
}

.dd-img-wrap {
  position: relative;
  overflow: hidden;
}

.dd-img {
  width: 100%;
  height: 170px;
  object-fit: cover;
  display: block;
  transition: transform 0.3s;
}

.dd-card:hover .dd-img {
  transform: scale(1.05);
}

.dd-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(16, 185, 129, 0.92);
  color: #fff;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
}

.dd-select-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(255, 255, 255, 0.95);
  border: 1.5px solid #d5dde8;
  border-radius: 999px;
  padding: 0.3rem 0.85rem;
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.18s;
  color: #1e2d44;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.dd-select-btn i {
  font-size: 1rem;
}

.dd-select-btn:hover {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

.dd-select-btn.remove {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

.dd-info {
  padding: 1rem;
}

.dd-info h5 {
  font-size: 0.97rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  color: #1e2d44;
}

.dd-addr {
  font-size: 0.82rem;
  color: #7a8ea0;
  margin-bottom: 0.4rem;
}

.dd-meta {
  display: flex;
  gap: 0.8rem;
  font-size: 0.8rem;
  color: #7a8ea0;
  flex-wrap: wrap;
}

.tlt-selection-info {
  font-size: 0.9rem;
  color: #5a6d80;
  font-weight: 600;
}

.sel-count {
  display: inline-block;
  background: #10b981;
  color: #fff;
  border-radius: 50%;
  width: 1.6rem;
  height: 1.6rem;
  text-align: center;
  line-height: 1.6rem;
  font-size: 0.85rem;
  font-weight: 700;
  margin-right: 0.35rem;
}

/* ──────────── Budget tracker ──────────── */
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

.budget-info {
  display: flex;
  flex-direction: column;
  font-size: 0.85rem;
}

.budget-info span {
  color: #7a8ea0;
}

.budget-info strong {
  font-size: 1.1rem;
  color: #1e2d44;
  font-weight: 800;
}

.budget-info strong.over {
  color: #f43f5e;
}

.budget-info strong.ok {
  color: #10b981;
}

.budget-bar-wrap {
  flex: 1;
  min-width: 120px;
  height: 8px;
  background: #e8edf5;
  border-radius: 999px;
  overflow: hidden;
}

.budget-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #0ea5e9);
  border-radius: 999px;
  transition: width 0.4s ease;
}

.budget-bar-fill.over {
  background: linear-gradient(90deg, #f43f5e, #fb923c);
}

/* ──────────── Day tabs ──────────── */
.day-tabs {
  display: flex;
  gap: 0.55rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.day-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.55rem 1.2rem;
  border-radius: 0.9rem;
  border: 1.5px solid #dbe3f0;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 80px;
}

.day-tab.active {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.28);
}

.day-tab-num {
  font-weight: 700;
  font-size: 0.88rem;
}

.day-tab-date {
  font-size: 0.75rem;
  opacity: 0.8;
}

/* ──────────── Timeline ──────────── */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.timeline-item {
  display: flex;
  gap: 1rem;
}

.timeline-time {
  width: 72px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 0.2rem;
  gap: 0.2rem;
}

.time-badge {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.25rem 0.45rem;
  border-radius: 0.55rem;
  white-space: nowrap;
}

.time-end-badge {
  background: #e0f2fe;
  color: #0369a1;
  font-size: 0.65rem;
  font-weight: 600;
  padding: 0.18rem 0.4rem;
  border-radius: 0.45rem;
  white-space: nowrap;
}

.duration-badge {
  background: #fef9c3;
  color: #854d0e;
  font-size: 0.62rem;
  font-weight: 700;
  padding: 0.15rem 0.38rem;
  border-radius: 0.45rem;
  white-space: nowrap;
}

.timeline-line {
  flex: 1;
  width: 3px;
  background: linear-gradient(to bottom, #10b981, #0ea5e9, transparent);
  margin: 0.4rem 0;
  min-height: 40px;
  border-radius: 99px;
  opacity: 0.4;
}

.timeline-card {
  flex: 1;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 1.25rem;
  border: 1px solid rgba(255, 255, 255, 0.5);
  padding: 1.2rem;
  margin-bottom: 1.2rem;
  box-shadow: 0 10px 30px rgba(30, 45, 68, 0.08);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.timeline-card:hover {
  transform: translateX(8px);
  box-shadow: 0 15px 40px rgba(30, 45, 68, 0.12);
  border-color: #10b981;
}

.tc-header {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
}

.tc-img-wrap {
  flex-shrink: 0;
}

.tc-img {
  width: 70px;
  height: 60px;
  object-fit: cover;
  border-radius: 0.7rem;
  display: block;
}

.tc-meta {
  flex: 1;
}

.tc-meta h5 {
  font-size: 0.93rem;
  font-weight: 700;
  margin-bottom: 0.2rem;
  color: #1e2d44;
}

.tc-meta p {
  font-size: 0.8rem;
  color: #7a8ea0;
  margin-bottom: 0.15rem;
}

.tc-order-btns {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  flex-shrink: 0;
}

.tc-order-btns button {
  width: 32px;
  height: 32px;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  color: #64748b;
  transition: all 0.2s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.tc-order-btns button:hover:not(:disabled) {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
  transform: scale(1.1);
}

.tc-order-btns button:disabled {
  opacity: 0.2;
  cursor: not-allowed;
}

.tc-order-btns .btn-remove:hover:not(:disabled) {
  background: #f43f5e;
  border-color: #f43f5e;
  color: #fff;
}

.sortable-ghost {
  opacity: 0.4;
  background: #eef4ff;
  border: 2px dashed #3b82f6;
}

.empty-day-placeholder {
  background: rgba(255, 255, 255, 0.5);
  border-radius: 1.5rem;
  border: 2px dashed #dbe3f0;
}

.empty-icon-wrap {
  font-size: 3rem;
  color: #c0cedf;
}

.btn-brand-lg {
  padding: 0.8rem 2rem;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s;
  box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
}

.btn-brand-lg:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3);
}

.tc-note {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px dashed #e8edf5;
  font-size: 0.82rem;
  color: #a0adbf;
}

.note-input {
  flex: 1;
  border: none;
  outline: none;
  font-size: 0.85rem;
  color: #3d5166;
  background: transparent;
}

.note-input::placeholder {
  color: #c0c8d8;
}

/* ──────────── Summary bước 4 ──────────── */
.summary-grid {
  display: grid;
  grid-template-columns: 1fr repeat(3, auto);
  gap: 1rem;
  align-items: start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.summary-card-big {
  background: linear-gradient(135deg, #f0fff8, #e0f2fe);
  border-radius: 1.2rem;
  padding: 1.4rem 1.6rem;
  border: 1px solid #a7f3d0;
}

.sum-label {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #065f46;
  font-weight: 700;
}

.summary-card-big h3 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1e2d44;
  margin: 0.3rem 0 0.65rem;
}

.sum-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.sum-chips span {
  background: rgba(16, 185, 129, 0.1);
  color: #065f46;
  border-radius: 999px;
  padding: 0.22rem 0.75rem;
  font-size: 0.82rem;
  font-weight: 600;
}

.summary-stat {
  background: #fff;
  border-radius: 1.1rem;
  padding: 1.1rem 1.2rem;
  border: 1px solid #e8edf5;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.15rem;
  text-align: center;
  box-shadow: 0 4px 14px rgba(30, 45, 68, 0.06);
  min-width: 120px;
}

.summary-stat i {
  font-size: 1.4rem;
  color: #10b981;
  margin-bottom: 0.3rem;
}

.summary-stat strong {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1e2d44;
}

.summary-stat span {
  font-size: 0.78rem;
  color: #7a8ea0;
}

.mini-days {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.mini-day {
  background: #fff;
  border-radius: 1rem;
  padding: 1rem 1.1rem;
  border: 1px solid #e8edf5;
}

.mini-day-head {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.6rem;
}

.mini-day-head strong {
  font-size: 0.9rem;
  color: #1e2d44;
}

.mini-day-head span {
  font-size: 0.8rem;
  color: #7a8ea0;
}

.mini-day ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mini-day li {
  font-size: 0.83rem;
  color: #3d5166;
  padding: 0.18rem 0;
}

/* ──────────── Save alert ──────────── */
.save-alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.85rem 1.2rem;
  border-radius: 0.9rem;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1rem;
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

/* ──────────── Loading / Empty ──────────── */
.tlt-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 3rem;
  color: #7a8ea0;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3.5px solid #e8edf5;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.tlt-empty {
  text-align: center;
  color: #a0adbf;
  padding: 2rem;
  font-size: 0.92rem;
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .summary-grid {
    grid-template-columns: 1fr 1fr;
  }

  .dd-grid {
    grid-template-columns: 1fr;
  }

  .step3-layout {
    flex-direction: column;
  }

  .step3-right {
    min-width: unset;
  }
}

/* ──── Step 3: 2-column layout ──── */
.step3-layout {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.step3-left {
  flex: 1;
  min-width: 0;
}

.step3-right {
  flex: 0 0 400px;
  min-width: 320px;
  position: sticky;
  top: 90px;
}

/* ──── Map Panel ──── */
.map-panel {
  background: #fff;
  border-radius: 1.2rem;
  border: 1px solid #e8edf5;
  box-shadow: 0 8px 24px rgba(30, 45, 68, 0.07);
  overflow: hidden;
}

.map-panel-head {
  padding: 0.85rem 1.1rem;
  border-bottom: 1px solid #eef2f7;
  font-weight: 700;
  font-size: 0.92rem;
  color: #1e2d44;
  display: flex;
  align-items: center;
  background: #f8fbff;
  gap: 0.5rem;
}

.route-loading-text {
  margin-left: auto;
  font-weight: 500;
  font-size: 0.8rem;
  color: #5a67d8;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.spin {
  animation: spin 0.9s linear infinite;
  display: inline-block;
}

#trip-map {
  height: 500px;
  width: 100%;
  z-index: 0;
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
  0% {
    opacity: 0;
    transform: translateY(-10px) rotate(0deg);
  }

  20% {
    opacity: 1;
  }

  100% {
    opacity: 0;
    transform: translateY(72px) rotate(180deg);
  }
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
  from {
    transform: scale(0);
  }

  to {
    transform: scale(1);
  }
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

/* Note Tags Styling */
.note-tag {
  cursor: pointer;
  font-weight: 500;
  font-size: 0.85rem;
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #475569;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  user-select: none;
}

.note-tag:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.note-tag.selected {
  background: linear-gradient(135deg, #10b981, #059669) !important;
  color: #ffffff !important;
  border-color: transparent !important;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25) !important;
  transform: translateY(-2px) scale(1.05) !important;
}
</style>
