<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

class TestTokenController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        dump('=== TestTokenController DÉBUT ===');

        $token = ''.$request->headers->get('Authorization');
        dump([
            'Authorization Header' => $request->headers->get('Authorization'),
            'getUser()' => $this->getUser(),
            'token' => $token,
            //$tokenHandler->getToken()' => $this->tokenHandler->getUserBadgeFrom($token),
        ]);


        $user = $this->getUser();

        if (!$user) {
            dump('❌ $user non trouvé : ' . $user);
        } else {
            dump('✅ $user chargé (' . $user->getEmail());
        }

        //dd('TestTokenController');
        //dd($this->getUser(), $user);

/*
        dump([
            'UserIdentifier' => $user ? $user->getUserIdentifier() : null,
            'User' => $user ? $user->getEmail() : null,
            'Authorization Header' => $request->headers->get('Authorization'),
            'All Headers' => $request->headers->all(),
        ]);
*/


        if (!$user) {
            return $this->json([
                'error' => 'Utilisateur non authentifié'
            ], 401);
        }

        return $this->json([
            'success' => true,
            'user' => [
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
                'name'  => $user->getName(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }

}
