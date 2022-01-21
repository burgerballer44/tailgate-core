<?php

namespace Tailgate\Test\Domain\Model;

use Tailgate\Domain\Model\User\PasswordResetToken;
use Tailgate\Test\BaseTestCase;

class PasswordResetTokenTest extends BaseTestCase
{
    public function testAPasswordResetTokenCanBeCreatedInACertainFormat()
    {
        $passwordResetToken = PasswordResetToken::create();

        // randomString
        $this->assertTrue(ctype_alnum(substr($passwordResetToken, 0, PasswordResetToken::LENGTH_STRING)));
        // _
        $this->assertEquals('_', substr($passwordResetToken, PasswordResetToken::LENGTH_STRING, 1));
        // 1572211329 (the time)
        $this->assertEquals(10, strlen(substr($passwordResetToken, PasswordResetToken::LENGTH_STRING + 1)));
    }

    public function testAPasswordResetTokenIsInvalidIfOlderThanOneHour()
    {
        $passwordResetToken = 'randomString' . "_" . strtotime("1 hour 1 minute ago");
        $this->assertFalse(PasswordResetToken::isPasswordResetTokenValid($passwordResetToken));
    }

    public function testAPasswordResetTokenIsValidIfWithinOneHour()
    {
        $passwordResetToken = 'randomString' . "_" . strtotime("30 minutes ago");
        $this->assertTrue(PasswordResetToken::isPasswordResetTokenValid($passwordResetToken));
    }
}
