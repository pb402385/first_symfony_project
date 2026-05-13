<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class UserService
{
    public function __construct(
        private readonly Security $security
    ) {}

    /**
     * Récupère l'utilisateur connecté via son JWT Token
     */
    public function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        // Vérification de type (important)
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    /**
     * Exemple d'utilisation
     */
    public function faireUneAction(): void
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            throw new \RuntimeException('Aucun utilisateur authentifié via JWT');
        }

        // Tu peux maintenant utiliser $user librement
        echo "Utilisateur : " . $user->getEmail();
        echo "Rôles : " . implode(', ', $user->getRoles());
    }
}
