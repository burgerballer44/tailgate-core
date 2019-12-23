<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Service\AbstractService;

class DeletePlayerHandler extends AbstractService
{
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeletePlayerCommand $command)
    {
        // $this->validate($command);

        $groupId = $command->getGroupId();
        $PlayerId = $command->getPlayerId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deletePlayer(PlayerId::fromString($PlayerId));

        $this->groupRepository->add($group);
    }
}
