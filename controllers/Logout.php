<?php
session_start();
if($_SESSION['role'] == 'employee'){
    require_once('../models/DbConnection.php');
    $conn = (new ConnectDatabase())->getConnection();

    $stmt = $conn->prepare("UPDATE employee SET status = ? WHERE user_name = ?");
    $stmt->execute([0, $_SESSION['username']]);
}

session_unset();  
session_destroy(); 
header("Location:/doancuoiky/views/login.php");
exit();
?>