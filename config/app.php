<?php
/**
 * Application Configuration
 * Expense Management System — TumrubThai Herbal Co., Ltd.
 */

define('APP_NAME',    'Expense Management System');
define('APP_VERSION', '2.0.0');
define('APP_COMPANY', 'ระบบจัดการค่าใช้จ่าย (EMS)');

// Base URL (auto-detect or set manually)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . '://' . $host . '/expense-management-system');

// External app URLs
define('TRTSCRIPT_URL', '../trtscript/');
define('SYSTEM_DOC_PATH', '../system_doc/');

// Session timeout (seconds)
define('SESSION_TIMEOUT', 3600);

// Asset base path (relative from any page)
define('ASSET_PATH', BASE_URL . '/assets');
