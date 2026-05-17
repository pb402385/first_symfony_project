<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Entity\Document;
use App\Entity\User;
use App\Form\DocumentType;
use App\Form\UserType;
use App\Repository\DocumentRepository;
use App\Security\JwtTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Service\UserService;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/document', name: 'document.')]
final class DocumentController extends AbstractController
{

    public function __construct(private DocumentRepository $repository, private UserService $userService){

    }

    #[Route('', name: 'index', methods: ['POST','GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser(); // peut être null

        $documents = $this->repository->findAll();

        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
            'title' => 'Liste des documents',
            'documents' => $documents,
            'user' => $user,
        ]);

    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {

        $document = $this->repository->find($id);
        $title = $document->getTitle();

        $categoryId = $document->getCategoryId();
        if($categoryId !== null){
            $category = $em->getRepository(Category::class)->find($categoryId)->getLabel();
        } else {
            $category = 'Non renseigné';
        }

        return $this->render('document/show.html.twig', [
            'controller_name' => 'DocumentController',
            'title' => 'Page de '.$title,
            'document' => $document,
            'category' => $category,
        ]);

    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET','POST'])]
    public function edit(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($user) {
            // On est logué, on ne peut modifier l'utilisateur que si l'on est ADMIN ou si il s'agit de notre compte
            if( !$this->isGranted('ROLE_ADMIN') && ((int)$user->getUserIdentifier() != $document->getUserID()) ) {
                $this->addFlash('danger',"Vous n'avez pas le droit de modifier ce document!");
                //throw new AuthenticationException('l\'utilisateur n\a pas le droit de réaliser cette action!');
                return $this->redirectToRoute('document.index');
            }
        }

        $form = $this->createForm(DocumentType::class, $document);
        // on recupère les catégories nécessaires à l'affichage du form
        $categoryId = $document->getCategoryId();
        $categories = $em->getRepository(Category::class)->findAll();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //on recupère la catégorie "category_select"
            $categoryId = $request->request->get('category_select');
            $document->setCategoryId($categoryId);

            //$em->persist($document);
            $em->flush();
            $this->addFlash('success',"Le document a bien été modifié");
            return $this->redirectToRoute('document.index');
        }
        return $this->render('document/admin/edit.html.twig', [
            'title' => 'Edition de '.$document->getTitle(),
            'document' => $document,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/add_to_fs', name: 'add.to.fs', methods: ['GET','POST'])]
    public function addToFS(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response
    {

        $document = new Document();
        // on recupère les catégories nécessaires à l'affichage du form
        $categories = $em->getRepository(Category::class)->findAll();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


                $uploadedFile = $form->get('file')->getData();

                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                    $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/documents';

                    // Création du dossier si nécessaire
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0775, true);
                    }

                    $destination = $uploadDirectory . '/' . $newFilename;

                    try {
                        // SOLUTION LA PLUS ROBUSTE : Lecture en mémoire
                        $fileContent = file_get_contents($uploadedFile->getPathname());

                        if ($fileContent === false) {
                            throw new \Exception('Impossible de lire le fichier temporaire');
                        }

                        // Écriture du fichier
                        if (file_put_contents($destination, $fileContent) === false) {
                            throw new \Exception('Impossible d\'écrire le fichier sur le disque');
                        }

                        // Mise à jour de l'entité
                        $document->setFile($newFilename);
                        $document->setOriginalName($uploadedFile->getClientOriginalName());
                        $document->setMimeType($uploadedFile->getMimeType());

                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Erreur lors de l\'upload du fichier : ' . $e->getMessage());
                        return $this->render('document/admin/add.html.twig', [
                            'form' => $form->createView(),
                            'title' => 'Création d\'un document',
                            'document' => $document,
                            'categories' => $categories,
                            ]);
                    }
                }

                $document->setCreatedAt(new \DateTimeImmutable());

                $document->setUpdatedAt(new \DateTime());

                //on recupère la catégorie "category_select"
                $categoryId = $request->request->get('category_select');
                if ($categoryId) {
                    $document->setCategoryId((int)$categoryId);
                }

                // Utilisateur connecté
                if ($this->getUser()) {
                    $document->setUserID((int)$this->getUser()->getId());   // Utilise getId() de préférence
                }

                $em->persist($document);
                $em->flush();
                $this->addFlash('success', "Le document a bien été créé");
                return $this->redirectToRoute('document.index');

        }

        // Si le formulaire n'est pas valide
        if ($form->isSubmitted() && !$form->isValid()) {
            dump($form->getErrors(true, true)); // Debug
        }

        return $this->render('document/admin/add.html.twig', [
            'title' => 'Création d\'un document',
            'document' => $document,
            'categories' => $categories,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST','PUT'])]
    public function delete(Document $document, Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if ($user) {
            // On est logué, on ne peut modifier l'utilisateur que si l'on est ADMIN ou si il s'agit de notre compte

            if( !$this->isGranted('ROLE_ADMIN') && ((int)$user->getUserIdentifier() != $document->getUserID()) ) {
                $this->addFlash('danger',"Vous n'avez pas le droit de supprimer ce document!");
                //throw new AuthenticationException('l\'utilisateur n\a pas le droit de réaliser cette action!');
                return $this->redirectToRoute('document.index');
            }
        }

        // Suppression du fichier physique
        if ($document->getFile()) {
            $filePath = $this->getParameter('kernel.project_dir')
                . '/public/uploads/documents/'
                . $document->getFile();

            if (file_exists($filePath)) {
                try {
                    unlink($filePath);   // Supprime le fichier
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de la suppression du fichier : ' . $e->getMessage());
                    return $this->redirectToRoute('document.index');
                }
            }
        }

        $em->remove($document);
        $em->flush();
        $this->addFlash('success',"Le document a bien été supprimé");
        return $this->redirectToRoute('document.index');
    }
}
