<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Entity\Document;
use App\Entity\User;
use App\Form\DocumentType;
use App\Form\UserType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/document', name: 'document.')]
final class DocumentController extends AbstractController
{

    public function __construct(private DocumentRepository $repository){

    }

    #[Route('', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $documents = $this->repository->findAll();

        return $this->render('document/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Liste des documents',
            'documents' => $documents,
        ]);

        return $this->redirectToRoute('document.index');
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {

        //dd($request, $request->attributes->get('id'));
        $document = $this->repository->find($id);
        $title = $document->getTitle();


        $categoryId = $document->getCategoryId();
        if($categoryId !== null){
            $category = $em->getRepository(Category::class)->find($categoryId)->getLabel();
        } else {
            $category = null;
        }

        return $this->render('document/show.html.twig', [
            'controller_name' => 'DocumentController',
            'title' => 'Page de '.$title,
            'document' => $document,
            'category' => $category,
        ]);

    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(Document $document, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(DocumentType::class, $document);
        // on recupère les catégories nécessaires à l'affichage du form
        $categoryId = $document->getCategoryId();
        $categories = $em->getRepository(Category::class)->findAll();
        $form->handleRequest($request);
        //dd($form);
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


    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        $document = new Document();
        // on recupère les catégories nécessaires à l'affichage du form
        $categories = $em->getRepository(Category::class)->findAll();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($request->request->get('category_select'));
            $document->setCreatedAt(new \DateTimeImmutable());
            $document->setUpdatedAt(new \DateTime());

            //on recupère la catégorie "category_select"
            $categoryId = $request->request->get('category_select');
            $document->setCategoryId($categoryId);
            $em->persist($document);
            $em->flush();
            $this->addFlash('success',"Le document a bien été créé");
            return $this->redirectToRoute('document.index');
        }
        return $this->render('document/admin/add.html.twig', [
            'title' => 'Création d\'un document',
            'document' => $document,
            'categories' => $categories,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($document);
        $em->flush();
        $this->addFlash('success',"Le document a bien été supprimé");
        return $this->redirectToRoute('document.index');
    }
}
