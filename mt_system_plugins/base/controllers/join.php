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
 * Join user
 *
 * @author Podyachev Evgeny <joker.OW2@gmail.com>
 * @package mt_system_plugins.base.controllers
 * @since 1.0
 */
class BASE_CTRL_Join extends MT_ActionController
{
    const JOIN_CONNECT_HOOK = 'join_connect_hook';

    protected $responderUrl;

    /**
     * @var BOL_UserService
     */
    protected $userService;

    /**
     * @var JoinForm
     */
    protected $joinForm;

    public function __construct()
    {
        parent::__construct();

        $this->responderUrl = MT::getRouter()->urlFor("BASE_CTRL_Join", "ajaxResponder");

        $this->userService = BOL_UserService::getInstance();
    }

    /**
     * @param $params
     */
    public function index($params )
    {
        $session = MT::getSession();

        if ( MT::getUser()->isAuthenticated() )
        {
            $this->redirect(MT::getRouter()->urlForRoute('base_member_dashboard'));
        }

        $language = MT::getLanguage();
        $this->setPageHeading($language->text('base', 'join_index'));

        //TODO DELETE config who_can_join from join
        if ( (int) MT::getConfig()->getValue('base', 'who_can_join') === BOL_UserService::PERMISSIONS_JOIN_BY_INVITATIONS )
        {
            $code = null;
            if ( isset($_GET['code']) )
            {
                $code = $_GET['code'];
            }
            else if ( isset($params['code']) )
            {
                $code = $params['code'];
            }

            //close join form
            try
            {
                $event = new MT_Event(MT_EventManager::ON_JOIN_FORM_RENDER, array('code' => $code));
                MT::getEventManager()->trigger($event);
                $this->assign('notValidInviteCode', true);
                return;
            }
            catch ( JoinRenderException $ex )
            {
                //ignore;
            }
        }

        $urlParams = $_GET;
        if ( is_array($params) && !empty($params) )
        {
            $urlParams = array_merge($_GET, $params);
        }

        $this->joinForm = MT::getClassInstance('JoinForm', $this);
        $this->joinForm->setAction(MT::getRouter()->urlFor('BASE_CTRL_Join', 'joinFormSubmit', $urlParams));

        $this->addForm($this->joinForm);

        $language->addKeyForJs('base', 'join_error_username_not_valid');
        $language->addKeyForJs('base', 'join_error_username_already_exist');
        $language->addKeyForJs('base', 'join_error_email_not_valid');
        $language->addKeyForJs('base', 'join_error_email_already_exist');
        $language->addKeyForJs('base', 'join_error_password_not_valid');
        $language->addKeyForJs('base', 'join_error_password_too_short');
        $language->addKeyForJs('base', 'join_error_password_too_long');

        //include js
        $onLoadJs = " window.join = new MT_BaseFieldValidators( " .
            json_encode(array(
                'formName' => $this->joinForm->getName(),
                'responderUrl' => $this->responderUrl,
                'passwordMaxLength' => UTIL_Validator::PASSWORD_MAX_LENGTH,
                'passwordMinLength' => UTIL_Validator::PASSWORD_MIN_LENGTH)) . ",
                " . UTIL_Validator::EMAIL_PATTERN . ", " . UTIL_Validator::USER_NAME_PATTERN . " ); ";

        MT::getDocument()->addOnloadScript($onLoadJs);

        $jsDir = MT::getPluginManager()->getPlugin("base")->getStaticJsUrl();
        MT::getDocument()->addScript($jsDir . "base_field_validators.js");

        $this->setDocumentKey('base_user_join');

        // set meta info
        $params = array(
            "sectionKey" => "base.base_pages",
            "entityKey" => "join",
            "title" => "base+meta_title_join",
            "description" => "base+meta_desc_join",
            "keywords" => "base+meta_keywords_join"
        );

