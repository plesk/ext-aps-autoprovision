<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Config
{
    public static function getPlanItems()
    {
        return [
            'wordpress' => pm_Locale::lmsg('wordpress'),
            'drupal'    => pm_Locale::lmsg('drupal'),
            'joomla'    => pm_Locale::lmsg('joomla'),
        ];
    }
}
