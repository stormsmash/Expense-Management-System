<?php
/**
 * Main Entry & Login Page — Modern Edition
 * Expense Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="assets/img/app_logo.png">
    <style>
        :root {
            --primary: #0f766e;
            --primary-hover: #0d5f58;
            --font-main: 'Prompt', 'Plus Jakarta Sans', sans-serif;
        }
        body {
            font-family: var(--font-main);
            background: linear-gradient(135deg, #0f172a 0%, #0d3b66 50%, #0f766e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        /* Background Glow Orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            pointer-events: none;
        }
        .glow-1 {
            width: 380px;
            height: 380px;
            background: #0d9488;
            top: -50px;
            left: -50px;
        }
        .glow-2 {
            width: 420px;
            height: 420px;
            background: #2563eb;
            bottom: -60px;
            right: -60px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            max-width: 480px;
            width: 100%;
            padding: 44px 36px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 10;
            animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .brand-badge {
            width: 76px;
            height: 76px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 118, 110, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        .btn-ems-submit {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            color: #fff;
            font-weight: 600;
            padding: 13px;
            border-radius: 12px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(15, 118, 110, 0.3);
        }
        .btn-ems-submit:hover {
            background: linear-gradient(135deg, #0d5f58 0%, #0f766e 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 118, 110, 0.4);
        }
        .demo-chip {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.88rem;
        }
        .demo-chip:hover {
            background: #f0fdfa;
            border-color: #0f766e;
            transform: translateX(3px);
        }
        .form-control-custom {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        }
    </style>
</head>
<body>

<div class="glow-orb glow-1"></div>
<div class="glow-orb glow-2"></div>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="brand-badge">
            <img src="assets/img/app_logo.png" alt="EMS Logo" style="width:52px;height:52px;border-radius:12px;object-fit:contain;">
        </div>
        <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars(APP_NAME) ?></h3>
        <p class="text-muted small mb-0"><?= htmlspecialchars(APP_COMPANY) ?> (v<?= htmlspecialchars(APP_VERSION) ?>)</p>
    </div>

    <form action="auth/login.php" method="POST" id="loginForm">
        <div class="mb-3">
            <label for="code" class="form-label fw-semibold text-secondary small">
                <i class="bi bi-qr-code-scan text-teal me-1" style="color:var(--primary);"></i> สแกนบัตร / รหัสพนักงาน
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;border:1.5px solid #e2e8f0;">
                    <i class="bi bi-person-vcard text-muted"></i>
                </span>
                <input type="text" class="form-control form-control-custom border-start-0" id="code" name="code" 
                       placeholder="เช่น EMP001 หรือยิงบาร์โค้ด" style="border-radius:0 12px 12px 0;" autofocus required>
            </div>
        </div>

        <button type="submit" class="btn btn-ems-submit w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบ
        </button>
    </form>

    <div class="mt-4 pt-3 border-top">
        <div class="text-muted small fw-semibold mb-2 text-center">
            <i class="bi bi-lightning-charge-fill text-warning"></i> เลือกบัญชีตัวอย่างเพื่อทดสอบ:
        </div>
        <div class="d-flex flex-column gap-2">
            <div class="demo-chip d-flex justify-content-between align-items-center" onclick="quickLogin('EMP001')">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">EMP001</span>
                    <span class="text-dark fw-medium">สมชาย ใจดี</span>
                    <span class="text-muted small">(ผู้จัดการฝ่ายจัดซื้อ)</span>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </div>
            <div class="demo-chip d-flex justify-content-between align-items-center" onclick="quickLogin('EMP002')">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">EMP002</span>
                    <span class="text-dark fw-medium">สมหญิง รักงาน</span>
                    <span class="text-muted small">(ฝ่ายบัญชี)</span>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </div>
            <div class="demo-chip d-flex justify-content-between align-items-center" onclick="quickLogin('EMP003')">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">EMP003</span>
                    <span class="text-dark fw-medium">มานะ มีสุข</span>
                    <span class="text-muted small">(ผู้จัดการสาขา)</span>
                </div>
                <i class="bi bi-chevron-right text-muted small"></i>
            </div>
        </div>
    </div>
</div>

<script>
function quickLogin(code) {
    document.getElementById('code').value = code;
    document.getElementById('loginForm').submit();
}
</script>

</body>
</html>
