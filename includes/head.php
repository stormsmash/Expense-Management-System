<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="ระบบจัดการค่าใช้จ่าย — Expense Management System">
<meta name="author" content="EMS">
<link href="../assets/img/app_logo.png" rel="icon" type="image/png">
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons & Font Awesome -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/jquery.datetimepicker.css">
<!-- Core JS libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php $clear_cache = '?' . time(); ?>
<link rel="stylesheet" href="../assets/css/style.css<?php echo $clear_cache; ?>">
<title><?php echo htmlspecialchars($title ?? APP_NAME); ?> — EMS</title>
</head>
<body>
<div id="w_load" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div class="spinner-border text-teal" style="color:var(--primary);width:3rem;height:3rem;" role="status">
        <span class="visually-hidden">กำลังโหลด...</span>
    </div>
</div>
