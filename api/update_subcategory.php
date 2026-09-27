<?php
/**
 * API: Get & Update Subcategory (Expense Item)
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $subId = (int)($_GET['subcategoryId'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM expense_item WHERE item_id = ?");
    $stmt->execute([$subId]);
    $item = $stmt->fetch();
    if ($item) {
        echo json_encode($item);
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูลรายการ']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId           = (int)($_POST['subcategoryId'] ?? 0);
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

    if ($itemId <= 0 || $itemName === '') {
        echo 'ข้อมูลไม่ถูกต้อง';
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE expense_item SET 
            item_name = ?, price_limit = ?, 
            acc_code_office = ?, acc_desc_office = ?, 
            acc_code_shop = ?, acc_desc_shop = ?, 
            acc_code_special = ?, acc_desc_special = ?, 
            branch_group = ?, can_select_branch = ?
            WHERE item_id = ?");
        $stmt->execute([
            $itemName, $priceLimit, $accCodeOffice, $accDescOffice, $accCodeShop, $accDescShop, $accCodeSpecial, $accDescSpecial, $branchGroup, $canSelectBranch, $itemId
        ]);

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
