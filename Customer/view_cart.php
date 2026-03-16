<?php
include_once(__DIR__ . '/../views/General.php');
require_once('../models/DbConnection.php');

$db = new ConnectDatabase();
$conn = $db->getConnection();

// Lấy order_id từ URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_details = [];

if ($order_id > 0) {
    $sql_details = "
        SELECT od.*, p.name AS product_name, p.image
        FROM order_detail od
        JOIN product p ON od.product_id = p.id
        WHERE od.order_id = :order_id";
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt_details->execute();

    while ($row = $stmt_details->fetch(PDO::FETCH_ASSOC)) {
        $order_details[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            padding: 15px;
            vertical-align: middle;
        }
        img.rounded.shadow-sm {
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <a href="/doancuoiky/Customer/Customer.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>

        <h4 class="mb-4">🧾 Chi tiết đơn hàng </h4>

        <table class="table table-bordered text-center bg-white">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($order_details)) {
                    echo "<tr><td colspan='5' class='text-muted'>⚠️ Không có sản phẩm trong đơn hàng này.</td></tr>";
                } else {
                    $stt = 1;
                    $total = 0;
                    foreach ($order_details as $item): 
                        $total += $item['total_price'];
                ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td>
                            <?php if (!empty($item['image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($item['image']) ?>" width="100" class="rounded shadow-sm">
                            <?php else: ?>
                                <img src="/doancuoiky/Transaction/product_default.jpg" width="100" class="rounded shadow-sm">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td class="text-danger fw-bold"><?= number_format($item['total_price'], 0) ?>₫</td>
                    </tr>
                <?php endforeach; ?>
                    <tr class="table-secondary fw-bold">
                        <td colspan="4" class="text-end">Tổng cộng:</td>
                        <td class="text-danger"><?= number_format($total, 0) ?>₫</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

