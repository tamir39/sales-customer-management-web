<?php
require_once('../models/DbConnection.php');

class Category {
    private $conn;

    public function __construct()
    {
        $db = new ConnectDatabase();
        $this->conn = $db->getConnection();
    }
    public function getAllCategories()
    {
        $sql = "SELECT * FROM category";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name, $description, $createdBy)
{
    $checkSql = "SELECT COUNT(*) FROM category WHERE name = :name";
    $checkStmt = $this->conn->prepare($checkSql);
    $checkStmt->bindParam(':name', $name, PDO::PARAM_STR);
    $checkStmt->execute();
    
    if ($checkStmt->fetchColumn() > 0) {
        return "Tên danh mục đã tồn tại!";
    }

    $sql = "INSERT INTO category (name, description, created_by) VALUES (:name, :description, :createdBy)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_INT);

    return $stmt->execute();
}

    public function deleteCategory($id)
{
    try {
        $this->conn->beginTransaction();
        $sql_delete_products = "DELETE FROM product WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql_delete_products);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $sql_delete_category = "DELETE FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($sql_delete_category);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $this->conn->commit();
        return true;
    } catch (PDOException $e) {
        $this->conn->rollBack();
        return false;
    }
}
public function updateCategory($id, $name, $description, $createdBy)
{
    $checkSql = "SELECT COUNT(*) FROM category WHERE name = :name AND id != :id";
    $checkStmt = $this->conn->prepare($checkSql);
    $checkStmt->bindParam(':name', $name, PDO::PARAM_STR);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    
    if ($checkStmt->fetchColumn() > 0) {
        return "Tên danh mục đã tồn tại!";
    }

    $sql = "UPDATE category SET name = :name, description = :description, created_by = :createdBy WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_INT);
    
    return $stmt->execute();
}
}
?>
