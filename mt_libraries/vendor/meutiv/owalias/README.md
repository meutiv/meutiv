PHP Class and Constant Aliasing Documentation
==============================================

This script provides class and constant aliases to match Meutiv core classes and constants to Oxwall core classes and constants using PHP aliases. The purpose of this script is to allow Oxwall plugins to run on Meutiv with minimal adjustments.

Class Aliases
=============

The following class aliases are defined in this script:

- MT is aliased as OW
- MT_Route is aliased as OW_Route
- MT_Router is aliased as OW_Router
- MT_Autoload is aliased as OW_Autoload
- MT_BaseDao is aliased as OW_BaseDao
- MT_Entity is aliased as OW_Entity
- MT_EventManager is aliased as OW_EventManager
- MT_Event is aliased as OW_Event
- MT_ActionController is aliased as OW_ActionController
- MT_AjaxDocument is aliased as OW_AjaxDocument
- MT_ApiActionController is aliased as OW_ApiActionController
- MT_ApiApplication is aliased as OW_ApiApplication
- MT_ApiDefaultRoute is aliased as OW_ApiDefaultRoute
- MT_ApiDocument is aliased as OW_ApiDocument
- MT_ApiRequestHandler is aliased as OW_ApiRequestHandler
- MT_Application is aliased as OW_Application
- MT_AuthAdapter is aliased as OW_AuthAdapter
- MT_AuthResult is aliased as OW_AuthResult
- MT_Auth is aliased as OW_Auth
- MT_Authorization is aliased as OW_Authorization
- MT_BillingAdapter is aliased as OW_BillingAdapter
- MT_BillingProductAdapter is aliased as OW_BillingProductAdapter
- MT_CacheManager is aliased as OW_CacheManager
- MT_CacheService is aliased as OW_CacheService
- MT_CliApplication is aliased as OW_CliApplication
- MT_Component is aliased as OW_Component
- MT_Config is aliased as OW_Config
- MT_Cron is aliased as OW_Cron
- MT_Database is aliased as OW_Database
- MT_DefaultRoute is aliased as OW_DefaultRoute
- MT_DeveloperTools is aliased as OW_DeveloperTools
- MT_Dispatcher is aliased as OW_Dispatcher
- MT_Document is aliased as OW_Document
- MT_ErrorManager is aliased as OW_ErrorManager
- MT_Example is aliased as OW_Example
- MT_Feedback is aliased as OW_Feedback
- MT_IFilter is aliased as OW_IFilter
- MT_HtmlDocument is aliased as OW_HtmlDocument
- MT_IAuthenticator is aliased as OW_IAuthenticator
- MT_ICacheBackend is aliased as OW_ICacheBackend
- MT_Language is aliased as OW_Language
- MT_LogWriter is aliased as OW_LogWriter
- MT_Log is aliased as OW_Log
- MT_Mailer is aliased as OW_Mailer
- MT_MasterPage is aliased as OW_MasterPage
- MT_MobileActionController is aliased as OW_MobileActionController
- MT_MobileApplication is aliased as OW_MobileApplication
- MT_MobileComponent is aliased as OW_MobileComponent
- MT_MobileMasterPage is aliased as OW_MobileMasterPage
- MT_Navigation is aliased as OW_Navigation
- MT_PluginManager is aliased as OW_PluginManager
- MT_Plugin is aliased as OW_Plugin
- MT_Registry is aliased as OW_Registry
- MT_RemoteAuthAdapter is aliased as OW_RemoteAuthAdapter
- MT_Renderable is aliased as OW_Renderable
- MT_RequestHandler is aliased as OW_RequestHandler
- MT_Request is aliased as OW_Request
- MT_Response is aliased as OW_Response
- MT_SessionAuthenticator is aliased as OW_SessionAuthenticator
- MT_Session is aliased as OW_Session
- MT_Singleton is aliased as OW_Singleton
- MT_Smarty is aliased as OW_Smarty
- MT_Storage is aliased as OW_Storage
- MT_TextSearchManager is aliased as OW_TextSearchManager
- MT_Theme is aliased as OW_Theme
- MT_TokenAuthenticator is aliased as OW_TokenAuthenticator
- MT_User is aliased as OW_User
- MT_Validator is aliased as OW_Validator
- MT_ViewRenderer is aliased as OW_ViewRenderer
- MT_View is aliased as OW_View

