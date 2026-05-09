<?php
// src/Security/JwtTokenHandler.php
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
        private EntityManagerInterface $em,
        private JwtTokenManager $jwtManager
    ) {}

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $decoded = JWT::decode(
                $accessToken,
                new Key(file_get_contents('config/jwt/public.pem'), 'RS256')
            );

            $decodedArray = (array) $decoded;

            $userId = (int) $decodedArray['sub'];

            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                throw new BadCredentialsException('User not found');
            }

            return new UserBadge($user->getUserIdentifier());

        } catch (\Exception $e) {
            throw new BadCredentialsException('Invalid JWT token: ' . $e->getMessage());
        }
    }
}
