<?php

namespace App\Controller\Admin;


use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController 
{
    #[Route('/', name: 'index')]
    public function index():Response{
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em):Response{
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée un nouveau produit 
        $product = new Products();

        // on crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //on traite la requete 
        $productForm->handleRequest($request);

        //dd($productForm);
        //on verifie si le formulaire est soumi et valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            //convertir les prix en centimes
            $prix = $product->getPrice()*100;
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();
            //on crée un partial flash à integrer au base.html.twig
            $this->addFlash('success', 'produit ajouté avec succès');

            //on redirige
            return $this->redirectToRoute('admin_products_index');
        }

        // return $this->render('admin/products/add.html.twig', [
        //     'productForm' => $productForm->createView()
        // ]);
        // on pt faire aussi 
        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em):Response
    {
        // on vérifie si l'utilisateur pt éditer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        //convertir les prix en centimes
            $prix = $product->getPrice()/100;
            $product->setPrice($prix);
        // on crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //on traite la requete 
        $productForm->handleRequest($request);

        //dd($productForm);
        //on verifie si le formulaire est soumi et valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            //convertir les prix en centimes
            $prix = $product->getPrice()*100;
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();
            //on crée un partial flash à integrer au base.html.twig
            $this->addFlash('success', 'produit modifié avec succès');

            //on redirige
            return $this->redirectToRoute('admin_products_index');
        }
        return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));
    }
    
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product):Response
    {
        // on vérifie si l'utilisateur pt supprimer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }
}