<template>
  <div class="member-progress-page">
    <header class="page-header">
      <h1>Báo Cáo Tiến Độ Thành Viên</h1>
      <p>Cập nhật công việc và trạng thái xử lý</p>
    </header>

    <section class="summary-box">
      <div class="card card-total">
        <h3>Tổng nhiệm vụ</h3>
        <span>{{ totalTasks }}</span>
      </div>

      <div class="card card-done">
        <h3>Hoàn thành</h3>
        <span>{{ completedTasks }}</span>
      </div>

      <div class="card card-processing">
        <h3>Đang xử lý</h3>
        <span>{{ processingTasks }}</span>
      </div>
    </section>

    <section class="task-list">
      <h2>Danh sách công việc</h2>

      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Tên công việc</th>
            <th>Người phụ trách</th>
            <th>Trạng thái</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(task, index) in tasks" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ task.name }}</td>
            <td>{{ task.member }}</td>
            <td>
              <span :class="['status-badge', task.statusClass]">
                {{ task.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script>
export default {
  name: "MemberProgressPage",
  data() {
    return {
      tasks: [
        {
          name: "Thiết kế giao diện đăng nhập",
          member: "Nguyễn Anh Hiếu",
          status: "Hoàn thành",
          statusClass: "done"
        },
        {
          name: "Xây dựng API đăng ký",
          member: "Trần Văn A",
          status: "Đang xử lý",
          statusClass: "processing"
        },
        {
          name: "Tối ưu database",
          member: "Lê Văn B",
          status: "Hoàn thành",
          statusClass: "done"
        },
        {
          name: "Kiểm thử hệ thống",
          member: "Phạm Văn C",
          status: "Chờ xử lý",
          statusClass: "pending"
        }
      ]
    };
  },
  computed: {
    totalTasks() {
      return this.tasks.length;
    },
    completedTasks() {
      return this.tasks.filter(t => t.status === "Hoàn thành").length;
    },
    processingTasks() {
      return this.tasks.filter(t => t.status === "Đang xử lý").length;
    }
  }
};
</script>

<style scoped>
.member-progress-page {
  padding: 32px 0;
  font-family: 'Segoe UI', Arial, sans-serif;
  background: linear-gradient(120deg, #f8f9fa 70%, #e3f0ff 100%);
  min-height: 100vh;
}

.page-header {
  margin-bottom: 28px;
  text-align: center;
}

.page-header h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 2.2rem;
  letter-spacing: 1px;
}

.page-header p {
  color: #666;
  font-size: 1.1rem;
}

.summary-box {
  display: flex;
  justify-content: center;
  gap: 24px;
  margin-bottom: 36px;
}

.card {
  background: white;
  padding: 24px 32px;
  border-radius: 14px;
  box-shadow: 0 4px 16px rgba(66,185,131,0.07);
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 140px;
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-4px) scale(1.03);
  box-shadow: 0 8px 24px rgba(66,185,131,0.13);
}
.card-total span {
  color: #2c3e50;
}
.card-done span {
  color: #42b983;
}
.card-processing span {
  color: #f39c12;
}
.card h3 {
  margin: 0 0 6px 0;
  font-size: 15px;
  color: #888;
  font-weight: 500;
}
.card span {
  font-size: 32px;
  font-weight: bold;
}

.task-list {
  background: white;
  padding: 28px 20px 20px 20px;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  max-width: 700px;
  margin: 0 auto;
}
.task-list h2 {
  margin-bottom: 18px;
  color: #2c3e50;
  font-size: 1.3rem;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}
th, td {
  padding: 13px 10px;
  border-bottom: 1px solid #f0f0f0;
  text-align: left;
  font-size: 1rem;
}
th {
  background: #f7f7f7;
  color: #555;
  font-weight: 600;
}
.status-badge {
  display: inline-block;
  padding: 4px 14px;
  border-radius: 12px;
  font-size: 0.97rem;
  font-weight: 500;
  background: #f3f3f3;
  min-width: 90px;
  text-align: center;
}
.done {
  color: #fff;
  background: linear-gradient(90deg, #42b983 60%, #2ecc71 100%);
}
.processing {
  color: #fff;
  background: linear-gradient(90deg, #f39c12 60%, #f7b731 100%);
}
.pending {
  color: #fff;
  background: linear-gradient(90deg, #95a5a6 60%, #bdc3c7 100%);
}
</style>