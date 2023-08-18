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
 * The base class for all action controllers.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_core
 * @since 1.0
 */
abstract class MT_ActionController extends MT_Renderable
{
    /**
     * Default controller action (used if action isn't provided).
     *
     * @var string
     */
    protected $defaultAction = 'index';

    /**
     * Constructor.
     */
    public function __construct()
    {
        
    }

    /**
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * @param string $action
     */
    public function setDefaultAction( $action )
    {
        $this->defaultAction = trim($action);
    }

    /**
     * Makes permanent redirect to the same controller and provided action.
     *
     * @param string $action
     */
    public function redirectToAction( $action )
    {
        $handlerAttrs = MT::getRequestHandler()->getHandlerAttributes();

        MT::getApplication()->redirect(MT::getRouter()->uriFor($handlerAttrs['controller'], trim($action)));
    }

    /**
     * Makes permanent redirect to provided URL or URI.
     *
     * @param string $redirectTo
     */
    public function redirect( $redirectTo = null )
    {
        MT::getApplication()->redirect($redirectTo);
    }

    /**
     * Optional method. Called before action.
     */
    public function init()
    {
        
    }

    /**
     * Sets custom document key for current page.
     *
     * @param string $key
     */
    public function setDocumentKey( $key )
    {
        MT::getApplication()->setDocumentKey($key);
    }

    /**
     * Returns document key for current page.
     * 
     * @return string
     */
    public function getDocumentKey()
    {
        return MT::getApplication()->getDocumentKey();
    }

    /**
     * Sets page heading.
     * @param string $heading
     */
    public function setPageHeading( $heading )
    {
        MT::getDocument()->setHeading(trim($heading));
    }

    /**
     * Sets page heading icon class.
     *
     * @param string $class
     */
    public function setPageHeadingIconClass( $class )
    {
        MT::getDocument()->setHeadingIconClass($class);
    }

    /**
     * @param string $title
     */
    public function setPageTitle( $title )
    {
        MT::getDocument()->setTitle(trim($title));
    }

    /**
     * @param string $desc
     */
    public function setPageDescription( $desc )
    {
        MT::getDocument()->setDescription($desc);
    }

    /**
     * @param array $keywords
     */
    public function setKeywords( $keywords )
    {
        MT::getDocument()->setKeywords($keywords);
    }
}
