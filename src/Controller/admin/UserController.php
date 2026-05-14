<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user', name: 'user.')]
final class UserController extends AbstractController
{

    public function __construct(private UserRepository $repository){

    }

    #[Route('', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $users = $this->repository->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs',
            'users' => $users,
        ]);
    }

    #[Route('/users/{country}', name: 'users.country')]
    public function showByCountry(string $country, Request $request): Response
    {
        $users = $this->repository->findByCountry($country);
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs (' .strtoupper($country). ')',
            'users' => $users,
        ]);
    }


    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->repository->find($id);

        return $this->render('user/show.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Page de '.$user->getName(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success',"L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user.index');
        }
        return $this->render('user/admin/edit.html.twig', [
            'title' => 'Edition de '.$user->getName(),
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setCreatedAt(new \DateTimeImmutable());
            $em->persist($user);
            $em->flush();
            $this->addFlash('success',"L'utilisateur a bien été créé");
            return $this->redirectToRoute('user.index');
        }
        return $this->render('user/admin/add.html.twig', [
            'title' => 'Création d\'un utilisateur',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $em->remove($user);
        $em->flush();
        $this->addFlash('success',"L'utilisateur a bien été supprimé");
        return $this->redirectToRoute('user.index');
    }
}
