<?php
include_once(__DIR__ . '/../views/General.php');
require_once('CategoryClass.php');

$category = new Category();
$categories = $category->getAllCategories();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .table {
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            background: #1a1a1a;  
            color: #ffffff;  
            border: 1px solid #ffffff;
        }

        .table thead {
            background: #000000;  
        }

        .table th, .table td {
            border: 1px solid #1a1a1a;
            padding: 12px;
        }

        .table-hover tbody tr:hover {
            background: #333333;
            transition: background 0.3s ease-in-out;
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

        .box {
            max-width: 1500px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container box">
        <div class="row align-items-start">
            <div class="col-lg-4 col-md-5 col-sm-12 mb-4">
                <h1>Danh Sách Danh Mục</h1>
                <?php if ($_SESSION['role'] !== 'employee'): ?>
                    <button onclick="window.location.href='addCategory.php'" 
                            class="btn btn-gray btn-lg btn-animated">
                        ➕ Thêm Danh Mục
                    </button>
                <?php endif; ?>
            </div>

            <div class="col-lg-8 col-md-7 col-sm-12">
                <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle">
                            <thead>
                                <tr class="table-dark bg-black text-uppercase">
                                    <th class="p-3">Tên Danh Mục</th>
                                    <th class="p-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $row): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td>
                                            <a href="categoryDetail.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                                class="btn btn-gray btn-animated">
                                                Xem Chi Tiết
                                            </a>
                                            <?php if ($_SESSION['role'] !== 'employee'): ?>
                                                <a href="editCategory.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                                    class="btn btn-gray btn-animated">
                                                    Sửa
                                                </a>
                                                <a href="deleteCategory.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                                    class="btn btn-gray btn-animated"
                                                    onclick="event.preventDefault(); confirmDelete(this.href);">
                                                    Xóa
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-black fs-4">Không có danh mục nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
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

            function confirmDelete(href) {
                showConfirmationDialog("xóa danh mục", "xóa", "#d33", "xóa danh mục", function() {
                    window.location.href = href;
                });
            }
        </script>
</body>
</html>
