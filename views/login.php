<?php
session_start();
if (isset($_SESSION["user"])) { 
    header("Location:/doancuoiky/views/main.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
    <div class="container">
        <h1 class="logo">POINT OF SALE</h1>
        
        <div class='border border-3 w-50 border-black rounded mx-auto p-3 m-5'>
            <form action="/doancuoiky/controllers/Validatelogin.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label fs-5">Tên đăng nhập:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        </div>
                        <input type="text" id="username" class="form-control mb-3"  
                        placeholder="Nhập tên đăng nhập" name="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label fs-5">Mật khẩu:</label>
                       
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        </div>
                        <input type="password" id="password" class="form-control mb-3" 
                        placeholder="Nhập mật khẩu" name="password" required>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="btn-gray btn-animated">Đăng nhập</button>
                </div>
            </form>
        </div>
            <?php 
                if (isset($_SESSION['message']) && isset($_SESSION['alert_type'])) {
                    echo '<div id="alertBox" class="alert '.$_SESSION['alert_type'].'" role="alert">' . $_SESSION["message"] . '</div>';
                    unset($_SESSION['message']);
                    unset($_SESSION['alert_type']);
                    echo '<script>document.addEventListener("DOMContentLoaded", function() { document.getElementById("username").focus(); });</script>';
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


