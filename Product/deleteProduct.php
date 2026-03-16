<?php
require_once('ProductController.php');
session_start();
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $controller = new ProductController();

    $productId = $_GET['id'];

    $result = $controller->deleteProduct($productId);

    if ($result == "Đã xóa sản phẩm thành công!") {
        $_SESSION['message'] = "✅ ".$result."";
        $_SESSION['alert_type'] = "alert-success";  
    } else {
        $_SESSION['message'] = "❌ Lỗi: ".$result."";
        $_SESSION['alert_type'] = "alert-danger";
    }
} else {
    $_SESSION['message'] = "❌ Lỗi: không thể xóa sản phẩm";
    $_SESSION['alert_type'] = "alert-danger";
}
header("Location: Product.php");
exit();
?>
