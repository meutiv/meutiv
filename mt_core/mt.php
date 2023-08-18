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
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_core
 * @since 1.0
 */
final class MT
{
    const CONTEXT_MOBILE = MT_Application::CONTEXT_MOBILE;
    const CONTEXT_DESKTOP = MT_Application::CONTEXT_DESKTOP;
    const CONTEXT_API = MT_Application::CONTEXT_API;
    const CONTEXT_CLI = MT_Application::CONTEXT_CLI;

    private static $context;

    private static function detectContext()
    {
        if ( self::$context !== null )
        {
            return;
        }

        if ( defined('MT_USE_CONTEXT') )
        {
            switch ( true )
            {
                case MT_USE_CONTEXT == 1:
                    self::$context = self::CONTEXT_DESKTOP;
                    return;

                case MT_USE_CONTEXT == 1 << 1:
                    self::$context = self::CONTEXT_MOBILE;
                    return;

                case MT_USE_CONTEXT == 1 << 2:
                    self::$context = self::CONTEXT_API;
                    return;

                case MT_USE_CONTEXT == 1 << 3:
                    self::$context = self::CONTEXT_CLI;
                    return;
            }
        }


        $context = self::CONTEXT_DESKTOP;

        try
        {
            $isSmart = UTIL_Browser::isSmartphone() ? true : UTIL_Browser::isTierTablet();
        }
        catch ( Exception $e )
        {
            return;
        }

        if ( defined('MT_CRON') )
        {
            $context = self::CONTEXT_DESKTOP;
        }
        else if ( self::getSession()->isKeySet(MT_Application::CONTEXT_NAME) )
        {
            $context = self::getSession()->get(MT_Application::CONTEXT_NAME);
        }
        else if ( $isSmart )
        {
            $context = self::CONTEXT_MOBILE;
        }

        if ( defined('MT_USE_CONTEXT') )
        {
            if ( (MT_USE_CONTEXT & 1 << 1) == 0 && $context == self::CONTEXT_MOBILE )
            {
                $context = self::CONTEXT_DESKTOP;
            }

            if ( (MT_USE_CONTEXT & 1 << 2) == 0 && $context == self::CONTEXT_API )
            {
                $context = self::CONTEXT_DESKTOP;
            }
        }

        if ( (bool) MT::getConfig()->getValue('base', 'disable_mobile_context') && $context == self::CONTEXT_MOBILE )
        {
            $context = self::CONTEXT_DESKTOP;
        }

        //temp API context detection
        //TODO remake
        $uri = UTIL_Url::getRealRequestUri(MT::getRouter()->getBaseUrl(), $_SERVER['REQUEST_URI']);


        if ( mb_strstr($uri, '/') )
        {
            if ( trim(mb_substr($uri, 0, mb_strpos($uri, '/'))) == 'api' )
            {
                $context = self::CONTEXT_API;
            }
        }
        else
        {
            if ( trim($uri) == 'api' )
            {
                $context = self::CONTEXT_API;
            }
        }

        self::$context = $context;
    }

    /**
     * Returns autoloader object.
     *
     * @return MT_Autoload
     */
    public static function getAutoloader()
    {
        return MT_Autoload::getInstance();
    }

    /**
     * Returns front controller object.
     *
     * @return MT_Application
     */
    public static function getApplication()
    {
        self::detectContext();

        switch ( self::$context )
        {
            case self::CONTEXT_MOBILE:
                return MT_MobileApplication::getInstance();

            case self::CONTEXT_API:
                return MT_ApiApplication::getInstance();

            case self::CONTEXT_CLI:
                return MT_CliApplication::getInstance();

            default:
                return MT_Application::getInstance();
        }
    }

    /**
     * Returns global config object.
     *
     * @return MT_Config
     */
    public static function getConfig()
    {
        return MT_Config::getInstance();
    }

    /**
     * Returns session object.
     *
     * @return MT_Session
     */
    public static function getSession()
    {
        return MT_Session::getInstance();
    }

    /**
     * Returns current web user object.
     *
     * @return MT_User
     */
    public static function getUser()
    {
        return MT_User::getInstance();
    }
    /**
     * Database object instance.
     *
     * @var MT_Database
     */
    private static $dboInstance;

    /**
     * Returns DB access object with default connection.
     *
     * @return MT_Database
     */
    public static function getDbo()
    {
        if ( self::$dboInstance === null )
        {
            $params = array(
                'host' => MT_DB_HOST,
                'username' => MT_DB_USER,
                'password' => MT_DB_PASSWORD,
                'dbname' => MT_DB_NAME
            );
            if ( defined('MT_DB_PORT') && (MT_DB_PORT !== null) )
            {
                $params['port'] = MT_DB_PORT;
            }
            if ( defined('MT_DB_SOCKET') )
            {
                $params['socket'] = MT_DB_SOCKET;
            }

            if ( MT_DEV_MODE || MT_PROFILER_ENABLE )
            {
                $params['profilerEnable'] = true;
            }

            if ( MT_DEBUG_MODE )
            {
                $params['debugMode'] = true;
            }

            self::$dboInstance = MT_Database::getInstance($params);
        }
        return self::$dboInstance;
    }

    /**
     * Returns system mailer object.
     *
     * 	@return MT_Mailer
     */
    public static function getMailer()
    {
        return MT_Mailer::getInstance();
    }

    /**
     * Returns responded HTML document object.
     *
     * @return MT_HtmlDocument
     */
    public static function getDocument()
    {
        return MT_Response::getInstance()->getDocument();
    }

    /**
     * Returns global request object.
     *
     * @return MT_Request
     */
    public static function getRequest()
    {
        return MT_Request::getInstance();
    }

