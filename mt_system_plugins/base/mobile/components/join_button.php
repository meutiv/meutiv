<?php

class BASE_MCMP_JoinButton extends BASE_CMP_JoinButton
{
    public function __construct( $params = array() )
    {
        parent::__construct();
        $this->setTemplate(MT::getPluginManager()->getPlugin('base')->getMobileCmpViewDir().'join_button.html');
    }
}