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
 * Data Access Object for `base_authorization_permission` table.
 *
 * @author Nurlan Dzhumakaliev <nurlanj@live.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_AuthorizationPermissionDao extends MT_BaseDao
{

    /**
     * Constructor.
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Singleton instance.
     *
     * @var BOL_AuthorizationPermissionDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_AuthorizationPermissionDao
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
        return 'BOL_AuthorizationPermission';
    }

    /**
     * @see MT_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return MT_DB_PREFIX . 'base_authorization_permission';
    }

    /**
     * @param $actionId
     * @param $roles
     * @throws InvalidArgumentException
     * @return
     *
     */
    public function findFirstIdForRoles( $actionId, $roles )
    {
        if ( $actionId === null || (int) $actionId < 1 )
        {
            throw new InvalidArgumentException('actionId must not be null');
        }

        if ( $roles === null || count($roles) < 1 )
        {
            return null;
        }

        $ex = new MT_Example();
        $ex->andFieldEqual('actionId', $actionId);
        $ex->andFieldInArray('roleId', $roles);
        $ex->setLimitClause(1, 1);

        return $this->findIdByExample($ex);
    }

    public function deleteAll()
    {
        $this->clearCache();
        $this->dbo->delete('TRUNCATE TABLE ' . $this->getTableName());
    }

    /**
     *
     * @param int $roleId
     * @param int $actionId
     * @return BOL_AuthorizationPermission
     */
    public function findByRoleIdAndActionId( $roleId, $actionId )
    {
        if ( (int) $roleId < 1 || (int) $actionId < 1 )
        {
            throw new InvalidArgumentException('actionId and roleId must not be null');
        }

        $ex = new MT_Example();
        $ex->andFieldEqual('roleId', $roleId);
        $ex->andFieldEqual('actionId', $actionId);

        return $this->findIdByExample($ex);
    }

    public function deleteByActionId( $actionId )
    {
        $this->clearCache();
        $actionId = (int)$actionId;
        $example = new MT_Example();
        $example->andFieldEqual('actionId', $actionId);

        $this->deleteByExample($example);
    }
    
    protected function clearCache()
    {
        MT::getCacheManager()->clean(array(BOL_AuthorizationActionDao::CACHE_TAG_AUTHORIZATION));
    }

    public function findAll( $cacheLifeTime = 0, $tags = array() )
    {
        return parent::findAll(3600 * 24, array(BOL_AuthorizationActionDao::CACHE_TAG_AUTHORIZATION, MT_CacheManager::TAG_OPTION_INSTANT_LOAD));
    }
}