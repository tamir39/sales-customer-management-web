<?php
session_start();
require_once('../models/ExecEmployee.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $employee = new ExecEmployee($email);
    
    if ($employee->blockEmployee()) {
        $_SESSION['message'] = "✅ ". $email ." được khóa thành công!";
        $_SESSION['alert_type'] = "alert-success";
    } else {
        $_SESSION['message'] = "❌ Lỗi: không thể khóa nhân viên.";
        $_SESSION['alert_type'] = "alert-danger";
    }
}

header("Location: /doancuoiky/views/Employee.php");
exit();
?>
