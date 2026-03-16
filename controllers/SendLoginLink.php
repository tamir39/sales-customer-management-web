<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor0/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = htmlspecialchars($_POST["email"]);
    sendLoginLink($email);
} elseif(isset($_SESSION['link_for'])){
    sendLoginLink($_SESSION['link_for']);
}

function sendLoginLink($email){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = ''; // Điền email vào nơi này.
        $mail->Password = ''; // Thêm mật khẩu ứng dụng vào nơi này.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom($mail->Username, 'ADMIN');

        $randomFilename = uniqid() . '.php';
        $filePath = __DIR__ . "/../views/" . $randomFilename;
        $_SESSION['login_file'] = $filePath;
        $startTime = time();
        $file_startTime = $filePath . 'startTime';
        
        $_SESSION['login_time_' . $email] = time();

        $loginLink = "http://localhost/doancuoiky/views/$randomFilename";
        
        $content = "<?php session_start();
        \$_SESSION['first_login'] = '$loginLink';
        \$_SESSION['file'] = '$filePath';
        \$_SESSION['$file_startTime'] = '$startTime';
        include('../views/FirstLogin.php');
        ?>\n";

        file_put_contents($filePath, $content);

        $link = "http://localhost/doancuoiky/views/$randomFilename";
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'LOGIN FORM';
        $mail->Body = 
        "<h1> Nhấn vào <a href='$link'>đây</a> để đăng nhập. Link sẽ mất sau 1 phút!</h1>.
        <h2><i>Tên đăng nhập là phần đầu email của bạn. 
        Ví dụ email của bạn là abc@gmail.com thì tên đăng nhập là 'abc'.</i></h2>
        <h2><i>Mật khẩu mặc định là '52300155'.</i></h2>";

        $mail->send();
        if(isset($_SESSION['link_for'])){
            $_SESSION['message'] = "✅ Tài khoản đã được tạo, link đăng nhập đã được gửi cho " . $email . ".";
            unset($_SESSION['link_for']);
        } 
        else $_SESSION['message'] = "✅ Link đến " . $email . " đã được gửi thành công!";
        $_SESSION['alert_type'] = "alert-success";
        
    } catch (Exception $e) {
        $_SESSION['message'] = "❌ Lỗi: Không thể gửi link.";
        $_SESSION['alert_type'] = "alert-danger";
    }

    header("Location: /doancuoiky/views/Employee.php");
    exit();
}
?>
