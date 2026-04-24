<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Coupon;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-database')]
class SeedDatabaseCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = [
            ['Iphone', 100.0],
            ['Наушники', 20.0],
            ['Чехол', 10.0],
        ];

        foreach ($products as [$name, $price]) {
            $product = new Product();
            $product->setName($name)->setPrice($price);
            $this->entityManager->persist($product);
            $output->writeln("Created product: $name");
        }

        $coupons = [
            ['P10', Coupon::TYPE_PERCENT, 10.0],
            ['D15', Coupon::TYPE_FIXED, 15.0],
            ['P100', Coupon::TYPE_PERCENT, 100.0],
        ];

        foreach ($coupons as [$code, $type, $value]) {
            $coupon = new Coupon();
            $coupon->setCode($code)->setType($type)->setValue($value);
            $this->entityManager->persist($coupon);
            $output->writeln("Created coupon: $code");
        }

        $this->entityManager->flush();
        $output->writeln('Database seeded successfully!');

        return Command::SUCCESS;
    }
}