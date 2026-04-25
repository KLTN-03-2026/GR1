<template>
  <div class="member-progress-page">
    <header class="page-header">
      <h1>Báo Cáo Tiến Độ Thành Viên</h1>
      <p>Cập nhật công việc và trạng thái xử lý</p>
    </header>

    <section class="summary-box">
      <div class="card">
        <h3>Tổng nhiệm vụ</h3>
        <span>{{ totalTasks }}</span>
      </div>

      <div class="card">
        <h3>Hoàn thành</h3>
        <span>{{ completedTasks }}</span>
      </div>

      <div class="card">
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
          </tr>
        </thead>

        <tbody>
          <tr v-for="(task, index) in tasks" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ task.name }}</td>
            <td>{{ task.member }}</td>
            <td>
              <span :class="task.statusClass">
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
  padding: 30px;
  font-family: Arial, sans-serif;
  background: #f8f9fa;
}

.page-header {
  margin-bottom: 25px;
}

.page-header h1 {
  margin: 0;
  color: #2c3e50;
}

.page-header p {
  color: #666;
}

.summary-box {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin-bottom: 30px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.card h3 {
  margin: 0;
  font-size: 15px;
  color: #666;
}

.card span {
  font-size: 28px;
  font-weight: bold;
  color: #42b983;
}

.task-list {
  background: white;
  padding: 20px;
  border-radius: 10px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 12px;
  border-bottom: 1px solid #eee;
  text-align: left;
}

.done {
  color: green;
  font-weight: bold;
}

.processing {
  color: orange;
  font-weight: bold;
}

.pending {
  color: gray;
  font-weight: bold;
}
</style>