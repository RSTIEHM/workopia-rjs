<?php

namespace Framework;

class Validation
{
    // VALIDATE A STRING - SET MIN AND MAX (int) return bool
    public static function string($value, $min = 1, $max = INF)
    {
        if (is_string($value)) {
            $value = trim($value);
            $length = strlen($value);
            return $length >= $min && $length <= $max;
        }
        return false;
    }

    // VALIDATE EMAIL  ===========================
    public static function email($value)
    {
        $value = trim($value);
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    // MATCH VALUE AGAINST ANOTHER RETURN BOOL
    public static function match($v1, $v2)
    {
        $v1 = trim($v1);
        $v2 = trim($v2);
        return $v1 === $v2;
    }
}
