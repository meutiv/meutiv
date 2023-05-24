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
 * Singleton. Language Data Access Object
 *
 * @author Aybat Duyshokov <duyshokov@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_LanguageDao extends MT_BaseDao
{

    /**
     * Class constructor
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Class instance
     *
     * @var BOL_LanguageDao
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return BOL_LanguageDao
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
            self::$classInstance = new self();

        return self::$classInstance;
    }

    /**
     * @see MT_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_Language';
    }

    /**
     * @see MT_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return MT_DB_PREFIX . 'base_language';
    }

    /**
     * Enter description here...
     *
     * @param string $tag
     * @return BOL_Language
     */
    public function findByTag( $tag )
    {
        $example = new MT_Example();
        $example->andFieldEqual('tag', trim($tag));

        return $this->findObjectByExample($example);
    }

    public function findMaxOrder()
    {
        return $this->dbo->queryForColumn('SELECT MAX(`order`) FROM ' . $this->getTableName());
    }

    public function getCurrent()
    {
        $ex = new MT_Example();

        $ex->setOrder('`order` ASC')->setLimitClause(0, 1);

        return $this->findObjectByExample($ex);
    }

    public function countActiveLanguages()
    {
        $ex = new MT_Example();
        $ex->andFieldEqual('status', 'active');

        return $this->countByExample($ex);
    }

    public function findActiveList()
    {
        $ex = new MT_Example();
        $ex->andFieldEqual('status', 'active');

        return $this->findListByExample($ex);
    }
}