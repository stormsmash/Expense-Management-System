<?php
/**
 * Auth Guard
 * Redirect unauthenticated users back to the login/landing page.
 */
if (empty($_SESSION['user_id'])) {
    session_destroy();
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>';
    echo '<script>
        $(document).ready(function () {
            Swal.fire({
                title: "เซสชั่นหมดอายุ",
                text: "กรุณาเข้าสู่ระบบอีกครั้ง",
                icon: "warning",
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true
            });
        });
    </script>';
    echo '<meta http-equiv="refresh" content="2; url=../index.php">';
    exit();
}
