<?php
    include('../models/DbConnection.php');
    class ExecEmployee{
        public $conn;

        public $email;
        public $username;
        public $password;
        public $fullname;

        public function __construct($email, $fullname = null, $password = null) {
            $this->email = $email;
            $this->username = strstr($this->email,"@",true);
            $this->fullname = $fullname;
            $this->password = $password;
            $this->conn = (new ConnectDatabase())->getConnection();
        }
        
        public function createEmployee() {
            $password = $this->password ? $this->password : 52300155;
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
            $sql = "INSERT INTO employee (email, user_name, password, full_name) VALUES (:email, :user_name, :password, :full_name)";
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                'email' => $this->email,
                'user_name'=> $this->username,
                'password' => $hashed_password,
                'full_name' => $this->fullname
            ]);;
        }
        
        public function deleteEmployee() {
            $sql = "DELETE FROM employee WHERE email = ?";
            try {
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    return false;  
                }
                $result = $stmt->execute([$this->email]);  
                return $result;  
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') {  
                    return false;
                } else {
                    return false;  
                }
            } catch (Exception $e) {
                return false;  
}
        }

        public function blockEmployee() {
            $stmt = $this->conn->prepare("UPDATE employee SET blocked = ? WHERE email = ?");
            return $stmt->execute([1, $this->email]);
        }

        public function unBlockEmployee() {
            $stmt = $this->conn->prepare("UPDATE employee SET blocked = ? WHERE email = ?");
            return $stmt->execute([0, $this->email]);
        }

        public function getEmail(){
            return $this->email;
        }
    }
?>

