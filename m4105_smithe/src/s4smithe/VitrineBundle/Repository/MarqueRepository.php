<?php

	namespace s4smithe\VitrineBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	/**
	 * MarqueRepository
	 *
	 * This class was generated by the Doctrine ORM. Add your own custom
	 * repository methods below.
	 */
	class MarqueRepository extends EntityRepository {
		public function findAllOrderedByName( $limit = -1 ) {
			
			if( $limit <= 0 ) 
				return $this->getEntityManager()
					->createQuery('SELECT p FROM s4smitheVitrineBundle:Marque p ORDER BY p.name')
					->getResult();
			else 
				return $this->getEntityManager()
					->createQuery('SELECT p FROM s4smitheVitrineBundle:Marque p ORDER BY p.name')
					->setMaxResults( $limit )
					->getResult();
			
		}
	}
	