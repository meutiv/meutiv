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
class BASE_CTRL_StaticDocument extends MT_ActionController
{
    /**
     * @var BOL_NavigationService
     */
    private $navService;

    /**
     * @var string
     */
    private $documentKey;

    public function __construct()
    {
        parent::__construct();
        $this->navService = BOL_NavigationService::getInstance();
    }

    public function index( $params )
    {
        if ( empty($params['documentKey']) )
        {
            throw new Redirect404Exception();
        }

        $language = MT::getLanguage();
        $documentKey = $params['documentKey'];

        $document = $this->navService->findDocumentByKey($documentKey);

        if ( $document === null )
        {
            throw new Redirect404Exception();
        }

        $menuItem = $this->navService->findMenuItemByDocumentKey($document->getKey());

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

        $this->assign('content', $language->text('base', "local_page_content_{$document->getKey()}"));
        $this->setPageHeading($language->text('base', "local_page_title_{$document->getKey()}"));
        $this->setPageTitle($language->text('base', "local_page_title_{$document->getKey()}"));
        $this->documentKey = $document->getKey();

        $this->setDocumentKey($document->getKey());

        MT::getEventManager()->bind(MT_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'setCustomMetaInfo'));
    }

    public function setCustomMetaInfo()
    {
        MT::getDocument()->setDescription(null);

        if ( MT::getLanguage()->valueExist('base', "local_page_meta_desc_{$this->getDocumentKey()}") )
        {
            MT::getDocument()->setDescription(MT::getLanguage()->text('base', "local_page_meta_desc_{$this->getDocumentKey()}"));
        }

        if ( MT::getLanguage()->valueExist('base', "local_page_meta_keywords_{$this->getDocumentKey()}") )
        {
            MT::getDocument()->setKeywords(MT::getLanguage()->text('base', "local_page_meta_keywords_{$this->getDocumentKey()}"));
        }
    }
}
