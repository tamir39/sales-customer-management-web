<?php
session_start();
require_once __DIR__ . '/../models/DbConnection.php';
require_once __DIR__ . '/../vendor/autoload.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ob_start();  

    try {
        $conn = (new ConnectDatabase())->getConnection();
        $conn->beginTransaction();

        if (!isset($_SESSION["id"])) {
            throw new Exception("Lỗi: Không tìm thấy ID nhân viên. Vui lòng đăng nhập!");
        }

        $customer_id = $_POST["dienthoai"] ?? "";
        $employee_id = $_SESSION["id"];
        $amount_paid = floatval(preg_replace('/\D/', '', $_POST['tienkhachdua']));
        $cartData = isset($_POST["cartData"]) ? json_decode($_POST["cartData"], true) : [];
    
        if (empty($cartData)) {
            throw new Exception("Giỏ hàng trống!");
        }

        $stmt = $conn->prepare("SELECT * FROM customer WHERE phone = ?");
        $stmt->execute([$customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            $stmt = $conn->prepare("INSERT INTO customer (phone, full_name, address) VALUES (?, ?, ?)");
            $stmt->execute([$customer_id, $_POST["hoten"], $_POST["diachi"]]);
        }

        $total_amount = 0;
        foreach ($cartData as $item) {
            $price = floatval(str_replace(',', '', $item['price']));
            $quantity = intval($item['quantity']);
            $total_amount += $price * $quantity;
        }

        $change_due = $amount_paid - $total_amount;

        foreach ($cartData as $item) {
            $stmt = $conn->prepare("
                UPDATE product 
                SET quantity = quantity - ? 
                WHERE name = ? AND quantity >= ?
            ");
    
            $stmt->bindParam(1, $item['quantity'], PDO::PARAM_INT);  
            $stmt->bindParam(2, $item['name'], PDO::PARAM_STR);      
            $stmt->bindParam(3, $item['quantity'], PDO::PARAM_INT);  
    
            $stmt->execute();}
        

        $stmt = $conn->prepare("INSERT INTO `order` (customer_phone, employee_id, total_amount, amount_paid, change_due) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_id, $employee_id, $total_amount, $amount_paid, $change_due]);
        $order_id = $conn->lastInsertId();

         $stmtDetail = $conn->prepare("INSERT INTO order_detail (order_id , product_id, quantity, price_product, total_price) VALUES (?, ?, ?, ?, ?)");
                        $sqlUpdateFlag = "UPDATE product SET flag = 1 WHERE id = :product_id";
        $stmtUpdate = $conn->prepare($sqlUpdateFlag);              
         
         
         foreach ($cartData as $item) {
                                        $quantity = intval($item['quantity']);
                                        $price = floatval(str_replace(',', '', $item['price']));
                                        $total_price = $price * $quantity;
                                    $product_id = intval($item['product_id']);  
                                        $stmtDetail->execute([$order_id, $product_id,$quantity, $price, $total_price]);
                                        $stmtUpdate->execute([':product_id' => $product_id]);
                                    }
       
        $conn->commit();

        $html = '<h1 style="text-align: center;">HÓA ĐƠN BÁN HÀNG</h1>';
        $html .= "<strong>Họ tên:</strong> " . htmlspecialchars($_POST["hoten"], ENT_QUOTES, 'UTF-8') . "<br>";
        $html .= "<strong>Địa chỉ:</strong> " . htmlspecialchars($_POST["diachi"], ENT_QUOTES, 'UTF-8') . "<br>";
        $html .= "<strong>Điện thoại:</strong> " . htmlspecialchars($customer_id, ENT_QUOTES, 'UTF-8') . "<br>";
        $html .= "<strong>Số tiền khách đưa:</strong> " . number_format((float)$amount_paid, 0, ',', '.') . " VND<br>";
        $html .= "<br><h3 style='text-align: center;'>Chi tiết đơn hàng</h3>";

        $html .= '<table border="1" width="100%" cellpadding="5" cellspacing="0">';
        $html .= '<tr style="background-color: #f2f2f2;"><th>STT</th><th>Tên sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th></tr>';
        $tong_tien = 0;
        foreach ($cartData as $index => $item) {
            $tong_tien += floatval(str_replace(',', '', $item['total']));
            $html .= "<tr>
                        <td>" . ($index + 1) . "</td>
                        <td>" . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . "</td>
                        <td>" . number_format($item['price'], 0, ',', '.') . "</td>
                        <td>" . $item['quantity'] . "</td>
                        <td>" . number_format($item['total'], 0, ',', '.') . "</td>
                      </tr>";
        }
        $html .= '</table>';
        $tien_thua = (float)$amount_paid - $tong_tien;
        $html .= "<h3 style='text-align: right;'>Tiền thừa: " . number_format($tien_thua, 0, ',', '.') . " VND</h3>";        
        $html .= "<h3 style='text-align: right;'>Tổng tiền: " . number_format($tong_tien, 0, ',', '.') . " VND</h3>";
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'dejavusans'  
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('hoa_don.pdf', 'D');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        die(json_encode(["status" => "error", "message" => $e->getMessage()]));
    }
}
?>


