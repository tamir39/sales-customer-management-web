<?php
require_once('../models/DbConnection.php');
session_start();
$db = new ConnectDatabase();
$conn = $db->getConnection();

$start_date = $end_date = "";
$total_revenue = 0;
$order_count = 0;
$product_count = 0;
$profit = 0;
$orders = [];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['today'])) {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+1 day'));
    } 
    elseif (isset($_POST['yesterday'])){
        $start_date = date('Y-m-d', strtotime('-1 day'));
        $end_date = date('Y-m-d');
    } 
    elseif (isset($_POST['last_7_days'])) {
        $start_date = date('Y-m-d', strtotime('-6 days')); 
        $end_date = date('Y-m-d', strtotime('+1 day'));
    }
    elseif (isset($_POST['last_30_days'])) {
        $start_date = date('Y-m-d', strtotime('-29 days'));  
        $end_date = date('Y-m-d', strtotime('+1 day'));
    }
    elseif (isset($_POST['custom_range'])) {
        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $start_date = $_POST['start_date'];
            $end_date = date('Y-m-d', strtotime($_POST['end_date']));
        }
    }

    if (!empty($start_date) && !empty($end_date))
    {
        $sql = "
            SELECT 
                COUNT(id) AS total_orders,
                SUM(total_amount) AS total_revenue
            FROM `order`
            WHERE created_at BETWEEN :start_date AND :end_date
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $order_count = $row['total_orders'];
            $total_revenue = $row['total_revenue'];
        }

        $sql_product = "
            SELECT SUM(od.quantity) AS total_products
            FROM `order_detail` od
            JOIN `order` o ON od.order_id = o.id
            WHERE o.created_at BETWEEN :start_date AND :end_date
        ";
        $stmt_product = $conn->prepare($sql_product);
        $stmt_product->bindParam(':start_date', $start_date);
        $stmt_product->bindParam(':end_date', $end_date);
        $stmt_product->execute();
        if ($stmt_product->rowCount() > 0) {
            $row = $stmt_product->fetch(PDO::FETCH_ASSOC);
            $product_count = $row['total_products'];
        }

        $sql_profit = "
            SELECT SUM(od.quantity * p.import_price) AS total_cost
            FROM `order_detail` od
            JOIN `order` o ON od.order_id = o.id
            JOIN `product` p ON od.product_id = p.id
            WHERE o.created_at BETWEEN :start_date AND :end_date
        ";
        $stmt_profit = $conn->prepare($sql_profit);
        $stmt_profit->bindParam(':start_date', $start_date);
        $stmt_profit->bindParam(':end_date', $end_date);
        $stmt_profit->execute();
        if ($stmt_profit->rowCount() > 0) {
            $row = $stmt_profit->fetch(PDO::FETCH_ASSOC);
            $total_cost = $row['total_cost'];
            $profit = $total_revenue - $total_cost;
        }

        $sql_orders = "
            SELECT o.*, e.full_name AS employee_name 
            FROM `order` o
            LEFT JOIN `employee` e ON o.employee_id = e.id
            WHERE o.created_at BETWEEN :start_date AND :end_date
            ORDER BY o.created_at DESC
        ";
        $stmt_orders = $conn->prepare($sql_orders);
        $stmt_orders->bindParam(':start_date', $start_date);
        $stmt_orders->bindParam(':end_date', $end_date);
        $stmt_orders->execute();
        while ($row = $stmt_orders->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = $row;
        }

        $_SESSION['stats'] = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'order_count' => $order_count,
            'total_revenue' => $total_revenue,
            'product_count' => $product_count,
            'profit' => $profit,
            'orders' => $orders
        ];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (isset($_SESSION['stats'])) {
    $start_date = $_SESSION['stats']['start_date'];
    $end_date = $_SESSION['stats']['end_date'];
    $order_count = $_SESSION['stats']['order_count'];
    $total_revenue = $_SESSION['stats']['total_revenue'];
    $product_count = $_SESSION['stats']['product_count'];
    $profit = $_SESSION['stats']['profit'];
    $orders = $_SESSION['stats']['orders'];
    unset($_SESSION['stats']);  
}

include_once(__DIR__ . '/../views/General.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .container {
            margin-top: 50px;  
        }
        .box {
            width: 1000px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            flex: 1;
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
<div class="container">
    <div class="row">
        <div class="col-6 box" style="margin-right: 20px;">
            <h1>Thống Kê Đơn Hàng</h1>
            <form method="POST" action="">
                <div class="mb-3">
                    <button type="submit" name="today" class="btn btn-animated">Hôm Nay</button>
                    <button type="submit" name="yesterday" class="btn btn-animated">Hôm Qua</button>
                    <button type="submit" name="last_7_days" class="btn btn-animated">7 Ngày Gần Đây</button>
                    <button type="submit" name="last_30_days" class="btn btn-animated">1 Tháng Gần Đây</button>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Từ ngày:</label>
                    <input type="date" name="start_date" value="<?= $start_date ?? '' ?>">

                    <label for="end_date" class="form-label">Đến ngày:</label>
                    <input type="date" name="end_date" value="<?= $end_date ?? '' ?>">

                    <button type="submit" name="custom_range" class="btn btn-animated">Xem Thống Kê</button>
                </div>
            </form>
        
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">Số lượng đơn hàng</th>
                            <td><?= $order_count ?? 0 ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Tổng số tiền đã nhận</th>
                            <td><?= number_format($total_revenue ?? 0, 0, ',', '.') ?> VND</td>
                        </tr>
                        <tr>
                            <th scope="row">Số lượng sản phẩm đã bán</th>
                            <td><?= $product_count ?? 0 ?></td>
                        </tr>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <tr>
                            <th scope="row">Lợi nhuận</th>
                            <td><?= number_format($profit ?? 0, 0, ',', '.') ?> VND</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
        </div>

        <div class="col-6 box">
            <h2>Danh sách đơn hàng:</h2>
            <?php if (!empty($orders) && count($orders) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Khách</th>
                            <th>Nhân viên</th>
                            <th>Tổng tiền</th>
                            <th>Ngày tạo</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['customer_phone'] ?></td>
                                <td><?= $order['employee_name'] ?></td>
                                <td><?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?> VND</td>
                                <td><?= $order['created_at'] ?></td>
                                <td><a href="orderDetail.php?id=<?= $order['id'] ?>" class="btn btn-gray btn-animated">Xem</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center mt-4">Không có đơn hàng trong khoảng thời gian này.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
