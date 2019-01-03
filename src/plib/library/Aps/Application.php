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
        $client = $this->_domain->getClient();
        return [
            'admin_email' => $client->getProperty('email') ?: 'nobody@example.com',
            'admin_name' => Modules_ApsAutoprovision_Helper::getRandomLogin($client->getProperty('email') ?: $client->getLogin()),
            'admin_password' => Modules_ApsAutoprovision_Helper::getRandomPassword(),
        ];
    }
}