        MT::getEventManager()->trigger(new MT_Event("base.provide_page_meta_info", $params));
    }

    public function joinFormSubmit( $params )
    {
        $this->setTemplate(MT::getPluginManager()->getPlugin('base')->getCtrlViewDir().'join_index.html');

        if ( (int) MT::getConfig()->getValue('base', 'who_can_join') === BOL_UserService::PERMISSIONS_JOIN_BY_INVITATIONS )
        {
            $code = null;
            if ( isset($params['code']) )
            {
                $code = $params['code'];
            }

            //close join form
            try
            {
                $event = new MT_Event(MT_EventManager::ON_JOIN_FORM_RENDER, array('code' => $code));
                MT::getEventManager()->trigger($event);
                $this->assign('notValidInviteCode', true);
                return;
            }
            catch ( JoinRenderException $ex )
            {
                //ignore;
            }
        }
        
        $this->index($params);
        $this->postProcess( $params );
    }

    protected function postProcess( $params )
    {
        if ( MT::getRequest()->isPost() )
        {
            if ( $this->joinForm->isValid($this->joinForm->getPost()) )
            {
                $session = MT::getSession();
                $joinData = $session->get(JoinForm::SESSION_JOIN_DATA);

                if ( !isset($joinData) || !is_array($joinData) )
                {
                    $joinData = array();
                }

                $data = $this->joinForm->getRealValues();

                unset($data['repeatPassword']);
                $this->joinForm->clearSession();

                foreach ( $this->joinForm->questions as $question )
                {
                    switch ( $question['presentation'] )
                    {
                        case BOL_QuestionService::QUESTION_PRESENTATION_MULTICHECKBOX:

                            if ( is_array($data[$question['name']]) )
                            {
                                $data[$question['name']] = array_sum($data[$question['name']]);
                            }
                            else
                            {
                                $data[$question['name']] = 0;
                            }

                            break;
                    }
                }

                $joinData = array_merge($joinData, $data);

                if ( $this->joinForm->isLastStep() )
                {
                    $session->set(JoinForm::SESSION_JOIN_DATA, $joinData);
                    $this->joinUser($joinData, $this->joinForm->getAccountType(), $params);

                    $this->redirect(MT::getRouter()->getBaseUrl());
                }
                else
                {
                    $step = $this->joinForm->getStep();

                    $step++;

                    $session->set(JoinForm::SESSION_JOIN_DATA, $joinData);
                    $session->set(JoinForm::SESSION_JOIN_STEP, $step);

                    $this->redirect(MT::getRequest()->buildUrlQueryString(MT::getRouter()->urlForRoute('base_join'), $params));

                }
            }
        }
        else
        {
            $this->redirect(MT::getRequest()->buildUrlQueryString(MT::getRouter()->urlForRoute('base_join'), $params));
        }
    }

    public function ajaxResponder()
    {
        if ( empty($_POST["command"]) || !MT::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $command = (string) $_POST["command"];
        
        switch ( $command )
        {
            case 'isExistUserName':

                $username = $_POST["value"];
                $result = $this->userService->isExistUserName($username);

                echo json_encode(array('result' => !$result));

                break;

            case 'isExistEmail':

                $email = $_POST["value"];

                $result = $this->userService->isExistEmail($email);

                echo json_encode(array('result' => !$result));

                break;

            default:
        }
        exit;
    }

    protected function joinUser( $joinData, $accountType, $params )
    {
        $event = new MT_Event(MT_EventManager::ON_BEFORE_USER_REGISTER, $joinData);
        MT::getEventManager()->trigger($event);

        $language = MT::getLanguage();
        // create new user
        $user = $this->userService->createUser($joinData['username'], $joinData['password'], $joinData['email'], $accountType);

        unset($joinData['username']);
        unset($joinData['password']);
        unset($joinData['email']);
        unset($joinData['accountType']);

        // save user data
        if ( !empty($user->id) )
        {
            if ( BOL_QuestionService::getInstance()->saveQuestionsData($joinData, $user->id) )
            {
                MT::getSession()->delete(JoinForm::SESSION_JOIN_DATA);
                MT::getSession()->delete(JoinForm::SESSION_JOIN_STEP);

                // authenticate user
                MT::getUser()->login($user->id);
                
                // create Avatar
                $this->createAvatar($user->id);

                $event = new MT_Event(MT_EventManager::ON_USER_REGISTER, array('userId' => $user->id, 'method' => 'native', 'params' => $params));
                MT::getEventManager()->trigger($event);

                MT::getFeedback()->info(MT::getLanguage()->text('base', 'join_successful_join'));

                if ( MT::getConfig()->getValue('base', 'confirm_email') )
                {
                    BOL_EmailVerifyService::getInstance()->sendUserVerificationMail($user);
                }
            }
            else
            {
                MT::getFeedback()->error($language->text('base', 'join_join_error'));
            }
        }
        else
        {
            MT::getFeedback()->error($language->text('base', 'join_join_error'));
        }
    }
    
    protected function createAvatar($userId)
    {
         BOL_AvatarService::getInstance()->createAvatar($userId, true, false);
    }
}

