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
 * Widget
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mt_system_plugins.base.classes
 * @since 1.0
 */
abstract class BASE_CLASS_Widget extends MT_Component
{
    const ACCESS_GUEST = 'guest';
    const ACCESS_MEMBER = 'member';
    const ACCESS_ALL = 'all';

    const SETTING_TITLE = 'title';
    const SETTING_WRAP_IN_BOX = 'wrap_in_box';
    const SETTING_SHMT_TITLE = 'shmt_title';
    const SETTING_ICON = 'icon';
    const SETTING_TOOLBAR = 'toolbar';
    const SETTING_CAP_CONTENT = 'capContent';
    const SETTING_FREEZE = 'freeze';
    const SETTING_AVALIABLE_SECTIONS = 'avaliable_sections';
    const SETTING_ACCESS_RESTRICTIONS = 'access_restrictions';
    const SETTING_RESTRICT_VIEW = 'restrict_view';

    const PRESENTATION_NUMBER = 'number';
    const PRESENTATION_TEXT = 'text';
    const PRESENTATION_TEXTAREA = 'textarea';
    const PRESENTATION_CHECKBOX = 'checkbox';
    const PRESENTATION_SELECT = 'select';
    const PRESENTATION_HIDDEN = 'hidden';
    const PRESENTATION_CUSTOM = 'custom';

    const ICON_ADD = "mt_ic_add";
    const ICON_ALOUD = "mt_ic_aloud";
    const ICON_APP = "mt_ic_app";
    const ICON_ATTACH = "mt_ic_attach";
    const ICON_BIRTHDAY = "mt_ic_birthday";
    const ICON_BOOKMARK = "mt_ic_bookmark";
    const ICON_CALENDAR = "mt_ic_calendar";
    const ICON_CART = "mt_ic_cart";
    const ICON_CHAT = "mt_ic_chat";
    const ICON_CLOCK = "mt_ic_clock";
    const ICON_COMMENT = "mt_ic_comment";
    const ICON_CUT = "mt_ic_cut";
    const ICON_DASHBOARD = "mt_ic_dashboard";
    const ICON_DELETE = "mt_ic_delete";
    const ICON_DOWN_ARROW = "mt_ic_down_arrow";
    const ICON_EDIT = "mt_ic_edit";
    const ICON_FEMALE = "mt_ic_female";
    const ICON_FILE = "mt_ic_file";
    const ICON_FILES = "mt_ic_files";
    const ICON_FLAG = "mt_ic_flag";
    const ICON_FOLDER = "mt_ic_folder";
    const ICON_FORUM = "mt_ic_forum";
    const ICON_FRIENDS = "mt_ic_friends";
    const ICON_GEAR_WHEEL = "mt_ic_gear_wheel";
    const ICON_HEART = "mt_ic_heart";
    const ICON_HELP = "mt_ic_help";
    const ICON_HOUSE = "mt_ic_house";
    const ICON_INFO = "mt_ic_info";
    const ICON_KEY = "mt_ic_key";
    const ICON_LEFT_ARROW = "mt_ic_left_arrow";
    const ICON_LENS = "mt_ic_lens";
    const ICON_LINK = "mt_ic_link";
    const ICON_LOCK = "mt_ic_lock";
    const ICON_MAIL = "mt_ic_mail";
    const ICON_MALE = "mt_ic_male";
    const ICON_MOBILE = "mt_ic_mobile";
    const ICON_MODERATOR = "mt_ic_moderator";
    const ICON_MONITOR = "mt_ic_monitor";
    const ICON_MOVE = "mt_ic_move";
    const ICON_MUSIC = "mt_ic_music";
    const ICON_NEW = "mt_ic_new";
    const ICON_OK = "mt_ic_ok";
    const ICON_ONLINE = "mt_ic_online";
    const ICON_PICTURE = "mt_ic_picture";
    const ICON_PLUGIN = "mt_ic_plugin";
    const ICON_PUSH_PIN = "mt_ic_push_pin";
    const ICON_REPLY = "mt_ic_reply";
    const ICON_RIGHT_ARROW = "mt_ic_right_arrow";
    const ICON_RSS = "mt_ic_rss";
    const ICON_SAVE = "mt_ic_save";
    const ICON_SCRIPT = "mt_ic_script";
    const ICON_SERVER = "mt_ic_server";
    const ICON_STAR = "mt_ic_star";
    const ICON_TAG = "mt_ic_tag";
    const ICON_TRASH = "mt_ic_trash";
    const ICON_UNLOCK = "mt_ic_unlock";
    const ICON_UP_ARROW = "mt_ic_up_arrow";
    const ICON_UPDATE = "mt_ic_update";
    const ICON_USER = "mt_ic_user";
    const ICON_VIDEO = "mt_ic_video";
    const ICON_WARNING = "mt_ic_warning";
    const ICON_WRITE = "mt_ic_write";


    private static $placeData = array();

    final public static function getPlaceData()
    {
        return self::$placeData;
    }

    final public static function setPlaceData( $placeData )
    {
        self::$placeData = $placeData;
    }



    public static function getSettingList()
    {
        return array();
    }

    public static function validateSettingList( $settingList )
    {

    }

    public static function processSettingList( $settingList, $place, $isAdmin )
    {
        if ( isset($settingList['title']) )
        {
            $settingList['title'] = UTIL_HtmlTag::stripJs($settingList['title']);
        }

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        return array();
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    private $runtimeSettings = array();

    public function setSettingValue( $setting, $value )
    {
        $this->runtimeSettings[$setting] = $value;
    }

    public function getRunTimeSettingList()
    {
        return $this->runtimeSettings;
    }
}

class WidgetSettingValidateException extends Exception
{
    private $fieldName;

    public function __construct( $message, $fieldName = null )
    {
        parent::__construct($message);

        $this->fieldName = trim($fieldName);
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }
}
