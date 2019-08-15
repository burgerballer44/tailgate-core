<?php

namespace Tailgate\Test\Common\PasswordHashing;

use PHPUnit\Framework\TestCase;
use Tailgate\Common\PasswordHashing\PlainPasswordHashing;

class PlainPasswordHashingTest extends TestCase
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
