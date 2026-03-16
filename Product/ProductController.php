<?php
require_once('ProductClass.php');

class ProductController {
    private $product;
    
    public function __construct() {
        $this->product = new Product();
    }

    public function addProduct($productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData = null) {
        return $this->product->addProduct($productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData);
    }
    
    public function deleteProduct($productId) {
        $product = $this->product->getProductById($productId);

        if ($product['flag'] == 1) {
            return "Không thể xóa sản phẩm này vì có sản phẩm đã được bán";
        }

        return $this->product->deleteProduct($productId);
    }
    
    public function getAllProducts() {
        return $this->product->getAllProducts();
    }
    
    public function getCategories() {
        return $this->product->getAllCategories();
    }
    
    public function getProductById($productId) {
        return $this->product->getProductById($productId);
    }

    public function updateProduct($productId, $productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData = null) {
        return $this->product->updateProduct($productId, $productName, $productBarcode, $productQuantity, $productImportPrice, $productRetailPrice, $productCategory, $imageData);
    }
}
?>
