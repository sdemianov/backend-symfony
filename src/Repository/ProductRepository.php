<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Exception\BusinessException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws BusinessException
     */
    public function findOrFail(int $id): Product
    {
        $product = $this->find($id);

        if (!$product) {
            throw new BusinessException(sprintf('Product with ID %d not found', $id));
        }

        return $product;
    }

}