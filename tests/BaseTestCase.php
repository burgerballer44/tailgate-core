<?php

namespace Tailgate\Tests;

use PHPUnit\Framework\TestCase;
use PDO;

abstract class BaseTestCase extends TestCase
{
    protected $pdo;

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
