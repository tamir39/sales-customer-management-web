<?php
session_start();
require_once('../models/Verify.php');
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["old_password"]) && isset($_POST["new_password"])) {
        $PasswordObject = new Verify($_SESSION['username']);
        
        if(!$PasswordObject->getVerification($_POST['old_password'])){
            $_SESSION["message"] = "❌ Mật khẩu cũ không đúng!";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Main.php");
            exit();
        }

        if($_POST["new_password"] == $_POST["old_password"]){
            $_SESSION["message"] = "❌ Không thể dùng lại mật khẩu cũ!";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Main.php");
            exit();
        }
        
        if($PasswordObject->changePassword($_POST["new_password"])){
            $_SESSION["message"] = "✅ Thay đổi mật khẩu thành công! vui lòng đăng nhập lại để dùng chức năng.";
            $_SESSION['alert_type'] = "alert-success";
            if($_SESSION['role'] == 'employee'){
                require_once('../models/DbConnection.php');
                $conn = (new ConnectDatabase())->getConnection();
            
                $stmt = $conn->prepare("UPDATE employee SET status = ? WHERE user_name = ?");
                $stmt->execute([0, $_SESSION['username']]);
            }
            unset($_SESSION['user']);
            header("Location: /doancuoiky/views/Login.php");
            exit();
            
        } else {
            $_SESSION["message"] = "❌ Lỗi: không thể thay đổi mật khẩu.";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Main.php");
            exit();
        }
    }
?>
