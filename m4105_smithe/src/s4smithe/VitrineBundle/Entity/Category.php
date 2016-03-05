<?php

	namespace s4smithe\VitrineBundle\Entity;

	/**
	 * Category
	 */
	class Category {

		/**
		 * @var integer
		 */
		private $id;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var \Doctrine\Common\Collections\Collection
		 */
		private $products;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->products = new \Doctrine\Common\Collections\ArrayCollection();
		}

		/**
		 * Get id
		 *
		 * @return integer 
		 */
		public function getId() {
			return $this->id;
		}

		/**
		 * Set name
		 *
		 * @param string $name
		 * @return Category
		 */
		public function setName($name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * Get name
		 *
		 * @return string 
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * Add products
		 *
		 * @param \s4smithe\VitrineBundle\Entity\Product $products
		 * @return Category
		 */
		public function addProduct(\s4smithe\VitrineBundle\Entity\Product $products) {
			$this->products[] = $products;

			return $this;
		}

		/**
		 * Remove products
		 *
		 * @param \s4smithe\VitrineBundle\Entity\Product $products
		 */
		public function removeProduct(\s4smithe\VitrineBundle\Entity\Product $products) {
			$this->products->removeElement($products);
		}

		/**
		 * Get products
		 *
		 * @return \Doctrine\Common\Collections\Collection 
		 */
		public function getProducts() {
			return $this->products;
		}

	}
	