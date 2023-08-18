<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.oxwall.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Meutiv software.
 * The Initial Developer of the Original Code is Meutiv Foundation (http://www.oxwall.org/foundation).
 * All portions of the code written by Meutiv Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Meutiv Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Meutiv software framework
 * Attribution URL: http://www.oxwall.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */
/**
 * @author Nurlan Dzhumakaliev <nurlanj@live.com>
 * @package mt_cron
 * @since 1.0
 */
define('_MT_', true);

define('DS', DIRECTORY_SEPARATOR);

define('MT_DIR_ROOT', substr(dirname(__FILE__), 0, - strlen('mt_cron')));

define('MT_CRON', true);

require_once(MT_DIR_ROOT . 'mt_includes' . DS . 'init.php');

// set error log file
if ( !defined('MT_ERROR_LOG_ENABLE') || (bool) MT_ERROR_LOG_ENABLE )
{
    $logFilePath = MT_DIR_LOG . 'cron_error.log';
    $logger = MT::getLogger('mt_core_log');
    $logger->setLogWriter(new BASE_CLASS_FileLogWriter($logFilePath));
    $errorManager->setLogger($logger);
}

if ( !isset($_GET['ow-light-cron']) && !MT::getConfig()->getValue('base', 'cron_is_configured') )
{
    if ( MT::getConfig()->configExists('base', 'cron_is_configured') )
    {
        MT::getConfig()->saveConfig('base', 'cron_is_configured', 1);
    }
    else
    {
        MT::getConfig()->addConfig('base', 'cron_is_configured', 1);
    }
}

MT::getRouter()->setBaseUrl(MT_URL_HOME);

date_default_timezone_set(MT::getConfig()->getValue('base', 'site_timezone'));
MT_Auth::getInstance()->setAuthenticator(new MT_SessionAuthenticator());

MT::getPluginManager()->initPlugins();
$event = new MT_Event(MT_EventManager::ON_PLUGINS_INIT);
MT::getEventManager()->trigger($event);

//init cache manager
$beckend = MT::getEventManager()->call('base.cache_backend_init');

if ( $beckend !== null )
{
    MT::getCacheManager()->setCacheBackend($beckend);
    MT::getCacheManager()->setLifetime(3600);
    MT::getDbo()->setUseCashe(true);
}

MT::getThemeManager()->initDefaultTheme();

// setting current theme
$activeThemeName = MT::getConfig()->getValue('base', 'selectedTheme');

if ( $activeThemeName !== BOL_ThemeService::DEFAULT_THEME && MT::getThemeManager()->getThemeService()->themeExists($activeThemeName) )
{
    MT_ThemeManager::getInstance()->setCurrentTheme(BOL_ThemeService::getInstance()->getThemeObjectByKey(trim($activeThemeName)));
}

$plugins = BOL_PluginService::getInstance()->findActivePlugins();

foreach ( $plugins as $plugin )
{
    /* @var $plugin BOL_Plugin */
    $pluginRootDir = MT::getPluginManager()->getPlugin($plugin->getKey())->getRootDir();
    if ( file_exists($pluginRootDir . 'cron.php') )
    {
        include $pluginRootDir . 'cron.php';
        $className = strtoupper($plugin->getKey()) . '_Cron';
        $cron = new $className;

        $runJobs = array();
        $newRunJobDtos = array();

        foreach ( BOL_CronService::getInstance()->findJobList() as $runJob )
        {
            /* @var $runJob BOL_CronJob */
            $runJobs[$runJob->methodName] = $runJob->runStamp;
        }

        $jobs = $cron->getJobList();

        foreach ( $jobs as $job => $interval )
        {
            $methodName = $className . '::' . $job;
            $runStamp = ( isset($runJobs[$methodName]) ) ? $runJobs[$methodName] : 0;
            $currentStamp = time();
            if ( ( $currentStamp - $runStamp ) > ( $interval * 60 ) )
            {
                $runJobDto = new BOL_CronJob();
                $runJobDto->methodName = $methodName;
                $runJobDto->runStamp = $currentStamp;
                $newRunJobDtos[] = $runJobDto;

                BOL_CronService::getInstance()->batchSave($newRunJobDtos);

                $newRunJobDtos = array();

                $cron->$job();
            }
        }
    }
}
