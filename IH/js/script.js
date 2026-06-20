// ============================================
// THEME TOGGLE - FIXED
// ============================================
const themeBtn = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const themeText = document.getElementById('themeText');

// Only run if elements exist
if (themeBtn && themeIcon && themeText) {
    let currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeUI(currentTheme);

    themeBtn.addEventListener('click', function() {
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        currentTheme = newTheme;
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
        showToast(`Switched to ${newTheme} mode`, 'info');
    });
}

function updateThemeUI(theme) {
    if (themeIcon && themeText) {
        if (theme === 'dark') {
            themeIcon.className = 'fas fa-sun';
            themeText.textContent = 'Light';
        } else {
            themeIcon.className = 'fas fa-moon';
            themeText.textContent = 'Dark';
        }
    }
}

// ============================================
// PAGE NAVIGATION - FIXED
// ============================================
function showPage(page) {
    // Hide all pages
    document.querySelectorAll('.page-content').forEach(p => {
        p.classList.remove('active');
        p.style.display = 'none';
    });
    
    // Show target page
    const targetPage = document.getElementById(`page-${page}`);
    if (targetPage) {
        targetPage.classList.add('active');
        targetPage.style.display = 'block';
    }
    
    // Update menu items
    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('active');
    });
    const activeMenuItem = document.querySelector(`.menu-item[data-page="${page}"]`);
    if (activeMenuItem) {
        activeMenuItem.classList.add('active');
    }
    
    // Update title
    const titles = {
        dashboard: 'Dashboard',
        analytics: 'Analytics',
        alerts: 'Alerts',
        data: 'Data Logs',
        buildings: 'Buildings',
        settings: 'Settings'
    };
    const pageTitle = document.getElementById('pageTitle');
    if (pageTitle) {
        pageTitle.textContent = titles[page] || 'Dashboard';
    }
    
    // Load page data
    if (page === 'analytics') loadAnalytics();
    if (page === 'alerts') loadAllAlerts();
    if (page === 'data') loadAllData();
    if (page === 'buildings') loadBuildings();
    
    // Close sidebar on mobile
    if (window.innerWidth <= 992) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.remove('open');
    }
}

window.showPage = showPage;

// ============================================
// SIDEBAR TOGGLE - FIXED
// ============================================
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.toggle('open');
    });
}

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.toggle('open');
    });
}

