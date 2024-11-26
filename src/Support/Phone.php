<?php

namespace LiteView\Support;

class Phone
{
    public static function phone($val, $pattern = [])
    {
        $required = $pattern['required'] ?? false;

        if ($required && '' === strval($val)) {
            return "不能为空";
        }
        if ('' !== strval($val)) {
            if (!preg_match('/^1\d{10}$/', $val)) {
                return "不正确";
            }
        }
        return 0;
    }

}