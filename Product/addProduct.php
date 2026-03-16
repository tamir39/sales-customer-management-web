<?php
session_start();
require_once('ProductController.php');
$controller = new ProductController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'];
    $productBarcode = $_POST['product_barcode'];
    $productQuantity = $_POST['product_quantity'];
    $productImportPrice = $_POST['product_import_price'];
    $productRetailPrice = $_POST['product_retail_price'];
    $productCategory = $_POST['product_category'];
    if (!is_numeric($productImportPrice) || $productImportPrice <= 0 || 
        !is_numeric($productRetailPrice) || $productRetailPrice <= 0 || 
        !is_numeric($productQuantity) || $productQuantity <= 0) {
        $_SESSION['message'] = "❌ Lỗi: Giá nhập, giá bán và số lượng phải là số hợp lệ và lớn hơn 0!";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: addProduct.php");   
        exit();
    }
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageData = file_get_contents($_FILES['product_image']['tmp_name']);
    } else {
        $imageData = null;
    }
    $result = $controller->addProduct($productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData);
    if ($result === true) {
        $_SESSION['message'] = "✅ Đã thêm sản phẩm thành công!";
        $_SESSION['alert_type'] = "alert-success";
        header("Location: Product.php");
        exit();
    } else {
        $_SESSION['message'] = "❌ Lỗi: ".$result."!";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: addProduct.php");   
        exit();
    }
}
include_once(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .container{
            margin-top: 50px;
        }
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
    <a href="/doancuoiky/Product/Product.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container box">
        <h1>Thêm Sản Phẩm Mới</h1>
        <form method="POST" action="addProduct.php"  enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="product_name" class="form-label">Tên Sản Phẩm:</label>
                    <input type="text" id="product_name" name="product_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="product_barcode" class="form-label">Mã Vạch:</label>
                    <input type="text" id="product_barcode" name="product_barcode" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="product_import_price" class="form-label">Giá Nhập:</label>
                    <input type="text" id="product_import_price" name="product_import_price" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="product_retail_price" class="form-label">Giá Bán:</label>
                    <input type="text" id="product_retail_price" name="product_retail_price" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="product_quantity" class="form-label">Số Lượng:</label>
                    <input type="text" id="product_quantity" name="product_quantity" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="product_category" class="form-label">Danh Mục:</label>
                    <select id="product_category" name="product_category" class="form-control" required>
                        <?php
                        $categories = $controller->getCategories();
                        foreach ($categories as $category) {
                            echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
        <div class="col-md-6">
            <label for="product_image" class="form-label">Ảnh Sản Phẩm:</label>
            <input type="file" id="product_image" name="product_image" class="form-control">
        </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-gray btn-animated">Thêm</button>
            </div>
        </form>
    </div>
</body>
</html>
