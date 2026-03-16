<?php
session_start();
require_once('../models/DbConnection.php');

if (!isset($_SESSION["user"])) { 
    $_SESSION["message"] = "❌ Vui lòng đăng nhập!";
    $_SESSION['alert_type'] = "alert-danger";
    header("Location:/doancuoiky/views/login.php"); 
    exit();
}
if (isset($_SESSION['message']) && isset($_SESSION['alert_type'])) {
    echo '<div id="alertBox" class="alert '.$_SESSION['alert_type'].'" role="alert">' . $_SESSION["message"] . '</div>';
    unset($_SESSION['message']);
    unset($_SESSION['alert_type']);
} 
require_once('../models/DbConnection.php');
$connect = new ConnectDatabase();
$conn = $connect->getConnection();

if($_SESSION['role'] == 'admin') $sql = "SELECT avatar, full_name FROM admin where user_name =?";
else $sql = "SELECT avatar, full_name, has_changed_password FROM employee where user_name =?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION["username"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($user)) {
    $_SESSION["message"] = "❌ Tài khoản không tồn tại hoặc đã bị xóa!";
    $_SESSION['alert_type'] = "alert-danger";
    unset($_SESSION['user']);
    header("Location:/doancuoiky/views/login.php");
    exit();
}
?>
<script>
    setTimeout(function() {
        var alertBox = document.getElementById("alertBox");
        if (alertBox) {
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3000);
</script>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container{
            text-align: center;
            justify-content: center;
            margin-top: 100px;
            margin-bottom: 50px;
        }
        .header-bar {
            width: 100vw;
            padding: 12px 30px;
            background: #ffffff;
            color: #000000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: auto;
            backdrop-filter: blur(8px);
            border-radius: 0px 0px 15px 15px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .header-bar .avatar-options {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-bar .avatar-options img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            transition: transform 0.3s ease-in-out, border-color 0.3s;
        }

        .header-bar .avatar-options img:hover {
            transform: scale(1.15);
            border-color: #000000;
        }

        .header-bar .dropdown button {
            font-size: 14px;
            padding: 8px 15px;
            border-radius: 8px;
            background: #000000;
            color: #ffffff;
            border: 2px solid #000000;
            transition: all 0.3s ease;
        }

        .header-bar .dropdown button:hover {
            background: #ffffff;
            color: #000000;
            border-color: #000000;
            transform: scale(1.05);
        }

        .homeButton, .returnButton {
            position: fixed;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 100px;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            border-radius: 0 50px 50px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 24px;
            transition: all 0.4s ease-in-out;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
            z-index: 1000;
            overflow: hidden;
        }

        .returnButton {
            top: calc(50% + 120px);
        }

        .homeButton:hover, .returnButton:hover {
            width: 90px;
            background: #000000;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4);
        }

        .homeButton i, .returnButton i {
            transition: transform 0.3s ease-in-out;
        }

        .homeButton:hover i, .returnButton:hover i {
            transform: scale(1.3);
        }

        #alertBox {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 300px;
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.2);
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
            font-size: 17px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h1 class="logo">Point Of Sale</h1>
            <div class="avatar-options">
                <?php if ($user['avatar']) : ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($user['avatar']) ?>" 
                        alt="Avatar" 
                        class="rounded-circle" 
                        width="70" height="70">
                <?php else: ?>
                    <img src="/doancuoiky/views/default.jpg" alt="Default Avatar" class="rounded-circle" width="70" height="70">
                <?php endif; ?>
                <?php if (isset($user['has_changed_password']) && $user['has_changed_password'] && $_SESSION['role'] == 'employee' || $_SESSION['role'] == 'admin') : ?>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">Tài khoản</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/doancuoiky/views/Profile.php">Thông tin</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout()">Đăng xuất</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
            
    <a href="/doancuoiky/views/Main.php" class="homeButton">
        <i class="fas fa-home"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
           function showConfirmationDialog(action, confirmText, confirmColor, message, callback) {
                Swal.fire({
                    title: `Xác nhận ${action}`,
                    text: `Bạn có chắc muốn ${message}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: `Có, ${confirmText}`,
                    cancelButtonText: "Hủy"
                }).then((result) => {
                    if (result.isConfirmed && typeof callback === "function") {
                        callback();
                    }
                });
            }

            function confirmLogout() {
                showConfirmationDialog("đăng xuất", "đăng xuất", "#d33", "đăng xuất", function() {
                    window.location.href = "/doancuoiky/controllers/logout.php";
                });
            }

        </script>
</body>
</html>