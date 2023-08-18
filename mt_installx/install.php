<?php

$installComplete = false;
$dbReady = false;

if ( defined('MT_URL_HOME') )
{
    try
    {
        $installedValue = (bool) MT::getConfig()->getValue('base', 'site_installed');
        $installComplete = (bool) MT::getConfig()->getValue('base', 'install_complete');
    }
    catch ( Exception $e )
    {
        $installedValue = false;
		$installComplete = false;
    }

    $dbReady = $installedValue;
}

if ( !$installComplete || ( defined('MT_INSTALL_DEV') && MT_INSTALL_DEV ) )
{
    if ( !defined('MT_URL_HOME') )
    {
        $selfUrl = UTIL_Url::selfUrl();

        if ( substr($selfUrl, -1) != '/' )
        {
            $selfUrl .= '/';
        }

        $installPos = strpos($selfUrl, '/install');

        if ( !$installPos )
        {
            $installPos = strpos($selfUrl, '/mt_install');
        }

        if ( $installPos )
        {
            $selfUrl = substr($selfUrl, 0, $installPos) . '/';
        }

        define('MT_URL_HOME', $selfUrl);
    }

    define('INSTALL_DIR_ROOT', dirname(__FILE__) . DS);
    define('INSTALL_URL_ROOT', MT_URL_HOME . 'mt_install/');

    define('INSTALL_URL_VIEW', INSTALL_URL_ROOT . 'view/');

    define('INSTALL_DIR_CLASSES', INSTALL_DIR_ROOT . 'classes' . DS);
    define('INSTALL_DIR_BOL', INSTALL_DIR_ROOT . 'bol' . DS);
    define('INSTALL_DIR_CTRL', INSTALL_DIR_ROOT . 'controllers' . DS);
    define('INSTALL_DIR_CMP', INSTALL_DIR_ROOT . 'components' . DS);
    define('INSTALL_DIR_VIEW', INSTALL_DIR_ROOT . 'view' . DS);
    define('INSTALL_DIR_VIEW_CTRL', INSTALL_DIR_VIEW . 'controllers' . DS);
    define('INSTALL_DIR_VIEW_CMP', INSTALL_DIR_VIEW . 'components' . DS);
    define('INSTALL_DIR_FILES', INSTALL_DIR_ROOT . 'files' . DS);

    MT::getAutoloader()->addPackagePointer('INSTALL', INSTALL_DIR_CLASSES);
    MT::getAutoloader()->addPackagePointer('INSTALL_BOL', INSTALL_DIR_BOL);
    MT::getAutoloader()->addPackagePointer('INSTALL_CTRL', INSTALL_DIR_CTRL);
    MT::getAutoloader()->addPackagePointer('INSTALL_CMP', INSTALL_DIR_CMP);

    MT::getAutoloader()->addClass('INSTALL', INSTALL_DIR_CLASSES . 'install.php');

    MT::getSession()->start();

    $application = INSTALL_Application::getInstance();

    $application->init($dbReady);

    $application->display($dbReady);

    exit;
}