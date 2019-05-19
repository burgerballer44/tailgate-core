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
    private $userId;
    private $username;
    private $password;
    private $email;
    private $recordedEvents = [];

    private function __construct($userId, $username, $password, $email)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

    public static function create(UserId $userId, $username, $password, $email)
    {
        $newUser = new User($userId, $username, $password, $email);

        $newUser->recordThat(
            new UserSignedUp($userId, $username, $password, $email)
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

    public function getPassword()
    {
        return $this->password;
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
        $user = new User($aggregateHistory->getAggregateId(), '', '', '');

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
        $this->password = $event->getPassword();
        $this->email = $event->getEmail();
    }
}
