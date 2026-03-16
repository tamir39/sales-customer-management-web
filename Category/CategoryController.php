<?php
include_once('CategoryClass.php');

class CategoryController {
    private $categoryModel;
    public function __construct()
    {
        $this->categoryModel = new Category();
    }
    public function getCategoryModel() {
        return $this->categoryModel;
    }
    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        include(__DIR__ . '/../views/category/indexCategory.php');
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $createdBy = intval($_POST['created_by'] ?? 0);

            if (empty($name) || $createdBy <= 0) {
                echo "<script>alert('⚠️ Vui lòng nhập tên danh mục và ID người tạo hợp lệ!');</script>";
                return;
            }

            $result = $this->categoryModel->addCategory($name, $description, $createdBy);

            if ($result === true) {
                header('Location: indexCategory.php');
                exit();
            } else {
                echo "<script>alert('❌ Lỗi khi thêm danh mục!');</script>";
            }
        }

        include(__DIR__ . '/../views/category/addCategory.php');
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);

        if (!$category) {
            echo "<script>alert('⚠️ Danh mục không tồn tại!');</script>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $createdBy = intval($_POST['created_by'] ?? 0);

            if (empty($name) || $createdBy <= 0)
            {
                echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!');</script>";
                return;
            }

            $result = $this->categoryModel->updateCategory($id, $name, $description, $createdBy);

            if ($result === true) {
                header('Location: indexCategory.php');
                exit();
            } else {
                echo "<script>alert('❌ Lỗi khi cập nhật danh mục!');</script>";
            }
        }

        include('editCategory.php');
    }

    public function updateCategory($id, $name, $description, $createdBy)
    {
        return $this->categoryModel->updateCategory($id, $name, $description, $createdBy);
    }
    public function delete($id)
    {
        $result = $this->categoryModel->deleteCategory($id);

        if ($result === true) {
            header('Location: indexCategory.php');
            exit();
        } else {
            echo "<script>alert('❌ Lỗi khi xóa danh mục!');</script>";
        }
    }
}
?>
