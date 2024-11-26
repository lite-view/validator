<?php

namespace LiteView\Support;

class Date
{
    //是否是严格的 Y-m-d 格式
    public static function date($val, $pattern = [])
    {
        $regx = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
        if (!preg_match($regx, $val, $parts)) {
            return "不正确";
        }
        //检测是否为日期,checkdate 的参数为：月日年
        if (!checkdate($parts[2], $parts[3], $parts[1])) {
            return "不正确";
        }
        return 0;
    }
}