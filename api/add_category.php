<?php
/**
 * API: Add Category (Expense Group)
 */
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['categoryName'] ?? '');
    if ($categoryName === '') {
        echo 'ชื่อหมวดหมู่ต้องไม่ว่างเปล่า';
        exit;
    }

    try {
        $pdo = getDB();
        $stmtMax = $pdo->query("SELECT MAX(group_idx) AS max_idx FROM expense_group");
        $maxRow = $stmtMax->fetch();
        $nextIdx = ($maxRow['max_idx'] ?? 0) + 1;

        $stmt = $pdo->prepare("INSERT INTO expense_group (group_name, group_idx) VALUES (?, ?)");
        $stmt->execute([$categoryName, $nextIdx]);
        echo 'success';
    } catch (Throwable $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
