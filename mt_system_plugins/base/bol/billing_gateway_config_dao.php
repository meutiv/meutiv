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
 * Data Access Object for `base_billing_gateway_config` table.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_BillingGatewayConfigDao extends MT_BaseDao
{

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
     * @var BOL_BillingGatewayConfigDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class
     *
     * @return BOL_BillingGatewayConfigDao
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
        return 'BOL_BillingGatewayConfig';
    }

    /**
     * @see MT_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return MT_DB_PREFIX . 'base_billing_gateway_config';
    }

    public function getConfig( $gatewayKey, $name )
    {
        if ( !mb_strlen($gatewayKey) || !mb_strlen($name) )
        {
            return null;
        }
        
        $gateway = BOL_BillingGatewayDao::getInstance()->findByKey($gatewayKey);

        if ( $gateway )
        {
            $example = new MT_Example();
            $example->andFieldEqual('gatewayId', $gateway->id);
            $example->andFieldEqual('name', $name);

            return $this->findObjectByExample($example);
        }

        return null;
    }

    public function getConfigValue( $gatewayKey, $name )
    {
        if ( !mb_strlen($gatewayKey) || !mb_strlen($name) )
        {
            return null;
        }

        $gateway = BOL_BillingGatewayDao::getInstance()->findByKey($gatewayKey);

        if ( $gateway )
        {
            $example = new MT_Example();
            $example->andFieldEqual('gatewayId', $gateway->id);
            $example->andFieldEqual('name', $name);

            $conf = $this->findObjectByExample($example);
            return $conf ? $conf->value : null;
        }

        return null;
    }

    public function setConfigValue( $gatewayKey, $name, $value )
    {
        if ( !mb_strlen($gatewayKey) || !mb_strlen($name) )
        {
            return false;
        }

        $config = $this->getConfig($gatewayKey, $name);

        if ( $config )
        {
            $config->value = $value;
            $this->save($config);

            return true;
        }

        return false;
    }
    
    public function addConfig( $gatewayKey, $name, $value )
    {
        if ( !mb_strlen($gatewayKey) || !mb_strlen($name) )
        {
            return false;
        }
        
        $gateway = BOL_BillingGatewayDao::getInstance()->findByKey($gatewayKey);

        if ( $gateway )
        {
            $config = new BOL_BillingGatewayConfig();
            $config->gatewayId = $gateway->id;
            $config->name = $name;
            $config->value = $value;
            
            $this->save($config);
            
            return true;
        }
        
        return false;
    }
    
    public function deleteConfig( $gatewayKey, $name )
    {
        if ( !mb_strlen($gatewayKey) || !mb_strlen($name) )
        {
            return false;
        }
        
        $config = BOL_BillingGatewayConfigDao::getInstance()->getConfig($gatewayKey, $name);

        if ( $config )
        {
            $this->deleteById($config->id);
            
            return true;
        }
        
        return false;
    }
}