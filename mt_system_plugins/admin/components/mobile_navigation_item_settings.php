<?php

class ADMIN_CMP_MobileNavigationItemSettings extends MT_Component
{
    public function __construct( $itemKey ) 
    {
        parent::__construct();
        
        list($prefix, $key) = explode(':', $itemKey);
        $menuItem = BOL_NavigationService::getInstance()->findMenuItem($prefix, $key);
        
        $custom = $menuItem->getPrefix() == BOL_MobileNavigationService::MENU_PREFIX;
        
        $form = new ADMIN_CLASS_MobileNavigationItemSettingsForm($menuItem, $custom);
        $this->addForm($form);
        
        $settings = BOL_MobileNavigationService::getInstance()->getItemSettings($menuItem);
        $this->assign("settings", $settings);
        $this->assign("custom", $custom);
        
        $js = UTIL_JsGenerator::composeJsString('owForms[{$formName}].bind("success", function(r) {
            _scope.callBack(r);
            _scope.floatBox.close();
        })', array(
            "formName" => $form->getName()
        ));
        
        MT::getDocument()->addOnloadScript($js);
    }
}