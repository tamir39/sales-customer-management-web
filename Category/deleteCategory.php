<?php
require_once('CategoryClass.php');
session_start();
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category = new Category();
    $categoryId = $_GET['id'];

    if ($category->deleteCategory($categoryId)) {
        $_SESSION['message'] = "✅ Đã xóa danh mục!";
        $_SESSION['alert_type'] = "alert-success";  
    } else {
        $_SESSION['message'] = "❌ Lỗi: không thể xóa danh mục.";
        $_SESSION['alert_type'] = "alert-danger";
    }
header("Location: Category.php");
exit();
}
?>