class JoinForm extends BASE_CLASS_UserQuestionForm
{
    const FORM_NAME = 'joinForm';

    const SESSION_JOIN_DATA = 'joinData';

    const SESSION_JOIN_STEP = 'joinStep';

    const SESSION_REAL_QUESTION_LIST = 'join.real_question_list';

    const SESSION_ALL_QUESTION_LIST = 'join.all_question_list';

    const SESSION_START_STAMP = 'join.session_start_stamp';

    protected $post = array();
    protected $stepCount = 1;
    protected $isLastStep = false;
    protected $displayAccountType = false;
    public  $questions = array();
    protected $sortedQuestionsList = array();
    protected $questionListBySection = array();
    protected $questionValuesList = array();
    protected $accountType = null;
    protected $data = array();
    protected $toggleClass = '';

    public function __construct( $controller )
    {
        parent::__construct(self::FORM_NAME);

        $this->setId(self::FORM_NAME);

        $stamp = MT::getSession()->get(self::SESSION_START_STAMP);

        if ( empty($stamp) )
        {
            MT::getSession()->set(self::SESSION_START_STAMP, time());
        }

        unset($stamp);

        $this->checkSession();

        $joinSubmitLabel = "";

        // get available account types from DB
        $accounts = $this->getAccountTypes();

        $joinData = MT::getSession()->get(self::SESSION_JOIN_DATA);

        if ( !isset($joinData) || !is_array($joinData) )
        {
            $joinData = array();
        }

        $accountsKeys = array_keys($accounts);
        $this->accountType = $accountsKeys[0];

        if ( isset($joinData['accountType']) )
        {
            $this->accountType = trim($joinData['accountType']);
        }

        $step = $this->getStep();

        if ( count($accounts) > 1 )
        {
            $this->stepCount = 2;
            switch ( $step )
            {
                case 1:
                    $this->displayAccountType = true;
                    $joinSubmitLabel = MT::getLanguage()->text('base', 'join_submit_button_continue');
                    break;

                case 2:
                    $this->isLastStep = true;
                    $joinSubmitLabel = MT::getLanguage()->text('base', 'join_submit_button_join');
                    break;
            }
        }
        else
        {
            $this->isLastStep = true;
            $joinSubmitLabel = MT::getLanguage()->text('base', 'join_submit_button_join');
        }

        $joinSubmit = new Submit('joinSubmit');
        $joinSubmit->addAttribute('class', 'mt_button mt_ic_submit');
        $joinSubmit->setValue($joinSubmitLabel);
        $this->addElement($joinSubmit);

        $this->init($accounts);

        $this->getQuestions();

        $section = null;

        $questionNameList = array();
        $this->sortedQuestionsList = array();

        foreach ( $this->questions as $sort => $question )
        {
            if ( (string) $question['base'] === '0' && $step === 2 || $step === 1 )
            {
                if ( $section !== $question['sectionName'] )
                {
                    $section = $question['sectionName'];
                }

                //$this->questionListBySection[$section][] = $this->questions[$sort];
                $questionNameList[] = $this->questions[$sort]['name'];
                $this->sortedQuestionsList[] = $this->questions[$sort];
            }
        }

        $this->questionValuesList = BOL_QuestionService::getInstance()->findQuestionsValuesByQuestionNameList($questionNameList);

        $this->processQuestions();

        $this->addQuestions($this->sortedQuestionsList, $this->questionValuesList, $this->updateJoinData());

        $this->setQuestionsLabel();

        $this->addClassToBaseQuestions();

        if ( $this->isLastStep )
        {
            $this->addLastStepQuestions($controller);
        }

        // Override random field ids with static one.
        $this->setStaticIdsForFields(self::FORM_NAME);

        $controller->assign('step', $step);
        $controller->assign('questionArray', $this->questionListBySection);
        $controller->assign('displayAccountType', $this->displayAccountType);
        $controller->assign('isLastStep', $this->isLastStep);
    }

