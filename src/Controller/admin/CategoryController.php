<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/category', name: 'admin.category.')]
final class CategoryController extends AbstractController
{

    public function __construct(private CategoryRepository $repository){

    }

    #[Route('', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $categories = $this->repository->findAll();
        // $users = $em->getRepository(User::class)->findAll();
        // = $repository->findAll()


        return $this->render('user/admin/category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'title' => 'Listes des categories',
            'categories' => $categories,
        ]);
    }


    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTime());
            $em->persist($category);
            $em->flush();
            $this->addFlash('success',"La catégorie  a bien été créée");
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('user/admin/category/add.html.twig', [
            'title' => 'Création d\'une categorie',
            'category' => $category,
            'form' => $form,
        ]);

    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success',"La catégorie a bien été supprimée");
        return $this->redirectToRoute('admin.category.index');
    }
}
