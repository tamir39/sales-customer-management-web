<?php
require_once('CategoryClass.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = trim($_POST['category_name']);
    $categoryDescription = trim($_POST['category_description']);

    if (!empty($categoryName)) {
        $category = new Category();
        $addResult = $category->addCategory($categoryName, $categoryDescription, $_SESSION['id']);

        if ($addResult) {
            $_SESSION['message'] = "✅ Đã thêm danh mục thành công!";
            $_SESSION['alert_type'] = "alert-success";
            
        } else {
            $_SESSION['message'] = "❌ Lỗi: ".$addResult."!";
            $_SESSION['alert_type'] = "alert-danger";
        }
        header("Location: /doancuoiky/Category/Category.php");
        exit();
    } else {
        $_SESSION['message'] = "❌ Lỗi: Vui lòng nhập tên danh mục và người tạo!";
        $_SESSION['alert_type'] = "alert-danger";
    }

}
include_once(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Danh Mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .box {
            max-width: 600px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .btn-gray.btn-animated {
            background: #444444;
            color: #ffffff;
        }

        .btn-animated:active {
            transform: scale(0.95);
        }

        .btn-animated {
            background: #000000;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            padding: 8px 15px;
        }

        .btn-animated:hover {
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <a href="/doancuoiky/Category/Category.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container box">
        <h1 style="display: flex; justify-content: center;">Thêm Danh Mục Mới</h1>
        <form method="POST" action="addCategory.php">
            <div class="mb-2">
                <label for="category_name" class="form-label">Tên Danh Mục:</label>
                <input type="text" id="category_name" name="category_name" class="form-control" required>
            </div>

            <div class="mb-2">
                <label for="category_description" class="form-label">Mô Tả:</label>
                <textarea id="category_description" name="category_description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-buttons text-center mt-2">
                <button type="submit" class="btn btn-gray btn-animated">Thêm</button>
            </div>
        </form>
    </div>
</body>
</html>
