<template>
  <main class="view-report-page">
    <section class="header-panel">
      <h1>Báo cáo chuy?n di</h1>
      <p>Ðây là view m?u d? hi?n th? thông tin báo cáo, chi phí và dánh giá.</p>
    </section>

    <section class="summary-panel">
      <div class="summary-card" v-for="item in summaryItems" :key="item.title">
        <h2>{{ item.value }}</h2>
        <p>{{ item.title }}</p>
      </div>
    </section>

    <section class="report-table-panel">
      <div class="panel-title">Danh sách m?c báo cáo</div>
      <table class="report-table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Tiêu d?</th>
            <th>Lo?i</th>
            <th>Giá tr?</th>
            <th>Tr?ng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in reportRows" :key="row.id">
            <td>{{ index + 1 }}</td>
            <td>{{ row.title }}</td>
            <td>{{ row.type }}</td>
            <td>{{ row.value }}</td>
            <td>{{ row.status }}</td>
          </tr>
        </tbody>
      </table>
    </section>

    <section class="notes-panel">
      <h3>Ghi chú</h3>
      <p>Thông tin này ch? là view ?o d? mô ph?ng giao di?n và chua k?t n?i d? li?u th?t.</p>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue'

const summaryItems = ref([
  { title: 'T?ng chi phí', value: '15.200.000 d' },
  { title: 'S? chuy?n dã hoàn thành', value: '4' },
  { title: 'Ðánh giá trung bình', value: '4.8/5' }
])

const reportRows = ref([
  { id: 1, title: 'Chi phí nhiên li?u', type: 'Chi phí', value: '4.500.000 d', status: 'Ðã xác nh?n' },
  { id: 2, title: 'Chi phí an u?ng', type: 'Chi phí', value: '3.200.000 d', status: 'Ðã xác nh?n' },
  { id: 3, title: 'S? lu?t dánh giá', type: 'Th?ng kê', value: '12', status: 'Hoàn t?t' },
  { id: 4, title: 'T? l? hài lòng', type: 'Th?ng kê', value: '95%', status: '?n d?nh' }
])
</script>

<style scoped>
.view-report-page {
  padding: 24px;
  font-family: var(--font-family, Arial, sans-serif);
  color: #2c3e50;
  background: #f9fbfd;
}

.header-panel {
  margin-bottom: 20px;
}

.header-panel h1 {
  margin: 0 0 8px;
  font-size: 28px;
}

.summary-panel {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.summary-card {
  background: #ffffff;
  border: 1px solid #e6eaf0;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 8px 20px rgba(29, 47, 76, 0.04);
}

.summary-card h2 {
  margin: 0 0 6px;
  font-size: 22px;
}

.report-table-panel {
  background: #ffffff;
  border: 1px solid #e6eaf0;
  border-radius: 12px;
  padding: 16px;
}

.panel-title {
  margin-bottom: 12px;
  font-weight: 700;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
}

.report-table th,
.report-table td {
  padding: 12px 10px;
  border-bottom: 1px solid #eef2f7;
  text-align: left;
}

.report-table th {
  color: #475569;
  font-weight: 600;
}

.notes-panel {
  margin-top: 24px;
  background: #ffffff;
  border: 1px solid #e6eaf0;
  border-radius: 12px;
  padding: 16px;
}
</style>
