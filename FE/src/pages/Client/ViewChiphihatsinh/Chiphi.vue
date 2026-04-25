<template>
	<div class="chiphi-page">
		<header class="page-header">
			<h1>Báo Cáo Chi Phí Phát Sinh</h1>
			<p>Theo dõi, quản lý và phân tích các chi phí phát sinh trong chuyến đi của bạn.</p>
		</header>

		<section class="summary-box">
			<div class="card total">
				<h3>Tổng chi phí</h3>
				<span>{{ totalCost | currency }}</span>
				<p class="desc">Tổng tất cả các chi phí đã ghi nhận</p>
			</div>
			<div class="card approved">
				<h3>Đã duyệt</h3>
				<span>{{ approvedCost | currency }}</span>
				<p class="desc">Chi phí đã được xác nhận</p>
			</div>
			<div class="card pending">
				<h3>Chờ duyệt</h3>
				<span>{{ pendingCost | currency }}</span>
				<p class="desc">Chi phí đang chờ xác nhận</p>
			</div>
		</section>

		<section class="cost-list">
			<div class="cost-list-header">
				<h2>Danh sách chi phí phát sinh</h2>
				<div class="search-box">
					<input v-model="search" placeholder="Tìm kiếm nội dung, người tạo..." />
				</div>
			</div>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>#</th>
							<th>Nội dung</th>
							<th>Người tạo</th>
							<th>Số tiền</th>
							<th>Ngày tạo</th>
							<th>Ghi chú</th>
							<th>Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(cost, index) in filteredCosts" :key="index" :class="{ even: index % 2 === 0 }">
							<td>{{ index + 1 }}</td>
							<td>
								<span class="desc-main">{{ cost.description }}</span>
								<span v-if="cost.important" class="important" title="Chi phí quan trọng">★</span>
							</td>
							<td>{{ cost.creator }}</td>
							<td>{{ cost.amount | currency }}</td>
							<td>{{ cost.date }}</td>
							<td>
								<span v-if="cost.note" class="note" :title="cost.note">🛈</span>
							</td>
							<td>
								<span :class="cost.statusClass" :title="cost.status">
									<span v-if="cost.status === 'Đã duyệt'">✔️</span>
									<span v-else-if="cost.status === 'Chờ duyệt'">⏳</span>
									<span v-else>❓</span>
									{{ cost.status }}
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="table-footer">
				<span>Tổng số dòng: {{ filteredCosts.length }}</span>
			</div>
		</section>
		<footer class="chiphi-footer">
			<p>© {{ new Date().getFullYear() }} Quản lý chi phí phát sinh. Thiết kế bởi nhóm bạn.</p>
		</footer>
	</div>
</template>

<script>
export default {
	name: "ChiphiPage",
	data() {
		return {
			search: "",
			costs: [
				{
					description: "Ăn uống ngoài kế hoạch",
					creator: "Nguyễn Văn A",
					amount: 1200000,
					date: "2026-04-20",
					note: "Ăn tối tại nhà hàng ven biển.",
					status: "Đã duyệt",
					statusClass: "approved",
					important: true
				},
				{
					description: "Thuê xe di chuyển",
					creator: "Trần Thị B",
					amount: 800000,
					date: "2026-04-21",
					note: "Thuê xe 16 chỗ cho đoàn.",
					status: "Chờ duyệt",
					statusClass: "pending",
					important: false
				},
				{
					description: "Mua vật dụng cần thiết",
					creator: "Lê Văn C",
					amount: 350000,
					date: "2026-04-22",
					note: "Mua áo mưa, nước uống.",
					status: "Đã duyệt",
					statusClass: "approved",
					important: false
				},
				{
					description: "Chi phí phát sinh khác",
					creator: "Phạm Thị D",
					amount: 500000,
					date: "2026-04-23",
					note: "Chi phí linh tinh.",
					status: "Chờ duyệt",
					statusClass: "pending",
					important: false
				},
				{
					description: "Phí tham quan",
					creator: "Nguyễn Văn E",
					amount: 900000,
					date: "2026-04-24",
					note: "Vé vào cổng khu du lịch.",
					status: "Đã duyệt",
					statusClass: "approved",
					important: true
				}
			]
		};
	},
	computed: {
		totalCost() {
			return this.costs.reduce((sum, c) => sum + c.amount, 0);
		},
		approvedCost() {
			return this.costs.filter(c => c.status === "Đã duyệt").reduce((sum, c) => sum + c.amount, 0);
		},
		pendingCost() {
			return this.costs.filter(c => c.status === "Chờ duyệt").reduce((sum, c) => sum + c.amount, 0);
		},
		filteredCosts() {
			if (!this.search) return this.costs;
			const s = this.search.toLowerCase();
			return this.costs.filter(c =>
				c.description.toLowerCase().includes(s) ||
				c.creator.toLowerCase().includes(s) ||
				(c.note && c.note.toLowerCase().includes(s))
			);
		}
	},
	filters: {
		currency(value) {
			if (!value && value !== 0) return '';
			return value.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
		}
	}
};
</script>

