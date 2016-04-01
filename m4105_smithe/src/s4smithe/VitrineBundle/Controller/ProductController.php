<?php

	/*
	 * Fichier : ProductController.php
	 * Auteur: SMITH Emmanuel
	 * Création: 08/03/2016
	 * Modification: 01/04/2016
	 *
	 * Controôleur pour la gestion des entitées Products (Artilces)
	 */

	namespace s4smithe\VitrineBundle\Controller;

	use s4smithe\VitrineBundle\Entity\Product;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\Form\Extension\Core\Type\FileType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * Product controller.
	 *
	 */
	class ProductController extends Controller {

		/**
		 * Lists all Product entities.
		 *
		 */
		public function indexAction() {
			$em = $this->getDoctrine()->getManager();

			$products = $em->getRepository('s4smitheVitrineBundle:Product')->findAll();

			return $this->render(
				's4smitheVitrineBundle:Product:index.html.twig', array(
					'products' => $products,
			));
		}

		/**
		 * Creates a new Product entity.
		 *
		 */
		public function newAction(Request $request) {
			$product = new Product();
			$form = $this->createForm('s4smithe\VitrineBundle\Form\Type\ProductType', $product);
			$form->add( 'brochure', FileType::class, array( 'label' => 'Image' ) );

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($product);
				$em->flush();

				return $this->redirectToRoute('product_show', array('id' => $product->getId()));
			}

			return $this->render(
				's4smitheVitrineBundle:Product:new.html.twig', array(
					'product' => $product,
					'form' => $form->createView(),
			));
		}

		/**
		 * Finds and displays a Product entity.
		 *
		 */
		public function showAction(Product $product) {
			$deleteForm = $this->createDeleteForm($product);

			return $this->render(
				's4smitheVitrineBundle:Product:show.html.twig', array(
					'product' => $product,
					'delete_form' => $deleteForm->createView(),
			));
		}

		/**
		 * Displays a form to edit an existing Product entity.
		 *
		 */
		public function editAction(Request $request, Product $product) {
			$deleteForm = $this->createDeleteForm($product);
			$editForm = $this->createForm('s4smithe\VitrineBundle\Form\Type\ProductType', $product);
			$editForm->handleRequest($request);

			if ($editForm->isSubmitted() && $editForm->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($product);
				$em->flush();

				return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
			}

			return $this->render(
				's4smitheVitrineBundle:Product:edit.html.twig', array(
					'product' => $product,
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
			));
		}

		/**
		 * Deletes a Product entity.
		 *
		 */
		public function deleteAction(Request $request, Product $product) {
			$form = $this->createDeleteForm($product);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->remove($product);
				$em->flush();
			}

			return $this->redirectToRoute('product_index');
		}

		/**
		 * Creates a form to delete a Product entity.
		 *
		 * @param Product $product The Product entity
		 *
		 * @return \Symfony\Component\Form\Form The form
		 */
		private function createDeleteForm(Product $product) {
			return $this->createFormBuilder()
					->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
					->setMethod('DELETE')
					->getForm()
			;
		}

	}
