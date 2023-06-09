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
 * Data Transfer Object for `base_vote` table.  
 * 
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_Vote extends MT_Entity
{
    /**
     * @var integer
     */
    public $entityId;
    /**
     * @var string
     */
    public $entityType;
    /**
     * @var integer
     */
    public $vote;
    /**
     * @var integer
     */
    public $userId;
    /**
     * @var timeStamp
     */
    public $timeStamp;

    /**
     * @return integer
     */
    public function getEntityId()
    {
        return (int) $this->entityId;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * @return integer
     */
    public function getVote()
    {
        return (int) $this->vote;
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return (int) $this->userId;
    }

    /**
     * @return integer
     */
    public function getTimeStamp()
    {
        return (int) $this->timeStamp;
    }

    /**
     * @param integer $entityId
     * @return BOL_Vote
     */
    public function setEntityId( $entityId )
    {
        $this->entityId = (int) $entityId;
        return $this;
    }

    /**
     * @param string $entityType
     * @return BOL_Vote
     */
    public function setEntityType( $entityType )
    {
        $this->entityType = trim($entityType);
        return $this;
    }

    /**
     * @param integer $vote
     * @return BOL_Vote
     */
    public function setVote( $vote )
    {
        $this->vote = (int) $vote;
        return $this;
    }

    /**
     * @param integer $userId
     * @return BOL_Vote
     */
    public function setUserId( $userId )
    {
        $this->userId = (int) $userId;
        return $this;
    }

    /**
     * @param integer $timeStamp
     * @return BOL_Vote
     */
    public function setTimeStamp( $timeStamp )
    {
        $this->timeStamp = (int) $timeStamp;
        return $this;
    }
}