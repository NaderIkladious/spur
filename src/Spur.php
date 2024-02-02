<?php

namespace Naderikladious\Spur;

class Spur
{
    public static function configIsNotPublished() {
        return is_null(config('spur'));
    }
    public static function components() {
        return config('spur.components');
    }
}