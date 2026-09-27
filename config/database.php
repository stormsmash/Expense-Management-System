<?php
/**
 * Database Configuration
 * Expense Management System — TumrubThai Herbal Co., Ltd.
 *
 * PDO Singleton pattern with MySQL and SQLite auto-fallback.
 */

// 0 = test server (192.168.1.59), 1 = production server
$server_url_index = 1;

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'tumrubthai';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        global $db_host, $db_user, $db_pass, $db_name;
        
        $mysql_dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 1,
        ];
        
        try {
            // Attempt MySQL connection
            $pdo = new PDO($mysql_dsn, $db_user, $db_pass, $options);
        } catch (Throwable $e) {
            // Fallback to SQLite for local development / testing
            $dataDir = __DIR__ . '/../data';
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0777, true);
            }
            $sqliteFile = $dataDir . '/database.sqlite';
            $isNew = !file_exists($sqliteFile);
            
            $pdo = new PDO("sqlite:" . $sqliteFile, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            if ($isNew || filesize($sqliteFile) === 0) {
                initSqliteDatabase($pdo);
            }
        }
    }
    return $pdo;
}

function initSqliteDatabase(PDO $pdo): void {
    // 1. Employee card table
    $pdo->exec("CREATE TABLE IF NOT EXISTS employee_card (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        seqno INTEGER,
        employee_code TEXT,
        status INTEGER DEFAULT 1
    )");

    // 2. Employee table
    $pdo->exec("CREATE TABLE IF NOT EXISTS employee (
        seqno INTEGER PRIMARY KEY,
        name_thai TEXT,
        position_no TEXT,
        branch TEXT,
        mobile TEXT,
        file INTEGER DEFAULT 0
    )");

    // 3. Employee position table
    $pdo->exec("CREATE TABLE IF NOT EXISTS employee_position (
        position_no TEXT PRIMARY KEY,
        position_name TEXT
    )");

    // 4. Employee group & chief tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS employee_group (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        group_seqno INTEGER,
        chief_no TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS employee_chief (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        chief_no TEXT,
        chief_seqno INTEGER
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS employee_location (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        seqno INTEGER,
        locacode TEXT
    )");

    // 5. Expense group table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expense_group (
        group_id INTEGER PRIMARY KEY AUTOINCREMENT,
        group_name TEXT NOT NULL,
        group_idx INTEGER DEFAULT 1
    )");

    // 6. Expense item table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expense_item (
        item_id INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id INTEGER NOT NULL,
        item_name TEXT NOT NULL,
        price_limit NUMERIC DEFAULT 0,
        acc_code_office TEXT,
        acc_desc_office TEXT,
        acc_code_shop TEXT,
        acc_desc_shop TEXT,
        acc_code_special TEXT,
        acc_desc_special TEXT,
        branch_group INTEGER DEFAULT 0,
        can_select_branch INTEGER DEFAULT 0,
        item_idx INTEGER DEFAULT 1
    )");

    // 7. System log table
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id TEXT,
        ip TEXT,
        doc_no TEXT,
        doc_type TEXT,
        action TEXT,
        trans_time TEXT
    )");

    // Seed initial mock data
    $pdo->exec("INSERT INTO employee_position (position_no, position_name) VALUES 
        ('P001', 'ผู้จัดการฝ่ายจัดซื้อ / Purchasing Manager'),
        ('P002', 'เจ้าหน้าที่ฝ่ายบัญชีและการเงิน'),
        ('P003', 'ผู้จัดการสาขา')
    ");

    $pdo->exec("INSERT INTO employee (seqno, name_thai, position_no, branch, mobile, file) VALUES 
        (101, 'สมชาย ใจดี (Office)', 'P001', '0001', '080-000-0001', 0),
        (102, 'สมหญิง รักงาน (Accounting)', 'P002', '0001', '080-000-0002', 0),
        (103, 'มานะ มีสุข (Branch)', 'P003', '0002', '080-000-0003', 0)
    ");

    $pdo->exec("INSERT INTO employee_card (seqno, employee_code, status) VALUES 
        (101, 'EMP001', 1),
        (102, 'EMP002', 1),
        (103, 'EMP003', 1)
    ");

    $pdo->exec("INSERT INTO employee_group (group_seqno, chief_no) VALUES (101, 'CH001')");
    $pdo->exec("INSERT INTO employee_chief (chief_no, chief_seqno) VALUES ('CH001', 101)");

    $pdo->exec("INSERT INTO expense_group (group_id, group_name, group_idx) VALUES 
        (1, 'ค่าใช้จ่ายสำนักงานและเครื่องเขียน', 1),
        (2, 'ค่าเดินทางและยานพาหนะ', 2),
        (3, 'ค่าซ่อมบำรุงและอุปกรณ์', 3),
        (4, 'ค่ารับรองและอาหาร', 4)
    ");

    $pdo->exec("INSERT INTO expense_item (item_id, group_id, item_name, price_limit, acc_code_office, acc_desc_office, acc_code_shop, acc_desc_shop, acc_code_special, acc_desc_special, branch_group, can_select_branch, item_idx) VALUES 
        (1, 1, 'กระดาษ A4 และหมึกพิมพ์', 2500, '51010-01', 'ค่าเครื่องเขียนและแบบพิมพ์', '51010-02', 'ค่าเครื่องเขียนสาขา', '51010-99', 'ค่าใช้จ่ายพิเศษ', 0, 0, 1),
        (2, 1, 'อุปกรณ์สำนักงานเบ็ดเตล็ด', 1000, '51010-03', 'ค่าใช้จ่ายสำนักงาน', '51010-04', 'ค่าใช้จ่ายสาขา', '51010-99', 'ค่าใช้จ่ายพิเศษ', 0, 0, 2),
        (3, 2, 'ค่าน้ำมันรถยนต์ส่วนกลาง', 5000, '52010-01', 'ค่าน้ำมันสำนักงาน', '52010-02', 'ค่าน้ำมันสาขา', '52010-99', 'ค่าน้ำมันพิเศษ', 1, 1, 1),
        (4, 2, 'ค่าทางด่วนและค่าที่จอดรถ', 1500, '52020-01', 'ค่าผ่านทาง/จอดรถ', '52020-02', 'ค่าผ่านทางสาขา', '52020-99', 'ค่าทางด่วนพิเศษ', 0, 0, 2),
        (5, 3, 'ค่าซ่อมเครื่องปรับอากาศ', 8000, '53010-01', 'ค่าซ่อมแซมสำนักงาน', '53010-02', 'ค่าซ่อมแซมสาขา', '53010-99', 'ค่าซ่อมพิเศษ', 0, 0, 1)
    ");
}

if (!function_exists('history')) {
    function history(string $userName, string $docNo, string $docType, string $action): void {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $sql = 'INSERT INTO system_log (user_id, ip, doc_no, doc_type, action, trans_time) VALUES (?, ?, ?, ?, ?, datetime("now", "localtime"))';
        try {
            $stmt = getDB()->prepare($sql);
            $stmt->execute([$userName, $ip, $docNo, $docType, $action]);
        } catch (Throwable $e) {
            error_log('[DB] history() error: ' . $e->getMessage());
        }
    }
}
