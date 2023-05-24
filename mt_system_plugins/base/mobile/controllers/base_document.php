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
 * @package mt_system_plugins.base.controllers
 * @since 1.0
 */
class BASE_MCTRL_BaseDocument extends MT_MobileActionController
{

    public function page404()
    {
        MT::getResponse()->setHeader('HTTP/1.0', '404 Not Found');
        MT::getResponse()->setHeader('Status', '404 Not Found');
        $this->setPageHeading(MT::getLanguage()->text('base', 'base_document_404_heading'));
        $this->setPageTitle(MT::getLanguage()->text('base', 'base_document_404_title'));
        $this->setDocumentKey('base_page404');
        $this->assign('message', MT::getLanguage()->text('mobile', 'page_is_not_available', array('url' => MT::getRouter()->urlForRoute('base.desktop_version'))));
    }

    public function page403( array $params )
    {
        $language = MT::getLanguage();
        MT::getResponse()->setHeader('HTTP/1.0', '403 Forbidden');
        MT::getResponse()->setHeader('Status', '403 Forbidden');
        $this->setPageHeading($language->text('base', 'base_document_403_heading'));
        $this->setPageTitle($language->text('base', 'base_document_403_title'));
        $this->setDocumentKey('base_page403');
        $this->assign('message', !empty($params['message']) ? $params['message'] : $language->text('base', 'base_document_403'));
    }

    public function redirectToDesktop()
    {
        $urlToRedirect = MT::getRouter()->getBaseUrl();

        if ( !empty($_GET['back-uri']) )
        {
            $urlToRedirect .= urldecode($_GET['back-uri']);
        }

        MT::getApplication()->redirect($urlToRedirect, MT::CONTEXT_DESKTOP);
    }

    public function staticDocument( $params )
    {
        $navService = BOL_NavigationService::getInstance();

        if ( empty($params['documentKey']) )
        {
            throw new Redirect404Exception();
        }

        $language = MT::getLanguage();
        $documentKey = $params['documentKey'];

        $document = $navService->findDocumentByKey($documentKey);
        
        if ( $document === null )
        {
            throw new Redirect404Exception();
        }

        $menuItem = $navService->findMenuItemByDocumentKey($document->getKey());

        if ( $menuItem !== null )
        {
            if ( !$menuItem->getVisibleFor() || ( $menuItem->getVisibleFor() == BOL_NavigationService::VISIBLE_FOR_GUEST && MT::getUser()->isAuthenticated() ) )
            {
                throw new Redirect403Exception();
            }

            if ( $menuItem->getVisibleFor() == BOL_NavigationService::VISIBLE_FOR_MEMBER && !MT::getUser()->isAuthenticated() )
            {
                throw new AuthenticateException();
            }
        }

        $settings = BOL_MobileNavigationService::getInstance()->getItemSettings($menuItem);
        $title = $settings[BOL_MobileNavigationService::SETTING_TITLE];


        $this->assign('content', $settings[BOL_MobileNavigationService::SETTING_CONTENT]);
        $this->setPageHeading($settings[BOL_MobileNavigationService::SETTING_TITLE]);
        $this->setPageTitle($settings[BOL_MobileNavigationService::SETTING_TITLE]);
        $this->setDocumentKey($document->getKey());

        //MT::getEventManager()->bind(MT_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'setCustomMetaInfo'));
    }

    public function maintenance()
    {
        if ( !MT::getRequest()->isAjax() )
        {
            MT::getDocument()->getMasterPage()->setTemplate(MT::getThemeManager()->getMasterPageTemplate(MT_MobileMasterPage::TEMPLATE_BLANK));
        }
        else
        {
            exit('{}');
        }
    }

    public function splashScreen()
    {
        if ( isset($_GET['agree']) )
        {
            setcookie('splashScreen', 1, time() + 3600 * 24 * 30, '/');
            $url = MT_URL_HOME;
            $url .= isset($_GET['back_uri']) ? $_GET['back_uri'] : '';
            $this->redirect($url);
        }

        MT::getDocument()->getMasterPage()->setTemplate(MT::getThemeManager()->getMasterPageTemplate(MT_MobileMasterPage::TEMPLATE_BLANK));
        $this->assign('submit_url', MT::getRequest()->buildUrlQueryString(null, array('agree' => 1)));

        $leaveUrl = MT::getConfig()->getValue('base', 'splash_leave_url');

        if ( !empty($leaveUrl) )
        {
            $this->assign('leaveUrl', $leaveUrl);
        }
    }

    public function passwordProtection()
    {
        $language = MT::getLanguage();

        $form = new Form('password_protection');
        $form->setAjax(true);
        $form->setAction(MT::getRouter()->urlFor('BASE_CTRL_BaseDocument', 'passwordProtection'));
        $form->setAjaxDataType(Form::AJAX_DATA_TYPE_SCRIPT);

        $password = new PasswordField('password');
        $form->addElement($password);

        $submit = new Submit('submit');
        $submit->setValue(MT::getLanguage()->text('base', 'password_protection_submit_label'));
        $form->addElement($submit);
        $this->addForm($form);

        if ( MT::getRequest()->isAjax() && $form->isValid($_POST) )
        {
            $data = $form->getValues();
            $password = MT::getConfig()->getValue('base', 'guests_can_view_password');
            $cryptedPassword = crypt($data['password'], MT_PASSWORD_SALT);

            if ( !empty($data['password']) && $cryptedPassword === $password )
            {
                setcookie('base_password_protection', UTIL_String::getRandomString(), (time() + 86400 * 30), '/');
                echo "MT.info('" . MT::getLanguage()->text('base', 'password_protection_success_message') . "');window.location.reload();";
            }
            else
            {
                echo "MT.error('" . MT::getLanguage()->text('base', 'password_protection_error_message') . "');";
            }
            exit;
        }

        MT::getDocument()->setHeading($language->text('base', 'password_protection_text'));
        MT::getDocument()->getMasterPage()->setTemplate(MT::getThemeManager()->getMasterPageTemplate(MT_MobileMasterPage::TEMPLATE_BLANK));
    }

    public function notAvailable()
    {
        $this->assign('message', MT::getLanguage()->text('mobile', 'page_is_not_available', array('url' => MT::getRouter()->urlForRoute('base.desktop_version'))));
    }

    public function authorizationFailed( array $params )
    {
        $language = MT::getLanguage();
        $this->setPageHeading($language->text('base', 'base_document_auth_failed_heading'));
        $this->setPageTitle($language->text('base', 'base_document_auth_failed_heading'));
        $this->setTemplate(MT::getPluginManager()->getPlugin('base')->getMobileCtrlViewDir() . 'authorization_failed.html');
        $this->assign('message', !empty($params['message']) ? $params['message'] : null);
    }
}
