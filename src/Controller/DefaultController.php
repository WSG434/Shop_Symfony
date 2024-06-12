<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('main/default/index.html.twig', []);
    }

    #[Route('/edit-product/{id}', name: 'product_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[Route('/add-product', name: 'product_add', methods: ['GET', 'POST'])]
    public function editProduct(Request $request, ?int $id): Response
    {
        $entityManager= $this->getDoctrine()->getManager();
        if ($id){
            $product = $entityManager->getRepository(Product::class)->find($id);
        } else
        {
            $product = new Product();
        }
        $form = $this->createFormBuilder($product)
            ->add('title', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('main/default/edit_product.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
