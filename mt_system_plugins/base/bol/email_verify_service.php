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
 * Email Verify Service Class
 *
 * @author Podyachev Evgeny <joker.OW2@gmail.com>
 * @package mt_system_plugins.base.bol
 * @since 1.0
 */
class BOL_EmailVerifyService
{
    const TYPE_USER_EMAIL = 'user';
    const TYPE_SITE_EMAIL = 'site';


    /**
     * @var BOL_EmailVerifyDao
     */
    private $emailVerifiedDao;

    /**
     * Constructor.
     *
     */
    private function __construct()
    {
        $this->emailVerifiedDao = BOL_EmailVerifyDao::getInstance();
    }
    /**
     * Singleton instance.
     *
     * @var BOL_EmailVerifyService
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_EmailVerifyService
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self();

        return self::$classInstance;
    }

    /**
     * @param BOL_EmailVerify $object
     */
    public function saveOrUpdate( BOL_EmailVerify $object )
    {
        $this->emailVerifiedDao->save($object);
    }

    /**
     * @param string $email
     * @param string $type
     * @return BOL_EmailVerify
     */
    public function findByEmail( $email, $type )
    {
        return $this->emailVerifiedDao->findByEmail($email, $type);
    }

    /**
     * @param string $email
     * @param int $userId
     * @param string $type
     * @return BOL_EmailVerify
     */
    public function findByEmailAndUserId( $email, $userId, $type )
    {
        return $this->emailVerifiedDao->findByEmailAndUserId($email, $userId, $type);
    }

    /**
     * @param string $hash
     * @return BOL_EmailVerify
     */
    public function findByHash( $hash )
    {
        return $this->emailVerifiedDao->findByHash($hash);
    }

    /**
     * @return string
     */
    public function generateHash()
    {
        return md5(uniqid());
    }

    /**
     * @param array $objects
     */
    public function batchReplace( $objects )
    {
        $this->emailVerifiedDao->batchReplace($objects);
    }

    /**
     * @param int $id
     */
    public function deleteById( $id )
    {
        $this->emailVerifiedDao->deleteById($id);
    }

    /**
     * @param int $userId
     */
    public function deleteByUserId( $userId )
    {
        $this->emailVerifiedDao->deleteByUserId($userId);
    }

    /**
     * @param int $stamp
     */
    public function deleteByCreatedStamp( $stamp )
    {
        $this->emailVerifiedDao->deleteByCreatedStamp($stamp);
    }

    public function sendVerificationMail( $type, $params )
    {
        $subject = $params['subject'];
        $template_html = $params['body_html'];
        $template_text = $params['body_text'];

        switch ( $type )
        {
            case self::TYPE_USER_EMAIL:
                $user = $params['user'];
                $email = $user->email;
                $userId = $user->id;

                break;

            case self::TYPE_SITE_EMAIL:
                $email = MT::getConfig()->getValue('base', 'unverify_site_email');
                $userId = 0;

                break;

            default :
                if ( isset($params['feedback']) && $params['feedback'] == true )
                {
                    MT::getFeedback()->error(MT::getLanguage()->text('base', 'email_verify_verify_mail_was_not_sent'));
                }
                return;
        }

        $emailVerifiedData = BOL_EmailVerifyService::getInstance()->findByEmailAndUserId($email, $userId, $type);

        if ( $emailVerifiedData !== null )
        {
            $timeLimit = 60 * 60 * 24 * 3; // 3 days

            if ( time() - (int) $emailVerifiedData->createStamp >= $timeLimit )
            {
                $emailVerifiedData = null;
            }
        }

        if ( $emailVerifiedData === null )
        {
            $hash = BOL_EmailVerifyService::getInstance()->generateHash();

            $event = new MT_Event('base.on_after_generate_email_verification_code', [], $hash);
            MT::getEventManager()->trigger($event);
            $hash = $event->getData();

            $emailVerifiedData = new BOL_EmailVerify();
            $emailVerifiedData->userId = $userId;
            $emailVerifiedData->email = trim($email);
            $emailVerifiedData->hash = $hash;
            $emailVerifiedData->createStamp = time();
            $emailVerifiedData->type = $type;

            BOL_EmailVerifyService::getInstance()->batchReplace(array($emailVerifiedData));
        }

        $vars = array(
            'code' => $emailVerifiedData->hash,
        );

        if ( MT::getApplication()->getContext() != MT_Application::CONTEXT_API )
        {
            $vars['url'] = MT::getRouter()->urlForRoute('base_email_verify_code_check', array('code' => $emailVerifiedData->hash));
            $vars['verification_page_url'] = MT::getRouter()->urlForRoute('base_email_verify_code_form');
        }

        $language = MT::getLanguage();

        $subject = UTIL_String::replaceVars($subject, $vars);
        $template_html = UTIL_String::replaceVars($template_html, $vars);
        $template_text = UTIL_String::replaceVars($template_text, $vars);

        $mail = MT::getMailer()->createMail();
        $mail->addRecipientEmail($emailVerifiedData->email);
        $mail->setSubject($subject);
        $mail->setHtmlContent($template_html);
        $mail->setTextContent($template_text);

        MT::getMailer()->send($mail);

        if ( isset($params['feedback']) && $params['feedback'] == true )
        {
            MT::getFeedback()->info($language->text('base', 'email_verify_verify_mail_was_sent'));
        }
    }

