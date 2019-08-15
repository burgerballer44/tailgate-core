<?php

namespace Tailgate\Application\Validator\User;

use Respect\Validation\Validator as V;
use Respect\Validation\Rules\AbstractRule;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UniqueUsername extends AbstractRule
{
    private $userViewRepository;

    public function __construct(UserViewRepositoryInterface $userViewRepository)
    {
        $this->userViewRepository = $userViewRepository;
    }

    /**
     * returns false when the user exists by the username
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function validate($input)
    {
        $user = $this->userViewRepository->byUsername($input);

        return false == $user;
    }
}
