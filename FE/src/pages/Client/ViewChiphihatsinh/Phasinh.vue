<template>
  <div class="phat-sinh-container">
    <h2>Chi phí phát sinh</h2>
    <div class="actions">
      <button @click="showAddModal = true">Thêm chi phí</button>
    </div>
    <table class="expense-table">
      <thead>
        <tr>
          <th>STT</th>
          <th>Tên chi phí</th>
          <th>Số tiền</th>
          <th>Ngày phát sinh</th>
          <th>Ghi chú</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, idx) in expenses" :key="item.id">
          <td>{{ idx + 1 }}</td>
          <td>{{ item.name }}</td>
          <td>{{ item.amount | currency }}</td>
          <td>{{ item.date }}</td>
          <td>{{ item.note }}</td>
          <td>
            <button @click="editExpense(item)">Sửa</button>
            <button @click="deleteExpense(item.id)">Xóa</button>
          </td>
        </tr>
        <tr v-if="expenses.length === 0">
          <td colspan="6" style="text-align:center">Không có dữ liệu</td>
        </tr>
      </tbody>
    </table>

    <!-- Modal thêm/sửa chi phí -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay">
      <div class="modal-content">
        <h3>{{ showEditModal ? 'Sửa chi phí' : 'Thêm chi phí' }}</h3>
        <form @submit.prevent="showEditModal ? updateExpense() : addExpense()">
          <div class="form-group">
            <label>Tên chi phí</label>
            <input v-model="form.name" required />
          </div>
          <div class="form-group">
            <label>Số tiền</label>
            <input v-model.number="form.amount" type="number" required />
          </div>
          <div class="form-group">
            <label>Ngày phát sinh</label>
            <input v-model="form.date" type="date" required />
          </div>
          <div class="form-group">
            <label>Ghi chú</label>
            <input v-model="form.note" />
          </div>
          <div class="form-actions">
            <button type="submit">Lưu</button>
            <button type="button" @click="closeModal">Hủy</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Phasinh',
  data() {
    return {
      expenses: [],
      showAddModal: false,
      showEditModal: false,
      form: {
        id: null,
        name: '',
        amount: '',
        date: '',
        note: ''
      }
    }
  },
  methods: {
    addExpense() {
      const newExpense = { ...this.form, id: Date.now() }
      this.expenses.push(newExpense)
      this.closeModal()
    },
    editExpense(item) {
      this.form = { ...item }
      this.showEditModal = true
    },
    updateExpense() {
      const idx = this.expenses.findIndex(e => e.id === this.form.id)
      if (idx !== -1) this.expenses.splice(idx, 1, { ...this.form })
      this.closeModal()
    },
    deleteExpense(id) {
      this.expenses = this.expenses.filter(e => e.id !== id)
    },
    closeModal() {
      this.showAddModal = false
      this.showEditModal = false
      this.form = { id: null, name: '', amount: '', date: '', note: '' }
    }
  },
  filters: {
    currency(val) {
      if (!val) return '0 ₫'
      return Number(val).toLocaleString('vi-VN') + ' ₫'
    }
  }
}
</script>

<style scoped>
.phat-sinh-container {
  max-width: 800px;
  margin: 30px auto;
  background: #fff;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.actions {
  margin-bottom: 16px;
  text-align: right;
}
.expense-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 16px;
}
.expense-table th, .expense-table td {
  border: 1px solid #eee;
  padding: 8px 12px;
}
.expense-table th {
  background: #f7f7f7;
}
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  padding: 24px;
  border-radius: 8px;
  min-width: 320px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.form-group {
  margin-bottom: 12px;
}
.form-group label {
  display: block;
  margin-bottom: 4px;
}
.form-group input {
  width: 100%;
  padding: 6px 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>
