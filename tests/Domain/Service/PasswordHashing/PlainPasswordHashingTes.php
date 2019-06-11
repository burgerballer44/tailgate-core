<?php

namespace Tailgate\Test\Domain\Service\PasswordHashing;

use Tailgate\Domain\Service\PasswordHashing\PlainPasswordHashing;
use Tailgate\Tests\BaseTestCase;

class PlainPasswordHashingTest extends BaseTestCase
{
    private $password = 'password';
    private $passwordHashing;

    public function setUp()
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