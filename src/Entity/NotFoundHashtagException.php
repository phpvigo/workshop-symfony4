<?php

namespace App\Entity;

use Throwable;

class NotFoundHashtagException extends \DomainException
{
    public function __construct($hashtag = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("No hashtag for " . $hashtag, $code, $previous);
    }
}
