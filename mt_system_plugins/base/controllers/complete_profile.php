<?php

require_once MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'controllers' . DS . 'edit.php';

class BASE_CTRL_CompleteProfile extends MT_ActionController
{
    protected $questionService;

    public function __construct()
    {
        parent::__construct();

        $this->questionService = BOL_QuestionService::getInstance();
        
        $this->setPageHeading(MT::getLanguage()->text('base', 'complete_your_profile_page_heading'));
        $this->setPageHeadingIconClass('mt_ic_user');

        $item = new BASE_MenuItem();
        $item->setLabel(MT::getLanguage()->text('base', 'complete_profile'));
        $item->setUrl(MT::getRouter()->urlForRoute("base.complete_required_questions"));
        $item->setKey('complete_profile');
        $item->setOrder(1);

        $masterpage = MT::getDocument()->getMasterPage();
        
        if ( !empty($masterpage) && method_exists($masterpage, 'getMenu') )
        {
            $menu = $masterpage->getMenu('main');

            if ( !empty($menu) )
            {
                $menu->setMenuItems(array($item));
            }
        }
    }

    public function fillAccountType( $params )
    {
        if ( !MT::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }
        
        $user = MT::getUser()->getUserObject();
        $accountType = BOL_QuestionService::getInstance()->findAccountTypeByName($user->accountType);

        if ( !empty($accountType) )
        {
            throw new Redirect404Exception();
        }

        $event = new MT_Event( MT_EventManager::ON_BEFORE_USER_COMPLETE_ACCOUNT_TYPE, array( 'user' => $user ) );
        MT::getEventManager()->trigger($event);
        
        $accounts = $this->getAccountTypes();
        
        if ( count($accounts) == 1 )
        {
            $accountTypeList = array_keys($accounts);
            $firstAccountType = reset($accountTypeList);
            $accountType = BOL_QuestionService::getInstance()->findAccountTypeByName($firstAccountType);

            if ( $accountType )
            {
                $user->accountType = $firstAccountType;
                BOL_UserService::getInstance()->saveOrUpdate($user);
                //BOL_PreferenceService::getInstance()->savePreferenceValue('profile_details_update_stamp', time(), $user->getId());
                $this->redirect(MT::getRouter()->urlForRoute('base_default_index'));
            }
        }

        $form = new Form('accountTypeForm');

        $joinAccountType = new Selectbox('accountType');
        $joinAccountType->setLabel(MT::getLanguage()->text('base', 'questions_question_account_type_label'));
        $joinAccountType->setRequired();
        $joinAccountType->setOptions($accounts);
        $joinAccountType->setHasInvitation(false);

        $form->addElement($joinAccountType);

        $submit = new Submit('submit');
        $submit->addAttribute('class', 'mt_button mt_ic_save');
        $submit->setValue(MT::getLanguage()->text('base', 'continue_button'));
        $form->addElement($submit);

        if ( MT::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $data = $form->getValues();            

                $this->saveRequiredQuestionsData($data, $user->id);            
                
            }
        }
        else
        {
            MT::getDocument()->addOnloadScript(" MT.info(".  json_encode(MT::getLanguage()->text('base', 'complete_profile_info')).") ");
        }
        
