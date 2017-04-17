<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Aps_Drupal_Application extends Modules_ApsAutoprovision_Aps_Application
{
    protected function _getSettings() {
        return [
            'admin_password' => Modules_ApsAutoprovision_Helper::getRandomPassword(),
        ];
    }
}
