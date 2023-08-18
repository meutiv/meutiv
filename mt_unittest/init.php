<?php

define('_MT_', true);
define('DS', DIRECTORY_SEPARATOR);
define('MT_DIR_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('MT_CRON', true);

require_once(MT_DIR_ROOT . 'mt_includes' . DS . 'init.php');

MT::getRouter()->setBaseUrl(MT_URL_HOME);

date_default_timezone_set(MT::getConfig()->getValue('base', 'site_timezone'));
MT_Auth::getInstance()->setAuthenticator(new MT_SessionAuthenticator());

MT::getPluginManager()->initPlugins();
$event = new MT_Event(MT_EventManager::ON_PLUGINS_INIT);
MT::getEventManager()->trigger($event);

MT::getThemeManager()->initDefaultTheme();

// setting current theme
$activeThemeName = MT::getConfig()->getValue('base', 'selectedTheme');

if ( $activeThemeName !== BOL_ThemeService::DEFAULT_THEME && MT::getThemeManager()->getThemeService()->themeExists($activeThemeName) )
{
    MT_ThemeManager::getInstance()->setCurrentTheme(BOL_ThemeService::getInstance()->getThemeObjectByKey(trim($activeThemeName)));
}