document.addEventListener('click', function(e) {
    if (window.innerWidth <= 992) {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (sidebar && menuBtn && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// ============================================
// GLOBAL VARIABLES
// ============================================
let allData = [];
let usageChart = null;
let buildingChart = null;
let refreshInterval = null;
let autoResolveInterval = null;

// DOM Elements - with null checks
const addDataBtn = document.getElementById('addDataBtn');
const importBtn = document.getElementById('importBtn');
const exportBtn = document.getElementById('exportBtn');
const refreshBtn = document.getElementById('refreshBtn');
const logoutBtn = document.getElementById('logoutBtn');
const modal = document.getElementById('addDataModal');
const importModal = document.getElementById('importModal');
const closeModalBtn = document.getElementById('closeModal');
const closeImportModalBtn = document.getElementById('closeImportModal');
const cancelModalBtn = document.getElementById('cancelModal');
const cancelImportBtn = document.getElementById('cancelImport');
const addDataForm = document.getElementById('addDataForm');
const importForm = document.getElementById('importForm');

// ============================================
// EVENT LISTENERS - With null checks
// ============================================
if (addDataBtn) addDataBtn.addEventListener('click', () => openModal('addDataModal'));
if (importBtn) importBtn.addEventListener('click', () => openModal('importModal'));
if (closeModalBtn) closeModalBtn.addEventListener('click', () => closeModal('addDataModal'));
if (closeImportModalBtn) closeImportModalBtn.addEventListener('click', () => closeModal('importModal'));
if (cancelModalBtn) cancelModalBtn.addEventListener('click', () => closeModal('addDataModal'));
if (cancelImportBtn) cancelImportBtn.addEventListener('click', () => closeModal('importModal'));

if (refreshBtn) {
    refreshBtn.addEventListener('click', function() {
        this.innerHTML = '⏳';
        this.disabled = true;
        loadDashboardData().finally(() => {
            this.innerHTML = '🔄';
            this.disabled = false;
        });
    });
}

if (exportBtn) exportBtn.addEventListener('click', exportData);
if (logoutBtn) logoutBtn.addEventListener('click', logout);

window.addEventListener('click', function(e) {
    if (e.target === modal) closeModal('addDataModal');
    if (e.target === importModal) closeModal('importModal');
});

if (addDataForm) addDataForm.addEventListener('submit', submitUsageData);
if (importForm) importForm.addEventListener('submit', importData);

// ============================================
// SEARCH & FILTER - With null checks
// ============================================
const searchInput = document.getElementById('searchInput');
const buildingFilter = document.getElementById('buildingFilter');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        filterTable(this.value);
    });
}

if (buildingFilter) {
    buildingFilter.addEventListener('change', function() {
        filterTableByBuilding(this.value);
    });
}

function filterTable(query) {
    const rows = document.querySelectorAll('#allDataTableBody tr');
    const searchTerm = query.toLowerCase().trim();
    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function filterTableByBuilding(building) {
    const rows = document.querySelectorAll('#allDataTableBody tr');
    rows.forEach(row => {
        if (row.classList.contains('no-data')) return;
        const buildingCell = row.cells[1]?.textContent || '';
        row.style.display = !building || buildingCell === building ? '' : 'none';
    });
}

// ============================================
// ALERT FILTERS
// ============================================
function filterAlerts(severity) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    const items = document.querySelectorAll('#allAlertsContainer .alert-item');
    items.forEach(item => {
        if (severity === 'all') {
            item.style.display = '';
        } else {
            const itemSeverity = item.className.includes(`severity-${severity}`) ? severity : '';
            item.style.display = itemSeverity ? '' : 'none';
        }
    });
}
window.filterAlerts = filterAlerts;

// ============================================
// INITIALIZE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard initializing...');
    loadDashboardData();
    startAutoResolve();
    startLiveUpdates();
});

// ============================================
// AUTO-RESOLVE ALERTS
// ============================================
function startAutoResolve() {
    if (autoResolveInterval) clearInterval(autoResolveInterval);
    autoResolveInterval = setInterval(autoResolve, 10000);
}

function autoResolve() {
    if (!allData || allData.length === 0) return;
    const alerts = allData.filter(item => parseFloat(item.consumption_kwh || 0) > 100);
    if (alerts.length > 0) {
        alerts.slice(0, 2).forEach(item => {
            resolveAlert(item.id);
        });
    }
}

// ============================================
// LIVE UPDATES
// ============================================
function startLiveUpdates() {
    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(loadDashboardData, 10000);
}

// ============================================
// LOAD DASHBOARD DATA - FIXED
// ============================================
async function loadDashboardData() {
    console.log('🔄 Loading dashboard data...');
    try {
        const response = await fetch('api.php?action=all');
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📊 Data received:', data.length, 'records');
        
        if (data.error) {
            console.error('API Error:', data.error);
            showToast('API Error: ' + data.error, 'error');
            return;
        }
        
        if (Array.isArray(data)) {
            allData = data;
        } else {
            console.error('Invalid data format:', data);
            allData = [];
        }
        
        updateDashboard();
        updateLastUpdate();
        
    } catch (error) {
        console.error('Error loading data:', error);
        showToast('Error loading data: ' + error.message, 'error');
    }
}

