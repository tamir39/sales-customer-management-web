<?php
require_once('../models/Verify.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name']) && isset($_POST['phone'])) {

    $user = new Verify($_SESSION['username']);
    
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $avatar = file_get_contents($_FILES['avatar']['tmp_name']); 
    } else {
        $avatar = null; 
    }
    
    if ($user->changeProfile($full_name, $phone, $avatar)) {
        $_SESSION['message'] = "✅ Thông tin cập nhật thành công!";
        $_SESSION['full_name'] = $user->getFullname();
        $_SESSION['alert_type'] = "alert-success";
    } 
}   else {
    $_SESSION['message'] = "❌ Lỗi: Không thể đổi thông tin cá nhân.";
    $_SESSION['alert_type'] = "alert-danger";
}

header("Location: /doancuoiky/views/Profile.php");
exit();
?>
