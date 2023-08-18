<?php

/**
 * Data Transfer Object for `base_search_entity` table.
 *
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_SearchEntity extends MT_Entity
{
    /**
     * Entity type
     * @var string
     */
    public $entityType;

    /**
     * Entity id
     * @var string
     */
    public $entityId;

    /**
     * Text
     * @var string
     */
    public $text;

    /**
     * Status
     * @var integer
     */
    public $status;

    /**
     * TimeStamp
     * @var integer
     */
    public $timeStamp;

    /**
     * Activated
     * @var integer
     */
    public $activated;
}