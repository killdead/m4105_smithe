<?php

	namespace s4smithe\VitrineBundle\Repository;
	use Doctrine\ORM\EntityRepository;

	/**
	 * ClientRepository
	 *
	 * This class was generated by the Doctrine ORM. Add your own custom
	 * repository methods below.
	 */
	class ProductRepository extends EntityRepository {

		public function findAllOrderedByName() {
			return $this->getEntityManager()
					->createQuery('SELECT p FROM s4smitheVitrineBundle:Product p ORDER BY p.name ASC')
					->getResult();
		}

		public function findAllBetterSales() {
			$stmt = $this->getEntityManager()->getConnection()->prepare(
				'SELECT p.id
				FROM (
					SELECT l.product_id AS id, SUM(l.qte) AS cnt
					FROM lignecommande l
					GROUP BY l.product_id
					ORDER BY cnt DESC ) popu
				NATURAL JOIN product p
				LIMIT 5'
			);

			$stmt->execute();
			$productIDs = $stmt->fetchAll();
			$products = [ ];

			foreach ( $productIDs as $id ) {
				$products[] = $this->getEntityManager()
					->getRepository( 's4smitheVitrineBundle:Product' )
					->findOneById( $id );
			}

			return $products;

			// SELECT * FROM ( SELECT l.product_id AS id, count(*) AS cnt FROM lignecommande l GROUP BY l.product_id ORDER BY cnt ) pop NATURAL JOIN product ORDER BY name LIMIT 5
		}

	}
