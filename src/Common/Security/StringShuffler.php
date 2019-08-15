<?php

namespace Tailgate\Common\Security;

class StringShuffler implements RandomStringInterface
{
    public function generate() : string
    {
        return substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 20);
    }
}
