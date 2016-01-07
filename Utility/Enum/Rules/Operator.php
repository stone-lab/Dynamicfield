<?php

namespace Modules\Dynamicfield\Utility\Enum\Rules;

use Modules\Dynamicfield\Utility\Enum\BasicEnum;

abstract class Operator extends BasicEnum
{
    const EQUAL      = 'is equal to';
    const NOTEQUAL  = 'is not equal to';
}
