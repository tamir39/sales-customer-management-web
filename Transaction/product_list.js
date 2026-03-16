function themvaogiohang(button) {
    let productCard = button.closest(".product-card");
    let productName = productCard.querySelector("h3").innerText.trim();
    let productPriceText = productCard.querySelector("strong").innerText;
    let productImage = productCard.querySelector("img").src;
    let quantityInput = productCard.querySelector("input[name='soluong']");
    let productId = productCard.getAttribute("data-id");
   
    let quantity = parseInt(quantityInput.value);
    
    let productPrice = parseFloat(productPriceText.replace(/[^\d]/g, ""));

    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

    let existingItem = cart.find(item => item.name === productName);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            product_id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: quantity
        });
    }

    sessionStorage.setItem("cart", JSON.stringify(cart));

    updateCartCount();
    Swal.fire({
        toast: true,
        position: "bottom-start",
        icon: "success",
        title: "Đã thêm vào giỏ hàng!",
        showConfirmButton: false,
        timer: 1000,
       
    });
}

function updateQuantityInput(index, newQuantity) {
    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

    newQuantity = parseInt(newQuantity); 

    if (newQuantity <= 0) {
        cart.splice(index, 1); 
    } else {
        cart[index].quantity = newQuantity; 
    }

    sessionStorage.setItem("cart", JSON.stringify(cart)); 
    showcart();
}
function removeItem(index) {
    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

    if (index >= 0 && index < cart.length) {
        cart.splice(index, 1);
        sessionStorage.setItem("cart", JSON.stringify(cart));

        showcart();
        updateCartCount();
    }
}

function updateCartCount() {
    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
    document.getElementById("cart-count").innerText = cart.length;
}

document.addEventListener("DOMContentLoaded", updateCartCount);
function showcart() {
    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
    let tbody = document.getElementById("mycart");
    tbody.innerHTML = "";

    let totalOrder = 0;

    cart.forEach((item, index) => {
        let totalPrice = item.price * item.quantity;
        totalOrder += totalPrice;

        let row = `<tr>
            <td>${index + 1}</td>
            <td><img src="${item.image}" width="50"></td>
            <td>${item.name}</td>
            <td>${parseInt(item.price).toLocaleString()}₫</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" max="100"
                    onchange="updateQuantityInput(${index}, this.value)">
            </td>
            <td>${totalPrice.toLocaleString()}₫</td>
            <td>
                <button onclick="removeItem(${index})" class="delete-btn">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });

    tbody.innerHTML += `<tr>
        <th colspan="5">Tổng đơn hàng</th>
        <th>${totalOrder.toLocaleString()}₫</th>
        <th></th>
    </tr>`;

    updateCartCount();
}
function checkStockBeforeCheckout() {
    let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
    if (cart.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "🛒 Giỏ hàng trống!",
            text: "Vui lòng thêm sản phẩm vào giỏ trước khi thanh toán.",
            confirmButtonText: "OK"
        });
        return;
    }
    fetch("check_stock.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cart: cart })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: "success",
                title: "✅ Đặt hàng thành công!",
                
                confirmButtonText: "OK"
            }).then(() => {
                sessionStorage.setItem("transactionCart", JSON.stringify(cart));
                sessionStorage.removeItem("cart");
                updateCartCount();
                window.location.href = "transaction.php";
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "❌ Sản phẩm không đủ hàng!",
                text: "Một số sản phẩm trong giỏ hàng đã hết."
            });
        }
    })
    .catch(error => console.error("Lỗi kiểm tra kho:", error));
}


