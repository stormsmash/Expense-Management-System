<?php
/**
 * Navbar Component (Modern Edition)
 * Expense Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/head.php';

$user_name_thai = $_SESSION['user_name_thai'] ?? 'ผู้ใช้งาน';
$user_position  = $_SESSION['user_position']  ?? 'พนักงาน';
$user_branch    = $_SESSION['user_branch_no'] ?? '-';
$user_initial   = mb_substr($user_name_thai, 0, 1, 'UTF-8');

// Thai Date helpers
$thai_month_arr = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
$d = date('j');
$m = $thai_month_arr[(int)date('n')];
$y = (int)date('Y') + 543;
$thai_today = "$d $m $y";
?>

<nav class="navbar navbar-expand-lg app-navbar fixed-top">
    <div class="container-fluid px-lg-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="../pages/dashboard.php">
            <img src="../assets/img/app_logo.png" alt="EMS Logo" class="nav-brand-logo">
            <div class="d-flex flex-column">
                <span class="nav-brand-title">EMS Portal</span>
                <span class="nav-brand-sub">ระบบจัดการค่าใช้จ่าย</span>
            </div>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <i class="bi bi-list fs-3 text-secondary"></i>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse mt-3 mt-lg-0" id="navContent">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto ms-lg-4 gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-3 text-dark fw-medium <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-light text-primary fw-semibold' : '' ?>" href="../pages/dashboard.php">
                        <i class="bi bi-grid-fill me-1 text-primary"></i> แดชบอร์ด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-3 text-dark fw-medium <?= (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'bg-light text-primary fw-semibold' : '' ?>" href="../pages/categories.php">
                        <i class="bi bi-tags-fill me-1 text-teal" style="color:var(--primary);"></i> จัดการหมวดค่าใช้จ่าย
                    </a>
                </li>
            </ul>

            <!-- Right: Date/Time + User Profile + Logout -->
            <div class="d-flex align-items-center flex-wrap gap-2 gap-lg-3">
                <!-- Date Pill -->
                <div class="d-none d-md-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border text-muted small">
                    <i class="bi bi-calendar3 text-primary"></i>
                    <span><?= $thai_today ?></span>
                    <span class="text-secondary">•</span>
                    <i class="bi bi-clock"></i>
                    <span id="liveClock"><?= date('H:i') ?></span>
                </div>

                <!-- User Pill -->
                <div class="nav-user-pill">
                    <div class="nav-user-avatar">
                        <?= htmlspecialchars($user_initial) ?>
                    </div>
                    <div class="nav-user-info me-1">
                        <div class="nav-user-name"><?= htmlspecialchars($user_name_thai) ?></div>
                        <div class="nav-user-role"><?= htmlspecialchars($user_position) ?> (สาขา: <?= htmlspecialchars($user_branch) ?>)</div>
                    </div>
                </div>

                <!-- Back / Logout Button -->
                <?php if (isset($back_btn) && $back_btn === true): ?>
                    <button class="btn btn-outline-secondary btn-sm px-3 rounded-pill" onclick="window.history.back()">
                        <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                    </button>
                <?php endif; ?>

                <?php if (!isset($logout) || $logout !== 'off'): ?>
                    <button onclick="logout()" class="btn btn-danger-custom btn-sm px-3 rounded-pill d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>ออกจากระบบ</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
// Live clock
setInterval(() => {
    const d = new Date();
    const clock = document.getElementById('liveClock');
    if (clock) {
        clock.textContent = d.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    }
}, 30000);
</script>
