<?php
namespace App\Models\LogicModel;

use App\Models\Core\DbConnect;
use PDO;

class RefreshTokenLogic
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DbConnect::getConnection();
    }

   
    public function addRefreshToken(string $email, string $token): bool
    {
        if (empty($email) || empty($token)) return false;
        $this->deleteRefreshToken($email);
        $stmt = $this->db->prepare("INSERT INTO refresh_tokens (email, token, expires_at) VALUES (?, ?, ?)");
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); 
        return $stmt->execute([$email, $token, $expiresAt]);
    }

    public function deleteRefreshToken(string $email): bool
    {
        if (empty($email)) return false;
        $stmt = $this->db->prepare("DELETE FROM refresh_tokens WHERE email = ?");
        return $stmt->execute([$email]);
    }

    public function checkRefreshToken(string $email, string $token): bool
    {
        if (empty($email) || empty($token)) return false;
        $stmt = $this->db->prepare("SELECT token, expires_at FROM refresh_tokens WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return false; 
        if ($data['token'] !== $token || strtotime($data['expires_at']) < time()) return false; 
        return true; 
    }

    public function getRefreshToken(string $email): ?string
    {
        if (empty($email)) return null;
        $stmt = $this->db->prepare("SELECT token FROM refresh_tokens WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $data['token'] : null; 
    }
}

