<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

$messages = [
    'wordpress' => 'WordPress',
    'drupal' => 'Drupal',
    'joomla' => 'Joomla',

    'autoprovisionError' => 'Installing package %%package%% on domain %%domain%% has failed. %%error%%',
    'installationError' => 'The package could not be installed: %%error%%',
    'downloadError' => 'The package could not be downloaded from the APS catalog.',
    'detailedDownloadError' => 'Could not start downloading the package from the APS catalog: %%error%%',
    'downloadTaskError' => 'The package could not be downloaded from the APS catalog: %%error%%',
    'packageListError' => 'Could not retrieve the list of applications from the APS catalog and applications installed on the server: %%error%%',
    'timeoutExceeded' => 'The timeout for package download was exceeded.',

    // Controllers messages
    'controllers.NotificationMessage.permissionDenied' => 'Permission denied',

    // JS messages
    'js' => [
        'message.title.info' => 'Information',
        'message.title.warning' => 'Warning',
        'message.title.error' => 'Error',
    ],

    'pageTitleApplicationList' => 'Application Auto-Provision',
    'pageDescriptionApplicationList' => 'List of all Service Plans on the server with the applications selected for automatic installation in each plan.',
    'columnTitlePlan' => 'Plan Name',
    'columnTitleApplication' => 'Application',

];
