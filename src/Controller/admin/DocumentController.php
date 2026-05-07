<?php

namespace App\Controller\admin;

use App\Entity\Document;
use App\Entity\User;
use App\Form\DocumentType;
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
        /*
        $users = $this->repository->findAll();
        // $users = $em->getRepository(User::class)->findAll();
        // = $repository->findAll()

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Utilisateurs',
            'users' => $users,
        ]);
        */
        return $this->redirectToRoute('user.index');
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {
        /*
        //dd($request, $request->attributes->get('id'));
        $user = $this->repository->find($id);
        $email = $user->getEmail();

        return $this->render('user/show.html.twig', [
            'controller_name' => 'UserController',
            'title' => 'Page de '.$user->getName(),
            'user' => $user,
        ]);
        */
        return $this->redirectToRoute('user.index');
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        /*
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
        */
        return $this->redirectToRoute('user.index');
    }


    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        /*
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
        */
        return $this->redirectToRoute('user.index');
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        /*
        $em->remove($user);
        $em->flush();
        $this->addFlash('success',"L'utilisateur a bien été supprimé");
        */
        return $this->redirectToRoute('user.index');
    }
}
