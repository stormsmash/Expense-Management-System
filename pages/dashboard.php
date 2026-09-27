<?php
/**
 * Dashboard (Main Menu) — Modern Enterprise Edition
 * Expense Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title  = 'แดชบอร์ดหลัก';
$logout = 'on';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/navbar.php';

$pdo    = getDB();
$userId = (int) ($_SESSION['user_id'] ?? 0);

// ── Supervisor Contact Logic ──────────────────────────────────────────────
$mobile_chief = '';
$chief_name   = 'หัวหน้างานส่วนกลาง';

try {
    $stmt = $pdo->prepare("SELECT chief_no FROM employee_group WHERE group_seqno = ?");
    $stmt->execute([$userId]);
    $groupRow = $stmt->fetch();

    if ($groupRow) {
        $stmt2 = $pdo->prepare("SELECT chief_seqno FROM employee_chief WHERE chief_no = ?");
        $stmt2->execute([$groupRow['chief_no']]);
        $chiefRow = $stmt2->fetch();
        if ($chiefRow) {
            $stmt3 = $pdo->prepare("SELECT name_thai, mobile FROM employee WHERE seqno = ?");
            $stmt3->execute([$chiefRow['chief_seqno']]);
            $empRow = $stmt3->fetch();
            if ($empRow) {
                $mobile_chief = $empRow['mobile'];
                $chief_name   = $empRow['name_thai'];
            }
        }
    } else {
        $branchNo = $_SESSION['user_branch_no'] ?? '';
        $stmt4 = $pdo->prepare(
            "SELECT b.name_thai, b.mobile FROM employee_location a
             JOIN employee b ON b.seqno = a.seqno
             WHERE b.branch = '0001' AND a.locacode = ?"
        );
        $stmt4->execute([$branchNo]);
        $khetRow = $stmt4->fetch();
        if ($khetRow) {
            $mobile_chief = $khetRow['mobile'];
            $chief_name   = $khetRow['name_thai'];
        }
    }
} catch (Throwable $e) {
    // Ignore and fallback
}

// ── Quick Metrics ─────────────────────────────────────────────────────────
$totalGroups = 0;
$totalItems  = 0;
$maxLimit    = 0;
try {
    $totalGroups = (int)$pdo->query("SELECT COUNT(*) FROM expense_group")->fetchColumn();
    $totalItems  = (int)$pdo->query("SELECT COUNT(*) FROM expense_item")->fetchColumn();
    $maxLimit    = (float)$pdo->query("SELECT MAX(price_limit) FROM expense_item")->fetchColumn();
} catch (Throwable $e) {
    // Fallback
}
?>

<div class="container mt-page">
    <!-- Hero Welcome Banner -->
    <div class="card-modern p-4 p-lg-5 mb-4 position-relative overflow-hidden text-white" 
         style="background: linear-gradient(135deg, #0f766e 0%, #0d5f58 50%, #1e293b 100%);">
        <div class="position-absolute end-0 bottom-0 opacity-25 d-none d-md-block pe-4 pb-2" style="pointer-events:none;">
            <i class="bi bi-wallet2" style="font-size: 11rem; line-height: 1;"></i>
        </div>
        <div class="position-relative z-1" style="max-width: 650px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-20 text-white small mb-3">
                <i class="bi bi-shield-check"></i> ระบบจัดการค่าใช้จ่ายองค์กร v<?= htmlspecialchars(APP_VERSION) ?>
            </div>
            <h1 class="display-6 fw-bold mb-2">
                สวัสดี, <?= htmlspecialchars($_SESSION['user_name_thai'] ?? 'คุณผู้ใช้งาน') ?> 👋
            </h1>
            <p class="text-white-50 fs-6 mb-4">
                ยินดีต้อนรับสู่ระบบควบคุมและจัดการหมวดค่าใช้จ่าย สะดวก รวดเร็ว และตรวจสอบได้แบบเรียลไทม์
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-white bg-opacity-15 text-white fw-normal px-3 py-2 rounded-pill">
                    <i class="bi bi-person-badge me-1"></i> ตำแหน่ง: <?= htmlspecialchars($_SESSION['user_position'] ?? '-') ?>
                </span>
                <span class="badge bg-white bg-opacity-15 text-white fw-normal px-3 py-2 rounded-pill">
                    <i class="bi bi-geo-alt me-1"></i> สาขา: <?= htmlspecialchars($_SESSION['user_branch_no'] ?? '-') ?>
                </span>
            </div>
        </div>
    </div>

    <!-- KPI Statistics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrap bg-primary-subtle text-primary" style="background:#f0fdfa;color:#0f766e;">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <div class="kpi-val"><?= number_format($totalGroups) ?></div>
                    <div class="kpi-lbl">หมวดค่าใช้จ่ายทั้งหมด</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrap bg-info-subtle text-info" style="background:#eff6ff;color:#2563eb;">
                    <i class="bi bi-list-check"></i>
                </div>
                <div>
                    <div class="kpi-val"><?= number_format($totalItems) ?></div>
                    <div class="kpi-lbl">รายการค่าใช้จ่ายย่อย</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrap bg-success-subtle text-success" style="background:#ecfdf5;color:#10b981;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="kpi-val">฿<?= number_format($maxLimit) ?></div>
                    <div class="kpi-lbl">วงเงินสูงสุดต่อรายการ</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrap bg-warning-subtle text-warning" style="background:#fffbeb;color:#f59e0b;">
                    <i class="bi bi-telephone-outbound"></i>
                </div>
                <div>
                    <div class="kpi-val fs-6"><?= $mobile_chief ? htmlspecialchars($mobile_chief) : '02-xxx-xxxx' ?></div>
                    <div class="kpi-lbl">เบอร์โทรสายด่วนหัวหน้างาน</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fs-5 fw-bold text-dark m-0">
            <i class="bi bi-grid me-2 text-teal" style="color:var(--primary);"></i> เลือกการทำงาน
        </h2>
    </div>

    <div class="row g-4 mb-4">
        <!-- Action: Manage Categories -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="action-card" onclick="window.location.href='categories.php'">
                <div class="action-card-icon bg-primary-subtle text-primary" style="background:#f0fdfa;color:#0f766e;">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <h3 class="fs-5 fw-bold text-dark mb-1">จัดการหมวดค่าใช้จ่าย</h3>
                <p class="text-muted small mb-4 flex-grow-1">
                    เพิ่ม แก้ไข และจัดกลุ่มหมวดค่าใช้จ่าย กำหนดวงเงินและผูกรหัสบัญชีสำนักงาน/สาขา
                </p>
                <div class="d-flex align-items-center text-teal fw-semibold small" style="color:var(--primary);">
                    <span>เข้าสู่ระบบจัดการ</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </div>
            </div>
        </div>

        <!-- Action: Quick Request Expense (Placeholder preview) -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="action-card" onclick="Swal.fire({title:'ระบบบันทึกใบเบิกจ่าย',text:'แบบฟอร์มการเบิกจ่ายออนไลน์จะเปิดให้ใช้งานในโมดูลถัดไป',icon:'info',customClass:{popup:'rounded-4'}})">
                <div class="action-card-icon bg-info-subtle text-info" style="background:#eff6ff;color:#2563eb;">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                </div>
                <h3 class="fs-5 fw-bold text-dark mb-1">บันทึกเบิกค่าใช้จ่าย</h3>
                <p class="text-muted small mb-4 flex-grow-1">
                    สร้างใบขอเบิกเงินสดย่อย หรือบันทึกค่าใช้จ่ายสำนักงานและสาขาตามสิทธิ์
                </p>
                <div class="d-flex align-items-center text-primary fw-semibold small">
                    <span>สร้างรายการใหม่</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </div>
            </div>
        </div>

        <!-- Action: Call Supervisor -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="action-card">
                <div class="action-card-icon bg-warning-subtle text-warning" style="background:#fffbeb;color:#f59e0b;">
                    <i class="bi bi-headset"></i>
                </div>
                <h3 class="fs-5 fw-bold text-dark mb-1">ติดต่อหัวหน้างาน / ผู้อนุมัติ</h3>
                <p class="text-muted small mb-3">
                    <?= htmlspecialchars($chief_name) ?>
                </p>
                <?php if ($mobile_chief): ?>
                    <a href="tel:<?= htmlspecialchars($mobile_chief) ?>" class="btn btn-outline-warning w-100 mt-auto py-2 rounded-3 text-dark fw-medium">
                        <i class="bi bi-telephone-fill text-warning me-2"></i> โทร <?= htmlspecialchars($mobile_chief) ?>
                    </a>
                <?php else: ?>
                    <div class="text-muted small mt-auto fst-italic">ไม่พบข้อมูลเบอร์โทรศัพท์</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
