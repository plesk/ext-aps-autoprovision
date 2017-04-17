<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_PlanItems extends pm_Hook_PlanItems
{
    public function getPlanItems()
    {
        return Modules_ApsAutoprovision_Config::getPlanItems();
    }
}
