<?php
session_start();
require_once('../models/Verify.php');
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["old_password"]) && isset($_POST["update_password"])) {
        $PasswordObject = new Verify($_SESSION['username']);
        
        if(!$PasswordObject->getVerification($_POST['old_password'])){
            $_SESSION["message"] = "❌ Mật khẩu cũ không đúng!";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Profile.php");
            exit();
        }

        if($_POST["update_password"] == $_POST["old_password"]){
            $_SESSION["message"] = "❌ Không thể dùng lại mật khẩu cũ!";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Profile.php");
            exit();
        }
        
        if($PasswordObject->changePassword($_POST["update_password"])){
            $_SESSION["message"] = "✅ Thay đổi mật khẩu thành công!";
            $_SESSION['alert_type'] = "alert-success";

        } else {
            $_SESSION["message"] = "❌ Lỗi: không thể thay đổi mật khẩu.";
            $_SESSION['alert_type'] = "alert-danger";
        }
    }
header("Location: /doancuoiky/views/Profile.php");
exit();
?>
