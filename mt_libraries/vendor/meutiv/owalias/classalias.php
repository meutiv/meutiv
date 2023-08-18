<?php

// class aliases
class_alias("MT", "OW");
class_alias("MT_Route", "OW_Route");
class_alias("MT_Router", "OW_Router");
class_alias("MT_Autoload", "OW_Autoload");
class_alias("MT_BaseDao", "OW_BaseDao");
class_alias("MT_Entity", "OW_Entity");
class_alias("MT_EventManager", "OW_EventManager");
class_alias("MT_Event", "OW_Event");
class_alias("MT_ActionController", "OW_ActionController");
class_alias("MT_AjaxDocument", "OW_AjaxDocument");
class_alias("MT_ApiActionController", "OW_ApiActionController");
class_alias("MT_ApiApplication", "OW_ApiApplication");
class_alias("MT_ApiDefaultRoute", "OW_ApiDefaultRoute");
class_alias("MT_ApiDocument", "OW_ApiDocument");
class_alias("MT_ApiRequestHandler", "OW_ApiRequestHandler");
class_alias("MT_Application", "OW_Application");
class_alias("MT_AuthAdapter", "OW_AuthAdapter");
class_alias("MT_AuthResult", "OW_AuthResult");
class_alias("MT_Auth", "OW_Auth");
class_alias("MT_Authorization", "OW_Authorization");
class_alias("MT_BillingAdapter", "OW_BillingAdapter");
class_alias("MT_BillingProductAdapter", "OW_BillingProductAdapter");
class_alias("MT_CacheManager", "OW_CacheManager");
class_alias("MT_CacheService", "OW_CacheService");
class_alias("MT_CliApplication", "OW_CliApplication");
class_alias("MT_Component", "OW_Component");
class_alias("MT_Config", "OW_Config");
class_alias("MT_Cron", "OW_Cron");
class_alias("MT_Database", "OW_Database");
class_alias("MT_DefaultRoute", "OW_DefaultRoute");
class_alias("MT_DeveloperTools", "OW_DeveloperTools");
class_alias("MT_Dispatcher", "OW_Dispatcher");
class_alias("MT_Document", "OW_Document");
class_alias("MT_ErrorManager", "OW_ErrorManager");
class_alias("MT_Example", "OW_Example");
class_alias("MT_Feedback", "OW_Feedback");
class_alias("MT_IFilter", "OW_IFilter");
class_alias("MT_HtmlDocument", "OW_HtmlDocument");
class_alias("MT_IAuthenticator", "OW_IAuthenticator");
class_alias("MT_ICacheBackend", "OW_ICacheBackend");
class_alias("MT_Language", "OW_Language");
class_alias("MT_LogWriter", "OW_LogWriter");
class_alias("MT_Log", "OW_Log");
class_alias("MT_Mailer", "OW_Mailer");
class_alias("MT_MasterPage", "OW_MasterPage");
class_alias("MT_MobileActionController", "OW_MobileActionController");
class_alias("MT_MobileApplication", "OW_MobileApplication");
class_alias("MT_MobileComponent", "OW_MobileComponent");
class_alias("MT_MobileMasterPage", "OW_MobileMasterPage");
class_alias("MT_Navigation", "OW_Navigation");
class_alias("MT_PluginManager", "OW_PluginManager");
class_alias("MT_Plugin", "OW_Plugin");
class_alias("MT_Registry", "OW_Registry");
class_alias("MT_RemoteAuthAdapter", "OW_RemoteAuthAdapter");
class_alias("MT_Renderable", "OW_Renderable");
class_alias("MT_RequestHandler", "OW_RequestHandler");
class_alias("MT_Request", "OW_Request");
class_alias("MT_Response", "OW_Response");
class_alias("MT_SessionAuthenticator", "OW_SessionAuthenticator");
class_alias("MT_Session", "OW_Session");
class_alias("MT_Singleton", "OW_Singleton");
class_alias("MT_Smarty", "OW_Smarty");
class_alias("MT_Storage", "OW_Storage");
class_alias("MT_TextSearchManager", "OW_TextSearchManager");
class_alias("MT_Theme", "OW_Theme");
class_alias("MT_TokenAuthenticator", "OW_TokenAuthenticator");
class_alias("MT_User", "OW_User");
class_alias("MT_Validator", "OW_Validator");
class_alias("MT_ViewRenderer", "OW_ViewRenderer");
class_alias("MT_View", "OW_View");

/* 
$constants = 'URL_HOME, DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME, DB_PREFIX, DIR_STATIC,URL_STATIC,DIR_USERFILES,URL_USERFILES,DIR_PLUGINFILES,PASSWORD_SALT,DIR_CORE,DIR_INC,DIR_LIB,DIR_UTIL,DIR_SMARTY,DIR_PLUGI,DIR_THEME,DIR_SYSTEM_PLUGIN,CRON,DEBUG_MODE,DEV_MODE,PROFILER_ENABLE, DIR_STATIC_PLUGIN, DIR_STATIC_THEME, DIR_PLUGIN_USERFILES, DIR_THEME_USERFILES, DIR_LOG, URL_STATIC_THEMES,URL_STATIC_PLUGINS,URL_PLUGIN_USERFILES,URL_THEME_USERFILES,DIR_LIB_VENDOR,SQL_LIMIT_USERS_COUNT';

foreach (explode(',', $constants) as $name) {
    if (defined("MT_{$name}")) {
        define("OW_{$name}", constant("MT_{$name}"));
    }
}
 */

if( defined('MT_URL_HOME') )
    define('OW_URL_HOME', MT_URL_HOME);

if( defined('MT_DB_HOST') )
    define('OW_DB_HOST', MT_DB_HOST);

if( defined('MT_DB_PORT') )
    define('OW_DB_PORT', MT_DB_PORT);

if( defined('MT_DB_USER') )
    define('OW_DB_USER', MT_DB_USER);

if( defined('MT_DB_PASSWORD') )
    define('OW_DB_PASSWORD', MT_DB_PASSWORD);

if( defined('MT_DB_NAME') )
    define('OW_DB_NAME', MT_DB_NAME);

if( defined('MT_DB_PREFIX') )
    define('OW_DB_PREFIX', MT_DB_PREFIX);
