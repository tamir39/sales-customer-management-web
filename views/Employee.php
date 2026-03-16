<?php
include_once(__DIR__ . '/../views/General.php');
function scheduleFileDeletion($filePath) {
    $deleteScript = __DIR__ . "/../views/Delete.php";

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen("start /B php " . escapeshellarg($deleteScript) . " " . escapeshellarg($filePath), "r"));
    } else {
        exec("nohup php " . escapeshellarg($deleteScript) . " " . escapeshellarg($filePath) . " > /dev/null 2>&1 &");
    }
}

if(isset($_SESSION['login_file'])){
    scheduleFileDeletion($_SESSION['login_file']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/doancuoiky/views/employee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>       
    <div style="display: flex; justify-content: center;">
        <button class="btn-gray btn-animated" onclick="openModal()">➕ Tạo nhân viên</button>
    </div>
    
    <?php
        $sql = "SELECT id, avatar, full_name, email, phone, status, blocked, has_changed_password, login_flag FROM employee";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
<div class="d-flex justify-content-center mt-4">
    <table class="table table-bordered table-hover text-center align-middle">
        <thead>
            <tr class="table-dark text-center">
                <th class="fw-bold">Ảnh đại diện</th>
                <th class="fw-bold">Tên đầy đủ</th>
                <th class="fw-bold">Liên lạc</th>
                <th class="fw-bold">Chi tiết</th>
                <th class="fw-bold">Chỉnh sửa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td>
                        <div class="avatar">
                            <?php if ($employee['avatar']) : ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($employee['avatar']) ?>" 
                                    alt="Avatar" 
                                    class="rounded-circle mb-3" 
                                    width="70" height="70">
                            <?php else: ?>
                                <img src="default.jpg" alt="Default Avatar" class="rounded-circle mb-3" width="70" height="70">
                            <?php endif; ?>
                        </div>
                        <form action="/doancuoiky/views/ViewSales.php" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($employee['id']) ?>">
                            <input type="hidden" name="name" value="<?= htmlspecialchars($employee['full_name']) ?>">
                            <button type="submit" class="btn btn-gray btn-sm w-100 fw-bold btn-animated">Xem thông tin bán hàng</button>
                        </form>
                    </td>
                    <td><?= htmlspecialchars($employee['full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-start">
                        <p><strong>Email:</strong> <?= htmlspecialchars($employee['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($employee['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                    </td>
                    <td class="text-start">
                        <p><strong>Trạng thái:</strong> 
                            <?= ($employee['status']) ? 
                                '<span class="badge bg-success">Đang hoạt động</span>' : 
                                '<span class="badge bg-secondary">Offline</span>' ?>
                        </p>
                        <p><strong>Khóa tài khoản:</strong> 
                            <?= $employee['blocked'] ? 
                                '<span class="badge bg-danger">Bị Khóa</span>' : 
                                '<span class="badge bg-secondary">Không</span>' ?>
                        </p>
                        <p><strong>Đổi mật khẩu lần đầu:</strong> 
                            <?= $employee['has_changed_password'] ? 
                                '<span class="badge bg-success">Đã đổi mật khẩu</span>' : 
                                '<span class="badge bg-danger">Chưa đổi mật khẩu</span>' ?>
                        </p>
                        <p><strong>Đăng nhập thông qua link:</strong> 
                            <?= $employee['login_flag'] ? 
                                '<span class="badge bg-success">Hoàn thành</span>' : 
                                '<span class="badge bg-warning">Chưa hoàn thành</span>' ?>
                        </p>
                    </td>
                    <td class="text-start">
                        <?php 
                            $email = $employee['email']; 
                            $timeRemaining = isset($_SESSION['login_time_' . $email]) 
                                ? ($_SESSION['login_time_' . $email] + 60 - time()) 
                                : 0;

                            if ($timeRemaining <= 0) {
                                unset($_SESSION['login_time_' . $email]);  
                            }

                            $disabled = ($timeRemaining > 0) ? "disabled" : ""; 
                        ?>

                        <script>
                            function countdown(){
                                let timeRemaining = <?php echo $timeRemaining; ?>;
                                let email = <?= json_encode($employee['email']) ?>;

                                let button = document.getElementById(`sendButton-${email}`);
                                let textSpan = document.getElementById(`button-text-${email}`);

                                function updateTimer() {
                                    if (timeRemaining <= 0) {
                                        textSpan.innerHTML = "Gửi link đăng nhập";
                                        button.disabled = false; 
                                        timeRemaining--; 
                                    } else {
                                        textSpan.innerHTML = `Thời gian còn lại: ${timeRemaining} giây`;
                                        button.disabled = true;
                                        timeRemaining--; 
                                    }
                                }
                                updateTimer();  
                                let interval = setInterval(() => {
                                    updateTimer();
                                    if (timeRemaining < 0) clearInterval(interval);  
                                }, 1000);
                                }
                            document.addEventListener("DOMContentLoaded", countdown);
                        </script>
                        <form action="/doancuoiky/controllers/SendLoginLink.php" method="POST" 
                            id="sendEmailForm-<?= htmlspecialchars($email) ?>" >

                            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                            <button type="submit"
                                class="btn btn-gray btn-sm w-100 fw-bold btn-animated position-relative"
                                id="sendButton-<?= htmlspecialchars($email) ?>"
                                <?= $disabled ?>>
                                <span id="button-text-<?= htmlspecialchars($email) ?>">Gửi link đăng nhập</span>
                            </button>
                        </form>

                        <?php if (!$employee['blocked']): ?>
                            <form action="/doancuoiky/controllers/BlockEmployee.php" method="POST" id="blockForm-<?= htmlspecialchars($employee['email']) ?>">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($employee['email']) ?>">
                                <button type="button" class="btn btn-gray btn-sm w-100 fw-bold btn-animated"
                                    onclick="confirmBlock('<?= htmlspecialchars($employee['email']) ?>', '<?= htmlspecialchars($employee['full_name']) ?>')">
                                    Khóa tài khoản
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="/doancuoiky/controllers/UnblockEmployee.php" method="POST" id="unblockForm-<?= htmlspecialchars($employee['email']) ?>">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($employee['email']) ?>">
                                <button type="button" class="btn btn-gray btn-sm w-100 fw-bold btn-animated"
                                    onclick="confirmUnblock('<?= htmlspecialchars($employee['email']) ?>', '<?= htmlspecialchars($employee['full_name']) ?>')">
                                    Bỏ khóa tài khoản
                                </button>
                            </form>
                        <?php endif;?>

                        <form action="/doancuoiky/controllers/DeleteEmployee.php" method="POST" id="deleteForm-<?= htmlspecialchars($employee['email']) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($employee['email']) ?>">
                            <button type="button" class="btn btn-gray btn-sm w-100 fw-bold btn-animated"
                                onclick="confirmDelete('<?= htmlspecialchars($employee['email']) ?>', '<?= htmlspecialchars($employee['full_name']) ?>')">
                                Xóa tài khoản
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
           function showConfirmationDialog(action, confirmText, confirmColor, message, callback) {
                Swal.fire({
                    title: `Xác nhận hành động ${action}`,
                    text: `Bạn có chắc là muốn ${message}?`,
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

            function confirmDelete(employeeEmail, employeeName) {
                showConfirmationDialog("xóa tài khoản", "xóa tài khoản", "#d33", `xóa ${employeeName}`, function() {
                    document.getElementById(`deleteForm-${employeeEmail}`).submit();
                });
            }

            function confirmUnblock(employeeEmail, employeeName) {
                showConfirmationDialog("gỡ khóa tài khoản", "gỡ khóa", "#28a745", `gỡ khóa ${employeeName}`, function() {
                    document.getElementById(`unblockForm-${employeeEmail}`).submit();
                });
            }

            function confirmBlock(employeeEmail, employeeName) {
                showConfirmationDialog("khóa tài khoản", "khóa", "#343a40", `khóa ${employeeName}`, function() {
                    document.getElementById(`blockForm-${employeeEmail}`).submit();
                });
            }

            function confirmLogout() {
                showConfirmationDialog("Đăng xuất", "Đăng xuất", "#d33", "Đăng xuất", function() {
                    window.location.href = "/doancuoiky/controllers/logout.php";
                });
            }

            function openModal() {
                document.getElementById("employeeModal").classList.add("active");
            }
            function closeModal(event) {
                if (!event || event.target === document.getElementById("employeeModal")) {
                    document.getElementById("employeeModal").classList.remove("active");
                }
            }

        </script>
 
<div id="employeeModal" class="create-modal-overlay" onclick="closeModal(event)">
    <div class="create-modal-content" onclick="event.stopPropagation()">  
        <h3>Tạo tài khoản mới</h3>
        <form action="/doancuoiky/controllers/CreateEmployee.php" method="POST">
            <label>Tên đầy đủ của nhân viên:</label>
            <input type="text" class="form-control" name="fullname" placeholder="Nhập tên...">
            <label>Địa chỉ email:</label>
            <input type="email" class="form-control" name="email" placeholder="Nhập email...">
            
            <div class="mt-3">
                <button type="submit" class="btn btn-gray btn-animated" onclick="closeModal()">Tạo</button>
                <button type="reset" class="btn btn-gray btn-animated">Hoàn tác</button>
                <button type="button" class="btn btn-gray btn-animated" onclick="closeModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>