Constant Aliases
================

The following constants are defined in this script:

- MT_URL_HOME is aliased as OW_URL_HOME
- MT_DB_HOST is aliased as OW_DB_HOST
- MT_DB_PORT is aliased as OW_DB_PORT
- MT_DB_USER is aliased as OW_DB_USER
- MT_DB_PASSWORD is aliased as OW_DB_PASSWORD
- MT_DB_NAME is aliased as OW_DB_NAME
- MT_DB_PREFIX is aliased as OW_DB_PREFIX
- MT_DIR_STATIC is aliased as OW_DIR_STATIC
- MT_URL_STATIC is aliased as OW_URL_STATIC
- MT_DIR_USERFILES is aliased as OW_DIR_USERFILES
- MT_URL_USERFILES is aliased as OW_URL_USERFILES
- MT_DIR_PLUGINFILES is aliased as OW_DIR_PLUGINFILES
- MT_PASSWORD_SALT is aliased as OW_PASSWORD_SALT
- MT_DIR_CORE is aliased as OW_DIR_CORE
- MT_DIR_INC is aliased as OW_DIR_INC
- MT_DIR_LIB is aliased as OW_DIR_LIB
- MT_DIR_UTIL is aliased as OW_DIR_UTIL
- MT_DIR_SMARTY is aliased as OW_DIR_SMARTY
- MT_DIR_PLUGI is aliased as OW_DIR_PLUGI
- MT_DIR_THEME is aliased as OW_DIR_THEME
- MT_DIR_SYSTEM_PLUGIN is aliased as OW_DIR_SYSTEM_PLUGIN
- MT_CRON is aliased as OW_CRON
- MT_DEBUG_MODE is aliased as OW_DEBUG_MODE
- MT_DEV_MODE is aliased as OW_DEV_MODE
- MT_PROFILER_ENABLE is aliased as OW_PROFILER_ENABLE
- MT_DIR_STATIC_PLUGIN is aliased as OW_DIR_STATIC_PLUGIN
- MT_DIR_STATIC_THEME is aliased as OW_DIR_STATIC_THEME
- MT_DIR_PLUGIN_USERFILES is aliased as OW_DIR_PLUGIN_USERFILES
- MT_DIR_THEME_USERFILES is aliased as OW_DIR_THEME_USERFILES
- MT_DIR_LOG is aliased as OW_DIR_LOG
- MT_URL_STATIC_THEMES is aliased as OW_URL_STATIC_THEMES
- MT_URL_STATIC_PLUGINS is aliased as OW_URL_STATIC_PLUGINS
- MT_URL_PLUGIN_USERFILES is aliased as OW_URL_PLUGIN_USERFILES
- MT_URL_THEME_USERFILES is aliased as OW_URL_THEME_USERFILES
- MT_DIR_LIB_VENDOR is aliased as OW_DIR_LIB_VENDOR
- MT_SQL_LIMIT_USERS_COUNT is aliased as OW_SQL_LIMIT_USERS_COUNT

Usage
=====

To use the class and constant aliases provided by this script, simply include or require the owalias/classalias.php file in your PHP project. Once included, you can reference the Meutiv classes and constants using the Oxwall aliases.

For example, instead of using MT_Application, you can now use OW_Application. Similarly, you can use OW_DB_PREFIX instead of MT_DB_PREFIX for accessing the constant.

By using these aliases, you can ensure that your Oxwall plugins can run seamlessly on Meutiv without making significant code adjustments.

Please note that this script is intended for use in specific scenarios where Meutiv and Oxwall compatibility is required. It is recommended to consult the documentation and support resources of the respective frameworks for more information on using and extending their functionality.
