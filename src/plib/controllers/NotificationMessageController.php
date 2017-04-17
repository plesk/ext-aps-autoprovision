<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class NotificationMessageController extends pm_Controller_Action
{
    public function deleteAction()
    {
        $this->_postRequestRequired();

        $messageId = $this->getParam('id');
        $domainId = (int) str_replace('domain_issue_' , '', $messageId);

        if (!is_null($messageId) && $domainId && pm_Session::getClient()->hasAccessToDomain($domainId)) {
            pm_Settings::set($messageId, null);
        }

        $options = [
            'status' => 'success',
        ];
        $this->_helper->json($options);
    }

    protected function _postRequestRequired()
    {
        if (!$this->_request->isPost()) {
            throw new pm_Exception(pm_Locale::lmsg('controllers.NotificationMessage.permissionDenied'));
        }
    }
}
