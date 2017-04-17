<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_List_Applications extends pm_View_List_Simple
{
    /**
     *
     * @param Zend_View $view
     * @param Zend_Controller_Request_Abstract $request
     * @param array $options
     */
    public function __construct(Zend_View $view, Zend_Controller_Request_Abstract $request, array $options = [])
    {
        parent::__construct($view, $request, [
            'defaultSortField' => 'plan',
            'defaultSortDirection' => pm_View_List_Simple::SORT_DIR_DOWN,
        ]);

        $data = [];
        $request = '<service-plan><get><filter/></get></service-plan>';
        $ids = pm_ApiRpc::getService()->call($request)->xpath('/packet/service-plan/get/result/id');

        foreach ($ids as $id) {
            $application = '';
            $plan = new pm_Plan((int) $id[0]);

            $planName = $view->escape($plan->getName());
            if ('Admin Simple' == $planName) {
                continue;
            }

            $planItems = $plan->getPlanItems();

            if (is_array($planItems) && count($planItems) > 0 && isset(Modules_ApsAutoprovision_Config::getPlanItems()[$planItems[0]])) {
                $application = pm_Locale::lmsg($planItems[0]);
            }

            $data[] = [
               'plan' => '<a href="/admin/customer-service-plan/edit/id/' . $plan->getId() . '">' . $planName . '</a>',
               'application' => $application
            ];
        }

        $this->setData($data);
        $this->setDataUrl(['action' => 'application-list-data']);

        $this->setColumns([
            'plan' => [
                'title' => $this->lmsg('columnTitlePlan'),
                'noEscape' => true,
                'searchable' => true,
                'sortable' => true,
            ],
            'application' => [
                'title' => $this->lmsg('columnTitleApplication'),
                'searchable' => true,
                'sortable' => true,
            ],
        ]);
    }
}
