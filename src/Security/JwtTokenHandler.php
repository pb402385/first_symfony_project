<?php
// src/Security/JwtTokenHandler.php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtTokenHandler implements AccessTokenHandlerInterface
{
    private string $publicKeyPath;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->publicKeyPath = dirname(__DIR__, 2) . '../config/jwt/public.pem';
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            if (!file_exists($this->publicKeyPath)) {
                throw new \RuntimeException('Clé publique JWT manquante');
            }

            $decoded = JWT::decode(
                $accessToken,
                new Key(file_get_contents($this->publicKeyPath), 'RS256')
            );

            $user = $this->em->getRepository(User::class)->find($decoded->sub ?? 0);

            if (!$user) {
                throw new BadCredentialsException('Utilisateur non trouvé');
            }

            return new UserBadge($user->getUserIdentifier());

        } catch (\Exception $e) {
            throw new BadCredentialsException('Token invalide : ' . $e->getMessage());
        }
    }
}
