<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Travel API Docs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        .method-get { @apply bg-blue-500/10 text-blue-400 border-blue-500/20; }
        .method-post { @apply bg-green-500/10 text-green-400 border-green-500/20; }
        .method-put { @apply bg-orange-500/10 text-orange-400 border-orange-500/20; }
        .method-delete { @apply bg-red-500/10 text-red-400 border-red-500/20; }
        .bg-glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .token.string { color: #a5d6ff; }
        .token.number { color: #79c0ff; }
        .token.boolean { color: #ff7b72; }
    </style>
</head>
<body class="h-screen flex text-slate-300 overflow-hidden">
    <!-- Sidebar -->
    <div class="w-80 bg-glass text-slate-300 border-r border-slate-700/50 flex flex-col h-full z-10 shrink-0 shadow-xl">
        <div class="p-5 border-b border-slate-700/50">
            <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">AI</span>
                <span class="text-white">Travel</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 ml-1 border border-indigo-500/30">API</span>
            </h1>
            <p class="text-[11px] text-slate-400 font-medium tracking-wide mt-1 uppercase">Custom API Tester V1.0</p>
        </div>
        <div class="p-4 border-b border-slate-700/50 bg-slate-900/40">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Tìm kiếm API (vd: adm...)" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-300 placeholder:text-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-inner">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto scrollbar-hide px-3 py-4 space-y-6" id="apiSidebar">
            <!-- Sidebar Items Injected via JS -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full bg-[#0b0f1a] relative shadow-2xl z-0 min-w-0">
        <!-- Empty State -->
        <div class="absolute inset-0 flex items-center justify-center flex-col animate-pulse opacity-60 z-10" id="emptyState">
            <div class="w-24 h-24 mb-6 rounded-2xl bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 flex items-center justify-center border border-indigo-500/10 shadow-[0_0_50px_rgba(99,102,241,0.2)]">
                <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h2 class="text-xl font-medium text-slate-300 tracking-wide">Trình Dịch Vụ API</h2>
            <p class="text-sm text-slate-500 mt-2">Chọn một API bên trái để xem chi tiết và thử nghiệm</p>
        </div>

        <div class="flex-1 flex flex-col hidden h-full overflow-hidden z-20" id="apiContent">
            <!-- Top Header -->
            <div class="px-6 py-5 border-b border-white/5 bg-slate-900/60 backdrop-blur justify-between flex items-start sm:items-center gap-4 flex-col sm:flex-row shadow-sm">
                <div>
                    <div class="flex items-center gap-3">
                        <span id="apiMethod" class="px-3 py-1 font-bold rounded text-sm border whitespace-nowrap shadow-sm">GET</span>
                        <div class="flex items-center text-xl font-mono truncate">
                            <span class="text-slate-500 hidden sm:inline" id="baseUrlSpan"></span>
                            <span class="text-slate-200 font-semibold" id="apiPath">/api/endpoint</span>
                        </div>
                    </div>
                    <p id="apiDesc" class="text-slate-400 mt-2 text-sm font-medium ml-1">Lấy danh sách dữ liệu</p>
                </div>
                <div class="w-full sm:w-auto shrink-0 flex items-center gap-3">
                    <div class="relative w-full sm:w-64 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-500 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="text" id="bearerToken" placeholder="Auth Bearer Token..." class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-300 placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Editor Areas -->
            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0 relative">
                <!-- Request Config Panel -->
                <div class="w-full lg:w-5/12 p-6 border-b lg:border-b-0 lg:border-r border-white/5 overflow-y-auto bg-[#0a0f18] shrink-0">
                    
                    <!-- Path Parameters -->
                    <div id="pathParamsContainer" class="mb-8 hidden">
                        <h3 class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Path Variables
                        </h3>
                        <div id="pathParamsList" class="space-y-3 bg-slate-900/30 p-4 rounded-xl border border-white/5"></div>
                    </div>

                    <!-- Request Body -->
                    <div id="bodyContainer" class="hidden flex-col">
                        <h3 class="flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">
                            <span class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                JSON Body
                            </span>
                            <button onclick="formatBody()" class="text-indigo-400 hover:text-indigo-300 transition-colors font-medium flex items-center gap-1 active:scale-95">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                Format
                            </button>
                        </h3>
                        <div class="relative group h-64 lg:h-80">
                            <textarea id="requestBody" spellcheck="false" class="absolute inset-0 w-full h-full bg-[#0d131f] text-slate-300 font-mono text-xs sm:text-sm p-4 rounded-xl border border-slate-700/50 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 resize-none shadow-inner z-10 transition-colors"></textarea>
                            <div class="absolute top-2 right-2 text-[10px] text-slate-500 font-mono z-20 pointer-events-none opacity-50 select-none">application/json</div>
                        </div>
                    </div>

                    <button id="sendBtn" class="mt-8 w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3.5 rounded-xl shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_25px_rgba(79,70,229,0.5)] transition-all flex justify-center items-center gap-2 relative overflow-hidden group active:scale-[0.98]">
                        <span class="relative z-10 flex items-center gap-2">
                            Gửi Request
                            <svg class="w-4 h-4 translate-x-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </button>
                    <p class="text-center text-[10px] text-slate-500 mt-3 font-medium">Nhấn Phím Tắt <kbd class="px-1.5 py-0.5 bg-slate-800 rounded mx-1 font-mono border border-slate-700 shadow-sm">Enter</kbd> khi đang nhập Path Variable</p>
                </div>

                <!-- Response Panel -->
                <div class="w-full lg:w-7/12 flex flex-col bg-[#070b12] relative">
                    <div class="px-6 py-3.5 border-b border-white/5 flex justify-between items-center bg-slate-900/40 shrink-0">
                        <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Response
                        </h3>
                        <div class="flex items-center gap-3 text-xs font-mono font-medium">
                            <span id="resStatus" class="hidden px-2.5 py-1 rounded shadow-sm"></span>
                            <span id="resTime" class="text-slate-400 hidden bg-slate-800/80 px-2 py-1 rounded border border-white/5"></span>
                            <span id="resSize" class="text-slate-400 hidden bg-slate-800/80 px-2 py-1 rounded border border-white/5"></span>
                        </div>
                    </div>
                    
                    <div class="flex-1 relative">
                        <!-- Empty Response State -->
                        <div id="responseEmpty" class="absolute inset-0 flex items-center justify-center text-slate-600 font-mono text-sm z-0">
                            Chưa có dữ liệu phản hồi
                        </div>
                        
                        <!-- Loading Overlay -->
                        <div id="loading" class="hidden absolute inset-0 bg-[#070b12]/80 backdrop-blur-sm flex flex-col items-center justify-center z-20">
                            <div class="w-10 h-10 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
                            <span class="text-indigo-400 text-xs font-medium tracking-widest uppercase mt-4 animate-pulse">Đang tải...</span>
                        </div>

                        <!-- Response Code Block -->
                        <div class="absolute inset-0 overflow-auto z-10 opacity-0 transition-opacity duration-300" id="responseContainer">
                            <pre id="responsePre" class="language-json m-0 h-full w-full !bg-transparent !p-6 !text-sm"><code id="responseCode" class="language-json"></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
    
    <script>
        // Danh sách các API dự kiến
        const apis = [
            {
                group: 'Admins',
                endpoints: [
                    { method: 'GET', path: '/api/admins', desc: 'Danh sách Admins', params: [], body: null },
                    { method: 'POST', path: '/api/admins', desc: 'Thêm Admin mới', params: [], body: "{\n  \"name\": \"John Doe\",\n  \"email\": \"admin@travel.com\",\n  \"password\": \"12345678\"\n}" },
                    { method: 'GET', path: '/api/admins/{admin}', desc: 'Lấy thông tin một Admin', params: ['admin'], body: null },
                    { method: 'PUT', path: '/api/admins/{admin}', desc: 'Cập nhật thông tin Admin', params: ['admin'], body: "{\n  \"name\": \"John Updated\"\n}" },
                    { method: 'DELETE', path: '/api/admins/{admin}', desc: 'Xóa Admin', params: ['admin'], body: null },
                ]
            },
            {
                group: 'Chức Vụ',
                endpoints: [
                    { method: 'GET', path: '/api/chuc-vus', desc: 'Danh sách chức vụ', params: [], body: null },
                    { method: 'POST', path: '/api/chuc-vus', desc: 'Thêm chức vụ', params: [], body: "{\n  \"ten_chuc_vu\": \"Quản lý\"\n}" },
                    { method: 'GET', path: '/api/chuc-vus/{chuc_vu}', desc: 'Chi tiết chức vụ', params: ['chuc_vu'], body: null },
                    { method: 'PUT', path: '/api/chuc-vus/{chuc_vu}', desc: 'Cập nhật chức vụ', params: ['chuc_vu'], body: "{\n  \"ten_chuc_vu\": \"Giám đốc\"\n}" },
                    { method: 'DELETE', path: '/api/chuc-vus/{chuc_vu}', desc: 'Xóa chức vụ', params: ['chuc_vu'], body: null },
                ]
            },
            {
                group: 'Chức Năng',
                endpoints: [
                    { method: 'GET', path: '/api/chuc-nangs', desc: 'Danh sách chức năng', params: [], body: null },
                    { method: 'POST', path: '/api/chuc-nangs', desc: 'Thêm chức năng', params: [], body: "{\n  \"ten_chuc_nang\": \"Quản lý Admin\",\n  \"route\": \"admins\"\n}" },
                    { method: 'GET', path: '/api/chuc-nangs/{chuc_nang}', desc: 'Chi tiết', params: ['chuc_nang'], body: null },
                    { method: 'PUT', path: '/api/chuc-nangs/{chuc_nang}', desc: 'Cập nhật', params: ['chuc_nang'], body: "{\n  \"ten_chuc_nang\": \"Quản lý Tài Khoản\"\n}" },
                    { method: 'DELETE', path: '/api/chuc-nangs/{chuc_nang}', desc: 'Xóa chức năng', params: ['chuc_nang'], body: null },
                ]
            },
            {
                group: 'Phân Quyền',
                endpoints: [
                    { method: 'GET', path: '/api/phan-quyens', desc: 'Danh sách phân quyền', params: [], body: null },
                    { method: 'POST', path: '/api/phan-quyens', desc: 'Cấp quyền', params: [], body: "{\n  \"id_chuc_vu\": 1,\n  \"id_chuc_nang\": 2\n}" },
                    { method: 'GET', path: '/api/phan-quyens/{phan_quyen}', desc: 'Lấy chi tiết', params: ['phan_quyen'], body: null },
                    { method: 'PUT', path: '/api/phan-quyens/{phan_quyen}', desc: 'Sửa phân quyền', params: ['phan_quyen'], body: "{\n  \"id_chuc_vu\": 1,\n  \"id_chuc_nang\": 3\n}" },
                    { method: 'DELETE', path: '/api/phan-quyens/{phan_quyen}', desc: 'Thu hồi quyền', params: ['phan_quyen'], body: null },
                ]
            },
            {
                group: 'Danh Mục',
                endpoints: [
                    { method: 'GET', path: '/api/danh-mucs', desc: 'Danh sách danh mục', params: [], body: null },
                    { method: 'POST', path: '/api/danh-mucs', desc: 'Thêm danh mục', params: [], body: "{\n  \"ten_danh_muc\": \"Loại hình du lịch\"\n}" },
                    { method: 'GET', path: '/api/danh-mucs/{danh_muc}', desc: 'Chi tiết danh mục', params: ['danh_muc'], body: null },
                    { method: 'PUT', path: '/api/danh-mucs/{danh_muc}', desc: 'Cập nhật danh mục', params: ['danh_muc'], body: "{\n  \"ten_danh_muc\": \"Tour Nước Ngoài\"\n}" },
                    { method: 'DELETE', path: '/api/danh-mucs/{danh_muc}', desc: 'Xóa danh mục', params: ['danh_muc'], body: null },
                ]
            },
            {
                group: 'Chi Tiết Danh Mục',
                endpoints: [
                    { method: 'GET', path: '/api/chi-tiet-danh-mucs', desc: 'Danh sách chi tiết DM', params: [], body: null },
                    { method: 'POST', path: '/api/chi-tiet-danh-mucs', desc: 'Thêm chi tiết DM', params: [], body: "{\n  \"danh_muc_id\": 1,\n  \"ten_chi_tiet\": \"Du lịch biển\"\n}" },
                    { method: 'GET', path: '/api/chi-tiet-danh-mucs/{chi_tiet_danh_muc}', desc: 'Xem chi tiết', params: ['chi_tiet_danh_muc'], body: null },
                    { method: 'PUT', path: '/api/chi-tiet-danh-mucs/{chi_tiet_danh_muc}', desc: 'Sửa chi tiết DM', params: ['chi_tiet_danh_muc'], body: "{\n  \"ten_chi_tiet\": \"Du lịch núi\"\n}" },
                    { method: 'DELETE', path: '/api/chi-tiet-danh-mucs/{chi_tiet_danh_muc}', desc: 'Xóa chi tiết DM', params: ['chi_tiet_danh_muc'], body: null },
                ]
            },
            {
                group: 'Chi Phí Phát Sinh',
                endpoints: [
                    { method: 'GET', path: '/api/chi-phi-phat-sinhs', desc: 'Danh sách chi phí', params: [], body: null },
                    { method: 'POST', path: '/api/chi-phi-phat-sinhs', desc: 'Thêm chi phí', params: [], body: "{\n  \"tour_id\": 1,\n  \"ten_chi_phi\": \"Vé tham quan\",\n  \"so_tien\": 500000\n}" },
                    { method: 'GET', path: '/api/chi-phi-phat-sinhs/{chi_phi_phat_sinh}', desc: 'Xem chi phí', params: ['chi_phi_phat_sinh'], body: null },
                    { method: 'PUT', path: '/api/chi-phi-phat-sinhs/{chi_phi_phat_sinh}', desc: 'Cập nhật', params: ['chi_phi_phat_sinh'], body: "{\n  \"so_tien\": 600000\n}" },
                    { method: 'DELETE', path: '/api/chi-phi-phat-sinhs/{chi_phi_phat_sinh}', desc: 'Xóa chi phí', params: ['chi_phi_phat_sinh'], body: null },
                ]
            }
        ];

        let currentApi = null;
        let isSending = false;
        
        // Setup Base URL
        const appUrl = window.location.origin;
        document.getElementById('baseUrlSpan').textContent = appUrl;

        // Render Sidebar
        function renderSidebar(search = '') {
            const sidebar = document.getElementById('apiSidebar');
            sidebar.innerHTML = '';

            const term = search.toLowerCase();

            apis.forEach(group => {
                const filtered = group.endpoints.filter(ep => 
                    ep.path.toLowerCase().includes(term) || 
                    ep.desc.toLowerCase().includes(term) ||
                    group.group.toLowerCase().includes(term)
                );
                
                if (filtered.length === 0) return;

                const groupDiv = document.createElement('div');
                groupDiv.className = 'mb-6';
                
                // Group Header
                const groupTitle = document.createElement('h2');
                groupTitle.className = 'text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2 group cursor-pointer';
                groupTitle.innerHTML = `
                    <div class="p-1 rounded bg-slate-800/50 border border-slate-700/50 group-hover:bg-slate-700/50 transition-colors">
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    ${group.group}
                    <span class="ml-auto text-slate-700 font-mono">${filtered.length}</span>
                `;
                
                const endpointsList = document.createElement('div');
                endpointsList.className = 'space-y-1';

                groupTitle.onclick = () => {
                    const svg = groupTitle.querySelector('svg');
                    const isExpanded = !endpointsList.classList.contains('hidden');
                    if (isExpanded) {
                        endpointsList.classList.add('hidden');
                        svg.style.transform = 'rotate(-90deg)';
                    } else {
                        endpointsList.classList.remove('hidden');
                        svg.style.transform = 'rotate(0deg)';
                    }
                };

                groupDiv.appendChild(groupTitle);
                
                filtered.forEach(ep => {
                    const btn = document.createElement('button');
                    const methodClass = `method-${ep.method.toLowerCase()}`;
                    btn.className = `w-full text-left p-2.5 rounded-xl border border-transparent flex items-start gap-3 hover:bg-slate-800/50 hover:border-slate-700/50 transition-all focus:outline-none group`;
                    btn.innerHTML = `
                        <span class="text-[10px] font-bold w-12 text-center py-1 mt-0.5 rounded shadow-sm border ${methodClass}">${ep.method}</span>
                        <div class="flex-1 overflow-hidden">
                            <div class="text-[13px] font-mono font-medium text-slate-300 truncate group-hover:text-white transition-colors tracking-tight">${ep.path}</div>
                            <div class="text-xs text-slate-500 truncate mt-1 group-hover:text-slate-400 transition-colors">${ep.desc}</div>
                        </div>
                    `;
                    btn.onclick = () => selectApi(ep, btn);
                    endpointsList.appendChild(btn);
                });
                
                groupDiv.appendChild(endpointsList);
                sidebar.appendChild(groupDiv);
            });
            
            if (sidebar.innerHTML === '') {
                sidebar.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-10 opacity-50">
                        <svg class="w-8 h-8 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-xs text-slate-400">Không tìm thấy API</p>
                    </div>
                `;
            }
        }

        // Event Listeners
        document.getElementById('searchInput').addEventListener('input', (e) => {
            renderSidebar(e.target.value);
        });

        function getMethodClass(method) {
            return `method-${method.toLowerCase()}`;
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function selectApi(api, el) {
            currentApi = api;
            if (isSending) return;
            
            // Highlight active in sidebar
            document.querySelectorAll('#apiSidebar button').forEach(b => {
                b.classList.remove('bg-indigo-500/10', 'border-indigo-500/30');
            });
            el.classList.add('bg-indigo-500/10', 'border-indigo-500/30');

            // Switch view
            document.getElementById('emptyState').classList.add('hidden');
            const apiContent = document.getElementById('apiContent');
            apiContent.classList.remove('hidden');
            
            // Animate entrance
            apiContent.style.opacity = '0';
            setTimeout(() => { apiContent.style.opacity = '1'; apiContent.style.transition = 'opacity 0.3s ease'; }, 10);

            // Set Title & Header
            const methodEl = document.getElementById('apiMethod');
            methodEl.textContent = api.method;
            methodEl.className = `px-3 py-1 font-bold rounded text-sm border shadow-sm ${getMethodClass(api.method)}`;
            document.getElementById('apiPath').textContent = api.path;
            document.getElementById('apiDesc').textContent = api.desc;

            // Reset Response State
            document.getElementById('responseContainer').classList.remove('opacity-100');
            document.getElementById('responseCode').textContent = '';
            document.getElementById('resStatus').classList.add('hidden');
            document.getElementById('resTime').classList.add('hidden');
            document.getElementById('resSize').classList.add('hidden');
            document.getElementById('responseEmpty').classList.remove('hidden');

            // Set Parameters
            const paramsContainer = document.getElementById('pathParamsContainer');
            const paramsList = document.getElementById('pathParamsList');
            
            if (api.params && api.params.length > 0) {
                paramsContainer.classList.remove('hidden');
                paramsList.innerHTML = api.params.map(p => `
                    <div class="flex items-center gap-3">
                        <label class="w-1/3 text-sm text-slate-300 font-mono font-medium truncate">{${p}}}</label>
                        <input type="text" data-param="${p}" placeholder="Giá trị..." class="flex-1 bg-slate-950/50 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-slate-300 placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all font-mono" onkeypress="if(event.key === 'Enter') document.getElementById('sendBtn').click()">
                    </div>
                `).join('');
            } else {
                paramsContainer.classList.add('hidden');
            }

            // Set Request Body
            const bodyContainer = document.getElementById('bodyContainer');
            const bd = document.getElementById('requestBody');
            
            if (api.method !== 'GET' && api.method !== 'DELETE') {
                bodyContainer.classList.remove('hidden');
                bodyContainer.classList.add('flex');
                bd.value = api.body || '';
                formatBody(); // auto format if invalid
            } else {
                bodyContainer.classList.add('hidden');
                bodyContainer.classList.remove('flex');
            }
        }

        function formatBody() {
            const bd = document.getElementById('requestBody');
            try {
                if (bd.value.trim() !== '') {
                    const parsed = JSON.parse(bd.value);
                    bd.value = JSON.stringify(parsed, null, 2);
                    bd.classList.remove('border-red-500/50', 'ring-red-500');
                }
            } catch (e) {
                // Flash red briefly to indicate error
                bd.classList.add('border-red-500/50', 'ring-red-500', 'ring-1');
                setTimeout(() => bd.classList.remove('border-red-500/50', 'ring-red-500', 'ring-1'), 500);
            }
        }

        // Send logic
        document.getElementById('sendBtn').addEventListener('click', async () => {
            if (!currentApi || isSending) return;

            // Parse path mapping
            let finalPath = currentApi.path;
            let isValidParams = true;
            
            if (currentApi.params && currentApi.params.length > 0) {
                document.querySelectorAll('#pathParamsList input').forEach(input => {
                    const param = input.getAttribute('data-param');
                    const val = input.value.trim();
                    if (!val) {
                        isValidParams = false;
                        input.classList.add('border-red-500', 'ring-1', 'ring-red-500/50');
                    } else {
                        input.classList.remove('border-red-500', 'ring-1', 'ring-red-500/50');
                        finalPath = finalPath.replace(`{${param}}`, val);
                    }
                });
            }

            if (!isValidParams) {
                return;
            }

            const reqBodyEl = document.getElementById('requestBody');
            let bodyData = null;

            if (currentApi.method !== 'GET' && currentApi.method !== 'DELETE') {
                const bd = reqBodyEl.value.trim();
                if (bd) {
                    try {
                        JSON.parse(bd);
                        bodyData = bd;
                        reqBodyEl.classList.remove('border-red-500/50', 'ring-1', 'ring-red-500');
                    } catch (e) {
                        reqBodyEl.classList.add('border-red-500/50', 'ring-1', 'ring-red-500');
                        return; // invalid json
                    }
                }
            }

            // UI updating for send process
            isSending = true;
            const btn = document.getElementById('sendBtn');
            const originalBtnText = btn.innerHTML;
            btn.innerHTML = `
                <span class="flex items-center gap-2">
                    <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang Gửi...
                </span>
            `;
            btn.classList.add('opacity-80', 'cursor-not-allowed');

            const loading = document.getElementById('loading');
            document.getElementById('responseEmpty').classList.add('hidden');
            document.getElementById('responseContainer').classList.remove('opacity-100');
            loading.classList.remove('hidden');

            // Fetch Setup
            const token = document.getElementById('bearerToken').value.trim();
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const options = { method: currentApi.method, headers };
            if (bodyData) options.body = bodyData;

            const startTime = performance.now();
            let statusClass = '';

            try {
                const response = await fetch(finalPath, options);
                const endTime = performance.now();
                const duration = Math.round(endTime - startTime);
                
                // Get Response Data
                const dataText = await response.text();
                const sizeBytes = new Blob([dataText]).size;
                
                let formattedOutput = dataText;
                try {
                    if(dataText.trim() !== '') {
                         formattedOutput = JSON.stringify(JSON.parse(dataText), null, 2);
                    }
                } catch(e) {}

                // Handle Status
                const statusStr = `${response.status} ${response.statusText}`;
                if (response.ok) {
                    statusClass = 'bg-green-500/10 border-green-500/20 text-green-400 border font-bold';
                } else if (response.status >= 400 && response.status < 500) {
                    statusClass = 'bg-orange-500/10 border-orange-500/20 text-orange-400 border font-bold';
                } else {
                    statusClass = 'bg-red-500/10 border-red-500/20 text-red-400 border font-bold';
                }

                showResponse(statusStr, statusClass, duration, sizeBytes, formattedOutput);

            } catch (error) {
                // Connection or abort errors
                const duration = Math.round(performance.now() - startTime);
                showResponse('Network Error', 'bg-red-500/10 border-red-500/20 text-red-500 border font-bold mb-0', duration, 0, String(error.message || error));
            } finally {
                // Restore Button
                isSending = false;
                btn.innerHTML = originalBtnText;
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
                loading.classList.add('hidden');
            }
        });

        function showResponse(statusText, statusClass, timeMs, sizeBytes, bodyText) {
            // Update Headers
            const resStatus = document.getElementById('resStatus');
            resStatus.textContent = statusText;
            resStatus.className = `px-2.5 py-1 rounded shadow-sm text-xs ${statusClass}`;
            resStatus.classList.remove('hidden');

            const resTime = document.getElementById('resTime');
            resTime.textContent = `${timeMs} ms`;
            resTime.classList.remove('hidden');
            if (timeMs > 800) { resTime.classList.add('text-orange-400'); resTime.classList.remove('text-slate-400'); }
            else { resTime.classList.add('text-slate-400'); resTime.classList.remove('text-orange-400'); }

            const resSize = document.getElementById('resSize');
            resSize.textContent = formatBytes(sizeBytes);
            resSize.classList.remove('hidden');

            // Render Body
            const codeBlock = document.getElementById('responseCode');
            codeBlock.textContent = bodyText;
            Prism.highlightElement(codeBlock);
            
            const container = document.getElementById('responseContainer');
            container.classList.add('opacity-100');
        }

        // Init App On Load
        window.addEventListener('DOMContentLoaded', () => {
            renderSidebar();
            
            // Format textarea inside visually
            const txt = document.getElementById('requestBody');
            txt.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    let start = this.selectionStart;
                    let end = this.selectionEnd;
                    this.value = this.value.substring(0, start) + "\t" + this.value.substring(end);
                    this.selectionStart = this.selectionEnd = start + 1;
                }
            });
        });

    </script>
</body>
</html>
