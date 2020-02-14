<?php

namespace Tailgate\Infrastructure\Service\Security;

use Tailgate\Domain\Service\Security\RandomStringInterface;

class StringShuffler implements RandomStringInterface
{
    public function generate() : string
    {
        return substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 20);
    }
}
