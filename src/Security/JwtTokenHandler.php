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
        $this->publicKeyPath = dirname(__DIR__, 2) . '/config/jwt/public.pem';
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        dump('=== JWT HANDLER DÉBUT ===');
        dump('Token reçu (premiers 100 chars) : ' . substr($accessToken, 0, 100) . '...');

        try {
            if (!file_exists($this->publicKeyPath)) {
                dump('❌ Clé publique non trouvée : ' . $this->publicKeyPath);
                throw new \RuntimeException('Clé publique introuvable');
            }

            $keyContent = file_get_contents($this->publicKeyPath);
            dump('✅ Clé publique chargée (' . strlen($keyContent) . ' caractères)');

            $decoded = JWT::decode($accessToken, new Key($keyContent, 'RS256'));
            dump('✅ Token décodé avec succès', (array)$decoded);

            $userId = $decoded->sub ?? null;
            if (!$userId) {
                throw new \RuntimeException('Aucun "sub" dans le token');
            }

            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                dump('❌ Utilisateur non trouvé en base (ID = ' . $userId . ')');
                throw new BadCredentialsException('User not found');
            }

            dump('✅ Utilisateur authentifié : ' . $user->getEmail());
            dump('=== JWT HANDLER FIN SUCCESS ===');

            return new UserBadge($user->getUserIdentifier());

        } catch (\Exception $e) {
            dump('💥 ERREUR JWT : ' . $e->getMessage());
            dump('=== JWT HANDLER FIN ERREUR ===');
            throw new BadCredentialsException('Token invalide : ' . $e->getMessage());
        }
    }
}
