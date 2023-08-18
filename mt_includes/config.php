<?php

define('MT_URL_HOME', 'http://meutiv.test/');

define('MT_DB_HOST', 'localhost');
define('MT_DB_PORT', null);
define('MT_DB_USER', 'root');
define('MT_DB_PASSWORD', 'passw0rd');
define('MT_DB_NAME', 'meutiv');

define('MT_DB_PREFIX', 'mt_');

define('MT_DIR_STATIC', MT_DIR_ROOT. 'mt_static'.DS);
define('MT_URL_STATIC', MT_URL_HOME. 'mt_static/');

define('MT_DIR_UPLOADS', MT_DIR_ROOT . 'mt_uploads'.DS);
define('MT_URL_UPLOADS', MT_URL_HOME . 'mt_uploads/');
define('MT_DIR_USERFILES', MT_DIR_UPLOADS . 'user'.DS);
define('MT_URL_USERFILES', MT_URL_UPLOADS . 'user/');
define('MT_DIR_PLUGINFILES', MT_DIR_UPLOADS . 'system/');

define('MT_PASSWORD_SALT', '7yLYZEd2WItESyli');

define('MT_DIR_CORE', MT_DIR_ROOT . 'mt_core' . DS);
define('MT_DIR_INC', MT_DIR_ROOT . 'mt_includes' . DS);
define('MT_DIR_LIB', MT_DIR_ROOT . 'mt_libraries' . DS);
define('MT_DIR_UTIL', MT_DIR_ROOT . 'mt_utilities' . DS);
define('MT_DIR_SMARTY', MT_DIR_ROOT . 'mt_smarty' . DS);

define('MT_DIR_PACKAGE', MT_DIR_ROOT . 'mt_packages' . DS);
define('MT_DIR_PLUGIN', MT_DIR_PACKAGE . 'plugins' . DS);
define('MT_DIR_THEME', MT_DIR_PACKAGE . 'themes' . DS);
define('MT_DIR_SYSTEM_PLUGIN', MT_DIR_ROOT . 'mt_system_plugins'.DS);

define('MT_USE_CLOUDFILES', false);

if ( defined('MT_CRON') )
{
    define('MT_DEBUG_MODE', false);
    define('MT_DEV_MODE', false);
    define('MT_PROFILER_ENABLE', false);
}
else
{
    /**
    * Make changes in this block if you want to enable DEV mode and DEBUG mode
    */

    define('MT_DEBUG_MODE', true);
    define('MT_DEV_MODE', true);
    define('MT_PROFILER_ENABLE', true);
}
