<?php 
include(__DIR__ . '/../views/General.php');
require_once __DIR__ . '/../models/DbConnection.php';
require_once __DIR__ .'/../Product/ProductClass.php';

$product = new Product();
$products = $product->getAllProducts();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styleTransaction.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
        .search-container{
            position: relative;
            width: 20%;
            justify-self: center;
        }
        #searchInput{
            border: 1px solid #ccc; 
            border-radius: 20px;
        }
        .search-input {
            border: none;
            outline: none;
            flex: 1;
            padding-right: 30px;
            cursor: text;
        }
        .search-icon {
            position: absolute;
            top: 40px;
            right: 10px;
            transform: translateY(-50%);
            color: gray;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="giohang" onclick="showcart()">
            <i class="fas fa-shopping-cart"></i>  (<span id="cart-count">0</span>)
        </div>
    </div>
    <div id="overlay"></div>
        <div id="showcart" >
            <button id="close-cart">X</button>
            <h2>Giỏ hàng</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa sản phẩm</th>
                    </tr>
                </thead>
                <tbody id="mycart"></tbody>
            </table>
            <div class="cart-footer">
            <button class="checkout-btn" id="checkout-btn" onclick="checkStockBeforeCheckout() ">Xác nhận</button>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let cartModal = document.getElementById("showcart");
            let overlay = document.getElementById("overlay");
            let closeCart = document.getElementById("close-cart");
            let cartIcon = document.querySelector(".giohang");
            
            cartIcon.addEventListener("click", function () {
                cartModal.style.display = "block";
                overlay.style.display = "block";
            });

            closeCart.addEventListener("click", function () {
                cartModal.style.display = "none";
                overlay.style.display = "none";
            });

            overlay.addEventListener("click", function () {
                cartModal.style.display = "none";
                overlay.style.display = "none";
            });
        });
    </script>

    <div class="search-container mb-4">
        <input type="text" class="search-input" id="searchInput" placeholder="Tìm kiếm...">
        <i class="fas fa-search search-icon"></i>   
    </div>
   
    <div class="product-container">
        <?php foreach ($products as $product): ?>
            <div class="product-card" data-id="<?= $product['id'] ?>">
                <?php if ($product['image']) : ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($product['image']) ?>" 
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        width="150px" >
                <?php else: ?>
                    <img src="product_default.jpg" alt="Default Product"  width="150px" >
                <?php endif; ?>
                <p>
                    <strong><?= number_format($product['retail_price'], 0) ?>₫</strong>
                </p>
                <h3 style="height: 50px; display: flex; align-items: center; justify-content: center; text-align: center;">
                    <?= htmlspecialchars($product['name']) ?></h3>
                <input type="number" name="soluong" min="1" max="1000" value="1">
                <button class="btn btn-gray btn-animated" onclick="themvaogiohang(this)">Đặt hàng</button>
                <p style="display:none;"><strong>Barcode:</strong> <span class="barcode"><?= htmlspecialchars($product['barcode']) ?></span></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function timKiemSanPham() {
            let searchValue = document.querySelector(".search-input").value.toLowerCase();
            let products = document.querySelectorAll(".product-card");
            
            products.forEach(product => {
                let productName = product.querySelector("h3").textContent.toLowerCase();
                  let productBarcode = product.querySelector(".barcode").textContent.toLowerCase();
                if (productName.includes(searchValue)|| productBarcode.includes(searchValue)) {
                    product.style.display = "block";
                } else {
                    product.style.display = "none";
                }
            });
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            let searchInput = document.querySelector(".search-input");
            let searchIcon = document.querySelector(".search-icon");
            
            searchIcon.addEventListener("click", timKiemSanPham);
            
            searchInput.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    timKiemSanPham();
                }
            });
        });
    </script>

<script src="product_list.js"></script>
</body>
</html>