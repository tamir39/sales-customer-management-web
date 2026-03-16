<?php
session_start();
if (!isset($_SESSION["user"])) { 
    $_SESSION["message"] = "❌ Vui lòng đăng nhập!";
    $_SESSION['alert_type'] = "alert-danger";
    header("Location:/doancuoiky/views/login.php"); 
    exit();
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point Of Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/doancuoiky/views/style.css">
    <style>
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

        .container {
                margin-top: 120px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

        .catalog {
            width: 280px;
            height: 200px;
            font-size: 22px;
            font-weight: 600;
            text-align: center;
            color: #333333;
            background: linear-gradient(135deg, #ffffff, #f5f5f5);
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid #dddddd;
        }

        .catalog:hover {
            transform: translateY(-4px);
            box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.2);
            border-color: #cccccc;
        }

        .catalog p {
            font-size: 20px;
            font-weight: 500;
            opacity: 1;
            transition: all 0.3s ease-in-out;
        }

        .catalog-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 60px;
        }

        .catalog p {
            font-size: 16px;
            margin-top: 5px;
            font-weight: 400;
            color: #555555;
        }

        .catalog i {
            font-size: 36px;
            margin-bottom: 8px;
            color: #666666;
            transition: all 0.3s ease-in-out;
        }

        .catalog:hover i {
            transform: scale(1.1);
            color: #222222;
        }

        .btn-animated {
            background: #222222;
            color: #ffffff;
            border: 2px rounded #ffffff;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            padding: 8px 15px;
            text-transform: uppercase;
        }

        .btn-animated:hover {
            background: #ffffff;
            color: #000000;
            border: 2px rounded #000000;
            transform: scale(1.1);
        }

        .btn-gray {
            background: #2e2e2e;
            color: #f5f5f5;
            border: 2px rounded #bfbfbf;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(191, 191, 191, 0.3);
        }

        .btn-gray:hover{
            background: #f5f5f5;
            color: #000000;
            border: 2px rounded #000000;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            transform: scale(1.1);
        }

        button, a {
            transition: all 0.3s ease-in-out;
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
                        <img src="default.jpg" alt="Default Avatar" class="rounded-circle" width="70" height="70">
                    <?php endif; ?>
                    <?php if (isset($user['has_changed_password']) && $user['has_changed_password'] && $_SESSION['role'] == 'employee' || $_SESSION['role'] == 'admin') : ?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">Tài khoản</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="Profile.php">Thông tin</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout()">Đăng xuất</a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php 
        if (isset($_SESSION['message']) && isset($_SESSION['alert_type'])) {
            echo '<div id="alertBox" class="alert alert-danger" role="alert">' . $_SESSION["message"] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['alert_type']);
        } 
        ?>

        <script>
            setTimeout(function() {
                var alertBox = document.getElementById("alertBox");
                if (alertBox) {
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 5000);
        </script>

        <?php if ($_SESSION['role'] === 'employee' && !$_SESSION['has_changed_password']) : ?>
            <div id="announcementBox" class="alert alert-warning alert-dismissible fade show" role="alert" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050; min-width: 250px; padding: 20px;">
                <strong style="padding-bottom: 10px; display: block;">Hãy đổi mật khẩu để có thể sử dụng chức năng!</strong>
                <div class="d-flex align-items-center" style="gap: 15px;"> 
                    <button type="button" class="btn btn-primary" id="changePasswordBtn" style="padding: 10px 20px;">Nhấn vào đây để đổi mật khẩu</button>
                    <span class="mx-3">Hoặc</span>  
                    <a class="text-white bg-danger btn btn-danger ms-3" href="#" onclick="confirmLogout()" style="padding: 10px 20px;" >Đăng xuất</a> 
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                    var announcementBox = document.getElementById("announcementBox");
                    var changePasswordBtn = document.getElementById("changePasswordBtn");

                    changePasswordBtn.addEventListener("click", function () {
                        changePasswordModal.show();
                        announcementBox.style.display = 'none';
                    });

                    document.getElementById('changePasswordModal').addEventListener('hidden.bs.modal', function () {
                        announcementBox.style.display = 'block';
                    });
                });
            </script>
        <?php else: ?>
            <div class="row mt-5 text-center catalog-container d-flex justify-content-center">
                <?php if(($_SESSION['role'] === 'admin')) :?>
                <div class="col-md-3">
                    <a href="/doancuoiky/views/Employee.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Nhân Viên</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endif;?>
                <div class="col-md-3">
                    <a href="/doancuoiky/Category/Category.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Danh mục</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/doancuoiky/Product/Product.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Sản phẩm</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/doancuoiky/Customer/Customer.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Khách hàng</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <?php if ($_SESSION['role'] !== 'admin') : ?>
                <div class="col-md-3">
                    <a href="/doancuoiky/Transaction/product_list.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Giao dịch</h4>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endif;?>

                <div class="col-md-3">
                    <a href="/doancuoiky/Report/indexReport.php" class="text-decoration-none">
                        <div class="card shadow-lg p-4 catalog d-flex justify-content-center align-items-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h4 class="card-title text-dark fw-bold" style="font-size: 28px;">Thống Kê</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary" id="changePasswordModalLabel">Đổi Mật Khẩu</h5>
                    </div>
                    <div class="modal-body">
                        <form action="/doancuoiky/controllers/ChangePassword.php" method="POST" id="changePasswordForm">
                            <div class="mb-3">
                                <label for="old_password" class="form-label fw-bold text-dark">Mật Khẩu Cũ</label>
                                <input type="password" class="form-control border border-dark" id="old_password" name="old_password" required>
                                <small class="text-danger d-none" id="oldPasswordError">Mật khẩu cũ không được trống.</small>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-bold text-dark">Mật khẩu mới</label>
                                <input type="password" class="form-control border border-dark" id="new_password" name="new_password" required>
                                <small class="text-danger d-none" id="newPasswordError">Mật khẩu phải ít nhất 6 kí tự.</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label fw-bold text-dark">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control border border-dark" id="confirm_password" name="confirm_password" required>
                                <small class="text-danger d-none" id="confirmPasswordError">Mật khẩu không trùng khớp.</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-gray btn-animated" data-bs-dismiss="modal">Hủy</button>
                                <button type="reset" class="btn-gray btn-animated">Hoàn tác</button>
                                <button type="submit" class="btn btn-gray btn-animated fw-bold" id="submitBtn" disabled>Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const oldPassword = document.getElementById("old_password");
                const newPassword = document.getElementById("new_password");
                const confirmPassword = document.getElementById("confirm_password");
                const submitBtn = document.getElementById("submitBtn");

                const oldPasswordError = document.getElementById("oldPasswordError");
                const newPasswordError = document.getElementById("newPasswordError");
                const confirmPasswordError = document.getElementById("confirmPasswordError");

                function validateForm() {
                    let valid = true;

                    if (oldPassword.value.trim() === "") {
                        oldPasswordError.classList.remove("d-none");
                        valid = false;
                    } else {
                        oldPasswordError.classList.add("d-none");
                    }

                    if (newPassword.value.length < 6) {
                        newPasswordError.classList.remove("d-none");
                        valid = false;
                    } else {
                        newPasswordError.classList.add("d-none");
                    }

                    if (confirmPassword.value !== newPassword.value || confirmPassword.value === "") {
                        confirmPasswordError.classList.remove("d-none");
                        valid = false;
                    } else {
                        confirmPasswordError.classList.add("d-none");
                    }

                    submitBtn.disabled = !valid;
                }

                oldPassword.addEventListener("input", validateForm);
                newPassword.addEventListener("input", validateForm);
                confirmPassword.addEventListener("input", validateForm);

                if (document.getElementById("errorTrigger")) {
                    let changePasswordModal = new bootstrap.Modal(document.getElementById("changePasswordModal"));
                    changePasswordModal.show();
                }
            });
        </script>

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
