<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .logo{
            font-family: inherit; 
            font-weight: bold;
            color:black;
            text-align: center; font-size: 120px; 
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
    
<?php         
    if(isset($_SESSION['file'])):
        $filePath = $_SESSION['file'];
        $file_startTime = $filePath . 'startTime';
        $startTime = $_SESSION[$file_startTime] ?? time();
    endif;
?>

<script>
    function countdown() {
        let serverStartTime = <?php echo $startTime; ?> * 1000; 
        let duration = 60 * 1000;

        let now = Date.now();
        let elapsedTime = now - serverStartTime;
        let remainingTime = Math.max(duration - elapsedTime, 0);

        let timerElement = document.getElementById("timer");
        let content = document.getElementById("content");
        let messageElement = document.getElementById("message");

        function updateTimer() {
            let timeLeft = Math.floor(remainingTime / 1000);

            if (timeLeft <= 0) {
                messageElement.innerHTML = "⏳ Thời gian đã hết!";
                timerElement.style.display = "none";
                content.style.display = "none";
                setTimeout(function () {
                    window.location.href = "/expired.php";
                }, 2000);
            } else {
                timerElement.innerHTML = timeLeft;
                remainingTime -= 1000;
            }
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    }
    document.addEventListener("DOMContentLoaded", countdown);
</script>

    <div class="container">
        <h1 class="logo">POINT OF SALE</h1>
        <p class="alertBox" id="message" style="text-align: center;"></p>
        
        <div id="content">
            <p class="alertBox" style="text-align: center;">⏳ Thời gian còn lại <span id="timer"></span> giây.</p>
            <div class="border border-3 w-50 border-black rounded mx-auto p-3 m-5">
                <form action="/doancuoiky/controllers/Validatelogin.php" method="POST">
                    <div class="form-group">
                        <label for="user_name" class="form-label fs-5">Tên đăng nhập:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            </div>
                            <input type="text" id="user_name" class="form-control mb-3"  
                            placeholder="Nhập tên đăng nhập" name="user_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pass_word" class="form-label fs-5">Mật khẩu:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            </div>
                            <input type="password" id="pass_word" class="form-control mb-3" 
                            placeholder="Nhập mật khẩu" name="pass_word" required>
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <button type="submit" class="btn-gray btn-animated">Đăng nhập</button>
                    </div>
                </form>
            </div>
        </div>

            <?php 
                if (isset($_SESSION['message']) && isset($_SESSION['alert_type'])) {
                    echo '<div id="alertBox" class="alert '.$_SESSION['alert_type'].'" role="alert">' . $_SESSION["message"] . '</div>';
                    unset($_SESSION['message']);
                    unset($_SESSION['alert_type']);
                    echo '<script>document.addEventListener("DOMContentLoaded", function() { document.getElementById("user_name").focus(); });</script>';
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
    </div>
</body>
</html>