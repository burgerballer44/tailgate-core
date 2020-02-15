<?php

namespace Tailgate\Domain\Service;

interface ValidatableService
{
    public function validate($command);
}
