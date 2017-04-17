<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Helper
{
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
