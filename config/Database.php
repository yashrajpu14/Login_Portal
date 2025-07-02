<?php
namespace Config;

require_once __DIR__ . '/../helper/getEnvironmentValue.php';
use Helper\EnvironmentValue;

class Database {
    private $conn;

    public function __construct() {
        $conn = $this->connect();
    }

    private function connect() {
        $env = new EnvironmentValue();
        $host = $env->getSecretKey('DB_HOST');
        $db   = $env->getSecretKey('DB_NAME');
        $user = $env->getSecretKey('DB_USER');
        $pass = $env->getSecretKey('DB_PASS');
        try {
            $this->conn = new \PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Database connection failed."]);
            exit;
        }
        return $this->conn;
    }

    public function select($email)
    {
        try 
        {
            $query = "SELECT created_time, password_hash FROM users WHERE email = ? AND is_active = 1 LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC); 
            return $result;
        }
        catch(\Throwable $e)
        {
            echo $e->getMessage();
            throw new \Exception($e);
        }
    }

    public function updatePassword($email, $newPassword, $timestamp)
    {
        try 
        {
            // SQL query to update the password
            $query = "UPDATE users SET password_hash = :password , created_time = :timestamp  WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            // Bind the parameters
            $stmt->bindParam(':password', $newPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':timestamp', $timestamp);
            // Execute the query
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } 
        catch (\Throwable $e) 
        {
            echo $e->getMessage();
            throw new \Exception("Error updating password: " . $e->getMessage());
        }
    }
}
