<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Aps_Drupal_Application extends Modules_ApsAutoprovision_Aps_Application
{
    protected function _getSettings() {
        return array_intersect_key(parent::_getSettings(), array_fill_keys([
            'admin_name',
            'admin_password',
        ], true));
    }
}
