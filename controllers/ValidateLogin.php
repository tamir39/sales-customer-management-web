<?php
include('../models/Verify.php');
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);
    $login = new Verify($username);

    if($login->getVerification($password)){
        $_SESSION['role'] = $login->getRole();
        $_SESSION['full_name'] = $login->getFullname(); 
        $_SESSION['username'] = $login->getUsername();
        $_SESSION['id'] = $login->getId();
        
        require_once('../models/DbConnection.php');
        $conn = (new ConnectDatabase())->getConnection();
        if (!$login->getLoginflag() && $_SESSION['role'] == 'employee') {
            $_SESSION["message"] = "❌ Hãy đăng nhập thông qua link được gửi, không thể đăng nhập trực tiếp.";
            $conn->prepare("UPDATE employee SET status = ? WHERE user_name = ?")->execute([0, $_SESSION['username']]);
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: /doancuoiky/views/Login.php"); 
            exit();
        }
        elseif ($login->getBlocked()) {
            $_SESSION["message"] = "❌ Đăng nhập bị từ chối! Tài khoản đã bị khóa.";
            $_SESSION['alert_type'] = "alert-danger";
            $conn->prepare("UPDATE employee SET status = ? WHERE user_name = ?")->execute([0, $_SESSION['username']]);
            header("Location: /doancuoiky/views/Login.php"); 
            exit();
        } 
        $_SESSION['user'] = True;
        header("Location: /doancuoiky/views/Main.php");
        exit();
    } else {
        $_SESSION["message"] = "❌ Sai tên đăng nhập hoặc mật khẩu! Vui lòng thử lại.";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: /doancuoiky/views/Login.php"); 
        exit();
    }
}   

elseif (isset($_POST['user_name']) && isset($_POST['pass_word'])) {
    $username = test_input($_POST["user_name"]);
    $password = test_input($_POST["pass_word"]);
    $login = new Verify($username, $password);

    if($login->getVerification($password)){
        $_SESSION['role'] = $login->getRole();
        $_SESSION['full_name'] = $login->getFullname(); 
        $_SESSION['username'] = $login->getUsername();
        $_SESSION['id'] = $login->getId();
        if (!$login->getLoginflag() && $_SESSION['role'] == 'employee') {
            if (isset($_SESSION['first_login'])) {
                $login->setLoginFlag(true);
                unset($_SESSION['first_login']);
                $_SESSION['user'] = True;
                header("Location: /doancuoiky/views/Main.php");
                exit();
            }
        }
        elseif ($login->getBlocked()) {
            $_SESSION["message"] = "❌ Đăng nhập bị từ chối! Tài khoản đã bị khóa.";
            $_SESSION['alert_type'] = "alert-danger";
        }
    } else {
        if (isset($_SESSION['first_login'])) {
            $_SESSION["message"] = "❌ Sai tên đăng nhập hoặc mật khẩu! Vui lòng thử lại.";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: " . $_SESSION['first_login']);
            exit();
        }
    }
}   
?>


