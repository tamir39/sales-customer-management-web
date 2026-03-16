document.getElementById("dienthoai").addEventListener("input", function () {
    let phoneNumber = this.value.trim();

    if (phoneNumber.length >= 10) {  
        fetch("get_customer.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "phone=" + encodeURIComponent(phoneNumber)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("hoten").value = data.full_name;
                document.getElementById("diachi").value = data.address;
            } else {
                document.getElementById("hoten").value = "";
                document.getElementById("diachi").value = "";
            }
        })
        .catch(error => console.error("Lỗi:", error));
    }
});
