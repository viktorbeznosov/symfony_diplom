<?php

namespace App\Helpers;


class JsonHelper
{

    public static function jsonIsValid($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

}