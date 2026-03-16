<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/DbConnection.php';  

$db = new ConnectDatabase();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("SELECT full_name, address FROM customer WHERE phone = ?");
    $stmt->bindParam(1, $phone, PDO::PARAM_STR);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        echo json_encode(["success" => true, "full_name" => $customer["full_name"], "address" => $customer["address"]]);
    } else {
        echo json_encode(["success" => false, "message" => "Không tìm thấy khách hàng"]);
    }
}
?>