function updateDashboard() {
    // Update summary cards
    const totalEl = document.getElementById('totalConsumption');
    const avgEl = document.getElementById('avgConsumption');
    const alertsEl = document.getElementById('activeAlerts');
    const recordsEl = document.getElementById('totalRecords');
    const alertCountEl = document.getElementById('alertCount');
    const sidebarAlertEl = document.getElementById('sidebarAlertCount');
    const sidebarBadge = document.getElementById('sidebarAlertBadge');
    
    if (!allData || allData.length === 0) {
        if (totalEl) totalEl.innerHTML = '0 <span class="text-sm text-slate-500">kWh</span>';
        if (avgEl) avgEl.innerHTML = '0 <span class="text-sm text-slate-500">kWh</span>';
        if (alertsEl) alertsEl.textContent = '0';
        if (recordsEl) recordsEl.textContent = '0';
        if (alertCountEl) alertCountEl.textContent = '0';
        if (sidebarAlertEl) sidebarAlertEl.textContent = '0';
        if (sidebarBadge) sidebarBadge.textContent = '0';
        
        updateAlerts([]);
        updateActivity([]);
        updateTable([]);
        updateEmptyCharts();
        return;
    }

    const total = allData.reduce((sum, item) => sum + parseFloat(item.consumption_kwh || 0), 0);
    const avg = total / allData.length;
    const alerts = allData.filter(item => parseFloat(item.consumption_kwh || 0) > 100);
    
    if (totalEl) totalEl.innerHTML = `${total.toFixed(1)} <span class="text-sm text-slate-500">kWh</span>`;
    if (avgEl) avgEl.innerHTML = `${avg.toFixed(1)} <span class="text-sm text-slate-500">kWh</span>`;
    if (alertsEl) alertsEl.textContent = alerts.length;
    if (recordsEl) recordsEl.textContent = allData.length;
    if (alertCountEl) alertCountEl.textContent = alerts.length;
    if (sidebarAlertEl) sidebarAlertEl.textContent = alerts.length;
    if (sidebarBadge) sidebarBadge.textContent = alerts.length;
    
    updateAlerts(alerts);
    updateActivity(allData);
    updateTable(allData);
    updateUsageChart(allData);
    updateBuildingChart(allData);
}

