<?php

/**
 * Data Transfer Object for `base_site_statistic` table.
 *
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_SiteStatistic extends MT_Entity
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
     * Entity count
     * @var integer
     */
    public $entityCount;

    /**
     * TimeStamp
     * @var integer
     */
    public $timeStamp;
}