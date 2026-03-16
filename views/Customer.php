<?php 
include(__DIR__ . '/../views/General.php');
require_once __DIR__ . '/../models/DbConnection.php';

$db = new ConnectDatabase();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("SELECT phone, full_name, address FROM customer ORDER BY full_name ASC");
    $stmt->execute();
    
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Khách Hàng</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Danh Sách Khách Hàng</h2>
    
    <table>
        <tr>
            <th>Số Điện Thoại</th>
            <th>Họ Tên</th>
            <th>Địa Chỉ</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
            <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
            <td><?php echo htmlspecialchars($customer['address']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
