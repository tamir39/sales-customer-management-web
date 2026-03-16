<?php
session_start();
require_once('../models/ExecEmployee.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $employee = new ExecEmployee($email);
    
    if ($employee->deleteEmployee()) {
        $_SESSION['message'] = "✅ ". $email ." được xóa thành công!";
        $_SESSION['alert_type'] = "alert-success";
    } else {
        $_SESSION['message'] = "❌ Lỗi: Không thể xóa nhân viên.";
        $_SESSION['alert_type'] = "alert-danger";
    }

}

header("Location: /doancuoiky/views/Employee.php");
exit();
?>
