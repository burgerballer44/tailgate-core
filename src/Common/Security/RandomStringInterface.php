<?php

namespace Tailgate\Common\Security;

interface RandomStringInterface
{
    public function generate() : string;
}
