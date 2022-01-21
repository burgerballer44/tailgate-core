<?php

namespace Tailgate\Test\Domain\Model;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvents;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\PasswordResetToken;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Test\BaseTestCase;

class UserTest extends BaseTestCase
{
    private function createUser()
    {
        return User::register($this->userId, $this->email, $this->passwordHash, $this->dateOccurred);
    }

    public function setUp() : void
    {
        $this->userId = UserId::fromString('userId');
        $this->email = Email::fromString('email@email.com');
        $this->passwordHash = 'passwordHashBlahBlah';
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
    }

    public function testUserShouldBeTheSameAfterReconstitution()
    {
        // create a user
        $user = $this->createUser();
        $events = $user->getRecordedEvents();
        $user->clearRecordedEvents();

        // recreate the user using event array
        $reconstitutedUser = User::reconstituteFromEvents(
            new AggregateHistory($this->userId, (array) $events)
        );

        // both user objects should be the same
        $this->assertEquals($user, $reconstitutedUser, 'the reconstituted user does not match the original user');
    }

    public function testRegisteringAUserStoresUserIdPasswordHashAndEmail()
    {
        $user = $this->createUser();

        $userRegisteredEvent = $user->getRecordedEvents()[0];

        // $this->assertEquals(
        //     new DomainEvents([new UserRegistered($this->userId, $this->email, $this->passwordHash, UserStatus::getPending(), UserRole::getStandard(), $this->dateOccurred)]),
        //     $user->getRecordedEvents()
        // );

        $this->assertEquals($this->userId, $userRegisteredEvent->getAggregateId());
        $this->assertEquals($this->email, $userRegisteredEvent->getEmail());
        $this->assertEquals($this->passwordHash, $userRegisteredEvent->getPasswordHash());
        $this->assertEquals($this->dateOccurred, $userRegisteredEvent->getDateOccurred());
        $this->assertEquals($this->userId, $user->getUserId());
        $this->assertEquals($this->email, $user->getEmail());
        $this->assertEquals($this->passwordHash, $user->getPasswordHash());
    }

    public function testRegisteringAUserHasPendingStatus()
    {
        $user = $this->createUser();

        $userRegisteredEvent = $user->getRecordedEvents()[0];

        $this->assertEquals(UserStatus::getPending(), $userRegisteredEvent->getStatus());
        $this->assertEquals(UserStatus::getPending(), $user->getStatus());
    }

    public function testRegisteringAUserHasStandardRole()
    {
        $user = $this->createUser();

        $userRegisteredEvent = $user->getRecordedEvents()[0];

        $this->assertEquals(UserRole::getStandard(), $userRegisteredEvent->getRole());
        $this->assertEquals(UserRole::getStandard(), $user->getRole());
    }

    public function testActivatingAUserSetsTheActiveStatus()
    {
        $user = $this->createUser();
        $user->clearRecordedEvents();

        $user->activate($this->dateOccurred);

        $userActicvatedEvent = $user->getRecordedEvents()[0];

        $this->assertEquals(UserStatus::getActive(), $userActicvatedEvent->getStatus());
        $this->assertEquals($this->dateOccurred, $userActicvatedEvent->getDateOccurred());
        $this->assertEquals(UserStatus::getActive(), $user->getStatus());
    }

    public function testAPasswordCanBeUpdated()
    {
        $user = $this->createUser();
        $user->clearRecordedEvents();

        $newPassword = 'newPassword';
        $user->updatePassword($newPassword, $this->dateOccurred);

        $passwordUpdatedEvent = $user->getRecordedEvents()[0];

        $this->assertEquals($newPassword, $passwordUpdatedEvent->getPasswordHash());
        $this->assertEquals($this->dateOccurred, $passwordUpdatedEvent->getDateOccurred());
        $this->assertNotEquals($this->passwordHash, $passwordUpdatedEvent->getPasswordHash());
        $this->assertEquals($newPassword, $user->getPasswordHash());
    }

    public function testAnEmailCanBeUpdated()
    {
        $user = $this->createUser();
        $user->clearRecordedEvents();

        $newEmail = Email::fromString('updated@email.com');
        $user->updateEmail($newEmail, $this->dateOccurred);

        $emailUpdatedEvent = $user->getRecordedEvents()[0];

        $this->assertEquals($newEmail, $emailUpdatedEvent->getEmail());
        $this->assertEquals($this->dateOccurred, $emailUpdatedEvent->getDateOccurred());
        $this->assertNotEquals($this->email, $emailUpdatedEvent->getEmail());
        $this->assertEquals($newEmail, $user->getEmail());
    }

    public function testEmailStatusAndRoleCanBeUpdated()
    {
        $user = $this->createUser();
        $user->clearRecordedEvents();

        $email = Email::fromString('updated@email.com');
        $status = UserStatus::getDeleted();
        $role = UserRole::getAdmin();
        $user->update($email, $status, $role, $this->dateOccurred);
        
        $userUpdatedEvent = $user->getRecordedEvents()[0];

        $this->assertEquals($email, $userUpdatedEvent->getEmail());
        $this->assertEquals($status, $userUpdatedEvent->getStatus());
        $this->assertEquals($role, $userUpdatedEvent->getRole());
        $this->assertEquals($this->dateOccurred, $userUpdatedEvent->getDateOccurred());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($status, $user->getStatus());
        $this->assertEquals($role, $user->getRole());
    }

    public function testAUserCanHaveApasswordResetTokenApplied()
    {
        $user = $this->createUser();
        $user->clearRecordedEvents();

        $token = PasswordResetToken::create();
        $user->applyPasswordResetToken($token, $this->dateOccurred);

        $passwordResetTokenAppliedEvent = $user->getRecordedEvents()[0];

        $this->assertEquals($token, $passwordResetTokenAppliedEvent->getPasswordResetToken());
        $this->assertEquals($this->dateOccurred, $passwordResetTokenAppliedEvent->getDateOccurred());
        $this->assertEquals($token, $user->getPasswordResetToken());
    }
}
