<?php

namespace Tests\JobBoy\Process\Domain\Repository\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\Configuration as MigrationConfiguration;
use Doctrine\DBAL\Migrations\Migration;
use JobBoy\Process\Domain\Entity\Factory\ProcessFactory;
use JobBoy\Process\Domain\Entity\Infrastructure\Doctrine\Migrations\Version20200101000000_create_process_table;
use JobBoy\Process\Domain\Entity\Infrastructure\TouchCallback\HydratableProcess;
use JobBoy\Process\Domain\Repository\Infrastructure\Doctrine\ProcessRepository;
use JobBoy\Process\Domain\Repository\ProcessRepositoryInterface;
use JobBoy\Process\Domain\Repository\Test\ProcessRepositoryInterfaceTest;

class ProcessRepositoryWithMigrationsTest extends ProcessRepositoryInterfaceTest
{
    protected $processFactory;


    protected function createRepository(): ProcessRepositoryInterface
    {
        $processFactory = $this->createFactory();

        $config = new Configuration();
        $params = array(
            'url' => 'mysql://root:root@db',
        );

        $connection = DriverManager::getConnection($params, $config);
        $connection->getSchemaManager()->dropAndCreateDatabase('jobboy_test');

        $params = array(
            'url' => 'mysql://root:root@db/jobboy_test',
        );

        $connection = DriverManager::getConnection($params, $config);

        $migrationConfig = new MigrationConfiguration($connection);
        $ref = new \ReflectionClass(Version20200101000000_create_process_table::class);
        $migrationDirectory = dirname($ref->getFileName());
        $migrationConfig->setMigrationsDirectory($migrationDirectory);
        $migrationConfig->setMigrationsNamespace($ref->getNamespaceName());

        $migration = new Migration($migrationConfig);
        $migration->migrate();

        return new ProcessRepository($processFactory, $connection);
    }

    protected function createFactory(): ProcessFactory
    {
        if (!$this->processFactory) {
            $this->processFactory = new ProcessFactory(HydratableProcess::class);
        }

        return $this->processFactory;
    }


}
