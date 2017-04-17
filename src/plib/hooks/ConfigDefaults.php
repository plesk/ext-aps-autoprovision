<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_ConfigDefaults extends pm_Hook_ConfigDefaults
{
 
    public function getDefaults()
    {
        return [
            'attempt' => 60,
            'timeout' => 15,
            'password_strength' => false,
        ];
    }
}
