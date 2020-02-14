<?php

namespace Tailgate\Domain\Service\Security;

interface RandomStringInterface
{
    public function generate() : string;
}
