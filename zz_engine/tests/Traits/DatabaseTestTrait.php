<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

trait DatabaseTestTrait
{
    public function clearDatabase(): void
    {
        $this->dropAndCreateDatabase();
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/_schema.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/_required_data.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/settings.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/example/listing_demo_user.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/example/listing_demo_admin.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/example/category.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/example/custom_field.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../install/data/example/page.sql'));
        $this->executeSql(file_get_contents(__DIR__.'/../../../zz_engine/tests/Data/test_database.sql'));
        usleep(50);
    }

    public function dropAndCreateDatabase(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->exec('DROP DATABASE IF EXISTS classifieds_test');
        $pdo->exec('CREATE DATABASE classifieds_test');
        $pdo->exec('USE classifieds_test');
        usleep(50);
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

    public function executeSql(string $sql): void
    {
        /** @var \Pdo $pdo */
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection()->getWrappedConnection()->getWrappedConnection();
        $result = $pdo->exec($sql);
        if (false === $result || '00000' !== $pdo->errorCode()) {
            throw new \RuntimeException("error while executing SQL: \n".print_r($pdo->errorInfo(), true)."\n".$sql);
        }
    }
}
