<?php

class ADMIN_CMP_MobileNavigationItem extends MT_Component
{
    public function __construct( $options ) 
    {
        parent::__construct();
        
        $this->assign("item", $options);
    }
}
