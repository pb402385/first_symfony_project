<?php
// src/Security/JwtTokenManager.php
namespace App\Security;

use App\Entity\User;
use Firebase\JWT\JWT;

class JwtTokenManager
{
    private string $privateKeyPath = __DIR__ . '/../../config/jwt/private.pem';

    public function createToken(User $user): string
    {
        $issuedAt = time();

        $payload = [
            'iss'  => 'http://localhost',
            'aud'  => 'http://localhost',
            'iat'  => $issuedAt,
            'exp'  => $issuedAt + 86400,   // 24 heures
            'sub'  => $user->getId(),
            'email'=> $user->getEmail(),
            'roles'=> $user->getRoles(),
        ];

        return JWT::encode($payload, file_get_contents($this->privateKeyPath), 'RS256');
    }
}
