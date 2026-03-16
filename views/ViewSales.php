<?php
session_start();
require_once('../models/DbConnection.php');

$connect = new ConnectDatabase();
$conn = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['name'])) {
    $_SESSION['employee_id_sales'] = $_POST['id'];
    $_SESSION['employee_name_sales'] = $_POST['name'];
    header("Location: ViewSales.php");
    exit();
}

if (isset($_SESSION['employee_id_sales']) && isset($_SESSION['employee_name_sales'])) {
    $sql = "SELECT id, customer_phone, total_amount, amount_paid, change_due, created_at
            FROM `order` WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['employee_id_sales']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $orders = [];
}

include_once(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Tin Bán Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .container{
            text-align: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <a href="/doancuoiky/views/Employee.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container mt-5">
        <h1 class="mb-4">Danh sách đơn hàng của nhân viên <?= htmlspecialchars($_SESSION['employee_name_sales'] ?? 'Không xác định') ?></h1>
        
        <?php if (!empty($orders)): ?>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID đơn hàng</th>
                        <th>SĐT khách</th>
                        <th>Tổng tiền</th>
                        <th>Khách đưa</th>
                        <th>Tiền thối</th>
                        <th>Thời gian tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td ><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                            <td><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</td>
                            <td><?= number_format($order['amount_paid'], 0, ',', '.') ?>₫</td>
                            <td><?= number_format($order['change_due'], 0, ',', '.') ?>₫</td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <h3 class="mt-4">Đơn Hàng Trống</h3>
        <?php endif; ?>
    </div>
</body>
</html>
