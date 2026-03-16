<?php
session_start();
require_once('../models/ExecEmployee.php');
require_once('../models/DbConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $employee = new ExecEmployee($email);
    
    if ($employee->unBlockEmployee()) {
        $_SESSION['message'] = "✅ ". $email ." được gỡ khóa thành công!";
        $_SESSION['alert_type'] = "alert-success";
    } else {
        $_SESSION['message'] = "❌ Lỗi: không thể gỡ khóa nhân viên.";
        $_SESSION['alert_type'] = "alert-danger";
    }

}
header("Location: /doancuoiky/views/Employee.php");
exit();
?>
