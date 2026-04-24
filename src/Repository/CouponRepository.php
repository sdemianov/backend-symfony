<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\BusinessException;
use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findOneByCode(string $code): ?Coupon
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findOneByCodeOrFail(string $code): ?Coupon
    {
        $coupon = $this->findOneByCode($code);

        if (!$coupon) {
            throw new BusinessException(sprintf('Coupon %s not found', $code));
        }

        return $coupon;
    }
}