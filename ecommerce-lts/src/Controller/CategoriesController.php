<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{id}', name: 'list')]
    public function list(Categories $category) : Response 
    {
        // on va chercher la liste des produits de la catégorie
        $products = $category->getProducts();
        
        return $this->render('categories/list.html.twig', compact('category', 'products'));
        // syntaxe alternative
        // return $this->render('categories/list.html.twig', [
        //     'category' => $category,
        //     'products' => $products
        // ]);        
    }
}
