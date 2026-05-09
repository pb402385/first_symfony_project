<?php
// src/Security/JwtTokenManager.php
namespace App\Security;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenManager
{
    private string $privateKeyPath = '../config/jwt/private.pem';
    private string $publicKeyPath  = '../config/jwt/public.pem';
    private string $passphrase     = 'passphrase'; // À mettre dans .env

    public function createToken(User $user): string
    {
        $now = time();
        $payload = [
            'iss'  => 'http://localhost',
            'aud'  => 'http://localhost',
            'iat'  => $now,
            'exp'  => $now + (24 * 3600), // 24 heures
            'sub'  => $user->getId(),
            'email'=> $user->getEmail(),
            'roles'=> $user->getRoles(),
            ];

        //dd(file_get_contents($this->privateKeyPath));
        return JWT::encode($payload, file_get_contents($this->privateKeyPath), 'RS256');
    }

    public function decodeToken(string $token): array
    {
        return (array) JWT::decode(
        $token,
        new Key(file_get_contents($this->publicKeyPath), 'RS256')
        );
    }
}
