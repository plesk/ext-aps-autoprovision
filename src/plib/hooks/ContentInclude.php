<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_ContentInclude extends pm_Hook_ContentInclude
{
    private $_showMessages = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if (is_null($request)) {
            return;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        switch ("{$module}/{$controller}/${action}") {
            case 'smb//':
            case 'smb/web/view':
                // "Websites & Domains" page for customer and power user of current subscription
                $this->_showMessages = true;
                break;
            default:
                return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJsConfig()
    {
        $messages = [];
        $locale = [];

        if ($this->_showMessages) {
            $domains = pm_Session::getCurrentDomains();
            foreach ($domains as $domain) {
                $messages = array_merge($messages, $this->_getDomainMessages($domain->getId()));
            }
            $locale = pm_Locale::getSection('js');
        }

        return [
            'messages' => $messages,
            'locale' => $locale,
        ];
    }

    /**
     * @param int $domainId
     * @return array
     */
    private function _getDomainMessages($domainId)
    {
        $messages = [];
        if ($message = pm_Settings::get('domain_issue_' . $domainId)) {
            $messages[] = [
                'type' => 'error',
                'message' => $message,
                'attrs' => [
                    'data-id' => 'domain_issue_' . $domainId,
                ],
            ];
        }
        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsOnReadyContent()
    {
        if (!$this->_showMessages) {
            return '';
        }
        $deleteMessageActionUrl = pm_Context::getActionUrl('notification-message', 'delete');

        $js = <<<JS
    // Add APS Autoprovision messages in reverse order because they are prepended.
    var messages = PleskExt.ApsAutoprovision.Config.messages;
    var locale = new Jsw.Locale(PleskExt.ApsAutoprovision.Config.locale);
    for (var i = messages.length - 1; i >= 0; --i) {
        var message = messages[i];

        if (!message.title) {
            switch (message.type) {
            case 'info':
                message.title = locale.lmsg('message.title.info');
                break;
            case 'warning':
                message.title = locale.lmsg('message.title.warning');
                break;
            case 'error':
                message.title = locale.lmsg('message.title.error');
                break;
            }
        }
        message.closable = true;
        message.onClose = closeMessage;
        new Jsw.StatusMessage(message);
    }

    function closeMessage() {
        this.hide();

        new Ajax.Request("{$deleteMessageActionUrl}", {
            method: 'POST',
            parameters: {
                id: this._attrs['data-id']
            }
        });
    }
JS;
        return $js;
    }
}
