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
 * Widget Settings
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mt_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_ComponentSettings extends MT_Component
{
    /**
     * Component default settings
     *
     * @var array
     */
    private $defaultSettingList = array();
    /**
     * Component default settings
     *
     * @var array
     */
    private $componentSettingList = array();
    private $standardSettingValueList = array();
    private $hiddenFieldList = array();
    private $access;

    private $uniqName;

    /**
     * Class constructor
     *
     * @param array $menuItems
     */
    public function __construct( $uniqName, array $componentSettings = array(), array $defaultSettings = array(), $access = null )
    {
        parent::__construct();

        $this->componentSettingList = $componentSettings;
        $this->defaultSettingList = $defaultSettings;
        $this->uniqName = $uniqName;
        $this->access = $access;
        
        $tpl = MT::getPluginManager()->getPlugin("base")->getCmpViewDir() . "component_settings.html";
        $this->setTemplate($tpl);
    }

    public function setStandardSettingValueList( $valueList )
    {
        $this->standardSettingValueList = $valueList;
    }

    protected function makeSettingList( $defaultSettingList )
    {
        $settingValues = $this->standardSettingValueList;
        foreach ( $defaultSettingList as $name => $value )
        {
            $settingValues[$name] = $value;
        }

        return $settingValues;
    }

    public function markAsHidden( $settingName )
    {
        $this->hiddenFieldList[] = $settingName;
    }

    /**
     * @see MT_Renderable::onBeforeRender()
     *
     */
    public function onBeforeRender()
    {
        $settingValues = $this->makeSettingList($this->defaultSettingList);

        $this->assign('values', $settingValues);

        $this->assign('avaliableIcons', IconCollection::allWithLabel());

        foreach ( $this->componentSettingList as $name => & $setting )
        {
            if ( $setting['presentation'] == BASE_CLASS_Widget::PRESENTATION_HIDDEN )
            {
                unset($this->componentSettingList[$name]);
                continue;
            }

            if ( isset($settingValues[$name]) )
            {
                $setting['value'] = $settingValues[$name];
            }

            if ( $setting['presentation'] == BASE_CLASS_Widget::PRESENTATION_CUSTOM )
            {
                if ( !$this->validateRenderCallback($setting['render']) )
                {
                    throw new LogicException('PHP render callable is not valid!');
                }

                $setting['markup'] = call_user_func($setting['render'], $this->uniqName, $name, empty($setting['value']) ? null : $setting['value']);
            }

            $setting['display'] = !empty($setting['display']) ? $setting['display'] : 'table';
        }

        $this->assign('settings', $this->componentSettingList);


        $authorizationService = BOL_AuthorizationService::getInstance();

        $roleList = array();
        $isModerator = MT::getUser()->isAuthorized('base');
        
        if ( $this->access == BASE_CLASS_Widget::ACCESS_GUEST || !$isModerator )
        {
            $this->markAsHidden(BASE_CLASS_Widget::SETTING_RESTRICT_VIEW);
        }
        else
        {
            $roleList = $authorizationService->findNonGuestRoleList();

            if ( $this->access == BASE_CLASS_Widget::ACCESS_ALL )
            {
                $guestRoleId = $authorizationService->getGuestRoleId();
                $guestRole = $authorizationService->getRoleById($guestRoleId);
                array_unshift($roleList, $guestRole);
            }
        }

        $this->assign('roleList', $roleList);

        $this->assign('hidden', $this->hiddenFieldList);
    }

    protected function validateRenderCallback($callable)
    {
        $regexClassName = '[a-z0-9]+(_CMP_|_MCMP_)[a-z0-9]+';
        $regexMethodName = '\w+';

        if ( is_string($callable) )
        {
            return preg_match("/\A{$regexClassName}::{$regexMethodName}\z/i", $callable);
        }
        else if ( is_array($callable) )
        {
            return
                count($callable) === 2
                && preg_match("/\A{$regexClassName}\z/i", is_object($callable[0]) ? get_class($callable[0]) : $callable[0])
                && preg_match("/\A{$regexMethodName}\z/i", $callable[1]);
        }
        else
        {
            return is_callable($callable);
        }
    }
}

class IconCollection
{
    private static $all = array(
        "mt_ic_add",
        "mt_ic_aloud",
        "mt_ic_app",
        "mt_ic_attach",
        "mt_ic_birthday",
        "mt_ic_bookmark",
        "mt_ic_calendar",
        "mt_ic_cart",
        "mt_ic_chat",
        "mt_ic_clock",
        "mt_ic_comment",
        "mt_ic_cut",
        "mt_ic_dashboard",
        "mt_ic_delete",
        "mt_ic_down_arrow",
        "mt_ic_edit",
        "mt_ic_female",
        "mt_ic_file",
        "mt_ic_files",
        "mt_ic_flag",
        "mt_ic_folder",
        "mt_ic_forum",
        "mt_ic_friends",
        "mt_ic_gear_wheel",
        "mt_ic_help",
        "mt_ic_heart",
        "mt_ic_house",
        "mt_ic_info",
        "mt_ic_key",
        "mt_ic_left_arrow",
        "mt_ic_lens",
        "mt_ic_link",
        "mt_ic_lock",
        "mt_ic_mail",
        "mt_ic_male",
        "mt_ic_mobile",
        "mt_ic_moderator",
        "mt_ic_monitor",
        "mt_ic_move",
        "mt_ic_music",
        "mt_ic_new",
        "mt_ic_ok",
        "mt_ic_online",
        "mt_ic_picture",
        "mt_ic_plugin",
        "mt_ic_push_pin",
        "mt_ic_reply",
        "mt_ic_right_arrow",
        "mt_ic_rss",
        "mt_ic_save",
        "mt_ic_script",
        "mt_ic_server",
        "mt_ic_star",
        "mt_ic_tag",
        "mt_ic_trash",
        "mt_ic_unlock",
        "mt_ic_up_arrow",
        "mt_ic_update",
        "mt_ic_user",
        "mt_ic_video",
        "mt_ic_warning",
        "mt_ic_write"
    );

    public static function all()
    {
        return self::$all;
    }

    public static function allWithLabel()
    {
        $out = array();

        foreach ( self::$all as $icon )
        {
            $item = array();
            $item['class'] = $icon;
            $item['label'] = ucfirst(str_replace('_', ' ', substr($icon, 6)));
            $out[] = $item;
        }

        return $out;
    }
}
