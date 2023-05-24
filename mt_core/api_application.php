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
 * Description...
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_core
 * @method static MT_ApiApplication getInstance()
 * @since 1.0
 */
class MT_ApiApplication extends MT_Application
{
    use MT_Singleton;

    private function __construct()
    {
        $this->context = self::CONTEXT_API;
    }

    /**
     * Application init actions.
     */
    public function init()
    {
        require_once MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'json_err_output.php';
        MT_ErrorManager::getInstance()->setErrorOutput(new BASE_CLASS_JsonErrOutput());

        $authToken = empty($_SERVER["HTTP_API_AUTH_TOKEN"]) ? null : $_SERVER["HTTP_API_AUTH_TOKEN"];
        MT_Auth::getInstance()->setAuthenticator(new MT_TokenAuthenticator($authToken));

        $tag = '';

        if ( !empty($_SERVER["HTTP_API_LANGUAGE"]) )
        {
            $tag = $_SERVER["HTTP_API_LANGUAGE"];
        }
        else
        {
            if( function_exists('apache_request_headers') )
            {
                $headers = apache_request_headers();

                if ( !empty($headers) && !empty($headers['api-language']) )
                {
                    $tag = trim($headers['api-language']);
                }
            }
        }

        if ( $tag )
        {
            $languageDto = BOL_LanguageService::getInstance()->findByTag($tag);

            if ( empty($languageDto) )
            {
                $tag = str_replace('_', '-', $tag);
                $languageDto = BOL_LanguageService::getInstance()->findByTag($tag);
            }

            if ( empty($languageDto) )
            {
                $tag = mb_substr($tag, 0, 2);
                $languageDto = BOL_LanguageService::getInstance()->findByTag($tag);
            }

            if ( !empty($languageDto) && $languageDto->status == "active" )
            {
                BOL_LanguageService::getInstance()->setCurrentLanguage($languageDto);
            }
        }

        //$this->detectLanguage();

        // setting default time zone
        date_default_timezone_set(MT::getConfig()->getValue('base', 'site_timezone'));

        if( MT::getUser()->isAuthenticated() )
        {
            $userId = MT::getUser()->getId();
            $timeZone = BOL_PreferenceService::getInstance()->getPreferenceValue('timeZoneSelect', $userId);

            if(!empty($timeZone))
            {
                date_default_timezone_set($timeZone);
            }
        }

        // synchronize the db's time zone
        MT::getDbo()->setTimezone();

//        MT::getRequestHandler()->setIndexPageAttributes('BASE_CTRL_ComponentPanel');
//        MT::getRequestHandler()->setStaticPageAttributes('BASE_CTRL_StaticDocument');
//
//        // router init - need to set current page uri and base url
        $router = MT::getRouter();
        $router->setBaseUrl(MT_URL_HOME . 'api/');
        $uri = MT::getRequest()->getRequestUri();

        // before setting in router need to remove get params
        if ( strstr($uri, '?') )
        {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $router->setUri($uri);

        $router->setDefaultRoute(new MT_ApiDefaultRoute());

        MT::getPluginManager()->initPlugins();
        $event = new MT_Event(MT_EventManager::ON_PLUGINS_INIT);
        MT::getEventManager()->trigger($event);

        $beckend = MT::getEventManager()->call('base.cache_backend_init');

        if ( $beckend !== null )
        {
            MT::getCacheManager()->setCacheBackend($beckend);
            MT::getCacheManager()->setLifetime(3600);
            MT::getDbo()->setUseCashe(true);
        }

        MT::getResponse()->setDocument($this->newDocument());

        if ( MT::getUser()->isAuthenticated() )
        {
            BOL_UserService::getInstance()->updateActivityStamp(MT::getUser()->getId(), $this->getContext());
        }
    }

    /**
     * Finds controller and action for current request.
     */
    public function route()
    {
        try
        {
            MT::getRequestHandler()->setHandlerAttributes(MT::getRouter()->route());
        }
        catch ( RedirectException $e )
        {
            $this->redirect($e->getUrl(), $e->getRedirectCode());
        }
        catch ( InterceptException $e )
        {
            MT::getRequestHandler()->setHandlerAttributes($e->getHandlerAttrs());
        }
    }

    /**
     * ---------
     */
    public function handleRequest()
    {
        try
        {
            MT::getRequestHandler()->dispatch();
        }
        catch ( RedirectException $e )
        {
            $this->redirect($e->getUrl(), $e->getRedirectCode());
        }
        catch ( InterceptException $e )
        {
            MT::getRequestHandler()->setHandlerAttributes($e->getHandlerAttrs());
            $this->handleRequest();
        }
        catch ( Exception $e )
        {
            $errorType = "exception";
            
            $responseData = array(
                "exception" => get_class($e),
                "message" => $e->getMessage(),
                "code" => $e->getCode()
            );
            
            if ( $e instanceof ApiResponseErrorException )
            {
                $responseData["userData"] = $e->data;
                $errorType = "userError";
            }
            else if ( defined("MT_DEBUG_MODE") && MT_DEBUG_MODE )
            {
                $responseData["trace"] = $e->getTraceAsString();
            }
            
            $apiResponse = array(
                "type" => $errorType,
                "data" => $responseData
            );
            
            //MT::getResponse()->setHeader(MT_Response::HD_CNT_TYPE, "application/json");
            //MT::getDocument()->setBody($apiResponse);
            
            header('Content-Type: application/json');
            
            echo json_encode($apiResponse);
            exit; // TODO remove exit
        }
    }

    /**
     * Method called just before request responding.
     */
    public function finalize()
    {
//        $document = MT::getDocument();
//
//        $meassages = MT::getFeedback()->getFeedback();
//
//        foreach ( $meassages as $messageType => $messageList )
//        {
//            foreach ( $messageList as $message )
//            {
//                $document->addOnloadScript("MT.message(" . json_encode($message) . ", '" . $messageType . "');");
//            }
//        }

        $event = new MT_Event(MT_EventManager::ON_FINALIZE);
        MT::getEventManager()->trigger($event);
    }

    /**
     * System method. Don't call it!!!
     */
    public function onBeforeDocumentRender()
    {
//        $document = MT::getDocument();
//
//        $document->addStyleSheet(MT::getPluginManager()->getPlugin('base')->getStaticCssUrl() . 'ow.css' . '?' . MT::getConfig()->getValue('base', 'cachedEntitiesPostfix'), 'all', -100);
//        $document->addStyleSheet(MT::getThemeManager()->getCssFileUrl() . '?' . MT::getConfig()->getValue('base', 'cachedEntitiesPostfix'), 'all', (-90));
//
//        // add custom css if page is not admin TODO replace with another condition
//        if ( !MT::getDocument()->getMasterPage() instanceof ADMIN_CLASS_MasterPage )
//        {
//            if ( MT::getThemeManager()->getCurrentTheme()->getDto()->getCustomCssFileName() !== null )
//            {
//                $document->addStyleSheet(MT::getThemeManager()->getThemeService()->getCustomCssFileUrl(MT::getThemeManager()->getCurrentTheme()->getDto()->getName()));
//            }
//
//            if ( $this->getDocumentKey() !== 'base.sign_in' )
//            {
//                $customHeadCode = MT::getConfig()->getValue('base', 'html_head_code');
//                $customAppendCode = MT::getConfig()->getValue('base', 'html_prebody_code');
//
//                if ( !empty($customHeadCode) )
//                {
//                    $document->addCustomHeadInfo($customHeadCode);
//                }
//
//                if ( !empty($customAppendCode) )
//                {
//                    $document->appendBody($customAppendCode);
//                }
//            }
//        }
//
//        $language = MT::getLanguage();
//
//        if ( $document->getTitle() === null )
//        {
//            $document->setTitle($language->text('nav', 'page_default_title'));
//        }
//
//        if ( $document->getDescription() === null )
//        {
//            $document->setDescription($language->text('nav', 'page_default_description'));
//        }
//
//        /* if ( $document->getKeywords() === null )
//          {
//          $document->setKeywords($language->text('nav', 'page_default_keywords'));
//          } */
//
//        if ( $document->getHeadingIconClass() === null )
//        {
//            $document->setHeadingIconClass('mt_ic_file');
//        }
//
//        if ( !empty($this->documentKey) )
//        {
//            $document->setBodyClass($this->documentKey);
//        }
//
//        if ( $this->getDocumentKey() !== null )
//        {
//            $masterPagePath = MT::getThemeManager()->getDocumentMasterPage($this->getDocumentKey());
//
//            if ( $masterPagePath !== null )
//            {
//                $document->getMasterPage()->setTemplate($masterPagePath);
//            }
//        }
    }

    /**
     * Triggers response object to send rendered page.
     */
    public function returnResponse()
    {
        MT::getResponse()->respond();
    }

    /**
     * Makes header redirect to provided URL or URI.
     *
     * @param string $redirectTo
     */
    public function redirect( $redirectTo = null, $switchContextTo = false )
    {
//        if ( $switchContextTo !== false && in_array($switchContextTo, array(self::CONTEXT_DESKTOP, self::CONTEXT_MOBILE)) )
//        {
//            MT::getSession()->set(self::CONTEXT_NAME, $switchContextTo);
//        }
//
//        // if empty redirect location -> current URI is used
//        if ( $redirectTo === null )
//        {
//            $redirectTo = MT::getRequest()->getRequestUri();
//        }
//
//        // if URI is provided need to add site home URL
//        if ( !strstr($redirectTo, 'http://') && !strstr($redirectTo, 'https://') )
//        {
//            $redirectTo = MT::getRouter()->getBaseUrl() . UTIL_String::removeFirstAndLastSlashes($redirectTo);
//        }
//
//        UTIL_Url::redirect($redirectTo);
    }

    /**
     * Menu item to activate.
     *
     * @var BOL_MenuItem
     */
    public function activateMenuItem()
    {
//        if ( !MT::getDocument()->getMasterPage() instanceof ADMIN_CLASS_MasterPage )
//        {
//            if ( MT::getRequest()->getRequestUri() === '/' || MT::getRequest()->getRequestUri() === '' )
//            {
//                MT::getNavigation()->activateMenuItem(MT_Navigation::MAIN, $this->indexMenuItem->getPrefix(), $this->indexMenuItem->getKey());
//            }
//        }
    }
    /* private auxilary methods */

    protected function newDocument()
    {
        $document = new MT_ApiDocument();

        return $document;

//        $language = BOL_LanguageService::getInstance()->getCurrent();
//        $document = new MT_HtmlDocument();
//        $document->setCharset('UTF-8');
//        $document->setMime('text/html');
//        $document->setLanguage($language->getTag());
//
//        if ( $language->getRtl() )
//        {
//            $document->setDirection('rtl');
//        }
//        else
//        {
//            $document->setDirection('ltr');
//        }
//
//        if ( (bool) MT::getConfig()->getValue('base', 'favicon') )
//        {
//            $document->setFavicon(MT::getPluginManager()->getPlugin('base')->getUserFilesUrl() . 'favicon.ico');
//        }
//
//        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery.min.js', 'text/javascript', (-100));
//        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-migrate.min.js', 'text/javascript', (-100));
//
//        //$document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'json2.js', 'text/javascript', (-99));
//        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'ow.js?' . MT::getConfig()->getValue('base', 'cachedEntitiesPostfix'), 'text/javascript', (-50));
//
//        $onloadJs = "MT.bindAutoClicks();MT.bindTips($('body'));";
//
//        if ( MT::getUser()->isAuthenticated() )
//        {
//            $activityUrl = MT::getRouter()->urlFor('BASE_CTRL_User', 'updateActivity');
//            $onloadJs .= "MT.getPing().addCommand('user_activity_update').start(600000);";
//        }
//
//        $document->addOnloadScript($onloadJs);
//        MT::getEventManager()->bind(MT_EventManager::ON_AFTER_REQUEST_HANDLE, array($this, 'onBeforeDocumentRender'));

        return $document;
    }

    protected function addCatchAllRequestsException( $eventName, $key )
    {
        $event = new BASE_CLASS_EventCollector($eventName);
        MT::getEventManager()->trigger($event);
        $exceptions = $event->getData();

        foreach ( $exceptions as $item )
        {
            if ( is_array($item) && !empty($item['controller']) && !empty($item['action']) )
            {
                MT::getRequestHandler()->addCatchAllRequestsExclude($key, trim($item['controller']), trim($item['action']));
            }
        }
    }
}
