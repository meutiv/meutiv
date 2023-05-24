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
 * @method static MT_Application getInstance()
 */
class MT_Application
{
    use MT_Singleton;
    
    const CONTEXT_MOBILE = BOL_UserService::USER_CONTEXT_MOBILE;
    const CONTEXT_DESKTOP = BOL_UserService::USER_CONTEXT_DESKTOP;
    const CONTEXT_API = BOL_UserService::USER_CONTEXT_API;
    const CONTEXT_CLI = BOL_UserService::USER_CONTEXT_CLI;
    const CONTEXT_NAME = 'owContext';

    /**
     * Current page document key.
     *
     * @var string
     */
    protected $documentKey;

    /**
     * @var string
     */
    protected $context;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->context = self::CONTEXT_DESKTOP;
    }

    /**
     * Sets site maintenance mode.
     *
     * @param boolean $mode
     */
    public function setMaintenanceMode( $mode )
    {
        MT::getConfig()->saveConfig('base', 'maintenance', (bool) $mode);
    }

    /**
     * @return string
     */
    public function getDocumentKey()
    {
        return $this->documentKey;
    }

    /**
     * @param string $key
     */
    public function setDocumentKey( $key )
    {
        $this->documentKey = $key;
    }

    /**
     * Application init actions.
     */
    public function init()
    {
        // router init - need to set current page uri and base url
        $router = MT::getRouter();
        $router->setBaseUrl(MT_URL_HOME);
        $this->urlHostRedirect();
        MT_Auth::getInstance()->setAuthenticator(new MT_SessionAuthenticator());
        $this->userAutoLogin();
        $this->detectLanguage();

        // setting default time zone
        date_default_timezone_set(MT::getConfig()->getValue('base', 'site_timezone'));

        if ( MT::getUser()->isAuthenticated() )
        {
            $userId = MT::getUser()->getId();
            $timeZone = BOL_PreferenceService::getInstance()->getPreferenceValue('timeZoneSelect', $userId);

            if ( !empty($timeZone) )
            {
                date_default_timezone_set($timeZone);
            }
        }

        // synchronize the db's time zone
        MT::getDbo()->setTimezone();
        $this->initRequestHandler();
        $uri = MT::getRequest()->getRequestUri();

        // before setting in router need to remove get params
        if ( strstr($uri, '?') )
        {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $router->setUri($uri);

        $router->setDefaultRoute(new MT_DefaultRoute());

        MT::getPluginManager()->initPlugins();

        $event = new MT_Event(MT_EventManager::ON_PLUGINS_INIT);
        MT::getEventManager()->trigger($event);

        $navService = BOL_NavigationService::getInstance();

        // try to find static document with current uri
        $document = $navService->findStaticDocument($uri);

        if ( $document !== null )
        {
            $this->documentKey = $document->getKey();
        }

        $beckend = MT::getEventManager()->call('base.cache_backend_init');

        if ( $beckend !== null )
        {
            MT::getCacheManager()->setCacheBackend($beckend);
            MT::getCacheManager()->setLifetime(3600);
            MT::getDbo()->setUseCashe(true);
        }

        MT_DeveloperTools::getInstance()->init();

        MT::getThemeManager()->initDefaultTheme($this->isMobile());

        // setting current theme
        $activeThemeName = MT::getEventManager()->call('base.get_active_theme_name');
        $activeThemeName = $activeThemeName ? $activeThemeName : MT::getConfig()->getValue('base', 'selectedTheme');

        if ( $activeThemeName !== BOL_ThemeService::DEFAULT_THEME && MT::getThemeManager()->getThemeService()->themeExists($activeThemeName) )
        {
            MT_ThemeManager::getInstance()->setCurrentTheme(BOL_ThemeService::getInstance()->getThemeObjectByKey(trim($activeThemeName),
                    $this->isMobile()));
        }

        // adding static document routes
        $staticDocs = $this->findAllStaticDocs();
        $staticPageDispatchAttrs = MT::getRequestHandler()->getStaticPageAttributes();

        /* @var $value BOL_Document */
        foreach ( $staticDocs as $value )
        {
            MT::getRouter()->addRoute(new MT_Route($value->getKey(), $value->getUri(),
                $staticPageDispatchAttrs['controller'], $staticPageDispatchAttrs['action'],
                array('documentKey' => array(MT_Route::PARAM_OPTION_HIDDEN_VAR => $value->getKey()))));

            // TODO refactor - hotfix for TOS page
            if ( in_array(UTIL_String::removeFirstAndLastSlashes($value->getUri()),
                    array("terms-of-use", "privacy", "privacy-policy")) )
            {
                MT::getRequestHandler()->addCatchAllRequestsExclude('base.members_only',
                    $staticPageDispatchAttrs['controller'], $staticPageDispatchAttrs['action'],
                    array('documentKey' => $value->getKey()));
            }
        }

        //adding index page route
        $availableFor = MT::getUser()->isAuthenticated() ? BOL_NavigationService::VISIBLE_FOR_MEMBER : BOL_NavigationService::VISIBLE_FOR_GUEST;
        $item = $this->findFirstMenuItem($availableFor);

        if ( $item !== null )
        {
            if ( $item->getRoutePath() )
            {
                $route = MT::getRouter()->getRoute($item->getRoutePath());
                $ddispatchAttrs = $route->getDispatchAttrs();
            }
            else
            {
                $ddispatchAttrs = MT::getRequestHandler()->getStaticPageAttributes();
            }

            $router->addRoute(new MT_Route('base_default_index', '/', $ddispatchAttrs['controller'],
                $ddispatchAttrs['action'],
                array('documentKey' => array(MT_Route::PARAM_OPTION_HIDDEN_VAR => $item->getDocumentKey()))));
            $this->indexMenuItem = $item;
            MT::getEventManager()->bind(MT_EventManager::ON_AFTER_REQUEST_HANDLE, array($this, 'activateMenuItem'));
        }
        else
        {
            $router->addRoute(new MT_Route('base_default_index', '/', 'BASE_CTRL_ComponentPanel', 'index'));
        }

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getResponse()->setDocument($this->newDocument());
            MT::getDocument()->setMasterPage($this->getMasterPage());
            MT::getResponse()->setHeader(MT_Response::HD_CNT_TYPE,
                MT::getDocument()->getMime() . '; charset=' . MT::getDocument()->getCharset());
        }
        else
        {
            MT::getResponse()->setDocument(new MT_AjaxDocument());
        }

        /* additional actions */
        if ( MT::getUser()->isAuthenticated() )
        {
            BOL_UserService::getInstance()->updateActivityStamp(MT::getUser()->getId(), $this->getContext());
        }

        // adding global template vars
        $currentThemeImagesDir = MT::getThemeManager()->getCurrentTheme()->getStaticImagesUrl();
        $currentThemeStaticUrl = MT::getThemeManager()->getCurrentTheme()->getStaticUrl();

        $viewRenderer = MT_ViewRenderer::getInstance();
        $viewRenderer->assignVar('themeImagesUrl', $currentThemeImagesDir);
        $viewRenderer->assignVar('themeStaticUrl', $currentThemeStaticUrl);
        $viewRenderer->assignVar('siteName', MT::getConfig()->getValue('base', 'site_name'));
        $viewRenderer->assignVar('siteTagline', MT::getConfig()->getValue('base', 'site_tagline'));
        $viewRenderer->assignVar('siteUrl', MT_URL_HOME);
        $viewRenderer->assignVar('isAuthenticated', MT::getUser()->isAuthenticated());
        $viewRenderer->assignVar('bottomPoweredByLink',
            '<a href="https://developers.oxwall.com/" target="_blank" title="Powered by Meutiv Community Software"><img src="' . $currentThemeImagesDir . 'powered-by-oxwall.png" alt="Meutiv Community Software" /></a>');

        $spotParams = array(
            "platform-version" => MT::getConfig()->getValue("base", "soft_version"),
            "platform-build" => MT::getConfig()->getValue("base", "soft_build"),
            "theme" => MT::getConfig()->getValue("base", "selectedTheme")
        );

        $viewRenderer->assignVar('adminDashboardIframeUrl',
            MT::getRequest()->buildUrlQueryString("//static.oxwall.org/spotlight/", $spotParams));

        if ( function_exists('mt_service_actions') )
        {
            call_user_func('mt_service_actions');
        }

        $this->handleHttps();
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

        $this->httpVsHttpsRedirect();
    }

    /**
     * ---------
     */
    public function handleRequest()
    {
        $baseConfigs = MT::getConfig()->getValues('base');

        //members only
        if ( (int) $baseConfigs['guests_can_view'] === BOL_UserService::PERMISSIONS_GUESTS_CANT_VIEW && !MT::getUser()->isAuthenticated() )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_CTRL_User',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'standardSignIn'
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.members_only', $attributes);
            $this->addCatchAllRequestsException('base.members_only_exceptions', 'base.members_only');
        }

        //splash screen
        if ( (bool) MT::getConfig()->getValue('base', 'splash_screen') && !isset($_COOKIE['splashScreen']) )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_CTRL_BaseDocument',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'splashScreen',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_REDIRECT => true,
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_JS => true,
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ROUTE => 'base_page_splash_screen'
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.splash_screen', $attributes);
            $this->addCatchAllRequestsException('base.splash_screen_exceptions', 'base.splash_screen');
        }

        // password protected
        if ( (int) $baseConfigs['guests_can_view'] === BOL_UserService::PERMISSIONS_GUESTS_PASSWORD_VIEW && !MT::getUser()->isAuthenticated() && !isset($_COOKIE['base_password_protection'])
        )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_CTRL_BaseDocument',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'passwordProtection'
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.password_protected', $attributes);
            $this->addCatchAllRequestsException('base.password_protected_exceptions', 'base.password_protected');
        }

        // maintenance mode
        if ( (bool) $baseConfigs['maintenance'] && !MT::getUser()->isAdmin() )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_CTRL_BaseDocument',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'maintenance',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_REDIRECT => true
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.maintenance_mode', $attributes);
            $this->addCatchAllRequestsException('base.maintenance_mode_exceptions', 'base.maintenance_mode');
        }

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
    }

    /**
     * Method called just before request responding.
     */
    public function finalize()
    {
        $document = MT::getDocument();

        $meassages = MT::getFeedback()->getFeedback();

        foreach ( $meassages as $messageType => $messageList )
        {
            foreach ( $messageList as $message )
            {
                $document->addOnloadScript("MT.message(" . json_encode($message) . ", '" . $messageType . "');");
            }
        }

        $event = new MT_Event(MT_EventManager::ON_FINALIZE);
        MT::getEventManager()->trigger($event);
    }

    /**
     * System method. Don't call it!!!
     */
    public function onBeforeDocumentRender()
    {
        $document = MT::getDocument();
        $themeManager = MT::getThemeManager();

        $document->addStyleSheet(MT::getPluginManager()->getPlugin('base')->getStaticCssUrl() . 'ow.css' . '?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', -100);
        $document->addStyleSheet($themeManager->getCssFileUrl() . '?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', (-90));

        // add custom css if page is not admin TODO replace with another condition
        if ( !MT::getDocument()->getMasterPage() instanceof ADMIN_CLASS_MasterPage )
        {
            if ( $themeManager->getCurrentTheme()->getDto()->getCustomCssFileName() !== null )
            {
                $document->addStyleSheet($themeManager->getThemeService()->getCustomCssFileUrl($themeManager->getCurrentTheme()->getDto()->getKey()));
            }

            if ( $this->getDocumentKey() !== 'base.sign_in' )
            {
                $customHeadCode = MT::getConfig()->getValue('base', 'html_head_code');
                $customAppendCode = MT::getConfig()->getValue('base', 'html_prebody_code');

                if ( !empty($customHeadCode) )
                {
                    $document->addCustomHeadInfo($customHeadCode);
                }

                if ( !empty($customAppendCode) )
                {
                    $document->appendBody($customAppendCode);
                }
            }
        }
        else
        {
            $document->addStyleSheet(MT::getPluginManager()->getPlugin('admin')->getStaticCssUrl() . 'admin.css' . '?' . MT::getConfig()->getValue('base',
                    'cachedEntitiesPostfix'), 'all', -50);
        }

        // add current theme name to body class
        $document->addBodyClass($themeManager->getCurrentTheme()->getDto()->getKey());

        $language = MT::getLanguage();

        if ( $document->getTitle() === null )
        {
            $document->setTitle($language->text('nav', 'page_default_title'));
        }

        if ( $document->getDescription() === null )
        {
            $document->setDescription($language->text('nav', 'page_default_description'));
        }

        /* if ( $document->getKeywords() === null )
          {
          $document->setKeywords($language->text('nav', 'page_default_keywords'));
          } */

        if ( $document->getHeadingIconClass() === null )
        {
            $document->setHeadingIconClass('mt_ic_file');
        }

        if ( !empty($this->documentKey) )
        {
            $document->addBodyClass($this->documentKey);
        }

        if ( $this->getDocumentKey() !== null )
        {
            $masterPagePath = MT::getThemeManager()->getDocumentMasterPage($this->getDocumentKey());

            if ( $masterPagePath !== null )
            {
                $document->getMasterPage()->setTemplate($masterPagePath);
            }
        }
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
        if ( $switchContextTo !== false && in_array($switchContextTo, array(self::CONTEXT_DESKTOP, self::CONTEXT_MOBILE)) )
        {
            MT::getSession()->set(self::CONTEXT_NAME, $switchContextTo);
        }

        // if empty redirect location -> current URI is used
        if ( $redirectTo === null )
        {
            $redirectTo = MT::getRequest()->getRequestUri();
        }

        // if URI is provided need to add site home URL
        if ( !strstr($redirectTo, 'http://') && !strstr($redirectTo, 'https://') )
        {
            $redirectTo = MT::getRouter()->getBaseUrl() . UTIL_String::removeFirstAndLastSlashes($redirectTo);
        }

        UTIL_Url::redirect($redirectTo);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function isMobile()
    {
        return $this->context == self::CONTEXT_MOBILE;
    }

    public function isDesktop()
    {
        return $this->context == self::CONTEXT_DESKTOP;
    }

    public function isApi()
    {
        return $this->context == self::CONTEXT_API;
    }

    public function isCli()
    {
        return $this->context == self::CONTEXT_CLI;
    }
    /* -------------------------------------------------------------------------------------------------------------- */
    /**
     * Menu item to activate.
     *
     * @var BOL_MenuItem
     */
    protected $indexMenuItem;

    public function activateMenuItem()
    {
        if ( !MT::getDocument()->getMasterPage() instanceof ADMIN_CLASS_MasterPage )
        {
            if ( MT::getRequest()->getRequestUri() === '/' || MT::getRequest()->getRequestUri() === '' )
            {
                MT::getNavigation()->activateMenuItem(MT_Navigation::MAIN, $this->indexMenuItem->getPrefix(),
                    $this->indexMenuItem->getKey());
            }
        }
    }
    /* private auxilary methods */

    protected function newDocument()
    {
        $language = BOL_LanguageService::getInstance()->getCurrent();
        $document = new MT_HtmlDocument();
        $document->setCharset('UTF-8');
        $document->setMime('text/html');
        $document->setLanguage($language->getTag());

        if ( $language->getRtl() )
        {
            $document->setDirection('rtl');
        }
        else
        {
            $document->setDirection('ltr');
        }

        if ( (bool) MT::getConfig()->getValue('base', 'favicon') )
        {
            $document->setFavicon(MT::getPluginManager()->getPlugin('base')->getUserFilesUrl() . 'favicon.ico');
        }

        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery.min.js',
            'text/javascript', (-100));
        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-migrate.min.js',
            'text/javascript', (-100));

        //$document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'json2.js', 'text/javascript', (-99));
        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'ow.js?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'text/javascript', (-50));

        $onloadJs = "MT.bindAutoClicks();MT.bindTips($('body'));";

        if ( MT::getUser()->isAuthenticated() )
        {
            $activityUrl = MT::getRouter()->urlFor('BASE_CTRL_User', 'updateActivity');
            $onloadJs .= "MT.getPing().addCommand('user_activity_update').start(600000);";
        }

        $document->addOnloadScript($onloadJs);
        MT::getEventManager()->bind(MT_EventManager::ON_AFTER_REQUEST_HANDLE, array($this, 'onBeforeDocumentRender'));

        return $document;
    }

    protected function urlHostRedirect()
    {
        if ( !isset($_SERVER['HTTP_HOST']) )
        {
            return;
        }

        $urlArray = parse_url(MT_URL_HOME);
        $constHost = $urlArray['host'];
        $serverHost = $_SERVER['HTTP_HOST'];

        if ( mb_strpos($serverHost, ':') !== false )
        {
            $serverHost = mb_substr($serverHost, 0, mb_strpos($serverHost, ':'));
        }

        if ( $serverHost !== $constHost )
        {
            $this->redirect(MT_URL_HOME . MT::getRequest()->getRequestUri());
        }
    }
    /**
     * @var array 
     */
    protected $httpsHandlerAttrsList = array();

    public function addHttpsHandlerAttrs( $controller, $action = false )
    {
        $this->httpsHandlerAttrsList[] = array(MT_RequestHandler::ATTRS_KEY_CTRL => $controller, MT_RequestHandler::ATTRS_KEY_ACTION => $action);
    }

    protected function httpVsHttpsRedirect()
    {
        if ( MT::getRequest()->isAjax() )
        {
            return;
        }

        $isSsl = MT::getRequest()->isSsl();

        if ( $isSsl === null )
        {
            return;
        }

        $attrs = MT::getRequestHandler()->getHandlerAttributes();
        $specAttrs = false;

        foreach ( $this->httpsHandlerAttrsList as $item )
        {
            if ( $item[MT_RequestHandler::ATTRS_KEY_CTRL] == $attrs[MT_RequestHandler::ATTRS_KEY_CTRL] && ( empty($item[MT_RequestHandler::ATTRS_KEY_ACTION]) || $item[MT_RequestHandler::ATTRS_KEY_ACTION] == $attrs[MT_RequestHandler::ATTRS_KEY_ACTION] ) )
            {
                $specAttrs = true;
                if ( !$isSsl )
                {
                    $this->redirect(str_replace("http://", "https://", MT_URL_HOME) . MT::getRequest()->getRequestUri());
                }
            }
        }

        if ( $specAttrs )
        {
            return;
        }

        $urlArray = parse_url(MT_URL_HOME);

        if ( !empty($urlArray["scheme"]) )
        {
            $homeUrlSsl = ($urlArray["scheme"] == "https");

            if ( ($isSsl && !$homeUrlSsl) || (!$isSsl && $homeUrlSsl) )
            {
                $this->redirect(MT_URL_HOME . MT::getRequest()->getRequestUri());
            }
        }
    }

    protected function handleHttps()
    {
        if ( !MT::getRequest()->isSsl() || substr(MT::getRouter()->getBaseUrl(), 0, 5) == "https" )
        {
            return;
        }

        function base_post_handle_https_static_content()
        {
            $markup = MT::getResponse()->getMarkup();
            $matches = array();
            preg_match_all("/<a([^>]+?)>(.+?)<\/a>/", $markup, $matches);
            $search = array_unique($matches[0]);
            $replace = array();
            $contentReplaceArr = array();

            for ( $i = 0; $i < sizeof($search); $i++ )
            {
                $replace[] = "<#|#|#" . $i . "#|#|#>";
                if ( mb_strstr($matches[2][$i], "http:") )
                {
                    $contentReplaceArr[] = $i;
                }
            }

            $markup = str_replace($search, $replace, $markup);
            $markup = str_replace("http:", "https:", $markup);

            foreach ( $contentReplaceArr as $index )
            {
                $search[$index] = str_replace($matches[2][$index], str_replace("http:", "https:", $matches[2][$index]),
                    $search[$index]);
            }

            $markup = str_replace($replace, $search, $markup);

            MT::getResponse()->setMarkup($markup);
        }
        MT::getEventManager()->bind(MT_EventManager::ON_AFTER_DOCUMENT_RENDER, "base_post_handle_https_static_content");
    }

    protected function userAutoLogin()
    {
        if ( MT::getSession()->isKeySet('no_autologin') )
        {
            MT::getSession()->delete('no_autologin');
            return;
        }

        if ( !empty($_COOKIE['mt_login']) && !MT::getUser()->isAuthenticated() )
        {
            $id = BOL_UserService::getInstance()->findUserIdByCookie(trim($_COOKIE['mt_login']));

            if ( !empty($id) )
            {
                MT_User::getInstance()->login($id);
                $loginCookie = BOL_UserService::getInstance()->findLoginCookieByUserId($id);
                setcookie('mt_login', $loginCookie->getCookie(), (time() + 86400 * 7), '/', '', false, true);
            }
        }
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
                MT::getRequestHandler()->addCatchAllRequestsExclude($key, trim($item['controller']),
                    trim($item['action']));
            }
        }
    }

    protected function initRequestHandler()
    {
        MT::getRequestHandler()->setIndexPageAttributes('BASE_CTRL_ComponentPanel');
        MT::getRequestHandler()->setStaticPageAttributes('BASE_CTRL_StaticDocument');
    }

    protected function findAllStaticDocs()
    {
        return BOL_NavigationService::getInstance()->findAllStaticDocuments();
    }

    protected function findFirstMenuItem( $availableFor )
    {
        return BOL_NavigationService::getInstance()->findFirstLocal($availableFor, MT_Navigation::MAIN);
    }

    protected function getSiteRootRoute()
    {
        return new MT_Route('base_default_index', '/', 'BASE_CTRL_ComponentPanel', 'index');
    }

    protected function getMasterPage()
    {
        return new MT_MasterPage();
    }

    protected function detectLanguage()
    {
        $languageId = 0;

        if ( !empty($_GET['language_id']) )
        {
            $languageId = intval($_GET['language_id']);
        }
        else if ( !empty($_COOKIE[BOL_LanguageService::LANG_ID_VAR_NAME]) )
        {
            $languageId = intval($_COOKIE[BOL_LanguageService::LANG_ID_VAR_NAME]);
        }

        if( $languageId > 0 )
        {
            MT::getSession()->set(BOL_LanguageService::LANG_ID_VAR_NAME, $languageId);
        }

        $session_language_id = MT::getSession()->get(BOL_LanguageService::LANG_ID_VAR_NAME);
        $languageService = BOL_LanguageService::getInstance();

        if( $session_language_id  )
        {
            $dto = $languageService->findById($session_language_id);

            if( $dto !== null && $dto->getStatus() == "active" )
            {
                $languageService->setCurrentLanguage($dto);
            }
        }

        $languageService->getCurrent();

        setcookie(BOL_LanguageService::LANG_ID_VAR_NAME, strval($languageService->getCurrent()->getId()), time() + 60 * 60 * 24 * 30, "/");
    }
}
