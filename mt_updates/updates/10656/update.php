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

$tblPrefix = MT_DB_PREFIX;

$dbo = Updater::getDbo();
$logger = Updater::getLogger();

// add absent columns
try
{
    $query = "SHOW COLUMNS FROM `{$tblPrefix}base_geolocation_ip_to_country` LIKE 'ipFrom'";
    $column = $dbo->queryForRow($query);

    if ( !$column )
    {
        $query = "ALTER TABLE `{$tblPrefix}base_geolocation_ip_to_country` ADD `ipFrom` bigint UNSIGNED";
        $dbo->query($query);
    }
    else
    {
        $query = "ALTER TABLE `{$tblPrefix}base_geolocation_ip_to_country` CHANGE `ipFrom` `ipFrom` bigint UNSIGNED";
        $dbo->query($query);
    }
}
catch (Exception $e)
{
    $logger->addEntry(json_encode($e));
}

try
{
    $query = "SHOW COLUMNS FROM `{$tblPrefix}base_geolocation_ip_to_country` LIKE 'ipTo'";
    $column = $dbo->queryForRow($query);

    if ( !$column )
    {
        $query = "ALTER TABLE `{$tblPrefix}base_geolocation_ip_to_country` ADD `ipTo` bigint UNSIGNED";
        $dbo->query($query);
    }
    else
    {
        $query = "ALTER TABLE `{$tblPrefix}base_geolocation_ip_to_country` CHANGE `ipTo` `ipTo` bigint UNSIGNED";
        $dbo->query($query);
    }
}
catch (Exception $e)
{
    $logger->addEntry(json_encode($e));
}

// add index
try
{
    $query = "ALTER TABLE `{$tblPrefix}base_geolocation_ip_to_country` ADD INDEX `ipRange` (`ipFrom`, `ipTo`)";
    $dbo->query($query);

}
catch (Exception $e)
{
    $logger->addEntry(json_encode($e));
}