    protected function init( array $accounts )
    {
        if ( $this->displayAccountType )
        {
            $joinAccountType = new Selectbox('accountType');
            $joinAccountType->setLabel(MT::getLanguage()->text('base', 'questions_question_account_type_label'));
            $joinAccountType->setRequired();
            $joinAccountType->setOptions($accounts);
            $joinAccountType->setValue($this->accountType);
            $joinAccountType->setHasInvitation(false);

            $this->addElement($joinAccountType);
        }
    }

    public function checkSession()
    {
        $stamp = BOL_QuestionService::getInstance()->getQuestionsEditStamp();
        $sessionStamp = MT::getSession()->get(self::SESSION_START_STAMP);
        
        if ( !empty($sessionStamp) && $stamp > $sessionStamp )
        {
            MT::getSession()->delete(self::SESSION_ALL_QUESTION_LIST);
            MT::getSession()->delete(self::SESSION_JOIN_DATA);
            MT::getSession()->delete(self::SESSION_JOIN_STEP);
            MT::getSession()->delete(self::SESSION_REAL_QUESTION_LIST);
            MT::getSession()->delete(self::SESSION_START_STAMP);

            if ( MT::getRequest()->isPost() )
            {
                UTIL_Url::redirect(MT::getRouter()->urlForRoute('base_join'));
            }
        }
    }

    public function setQuestionsLabel()
    {
        foreach ( $this->sortedQuestionsList as $question )
        {
            if ( !empty($question['realName']) )
            {
                $event = new MT_Event('base.questions_field_add_label_join', $question, true);

                MT::getEventManager()->trigger($event);

                $data = $event->getData();

                if( !empty($data['label']) )
                {
                    $this->getElement($question['name'])->setLabel($data['label']);
                }
                else
                {
                    $this->getElement($question['name'])->setLabel(MT::getLanguage()->text('base', 'questions_question_' . $question['realName'] . '_label'));
                }

            }
        }
    }

    public function addClassToBaseQuestions()
    {
        foreach ( $this->sortedQuestionsList as $question )
        {
            if ( !empty($question['realName']) )
            {
                if ( $question['realName'] == 'username' )
                {
                    $this->getElement($question['name'])->addAttribute("class", "mt_username_validator");
                }

                if ( $question['realName'] == 'email' )
                {
                    $this->getElement($question['name'])->addAttribute("class", "mt_email_validator");
                }
            }
        }
    }

    protected function toggleQuestionClass()
    {
        $class = 'mt_alt1';
        switch ( $this->toggleClass )
        {
            case null:
            case 'mt_alt2':
                break;
            case 'mt_alt1':
                $class = 'mt_alt2';
        }

        $this->toggleClass = $class;

        return $class;
    }

    protected function processQuestions()
    {
        $step = $this->getStep();
        $realQuestionList = array();
        $valueList = $this->questionValuesList;
        $this->questionValuesList = array();
        $this->sortedQuestionsList = array();
        $this->questionListBySection = array();
        $section = '';

        $oldQuestionList = MT::getSession()->get(self::SESSION_REAL_QUESTION_LIST);
        $allQuestionList = MT::getSession()->get(self::SESSION_ALL_QUESTION_LIST);

        if ( !empty($oldQuestionList) && !empty($allQuestionList) )
        {
            $realQuestionList = $oldQuestionList;
            $this->sortedQuestionsList = $allQuestionList;

            foreach ( $this->sortedQuestionsList as $key => $question )
            {
                $this->questionListBySection[$question['sectionName']][] = $question;

                $this->addEmptyClass(preg_replace('/\s+(mt_alt1|mt_alt2)/', '', $question['trClass']));

                if ( !empty($valueList[$question['realName']]) )
                {
                    $this->questionValuesList[$question['name']] = $valueList[$question['realName']];
                }
            }
        }
        else
        {
            foreach ( $this->questions as $sort => $question )
            {
                if ( (string) $question['base'] === '0' && $step === 2 || $step === 1 )
                {
                    if ( $section !== $question['sectionName'] )
                    {
                        $section = $question['sectionName'];
                    }

                    $this->questions[$sort]['realName'] = $question['name'];

                    $this->questions[$sort]['trClass'] = $this->toggleQuestionClass();

                    if ( $this->questions[$sort]['presentation'] == 'password' )
                    {
                        $this->toggleQuestionClass();
                    }

                    $this->sortedQuestionsList[$question['name']] = $this->questions[$sort];
                    $this->questionListBySection[$section][] = $this->questions[$sort];

                    if ( !empty($valueList[$question['name']]) )
                    {
                        $this->questionValuesList[$question['name']] = $valueList[$question['name']];
                    }
                }
            }
        }

        if ( MT::getRequest()->isPost() )
        {
            $this->post = $_POST;

            if ( empty($oldQuestionList) )
            {
                $oldQuestionList = array();
            }

            if ( empty($allQuestionList) )
            {
                $allQuestionList = array();
            }

            if ( $oldQuestionList && $allQuestionList )
            {
                foreach ( $oldQuestionList as $key => $value )
                {
                    $newKey = array_search($value, $realQuestionList);

                    if ( $newKey !== false && isset($_POST[$key]) && isset($realQuestionList[$newKey]) )
                    {
                        $this->post[$newKey] = $_POST[$key];
                    }
                }
            }
        }

        MT::getSession()->set(self::SESSION_REAL_QUESTION_LIST, $realQuestionList);
        MT::getSession()->set(self::SESSION_ALL_QUESTION_LIST, $this->sortedQuestionsList);
    }

