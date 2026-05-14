<?php
namespace App\Security;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Security\JwtTokenManager;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private RouterInterface $router,
        private UserRepository $repository
    ) {}

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
    ): Response
    {
        $userInterface = $token->getUser();
        $jwtManager = new JwtTokenManager();

        $id = $userInterface->getUserIdentifier();
        $user = $this->repository->find(intval($id));

        // On crée notre TOKEN JWT ici
        if($user !== null){
            $token = $jwtManager->createToken($user);
            //dd($token);
        }


        // ========================
        // ICI tu peux faire ce que tu veux après connexion réussie
        // ========================

        // Exemple : Log
        // $this->logger->info('User logged in', ['email' => $user->getEmail()]);

        // Message flash
        $request->getSession()->getFlashBag()->add('success', 'Connexion réussie (token JWT créé)! ' . $user->getEmail() . ' !');

        // Exemple : Redirection personnalisée selon les rôles
        //if (in_array('ROLE_ADMIN', $user->getRoles())) {
        //    return new RedirectResponse($this->router->generate('admin_dashboard'));
        //}

        // Redirection par défaut
        return new RedirectResponse($this->router->generate('home.login.ok', [
            'success-login' => 1,
            'token' => $token,
        ]));
    }
}
