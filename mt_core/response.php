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
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package mt_core
 * @method static MT_Response getInstance()
 * @since 1.0
 */
class MT_Response
{
    /**
     * HTTP Header constants
     */
    const HD_CACHE_CONTROL = 'Cache-Control';
    const HD_CNT_DISPOSITION = 'Content-Disposition';
    const HD_CNT_LENGTH = 'Content-Length';
    const HD_CONNECTION = 'Connection';
    const HD_PRAGMA = 'Pragma';
    const HD_CNT_TYPE = 'Content-Type';
    const HD_EXPIRES = 'Expires';
    const HD_LAST_MODIFIED = 'Last-Modified';
    const HD_LOCATION = 'Location';

    use MT_Singleton;
    
    /**
     * Headers to send with response
     *
     * @var array
     */
    private $headers = array();

    /**
     * Document to send
     *
     * @var MT_Document
     */
    private $document;

    /**
     * Rendered markup
     *
     * @var string
     */
    private $markup = '';

    /**
     * @return MT_Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param MT_Document $document
     */
    public function setDocument( MT_Document $document )
    {
        $this->document = $document;
    }

    /**
     * Adds headers to response.
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader( $name, $value )
    {
        $this->headers[trim($name)] = trim($value);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders( array $headers )
    {
        $this->headers = $headers;
    }

    /**
     * Clears all headers.
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * Sends all added headers.
     */
    public function sendHeaders()
    {
        if ( !headers_sent() )
        {
            foreach ( $this->headers as $headerName => $headerValue )
            {
                if ( substr(mb_strtolower($headerName), 0, 4) === 'http' )
                {
                    header($headerName . ' ' . $headerValue);
                }
                else if ( mb_strtolower($headerName) === 'status' )
                {
                    header(ucfirst(mb_strtolower($headerName)) . ': ' . $headerValue, false, (int) $headerValue);
                }
                else
                {
                    header($headerName . ':' . $headerValue);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @param string $markup
     */
    public function setMarkup( $markup )
    {
        $this->markup = $markup;
    }

    /**
     * Sends generated response
     *
     */
    public function respond()
    {
        $event = new MT_Event(MT_EventManager::ON_BEFORE_DOCUMENT_RENDER);
        MT::getEventManager()->trigger($event);
        if ( $this->document !== null )
        {
            $renderedMarkup = $this->document->render();

            $event = new BASE_CLASS_EventCollector('base.append_markup');
            MT::getEventManager()->trigger($event);
            $data = $event->getData();
            $this->markup = str_replace(MT_Document::APPEND_PLACEHOLDER, PHP_EOL . implode(PHP_EOL, $data), $renderedMarkup);
        }

        $event = new MT_Event(MT_EventManager::ON_AFTER_DOCUMENT_RENDER);
        MT::getEventManager()->trigger($event);

        $this->sendHeaders();

        if ( MT::getRequest()->isAjax() )
        {
            exit();
        }

        if ( MT_PROFILER_ENABLE || MT_DEV_MODE )
        {
            UTIL_Profiler::getInstance()->mark('final');
        }

        if ( MT_DEBUG_MODE )
        {
            echo ob_get_clean();
        }

        echo $this->markup;

        $event = new MT_Event('core.exit');
        MT::getEventManager()->trigger($event);
    }
}
