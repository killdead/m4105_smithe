<?php
	/*
		Fichier : DefaultController.php
		Auteur : Camille Persson
		Creation : 08/02/2016
		Modification :
			> 08/02/2016		Mise en place du name + mentions
			> 12/02/2016		Ajout template
/
		Ceci est le controlleur par défaut de l'application Vitrine
	*/
	

	namespace s4smithe\VitrineBundle\Controller;

	use s4smithe\VitrineBundle\Entity\Client;
	use s4smithe\VitrineBundle\Entity\Commande;
	use s4smithe\VitrineBundle\Entity\CommandeWorkflow;
	use s4smithe\VitrineBundle\Entity\Panier;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;

	class PanierController extends Controller {

		/**
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function contenuPanierAction() {
			$panier = $this->getSessionPanier();
			$articles = array();
			
			if ( !empty( $panier->getArticles() ) ) {
				foreach ( $panier->getArticles() as $item ) {
					$article = $this->getDoctrine()->getManager()
						->getRepository( 's4smitheVitrineBundle:Product' )
						->findOneById( $item[ 'id' ] );
					
					$articles[] = array(
						'article' => $article,
						'qte'     => $item[ 'qte' ]
					);
				}
			}

			return $this->render(
				's4smitheVitrineBundle:Panier:panier.html.twig',
				array(
					'articles' => $articles,
					'panier'   => $panier,
					'total'    => $this->getTotalPanier()
				)
			);
		}

		/**
		 * @return \Symfony\Component\HttpFoundation\RedirectResponse
		 */
		public function emptyPanierAction() {
			$panier = $this->getSessionPanier();
			$panier->clearPanier();
			
			return $this->redirectToRoute( 's4smithe_vitrine_contenuPanier' );
		}
		
		/**
		 * @param $articleId
		 * @param $qte
		 *
		 * @return \Symfony\Component\HttpFoundation\RedirectResponse
		 */
		public function ajouterUnArticleAction( $articleId, $qte ) {
			$panier = $this->getSessionPanier();
			
			$panier->addArticle( $articleId, $qte );
			
			$this->setSessionPanier( $panier );
			
			return $this->redirectToRoute( 's4smithe_vitrine_contenuPanier' );
		}
		
		/**
		 * @param $articleId
		 * @param $qte
		 *
		 * @return \Symfony\Component\HttpFoundation\RedirectResponse
		 */
		public function enleverUnArticleAction( $articleId, $qte ) {
			$panier = $this->getSessionPanier();
			
			$panier->removeOneArticle( $articleId, $qte );
			
			$this->setSessionPanier( $panier );
			
			return $this->redirectToRoute( 's4smithe_vitrine_contenuPanier' );
		}
		
		/**
		 * @param $articleId
		 *
		 * @return \Symfony\Component\HttpFoundation\RedirectResponse
		 */
		public function enleverArticlesAction( $articleId ) {
			$panier = $this->getSessionPanier();
			
			$panier->removeArticles( $articleId );
			
			$this->setSessionPanier( $panier );
			
			return $this->redirectToRoute( 's4smithe_vitrine_contenuPanier' );
		}
		
		/**
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function panierInfoHeaderAction() {
			$panier = $this->getSessionPanier();
			
			$sessionUser = $this->getSessionUser();
			$userConnected = ( $sessionUser >= 0 ) ? true : false;
			
			return $this->render(
				's4smitheVitrineBundle:Panier:panierInfo.html.twig',
				array(
					'total'         => $this->getTotalPanier(),
					'nbArticle'     => $panier->getNbArticle(),
					'userConnected' => $userConnected
				)
			);
		}

		// TODO: Fix bug ajout commandeWorklfow
		/**
		 * @return \Symfony\Component\HttpFoundation\Response
		 */
		public function validationAction() {
			// Création d'une commande pour l'Utilisateur
			$user = $this->findUser( $this->getSessionUser() );
			$commande = new Commande( $user );
			
			// Données panier
			$panier = $this->getSessionPanier();
			$articles = array();
			$total = $this->getTotalPanier();
			$nbArticles = $panier->getNbArticle();
			
			
			$em = $this->getDoctrine()->getManager();
			$em->persist( $commande );
			
			if ( !empty( $panier->getArticles() ) ) {
				foreach ( $panier->getArticles() as $item ) {
					// Objet Product récupérer avec l'ID de l'item
					$article = $this->getDoctrine()->getManager()
						->getRepository( 's4smitheVitrineBundle:Product' )
						->findOneById( $item[ 'id' ] );
					
					// Création d'une ligne de commande
					$ligneCommande = new CommandeWorkflow( $commande, $article, $item[ 'qte' ] );
					
					// Génération de chaque ligne pour la validation de la commande
					$articles[] = array(
						'article' => $article,
						'qte'     => $item[ 'qte' ]
					);
					
					$em->persist( $ligneCommande );
				}

			}

			$em->flush();
			
			// Création d'un nouveau panier vide => Commande validé
			$this->setSessionPanier( new Panier() );
			
			return $this->render(
				's4smitheVitrineBundle:Panier:validation.html.twig',
				array(
					'commande'   => $commande,
					'artiles'    => $articles,
					'total'      => $total,
					'nbArticles' => $nbArticles
				)
			);
		}
		
		
		/**
		 * @return mixed
		 */
		private function getSessionPanier() {
			$session = $this->getRequest()->getSession();
			
			return $session->get( 'panier', new Panier() );
		}
		
		/**
		 * @param $panier
		 */
		private function setSessionPanier( $panier ) {
			$session = $this->getRequest()->getSession();
			$session->set( 'panier', $panier );
		}
		
		/**
		 * @return int
		 */
		private function getSessionUser() {
			$session = $this->getRequest()->getSession();
			
			return $session->get( 'userId', -1 );
		}


		/**
		 * @return int
		 */
		private function getTotalPanier() {
			$total = 0;
			$panier = $this->getSessionPanier();
			
			if ( !empty( $panier->getArticles() ) ) {
				foreach ( $panier->getArticles() as $item ) {
					$article = $this->getDoctrine()->getManager()
						->getRepository( 's4smitheVitrineBundle:Product' )
						->findOneById( $item[ 'id' ] );
					
					$total += $article->getPrice() * $item[ 'qte' ];
				}
			}
			
			return $total;
		}
		
		/**
		 * @param $id
		 *
		 * @return Client
		 */
		private function findUser( $id ) {
			$user = $this->getDoctrine()->getManager()
				->getRepository( 's4smitheVitrineBundle:Client' )
				->findOneById( $id );
			
			if ( !$user ) {
				$user = new Client();
				$user->setName( 'Inconus' );
			}
			
			return $user;
		}
	}
