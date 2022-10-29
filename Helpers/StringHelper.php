<?php
namespace AzizbekIsmoilov\phpmvc\Helpers;
class StringHelper
{

    public static function label(string $str):string
    {
        return ucfirst(str_replace('_',' ',$str));
    }
}