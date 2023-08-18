<?php

class BASE_CMP_ConsoleInvitations extends BASE_CMP_ConsoleDropdownList
{
    public function __construct()
    {
        $label = MT::getLanguage()->text('base', 'console_item_invitations_label');

        parent::__construct( $label, 'invitation' );

        $this->addClass('mt_invitation_list');
    }

    public function initJs()
    {
        parent::initJs();

        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('MT.Invitation = new MT_Invitation({$key}, {$params});', array(
            'key' => $this->getKey(),
            'params' => array(
                'rsp' => MT::getRouter()->urlFor('BASE_CTRL_Invitation', 'ajax')
            )
        ));
        
        MT::getDocument()->addOnloadScript($js);
    }
}