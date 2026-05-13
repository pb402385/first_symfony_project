<?php
// src/Security/JwtTokenHandler.php
namespace App\Security;

use App\Entity\User;
use App\Repository\RevokedTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtTokenHandler implements AccessTokenHandlerInterface
{
    private string $publicKeyPath;

    public function __construct(
        private EntityManagerInterface $em,
        private RevokedTokenRepository $revokedTokenRepository,
    ){
        $this->publicKeyPath = dirname(__DIR__, 2) . '/config/jwt/public.pem';
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $text_debug = false;

        if($text_debug){
            dump('=== JWT HANDLER DÉBUT ===');
            dump('Token reçu (premiers 100 chars) : ' . substr($accessToken, 0, 100) . '...');
        }

        // Vérifier si le token est révoqué
        if ($this->revokedTokenRepository->isRevoked($accessToken)) {
            if($text_debug) dump('✅ Le TOKEN a été révoqué !');
            throw new BadCredentialsException('Token has been revoked');
        }

        try {
            if (!file_exists($this->publicKeyPath)) {
                if($text_debug) dump('❌ Clé publique non trouvée : ' . $this->publicKeyPath);
                throw new \RuntimeException('Clé publique introuvable');
            }

            $keyContent = file_get_contents($this->publicKeyPath);
            if($text_debug) dump('✅ Clé publique chargée (' . strlen($keyContent) . ' caractères)');

            $decoded = JWT::decode($accessToken, new Key($keyContent, 'RS256'));
            if($text_debug) dump('✅ Token décodé avec succès', (array)$decoded);

            $userId = $decoded->sub ?? null;
            if (!$userId) {
                throw new \RuntimeException('Aucun "sub" dans le token');
            }

            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                if($text_debug) dump('❌ Utilisateur non trouvé en base (ID = ' . $userId . ')');
                throw new BadCredentialsException('User not found');
            }

            if($text_debug){
                dump('✅ Utilisateur authentifié : ' . $user->getEmail());
                dump('=== JWT HANDLER FIN SUCCESS ===');
            }

            // Cette partie faisait échouer la création du badge car c'est l'email qui sert de clé pour identifier l'utilisateur
            //$badge = new UserBadge($user->getUserIdentifier());

            $userIdentifier = $user->getEmail();
            $badge = new UserBadge($userIdentifier);

            if($text_debug) dump('✅ Utilisateur badge (isResolved): ' . $badge->isResolved());

            return $badge;

        } catch (\Exception $e) {
            if($text_debug) dump('💥 ERREUR JWT : ' . $e->getMessage());
            if($text_debug) dump('=== JWT HANDLER FIN ERREUR ===');
            throw new BadCredentialsException('Token invalide : ' . $e->getMessage());
        }
    }
}
