<?php 
include(__DIR__ . '/../views/General.php');
require_once __DIR__ . '/../models/DbConnection.php';

$db = new ConnectDatabase();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT o.id AS order_id, c.phone, c.full_name, c.address, o.created_at AS order_time
    FROM customer c
    LEFT JOIN `order` o ON c.phone = o.customer_phone
    ORDER BY o.created_at DESC
");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Khách Hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        .table th, .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .card {
            border-radius: 15px;
            border: 1px solid #ddd;
            background-color: #ffffff;
        }

        .btn-gray.btn-animated {
            background: #444444;
            color: #ffffff;
        }

        /* Click Effect */
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
    <div class="container mt-4">
        <div class="card shadow-sm p-4">
            <h2 class="mb-4">📋 Danh Sách Khách Hàng</h2>
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center align-middle">
                    <thead class="table-dark text-uppercase">
                        <tr>
                            <th>Ngày đặt/thời gian</th>
                            <th>Số Điện Thoại</th>
                            <th>Họ Tên</th>
                            <th>Địa Chỉ</th>
                            <th>Giỏ Hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= htmlspecialchars($customer['order_time']); ?></td>
                            <td><?= htmlspecialchars($customer['phone']); ?></td>
                            <td><?= htmlspecialchars($customer['full_name']); ?></td>
                            <td><?= htmlspecialchars($customer['address']); ?></td>
                            <td>
                                <a href="view_cart.php?order_id=<?= urlencode($customer['order_id']); ?>" 
                                   class="btn btn-gray btn-animated">
                                    🛒 Xem giỏ hàng
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
