<?php 
include(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giao dịch</title>
    <link rel="stylesheet" href="styleTransaction.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<style>
   /* Cải tiến nút bấm */
   .dongy {
        margin-top: 20px; /* Tạo khoảng cách giữa bảng và nút */
        padding: 10px 20px;
        background-color: #ff6600;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .dongy:hover {
        background-color: #e65c00;
    }

    /* Cải tiến bảng dữ liệu */
    table {
        width: 100%;    
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin-top:20px;
    }

    th {
        background: #444;
        color: white;
        padding: 12px;
        text-align: center;
        font-size: 1rem;
    }

    th, td {
        border-bottom: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    td img {
        width: 80px;
        border-radius: 5px;
    }

    tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.3s;
    }

    /* Tùy chỉnh input */
    input[type="text"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        transition: all 0.3s;
    }

    .main-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 40px;
        padding: 20px;
    }

    .left-panel {
        flex: 1;
        max-width: 40%;
    }

    .right-panel {
        flex: 2;
    }

    .btn-gray.btn-animated {
        background: #444444;
        color: #ffffff;
    }

    /* Click Effect */
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
<body>
    <a href="/doancuoiky/Transaction/Product_list.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
<h1 class="mt-2 text-center">THANH TOÁN HÓA ĐƠN</h1>

<div class="main-container">
    <div class="left-panel">
        <table class="thongtinnhanhang">
            <h5 class="text-center">Thông Tin Khách Hàng</h5>
            <tbody id="thongtinnhanhang">
                <form method="POST" action="createpdf.php" id="orderForm">
                <tr>
                    <td>Điện thoại</td>
                    <td><input type="text" id="dienthoai" name="dienthoai"></td>
                </tr>
                <tr>
                    <td width="20%">Họ tên</td>
                    <td><input type="text" id="hoten" name="hoten"></td>
                </tr>
                <tr>
                    <td>Địa chỉ</td>
                    <td><input type="text" id="diachi" name="diachi"></td>
                </tr>
                <tr>
                    <td>Số tiền khách đưa</td>
                    <td>
                        <input type="text" id="tienkhachdua_display" oninput="formatAndSync()" placeholder="0₫">
                        <input type="hidden" id="tienkhachdua" name="tienkhachdua">
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>

    <div class="right-panel">
        <script src="customer.js"></script>
        <input type="hidden" id="cartData" name="cartData">
            <div class="table-containner"> <div class="div">
                <table border="1" >
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="transactionCart"></tbody>
                </table> 
            </div>
            <div class="row mb" style="width: 100%; display: flex; justify-content: center; margin-top: 20px;">
                <div style="width: 100%; text-align: center;">
                    <button class="btn-gray btn-animated" type="submit" value="Thanh toán">Thanh toán</button>
                </div>
            </div>
            </form>
    </div>
</div>

<script>
    function formatAndSync() {
    let displayInput = document.getElementById("tienkhachdua_display");
    let hiddenInput = document.getElementById("tienkhachdua");

    let rawValue = displayInput.value.replace(/\D/g, '');

    hiddenInput.value = rawValue;

    displayInput.value = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
document.addEventListener("DOMContentLoaded", function () {
    let transactionCart = JSON.parse(sessionStorage.getItem("transactionCart")) || [];
    let cartContainer = document.getElementById("transactionCart");
    let totalAmount = 0;

    if (transactionCart.length === 0) {
        cartContainer.innerHTML = `<tr><td colspan="6">Không có đơn hàng nào!</td></tr>`;
    } else {
        cartContainer.innerHTML = "";
        transactionCart.forEach((item, index) => {
            totalAmount += item.price * item.quantity;
            let row = `
                <tr data-id="${item.product_id}">
                    <td>${index + 1}</td>
                    <td><img src="${item.image}" width="50" alt="${item.name}"></td>
                    <td>${item.name}</td>
                    <td>${item.price.toLocaleString()}₫</td>
                    <td>${item.quantity}</td>
                    <td>${(item.price * item.quantity).toLocaleString()}₫</td>
                </tr>
            `;
            cartContainer.innerHTML += row;
        });

        cartContainer.innerHTML += `
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Tổng tiền:</td>
                <td style="font-weight: bold;">${totalAmount.toLocaleString()}₫</td>
            </tr>
        `;
    }
});

document.getElementById("orderForm").addEventListener("submit", function(event) {
    let dienThoai = document.getElementById("dienthoai").value.trim();
    let hoTen = document.getElementById("hoten").value.trim();
    let diaChi = document.getElementById("diachi").value.trim();
    let tienKhachDua = parseInt(document.getElementById("tienkhachdua").value);
    
    let transactionCart = JSON.parse(sessionStorage.getItem("transactionCart")) || [];
    let totalAmount = transactionCart.reduce((total, item) => total + item.price * item.quantity, 0);

    if (transactionCart.length === 0) {
        Swal.fire({ icon: "warning", title: "🛒 Giỏ hàng trống!", text: "Vui lòng thêm sản phẩm vào giỏ trước khi thanh toán." });
        event.preventDefault(); 
        return;
    }

    if (dienThoai === "" || hoTen === "" || diaChi === "") {
        Swal.fire({ icon: "warning", title: "Thiếu thông tin!", text: "Vui lòng nhập đầy đủ thông tin trước khi thanh toán." });
        event.preventDefault(); 
        return;
    }

    if (isNaN(tienKhachDua) || tienKhachDua < totalAmount) {
        Swal.fire({ icon: "error", title: "Số tiền khách đưa không đủ!", text: `Tổng hóa đơn là ${totalAmount.toLocaleString()}₫. Vui lòng nhập số tiền lớn hơn hoặc bằng.` });
        event.preventDefault(); 
        return;
    }

    let cartItems = [];
    document.querySelectorAll("#transactionCart tr").forEach((row, index) => {
        let cells = row.getElementsByTagName("td");
        let productId = row.getAttribute("data-id");

        if (cells.length === 6 && productId) {
            cartItems.push({
                stt: index + 1,
                product_id: productId,
                image: cells[1].innerHTML, 
                name: cells[2].textContent.trim(),
                price: parseFloat(cells[3].textContent.replace(/[^\d]/g, "")),
                quantity: parseInt(cells[4].textContent.trim()),
                total: parseFloat(cells[5].textContent.replace(/[^\d]/g, ""))
            });
        }
    });

    document.getElementById("cartData").value = JSON.stringify(cartItems);
});
</script>

</body>

</html>



