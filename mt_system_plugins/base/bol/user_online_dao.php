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
 * Data Access Object for `user_online` table.  
 * 
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_UserOnlineDao extends MT_BaseDao
{
    const USER_ID = 'userId';
    const ACTIVITY_STAMP = 'activityStamp';
    const CONTEXT = 'context';
    const CONTEXT_VAL_DESKTOP = 1;
    const CONTEXT_VAL_MOBILE = 2;
    const CONTEXT_VAL_API = 4;
    const CONTEXT_VAL_CLI = 8;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Singleton instance.
     * 
     * @var BOL_UserOnlineDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_UserOnlineDao
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
     * @see MT_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_UserOnline';
    }

    /**
     * @see MT_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return MT_DB_PREFIX . 'base_user_online';
    }

    /**
     * 
     * @param integer $userId
     * @return BOL_UserOnline
     */
    public function findByUserId( $userId )
    {
        $example = new MT_Example();
        $example->andFieldEqual(self::USER_ID, (int) $userId);

        return $this->findObjectByExample($example);
    }

    public function findOnlineUserIdListFromIdList( $idList )
    {
        if ( empty($idList) )
        {
            return array();
        }

        $query = "SELECT * FROM `" . $this->getTableName() . "` WHERE `" . self::USER_ID . "` IN (" . $this->dbo->mergeInClause($idList) . ")";

        return $this->dbo->queryForList($query);
    }

    public function deleteExpired( $timestamp )
    {
        $query = "DELETE FROM `{$this->getTableName()}` WHERE `activityStamp` < ?";

        $this->dbo->query($query, array($timestamp));
    }
}
