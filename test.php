<?php

use LiteView\Validator;

require_once "vendor/autoload.php";


$rule = [
    'title'      => ['require|string', 'title'],
    'desc'       => ['nullable|string', 'desc'],
    'icon'       => ['nullable|string', 'icon'],
    'low_color'  => ['require|string', 'low_color'],
    'high_color' => ['require|string', 'high_color'],
    'fast_range' => ['require|number|min:0|max:7', 'fast_range'],
];

$messages = [
    'fast_range.null'    => '不能为null',
    'fast_range.require' => '不能为空',
    'fast_range.number'  => '必须为数字',
];

$r = Validator::validate(['fast_range' => 1], $rule, $messages);
var_dump($r->data);
var_dump($r->error);