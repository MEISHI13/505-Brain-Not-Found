<?php
// Check if auth file exists, create if not
if (!file_exists('includes/auth.php')) {
    // Create the auth file
    if (!is_dir('includes')) {
        mkdir('includes', 0755, true);
    }
    $authContent = '<?php
function requireLogin() {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
}

function getUser() {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Return user data from session or default
    return [
        "full_name" => $_SESSION["full_name"] ?? "Demo User",
        "role" => $_SESSION["role"] ?? "Admin",
        "email" => $_SESSION["email"] ?? "demo@example.com"
    ];
}
?>';
    file_put_contents('includes/auth.php', $authContent);
}

require_once 'includes/auth.php';
requireLogin();

$user = getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoStream - Comprehensive Resource Management Hub</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* ============================================ */
        /* LIGHT/DARK MODE VARIABLES */
        /* ============================================ */
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #0f172a;
            --bg-input: #1e293b;
            --bg-hover: rgba(30, 41, 59, 0.5);
            --bg-sidebar: #0f172a;
            --bg-sidebar-hover: #1e293b;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: #1e293b;
            --border-light: #334155;
            --shadow-color: rgba(0,0,0,0.5);
            --toast-bg: #1e293b;
            --toast-border: #334155;
            --chart-grid: #1e293b;
            --chart-text: #64748b;
            --scrollbar-track: #1e293b;
            --scrollbar-thumb: #22d3ee;
            --card-hover: rgba(30, 41, 59, 0.5);
            --input-bg: #1e293b;
            --input-border: #334155;
            --input-text: #e2e8f0;
            --dropdown-bg: #1e293b;
            --dropdown-text: #e2e8f0;
            --alert-bg-warning: rgba(245, 158, 11, 0.15);
            --alert-border-warning: rgba(245, 158, 11, 0.4);
            --alert-bg-critical: rgba(239, 68, 68, 0.15);
            --alert-border-critical: rgba(239, 68, 68, 0.4);
        }

        [data-theme="light"] {
            --bg-primary: #f1f5f9;
            --bg-secondary: #e2e8f0;
            --bg-card: #ffffff;
            --bg-input: #f1f5f9;
            --bg-hover: rgba(226, 232, 240, 0.8);
            --bg-sidebar: #ffffff;
            --bg-sidebar-hover: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-light: #cbd5e1;
            --shadow-color: rgba(0,0,0,0.1);
            --toast-bg: #ffffff;
            --toast-border: #e2e8f0;
            --chart-grid: #e2e8f0;
            --chart-text: #64748b;
            --scrollbar-track: #e2e8f0;
            --scrollbar-thumb: #22d3ee;
            --card-hover: rgba(226, 232, 240, 0.8);
            --input-bg: #f1f5f9;
            --input-border: #cbd5e1;
            --input-text: #0f172a;
            --dropdown-bg: #ffffff;
            --dropdown-text: #0f172a;
            --alert-bg-warning: rgba(245, 158, 11, 0.1);
            --alert-border-warning: rgba(245, 158, 11, 0.3);
            --alert-bg-critical: rgba(239, 68, 68, 0.63);
            --alert-border-critical: rgba(239, 68, 68, 0.3);
        }

        /* ============================================ */
        /* BASE STYLES */
        /* ============================================ */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        /* Sidebar */
        aside {
            background: var(--bg-sidebar) !important;
            border-color: var(--border-color) !important;
        }

        aside .bg-slate-900 {
            background: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
        }

        aside nav button {
            color: var(--text-secondary) !important;
        }
        aside nav button:hover {
            background: var(--bg-sidebar-hover) !important;
            color: var(--text-primary) !important;
        }
        aside nav button.bg-cyan-950\/50 {
            background: rgba(34, 211, 238, 0.15) !important;
            color: #22d3ee !important;
            border-color: rgba(34, 211, 238, 0.3) !important;
        }

        /* Cards */
        .bg-slate-950, .bg-slate-900, .bg-slate-800 {
            background: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        .bg-slate-900\/30, .bg-slate-900\/50 {
            background: var(--bg-hover) !important;
        }

        /* Text colors */
        .text-slate-100, .text-slate-200, .text-slate-300 {
            color: var(--text-primary) !important;
        }
        .text-slate-400, .text-slate-500 {
            color: var(--text-secondary) !important;
        }
        .text-slate-600, .text-slate-700 {
            color: var(--text-muted) !important;
        }

        /* Borders */
        .border-slate-800, .border-slate-700 {
            border-color: var(--border-color) !important;
        }

        /* Form inputs */
        .form-input, .form-select {
            background: var(--input-bg) !important;
            border-color: var(--input-border) !important;
            color: var(--input-text) !important;
        }
        .form-input::placeholder {
            color: var(--text-muted) !important;
        }

        /* Toast */
        #toastContainer .toast {
            background: var(--toast-bg);
            border-color: var(--toast-border);
            color: var(--text-primary);
            box-shadow: 0 10px 40px var(--shadow-color);
        }

        /* Custom scrollbar */
        #alerts-container::-webkit-scrollbar {
            width: 4px;
        }
        #alerts-container::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 10px;
        }
        #alerts-container::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
        }

        .tab-view {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .status-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        
        /* ============================================ */
        /* TOAST NOTIFICATIONS - STACKED/PUSH UP */
        /* ============================================ */
        #toastContainer {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            max-width: 420px;
            width: 100%;
            pointer-events: none;
        }
        
        .toast {
            background: var(--toast-bg);
            border: 1px solid var(--toast-border);
            padding: 16px 24px;
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 14px;
            box-shadow: 0 10px 40px var(--shadow-color);
            pointer-events: auto;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            width: 100%;
            position: relative;
        }
        
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .toast.hiding {
            transform: translateX(120%);
            opacity: 0;
        }
        
        .toast i {
            margin-right: 10px;
            font-size: 18px;
        }
        .toast.success i { color: #10b981; }
        .toast.error i { color: #ef4444; }
        .toast.info i { color: #22d3ee; }
        
        .toast .toast-close {
            position: absolute;
            top: 8px;
            right: 12px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            transition: color 0.2s;
        }
        .toast .toast-close:hover {
            color: var(--text-primary);
        }
        
        .toast .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(34, 211, 238, 0.3);
            border-radius: 0 0 12px 12px;
            transition: width 0.1s linear;
        }
        .toast.success .toast-progress { background: rgba(16, 185, 129, 0.77); }
        .toast.error .toast-progress { background: rgba(239, 68, 68, 0.73); }
        .toast.info .toast-progress { background: rgba(34, 211, 238, 0.61); }
        
        /* Table hover effect */
        #device-rows tr:hover {
            background: var(--card-hover);
            transition: background 0.2s;
        }
        
        /* Alert badge pulse */
        .alert-badge {
            animation: alertPulse 1.5s infinite;
        }
        @keyframes alertPulse {
            0%, 100% { background-color: #ef4444; }
            50% { background-color: #dc2626; }
        }
        
        /* Button hover transitions */
        .transition-all {
            transition: all 0.2s ease;
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: all 0.3s ease;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        .modal-content::-webkit-scrollbar {
            width: 4px;
        }
        .modal-content::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
        }
        
        .form-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--input-text);
            width: 100%;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #22d3ee;
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.45);
        }
        .form-input::placeholder {
            color: var(--text-muted);
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-select {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 10px 14px;
            color: var(--input-text);
            width: 100%;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }
        .form-select:focus {
            outline: none;
            border-color: #22d3ee;
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.43);
        }
        
        .btn-primary {
            background: #22d3ee;
            color: #313847;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #06b6d4;
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(34, 211, 238, 0.55);
        }
        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 10px;
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: var(--border-light);
            color: var(--text-primary);
        }
        .btn-success {
            background: #10b981;
            color: #0f172a;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }
        .btn-danger {
            background: #ef4444;
            color: white;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        .page-content {
            display: block;
        }
        .page-content.hidden {
            display: none !important;
        }
        
        /* Live mode indicator */
        .live-mode-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .live-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: livePulse 1.2s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.8); }
        }
        
        /* Row update animation */
        .row-update {
            animation: rowFlash 0.6s ease;
        }
        @keyframes rowFlash {
            0% { background: rgba(34, 211, 238, 0.49); }
            100% { background: transparent; }
        }

        /* Alert count badge pulse */
        .alert-count-pulse {
            animation: alertCountPulse 1.5s ease-in-out infinite;
        }
        @keyframes alertCountPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Persistent alert style */
        .persistent-alert {
            border-left: 4px solid #ef4444;
        }

        /* ============================================ */
        /* THEME TOGGLE BUTTON */
        /* ============================================ */
        .theme-toggle {
            position: relative;
            width: 52px;
            height: 28px;
            background: var(--bg-secondary);
            border: 2px solid var(--border-light);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            padding: 2px;
            flex-shrink: 0;
        }
        .theme-toggle .toggle-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f59e0b;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .theme-toggle.dark .toggle-thumb {
            transform: translateX(24px);
            background: #22d3ee;
        }
        .theme-toggle .toggle-icon {
            position: absolute;
            font-size: 12px;
            transition: opacity 0.3s ease;
        }
        .theme-toggle .icon-sun {
            left: 6px;
            opacity: 1;
        }
        .theme-toggle .icon-moon {
            right: 6px;
            opacity: 0.3;
        }
        .theme-toggle.dark .icon-sun {
            opacity: 0.3;
        }
        .theme-toggle.dark .icon-moon {
            opacity: 1;
        }

        /* ============================================ */
        /* CHART COLORS FOR LIGHT MODE */
        /* ============================================ */
        [data-theme="light"] canvas {
            filter: brightness(0.95);
        }

        /* ============================================ */
        /* RESPONSIVE ADJUSTMENTS */
        /* ============================================ */
        @media (max-width: 768px) {
            .theme-toggle {
                width: 44px;
                height: 24px;
            }
            .theme-toggle .toggle-thumb {
                width: 18px;
                height: 18px;
                font-size: 10px;
            }
            .theme-toggle.dark .toggle-thumb {
                transform: translateX(20px);
            }
        }

        /* Alert styles with theme support */
        .bg-rose-950\/20 {
            background: var(--alert-bg-critical) !important;
            border-color: var(--alert-border-critical) !important;
        }
        .bg-amber-950\/20 {
            background: var(--alert-bg-warning) !important;
            border-color: var(--alert-border-warning) !important;
        }
        .text-rose-300 {
            color: var(--text-primary) !important;
        }
        .text-amber-300 {
            color: var(--text-primary) !important;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 p-6 flex flex-col justify-between shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-10">
                <i data-lucide="zap" class="text-cyan-400 w-8 h-8"></i>
                <span class="text-xl font-bold tracking-wider text-cyan-400">ECOSTREAM</span>
            </div>
            
            <div class="p-4 bg-slate-900 border border-slate-800 rounded-xl mb-6">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">User Status</p>
                <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span> 
                    <?php echo htmlspecialchars($user['full_name'] ?? 'Demo User'); ?>
                </div>
                <div class="text-[10px] text-slate-500 mt-1"><?php echo htmlspecialchars($user['role'] ?? 'Admin'); ?></div>
            </div>

            <nav class="space-y-2">
                <button onclick="showPage('dashboard')" id="btn-dashboard" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-cyan-950/50 text-cyan-400 border border-cyan-800/50 font-medium text-left cursor-pointer transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </button>
                <button onclick="showPage('analytics')" id="btn-analytics" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-900 hover:text-slate-200 text-left cursor-pointer transition-all">
                    <i data-lucide="chart-line" class="w-5 h-5"></i> Analytics
                </button>
                <button onclick="showPage('alerts')" id="btn-alerts" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-900 hover:text-slate-200 text-left cursor-pointer transition-all">
                    <i data-lucide="bell" class="w-5 h-5"></i> Alerts
                    <span class="ml-auto bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full alert-count-pulse" id="sidebarAlertCount">0</span>
                </button>
                <button onclick="showPage('data')" id="btn-data" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-900 hover:text-slate-200 text-left cursor-pointer transition-all">
                    <i data-lucide="table" class="w-5 h-5"></i> Data Logs
                </button>
                <button onclick="showPage('settings')" id="btn-settings" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-900 hover:text-slate-200 text-left cursor-pointer transition-all">
                    <i data-lucide="settings" class="w-5 h-5"></i> Settings
                </button>
            </nav>
        </div>
        <div class="text-xs text-slate-500 border-t border-slate-800 pt-4 flex justify-between items-center">
            <span>ResourCity © 2026</span>
            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
                    <span class="toggle-icon icon-sun">☀️</span>
                    <span class="toggle-icon icon-moon">🌙</span>
                    <div class="toggle-thumb" id="toggleThumb">☀️</div>
                </div>
                <button onclick="handleLogout()" class="text-rose-400 hover:text-rose-300 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        
        <!-- Top Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight" id="pageTitle">Dashboard</h1>
                <p class="text-slate-400" id="pageSubtitle">Energy monitoring and management</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <span class="live-mode-indicator">
                    <span class="live-dot"></span>
                    LIVE
                </span>
                <span class="text-xs bg-emerald-900/40 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-800/40 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full status-dot"></span>
                    Manual Resolve Only
                </span>
                <span id="lastUpdate" class="text-xs text-slate-400 flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i> just now
                </span>
                <button onclick="refreshData()" class="bg-slate-800 hover:bg-slate-700 text-white p-2 rounded-xl transition-all cursor-pointer" title="Refresh">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </button>
                <button onclick="openAddModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl transition-all cursor-pointer text-sm font-semibold flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add
                </button>
                <button onclick="exportData()" class="bg-cyan-600 hover:bg-cyan-500 text-white px-4 py-2 rounded-xl transition-all cursor-pointer text-sm font-semibold flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </button>
            </div>
        </header>

        <!-- ============================================ -->
        <!-- PAGE: DASHBOARD -->
        <!-- ============================================ -->
        <div id="page-dashboard" class="page-content tab-view">
            <!-- Summary Cards -->
            <section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Consumption</p>
                        <h3 class="text-2xl font-bold" id="totalConsumption">0 <span class="text-sm text-slate-500">kWh</span></h3>
                    </div>
                    <div class="p-3 bg-cyan-950/40 rounded-xl text-cyan-400"><i data-lucide="bolt"></i></div>
                </div>
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Average Usage</p>
                        <h3 class="text-2xl font-bold" id="avgConsumption">0 <span class="text-sm text-slate-500">kWh</span></h3>
                    </div>
                    <div class="p-3 bg-purple-950/40 rounded-xl text-purple-400"><i data-lucide="chart-bar"></i></div>
                </div>
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Active Alerts</p>
                        <h3 class="text-2xl font-bold text-rose-500 alert-count-pulse" id="activeAlerts">0</h3>
                    </div>
                    <div class="p-3 bg-rose-950/40 rounded-xl text-rose-400"><i data-lucide="alert-triangle"></i></div>
                </div>
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Records</p>
                        <h3 class="text-2xl font-bold" id="totalRecords">0</h3>
                    </div>
                    <div class="p-3 bg-emerald-950/40 rounded-xl text-emerald-400"><i data-lucide="database"></i></div>
                </div>
            </section>

            <!-- Charts - Removed Building Comparison Chart -->
            <section class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <i data-lucide="chart-area" class="text-cyan-400"></i> Consumption Trends
                    </h3>
                    <div class="h-72">
                        <canvas id="usageChartCanvas"></canvas>
                    </div>
                </div>
            </section>

            <!-- Alerts & Activity -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <i data-lucide="bell" class="text-rose-400"></i> Active Alerts
                            <span class="ml-2 text-xs bg-rose-950/40 text-rose-400 px-2 py-0.5 rounded-full alert-count-pulse" id="alertCount">0</span>
                        </h3>
                        <span class="text-xs text-amber-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                            Manual resolve only
                        </span>
                    </div>
                    <div id="alertsContainer" class="space-y-3 max-h-64 overflow-y-auto pr-1"></div>
                </div>

                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <i data-lucide="clock" class="text-cyan-400"></i> Live Activity
                        </h3>
                        <span class="text-xs bg-emerald-900/40 text-emerald-400 px-2 py-0.5 rounded-full">Auto</span>
                    </div>
                    <div id="activityContainer" class="space-y-2 max-h-64 overflow-y-auto pr-1"></div>
                </div>
            </section>
        </div>

        <!-- ============================================ -->
        <!-- PAGE: ANALYTICS -->
        <!-- ============================================ -->
        <div id="page-analytics" class="page-content tab-view hidden">
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="chart-line" class="text-cyan-400"></i> Detailed Analytics
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Peak Hours</p>
                        <p class="text-lg font-bold text-cyan-400 mt-1" id="peakHours">9:00 AM - 5:00 PM</p>
                    </div>
                    <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Most Active Building</p>
                        <p class="text-lg font-bold text-emerald-400 mt-1" id="mostActiveBuilding">Loading...</p>
                    </div>
                    <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Total Anomalies</p>
                        <p class="text-lg font-bold text-rose-400 mt-1" id="totalAnomalies">0</p>
                    </div>
                    <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Avg Power Factor</p>
                        <p class="text-lg font-bold text-purple-400 mt-1" id="avgPowerFactor">0.00</p>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="analyticsChartCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PAGE: ALERTS -->
        <!-- ============================================ -->
        <div id="page-alerts" class="page-content tab-view hidden">
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i data-lucide="bell" class="text-rose-400"></i> All Alerts
                        <span class="ml-2 text-xs bg-rose-950/40 text-rose-400 px-2 py-0.5 rounded-full alert-count-pulse" id="allAlertsCount">0</span>
                    </h2>
                    <div class="flex gap-2">
                        <button class="filter-btn px-3 py-1.5 rounded-lg bg-cyan-950/50 text-cyan-400 border border-cyan-800/50 text-sm font-medium" onclick="filterAlerts('all', event)">All</button>
                        <button class="filter-btn px-3 py-1.5 rounded-lg text-slate-400 hover:bg-slate-900 text-sm font-medium" onclick="filterAlerts('high', event)">High</button>
                        <button class="filter-btn px-3 py-1.5 rounded-lg text-slate-400 hover:bg-slate-900 text-sm font-medium" onclick="filterAlerts('medium', event)">Medium</button>
                        <button class="filter-btn px-3 py-1.5 rounded-lg text-slate-400 hover:bg-slate-900 text-sm font-medium" onclick="filterAlerts('low', event)">Low</button>
                    </div>
                </div>
                <div id="allAlertsContainer" class="space-y-3"></div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PAGE: DATA LOGS -->
        <!-- ============================================ -->
        <div id="page-data" class="page-content tab-view hidden">
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i data-lucide="table" class="text-cyan-400"></i> All Data Logs
                    </h2>
                    <div class="flex gap-3">
                        <input type="text" id="searchInput" placeholder="Search..." class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-300 placeholder-slate-500 focus:outline-none focus:border-cyan-500 w-48">
                        <select id="buildingFilter" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 cursor-pointer focus:outline-none focus:border-cyan-500">
                            <option value="">All Buildings</option>
                            <option value="Tower A">Tower A</option>
                            <option value="Tower B">Tower B</option>
                            <option value="Tower C">Tower C</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 font-medium">
                                <th class="pb-3">ID</th>
                                <th class="pb-3">Building</th>
                                <th class="pb-3">Floor</th>
                                <th class="pb-3">Department</th>
                                <th class="pb-3">Consumption</th>
                                <th class="pb-3">Voltage</th>
                                <th class="pb-3">Current</th>
                                <th class="pb-3">Time</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="allDataTableBody" class="divide-y divide-slate-800/40">
                            <tr><td colspan="9" class="py-8 text-center text-slate-500">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PAGE: SETTINGS -->
        <!-- ============================================ -->
        <div id="page-settings" class="page-content tab-view hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                    <h3 class="text-lg font-semibold text-cyan-400 mb-4 flex items-center gap-2">
                        <i data-lucide="zap" class="w-5 h-5"></i> Dynamic Mode
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-slate-800">
                            <span class="text-slate-400 text-sm">Status</span>
                            <span class="text-emerald-400 text-sm font-semibold flex items-center gap-1.5">
                                <span class="live-dot"></span> Active
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-800">
                            <span class="text-slate-400 text-sm">Update Interval</span>
                            <span class="text-slate-200 text-sm">2 seconds</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-400 text-sm">Fixed Records</span>
                            <span class="text-slate-200 text-sm">13 records</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                    <h3 class="text-lg font-semibold text-amber-400 mb-4 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i> Alert Settings
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-slate-800">
                            <span class="text-slate-400 text-sm">Auto-Resolve</span>
                            <span class="text-amber-400 text-sm font-semibold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Disabled
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-800">
                            <span class="text-slate-400 text-sm">Resolution Method</span>
                            <span class="text-slate-200 text-sm">Manual Only</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-400 text-sm">Alert Persistence</span>
                            <span class="text-emerald-400 text-sm font-semibold">✓ Always Stays</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                        <i data-lucide="server" class="w-5 h-5"></i> System
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-500 font-medium uppercase tracking-wide block">Database</span>
                            <div class="text-sm font-semibold text-emerald-400 mt-1 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full status-dot"></span> Connected
                            </div>
                        </div>
                        <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-500 font-medium uppercase tracking-wide block">Total Records</span>
                            <div class="text-sm font-bold text-cyan-400 mt-1" id="totalRecordsSettings">0</div>
                        </div>
                        <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-500 font-medium uppercase tracking-wide block">Last Generated</span>
                            <div class="text-sm text-slate-200 mt-1" id="lastGenerated">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- ============================================ -->
    <!-- TOAST CONTAINER - STACKED NOTIFICATIONS -->
    <!-- ============================================ -->
    <div id="toastContainer"></div>

    <!-- ============================================ -->
    <!-- MODALS -->
    <!-- ============================================ -->
    
    <!-- Add Data Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <i data-lucide="plus-circle" class="text-cyan-400"></i> Add Energy Data
                </h2>
                <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="addDataForm" class="space-y-4">
                <div>
                    <label class="form-label">Building Name *</label>
                    <select id="buildingName" class="form-select" required>
                        <option value="Tower A">Tower A</option>
                        <option value="Tower B">Tower B</option>
                        <option value="Tower C">Tower C</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Floor</label>
                    <input type="text" id="floor" class="form-input" placeholder="e.g., Floor 1">
                </div>
                <div>
                    <label class="form-label">Department</label>
                    <input type="text" id="department" class="form-input" placeholder="e.g., IT">
                </div>
                <div>
                    <label class="form-label">Consumption (kWh) *</label>
                    <input type="number" id="consumption" step="0.01" class="form-input" placeholder="45.5" required>
                </div>
                <div>
                    <label class="form-label">Voltage (V)</label>
                    <input type="number" id="voltage" step="0.1" class="form-input" placeholder="220.5">
                </div>
                <div>
                    <label class="form-label">Current (A)</label>
                    <input type="number" id="current" step="0.1" class="form-input" placeholder="12.3">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAddModal()" class="btn-secondary flex-1">Cancel</button>
                    <button type="submit" class="btn-success flex-1">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ============================================
        // THEME TOGGLE
        // ============================================
        function getTheme() {
            return localStorage.getItem('theme') || 'dark';
        }

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            const toggle = document.getElementById('themeToggle');
            const thumb = document.getElementById('toggleThumb');
            
            if (theme === 'light') {
                toggle.classList.add('dark');
                thumb.textContent = '🌙';
            } else {
                toggle.classList.remove('dark');
                thumb.textContent = '☀️';
            }
        }

        function toggleTheme() {
            const current = getTheme();
            const newTheme = current === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
            
            // Update chart colors if charts exist
            if (window.usageChart) {
                window.usageChart.update();
            }
            if (window.analyticsChart) {
                window.analyticsChart.update();
            }
        }

        // Initialize theme
        setTheme(getTheme());

        // ============================================
        // MAIN SCRIPT
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            // ============================================
            // TOAST SYSTEM - STACKED/PUSH UP with SLOWER TIMING
            // ============================================
            let toastCounter = 0;
            const toastContainer = document.getElementById('toastContainer');
            
            function showToast(message, type = 'info', duration = 8000) {
                const id = ++toastCounter;
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.id = `toast-${id}`;
                
                const icons = {
                    success: 'check-circle',
                    error: 'alert-circle',
                    info: 'info'
                };
                
                const icon = icons[type] || 'info';
                
                toast.innerHTML = `
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i data-lucide="${icon}" class="w-5 h-5"></i>
                        <span style="flex:1;">${message}</span>
                    </div>
                    <button class="toast-close" onclick="closeToast(${id})">✕</button>
                    <div class="toast-progress" style="width:100%;"></div>
                `;
                
                toastContainer.appendChild(toast);
                lucide.createIcons();
                
                setTimeout(() => {
                    toast.classList.add('show');
                }, 50);
                
                const progress = toast.querySelector('.toast-progress');
                let startTime = Date.now();
                const durationMs = duration;
                
                function updateProgress() {
                    const elapsed = Date.now() - startTime;
                    const remaining = Math.max(0, 1 - (elapsed / durationMs));
                    if (progress) {
                        progress.style.width = (remaining * 100) + '%';
                    }
                    if (elapsed < durationMs) {
                        requestAnimationFrame(updateProgress);
                    }
                }
                setTimeout(() => {
                    requestAnimationFrame(updateProgress);
                }, 100);
                
                const timeoutId = setTimeout(() => {
                    closeToast(id);
                }, duration);
                
                toast._timeoutId = timeoutId;
                
                return id;
            }
            
            function closeToast(id) {
                const toast = document.getElementById(`toast-${id}`);
                if (!toast) return;
                
                if (toast._timeoutId) {
                    clearTimeout(toast._timeoutId);
                }
                
                toast.classList.remove('show');
                toast.classList.add('hiding');
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 600);
            }
            
            window.closeToast = closeToast;
            window.showToast = showToast;

            // ============================================
            // STATE - Persistent with localStorage
            // ============================================
            let allData = [];
            let usageChart = null;
            let analyticsChart = null;
            let dynamicInterval = null;
            let alertTimeout = null;
            let alertHistory = [];
            const FIXED_RECORD_COUNT = 13;
            const UPDATE_INTERVAL = 2000;
            const ALERT_THRESHOLD = 100;

            function loadAlertHistory() {
                try {
                    const saved = localStorage.getItem('alertHistory');
                    if (saved) {
                        alertHistory = JSON.parse(saved);
                    } else {
                        alertHistory = [];
                    }
                } catch (e) {
                    alertHistory = [];
                }
            }

            function saveAlertHistory() {
                try {
                    localStorage.setItem('alertHistory', JSON.stringify(alertHistory));
                } catch (e) {
                    console.error('Error saving alert history:', e);
                }
            }

            loadAlertHistory();

            // ============================================
            // GENERATE STATIC RECORDS
            // ============================================
            function generateStaticRecords() {
                const buildings = ['Tower A', 'Tower B', 'Tower C'];
                const departments = ['IT', 'Finance', 'HR', 'Operations', 'Sales', 'Marketing'];
                const floors = ['Floor 1', 'Floor 2', 'Floor 3', 'Floor 4', 'Floor 5'];
                
                const records = [];
                for (let i = 1; i <= FIXED_RECORD_COUNT; i++) {
                    const consumption = 20 + Math.random() * 130;
                    records.push({
                        id: i,
                        building_name: buildings[Math.floor(Math.random() * buildings.length)],
                        floor: floors[Math.floor(Math.random() * floors.length)],
                        department: departments[Math.floor(Math.random() * departments.length)],
                        consumption_kwh: consumption,
                        voltage: 210 + Math.random() * 30,
                        current_amps: 5 + Math.random() * 15,
                        power_factor: 0.75 + Math.random() * 0.2,
                        temperature: 20 + Math.random() * 10,
                        timestamp: new Date().toLocaleString()
                    });
                }
                return records;
            }

            // ============================================
            // CHECK AND CREATE ALERTS
            // ============================================
            function checkAndCreateAlerts(records) {
                records.forEach(item => {
                    const consumption = parseFloat(item.consumption_kwh || 0);
                    
                    if (consumption > ALERT_THRESHOLD) {
                        const existingAlert = alertHistory.find(a => 
                            a.record_id === item.id && !a.resolved
                        );
                        
                        if (!existingAlert) {
                            const newAlert = {
                                record_id: item.id,
                                building_name: item.building_name,
                                floor: item.floor,
                                department: item.department,
                                consumption: consumption,
                                timestamp: item.timestamp || new Date().toLocaleString(),
                                triggered_at: new Date().toISOString(),
                                resolved: false,
                                resolved_at: null,
                                current_consumption: consumption
                            };
                            alertHistory.push(newAlert);
                            showToast(`⚠️ ALERT: High consumption (${consumption.toFixed(1)} kWh) at ${item.building_name}`, 'error', 10000);
                        } else {
                            existingAlert.current_consumption = consumption;
                            existingAlert.timestamp = item.timestamp || new Date().toLocaleString();
                        }
                    }
                });

                saveAlertHistory();
            }

            // ============================================
            // UPDATE RECORDS WITH NEW RANDOM VALUES
            // ============================================
            function updateRecordsWithNewValues(records) {
                return records.map(record => {
                    const newConsumption = 20 + Math.random() * 130;
                    return {
                        ...record,
                        consumption_kwh: newConsumption,
                        voltage: 210 + Math.random() * 30,
                        current_amps: 5 + Math.random() * 15,
                        power_factor: 0.75 + Math.random() * 0.2,
                        temperature: 20 + Math.random() * 10,
                        timestamp: new Date().toLocaleString()
                    };
                });
            }

            // ============================================
            // DYNAMIC UPDATE LOOP
            // ============================================
            function dynamicUpdate() {
                allData = updateRecordsWithNewValues(allData);
                checkAndCreateAlerts(allData);
                renderAll();
                updateLastUpdate();
            }

            // ============================================
            // PAGE NAVIGATION
            // ============================================
            window.showPage = function(page) {
                document.querySelectorAll('.page-content').forEach(el => {
                    el.classList.add('hidden');
                });
                
                const target = document.getElementById(`page-${page}`);
                if (target) {
                    target.classList.remove('hidden');
                }
                
                document.querySelectorAll('nav button').forEach(btn => {
                    btn.className = 'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-900 hover:text-slate-200 text-left cursor-pointer transition-all';
                });
                
                const btnMap = {
                    'dashboard': 'btn-dashboard',
                    'analytics': 'btn-analytics',
                    'alerts': 'btn-alerts',
                    'data': 'btn-data',
                    'settings': 'btn-settings'
                };
                
                const activeBtn = document.getElementById(btnMap[page]);
                if (activeBtn) {
                    activeBtn.className = 'w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-cyan-950/50 text-cyan-400 border border-cyan-800/50 font-medium text-left cursor-pointer transition-all';
                }
                
                const titles = {
                    'dashboard': ['Dashboard', 'Dynamic energy monitoring'],
                    'analytics': ['Analytics', 'Detailed consumption insights'],
                    'alerts': ['Alerts', 'System notifications and warnings'],
                    'data': ['Data Logs', 'All recorded energy data'],
                    'settings': ['Settings', 'System configuration']
                };
                
                const [title, subtitle] = titles[page] || ['Dashboard', ''];
                document.getElementById('pageTitle').textContent = title;
                document.getElementById('pageSubtitle').textContent = subtitle;

                if (page === 'alerts') {
                    renderAllAlerts();
                }
            };

            // ============================================
            // RENDER FUNCTIONS
            // ============================================
            function renderAll() {
                renderDashboard();
                renderDataTable();
                renderAlerts();
                renderAnalytics();
            }

            // ============================================
            // RENDER DASHBOARD
            // ============================================
            function renderDashboard() {
                if (!allData || !allData.length) {
                    document.getElementById('totalConsumption').innerHTML = '0 <span class="text-sm text-slate-500">kWh</span>';
                    document.getElementById('avgConsumption').innerHTML = '0 <span class="text-sm text-slate-500">kWh</span>';
                    document.getElementById('activeAlerts').textContent = '0';
                    document.getElementById('totalRecords').textContent = '0';
                    document.getElementById('alertCount').textContent = '0';
                    document.getElementById('sidebarAlertCount').textContent = '0';
                    
                    renderEmptyUsageChart();
                    renderActivityLog();
                    return;
                }

                const total = allData.reduce((sum, item) => sum + parseFloat(item.consumption_kwh || 0), 0);
                const avg = total / allData.length;
                
                const activeAlerts = alertHistory.filter(a => !a.resolved);
                
                document.getElementById('totalConsumption').innerHTML = `${total.toFixed(1)} <span class="text-sm text-slate-500">kWh</span>`;
                document.getElementById('avgConsumption').innerHTML = `${avg.toFixed(1)} <span class="text-sm text-slate-500">kWh</span>`;
                document.getElementById('activeAlerts').textContent = activeAlerts.length;
                document.getElementById('totalRecords').textContent = allData.length;
                document.getElementById('alertCount').textContent = activeAlerts.length;
                document.getElementById('sidebarAlertCount').textContent = activeAlerts.length;
                
                const allAlertsCount = document.getElementById('allAlertsCount');
                if (allAlertsCount) {
                    allAlertsCount.textContent = activeAlerts.length;
                }
                
                renderUsageChart();
                renderActivityLog();
            }

            // ============================================
            // RENDER ALERTS (Dashboard)
            // ============================================
            function renderAlerts() {
                const container = document.getElementById('alertsContainer');
                const activeAlerts = alertHistory.filter(a => !a.resolved);
                
                if (!activeAlerts.length) {
                    container.innerHTML = `
                        <div class="text-center text-emerald-400 py-8">
                            <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-2"></i>
                            <p class="text-sm font-medium">All systems optimized</p>
                            <p class="text-xs text-slate-500">No active alerts</p>
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                container.innerHTML = activeAlerts.slice(0, 5).map((alert, index) => `
                    <div class="persistent-alert bg-rose-950/20 border border-rose-900/40 rounded-xl p-4 flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="text-rose-400 w-5 h-5 mt-0.5"></i>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-semibold text-rose-300">${alert.building_name || 'Unknown'}</h4>
                                <span class="text-xs text-rose-400 font-bold">${(alert.current_consumption || alert.consumption).toFixed(1)} kWh</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">⚠️ High consumption detected • ${alert.department || 'General'}</p>
                            <p class="text-[10px] text-slate-500 mt-1">🔴 Alert will stay until manually resolved</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[10px] text-slate-500">Triggered: ${alert.triggered_at ? new Date(alert.triggered_at).toLocaleTimeString() : 'just now'}</span>
                                <button onclick="resolvePersistentAlert(${alert.record_id})" class="text-xs font-semibold px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all cursor-pointer">
                                    Resolve Now
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                lucide.createIcons();
            }

            // ============================================
            // RENDER ALL ALERTS (Alerts Page)
            // ============================================
            function renderAllAlerts() {
                const container = document.getElementById('allAlertsContainer');
                const activeAlerts = alertHistory.filter(a => !a.resolved);
                
                if (!activeAlerts.length) {
                    container.innerHTML = `
                        <div class="text-center text-emerald-400 py-8">
                            <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-2"></i>
                            <p class="text-sm font-medium">All systems optimized</p>
                            <p class="text-xs text-slate-500">No active alerts</p>
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                container.innerHTML = activeAlerts.map(alert => `
                    <div class="persistent-alert bg-slate-900 border border-slate-800 rounded-xl p-4 flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-rose-400 w-5 h-5 mt-0.5"></i>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-semibold text-slate-200">${alert.building_name || 'Unknown'}</h4>
                                <span class="text-xs font-mono text-rose-400 font-bold">${(alert.current_consumption || alert.consumption).toFixed(1)} kWh</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">${alert.department || 'General'} • ${alert.floor || 'Floor 1'}</p>
                            <p class="text-[10px] text-amber-400 mt-1">🔴 This alert will NOT auto-resolve. Manual action required.</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[10px] text-slate-500">Triggered: ${alert.triggered_at ? new Date(alert.triggered_at).toLocaleString() : 'just now'}</span>
                                <button onclick="resolvePersistentAlert(${alert.record_id})" class="text-xs font-semibold px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all cursor-pointer">
                                    Resolve
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                lucide.createIcons();
            }

            // ============================================
            // RESOLVE PERSISTENT ALERT
            // ============================================
            window.resolvePersistentAlert = function(recordId) {
                const alert = alertHistory.find(a => a.record_id === recordId && !a.resolved);
                if (alert) {
                    alert.resolved = true;
                    alert.resolved_at = new Date().toISOString();
                    saveAlertHistory();
                    renderAll();
                    renderAllAlerts();
                    showToast(`✅ Alert resolved for ${alert.building_name}`, 'success', 6000);
                } else {
                    showToast('Alert not found or already resolved', 'info', 4000);
                }
            };

            // ============================================
            // ACTIVITY LOG
            // ============================================
            function renderActivityLog() {
                const container = document.getElementById('activityContainer');
                
                if (!allData || !allData.length) {
                    container.innerHTML = `
                        <div class="text-center text-slate-500 py-4 text-sm">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                            No activity recorded
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                const recent = allData.slice(-10).reverse();
                
                container.innerHTML = recent.map(item => {
                    const consumption = parseFloat(item.consumption_kwh || 0);
                    const hasAlert = alertHistory.some(a => a.record_id === item.id && !a.resolved);
                    return `
                        <div class="row-update flex items-center justify-between py-2 border-b border-slate-800/40">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 ${consumption > 100 ? 'bg-rose-400' : 'bg-emerald-400'} rounded-full"></span>
                                <span class="text-sm text-slate-300">${item.building_name || 'Unknown'}</span>
                                ${hasAlert ? '<span class="text-xs text-rose-400 font-bold">🔴 ALERT</span>' : ''}
                            </div>
                            <span class="text-xs text-slate-400">${consumption.toFixed(1)} kWh</span>
                            <span class="text-xs text-slate-500">${item.timestamp || 'just now'}</span>
                        </div>
                    `;
                }).join('');
                
                lucide.createIcons();
            }

            // ============================================
            // RENDER DATA TABLE
            // ============================================
            function renderDataTable() {
                const tbody = document.getElementById('allDataTableBody');
                const search = document.getElementById('searchInput').value.toLowerCase();
                const buildingFilter = document.getElementById('buildingFilter').value;
                
                let filtered = allData || [];
                
                if (search) {
                    filtered = filtered.filter(item => 
                        (item.building_name || '').toLowerCase().includes(search) ||
                        (item.department || '').toLowerCase().includes(search) ||
                        (item.floor || '').toLowerCase().includes(search)
                    );
                }
                
                if (buildingFilter) {
                    filtered = filtered.filter(item => item.building_name === buildingFilter);
                }
                
                if (!filtered.length) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">
                                <i data-lucide="database" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                <p class="text-sm font-medium">No data found</p>
                                <p class="text-xs text-slate-600 mt-1">Add your first energy record using the "Add" button</p>
                            </td>
                        </tr>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                tbody.innerHTML = filtered.map(item => {
                    const consumption = parseFloat(item.consumption_kwh || 0);
                    const hasAlert = alertHistory.some(a => a.record_id === item.id && !a.resolved);
                    return `
                        <tr class="hover:bg-slate-900/30 transition-all row-update">
                            <td class="py-3 text-sm text-slate-400">${item.id || '-'}</td>
                            <td class="py-3 text-sm font-medium">${item.building_name || '-'}</td>
                            <td class="py-3 text-sm text-slate-300">${item.floor || '-'}</td>
                            <td class="py-3 text-sm text-slate-300">${item.department || '-'}</td>
                            <td class="py-3 text-sm font-mono ${consumption > 100 ? 'text-rose-400' : 'text-emerald-400'}">${consumption.toFixed(2)}</td>
                            <td class="py-3 text-sm text-slate-300">${parseFloat(item.voltage || 0).toFixed(1)}</td>
                            <td class="py-3 text-sm text-slate-300">${parseFloat(item.current_amps || 0).toFixed(1)}</td>
                            <td class="py-3 text-sm text-slate-400">${item.timestamp || '-'}</td>
                            <td class="py-3 text-sm">
                                ${hasAlert ? 
                                    '<span class="px-2 py-1 rounded-full text-xs bg-rose-950/40 text-rose-400 font-bold">🔴 ALERT</span>' :
                                    `<span class="px-2 py-1 rounded-full text-xs ${consumption > 100 ? 'bg-rose-950/40 text-rose-400' : 'bg-emerald-950/40 text-emerald-400'}">
                                        ${consumption > 100 ? '⚠️ High' : '✅ Normal'}
                                    </span>`
                                }
                            </td>
                        </tr>
                    `;
                }).join('');
                
                lucide.createIcons();
            }

            // ============================================
            // RENDER ANALYTICS
            // ============================================
            function renderAnalytics() {
                if (!allData || !allData.length) {
                    document.getElementById('mostActiveBuilding').textContent = 'No Data';
                    document.getElementById('totalAnomalies').textContent = '0';
                    document.getElementById('avgPowerFactor').textContent = '0.00';
                    renderEmptyAnalyticsChart();
                    return;
                }
                
                const buildings = {};
                allData.forEach(item => {
                    const name = item.building_name || 'Unknown';
                    buildings[name] = (buildings[name] || 0) + parseFloat(item.consumption_kwh || 0);
                });
                
                let maxBuilding = 'None';
                let maxUsage = 0;
                for (const [name, usage] of Object.entries(buildings)) {
                    if (usage > maxUsage) {
                        maxUsage = usage;
                        maxBuilding = name;
                    }
                }
                document.getElementById('mostActiveBuilding').textContent = maxBuilding;
                
                const activeAlerts = alertHistory.filter(a => !a.resolved);
                document.getElementById('totalAnomalies').textContent = activeAlerts.length;
                
                const avgPF = allData.reduce((sum, item) => sum + (parseFloat(item.power_factor) || 0.85), 0) / allData.length;
                document.getElementById('avgPowerFactor').textContent = avgPF.toFixed(2);
                
                renderAnalyticsChart();
            }

            // ============================================
            // CHART FUNCTIONS
            // ============================================
            function renderEmptyUsageChart() {
                const ctx = document.getElementById('usageChartCanvas').getContext('2d');
                if (usageChart) usageChart.destroy();
                usageChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'Consumption (kWh)',
                            data: [0],
                            borderColor: '#22d3ee',
                            backgroundColor: 'rgba(34, 211, 238, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#94a3b8' } } },
                        scales: {
                            y: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } },
                            x: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } }
                        }
                    }
                });
                window.usageChart = usageChart;
            }

            function renderEmptyAnalyticsChart() {
                const ctx = document.getElementById('analyticsChartCanvas').getContext('2d');
                if (analyticsChart) analyticsChart.destroy();
                analyticsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['No Data'],
                        datasets: [{
                            label: 'Consumption by Building',
                            data: [0],
                            backgroundColor: '#334155',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#94a3b8' } } },
                        scales: {
                            y: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } },
                            x: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } }
                        }
                    }
                });
                window.analyticsChart = analyticsChart;
            }

            function renderUsageChart() {
                const ctx = document.getElementById('usageChartCanvas').getContext('2d');
                if (usageChart) usageChart.destroy();
                
                if (!allData || !allData.length) {
                    renderEmptyUsageChart();
                    return;
                }
                
                const labels = allData.map(item => `#${item.id}`);
                const data = allData.map(item => parseFloat(item.consumption_kwh || 0));
                
                usageChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Consumption (kWh)',
                            data: data,
                            borderColor: '#22d3ee',
                            backgroundColor: 'rgba(34, 211, 238, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: data.map(v => v > 100 ? '#ef4444' : '#22d3ee'),
                            pointBorderColor: data.map(v => v > 100 ? '#ef4444' : '#22d3ee'),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#94a3b8' } } },
                        scales: {
                            y: { 
                                grid: { color: '#1e293b' }, 
                                ticks: { color: '#64748b' },
                                beginAtZero: true
                            },
                            x: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } }
                        }
                    }
                });
                window.usageChart = usageChart;
            }

            function renderAnalyticsChart() {
                const ctx = document.getElementById('analyticsChartCanvas').getContext('2d');
                if (analyticsChart) analyticsChart.destroy();
                
                if (!allData || !allData.length) {
                    renderEmptyAnalyticsChart();
                    return;
                }
                
                const buildingTotals = {};
                allData.forEach(item => {
                    const name = item.building_name || 'Unknown';
                    buildingTotals[name] = (buildingTotals[name] || 0) + parseFloat(item.consumption_kwh || 0);
                });
                
                const labels = Object.keys(buildingTotals);
                const data = Object.values(buildingTotals);
                const colors = ['#22d3ee', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                
                analyticsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Consumption by Building',
                            data: data,
                            backgroundColor: colors.slice(0, data.length),
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#94a3b8' } } },
                        scales: {
                            y: { 
                                grid: { color: '#1e293b' }, 
                                ticks: { color: '#64748b' },
                                beginAtZero: true
                            },
                            x: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } }
                        }
                    }
                });
                window.analyticsChart = analyticsChart;
            }

            // ============================================
            // FILTER ALERTS
            // ============================================
            window.filterAlerts = function(level, event) {
                const container = document.getElementById('allAlertsContainer');
                let filtered = alertHistory.filter(a => !a.resolved);
                
                if (level === 'high') {
                    filtered = filtered.filter(a => (a.current_consumption || a.consumption) > 200);
                } else if (level === 'medium') {
                    filtered = filtered.filter(a => (a.current_consumption || a.consumption) > 100 && (a.current_consumption || a.consumption) <= 200);
                } else if (level === 'low') {
                    filtered = filtered.filter(a => (a.current_consumption || a.consumption) > 50 && (a.current_consumption || a.consumption) <= 100);
                }
                
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.className = 'filter-btn px-3 py-1.5 rounded-lg text-slate-400 hover:bg-slate-900 text-sm font-medium';
                });
                if (event && event.target) {
                    event.target.className = 'filter-btn px-3 py-1.5 rounded-lg bg-cyan-950/50 text-cyan-400 border border-cyan-800/50 text-sm font-medium';
                }
                
                if (!filtered.length) {
                    container.innerHTML = `
                        <div class="text-center text-slate-500 py-8">
                            <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-emerald-500"></i>
                            <p class="text-sm font-medium">No alerts found</p>
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }
                
                container.innerHTML = filtered.map(alert => `
                    <div class="persistent-alert bg-slate-900 border border-slate-800 rounded-xl p-4 flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-rose-400 w-5 h-5 mt-0.5"></i>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-semibold text-slate-200">${alert.building_name || 'Unknown'}</h4>
                                <span class="text-xs font-mono ${(alert.current_consumption || alert.consumption) > 200 ? 'text-rose-400' : 'text-amber-400'} font-bold">${(alert.current_consumption || alert.consumption).toFixed(1)} kWh</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">${alert.department || 'General'} • ${alert.floor || 'Floor 1'}</p>
                            <p class="text-[10px] text-amber-400 mt-1">🔴 Alert requires manual resolution</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[10px] text-slate-500">${alert.triggered_at ? new Date(alert.triggered_at).toLocaleString() : 'just now'}</span>
                                <button onclick="resolvePersistentAlert(${alert.record_id})" class="text-xs font-semibold px-2.5 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all cursor-pointer">
                                    Resolve
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                lucide.createIcons();
            };

            // ============================================
            // REFRESH DATA
            // ============================================
            window.refreshData = function() {
                allData = generateStaticRecords();
                checkAndCreateAlerts(allData);
                renderAll();
                renderAllAlerts();
                updateLastUpdate();
                showToast('🔄 Data refreshed', 'success', 5000);
            };

            // ============================================
            // ADD DATA
            // ============================================
            document.getElementById('addDataForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                
                const consumptionVal = parseFloat(document.getElementById('consumption').value);
                const data = {
                    building_name: document.getElementById('buildingName').value,
                    floor: document.getElementById('floor').value || 'Floor 1',
                    department: document.getElementById('department').value || 'General',
                    consumption_kwh: consumptionVal,
                    voltage: parseFloat(document.getElementById('voltage').value) || 220,
                    current_amps: parseFloat(document.getElementById('current').value) || 10,
                    power_factor: 0.85,
                    temperature: 25
                };
                
                try {
                    const newId = allData.length > 0 ? Math.max(...allData.map(d => d.id)) + 1 : 1;
                    const newRecord = {
                        id: newId,
                        ...data,
                        timestamp: new Date().toLocaleString()
                    };
                    allData.push(newRecord);
                    
                    if (consumptionVal > ALERT_THRESHOLD) {
                        const newAlert = {
                            record_id: newId,
                            building_name: data.building_name,
                            floor: data.floor,
                            department: data.department,
                            consumption: consumptionVal,
                            timestamp: newRecord.timestamp,
                            triggered_at: new Date().toISOString(),
                            resolved: false,
                            resolved_at: null,
                            current_consumption: consumptionVal
                        };
                        alertHistory.push(newAlert);
                        saveAlertHistory();
                        showToast(`⚠️ ALERT: High consumption (${consumptionVal} kWh) at ${data.building_name}`, 'error', 10000);
                    }
                    
                    showToast('Data added successfully!', 'success', 5000);
                    closeAddModal();
                    document.getElementById('addDataForm').reset();
                    renderAll();
                    renderAllAlerts();
                    updateLastUpdate();
                } catch (err) {
                    console.error('Error submitting data:', err);
                    showToast('Error submitting data: ' + err.message, 'error', 7000);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });

            // ============================================
            // EXPORT DATA
            // ============================================
            window.exportData = function() {
                try {
                    if (!allData || allData.length === 0) {
                        showToast('No data to export.', 'info', 4000);
                        return;
                    }
                    
                    const headers = ['ID', 'Building', 'Floor', 'Department', 'Consumption (kWh)', 'Voltage (V)', 'Current (A)', 'Timestamp', 'Has Alert'];
                    const rows = allData.map(item => {
                        const hasAlert = alertHistory.some(a => a.record_id === item.id && !a.resolved);
                        return [
                            item.id || '',
                            item.building_name || '',
                            item.floor || '',
                            item.department || '',
                            parseFloat(item.consumption_kwh || 0).toFixed(2),
                            parseFloat(item.voltage || 0).toFixed(1),
                            parseFloat(item.current_amps || 0).toFixed(1),
                            item.timestamp || '',
                            hasAlert ? 'YES' : 'NO'
                        ];
                    });
                    
                    const csvContent = [headers.join(','), ...rows.map(row => row.join(','))].join('\n');
                    
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'energy_data_' + new Date().toISOString().slice(0, 10) + '.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    
                    showToast('Export successful!', 'success', 5000);
                } catch (err) {
                    console.error('Export error:', err);
                    showToast('Export failed: ' + err.message, 'error', 7000);
                }
            };

            // ============================================
            // MODAL CONTROLS
            // ============================================
            window.openAddModal = function() {
                document.getElementById('addModal').classList.add('active');
            };
            
            window.closeAddModal = function() {
                document.getElementById('addModal').classList.remove('active');
            };

            // ============================================
            // LOGOUT
            // ============================================
            window.handleLogout = function() {
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logout.php';
                }
            };

            // ============================================
            // UPDATE LAST UPDATE TIME
            // ============================================
            function updateLastUpdate() {
                const now = new Date();
                document.getElementById('lastUpdate').innerHTML = `
                    <i data-lucide="clock" class="w-3 h-3"></i> ${now.toLocaleTimeString()}
                `;
                lucide.createIcons();
                document.getElementById('totalRecordsSettings').textContent = allData ? allData.length : 0;
                document.getElementById('lastGenerated').textContent = now.toLocaleTimeString();
            }

            // ============================================
            // SEARCH AND FILTER HANDLERS
            // ============================================
            document.getElementById('searchInput').addEventListener('input', renderDataTable);
            document.getElementById('buildingFilter').addEventListener('change', renderDataTable);

            // ============================================
            // MODAL CLOSE ON OVERLAY CLICK
            // ============================================
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            });

            // ============================================
            // KEYBOARD SHORTCUTS
            // ============================================
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    refreshData();
                }
                // Toggle theme with Ctrl+T
                if ((e.ctrlKey || e.metaKey) && e.key === 't') {
                    e.preventDefault();
                    toggleTheme();
                }
            });

            // ============================================
            // INIT
            // ============================================
            allData = generateStaticRecords();
            checkAndCreateAlerts(allData);
            renderAll();
            renderAllAlerts();
            updateLastUpdate();
            
            dynamicInterval = setInterval(dynamicUpdate, UPDATE_INTERVAL);
            showPage('dashboard');

            window.addEventListener('beforeunload', function() {
                if (dynamicInterval) clearInterval(dynamicInterval);
                saveAlertHistory();
            });
        });
    </script>
    
</body>
</html>