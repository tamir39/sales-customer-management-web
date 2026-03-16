<?php
session_start();
require_once('ProductController.php');

$productController = new ProductController();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    exit();
}

$productId = $_GET['id'];
$product = $productController->getProductById($productId);

if (!$product) {
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = trim($_POST['product_name']);
    $productBarcode = trim($_POST['product_barcode']);
    $productQuantity = $_POST['product_quantity'];
    $productImportPrice = $_POST['product_import_price'];
    $productRetailPrice = $_POST['product_retail_price'];
    $productCategory = $_POST['product_category'];
    if (empty($productName) || empty($productBarcode) || empty($productQuantity) || empty($productImportPrice) || empty($productRetailPrice)) {
        $_SESSION['message'] = "❌ Lỗi: Vui lòng nhập đầy đủ thông tin!";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: editProduct.php?id=$productId");   
        exit();
    } else if (!is_numeric($productQuantity) || !is_numeric($productImportPrice) || !is_numeric($productRetailPrice)) {
        $_SESSION['message'] = "❌ Lỗi: Số lượng, giá nhập và giá bán phải là số hợp lệ!";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: editProduct.php?id=$productId"); 
        exit();
    } else if ($productQuantity <= 0 || $productImportPrice <= 0 || $productRetailPrice <= 0) {
        $_SESSION['message'] = "❌ Lỗi: Số lượng, giá nhập và giá bán phải lớn hơn 0!";
        $_SESSION['alert_type'] = "alert-danger";
        header("Location: editProduct.php?id=$productId");
        exit();
    } else {
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $imageTmpName = $_FILES['product_image']['tmp_name'];
            $imageData = file_get_contents($imageTmpName);

            $updateResult = $productController->updateProduct($productId, $productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData);
        } else {
            $updateResult = $productController->updateProduct($productId, $productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory);
        }

        if ($updateResult === true) {
            $_SESSION['message'] = "✅ Đã cập nhật sản phẩm thành công!";
            $_SESSION['alert_type'] = "alert-success";
            header("Location: Product.php");
            exit();
        } else {
            $_SESSION['message'] = "❌ Lỗi: ".$updateResult."!";
            $_SESSION['alert_type'] = "alert-danger";
            header("Location: editProduct.php?id=$productId");   
            exit();
        }
    }
}
include_once(__DIR__ . '/../views/General.php');

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
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

        label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
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
    <h1>Chỉnh sửa sản phẩm</h1>
    <form method="POST" action="editProduct.php?id=<?php echo $productId; ?>" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mt-2">
                <label for="product_name">Tên sản phẩm:</label>
                <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <label for="product_barcode">Mã vạch:</label>
                <input type="text" id="product_barcode" name="product_barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode']); ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <label for="product_import_price">Giá nhập:</label>
                <input type="text" id="product_import_price" name="product_import_price" class="form-control" value="<?php echo htmlspecialchars($product['import_price']); ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <label for="product_retail_price">Giá bán:</label>
                <input type="text" id="product_retail_price" name="product_retail_price" class="form-control" value="<?php echo htmlspecialchars($product['retail_price']); ?>" required>
            </div>

            <div class="col-md-6 mt-2">
                <label for="product_quantity">Số lượng:</label>
                <input type="text" id="product_quantity" name="product_quantity" class="form-control" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <label for="product_category">Danh mục:</label>
                <select id="product_category" name="product_category" class="form-control" required>
                    <?php
                    $categories = $productController->getCategories();
                    foreach ($categories as $category) {
                        $selected = ($category['id'] == $product['category_id']) ? 'selected' : '';
                        echo "<option value='" . $category['id'] . "' $selected>" . htmlspecialchars($category['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mt-2">
                <label for="product_image">Chọn ảnh (nếu có):</label>
                <input type="file" id="product_image" name="product_image" class="form-control">
            </div>

            <div style="text-align: center;">
                <button type="submit" class="btn btn-gray btn-animated mt-2">Cập nhật</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>