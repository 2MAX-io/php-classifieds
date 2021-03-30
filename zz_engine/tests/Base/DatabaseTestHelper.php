<?php

declare(strict_types=1);

namespace App\Tests\Base;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

trait DatabaseTestHelper
{
    public function clearDatabase(): void
    {
        $this->dropAndCreateDatabase();
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/_schema.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/_required_data.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/settings.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/example/listing_demo_user.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/example/listing_demo_admin.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/example/category.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/example/custom_field.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../install/data/example/page.sql'));
        $pdo->exec(\file_get_contents(__DIR__.'/../../../zz_engine/tests/Data/test_listings.sql'));
        \usleep(50);
    }

    public function dropAndCreateDatabase(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->exec('DROP DATABASE IF EXISTS classifieds_test');
        $pdo->exec('CREATE DATABASE classifieds_test');
        $pdo->exec('USE classifieds_test');
        \usleep(50);
    }

    public function getPdoConnection(): Connection
    {
        return $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
    }

    public function getEm(): EntityManager
    {
        return $this->getTestContainer()->get(EntityManagerInterface::class);
    }

    public function getConnection(): Connection
    {
        return $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
    }
}
