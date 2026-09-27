<?php
/**
 * Logout
 * Expense Management System — TumrubThai Herbal Co., Ltd.
 */
session_start();
session_destroy();
header('Location: ../index.php');
exit();
