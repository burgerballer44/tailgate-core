<?php

namespace Tailgate\Test\Infrastructure\ServicePasswordHashing;

use Tailgate\Test\BaseTestCase;
use Tailgate\Infrastructure\Service\PasswordHashing\PlainPasswordHashing;

class PlainPasswordHashingTest extends BaseTestCase
{
    private $password = 'password';
    private $passwordHashing;

    public function setUp(): void
    {
        $this->passwordHashing = new PlainPasswordHashing();
    }

    public function testItHashesByReturningTheSameThing()
    {
        $passwordHash = $this->passwordHashing->hash($this->password);
        $this->assertEquals($this->password, $passwordHash);
    }

    public function testItVerifiesByMakingSureTheyAreTheSame()
    {
        $this->assertTrue($this->passwordHashing->verify($this->password, $this->password));
    }
}
