<?php
require_once('../models/DbConnection.php');

class Product {
    private $conn;

    public function __construct() {
        $db = new ConnectDatabase();
        $this->conn = $db->getConnection();
    }

    public function getAllProducts() {
        $sql = "SELECT * FROM product";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData = null) {
        $checkSql = "SELECT COUNT(*) FROM product WHERE name = ? OR barcode = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$productName, $productBarcode]);
        $count = $checkStmt->fetchColumn();
        
        if ($count > 0) {
            return "Sản phẩm đã tồn tại với cùng tên hoặc mã vạch!";
        }
    
        $sql = "INSERT INTO product (barcode, name, quantity, import_price, retail_price, category_id, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $productBarcode);
        $stmt->bindParam(2, $productName);
        $stmt->bindParam(3, $productQuantity);
        $stmt->bindParam(4, $productImportPrice);
        $stmt->bindParam(5, $productRetailPrice);
        $stmt->bindParam(6, $productCategory);
        $stmt->bindParam(7, $imageData, PDO::PARAM_LOB);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteProduct($productId) {
        $sql_check_order = "SELECT * FROM order_detail WHERE product_id = ?";
        $stmt_check_order = $this->conn->prepare($sql_check_order);
        $stmt_check_order->bindParam(1, $productId);
        $stmt_check_order->execute();

        $sql = "DELETE FROM product WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $productId);

        if ($stmt->execute()) {
            return "Đã xóa sản phẩm thành công!";
        } else {
            return "Có lỗi xảy ra khi xóa sản phẩm";
        }
    }

    public function getAllCategories() {
        $sql = "SELECT id, name FROM category";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProductById($id) {
        $sql = "SELECT * FROM product WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProduct($productId, $productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData = null) {
        $checkSql = "SELECT COUNT(*) FROM product WHERE barcode = :barcode AND id != :id";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bindParam(':barcode', $productBarcode, PDO::PARAM_STR);
        $checkStmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            return "Mã vạch đã tồn tại!";
        }
    
        if ($imageData !== null) {
            $sql = "UPDATE product SET barcode = :barcode, name = :name, quantity = :quantity, import_price = :import_price, retail_price = :retail_price, category_id = :category_id, image = :image WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
        } else {
            $sql = "UPDATE product SET barcode = :barcode, name = :name, quantity = :quantity, import_price = :import_price, retail_price = :retail_price, category_id = :category_id, image = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->bindParam(':barcode', $productBarcode);
        $stmt->bindParam(':name', $productName);
        $stmt->bindParam(':quantity', $productQuantity);
        $stmt->bindParam(':import_price', $productImportPrice);
        $stmt->bindParam(':retail_price', $productRetailPrice);
        $stmt->bindParam(':category_id', $productCategory);
        $stmt->bindParam(':id', $productId);
    
        return $stmt->execute();
    }      
}
?>
