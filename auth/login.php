<?php
/**
 * Login Handler — QR Card Authentication
 * Expense Management System — TumrubThai Herbal Co., Ltd.
 */
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$title = 'เข้าสู่ระบบ';
require_once __DIR__ . '/../includes/head.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<?php

$code = trim($_GET['code'] ?? $_POST['code'] ?? '');

if ($code !== '') {
    $pdo = getDB();

    // 1. Verify employee card
    $stmt = $pdo->prepare("SELECT seqno FROM employee_card WHERE employee_code = ? AND status = 1");
    $stmt->execute([$code]);
    $card = $stmt->fetch();

    if ($card) {
        $seqno = $card['seqno'];

        // 2. Fetch employee details
        $stmt2 = $pdo->prepare(
            "SELECT a.name_thai, a.position_no, a.branch, b.position_name
             FROM employee a
             JOIN employee_position b ON a.position_no = b.position_no
             WHERE a.seqno = ? AND a.file = 0"
        );
        $stmt2->execute([$seqno]);
        $emp = $stmt2->fetch();

        if ($emp) {
            $_SESSION['user_id']          = $seqno;
            $_SESSION['user_name_thai']   = $emp['name_thai'];
            $_SESSION['user_branch_no']   = $emp['branch'];
            $_SESSION['user_position_no'] = $emp['position_no'];
            $_SESSION['user_position']    = $emp['position_name'];

            history($emp['name_thai'], '', 'expense_management', 'Log In');

            echo '<div class="overlay">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-grow text-primary" role="status" style="width:3rem;height:3rem;z-index:30;">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                  </div>';
            echo '<meta http-equiv="refresh" content="1; url=../pages/dashboard.php">';

        } else {
            echo '<script>$(document).ready(function(){
                Swal.fire({text:"ระบบนี้ใช้เฉพาะ Office",icon:"warning",timer:2000,showConfirmButton:false,timerProgressBar:true});
            });</script>';
            session_destroy();
            echo '<meta http-equiv="refresh" content="2; url=../index.php">';
        }

    } else {
        echo '<script>$(document).ready(function(){
            Swal.fire({title:"error",text:"บัตรพนักงานไม่ถูกต้อง...!",icon:"error",timer:1000,showConfirmButton:false,timerProgressBar:true});
        });</script>';
        session_destroy();
        echo '<meta http-equiv="refresh" content="1; url=../index.php">';
    }

} else {
    echo '<script>$(document).ready(function(){
        Swal.fire({title:"error",text:"เกิดข้อผิดพลาด...!",icon:"error",timer:1000,showConfirmButton:false,timerProgressBar:true});
    });</script>';
    session_destroy();
    echo '<meta http-equiv="refresh" content="1; url=../index.php">';
}
?>
