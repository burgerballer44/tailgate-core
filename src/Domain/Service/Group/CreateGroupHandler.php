<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Common\Security\RandomStringInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\AbstractService;

class CreateGroupHandler extends AbstractService
{
    public $groupRepository;
    private $randomStringer;

    public function __construct(
        ValidatorInterface $validator,
        GroupRepositoryInterface $groupRepository,
        RandomStringInterface $randomStringer
    ) {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
        $this->randomStringer = $randomStringer;
    }

    public function handle(CreateGroupCommand $command)
    {
        $this->validate($command);

        $name = $command->getName();
        $ownerId = $command->getOwnerId();

        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $name,
            $this->randomStringer->generate(),
            UserId::fromString($ownerId)
        );
        
        $this->groupRepository->add($group);
    }
}
