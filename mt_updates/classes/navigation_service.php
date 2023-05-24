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
 * The service class helps to manage menus and documents. 
 * 
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class UPDATE_NavigationService
{
    const MAIN = MT_Navigation::MAIN;
    const BOTTOM = MT_Navigation::BOTTOM;

    const VISIBLE_FOR_GUEST = MT_Navigation::VISIBLE_FOR_GUEST;
    const VISIBLE_FOR_MEMBER = MT_Navigation::VISIBLE_FOR_MEMBER;
    const VISIBLE_FOR_ALL = MT_Navigation::VISIBLE_FOR_ALL;

    /**
     * @var MT_Navigation
     */
    private $navigation;
    /**
     * @var UPDATE_NavigationService
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return UPDATE_NavigationService
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->navigation = MT_Navigation::getInstance();
    }

    /**
     * Adds menu items to global menu system.
     *
     * @param string $menuType
     * @param string $routeName
     * @param string $prefix
     * @param string $key
     * @param string $visibleFor
     */
    public function addMenuItem( $menuType, $routeName, $prefix, $key, $visibleFor = self::VISIBLE_FOR_ALL )
    {
        $this->navigation->addMenuItem($menuType, $routeName, $prefix, $key, $visibleFor);
    }

    /**
     * Deletes menu item.
     *
     * @param string $prefix
     * @param string $key
     */
    public function deleteMenuItem( $prefix, $key )
    {
        $this->navigation->deleteMenuItem($prefix, $key);
    }
    
    /**
     * Saves and updates menu items.
     * 
     * @param BOL_MenuItem $menuItem
     */
    public function saveMenuItem( BOL_MenuItem $menuItem )
    {
        BOL_NavigationService::getInstance()->saveMenuItem($menuItem);
    }
}