<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Aps_Application
{
    /**
     * @var pm_Domain
     */
    private $_domain = null;

    private $_name = '';

    /**
     * Application constructor
     *
     * @param string $appName
     */
    public function __construct($appName) {
        $this->_name = $appName;
    }

    public function install(pm_Domain $domain)
    {
        $result = true;
        $this->_domain = $domain;
        $package = new Modules_ApsAutoprovision_Aps_Package();
        $latestPackage = $package->retriveLastVersion($this->_name);

        if (!$package->isAvailable($latestPackage)) {
            $successfulDownload = false;
            $taskId = $package->download($latestPackage);
            if (!$taskId) {
                throw new pm_Exception(pm_Locale::lmsg('downloadError'));
            }
            $attempt = pm_Config::get('attempt');
            $timeout = pm_Config::get('timeout');
            do {
                $task = $package->getDownloadTaskStatus($taskId);
                if ('ok' === $task['status'] && !$task['in-progress'] && $task['finished']) {
                    $successfulDownload = true;
                    break;
                }
                sleep($timeout);
            } while (--$attempt);

            if (false === $successfulDownload) {
                throw new pm_Exception(pm_Locale::lmsg('downloadTaskError', ['error' => pm_Locale::lmsg('timeoutExceeded')]));
            }
        }

        return $package->install($latestPackage, $this->_getSettings(), $this->_domain->getId());
    }

    protected function _getSettings() {
        return [
            'admin_email' => ($this->_domain->getClient()->getProperty('email') ?: 'nobody@example.com'),
            'admin_password' => Modules_ApsAutoprovision_Helper::getRandomPassword(),
        ];
    }
}
