<?php
include_once(__DIR__ . '/../views/General.php');
include_once('CategoryClass.php');

$categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($categoryId == 0) {
    die("Lỗi: ID danh mục không hợp lệ!");
}

$categoryModel = new Category();
$category = $categoryModel->getCategoryById($categoryId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Danh Mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .box {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        } 
    </style>
</head>
<body>
    <a href="/doancuoiky/Category/Category.php" class="returnButton">
        <i class="fas fa-arrow-left"></i> 
    </a>
    <div class="container box">
        <h1>Chi Tiết Danh Mục</h1>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Thuộc tính</th>
                    <th>Giá trị</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Tên Danh Mục</strong></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                </tr>
                <tr>
                    <td><strong>Mô Tả</strong></td>
                    <td><?php echo nl2br(htmlspecialchars($category['description'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Ngày Tạo</strong></td>
                    <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                </tr>
                <tr>
                    <td><strong>Người Tạo</strong></td>
                    <?php
                        $connect = new ConnectDatabase();
                        $conn = $connect->getConnection();
                        
                        $sql = "SELECT full_name FROM admin where id =?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$category['created_by']]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
