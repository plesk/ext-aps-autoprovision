<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_ApsAutoprovision_Aps_Package
{
    private $_apiUser = 'admin';

    public function install($package, $settings, $domainId)
    {
        $result = true;

        if ($settings) {
            $requestSettings = '<settings>';
            foreach ($settings as $name => $value) {
                $requestSettings .= "<setting><name>$name</name><value>$value</value></setting>";
            }
            $requestSettings .= '</settings>';
        }

        $request = <<<REQUEST
<aps>
    <install>
        <domain-id>$domainId</domain-id>
        <package>
            <name>{$package['name']}</name>
            <version>{$package['version']}</version>
        </package>
        $requestSettings
    </install>
</aps>
REQUEST;

        $response = pm_ApiRpc::getService()->call($request, $this->_apiUser);

        if ('ok' != $response->aps->install->result->status) {
            throw new pm_Exception(pm_Locale::lmsg('installationError', ['error' => $response->aps->install->result->errtext]));
        }
        return $result;

    }

    public function retriveLastVersion($packageName)
    {
        $packages = $this->_getAllPackages();
        $package = $packages->xpath("/packet/aps/get-packages-list/result/package/name[translate(.,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz') = '$packageName']/parent::*");

        return (array) $package[0];
    }

    private function _getAllPackages()
    {
        $request = <<<REQUEST
<aps>
    <get-packages-list>
        <all/>
    </get-packages-list>
</aps>
REQUEST;
        $response = pm_ApiRpc::getService()->call($request, $this->_apiUser);
        if ('ok' != $response->aps->{'get-packages-list'}->result->status) {
            throw new pm_Exception(
                pm_Locale::lmsg('packageListError', ['error' => $response->aps->{'get-packages-list'}->result->errtext])
            );
        }

        return $response->aps->{'get-packages-list'}->result;
    }

    private function _getAvailablePackages()
    {
        $request = <<<REQUEST
<aps>
    <get-packages-list>
        <filter/>
    </get-packages-list>
</aps>
REQUEST;

        return pm_ApiRpc::getService()->call($request, $this->_apiUser);
    }

    public function isAvailable($package)
    {
        $packages = $this->_getAvailablePackages();
        $query = "/packet/aps/get-packages-list/result/package[name = '{$package['name']}' and version = '{$package['version']}' and release='{$package['release']}']";
        $result = $packages->xpath($query);

        if (is_array($result) && isset($result[0])) {
            return true;
        } else {
            return false;
        }
    }

    public function download($package)
    {
        $request = <<<REQUEST
<aps>
    <download-package>
        <package>
            <name>{$package['name']}</name>
            <version>{$package['version']}</version>
        </package>
    </download-package>
</aps>
REQUEST;

        $response = pm_ApiRpc::getService()->call($request, $this->_apiUser);

        if ('ok' != $response->aps->{'download-package'}->result->status) {
            throw new pm_Exception(pm_Locale::lmsg('detailedDownloadError', ['error' => $response->aps->{'download-package'}->result->errtext]));
        }

        return $response->aps->{'download-package'}->result->{'task-id'};
    }

    public function getDownloadTaskStatus($taskId)
    {
        $result = ['in-progress' => false, 'finished' => false];
        $request = <<<REQUEST
<aps>
    <get-download-status>
        <filter>
            <task-id>$taskId</task-id>
        </filter>
    </get-download-status>
</aps>
REQUEST;

        $response = pm_ApiRpc::getService()->call($request, $this->_apiUser);

        if ('ok' == $response->aps->{'get-download-status'}->result->status) {
            $result['status'] = 'ok';
            if ($response->xpath('/packet/aps/get-download-status/result/task/in-progress')) {
                $result['in-progress'] = true;
            } elseif ($response->xpath('/packet/aps/get-download-status/result/task/finished')) {
                if (!$response->xpath('/packet/aps/get-download-status/result/task/finished/package-id')) {
                    throw new pm_Exception(
                        pm_Locale::lmsg('downloadTaskError', ['error' => $response->aps->{'get-download-status'}->result->task->finished->error])
                    );
                }

                $result['finished'] = true;
            }
        } else {
            throw new pm_Exception(
                pm_Locale::lmsg('downloadTaskError', ['error' => $response->aps->{'get-download-status'}->result->errtext])
            );
        }

        return $result;
    }
}
