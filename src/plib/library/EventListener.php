<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_EventListener implements EventListener
{
    public function handleEvent($objectType, $objectId, $action, $oldValue, $newValue)
    {
        if ($action == 'phys_hosting_create') {
            $domain = new pm_Domain($objectId);
            $planItems = $domain->getPlanItems();
            if (is_array($planItems) && count($planItems) > 0 && isset(Modules_ApsAutoprovision_Config::getPlanItems()[$planItems[0]])) {
                $className = 'Modules_ApsAutoprovision_Aps_' . ucfirst($planItems[0]) . '_Application';
                $application = class_exists($className) ? new $className($planItems[0]) : new Modules_ApsAutoprovision_Aps_Application($planItems[0]);
                try {
                    $application->install($domain);
                } catch (pm_Exception $e) {
                    pm_Settings::set(
                        'domain_issue_' . $objectId,
                        pm_Locale::lmsg(
                            'autoprovisionError',
                            ['domain' => $domain->getDisplayName(), 'package' => $planItems[0], 'error' => $e->getMessage()]
                        )
                    );

                }
            }
        } elseif ($action == 'domain_delete' && pm_Settings::get('domain_issue_' . $objectId)) {
            pm_Settings::set('domain_issue_' . $objectId, null);
        }
    }
}
 
return new Modules_ApsAutoprovision_EventListener();