        $this->addForm($form);
    }

    public function fillRequiredQuestions( $params )
    {
        if ( !MT::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        $user = MT::getUser()->getUserObject();

        $accountType = BOL_QuestionService::getInstance()->findAccountTypeByName($user->accountType);

        if ( empty($accountType) )
        {
            throw new Redirect404Exception();
        }

        $language = MT::getLanguage();
        
        $event = new MT_Event( MT_EventManager::ON_BEFORE_USER_COMPLETE_PROFILE, array( 'user' => $user ) );
        MT::getEventManager()->trigger($event);
        
        // -- Edit form --

        $form = new EditQuestionForm('requiredQuestionsForm', $user->id);
        $form->setId('requiredQuestionsForm');

        $editSubmit = new Submit('submit');
        $editSubmit->addAttribute('class', 'mt_button mt_ic_save');

        $editSubmit->setValue($language->text('base', 'continue_button'));

        $form->addElement($editSubmit);

        $questions = $this->questionService->getEmptyRequiredQuestionsList($user->id);

        if ( empty($questions) )
        {
            $this->redirect(MT::getRouter()->urlForRoute('base_default_index'));
        }

        $section = null;
        $questionArray = array();
        $questionNameList = array();

        foreach ( $questions as $sort => $question )
        {
            if ( $section !== $question['sectionName'] )
            {
                $section = $question['sectionName'];
            }

            $questionArray[$section][$sort] = $questions[$sort];
            $questionNameList[] = $questions[$sort]['name'];
        }

        $this->assign('questionArray', $questionArray);

        //$questionData = $this->questionService->getQuestionData(array($user->id), $questionNameList);

        $questionValues = $this->questionService->findQuestionsValuesByQuestionNameList($questionNameList);

        $form->addQuestions($questions, $questionValues, array());

        if ( MT::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $this->saveRequiredQuestionsData($form->getValues(), $user->id);
            }
        }
        else
        {
            MT::getDocument()->addOnloadScript(" MT.info(".  json_encode(MT::getLanguage()->text('base', 'complete_profile_info')).") ");
        }

        $form->setStaticIdsForFields('requiredQuestionsForm');

        $this->addForm($form);

        $language->addKeyForJs('base', 'join_error_username_not_valid');
        $language->addKeyForJs('base', 'join_error_username_already_exist');
        $language->addKeyForJs('base', 'join_error_email_not_valid');
        $language->addKeyForJs('base', 'join_error_email_already_exist');
        $language->addKeyForJs('base', 'join_error_password_not_valid');
        $language->addKeyForJs('base', 'join_error_password_too_short');
        $language->addKeyForJs('base', 'join_error_password_too_long');

        //include js
        $onLoadJs = " window.edit = new MT_BaseFieldValidators( " .
            json_encode(array(
                'formName' => $form->getName(),
                'responderUrl' => MT::getRouter()->urlFor("BASE_CTRL_Edit", "ajaxResponder"))) . ",
                " . UTIL_Validator::EMAIL_PATTERN . ", " . UTIL_Validator::USER_NAME_PATTERN . ", " . $user->id . " ); ";

        MT::getDocument()->addOnloadScript($onLoadJs);

        $jsDir = MT::getPluginManager()->getPlugin("base")->getStaticJsUrl();
        MT::getDocument()->addScript($jsDir . "base_field_validators.js");
    }

    protected function saveRequiredQuestionsData($data, $userId)
    {
        // save user data
        if ( !empty($userId) )
        {
            if ( $this->questionService->saveQuestionsData($data, $userId) )
            {
                MT::getFeedback()->info(MT::getLanguage()->text('base', 'edit_successfull_edit'));
                
                $event = new MT_Event(MT_EventManager::ON_AFTER_USER_COMPLETE_PROFILE, array( 'userId' => $userId ));
                    
                MT::getEventManager()->trigger($event);
                //BOL_PreferenceService::getInstance()->savePreferenceValue('profile_details_update_stamp', time(), $userId);
                $this->redirect(MT::getRouter()->urlForRoute('base_default_index'));
            }
            else
            {
                MT::getFeedback()->info(MT::getLanguage()->text('base', 'edit_edit_error'));
            }
        }
        else
        {
            MT::getFeedback()->info(MT::getLanguage()->text('base', 'edit_edit_error'));
        }
    }

    protected function getAccountTypes()
    {
        // get available account types from DB
        $accountTypes = BOL_QuestionService::getInstance()->findAllAccountTypes();

        $accounts = array();

        foreach ( $accountTypes as $key => $value )
        {
            $accounts[$value->name] = MT::getLanguage()->text('base', 'questions_account_type_' . $value->name);
        }

        return $accounts;
    }
}
