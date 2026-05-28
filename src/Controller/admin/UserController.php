<?php

namespace App\Controller\admin;

use App\Entity\Document;
use App\Entity\Note;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


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


        // numéro de la page souhaitée
        $page = $request->query->getInt('page', 1);
        // termes recherchés
        $search = $request->query->get('search');
        // Tri (par date de création par défaut)
        $sort = $request->query->get('sort', 'u.createdAt');
        // odre décroissant
        $direction = $request->query->get('direction', 'DESC');
        // nombre de résultats par page
        $limit = $request->query->getInt('limit', 5);

        $viewMode = $request->query->get('view_mode','table');

        if($viewMode == 'cards'){
            $limit = 4;
        }

        $users = $this->repository->paginateUsersWithSearchTerm(
            $page,
            $limit,
            $sort,
            $direction,
            $search
        );

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs',
            'users' => $users,
            //'maxPage' => $maxPage,
            'page' => $page,
            'search' => $search,
            'view_mode' => $viewMode,
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

        $fromProfile = $request->query->get('fromProfile','');
        $fromDocument = $request->query->get('fromDocument','');

        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // On récupère l'user ainsi que tous les documents associé à l'user
        $userWithDocumentsAndNotes = $this->repository->findUserAndDocumentsAndNotesByUserID($id);

        //On doit s'assurer que le document existe !
        if(!$userWithDocumentsAndNotes){
            $this->addFlash('danger', "Cet utilisateur n'existe pas (ou plus) !");
            return $this->redirectToRoute('user.index');
        }

        $documents = $userWithDocumentsAndNotes->getDocuments()->toArray();

        // On récupère également les réactions de l'utilisateurs par rapport à l'ensemble des documents
        $limit = 5; //On récupère ses 5 derniers avis, comme ça on peut savoir quels sont les derniers documents qui ont interressé cet utilisateur
        $avis = $em->getRepository(Note::class)->findDocumentsAndNotesByUserID($id, $limit);

        //dd($userWithDocumentsAndNotes, $user, $documents, $avis);

        return $this->render('user/show_profil.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Page de '.$userWithDocumentsAndNotes->getName(),
            'user' => $userWithDocumentsAndNotes,
            'image' => $userWithDocumentsAndNotes->getImage(),
            'documents' => $documents,
            'avis' => $avis,
            'from_document' => $fromDocument,
            'from_profile' => $fromProfile,
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
        if ($form->isSubmitted()) {

            // dd($form->isValid(), $form->getErrors());

            if ($form->isValid()) {
                //Si on n'a pas d'image dans le formulaire
                $imageFile = $form->get('image')->getData();

                if( $imageFile != null ) {
                    // Lecture du contenu du fichier en binaire
                    $imageContent = file_get_contents($imageFile->getPathname());
                    $user->setImage($imageContent);
                }

                $em->persist($user);
                $em->flush();
                $this->addFlash('success', "L'utilisateur a bien été modifié");
                return $this->redirectToRoute('user.index');
            }
        }

        return $this->render('user/admin/edit_profil.html.twig', [
            'title' => 'Edition de '.$user->getName(),
            'form' => $form,
            'user' => $user,
            'image' => $user->getImage(),
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
    public function delete(User $user, Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        // On vérifie que l'utilisateur a bien un token valide pour accéder à la page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $userapp = $this->getUser();
        $isDeletingSelf = $userapp && $userapp->getId() === $user->getId();

        if ($userapp) {

            // 1. Récupérer l'utilisateur système
            $systemUser = $this->repository->findOneBy(['email' => 'noreply@docshare.fr']);

            if (!$systemUser) {
                $this->addFlash('danger', 'Utilisateur système "noreply@docshare.fr" non trouvé.');
                return $this->redirectToRoute('user.index');
            }

            // 2. Réassigner les documents
            foreach ($user->getDocuments() as $document) {
                $document->setUser($systemUser);
            }

            // 3. Réassigner les notes
            foreach ($user->getNotes() as $note) {
                $note->setUser($systemUser);
            }

            //dd($systemUser, $user);

            // On est logué, on ne peut modifier l'utilisateur que si l'on est ADMIN ou si il s'agit de notre compte

            if( !$this->isGranted('ROLE_ADMIN') && ((int)$userapp->getUserIdentifier() != $user->getId()) ) {
                $this->addFlash('danger',"Vous n'avez pas le droit de supprimer ce User!");
                return $this->redirectToRoute('user.index');
            }

            //dd($user);

            // si on se supprime sois même c'est différent
            if ($isDeletingSelf) {
                // On remplace d'abord le token de sécurité par l'utilisateur système
                $systemToken = new UsernamePasswordToken($systemUser, 'main', $systemUser->getRoles());
                $tokenStorage->setToken($systemToken);

                // Maintenant on peut supprimer l'utilisateur en toute sécurité
                $em->remove($user);
                $em->flush();

                $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
                // Déconnexion immédiate
                return $this->redirectToRoute('app_logout');
            }

            $em->remove($user);
            $em->flush();
            $this->addFlash('success',"L'utilisateur a bien été supprimé");
        }

        return $this->redirectToRoute('user.index');
    }
}
