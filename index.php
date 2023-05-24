<?php

/*
 * Meutiv Software Framework
 * Version 0.0.1
 * Copyright (c) 2023 Peatech LLC. All rights reserved.
 * Licensed under the MIT License.
 * 
 * Meutiv multi-purpose software framework is licensed under the MIT License. This open-source license grants users the freedom to use, modify, and distribute Meutiv with minimal restrictions.
 * 
 * MIT License Summary:
 * - Permission is granted to freely use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of Meutiv.
 * - The copyright notice and permission notice shall be included in all copies or substantial portions of Meutiv.
 * - Meutiv is provided "as is," without any warranty or guarantee of any kind, express or implied.
 * - Peatech LLC and the contributors to Meutiv shall not be held liable for any claim, damages, or liability arising from the use of Meutiv.
 *
 * Legal information about Meutiv:
 * - Full license text: See LICENSE.txt in the same directory.
 * - Logo policy: https://meutiv.com/attribution/
 * - Terms of use: https://meutiv.com/terms/
 */


stream_wrapper_unregister('phar');

define('_MT_', true);

define('DS', DIRECTORY_SEPARATOR);

define('MT_DIR_ROOT', dirname(__FILE__) . DS);

require_once(MT_DIR_ROOT . 'mt_includes' . DS . 'init.php');

if ( !defined('MT_ERROR_LOG_ENABLE') || (bool) MT_ERROR_LOG_ENABLE )
{
    $logFilePath = MT_DIR_LOG . 'error.log';
    $logger = MT::getLogger('mt_core_log');
    $logger->setLogWriter(new BASE_CLASS_FileLogWriter($logFilePath));
    $errorManager->setLogger($logger);
}

if ( file_exists(MT_DIR_ROOT . 'mt_install' . DS . 'install.php') )
{
    include MT_DIR_ROOT . 'mt_install' . DS . 'install.php';
}

MT::getSession()->start();

$application = MT::getApplication();

if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('before_app_init');
}

$application->init();

if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_app_init');
}

$event = new MT_Event(MT_EventManager::ON_APPLICATION_INIT);

MT::getEventManager()->trigger($event);

$application->route();

$event = new MT_Event(MT_EventManager::ON_AFTER_ROUTE);

if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_route');
}

MT::getEventManager()->trigger($event);

$application->handleRequest();

if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_controller_call');
}

$event = new MT_Event(MT_EventManager::ON_AFTER_REQUEST_HANDLE);

MT::getEventManager()->trigger($event);

$application->finalize();

if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_finalize');
}

$application->returnResponse();
