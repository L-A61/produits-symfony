<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;



final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products=$productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/products/add',name:'app_products_add')]
	public function add(EntityManagerInterface $entityManager,Request $request): Response
	{
		$product=new Product();
		$form = $this->createFormBuilder($product)
            ->add('name', TextType::class,['attr' => ['class' => 'form-control']])
			->add('price',IntegerType::class,['attr' => ['class' => 'form-control']])
			->add('promotion',CheckboxType::class,['required' => false,'attr' => ['class' => 'form-check']])
			->add('discount',PercentType::class,['type' => 'integer','attr' => ['class' => 'form-control']])
			->add('category',EntityType::class, [
					// looks for choices from this entity
					'class' => Category::class,
 
					// uses the User.username property as the visible option string
					'choice_label' => 'name',
					'attr' => ['class' => 'form-control']
					])
			->add('save', SubmitType::class, ['label' => 'Créer produit','attr' => ['class' => 'btn btn-primary']])
            ->getForm();
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
			$product = $form->getData();

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
			$entityManager->persist($product);
 
			// actually executes the queries (i.e. the INSERT query)
			$entityManager->flush();

            $this->addFlash('notice','Le produit a bien été ajouté');

            return $this->redirectToRoute('app_products');
		}
		return $this->render('product/add.html.twig', [
            'form'=>$form,
			'title'=>'Ajouter produit'
        ]);
	}

    #[Route('/products/update/{id}',name:'app_products_update')]
    public function update(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class,['attr' => ['class' => 'form-control']])
			->add('price',IntegerType::class,['attr' => ['class' => 'form-control']])
			->add('promotion',CheckboxType::class,['required' => false,'attr' => ['class' => 'form-check']])
			->add('discount',PercentType::class,['type' => 'integer','attr' => ['class' => 'form-control']])
			->add('category',EntityType::class, [
					// looks for choices from this entity
					'class' => Category::class,
 
					// uses the User.username property as the visible option string
					'choice_label' => 'name',
					'attr' => ['class' => 'form-control']
					])
			->add('save', SubmitType::class, ['label' => 'Modifier produit','attr' => ['class' => 'btn btn-primary']])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($product);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            $this->addFlash('notice','Le produit a bien été modifié');

            return $this->redirectToRoute('app_products');
        }
        
        return $this->render('product/add.html.twig', [
            'form'=>$form,
            'title'=>"Modifier produit",
        ]);
    }

    #[Route('/products/delete/{id}',name:'app_products_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        $entityManager->remove($product);

        $entityManager->flush();

        $this->addFlash('notice','Le produit a bien été supprimé');

        return $this->redirectToRoute('app_products');
    }
}
