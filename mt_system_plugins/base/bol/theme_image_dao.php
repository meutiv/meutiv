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
 * Data Access Object for `base_theme_image` table.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_ThemeImageDao extends MT_BaseDao
{
    const FILENAME = 'filename';

    /**
     * Singleton instance.
     *
     * @var BOL_ThemeImageDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_ThemeImageDao
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
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * @see MT_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_ThemeImage';
    }

    /**
     * @see MT_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return MT_DB_PREFIX . 'base_theme_image';
    }

    /**
     * @return array<BOL_ThemeImage>
     */
    public function findGraphics()
    {
        $example = new MT_Example();
        $example->setOrder('`id` DESC');

        return $this->findListByExample($example);
    }

    /**
     * @param MT_Example$example
     * @param array $params
     */
    private function applyDatesBetweenFilter(MT_Example $example, $params)
    {
        if ( isset($params['start'], $params['end']) )
        {
            $start = $params['start'];
            $end = $params['end'];
            if ( !is_null($start) && !is_null($end) )
            {
                $example->andFieldBetween('addDatetime', $start, $end);
            }
        }
    }

    /**
     * @param MT_Example $example
     * @param array $params
     */
    private function applyLimitClause(MT_Example $example, $params)
    {
        if ( isset($params['page'], $params['limit']) && !is_null($params['page']) && !is_null($params['limit']) )
        {
            $page = $params['page'];
            $limit = $params['limit'];
            $first = ( $page - 1 ) * $limit;
            $example->setLimitClause($first, $limit);
        }
    }

    /**
     * @param array $params
     * @return array <BOL_ThemeImage>
     */
    public function filterGraphics($params)
    {
        $example = new MT_Example();
        $this->applyDatesBetweenFilter($example, $params);
        $this->applyLimitClause($example, $params);

        $example->setOrder('`id` DESC');
        return $this->findListByExample($example);
    }

    /**
     * @param int $id
     * @param array $params
     * @return array <BOL_ThemeImage>
     */
    public function getPrevImageList($id, $params)
    {
        $example = new MT_Example();
        $this->applyDatesBetweenFilter($example, $params);
        $this->applyLimitClause($example, $params);
        $example->andFieldGreaterThan('id', $id);
        $example->setOrder('`id` DESC');
        return $this->findListByExample($example);
    }

    /**
     * @param int $id
     * @param array $params
     * @return array <BOL_ThemeImage>
     */
    public function getNextImageList($id, $params)
    {
        $example = new MT_Example();
        $this->applyDatesBetweenFilter($example, $params);
        $this->applyLimitClause($example, $params);
        $example->andFieldLessThan('id', $id);
        $example->setOrder('`id` DESC');
        return $this->findListByExample($example);
    }
}
