<?php

namespace Validator;

class SequenceOfNumbers
{
    static function isValid(array $value)
    {
        if ($value[0] === ($value[1] - 1) &&
            $value[1] === ($value[2] - 1)) {
            return false;
        }

        if ($value[1] === ($value[2] - 1) &&
            $value[2] === ($value[3] - 1)) {
            return false;
        }

        if ($value[2] === ($value[3] - 1) &&
            $value[3] === ($value[4] - 1)) {
            return false;
        }

        return true;
    }
}
