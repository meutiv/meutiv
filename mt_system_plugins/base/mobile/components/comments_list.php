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
 * @package ow.mt_system_plugins.base.components
 * @since 1.0
 */
class BASE_MCMP_CommentsList extends BASE_CMP_CommentsList
{
    /**
     * Constructor.
     *
     * @param string $entityType
     * @param integer $entityId
     * @param integer $page
     * @param string $displayType
     */
    public function __construct( BASE_CommentsParams $params, $id )
    {
        parent::__construct($params, $id);
        $this->setTemplate(MT::getPluginManager()->getPlugin('base')->getMobileCmpViewDir() . 'comments_list.html');
    }

    protected function init()
    {
        $commentList = $this->commentService->findCommentList($this->params->getEntityType(), $this->params->getEntityId(), null, $this->params->getCommentCountOnPage());
        $commentList = array_reverse($commentList);
        $commentList = $this->processList($commentList);
        $this->assign('comments', $commentList);
        $countToLoad = $this->commentCount - $this->params->getCommentCountOnPage();
        $this->assign('countToLoad', $countToLoad);
        

        static $dataInit = false;

        if ( !$dataInit )
        {
            $staticDataArray = array(
                'respondUrl' => MT::getRouter()->urlFor('BASE_CTRL_Comments', 'getMobileCommentList'),
                'delUrl' => MT::getRouter()->urlFor('BASE_CTRL_Comments', 'deleteComment'),
                'delAtchUrl' => MT::getRouter()->urlFor('BASE_CTRL_Comments', 'deleteCommentAtatchment'),
                'delConfirmMsg' => MT::getLanguage()->text('base', 'comment_delete_confirm_message'),
            );
            MT::getDocument()->addOnloadScript("window.owCommentListCmps.staticData=" . json_encode($staticDataArray) . ";");
            $dataInit = true;
        }
        
        $jsParams = json_encode(
                array(
                    'totalCount' => $this->commentCount,
                    'contextId' => $this->cmpContextId,
                    'displayType' => $this->params->getDisplayType(),
                    'entityType' => $this->params->getEntityType(),
                    'entityId' => $this->params->getEntityId(),
                    'commentIds' => $this->commentIdList,
                    'pluginKey' => $this->params->getPluginKey(),
                    'ownerId' => $this->params->getOwnerId(),
                    'commentCountOnPage' => $this->params->getCommentCountOnPage(),
                    'cid' => $this->id,
                    'loadCount' => $this->commentService->getConfigValue(BOL_CommentService::CONFIG_MB_COMMENTS_COUNT_TO_LOAD)
                )
        );

        MT::getDocument()->addOnloadScript(
            "window.owCommentListCmps.items['$this->id'] = new OwMobileCommentsList($jsParams);
            window.owCommentListCmps.items['$this->id'].init();"
        );
    }
}
