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
 * Restricted Usernames
 *
 * @author Zarif Safiullin <zaph.saph@gmail.com>
 * @package mt_system_plugins.admin.controllers
 * @since 1.0
 */
class ADMIN_CTRL_RestrictedUsernames extends ADMIN_CTRL_Abstract
{
    private $userService;
    private $ajaxResponderUrl;

    public function __construct()
    {
        $this->userService = BOL_UserService::getInstance();

        $this->ajaxResponderUrl = MT::getRouter()->urlFor("ADMIN_CTRL_RestrictedUsernames", "ajaxResponder");

        parent::__construct();
    }

    public function index( $params = array() )
    {
        $userService = BOL_UserService::getInstance();

        $language = MT::getLanguage();

        $this->setPageHeading($language->text('admin', 'restrictedusernames'));

        $this->setPageHeadingIconClass('mt_ic_script');

        $restrictedUsernamesForm = new Form('restrictedUsernamesForm');
        $restrictedUsernamesForm->setId('restrictedUsernamesForm');

        $username = new TextField('restrictedUsername');
        $username->addAttribute('class', 'mt_text');
        $username->addAttribute('style', 'width: auto;');
        $username->setRequired();
        $username->setLabel($language->text('admin', 'restrictedusernames_username_label'));

        $restrictedUsernamesForm->addElement($username);

        $submit = new Submit('addUsername');
        $submit->addAttribute('class', 'mt_button');
        $submit->setValue($language->text('admin', 'restrictedusernames_add_username_button'));

        $restrictedUsernamesForm->addElement($submit);

        $this->addForm($restrictedUsernamesForm);

        $this->assign('restricted_list', $this->userService->getRestrictedUsernameList());

        if ( MT::getRequest()->isPost() )
        {
            if ( $restrictedUsernamesForm->isValid($_POST) )
            {
                $data = $restrictedUsernamesForm->getValues();

                $username = $this->userService->getRestrictedUsername($data['restrictedUsername']);

                if ( empty($username) )
                {
                    $username = new BOL_RestrictedUsernames();

                    $username->setRestrictedUsername($data['restrictedUsername']);

                    $this->userService->addRestrictedUsername($username);

                    MT::getFeedback()->info($language->text('admin', 'restrictedusernames_username_added'));
                    $this->redirect();
                }
                else
                {
                    MT::getFeedback()->warning($language->text('admin', 'restrictedusernames_username_already_exists'));
                }
            }
        }
    }

    public function delete()
    {
        $restrictedUsernamesService = BOL_RestrictedUsernamesDao::getInstance();
        $restrictedUsernamesService->deleteRestrictedUsername($_GET['username']);

        $language = MT::getLanguage();
        MT::getFeedback()->info($language->text('admin', 'restrictedusernames_username_deleted'));

        $this->redirect(MT::getRouter()->urlForRoute('admin_restrictedusernames'));
    }
}
