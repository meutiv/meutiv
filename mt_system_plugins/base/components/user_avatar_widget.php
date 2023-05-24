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
 * User avatar widget
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.mt_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_UserAvatarWidget extends BASE_CLASS_Widget
{
    public function __construct( BASE_CLASS_WidgetParameter $paramObj )
    {
        parent::__construct();

        $avatarService = BOL_AvatarService::getInstance();

        $viewerId = MT::getUser()->getId();

        $userId = $paramObj->additionalParamList['entityId'];

        $owner = false;
        
        if ( $viewerId == $userId )
        {
            $owner = true;
            

            $label = MT::getLanguage()->text('base', 'avatar_change');

            $script =
            '$("#avatar-change").click(function(){
                document.avatarFloatBox = MT.ajaxFloatBox(
                    "BASE_CMP_AvatarChange",
                    { params : { step : 1 } },
                    { width : 749, title: ' . json_encode($label) . '}
                );
            });

            MT.bind("base.avatar_cropped", function(data){
                if ( data.bigUrl != undefined ) {
                    $("#avatar_console_image").css({ "background-image" : "url(" + data.bigUrl + ")" });
                }

                if ( data.modearationStatus )
                {
                    if ( data.modearationStatus != "active" )
                    {
                        $(".mt_avatar_pending_approval").show();
                    }
                    else 
                    {
                        $(".mt_avatar_pending_approval").hide();
                    }
                }
            });
            ';

            MT::getDocument()->addOnloadScript($script);
        }
        
        $isModerator = (MT::getUser()->isAuthorized('base') || MT::getUser()->isAdmin());
        
        $this->assign('owner', $owner);
        $this->assign('isModerator', $isModerator);
        
        $avatarDto = $avatarService->findByUserId($userId);
        
        $this->assign('hasAvatar', !empty($avatarDto));
        $moderation = false;

        // approve button
        if ( $isModerator && !empty($avatarDto) && $avatarDto->status == BOL_ContentService::STATUS_APPROVAL )
        {
            $moderation = true;
            
            $script = ' window.avartar_arrove_request = false;
            $("#avatar-approve").click(function(){
            
                if ( window.avartar_arrove_request == true )
                {
                    return;
                }
                
                window.avartar_arrove_request = true;
                
                $.ajax({
                    "type": "POST",
                    "url": '.json_encode(MT::getRouter()->urlFor('BASE_CTRL_Avatar', 'ajaxResponder')).',
                    "data": {
                        \'ajaxFunc\' : \'ajaxAvatarApprove\',
                        \'avatarId\' : '.((int)$avatarDto->id).'
                    },
                    "success": function(data){
                        if ( data.result == true )
                        {
                            if ( data.message )
                            {
                                MT.info(data.message);
                            }
                            else
                            {
                                MT.info('.json_encode(MT::getLanguage()->text('base', 'avatar_has_been_approved')).');
                            }
                            
                            $("#avatar-approve").remove();
                            $(".mt_avatar_pending_approval").hide();
                        }
                        else
                        {
                            if ( data.error )
                            {
                                MT.info(data.error);
                            }
                        }
                    },
                    "complete": function(){
                        window.avartar_arrove_request = false;
                    },
                    "dataType": "json"
                });
            }); ';

            MT::getDocument()->addOnloadScript($script);
        }
        
        $avatar = $avatarService->getAvatarUrl($userId, 2, null, false, !($moderation || $owner));
        $this->assign('avatar', $avatar ? $avatar : $avatarService->getDefaultAvatarUrl(2));
        $roles = BOL_AuthorizationService::getInstance()->getRoleListOfUsers(array($userId));
        $this->assign('role', !empty($roles[$userId]) ? $roles[$userId] : null);

        $userService = BOL_UserService::getInstance();

        $showPresence = true;
        // Check privacy permissions 
        $eventParams = array(
            'action' => 'base_view_my_presence_on_site',
            'ownerId' => $userId,
            'viewerId' => MT::getUser()->getId()
        );
        try
        {
            MT::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);
        }
        catch ( RedirectException $e )
        {
            $showPresence = false;
        }

        $this->assign('isUserOnline', ($userService->findOnlineUserById($userId) && $showPresence));
        $this->assign('userId', $userId);

        $this->assign('avatarSize', MT::getConfig()->getValue('base', 'avatar_big_size'));
        
        $this->assign('moderation', $moderation);
        $this->assign('avatarDto', $avatarDto);
        
        MT::getLanguage()->addKeyForJs('base', 'avatar_has_been_approved');
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => MT::getLanguage()->text('base', 'avatar_widget'),
            self::SETTING_SHMT_TITLE => false,
            self::SETTING_ICON => self::ICON_PICTURE,
            self::SETTING_FREEZE => true
        );
    }
}