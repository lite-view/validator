<?php

namespace LiteView\Support;

class Url
{
    public static function url($val, $pattern = [])
    {
        $required = $pattern['required'] ?? false;

        if ($required && '' === strval($val)) {
            return "不能为空";
        }
        if ('' !== strval($val)) {
            if (!preg_match('/https?:\/\/.+\..+/', $val)) {
                return "不正确";
            }
        }
        return 0;
    }
}