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
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $decoded = JWT::decode(
                $accessToken,
                new Key(file_get_contents('config/jwt/public.pem'), 'RS256')
            );

            $user = $this->em->getRepository(User::class)->find($decoded->sub ?? 0);

            if (!$user) {
                throw new BadCredentialsException('Utilisateur non trouvé');
            }

            if (!$user->isVerified()) {
                throw new BadCredentialsException('Email non vérifié');
            }

            return new UserBadge($user->getUserIdentifier());

        } catch (\Exception $e) {
            throw new BadCredentialsException('Token invalide : ' . $e->getMessage());
        }
    }
}
