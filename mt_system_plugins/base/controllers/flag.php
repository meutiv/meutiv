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
 * @author Aybat Duyshokov <duyshokov@gmail.com>, Kambalin Sergey <greyexpert@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BASE_CTRL_Flag extends MT_ActionController
{

    public function flag()
    {
        if ( !MT::getUser()->isAuthenticated() )
        {
            exit(json_encode(array(
                'result' => 'success',
                'js' => 'MT.error(' . json_encode(MT::getLanguage()->text('base', 'sing_in_to_flag')) . ')'
            )));
        }

        $entityType = $_POST["entityType"];
        $entityId = $_POST["entityId"];
        
        $data = BOL_ContentService::getInstance()->getContent($entityType, $entityId);
        $ownerId = $data["userId"];
        $userId = MT::getUser()->getId();
        
        if ( $ownerId == $userId )
        {
            exit(json_encode(array(
                'result' => 'success',
                'js' => 'MT.error("' . MT::getLanguage()->text('base', 'flag_own_content_not_accepted') . '")'
            )));
        }

        $service = BOL_FlagService::getInstance();
        $service->addFlag($entityType, $entityId, $_POST['reason'], $userId);
                
        exit(json_encode(array(
            'result' => 'success',
            'js' => 'MT.info("' . MT::getLanguage()->text('base', 'flag_accepted') . '")'
        )));
    }

    public function delete( $params )
    {
        if ( !(MT::getUser()->isAdmin() || BOL_AuthorizationService::getInstance()->isModerator()) )
        {
            throw new Redirect403Exception;
        }

        BOL_FlagService::getInstance()->deleteFlagById($params['id']);
        MT::getFeedback()->info(MT::getLanguage()->text('base', 'flags_deleted'));
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
}