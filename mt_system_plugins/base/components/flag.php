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
 * Singleton. 'Flag' Data Access Object
 *
 * @author Aybat Duyshokov <duyshokov@gmail.com>
 * @package mt_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_Flag extends MT_Component
{

    public function __construct( $entityType, $entityId )
    {
        parent::__construct();

        $this->addForm(new FlagForm($entityType, $entityId));
    }
}

class FlagForm extends Form
{

    public function __construct( $entityType, $entityId )
    {
        parent::__construct('flag');

        $this->setAjax(true);

        $this->setAction(MT::getRouter()->urlFor('BASE_CTRL_Flag', 'flag'));

        $element = new HiddenField('entityType');
        $element->setValue($entityType);
        $this->addElement($element);
        
        $element = new HiddenField('entityId');
        $element->setValue($entityId);
        $this->addElement($element);
        

        $element = new RadioField('reason');
        $element->setOptions(array(
            'spam' => MT::getLanguage()->text('base', 'flag_spam'),
            'offence' => MT::getLanguage()->text('base', 'flag_offence'),
            'illegal' => MT::getLanguage()->text('base', 'flag_illegal'))
        );

        $flagDto = BOL_FlagService::getInstance()->findFlag($entityType, $entityId, MT::getUser()->getId());
        
        if ( $flagDto !== null )
        {
            $element->setValue($flagDto->reason);
        }

        $this->addElement($element);

        MT::getDocument()->addOnloadScript(
            "owForms['{$this->getName()}'].bind('success', function(json){
                if (json['result'] == 'success') {
                    _scope.floatBox && _scope.floatBox.close();
                    MT.addScript(json.js);
                }
            })");
    }
}