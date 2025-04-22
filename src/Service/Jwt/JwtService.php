<?php

namespace App\Service\Jwt;

use App\Models\LogicModel\RefreshTokenLogic;
use Dotenv\Dotenv;
use DateTimeImmutable;

class JwtService
{
    private const ALGORITHM = 'RS256'; 
    private $privateKeyPath;
    private $publicKeyPath;
    private RefreshTokenLogic $refreshTokenLogic;

    public function __construct(RefreshTokenLogic $refreshTokenLogic)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');  
        $dotenv->load();
        $this->privateKeyPath = $_ENV['JWT_PRIVATE_KEY_PATH'];
        $this->publicKeyPath = $_ENV['JWT_PUBLIC_KEY_PATH'];
        $this->privateKeyPath = realpath(__DIR__ . '/../../../' . $this->privateKeyPath);
        $this->publicKeyPath = realpath(__DIR__ . '/../../../' . $this->publicKeyPath);
        $this->refreshTokenLogic = $refreshTokenLogic;
    }

    public function createTokens(string $email): array
    {
        $accessToken = $this->createAccessToken($email);  
        $refreshToken = $this->createRefreshToken($email);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
    }

    private function createJwt(array $payload): string
    {
        $header = [
            'alg' => self::ALGORITHM,
            'typ' => 'JWT'
        ];
        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));
        $data = $encodedHeader . '.' . $encodedPayload;
        $signature = $this->sign($data);
        return $data . '.' . $signature;
    }


    public function refreshTokens(string $email, string $refreshToken): array
    {
        if (!$this->verifyRefreshToken($email, $refreshToken)) {
            throw new \Exception('Invalid refresh token');
        }

        $newAccessToken = $this->createAccessToken($email);  
        $newRefreshToken = $this->createRefreshToken($email);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ];
    }

    private function createAccessToken(string $email): string
    {
        $payload = [
            'sub' => $email,
            'exp' => (new DateTimeImmutable())->modify('+1 hour')->getTimestamp(), 
        ];
        return $this->createJwt($payload);
    }

    private function createRefreshToken(string $email): string
    {
        $token = bin2hex(random_bytes(64)); 
        $this->refreshTokenLogic->addRefreshToken($email, $token);  
        return $token;
    }

    public function verifyJwt(string $jwt): bool
    {
        list($encodedHeader, $encodedPayload, $encodedSignature) = explode('.', $jwt);
        $signature = $this->base64UrlDecode($encodedSignature);
        $data = $encodedHeader . '.' . $encodedPayload;
        $publicKey = file_get_contents($this->publicKeyPath);
        return openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    public function verifyRefreshToken(string $email, string $token): bool
    {
        return $this->refreshTokenLogic->checkRefreshToken($email, $token);
    }

    public function deleteRefreshToken(string $email): bool
    {
        return $this->refreshTokenLogic->deleteRefreshToken($email);
    }

    private function sign(string $data): string
    {
        $privateKey = file_get_contents($this->privateKeyPath);
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
