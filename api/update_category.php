<?php
/**
 * API: Get & Update Category (Expense Group)
 */
require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $catId = (int)($_GET['categoryId'] ?? 0);
    $stmt = $pdo->prepare("SELECT group_id AS category_id, group_name AS category_name FROM expense_group WHERE group_id = ?");
    $stmt->execute([$catId]);
    $cat = $stmt->fetch();
    if ($cat) {
        echo json_encode($cat);
    } else {
        echo json_encode(['error' => 'ไม่พบหมวดหมู่ที่ระบุ']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = (int)($_POST['categoryId'] ?? 0);
    $categoryName = trim($_POST['categoryName'] ?? '');
    
    if ($catId <= 0 || $categoryName === '') {
        echo 'ข้อมูลไม่ถูกต้อง';
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE expense_group SET group_name = ? WHERE group_id = ?");
        $stmt->execute([$categoryName, $catId]);
        echo 'success';
    } catch (Throwable $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