    public function sendUserVerificationMail( BOL_User $user, $feedback = true )
    {
        $vars = array(
            'username' => BOL_UserService::getInstance()->getDisplayName($user->id),
        );

        $language = MT::getLanguage();

        $subject = $language->text('base', 'email_verify_subject', $vars);
        $template_html = $language->text('base', 'email_verify_template_html', $vars);
        $template_text = $language->text('base', 'email_verify_template_text', $vars);

        $params = array(
            'user' => $user,
            'subject' => $subject,
            'body_html' => $template_html,
            'body_text' => $template_text,
            'feedback' => $feedback
        );

        $this->sendVerificationMail(self::TYPE_USER_EMAIL, $params);
    }



    public function sendSiteVerificationMail($feedback = true)
    {
        $language = MT::getLanguage();

        $subject = $language->text('base', 'site_email_verify_subject');
        $template_html = $language->text('base', 'site_email_verify_template_html');
        $template_text = $language->text('base', 'site_email_verify_template_text');

        $params = array(
            'subject' => $subject,
            'body_html' => $template_html,
            'body_text' => $template_text,
            'feedback' => $feedback
        );

        $this->sendVerificationMail(self::TYPE_SITE_EMAIL, $params);
    }

    /**
     * @param string $code
     */
    public function verifyEmail( $code )
    {
        $language = MT::getLanguage();

        $data =  $this->verifyEmailCode($code);

        if ( $data['isValid'] )
        {
            switch ( $data["type"] )
            {
                case self::TYPE_USER_EMAIL:

                    MT::getFeedback()->info($language->text('base', 'email_verify_email_verify_success'));
                    MT::getApplication()->redirect(MT::getRouter()->urlForRoute('base_default_index'));
                    break;

                case self::TYPE_SITE_EMAIL:

                    MT::getFeedback()->info($language->text('base', 'email_verify_email_verify_success'));
                    MT::getApplication()->redirect(MT::getRouter()->urlForRoute('admin_settings_main'));
                    break;
            }
        }
    }

    /**
     * @param string $code
     * @param bool $loginUser
     * @return array
     */
    public function verifyEmailCode( $code, $loginUser = true )
    {
        $result = ["isValid" => false, "type" => "", "message" => ""];

        /** @var BOL_EmailVerify $emailVerifyData */
        $emailVerifyData = $this->findByHash($code);

        if ( $emailVerifyData !== null )
        {
            $result["type"] = $emailVerifyData->type;
            switch ( $emailVerifyData->type )
            {
                case self::TYPE_USER_EMAIL:

                    $user = BOL_UserService::getInstance()->findUserById($emailVerifyData->userId);

                    if ( $user !== null )
                    {
                        if ( $loginUser ) {
                            if (MT::getUser()->isAuthenticated()) {
                                if (MT::getUser()->getId() !== $user->getId()) {
                                    MT::getUser()->logout();
                                }
                            }

                            MT::getUser()->login($user->getId());
                        }

                        $this->deleteById($emailVerifyData->id);

                        $user->emailVerify = true;
                        BOL_UserService::getInstance()->saveOrUpdate($user);

                        $result["isValid"] = true;
                    }

                    break;

                case self::TYPE_SITE_EMAIL:

                    MT::getConfig()->saveConfig('base', 'site_email', $emailVerifyData->email);
                    BOL_LanguageService::getInstance()->generateCacheForAllActiveLanguages();

                    $result["isValid"] = true;

                    break;
            }
        }

        return $result;
    }
}
