<?php

namespace LiteView\Support;

class Ascii
{
    //ASCII
    public static function ascii($val, $pattern = [])
    {
        if (strlen($val) !== mb_strlen($val)) {
            return "不正确";
        }
        return 0;
    }
}