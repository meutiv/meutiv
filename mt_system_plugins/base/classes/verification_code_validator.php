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
 * Email verification code validator
 *
 * @author Podyachev Evgeny <joker.OW2@gmail.com>
 * @package mt_system_plugins.base.classes
 * @since 1.8.2
 */

class BASE_CLASS_VerificationCodeValidator extends MT_Validator
{

    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct()
    {
        $language = MT::getLanguage();

        $this->setErrorMessage($language->text('base', 'email_verify_invalid_verification_code'));
    }

    /**
     * @see Validator::isValid()
     *
     * @param mixed $value
     */
    public function isValid( $value )
    {
        $emailVerifyData = BOL_EmailVerifyService::getInstance()->findByHash($value);

        if ( $emailVerifyData == null )
        {
            return false;
        }

        if( $emailVerifyData->type === BOL_EmailVerifyService::TYPE_USER_EMAIL )
        {
            $user = BOL_UserService::getInstance()->findUserById($emailVerifyData->userId);

            if ( $user == null )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @see Validator::getJsValidator()
     *
     * @return string
     */
    public function getJsValidator()
    {
        return "{
                validate : function( value )
                {
                },
                getErrorMessage : function(){
                    return " . json_encode($this->getError()) . ";
                 }
        }";
    }
}