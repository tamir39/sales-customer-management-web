<?php
session_start();
include('../models/ExecEmployee.php');

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (empty($_POST['fullname']) || empty($_POST['email'])) {
    $_SESSION['message'] = "❌ Thiếu thông tin!";
    $_SESSION['alert_type'] = "alert-danger";  
    header("Location: /doancuoiky/views/Employee.php"); 
    exit();
}

$fullname = test_input($_POST["fullname"]);
$email = test_input($_POST["email"]);

$createAccount = new ExecEmployee($email, $fullname);
if ($createAccount->createEmployee()) {
    $_SESSION['link_for'] = $email;
    header("Location:/doancuoiky/controllers/SendLoginLink.php");
    exit();
} else {
    $_SESSION['message'] = "❌ Error: Could not create the employee.";
    $_SESSION['alert_type'] = "alert-danger";  
    header("Location:/doancuoiky/views/Employee.php");
    exit(); 
}
?>
