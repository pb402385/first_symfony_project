<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user', name: 'user.')]
final class UserController extends AbstractController
{

    public function __construct(private UserRepository $repository){

    }

    #[Route('', name: 'index', methods: ['POST','GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $this->repository->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs',
            'users' => $users,
        ]);
    }

    #[Route('/users/{country}', name: 'users.country', methods: ['POST','GET'])]
    public function showByCountry(string $country, Request $request): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $this->repository->findByCountry($country);
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs (' .strtoupper($country). ')',
            'users' => $users,
        ]);
    }


    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['POST','GET'])]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->repository->find($id);

        return $this->render('user/show.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Page de '.$user->getName(),
            'user' => $user,
            'image' => $user->getImage(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['POST','GET'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $userapp = $this->getUser();
        if ($userapp) {
            // On est logué, on ne peut modifier l'utilisateur que si l'on est ADMIN ou si il s'agit de notre compte

            if( !$this->isGranted('ROLE_ADMIN') && ((int)$userapp->getUserIdentifier() != $user->getId()) ) {
                $this->addFlash('danger',"Vous n'avez pas le droit de modifier ce User!");
                return $this->redirectToRoute('user.index');
            }
        }


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


    #[Route('/add', name: 'add', methods: ['POST','GET'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //dd($form);

        if ($form->isSubmitted()) {

            /**
            // Utile pour débug quand le form ne passe pas les validateurs
            dump([
                'Submitted' => $form->isSubmitted(),
                'Valid' => $form->isValid(),
                'Errors' => $form->getErrors(true, true), // Voir toutes les erreurs
                'Image Data' => $form->get('image')->getData() ? 'Fichier reçu' : 'Aucun fichier',
            ]);
            **/

            // La création de compte est normalement interdite, seul l'admin peut en faire
            // On choisit donc de donner le mot de passe "test" par défaut quand l'admin crée un compte
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, 'test');
            $user->setPassword($hashedPassword);

            if ($form->isValid()) {

                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    // Lecture du contenu du fichier en binaire
                    $imageContent = file_get_contents($imageFile->getPathname());

                    $user->setImage($imageContent);
                }

                //dd($form->getData(), $user->getImage());
                $user->setCreatedAt(new \DateTimeImmutable());
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', "L'utilisateur a bien été créé");
                return $this->redirectToRoute('user.index');
            }
        }
        return $this->render('user/admin/add.html.twig', [
            'title' => 'Création d\'un utilisateur',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST','PUT'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $userapp = $this->getUser();
        if ($userapp) {
            // On est logué, on ne peut modifier l'utilisateur que si l'on est ADMIN ou si il s'agit de notre compte

            if( !$this->isGranted('ROLE_ADMIN') && ((int)$userapp->getUserIdentifier() != $user->getId()) ) {
                $this->addFlash('danger',"Vous n'avez pas le droit de supprimer ce User!");
                return $this->redirectToRoute('user.index');
            }
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('success',"L'utilisateur a bien été supprimé");
        return $this->redirectToRoute('user.index');
    }
}
