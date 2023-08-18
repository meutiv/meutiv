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
 * @method static MT_MobileApplication getInstance()
 * @since 1.0
 */
class MT_MobileApplication extends MT_Application
{
    use MT_Singleton;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->context = self::CONTEXT_MOBILE;
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
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_User',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'standardSignIn'
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.members_only', $attributes);
            $this->addCatchAllRequestsException('base.members_only_exceptions', 'base.members_only');
        }

        //splash screen
        if ( (bool) MT::getConfig()->getValue('base', 'splash_screen') && !isset($_COOKIE['splashScreen']) )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
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
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'passwordProtection'
            );

            MT::getRequestHandler()->setCatchAllRequestsAttributes('base.password_protected', $attributes);
            $this->addCatchAllRequestsException('base.password_protected_exceptions', 'base.password_protected');
        }

        // maintenance mode
        if ( (bool) $baseConfigs['maintenance'] && !MT::getUser()->isAdmin() )
        {
            $attributes = array(
                MT_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
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
                $document->addOnloadScript("OWM.message(" . json_encode($message) . ", '" . $messageType . "');");
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

        $document->addStyleSheet(MT::getPluginManager()->getPlugin('base')->getStaticCssUrl() . 'mobile.css' . '?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', -100);
        $document->addStyleSheet(MT::getThemeManager()->getCssFileUrl(true) . '?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', (-90));

        if ( MT::getThemeManager()->getCurrentTheme()->getDto()->getCustomCssFileName() !== null )
        {
            $document->addStyleSheet(MT::getThemeManager()->getThemeService()->getCustomCssFileUrl(MT::getThemeManager()->getCurrentTheme()->getDto()->getKey(),
                    true));
        }

        $language = MT::getLanguage();

        if ( $document->getTitle() === null )
        {
            $document->setTitle($language->text('mobile', 'page_default_title'));
        }

        if ( $document->getDescription() === null )
        {
            $document->setDescription($language->text('mobile', 'page_default_description'));
        }

        if ( $document->getHeading() === null )
        {
            $document->setHeading($language->text('mobile', 'page_default_heading'));
        }

        $document->addMetaInfo('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }

    public function activateMenuItem()
    {
        
    }

    protected function newDocument()
    {
        $language = BOL_LanguageService::getInstance()->getCurrent();
        $document = new MT_HtmlDocument();
        $document->setTemplate(MT::getThemeManager()->getMasterPageTemplate('mobile_html_document'));
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
        $document->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'mobile.js?' . MT::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'text/javascript', (-50));
        MT::getEventManager()->bind(MT_EventManager::ON_AFTER_REQUEST_HANDLE, array($this, 'onBeforeDocumentRender'));

        return $document;
    }

    protected function initRequestHandler()
    {
        MT::getRequestHandler()->setStaticPageAttributes('BASE_MCTRL_BaseDocument', 'staticDocument');
    }

    protected function findAllStaticDocs()
    {
        return BOL_NavigationService::getInstance()->findAllMobileStaticDocuments();
    }

    protected function findFirstMenuItem( $availableFor )
    {
        return BOL_NavigationService::getInstance()->findFirstLocal($availableFor, MT_Navigation::MOBILE_TOP);
    }

    protected function getSiteRootRoute()
    {
        return new MT_Route('base_default_index', '/', 'BASE_MCTRL_WidgetPanel', 'index');
    }

    protected function getMasterPage()
    {
        return new MT_MobileMasterPage();
    }
}