<style scoped>
/* Layout & background */
.chiphi-page {
	padding: 32px 8vw 0 8vw;
	font-family: 'Segoe UI', Arial, sans-serif;
	background: linear-gradient(120deg, #f8f9fa 70%, #e3f0ff 100%);
	min-height: 100vh;
}

/* Header */
.page-header {
	margin-bottom: 28px;
	text-align: center;
}
.page-header h1 {
	margin: 0;
	color: #2c3e50;
	font-size: 2.5rem;
	letter-spacing: 1px;
}
.page-header p {
	color: #666;
	font-size: 1.1rem;
}

/* Summary cards */
.summary-box {
	display: flex;
	gap: 18px;
	margin-bottom: 34px;
	flex-wrap: wrap;
	justify-content: center;
}
.card {
	background: white;
	padding: 24px 32px 18px 32px;
	border-radius: 14px;
	box-shadow: 0 3px 16px rgba(0,0,0,0.08);
	min-width: 220px;
	flex: 1 1 220px;
	position: relative;
	transition: transform 0.18s, box-shadow 0.18s;
}
.card:hover {
	transform: translateY(-4px) scale(1.03);
	box-shadow: 0 8px 24px rgba(66,185,131,0.13);
}
.card h3 {
	margin: 0 0 4px 0;
	font-size: 1.1rem;
	color: #666;
}
.card span {
	font-size: 2.1rem;
	font-weight: bold;
	color: #42b983;
	display: block;
	margin-bottom: 2px;
}
.card .desc {
	font-size: 0.95rem;
	color: #aaa;
	margin-top: 2px;
}
.card.total span {
	color: #007bff;
}
.card.approved span {
	color: #42b983;
}
.card.pending span {
	color: orange;
}

/* Cost list */
.cost-list {
	background: white;
	padding: 28px 18px 18px 18px;
	border-radius: 14px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
	margin-bottom: 32px;
}
.cost-list-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 18px;
	flex-wrap: wrap;
}
.cost-list-header h2 {
	margin: 0;
	font-size: 1.3rem;
	color: #2c3e50;
}
.search-box {
	margin-left: 12px;
}
.search-box input {
	padding: 7px 14px;
	border-radius: 7px;
	border: 1px solid #d0d7de;
	font-size: 1rem;
	min-width: 220px;
	outline: none;
	transition: border-color 0.2s;
}
.search-box input:focus {
	border-color: #42b983;
}

.table-responsive {
	overflow-x: auto;
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
	background: #f6f8fa;
	color: #333;
	font-weight: 600;
}
tr.even {
	background: #f9fcff;
}
tr:hover td {
	background: #eafaf1;
	transition: background 0.2s;
}
.desc-main {
	font-weight: 500;
}
.important {
	color: #ff9800;
	margin-left: 4px;
	font-size: 1.1em;
	cursor: pointer;
}
.note {
	color: #007bff;
	cursor: pointer;
	font-size: 1.2em;
}
.approved {
	color: #42b983;
	font-weight: bold;
}
.pending {
	color: orange;
	font-weight: bold;
}
.table-footer {
	margin-top: 10px;
	color: #888;
	font-size: 0.98rem;
	text-align: right;
}

/* Footer */
.chiphi-footer {
	text-align: center;
	color: #aaa;
	font-size: 0.98rem;
	margin-top: 38px;
	margin-bottom: 12px;
}

/* Responsive */
@media (max-width: 900px) {
	.chiphi-page {
		padding: 18px 2vw 0 2vw;
	}
	.summary-box {
		flex-direction: column;
		gap: 10px;
	}
	.card {
		min-width: unset;
		padding: 18px 12px 12px 12px;
	}
	.cost-list {
		padding: 14px 2px 10px 2px;
	}
}
</style>
</style>
