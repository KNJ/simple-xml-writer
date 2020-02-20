<?php

namespace Wazly\SimpleXmlWriter;

class Filter
{
    public static function htmlspecialchars(string $value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true);
    }
}
