<?php
include_once(__DIR__ . '/../views/General.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .box {
            max-width: 650px;          
            width: 100%;               
            background-color: #ffffff;
            padding: 40px;             
            border-radius: 12px;       
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            margin: 50px auto;            
        }

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

        .avatar img {
            border-radius: 50%;
            border: 3px solid #ffffff;
            transition: transform 0.3s ease-in-out, border-color 0.3s;
        }

        .avatar img:hover {
            transform: scale(1.15);
            border-color: #000000;
        }
    </style>
</head>
<body>
    <?php
        $connect = new ConnectDatabase();
        $conn = $connect->getConnection();
        
        if($_SESSION['role'] == 'admin') $sql = "SELECT avatar, full_name, email, phone FROM admin where user_name =?";
        else $sql = "SELECT avatar, full_name, email, phone FROM employee where user_name =?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION["username"]]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="container mt-5">
        <div class="row align-items-center box">
            <div class="col-md-4 text-center mb-4 mb-md-0 avatar">
                <?php if ($info && $info['avatar']) : ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($info['avatar']) ?>" 
                        alt="Avatar" 
                        class="rounded-circle profile-img" 
                        width="150" height="150">
                <?php else: ?>
                    <img src="/doancuoiky/views/default.jpg" 
                        alt="Default Avatar" 
                        class="rounded-circle profile-img" 
                        width="150" height="150">
                <?php endif; ?>
            </div>

            <div class="col-md-8">
                <div class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($info['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mb-2"><strong>Tên đầy đủ:</strong> <?= htmlspecialchars($info['full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mb-3"><strong>Số điện thoại:</strong> <?= htmlspecialchars($info['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>

                <div class="d-flex gap-2">
                    <button class="btn btn-gray btn-animated" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                        Chỉnh sửa thông tin
                    </button>
                    <button class="btn btn-gray btn-animated" id="changePasswordBtn">
                        Đổi mật khẩu
                    </button>
                </div>
            </div>
        </div>
    </div>

    </div>
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary" id="changePasswordModalLabel">Đổi Mật Khẩu</h5>
                    </div>
                    <div class="modal-body">
                        <form action="/doancuoiky/controllers/UpdatePassword.php" method="POST" id="changePasswordForm">
                            <div class="mb-3">
                                <label for="old_password" class="form-label fw-bold text-dark">Mật khẩu cũ</label>
                                <input type="password" class="form-control border border-dark" id="old_password" name="old_password" required>
                                <small class="text-danger d-none" id="oldPasswordError">Mật khẩu cũ không được trống.</small>
                            </div>
                            <div class="mb-3">
                                <label for="update_password" class="form-label fw-bold text-dark">Mật khẩu mới</label>
                                <input type="password" class="form-control border border-dark" id="update_password" name="update_password" required>
                                <small class="text-danger d-none" id="newPasswordError">Mật khẩu mới phải có ít nhất 6 kí tự.</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label fw-bold text-dark">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control border border-dark" id="confirm_password" name="confirm_password" required>
                                <small class="text-danger d-none" id="confirmPasswordError">Mật khẩu không trùng khớp.</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-gray btn-animated" data-bs-dismiss="modal">Hủy</button>
                                <button type="reset" class="btn btn-gray btn-animated">Reset</button>
                                <button type="submit" class="btn btn-gray btn-animated fw-bold" id="submitBtn" disabled>Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const oldPassword = document.getElementById("old_password");
                const newPassword = document.getElementById("update_password");
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

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                var changePasswordBtn = document.getElementById("changePasswordBtn");

                changePasswordBtn.addEventListener("click", function () {
                    changePasswordModal.show(); 
                });
            });
        </script>
    </div>

        <?php 
            if (isset($_SESSION['message']) && isset($_SESSION['alert_type'])) {
                echo '<div id="alertBox" class="alert '.$_SESSION['alert_type'].'" role="alert">' . $_SESSION["message"] . '</div>';
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
            }, 3000);
        </script>
        <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa thông tin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/doancuoiky/controllers/UpdateProfile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Tên đầy đủ</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" value="<?= htmlspecialchars($info['full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($info['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Tải lên ảnh đại điện</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                            </div>
                            <button type="submit" class="btn btn-gray btn-animated">Thay đổi</button>
                            <button type="reset" class="btn btn-gray btn-animated">Hoàn tác</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</body>
</html>
