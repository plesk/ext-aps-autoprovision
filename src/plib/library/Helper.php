<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Helper
{
    public static function getRandomLogin($email, $maxLength = 32, $randomLength = 8)
    {
        list($prefix) = explode('@', $email, 2);

        $args = [
            range('a', 'z'),
            range(0, 9),
        ];

        mt_srand((float)microtime()*1000000);

        // login should starts from letter, not a digit or special symbols
        $prefix = preg_replace("/^[^a-zA-Z]+|[^a-zA-Z0-9-_]+/", '', $prefix);
        $prefix = substr($prefix, 0, $maxLength - $randomLength - 1);

        $separator = (0 < strlen($prefix)) ? '_' : '';
        // add remaining symbols
        $suffix = [];
        while (count($suffix) < ($randomLength - 1)) {
            $arg = $args[mt_rand(0, count($args) - 1)];
            $suffix[] = $arg[mt_rand(0, count($arg) - 1)];
        }
        shuffle($suffix);
        // if prefix is empty, first symbol must be not numeric
        $suffix = $args[0][mt_rand(0, count($args[0]) - 1)] . implode('', $suffix);

        return $prefix . $separator . $suffix;
    }

    /**
    * Returns randomly-generated password based on strong policy
    * Password length is 10
    *
    * @return string
    */
    public static function getRandomPassword()
    {
        $patterns = [];
        $patterns[] = '1234567890';
        $patterns[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $patterns[] = 'abcdefghijklmnopqrstuvwxyz';

        $symbolsCount = pm_Config::get('password_strength') ? 5 : 3;
        $passwordString = '';
        foreach($patterns as $pattern) {
            // get 3 random symbols from every pattern
            $passwordString .= substr( str_shuffle($pattern), 0, $symbolsCount);
        }
        // add one random special character
        $passwordString .= substr( str_shuffle('!#$_'), 0, 1);

        return str_shuffle($passwordString);
    }
}
