<?php
require_once __DIR__ . '/../models/DbConnection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['cart'])) {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
    exit;
}

$db = new ConnectDatabase();
$conn = $db->getConnection();

$cart = $data['cart'];
$errors = [];

foreach ($cart as $item) {
    $stmt = $conn->prepare("SELECT quantity FROM product WHERE name = ?");
    $stmt->execute([$item['name']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || $product['quantity'] < $item['quantity']) {
        $errors[] = "Sản phẩm '{$item['name']}' không đủ hàng trong kho!";
    }
}

if (!empty($errors)) {
    echo json_encode(["success" => false, "message" => $errors]);
} else {
    echo json_encode(["success" => true]);
}
?>
