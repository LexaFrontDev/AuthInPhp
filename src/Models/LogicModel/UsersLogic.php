<?php
namespace App\Models\LogicModel;

use App\Models\Core\DbConnect;
use PDO;

class UsersLogic
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DbConnect::getConnection();
    }

    public function registerUser(string $name, string $email, string $password): bool
    {
        if (empty($name) || empty($email) || empty($password))  return false;
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) return false;  
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword]);
    }
    

    public function loginUser(string $email, string $password): bool
    {
        if (empty($email) || empty($password))   return false;
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password']))  return false;
        return $user['id'];
    }

    public function getList(): array
{
    $stmt = $this->db->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
