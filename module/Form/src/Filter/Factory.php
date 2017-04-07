<?php
/**
 * Factory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form\Filter;


class Factory
{

    public static function StringTrim()
    {
        return ['name' => 'StringTrim'];
    }

    public static function StripTags()
    {
        return ['name' => 'StripTags'];
    }

    public static function StripNewlines()
    {
        return ['name' => 'StripNewlines'];
    }

    public static function StringToLower()
    {
        return [
            'name' => 'StringToLower',
            'options' => [
                'encoding' => 'UTF-8',
            ],
        ];
    }
}