    protected function updateJoinData()
    {
        $joinData = MT::getSession()->get(self::SESSION_JOIN_DATA);

        if ( empty($joinData) )
        {
            return;
        }

        $this->data = $joinData;

        $list = MT::getSession()->get(self::SESSION_REAL_QUESTION_LIST);

        if ( !empty($list) )
        {
            foreach ( $list as $fakeName => $realName )
            {
                if ( !empty($joinData[$realName]) )
                {
                    unset($this->data[$realName]);
                    $this->data[$fakeName] = $joinData[$realName];
                }
            }
        }

        return $this->data;
    }

    public function getRealValues()
    {
        $list = $this->sortedQuestionsList;

        $values = $this->getValues();
        $result = array();

        if ( !empty($list) )
        {
            foreach ( $values as $name => $value )
            {
                if ( !empty($list[$name]) )
                {
                    $result[$list[$name]['realName']] = $value;
                }

                if ( $name == 'accountType' )
                {
                    $result[$name] = $value;
                }
            }
        }
        
        return $result;
    }

    public function getStep()
    {
        $session = MT::getSession();

        $step = $session->get(self::SESSION_JOIN_STEP);

        if ( isset($step) )
        {
            $step = (int) $step;

            if ( $step === 0 )
            {
                $step = 1;
                $session->set(self::SESSION_JOIN_STEP, $step);
            }
        }
        else
        {
            $step = 1;
            $session->set(self::SESSION_JOIN_STEP, $step);
        }

        return $step;
    }

    public function getQuestions()
    {
        $this->questions = array();

        if ( $this->isLastStep )
        {
            $this->questions = BOL_QuestionService::getInstance()->findSignUpQuestionsForAccountType($this->accountType);
        }
        else
        {
            $this->questions = BOL_QuestionService::getInstance()->findBaseSignUpQuestions();
        }
    }
    
