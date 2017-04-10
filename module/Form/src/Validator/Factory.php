<?php
/**
 * Factory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form\Validator;


class Factory
{

    public static function NotEmpty()
    {
        return [
            'name' => 'NotEmpty',
            'break_chain_on_failure' => true
        ];
    }

    public static function EmailAddress()
    {
        return [
            'name' => 'EmailAddress',
            'break_chain_on_failure' => true,
            'options' => [
                'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                'useMxCheck' => false,
            ],
        ];
    }


    public static function StringLength($min = 1, $max = 1024)
    {
        return [
            'name'    => 'StringLength',
            'break_chain_on_failure' => true,
            'options' => [
                'min' => (int)$min,
                'max' => (int)$max,
            ],
        ];
    }


    public static function Regex($match)
    {
        return [
            'name'    => 'Regex',
            'break_chain_on_failure' => true,
            'options' => [
                'pattern' => $match, //"/^wx[0-9a-z]+$/",
            ],
        ];
    }


    public static function CaptchaImage()
    {
        return [
            'name' => \Zend\Captcha\Image::class,
        ];
    }


    public static function Identical($token)
    {
        return [
            'name'    => 'identical',
            'options' => [
                'token' => $token,
            ],
        ];
    }


}