<?php

namespace LiteView;

/*
参考:
https://github.com/zendframework/zendframework/tree/release-2.4/library/Zend/Validator

示例:
| 分割验证方法
: 指定参数
, 分割参数
$rule = [
    'name' => 'require|maxLen:25',
    'sex'  => 'enum:1,2',
    'age'  => 'nullable|number|min:0,max:200',
];
 */

class Validator
{
    protected static function parseRuleString($rule): array
    {
        if (is_string($rule)) {
            $rule = [$rule];
        }
        $__raw = $rule[0];
        $label = $rule[1] ?? '';
        $alias = $rule[2] ?? null;

        $functions = [];
        $func_arr  = explode('|', $__raw);
        foreach ($func_arr as $item) {
            $fun_args = explode(':', $item);
            $function = $fun_args[0];
            $args     = $fun_args[1] ?? null;
            if ($args) {
                $args = explode(',', $args);
            }
            $functions[] = [$function, $args];
        }

        return [$functions, $label, $alias];
    }

    public static function validate(array $data, array $rule, array $messages = [], array $default_value = [], array $custom_func = []): Validator
    {
        $validator = new self($messages, $default_value);

        foreach ($rule as $field => $item) {
            $value = $data[$field] ?? null;
            list($functions, $label, $alias) = self::parseRuleString($item);
            $validator->putData($alias ?: $field, $value);

            $pass     = true;
            $nullable = false;
            foreach ($functions as $fun) {
                list($function, $args) = $fun;
                if ('nullable' === $function) {
                    $nullable = true;
                } elseif ('required' === $function || 'require' === $function) {
                    $pass = (!is_null($value) && '' !== $value);
                } elseif (method_exists(self::class, $function) && !is_null($value)) {
                    $pass = call_user_func([self::class, $function], $value, $args);
                } else {
                    if (!method_exists(self::class, $function)) {
                        throw new \Exception("Invalid validation rule : $function");
                    }
                }

                if (is_null($value) && !$nullable) {
                    $validator->putError($field, 'can not be null', $label);
                } elseif (!$pass) {
                    $validator->putError($field, $function, $label);
                }
            }

            // 自定义方法验证
            $custom_func_arr = $custom_func[$field] ?? [];
            foreach ($custom_func_arr as $idx => $call) {
                if (!is_null($value)) {
                    $pass = call_user_func($call, $value);
                    if (!$pass) {
                        $validator->putError($field, "call_user_func_$idx", $label);
                    }
                }
            }
        }

        return $validator;
    }

    public static function enum($value, $args): bool
    {
        return in_array($value, $args);
    }

    public static function number($value): bool
    {
        return is_numeric($value);
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    public static function string($value): bool
    {
        return is_string($value);
    }

    public static function max($value, $args): bool
    {
        $limit = $args[0];
        return $value <= $limit;
    }

    public static function min($value, $args): bool
    {
        $limit = $args[0];
        return $value >= $limit;
    }

    public static function maxLen($value, $args): bool
    {
        $limit = $args[0];
        return mb_strlen($value, 'UTF-8') <= $limit;
    }

    public static function minLen($value, $args): bool
    {
        $limit = $args[0];
        return mb_strlen($value, 'UTF-8') >= $limit;
    }

    public $data;
    public $error;
    protected $messages;
    protected $default_value;

    public function __construct(array $messages, array $default_value)
    {
        $this->parseMessages($messages);
        $this->default_value = $default_value;
    }

    protected function parseMessages(array $messages)
    {
        $arr = [];
        foreach ($messages as $item => $msg) {
            list($field, $fun) = explode('.', $item);
            $arr[$field][$fun] = $msg;
        }
        $this->messages = $arr;
    }

    protected function putData($key, $value)
    {
        if (is_null($value)) {
            $value = $this->default_value[$key] ?? null;
        }
        $this->data[$key] = $value;
    }

    protected function putError($field, $function, $label)
    {
        $label         = $label ?: $field;
        $err_msg       = $this->messages[$field][$function] ?? "$function verification failed";
        $this->error[] = "$label $err_msg";
    }

    public function getData($need_null = false): array
    {
        if ($need_null) {
            return $this->data;
        }
        $data = [];
        foreach ($this->data as $field => $value) {
            if (!is_null($value)) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    public function getDataBy($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->data;
        }
        return $this->data[$key] ?? $default;
    }

    public function getDataOnly(array $fields, $need_null = false): array
    {
        $data = [];
        foreach ($fields as $field) {
            $value = $this->data[$field];
            if (is_null($value) && !$need_null) {
                continue;
            }
            $data[$field] = $value;
        }
        return $data;
    }

    public function getDataExcept(array $fields, $need_null = false): array
    {
        $fields = array_diff(array_keys($this->data), $fields);
        return $this->getDataOnly($fields, $need_null);
    }
}
