<?php

namespace Tailgate\Test\Infrastructure\Service\PasswordHashing;

use Tailgate\Infrastructure\Service\PasswordHashing\BasicPasswordHashing;
use Tailgate\Test\BaseTestCase;

class BasicPasswordHashingTest extends BaseTestCase
{
    private $password = 'password';
    // a hash of he text 'password'
    private $passwordHash = '$2y$10$QaQC1BqV8O9N.NPeimP4yugJNDL60TIVQWb7eK24FR3dYsXQecT8u';
    private $passwordHashing;

    public function setUp(): void
    {
        $this->passwordHashing = new BasicPasswordHashing();
    }

    public function testItHashesByUsingStandardPHPStyle()
    {
        $passwordHash = $this->passwordHashing->hash($this->password);
        $this->assertNotEquals($this->password, $passwordHash);
    }

    public function testItVerifiesByUsingStandardPHPStyle()
    {
        $this->assertTrue($this->passwordHashing->verify($this->password, $this->passwordHash));
    }
}
