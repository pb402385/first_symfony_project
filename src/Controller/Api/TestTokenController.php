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

        $user = $this->getUser();

        //dd($this->getUser(), $user);

        /**
        dd([
            'UserIdentifier' => $user ? $user->getUserIdentifier() : null,
            'User' => $user ? $user->getEmail() : null,
            'Authorization Header' => $request->headers->get('Authorization'),
            'All Headers' => $request->headers->all(),
        ]);
         **/

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
