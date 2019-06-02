<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;
use Verraes\ClassFunctions\ClassFunctions;

class User implements RecordsEvents, IsEventSourced
{
    const ROLE_USER = 10; // the average user, normal people who sign up
    const ROLE_ADMIN = 20; // an important person who can mostly do whatever
    const ROLE_DEVELOPER = 30; // more than an admin who can do whatever

    const STATUS_ACTIVE = 10; // can use the app
    const STATUS_PENDING = 20; // user who signs up but needs to confirm email
    const STATUS_INVITED = 30; // user who was invited by an admin
    const STATUS_DELETED = 99; // user who is deleted

    private $userId;
    private $username;
    private $passwordHash;
    private $email;
    private $status;
    private $role;
    private $recordedEvents = [];

    private function __construct(
        $userId,
        $username,
        $passwordHash,
        $email,
        $status,
        $role
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
    }

    public static function create(UserId $userId, $username, $passwordHash, $email)
    {
        $newUser = new User(
            $userId,
            $username,
            $passwordHash,
            $email,
            User::STATUS_PENDING,
            User::ROLE_USER
        );

        $newUser->recordThat(
            new UserSignedUp(
                $userId,
                $username,
                $passwordHash,
                $email,
                User::STATUS_PENDING,
                User::ROLE_USER
            )
        );

        return $newUser;
    }

    public function getId()
    {
        return (string) $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    private function recordThat(DomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;
    }

    public function getRecordedEvents()
    {
        return new DomainEvents($this->recordedEvents);
    }

    public function clearRecordedEvents()
    {
        $this->recordedEvents = [];
    }

    public static function reconstituteFrom(AggregateHistory $aggregateHistory)
    {
        $user = new User(
            $aggregateHistory->getAggregateId(), '', '', '', '', ''
        );

        foreach ($aggregateHistory as $event) {
            $user->apply($event);
        }

        return $user;
    }

    private function apply($anEvent)
    {
        $method = 'apply' . ClassFunctions::short($anEvent);
        $this->$method($anEvent);
    }

    private function applyUserSignedUp(UserSignedUp $event)
    {
        $this->username = $event->getUsername();
        $this->passwordHash = $event->getPasswordHash();
        $this->email = $event->getEmail();
        $this->status = $event->getStatus();
        $this->role = $event->getRole();
    }
}
