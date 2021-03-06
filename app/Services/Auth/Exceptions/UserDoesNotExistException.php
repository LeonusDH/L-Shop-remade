<?php
declare(strict_types = 1);

namespace app\Services\Auth\Exceptions;

use app\Exceptions\DomainException;

class UserDoesNotExistException extends DomainException
{
    public function __construct($criteria = null)
    {
        if (!empty($criteria)) {
            $criteria = "User [{$criteria}] does not exist";
        }

        parent::__construct($criteria, 0, null);
    }
}