// ============================================
// UPDATE FUNCTIONS
// ============================================
function animateNumber(elementId, value) {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    const current = parseFloat(el.textContent) || 0;
    const target = parseFloat(value) || 0;
    if (current === target) return;
    
    const duration = 500;
    const startTime = performance.now();
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const currentValue = current + (target - current) * eased;
        el.textContent = target % 1 === 0 ? Math.round(currentValue) : currentValue.toFixed(1);
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

function getTimeAgo(timestamp) {
    if (!timestamp) return 'Just now';
    const now = new Date();
    const past = new Date(timestamp);
    const diff = Math.floor((now - past) / 1000);
    if (diff < 5) return 'Just now';
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
}

function updateAlerts(alerts) {
    const container = document.getElementById('alertsContainer');
    if (!container) return;
    
    if (!alerts || alerts.length === 0) {
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
    
    container.innerHTML = alerts.slice(0, 5).map((alert, index) => `
        <div class="bg-rose-950/20 border border-rose-900/40 rounded-xl p-4 flex items-start gap-3">
            <i data-lucide="alert-triangle" class="text-rose-400 w-5 h-5 mt-0.5"></i>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <h4 class="text-sm font-semibold text-rose-300">${alert.building_name || 'Unknown'}</h4>
                    <span class="text-xs text-rose-400">${parseFloat(alert.consumption_kwh || 0).toFixed(1)} kWh</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">High consumption detected</p>
                <button onclick="resolveAlert(${alert.id})" class="mt-2 text-xs font-semibold px-3 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all">
                    Resolve Now
                </button>
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

function updateActivity(usageData) {
    const container = document.getElementById('activityContainer');
    if (!container) return;
    
    if (!usageData || usageData.length === 0) {
        container.innerHTML = `
            <div class="text-center text-slate-500 py-4 text-sm">
                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                No activity recorded
            </div>
        `;
        lucide.createIcons();
        return;
    }
    
    const recent = usageData.slice(-10).reverse();
    container.innerHTML = recent.map(record => `
        <div class="flex items-center justify-between py-2 border-b border-slate-800/40">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                <span class="text-sm text-slate-300">${record.building_name || 'Unknown'}</span>
            </div>
            <span class="text-xs text-slate-400">${parseFloat(record.consumption_kwh || 0).toFixed(1)} kWh</span>
            <span class="text-xs text-slate-500">${record.timestamp || 'just now'}</span>
        </div>
    `).join('');
    
    lucide.createIcons();
}

function updateTable(usageData) {
    const tbody = document.getElementById('usageTableBody');
    if (!tbody) return;
    
    if (!usageData || usageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-slate-500 py-4">No data available</td></tr>';
        return;
    }
    
    tbody.innerHTML = usageData.slice(0, 10).map(record => `
        <tr class="hover:bg-slate-900/30">
            <td class="py-2 text-sm font-medium">${record.building_name || 'Unknown'}</td>
            <td class="py-2 text-sm text-slate-300">${record.floor || 'N/A'}</td>
            <td class="py-2 text-sm text-slate-300">${record.department || 'N/A'}</td>
            <td class="py-2 text-sm font-mono ${parseFloat(record.consumption_kwh || 0) > 100 ? 'text-rose-400' : 'text-emerald-400'}">${parseFloat(record.consumption_kwh || 0).toFixed(1)}</td>
            <td class="py-2 text-sm text-slate-400">${record.timestamp || 'N/A'}</td>
            <td class="py-2 text-sm">
                <span class="px-2 py-1 rounded-full text-xs ${parseFloat(record.consumption_kwh || 0) > 100 ? 'bg-rose-950/40 text-rose-400' : 'bg-emerald-950/40 text-emerald-400'}">
                    ${parseFloat(record.consumption_kwh || 0) > 100 ? '⚠️ High' : '✅ Normal'}
                </span>
            </td>
        </tr>
    `).join('');
}

// ============================================
// CHART FUNCTIONS - FIXED
// ============================================
function updateEmptyCharts() {
    const canvas1 = document.getElementById('usageChart');
    const canvas2 = document.getElementById('buildingChart');
    
    if (canvas1 && !usageChart) {
        const ctx = canvas1.getContext('2d');
        usageChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['No Data'],
                datasets: [{
                    label: 'Consumption (kWh)',
                    data: [0],
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34,211,238,0.1)',
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
    }
    
    if (canvas2 && !buildingChart) {
        const ctx = canvas2.getContext('2d');
        buildingChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['No Data'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#334155']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#94a3b8' } } }
            }
        });
    }
}

function updateUsageChart(usageData) {
    const canvas = document.getElementById('usageChart');
    if (!canvas) return;
    
    if (usageChart) {
        usageChart.destroy();
        usageChart = null;
    }
    
    if (!usageData || usageData.length === 0) {
        updateEmptyCharts();
        return;
    }
    
    const ctx = canvas.getContext('2d');
    const data = usageData.slice(0, 20).reverse();
    const labels = data.map((d, i) => i + 1);
    const values = data.map(d => parseFloat(d.consumption_kwh || 0));
    
    usageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Consumption (kWh)',
                data: values,
                borderColor: '#22d3ee',
                backgroundColor: 'rgba(34,211,238,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#22d3ee'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#94a3b8' } }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: '#1e293b' }, 
                    ticks: { color: '#64748b' } 
                },
                x: { 
                    grid: { color: '#1e293b' }, 
                    ticks: { color: '#64748b' } 
                }
            }
        }
    });
}

function updateBuildingChart(usageData) {
    const canvas = document.getElementById('buildingChart');
    if (!canvas) return;
    
    if (buildingChart) {
        buildingChart.destroy();
        buildingChart = null;
    }
    
    if (!usageData || usageData.length === 0) {
        updateEmptyCharts();
        return;
    }
    
    // Group by building
    const buildings = {};
    usageData.forEach(item => {
        const name = item.building_name || 'Unknown';
        buildings[name] = (buildings[name] || 0) + parseFloat(item.consumption_kwh || 0);
    });
    
    const ctx = canvas.getContext('2d');
    const colors = ['#22d3ee', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
    
    buildingChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(buildings),
            datasets: [{
                data: Object.values(buildings),
                backgroundColor: colors.slice(0, Object.keys(buildings).length),
                borderWidth: 2,
                borderColor: '#0f172a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { color: '#94a3b8', padding: 20 } 
                }
            }
        }
    });
}

function updateLastUpdate() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const lastUpdate = document.getElementById('lastUpdate');
    if (lastUpdate) {
        lastUpdate.innerHTML = `<i data-lucide="clock" class="w-3 h-3"></i> ${timeStr}`;
        lucide.createIcons();
    }
}

// ============================================
// PAGE: ANALYTICS
// ============================================
function loadAnalytics() {
    if (!allData || allData.length === 0) {
        const mostActive = document.getElementById('mostActiveBuilding');
        const anomalies = document.getElementById('totalAnomalies');
        const avgPF = document.getElementById('avgPowerFactor');
        if (mostActive) mostActive.textContent = 'No Data';
        if (anomalies) anomalies.textContent = '0';
        if (avgPF) avgPF.textContent = '0.00';
        return;
    }
    
    // Most active building
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
    const mostActive = document.getElementById('mostActiveBuilding');
    if (mostActive) mostActive.textContent = maxBuilding;
    
    // Anomalies
    const anomalies = allData.filter(item => parseFloat(item.consumption_kwh || 0) > 100);
    const anomaliesEl = document.getElementById('totalAnomalies');
    if (anomaliesEl) anomaliesEl.textContent = anomalies.length;
    
    // Avg Power Factor
    const pfSum = allData.reduce((sum, item) => sum + (parseFloat(item.power_factor) || 0.85), 0);
    const avgPF = document.getElementById('avgPowerFactor');
    if (avgPF) avgPF.textContent = (pfSum / allData.length).toFixed(2);
}

// ============================================
// PAGE: ALL ALERTS
// ============================================
function loadAllAlerts() {
    const container = document.getElementById('allAlertsContainer');
    if (!container) return;
    
    if (!allData || allData.length === 0) {
        container.innerHTML = '<p class="text-center text-slate-500 py-4">No alerts found</p>';
        return;
    }
    
    const alerts = allData.filter(item => parseFloat(item.consumption_kwh || 0) > 100);
    
    if (alerts.length === 0) {
        container.innerHTML = `
            <div class="text-center text-emerald-400 py-8">
                <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-2"></i>
                <p class="text-sm font-medium">All systems optimized</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }
    
    container.innerHTML = alerts.map(alert => `
        <div class="bg-rose-950/20 border border-rose-900/40 rounded-xl p-4 flex items-start gap-3">
            <i data-lucide="alert-triangle" class="text-rose-400 w-5 h-5 mt-0.5"></i>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <h4 class="text-sm font-semibold text-rose-300">${alert.building_name || 'Unknown'}</h4>
                    <span class="text-xs text-rose-400">${parseFloat(alert.consumption_kwh || 0).toFixed(1)} kWh</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">${alert.department || 'General'} • ${alert.floor || 'Floor 1'}</p>
                <button onclick="resolveAlert(${alert.id})" class="mt-2 text-xs font-semibold px-3 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all">
                    Resolve
                </button>
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

// ============================================
// PAGE: ALL DATA
// ============================================
function loadAllData() {
    const tbody = document.getElementById('allDataTableBody');
    if (!tbody) return;
    
    if (!allData || allData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-slate-500 py-4">No data available</td></tr>';
        return;
    }
    
    tbody.innerHTML = allData.slice(0, 50).map(record => `
        <tr class="hover:bg-slate-900/30">
            <td class="py-2 text-sm text-slate-400">#${record.id || '-'}</td>
            <td class="py-2 text-sm font-medium">${record.building_name || '-'}</td>
            <td class="py-2 text-sm text-slate-300">${record.floor || 'N/A'}</td>
            <td class="py-2 text-sm text-slate-300">${record.department || 'N/A'}</td>
            <td class="py-2 text-sm font-mono ${parseFloat(record.consumption_kwh || 0) > 100 ? 'text-rose-400' : 'text-emerald-400'}">${parseFloat(record.consumption_kwh || 0).toFixed(1)}</td>
            <td class="py-2 text-sm text-slate-300">${record.voltage || 'N/A'}</td>
            <td class="py-2 text-sm text-slate-300">${record.current_amps || 'N/A'}</td>
            <td class="py-2 text-sm text-slate-400">${record.timestamp || 'N/A'}</td>
            <td class="py-2 text-sm">
                <span class="px-2 py-1 rounded-full text-xs ${parseFloat(record.consumption_kwh || 0) > 100 ? 'bg-rose-950/40 text-rose-400' : 'bg-emerald-950/40 text-emerald-400'}">
                    ${parseFloat(record.consumption_kwh || 0) > 100 ? '⚠️ High' : '✅ Normal'}
                </span>
            </td>
        </tr>
    `).join('');
}

// ============================================
// PAGE: BUILDINGS
// ============================================
function loadBuildings() {
    const buildings = ['Tower A', 'Tower B', 'Tower C'];
    
    buildings.forEach(tower => {
        const items = (allData || []).filter(item => item.building_name === tower);
        const total = items.reduce((sum, item) => sum + parseFloat(item.consumption_kwh || 0), 0);
        const avg = items.length ? total / items.length : 0;
        
        const id = tower.replace(' ', '');
        const usageEl = document.getElementById(`building${id}Usage`);
        const avgEl = document.getElementById(`building${id}Avg`);
        const countEl = document.getElementById(`building${id}Count`);
        
        if (usageEl) usageEl.textContent = `${total.toFixed(1)} kWh`;
        if (avgEl) avgEl.textContent = `${avg.toFixed(1)} kWh`;
        if (countEl) countEl.textContent = items.length;
    });
}

// ============================================
// FUNCTIONS
// ============================================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'block';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
    if (id === 'addDataModal' && addDataForm) {
        addDataForm.reset();
    }
}

async function submitUsageData(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    if (!btn) return;
    
    const original = btn.innerHTML;
    btn.innerHTML = '⏳ Submitting...';
    btn.disabled = true;
    
    try {
        const data = {
            building_name: document.getElementById('buildingName')?.value || 'Tower A',
            floor: document.getElementById('floor')?.value || 'Floor 1',
            department: document.getElementById('department')?.value || 'General',
            consumption_kwh: parseFloat(document.getElementById('consumption')?.value || 0),
            voltage: parseFloat(document.getElementById('voltage')?.value || 220),
            current_amps: parseFloat(document.getElementById('current')?.value || 10)
        };
        
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showToast('✅ Data added successfully!', 'success');
            closeModal('addDataModal');
            loadDashboardData();
        } else {
            showToast('Error: ' + (result.error || 'Unknown'), 'error');
        }
    } catch (error) {
        showToast('Network error: ' + error.message, 'error');
    } finally {
        btn.innerHTML = original;
        btn.disabled = false;
    }
}

async function resolveAlert(id) {
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        if (result.success) {
            showToast('✅ Alert resolved', 'success');
            loadDashboardData();
            loadAllAlerts();
        } else {
            showToast('Failed to resolve alert', 'error');
        }
    } catch (error) {
        showToast('Error resolving alert', 'error');
    }
}

async function importData(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    if (!btn) return;
    
    const original = btn.innerHTML;
    btn.innerHTML = '⏳ Importing...';
    btn.disabled = true;
    
    const formData = new FormData();
    const file = document.getElementById('csvFile')?.files[0];
    if (file) {
        formData.append('csv_file', file);
    }
    
    try {
        const response = await fetch('api.php?action=import', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (response.ok) {
            showToast(result.message || 'Import successful', 'success');
            closeModal('importModal');
            loadDashboardData();
        } else {
            showToast('Error: ' + (result.error || 'Unknown'), 'error');
        }
    } catch (error) {
        showToast('Network error', 'error');
    } finally {
        btn.innerHTML = original;
        btn.disabled = false;
    }
}

function exportData() {
    if (!allData || allData.length === 0) {
        showToast('No data to export', 'info');
        return;
    }
    
    const btn = exportBtn;
    if (btn) {
        const original = btn.innerHTML;
        btn.innerHTML = '⏳';
        btn.disabled = true;
        
        window.location.href = 'api.php?action=export';
        setTimeout(() => {
            btn.innerHTML = original;
            btn.disabled = false;
            showToast('📥 Export started!', 'success');
        }, 1500);
    }
}

function viewDetails(id) {
    showToast(`Viewing details for record #${id}`, 'info');
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const msg = document.getElementById('toastMessage');
    
    if (!toast || !msg) {
        console.log('Toast:', message);
        return;
    }
    
    toast.className = 'toast';
    toast.classList.add(type);
    
    if (icon) {
        const icons = { 
            success: 'check-circle', 
            error: 'alert-circle', 
            info: 'info' 
        };
        icon.setAttribute('data-lucide', icons[type] || 'info');
        lucide.createIcons();
    }
    
    msg.textContent = message;
    toast.classList.add('show');
    
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => toast.classList.remove('show'), 4000);
}

// Make functions global
window.resolveAlert = resolveAlert;
window.viewDetails = viewDetails;
window.filterAlerts = filterAlerts;
window.exportData = exportData;
window.showPage = showPage;