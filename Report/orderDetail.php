<?php
include_once(__DIR__ . '/../views/General.php');
require_once('../models/DbConnection.php');
$db = new ConnectDatabase();
$conn = $db->getConnection();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order_details = [];

if ($order_id > 0) {
    $sql_details = "
        SELECT od.*, p.name AS product_name
        FROM `order_detail` od
        JOIN `product` p ON od.product_id = p.id
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .box {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid #ddd;
        }

        .table {
            background: #1a1a1a;
            color: #ffffff;
            border: 1px solid #ffffff;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .table thead {
            background: #000000;
        }

        .table th, .table td {
            border: 1px solid #1a1a1a;
            padding: 12px;
        }

        .table-hover tbody tr:hover {
            background: #333333;
            transition: background 0.3s ease-in-out;
        }

    </style>
</head>
<body>
    <a href="/doancuoiky/Report/indexReport.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container mt-2">

        <div class="box">
            <h1 class="text-center">Chi Tiết Đơn Hàng</h1>

            <?php if (!empty($order_details)): ?>
                <table class="table">
                    <thead>
                        <tr class="table-dark text-center">
                            <th>Hóa đơn</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_details as $detail): ?>
                            <tr>
                                <td><?= $detail['id'] ?></td>
                                <td><?= $detail['product_name'] ?></td>
                                <td><?= $detail['quantity'] ?></td>
                                <td><?= number_format($detail['price_product'], 2) ?> VND</td>
                                <td><?= number_format($detail['total_price'], 2) ?> VND</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không tìm thấy sản phẩm nào trong đơn hàng này!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

