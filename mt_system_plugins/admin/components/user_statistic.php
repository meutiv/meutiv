<?php

/**
 * This software is intended for use with Meutiv Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Meutiv Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Meutiv Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * User statistics component
 *
 * @author Alex Ermashev <alexermashev@gmail.com>
 * @package mt_system_plugins.base.components
 * @since 1.7.6
 */
class ADMIN_CMP_UserStatistic extends MT_Component
{
    /**
     * Default period
     * @var string
     */
    protected $defaultPeriod;

    /**
     * Class constructor
     *
     * @param array $params
     */
    public function __construct( $params )
    {
        parent::__construct();

        $this->defaultPeriod = !empty($params['defaultPeriod'])
            ? $params['defaultPeriod']
            : BOL_SiteStatisticService::PERIOD_TYPE_TODAY;
    }

    /**
     * On before render
     *
     * @return void
     */
    public function onBeforeRender()
    {
        $entityTypes = array(
            'user_join',
            'user_login'
        );

        $entityLabels = array(
            'user_join' => MT::getLanguage()->text('admin', 'site_statistics_user_registrations'),
            'user_login' => MT::getLanguage()->text('admin', 'site_statistics_user_logins')
        );

        // register components
        $this->addComponent('statistics',
                new BASE_CMP_SiteStatistic('user-statistics-chart', $entityTypes, $entityLabels, $this->defaultPeriod));
    }
}

