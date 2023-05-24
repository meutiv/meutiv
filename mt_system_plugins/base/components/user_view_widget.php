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
 * @author Podyachev Evgeny, Sergey Pryadkin <joker.OW2@gmail.com, GiperProger@gmail.com>
 * @package mt_system_plugins.base.components
 * @since 1.8.5
 */

class BASE_CMP_UserViewWidget extends BASE_CLASS_Widget
{
    const USER_VIEW_PRESENTATION_TABS = 'tabs';

    const USER_VIEW_PRESENTATION_TABLE = 'table';

    /**
     * @param BASE_CLASS_WidgetParameter $params
     * @return \BASE_CMP_UserViewWidget
     */
    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

        $userId = $params->additionalParamList['entityId'];

        $viewerId = MT::getUser()->getId();

        $ownerMode = $userId == $viewerId;
        $adminMode = MT::getUser()->isAdmin() || MT::getUser()->isAuthorized('base');
        $isSuperAdmin = BOL_AuthorizationService::getInstance()->isSuperModerator($userId);

        $user = BOL_UserService::getInstance()->findUserById($userId);
        $accountType = $user->accountType;
        $questionService = BOL_QuestionService::getInstance();

        $questions = self::getUserViewQuestions($userId, $adminMode);
        
        if ( empty($questions['questions']) && $adminMode )
        {
            $list = BOL_QuestionService::getInstance()->getRequiredQuestionsForNewAccountType();
            
            $questions = self::getUserViewQuestions($userId, $adminMode, array_keys($list) );
        }

        $sectionsHtml = $questions['sections'];

        $sections = array_keys($sectionsHtml);

        $template = MT::getPluginManager()->getPlugin('base')->getViewDir() . 'components' . DS . 'user_view_widget_table.html';

        $userViewPresntation = MT::getConfig()->getValue('base', 'user_view_presentation');

        if ( $userViewPresntation === self::USER_VIEW_PRESENTATION_TABS )
        {
            $template = MT::getPluginManager()->getPlugin('base')->getViewDir() . 'components' . DS . 'user_view_widget_tabs.html';

            MT::getDocument()->addOnloadScript(" view = new UserViewWidget(); ");

            $jsDir = MT::getPluginManager()->getPlugin("base")->getStaticJsUrl();
            MT::getDocument()->addScript($jsDir . "user_view_widget.js");

            $this->addMenu($sections);
        }

        $script = ' $(".profile_hidden_field").hover(function(){MT.showTip($(this), {timeout:150, show: "'.MT::getLanguage()->text('base', 'base_invisible_profile_field_tooltip').'"})}, function(){MT.hideTip($(this))});';

        MT::getDocument()->addOnloadScript($script);

        $this->setTemplate($template);

        $accountTypes = $questionService->findAllAccountTypes();

        if ( !isset($sections[0]) )
        {
            $sections[0] = 0;
        }

        if ( count($accountTypes) > 1 )
        {
            if ( !isset($questionArray[$sections[0]]) )
            {
                $questionArray[$sections[0]] = array();
            }

            array_unshift($questionArray[$sections[0]], array('name' => 'accountType', 'presentation' => 'select'));
            $questionData[$userId]['accountType'] = $questionService->getAccountTypeLang($accountType);
        }

        if ( !isset($questionData[$userId]) )
        {
            $questionData[$userId] = array();
        } 

        $this->assign('firstSection', $sections[0]);
        //$this->assign('questionArray', $questionArray);
        $this->assign('sectionsHtml', $sectionsHtml);
        //$this->assign('questionData', $questionData[$userId]);
        $this->assign('ownerMode', $ownerMode);
        $this->assign('adminMode', $adminMode);
        $this->assign('superAdminProfile', $isSuperAdmin);
        $this->assign('profileEditUrl', MT::getRouter()->urlForRoute('base_edit'));

        if ( $adminMode && !$ownerMode )
        {
            $this->assign('profileEditUrl', MT::getRouter()->urlForRoute('base_edit_user_datails', array('userId' => $userId) ));
        }

        $this->assign('avatarUrl', BOL_AvatarService::getInstance()->getAvatarUrl($userId) );
        $this->assign('displayName', BOL_UserService::getInstance()->getDisplayName($userId) );
        //$this->assign('questionLabelList', $questionLabelList);
        $this->assign('userId', $userId);
    }

    public static function getStandardSettingValueList()
    {
        $language = MT::getLanguage();
        return array(
            self::SETTING_SHMT_TITLE => false,
            self::SETTING_WRAP_IN_BOX => false,
            self::SETTING_TITLE => $language->text('base', 'view_index'),
            self::SETTING_FREEZE => true
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    public function addMenu( $sections )
    {
        $menuItems = array();

        foreach ( $sections as $key => $section )
        {
            $item = new BASE_MenuItem();

            $item->setLabel(BOL_QuestionService::getInstance()->getSectionLang($section))
                ->setKey($section)
                ->setUrl('javascript://')
                ->setPrefix('menu')
                ->setOrder($key);

            if ( $key == 0 )
            {
                $item->setActive(true);
            }

            $menuItems[] = $item;
            $script = '$(\'li.menu_' . $section . '\').click(function(){view.showSection(\'' . $section . '\');});';
            MT::getDocument()->addOnloadScript($script);
        }

        $this->addComponent('menu', new BASE_CMP_ContentMenu($menuItems));
    }

    public static function getUserViewQuestions( $userId, $adminMode = false, $questionNames = array(), $sectionNames = null )
    {
        $questions = BOL_UserService::getInstance()->getUserViewQuestions($userId, $adminMode, $questionNames, $sectionNames);

        $event = new MT_Event(BOL_QuestionService::EVENT_ON_COLLECT_QUESTIONS_SECTIONS_LIST, array('userId' => $userId), $questions);

        MT::getEventManager()->trigger($event);

        $questions = $event->getData();

        if ( !empty($questions['data'][$userId]) )
        {
            $data = array();
            foreach ( $questions['data'][$userId] as $key => $value )
            {
                if ( is_array($value) )
                {
                    $questions['data'][$userId][$key] = implode(', ', $value);
                }
            }
        }

        $sectionList = array();

        $userViewPresntation = MT::getConfig()->getValue('base', 'user_view_presentation');

        if ( !empty($questions['questions']) )
        {
            $sections = array_keys($questions['questions']);
            $count = 0;

            $isHidden = false;

            foreach ( $sections as $section )
            {
                if ( $userViewPresntation === self::USER_VIEW_PRESENTATION_TABS && $count != 0 )
                {
                    $isHidden = true;
                }

                $sectionQuestions = !empty($questions['questions'][$section]) ? $questions['questions'][$section] : array();
                $data = !empty($questions['data'][$userId]) ? $questions['data'][$userId] : array();
                $component = MT::getClassInstance( 'BASE_CMP_UserViewSection', $section, $sectionQuestions, $data, $questions['labels'], $userViewPresntation, $isHidden, array('userId' => $userId) );

                if ( !empty($component) )
                {
                    $sectionList[$section] = $component->render();
                }
                $count++;
            }
        }

        $questions['sections'] = $sectionList;

        return $questions;
    }
}
