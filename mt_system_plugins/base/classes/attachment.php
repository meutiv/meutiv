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
 * @since 1.0
 */
class BASE_CLASS_Attachment extends MT_Component
{
    private $uid;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct( $pluginKey, $uid, $buttonId )
    {
        parent::__construct();
        $language = MT::getLanguage();
        $this->uid = $uid;
        $previewContId = 'attch_preview_' . $this->uid;

        $params = array(
            'uid' => $uid,
            'previewId' => $previewContId,
            'buttonId' => $buttonId,
            'pluginKey' => $pluginKey,
            'addPhotoUrl' => MT::getRouter()->urlFor('BASE_CTRL_Attachment', 'addPhoto'),
            'langs' => array(
                'attchLabel' => $language->text('base', 'attch_attachment_label')
            )
        );

        $this->assign('previewId', $previewContId);
        MT::getDocument()->addScript(MT::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'attachments.js');
        MT::getDocument()->addOnloadScript("window.owPhotoAttachment['" . $uid . "'] =  new OWPhotoAttachment(" . json_encode($params) . ");");
    }
}