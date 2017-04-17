<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class IndexController extends pm_Controller_Action
{
    protected $_accessLevel = ['admin'];

    public function indexAction()
    {
        $this->_redirect('index/application-list');
    }

    public function applicationListAction()
    {
        $this->view->pageTitle = $this->lmsg('pageTitleApplicationList');
        $this->view->applicationList = new Modules_ApsAutoprovision_List_Applications($this->view, $this->_request);
    }

    public function applicationListDataAction()
    {
        $applicationList = new Modules_ApsAutoprovision_List_Applications($this->view, $this->_request);
        $this->_helper->json($applicationList->fetchData());
    }
}
