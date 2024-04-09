<?php

namespace App\Controller;

use Exception;
use App\Entity\Categories;
use App\Repository\TasksRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CategoriesController extends AbstractController
{
    
    // #[Route('/', name: 'index')]
    // public function index(): Response
    // {
    //     return $this->render('categories/index.html.twig', [
    //         'controller_name' => 'CategoriesController',
    //     ]);
    // }
    #[Route('/categories/{id}', name: 'categories_category_tasks')]
    public function listTasksByCategory(Request $request, TasksRepository $tasksRepository, CategoriesRepository $categoriesRepository): Response
    {
        //dd($request);
        $categoryId = $request->attributes->get('id');
        
        // Recherche des tâches liées à la catégorie spécifiée par son ID
        $tasks = $tasksRepository->findBy(['category' => $categoryId], ['due_date' => 'asc']);
        $category = $categoriesRepository->findOneBy(['id' => $categoryId]);
        return $this->render('categories/listTasks.html.twig', [
            'tasks' => $tasks,
            'categories' => $categoriesRepository->findBy([],
                ['id' => 'asc']),
            'categoryname' => $category,
            'fromFunction' => 'tasksByCategory',
        ]);
    }

    #[Route('/category/add', name: 'categories_category_add')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager):JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $name = $requestData['Titre'];
        // $name = $request->query->get('Titre');
        $category = new Categories();
        
        $category->setName($name);

        try {
            $entityManager->persist($category);
            $entityManager->flush();
            
            // Retourner une réponse JSON avec un code 200 si l'ajout est réussi
            return new JsonResponse(['success' => true], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            // En cas d'erreur, retourner une réponse JSON avec un code d'erreur approprié
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{id}', name: 'delete_category')]
    public function delete(Categories $category, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        // on supprime la tâche de la bdd
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Catégorie supprimée avec succès');

        $referer = $request->headers->get('referer');
        
        return $this->redirect($referer);
    }
}
