<?php

namespace Tailgate\Tests;

use PHPUnit\Framework\TestCase;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use PDO;

abstract class DatabaseTestCase extends TestCase
{
    protected $phinxWrapper;
    protected $pdo;

    public function setUp()
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput(' '), new NullOutput());

        $this->phinxWrapper = new TextWrapper($app);
        $this->phinxWrapper->getMigrate("testing");
    }

    public function tearDown()
    {
        $this->phinxWrapper->getRollback("testing", 0);
    }

    protected function createPDOConnection()
    {
        $connection = getenv('DB_CONNECTION');
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $name = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            $this->pdo = new PDO("{$connection}:host={$host};port={$port};dbname={$name};charset=utf8mb4", "{$user}", "{$password}", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

        } catch (\Throwable $e) {
            $this->markTestSkipped('PDO connection failed');
        }
    }
}