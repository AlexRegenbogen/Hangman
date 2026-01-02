<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusInformation: string
{
    case BUSY = 'busy';
    case FAIL = 'fail';
    case SUCCESS = 'success';
}
