<?php

namespace Validator;

class SequenceOfNumbers
{
    static function isValid($value)
    {
        $value = '1234B';

        die(dump($value));

        return false;
    }
}