    protected function addLastStepQuestions( $controller )
    {
        $displayPhoto = false;

        $displayPhotoUpload = MT::getConfig()->getValue('base', 'join_display_photo_upload');
        $avatarValidator = MT::getClassInstance("BASE_CLASS_AvatarFieldValidator", false);

        switch ( $displayPhotoUpload )
        {
            case BOL_UserService::CONFIG_JOIN_DISPLAY_AND_SET_REQUIRED_PHOTO_UPLOAD :
                $avatarValidator = MT::getClassInstance("BASE_CLASS_AvatarFieldValidator", true);

            case BOL_UserService::CONFIG_JOIN_DISPLAY_PHOTO_UPLOAD :
                $userPhoto = MT::getClassInstance("BASE_CLASS_JoinUploadPhotoField", 'userPhoto');
                $userPhoto->setLabel(MT::getLanguage()->text('base', 'questions_question_user_photo_label'));
                $userPhoto->addValidator($avatarValidator);
                $this->addElement($userPhoto);

                $displayPhoto = true;
        }

        $displayTermsOfUse = false;

        if ( MT::getConfig()->getValue('base', 'join_display_terms_of_use') )
        {
            $termOfUse = new CheckboxField('termOfUse');
            $termOfUse->setLabel(MT::getLanguage()->text('base', 'questions_question_user_terms_of_use_label'));
            $termOfUse->setRequired();

            $this->addElement($termOfUse);

            $displayTermsOfUse = true;
        }

        $this->setEnctype('multipart/form-data');

        $event = new MT_Event('join.get_captcha_field');
        MT::getEventManager()->trigger($event);
        $captchaField = $event->getData();

        $displayCaptcha = false;

        $enableCaptcha = MT::getConfig()->getValue('base', 'enable_captcha');
        
        if ( $enableCaptcha && !empty($captchaField) && $captchaField instanceof FormElement )
        {
            $captchaField->setName('captchaField');
            $this->addElement($captchaField);
            $displayCaptcha = true;
        }

        $controller->assign('display_captcha', $displayCaptcha);
        $controller->assign('display_photo', $displayPhoto);
        $controller->assign('display_terms_of_use', $displayTermsOfUse);

        if ( MT::getRequest()->isPost() )
        {
            if ( !empty($captchaField) && $captchaField instanceof FormElement )
            {
                $captchaField->setValue(null);
            }

            if ( isset($userPhoto) && isset($_FILES[$userPhoto->getName()]['name']) )
            {
                $_POST[$userPhoto->getName()] = $_FILES[$userPhoto->getName()]['name'];
            }
        }
    }

    protected function addFieldValidator( $formField, $question )
    {
        $list = MT::getSession()->get(self::SESSION_ALL_QUESTION_LIST);

        $questionInfo = empty($list[$question['name']]) ? null : $list[$question['name']];

        if ( (string) $question['base'] === '1' )
        {
            if ( !empty($questionInfo['realName']) && $questionInfo['realName'] === 'email' )
            {
                $formField->addValidator(new BASE_CLASS_JoinEmailValidator());
            }

            if ( !empty($questionInfo['realName']) && $questionInfo['realName'] === 'username' )
            {
                $formField->addValidator(new BASE_CLASS_JoinUsernameValidator());
            }

            if ( $question['name'] === 'password' )
            {
                $passwordRepeat = BOL_QuestionService::getInstance()->getPresentationClass($question['presentation'], 'repeatPassword');
                $passwordRepeat->setLabel(MT::getLanguage()->text('base', 'questions_question_repeat_password_label'));
                $passwordRepeat->setRequired((string) $question['required'] === '1');
                $this->addElement($passwordRepeat);

                $formField->addValidator(new PasswordValidator());
            }
        }
    }

    protected function setFieldOptions( $formField, $questionName, array $questionValues )
    {
        $realQuestionList = MT::getSession()->get(self::SESSION_REAL_QUESTION_LIST);

        $name = $questionName;
        if ( !empty($realQuestionList[$questionName]) )
        {
            $name = $realQuestionList[$questionName];
        }

        parent::setFieldOptions($formField, $name, $questionValues);
    }

    public function isLastStep()
    {
        return $this->isLastStep;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getAccountType()
    {
        return $this->accountType;
    }

    public function addEmptyClass( $className )
    {
        MT::getDocument()->addStyleDeclaration("
            .{$className}
            {
                
            } ");
    }

    public function clearSession()
    {
        MT::getSession()->delete(self::SESSION_REAL_QUESTION_LIST);
        MT::getSession()->delete(self::SESSION_ALL_QUESTION_LIST);
    }

    public function getSortedQuestionsList()
    {
        return $this->sortedQuestionsList;
    }
}

class PasswordValidator extends BASE_CLASS_PasswordValidator
{

    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct()
    {
        parent::__construct();
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
                    if( !window.join.validatePassword() )
                    {
                        throw window.join.errors['password']['error'];
                    }
                },
                getErrorMessage : function()
                {
                       if( window.join.errors['password']['error'] !== undefined ){ return window.join.errors['password']['error'] }
                       else{ return " . json_encode($this->getError()) . " }
                }
        }";
    }
}

class JoinRenderException extends Exception
{
    
}
