<?php
include_once(__DIR__ . '/../views/General.php');
include_once('ProductClass.php');
include_once(__DIR__ . '/../Category/CategoryClass.php');

$db = new ConnectDatabase();
$productObj = new Product($db);

$productId = isset($_GET['id']) ? $_GET['id'] : null;
if ($productId) {
    $product = $productObj->getProductById($productId);
}

$category = new Category();
$stmt = $category->getCategoryById($product['category_id']);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .box {
            max-width: 800px;
            max-height: 600px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <a href="/doancuoiky/Product/Product.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container box" style="margin-top: 50px">
        <h1>Chi Tiết Sản Phẩm</h1>
        <?php if (isset($product) && $product): ?>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered text-center mt-4">
                        <thead class="table-dark">
                            <tr>
                                <th>Thuộc tính</th>
                                <th>Giá trị</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Mã Vạch</strong></td>
                                <td><?= htmlspecialchars($product['barcode']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tên Sản Phẩm</strong></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                            </tr>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <tr>
                                    <td><strong>Giá Nhập Khẩu</strong></td>
                                    <td><?= number_format($product['import_price'], 0, '', '.') . 'đ' ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Giá Bán Lẻ</strong></td>
                                <td><?= number_format($product['retail_price'], 0, '', '.') . 'đ' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Số lượng</strong></td>
                                <td><?= htmlspecialchars($product['quantity']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Danh Mục</strong></td>
                                
                                <td><?= htmlspecialchars($stmt['name']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày Tạo</strong></td>
                                <td><?= htmlspecialchars($product['created_at']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div style="padding: 0; text-align: center;">
                        <?php if (isset($product['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($product['image']); ?>" 
                            alt="Product Image" width="100%" height="100%" style="object-fit: contain;"/>
                        <?php else: ?>
                            <p>Chưa có hình ảnh cho sản phẩm này.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Không tìm thấy sản phẩm.</p>
        <?php endif; ?>
    </div>
        <div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productImageModalLabel">Hình ảnh sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <?php if (isset($product['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($product['image']); ?>" alt="Product Image" class="img-fluid" />
                        <?php else: ?>
                            <p>Chưa có hình ảnh cho sản phẩm này.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
