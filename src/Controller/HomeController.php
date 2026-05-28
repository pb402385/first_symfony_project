<?php
namespace App\Controller;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index')]
    public function index(EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        $categoryStats = null;
        if ($user) {
            // On est logué, on cherche toutes les catégories et le nombre de documents associés
            $documentRepository = $em->getRepository(Document::class);
            $categoryStats = $documentRepository->getDocumentsCountByCategory();
        }

        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'categoryStats' => $categoryStats,
        ]);

    }

    #[Route('/home', name: 'home.fullpath.index')]
    public function indexFullPath(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $categoryStats = null;
        if ($user) {
            // On est logué, on cherche toutes les catégories et le nombre de documents associés
            $documentRepository = $em->getRepository(Document::class);
            $categoryStats = $documentRepository->getDocumentsCountByCategory();
        }

        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'categoryStats' => $categoryStats,
        ]);
    }

    #[Route('/new-user', name: 'home.new-user')]
    public function newUser(Request $request): Response
    {
        //dd($request->query->get('email'));
        $email = $request->query->get('email');
        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'message' => 'Compte créé avec succès!',
            'email' => $email,
        ]);

    }

    #[Route('/login-ok', name: 'home.login.ok')]
    public function loginOk(Request $request): Response
    {

        $token = $request->query->get('token');

        // Render a Twig template
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'message' => 'Connexion ok!',
            'token' => $token,
        ]);

    }

    #[Route('/how_it_works', name: 'home.how.it.works')]
    public function howItworks(): Response
    {
        // Render a Twig template
        return $this->render('home/how_it_works.html.twig', [
            'controller_name' => 'HomeController',
        ]);

    }

}
