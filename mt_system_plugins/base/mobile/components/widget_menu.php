<?php

class BASE_MCMP_WidgetMenu extends MT_MobileComponent
{

    public function __construct( $items )
    {
        parent::__construct();

        $this->assign('items', $items);
        MT::getDocument()->addOnloadScript('OWM.initWidgetMenu(' . json_encode($items) . ')');
    }
}