    /**
     * Returns global response object.
     *
     * @return MT_Response
     */
    public static function getResponse()
    {
        return MT_Response::getInstance();
    }

    /**
     * Returns language object.
     *
     * @return MT_Language
     */
    public static function getLanguage()
    {
        return MT_Language::getInstance();
    }

    /**
     * Returns system router object.
     *
     * @return MT_Router
     */
    public static function getRouter()
    {
        return MT_Router::getInstance();
    }

    /**
     * Returns system plugin manager object.
     *
     * @return MT_PluginManager
     */
    public static function getPluginManager()
    {
        return MT_PluginManager::getInstance();
    }

    /**
     * Returns system theme manager object.
     *
     * @return MT_ThemeManager
     */
    public static function getThemeManager()
    {
        return MT_ThemeManager::getInstance();
    }

    /**
     * Returns system event manager object.
     *
     * @return MT_EventManager
     */
    public static function getEventManager()
    {
        return MT_EventManager::getInstance();
    }

    /**
     * @return MT_Registry
     */
    public static function getRegistry()
    {
        return MT_Registry::getInstance();
    }

    /**
     * Returns global feedback object.
     *
     * @return MT_Feedback
     */
    public static function getFeedback()
    {
        return MT_Feedback::getInstance();
    }

    /**
     * Returns global navigation object.
     *
     * @return MT_Navigation
     */
    public static function getNavigation()
    {
        return MT_Navigation::getInstance();
    }

    /**
     * @deprecated
     * @return MT_Dispatcher
     */
    public static function getDispatcher()
    {
        return MT_RequestHandler::getInstance();
    }

    /**
     * @return MT_RequestHandler
     */
    public static function getRequestHandler()
    {
        self::detectContext();

        switch ( self::$context )
        {
            case self::CONTEXT_API:
                return MT_ApiRequestHandler::getInstance();

            default:
                return MT_RequestHandler::getInstance();
        }
    }

    /**
     *
     * @return MT_CacheService
     */
    public static function getCacheService()
    {
        return BOL_DbCacheService::getInstance(); //TODO make configurable
    }
    private static $storage;

    /**
     *
     * @return MT_Storage
     */
    public static function getStorage()
    {
        if ( self::$storage === null )
        {
            self::$storage = MT::getEventManager()->call('core.get_storage');

            if ( self::$storage === null )
            {
                switch ( true )
                {
                    case defined('MT_USE_AMAZON_S3_CLOUDFILES') && MT_USE_AMAZON_S3_CLOUDFILES :
                        self::$storage = new BASE_CLASS_AmazonCloudStorage();
                        break;

                    case defined('MT_USE_CLOUDFILES') && MT_USE_CLOUDFILES :
                        self::$storage = new BASE_CLASS_CloudStorage();
                        break;

                    default :
                        self::$storage = new BASE_CLASS_FileStorage();
                        break;
                }
            }
        }

        return self::$storage;
    }

    public static function getLogger( $logType = 'ow' )
    {
        return MT_Log::getInstance($logType);
    }

    /**
     * @return MT_Authorization
     */
    public static function getAuthorization()
    {
        return MT_Authorization::getInstance();
    }

    /**
     * @return MT_CacheManager
     */
    public static function getCacheManager()
    {
        return MT_CacheManager::getInstance();
    }

    public static function getClassInstance( $className, $arguments = null )
    {
        $args = func_get_args();
        $constuctorArgs = array_splice($args, 1);

        return self::getClassInstanceArray($className, $constuctorArgs);
    }

    public static function getClassInstanceArray( $className, array $arguments = array() )
    {
        if ( !self::isInternalClass($className) )
        {
            throw new LogicException("Unable to instantiate class `{$className}`.Only internal Meutiv classes that adherence to the naming convention are allowed for instantiating!");
        }

        $params = array(
            'className' => $className,
            'arguments' => $arguments
        );

        $eventManager = MT::getEventManager();
        $eventManager->trigger(new MT_Event("core.performance_test", array("key" => "component_construct.start", "params" => $params)));

        $event = new MT_Event("class.get_instance." . $className, $params);
        $eventManager->trigger($event);
        $instance = $event->getData();

        if ( $instance !== null )
        {
            $eventManager->trigger(new MT_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
            return $instance;
        }

        $event = new MT_Event("class.get_instance", $params);

        $eventManager->trigger($event);
        $instance = $event->getData();

        if ( $instance !== null )
        {
            $eventManager->trigger(new MT_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
            return $instance;
        }

        $rClass = new ReflectionClass($className);
        $eventManager->trigger(new MT_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
        return $rClass->newInstanceArgs($arguments);
    }

    /**
     * Returns text search manager object.
     *
     * @return MT_TextSearchManager
     */
    public static function getTextSearchManager()
    {
        return MT_TextSearchManager::getInstance();
    }

    /**
     * Checks if argument class name is OxWall class type.
     *
     * @param string $className
     * @return bool
     */
    private static function isInternalClass( $className )
    {
        $allowedClassTypes = array(
            "MT_",
            "BOL_",
            "_BOL_",
            "_CLASS_",
            "_CMP_",
            "_CTRL_",
            "_MCLASS_",
            "_MCMP_",
            "_MCTRL_",
            "JoinForm",
            "BASE_Members",
            "MainSearchForm",
            "EditQuestionForm",
            "UserSettingsForm",
            "YearRange",
        );

        foreach ( $allowedClassTypes as $classType )
        {
            $pos = strpos($className, $classType);

            if ( $pos !== false )
            {
                return true;
            }
        }

        return false;
    }
}
