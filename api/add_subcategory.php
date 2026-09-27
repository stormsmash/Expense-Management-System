<?php
/**
 * API: Add Subcategory (Expense Item)
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId          = (int)($_POST['categoryId'] ?? 0);
    $itemName         = trim($_POST['subcategoryName'] ?? '');
    $priceLimit       = (float)($_POST['price_limit'] ?? 0);
    $accCodeOffice    = trim($_POST['acc_code_office'] ?? '');
    $accDescOffice    = trim($_POST['acc_desc_office'] ?? '');
    $accCodeShop      = trim($_POST['acc_code_shop'] ?? '');
    $accDescShop      = trim($_POST['acc_desc_shop'] ?? '');
    $accCodeSpecial   = trim($_POST['acc_code_special'] ?? '');
    $accDescSpecial   = trim($_POST['acc_desc_special'] ?? '');
    $branchGroup      = (int)($_POST['branchGroup'] ?? 0);
    $canSelectBranch  = (int)($_POST['canSelectBranch'] ?? 0);

    if ($groupId <= 0 || $itemName === '') {
        echo 'กรุณากรอกข้อมูลให้ครบถ้วน';
        exit;
    }

    try {
        $pdo = getDB();
        $stmtMax = $pdo->prepare("SELECT MAX(item_idx) AS max_idx FROM expense_item WHERE group_id = ?");
        $stmtMax->execute([$groupId]);
        $maxRow = $stmtMax->fetch();
        $nextIdx = ($maxRow['max_idx'] ?? 0) + 1;

        $stmt = $pdo->prepare("INSERT INTO expense_item 
            (group_id, item_name, price_limit, acc_code_office, acc_desc_office, acc_code_shop, acc_desc_shop, acc_code_special, acc_desc_special, branch_group, can_select_branch, item_idx)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $groupId, $itemName, $priceLimit, $accCodeOffice, $accDescOffice, $accCodeShop, $accDescShop, $accCodeSpecial, $accDescSpecial, $branchGroup, $canSelectBranch, $nextIdx
        ]);
        
        $itemId = (int)$pdo->lastInsertId();

        // Handle image upload if present
        if (!empty($_FILES['subcategoryImage']['name']) && $_FILES['subcategoryImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../system_doc/items_list/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['subcategoryImage']['name'], PATHINFO_EXTENSION);
            $fileName = $itemId . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['subcategoryImage']['tmp_name'], $uploadDir . $fileName);
        }

        echo 'success';
    } catch (Throwable $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
