<?php
session_start();
    include('../models/DbConnection.php');
    class Verify{
        public $conn;

        public $email;
        public $username;
        public $password;
        public $fullname;
        public $id;
        
        public $role;
        public $blocked;
        public $login_flag;

        public function __construct($username)
        {
            $this->username = $username;
            $this->conn = (new ConnectDatabase())->getConnection();
        }

        public function getVerification ($password){
            $sql = "SELECT * FROM admin WHERE user_name = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$this->username]); 
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->role = 'admin';
            $this->fullname = $user['full_name'];
            $this->id = $user['id'];

            if ($user && password_verify($password, $user['password'])) {
                return true;
            } else {
                $sql = "SELECT * FROM employee WHERE user_name = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$this->username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->role = 'employee';
                $this->fullname = $user['full_name'];
                $this->id = $user['id'];

                if($user && $user['blocked'] == true){
                    $this->blocked = true;
                }

                $this->login_flag = $user['login_flag'];

                if ($user  &&  password_verify($password, $user['password'])) {
                    $_SESSION['has_changed_password'] = $user['has_changed_password'];
                    $get = $this->conn->prepare("UPDATE employee SET status = ? WHERE user_name = ?");
                    $get->execute([1, $this->username]);
                    return true;
                } else {
                    return false;
                }
            }
        }

        public function changePassword($newPassword){
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            if($_SESSION['role'] == 'employee'){
                $stmt = $this->conn->prepare("UPDATE employee SET password = ?, has_changed_password = TRUE WHERE user_name = ?");
            } 
            else {
                $stmt = $this->conn->prepare("UPDATE admin SET password = ? WHERE user_name = ?");
            }
            
            return $stmt->execute([$hashedPassword, $this->username]);
        }

        public function changeProfile($full_name, $phone, $avatar){    
            if($_SESSION['role'] == 'employee'){
                $stmt = $this->conn->prepare("UPDATE employee SET full_name = ? , phone = ?, avatar = ? WHERE user_name = ?");
            } 
            else {
                $stmt = $this->conn->prepare("UPDATE admin SET full_name = ?,  phone = ?, avatar = ? WHERE user_name = ?");
            }
            $this->fullname = $full_name;  
            return $stmt->execute([$full_name, $phone, $avatar, $this->username]);

        }

        public function getRole(){
            return $this->role;
        }

        public function getFullname(){
            return $this->fullname;
        }

        public function getEmail(){
            return $this->email;
        }

        public function getBlocked(){
            return $this->blocked;
        }

        public function getUsername(){
            return $this->username;
        }

        public function getLoginflag(){
            return $this->login_flag;
        }

        public function getId(){
            return $this->id;
        }

        public function setLoginFlag($login_flag) {
            $this->login_flag = $login_flag;
            $sql = $this->role == 'employee' ? 
                "UPDATE employee SET login_flag = ? WHERE user_name = ?" : 
                "UPDATE admin SET login_flag = ? WHERE user_name = ?";
        
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$login_flag, $this->username]);
        }
        
    }
?>