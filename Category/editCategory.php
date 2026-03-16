<?php
include_once('CategoryController.php');
include_once('CategoryClass.php');
session_start();
$categoryController = new CategoryController();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID không hợp lệ.");
}

$categoryId = intval($_GET['id']);
$category = $categoryController->getCategoryModel()->getCategoryById($categoryId);

if (!$category) {
    die("Danh mục không tồn tại!");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = trim($_POST['category_name']);
    $categoryDescription = trim($_POST['category_description']);

    if (empty($categoryName)) {
        echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!');</script>";
    } else {
        $updateResult = $categoryController->updateCategory($categoryId, $categoryName, $categoryDescription, $_SESSION['id']);
        if ($updateResult === true) {
            $_SESSION['message'] = "✅ Đã cập nhật danh mục!";
            $_SESSION['alert_type'] = "alert-success";      
        } else {
            $_SESSION['message'] = "❌ Lỗi: ".$updateResult."!";
            $_SESSION['alert_type'] = "alert-danger";
        }
    }
header("Location: /doancuoiky/Category/Category.php");
exit();
}
include_once(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Danh Mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="product.css">
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

        label {
            font-weight: 600;  
            color: #333;  
            display: block;  
            margin-bottom: 5px;  
        }

        .form-control {
            border: 1px solid #ccc;  
            border-radius: 5px;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #007bff;  
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);  
        }
    </style>
</head>
<body>
    <a href="/doancuoiky/Category/Category.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
<div class="container box">
    <h1>Chỉnh sửa Danh Mục</h1>
    <form method="POST" action="editCategory.php?id=<?php echo $categoryId; ?>">
        <div class="row">
            <label for="category_name">Tên Danh Mục:</label>
            <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        <div class="row">
            <label for="category_description">Mô Tả:</label>
            <textarea id="category_description" name="category_description" class="form-control" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        <div style="text-align: center;">
            <button type="submit" class="btn btn-animated">Cập nhật</button>
        </div>
    </form>
</div>
</body>
</html>