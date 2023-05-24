<?php

class ADMIN_CTRL_Settings extends ADMIN_CTRL_Abstract
{

    public function __construct()
    {
        parent::__construct();
    }

    private function getMenu()
    {
        $language = MT::getLanguage();

        $menuItems = array();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('admin', 'menu_item_basics'));
        $item->setUrl(MT::getRouter()->urlForRoute('admin_settings_main'));
        $item->setKey('basics');
        $item->setIconClass('mt_ic_gear_wheel');
        $item->setOrder(0);
        $menuItems[] = $item;

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('admin', 'menu_item_page_settings'));
        $item->setUrl(MT::getRouter()->urlForRoute('admin_settings_page'));
        $item->setKey('page');
        $item->setIconClass('mt_ic_file');
        $item->setOrder(1);
        $menuItems[] = $item;

        if ( !defined('MT_PLUGIN_XP') )
        {
            $item = new BASE_MenuItem();
            $item->setLabel($language->text('admin', 'menu_item_mail_settings'));
            $item->setUrl(MT::getRouter()->urlForRoute('admin_settings_mail'));
            $item->setKey('mail');
            $item->setIconClass('mt_ic_mail');
            $item->setOrder(2);
            $menuItems[] = $item;
        }

        return new BASE_CMP_ContentMenu($menuItems);
    }

    public function index()
    {
        if ( !MT::getRequest()->isAjax() )
        {
            MT::getNavigation()->activateMenuItem(MT_Navigation::ADMIN_SETTINGS, 'admin', 'sidebar_menu_item_main_settings');
        }

        $language = MT::getLanguage();

        $configSaveForm = new ConfigSaveForm();
        $this->addForm($configSaveForm);


        $configs = MT::getConfig()->getValues('base');

        if ( MT::getRequest()->isPost() && $configSaveForm->isValid($_POST) && isset($_POST['save']) )
        {
            $res = $configSaveForm->process();
            MT::getFeedback()->info($language->text('admin', 'main_settings_updated'));

            $this->redirect();
        }

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getDocument()->setHeading(MT::getLanguage()->text('admin', 'heading_main_settings'));
            MT::getDocument()->setHeadingIconClass('mt_ic_gear_wheel');
        }

        $configSaveForm->getElement('siteTitle')->setValue($configs['site_name']);

        $this->assign('showVerifyButton', false);

        if ( defined('MT_PLUGIN_XP') )
        {
            $this->assign('showVerifyButton', $configs['unverify_site_email'] !== $configs['site_email']);
            $configSaveForm->getElement('siteEmail')->setValue($configs['unverify_site_email']);
        }
        else
        {
            $configSaveForm->getElement('siteEmail')->setValue($configs['site_email']);
        }


        $configSaveForm->getElement('tagline')->setValue($configs['site_tagline']);
        $configSaveForm->getElement('description')->setValue($configs['site_description']);
        $configSaveForm->getElement('timezone')->setValue($configs['site_timezone']);
        $configSaveForm->getElement('relativeTime')->setValue($configs['site_use_relative_time'] === '1' ? true : false);
        $configSaveForm->getElement('militaryTime')->setValue($configs['military_time'] === '1' ? true : false);
        $configSaveForm->getElement('currency')->setValue($configs['billing_currency']);
        $configSaveForm->getElement('enableCaptcha')->setValue($configs['enable_captcha']);

        $language->addKeyForJs('admin', 'verify_site_email');

        $jsDir = MT::getPluginManager()->getPlugin("admin")->getStaticJsUrl();
        MT::getDocument()->addScript($jsDir . "main_settings.js");

        $script = ' var main_settings = new mainSettings( ' . json_encode(MT::getRouter()->urlFor("ADMIN_CTRL_Settings", "ajaxResponder")) . ' )';

        MT::getDocument()->addOnloadScript($script);
    }

    public function userInput()
    {
        $language = MT::getLanguage();
        $config = MT::getConfig();

        $settingsForm = new Form('input_settings');

        $userCustomHtml = new CheckboxField('user_custom_html');
        $userCustomHtml->setLabel($language->text('admin', 'input_settings_user_custom_html_disable_label'));
        $userCustomHtml->setDescription($language->text('admin', 'input_settings_user_custom_html_disable_desc'));
        $settingsForm->addElement($userCustomHtml);

        $userRichMedia = new CheckboxField('user_rich_media');
        $userRichMedia->setLabel($language->text('admin', 'input_settings_user_rich_media_disable_label'));
        $userRichMedia->setDescription($language->text('admin', 'input_settings_user_rich_media_disable_desc'));
        $settingsForm->addElement($userRichMedia);

        $commentsRichMedia = new CheckboxField('comments_rich_media');
        $commentsRichMedia->setLabel($language->text('admin', 'input_settings_comments_rich_media_disable_label'));
        $commentsRichMedia->setDescription($language->text('admin', 'input_settings_comments_rich_media_disable_desc'));
        $settingsForm->addElement($commentsRichMedia);

        $maxUploadMaxFilesize = BOL_FileService::getInstance()->getUploadMaxFilesize();

        $this->assign('maxUploadMaxFilesize', $maxUploadMaxFilesize);

        $maxUploadMaxFilesizeValidator = new FloatValidator(0, $maxUploadMaxFilesize);
        $maxUploadMaxFilesizeValidator->setErrorMessage($language->text('admin', 'settings_max_upload_size_error'));

        $maxUploadSize = new TextField('max_upload_size');
        $maxUploadSize->setLabel($language->text('admin', 'input_settings_max_upload_size_label'));
        $maxUploadSize->addValidator($maxUploadMaxFilesizeValidator);
        $settingsForm->addElement($maxUploadSize);

        $resourceList = new Textarea('resource_list');
        $resourceList->setLabel($language->text('admin', 'input_settings_resource_list_label'));
        $resourceList->setDescription($language->text('admin', 'input_settings_resource_list_desc'));
        $settingsForm->addElement($resourceList);
        
        $attchMaxUploadSize = new TextField('attch_max_upload_size');
        $attchMaxUploadSize->setLabel($language->text('admin', 'input_settings_attch_max_upload_size_label'));
        $attchMaxUploadSize->addValidator($maxUploadMaxFilesizeValidator);
        $settingsForm->addElement($attchMaxUploadSize);

        $attchExtList = new Textarea('attch_ext_list');
        $attchExtList->setLabel($language->text('admin', 'input_settings_attch_ext_list_label'));
        $attchExtList->setDescription($language->text('admin', 'input_settings_attch_ext_list_desc'));
        $attchExtList->addValidator( new FileExtensionValidator() );
        $settingsForm->addElement($attchExtList);

        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $settingsForm->addElement($submit);

        $this->addForm($settingsForm);

        if ( MT::getRequest()->isPost() )
        {
            if ( $settingsForm->isValid($_POST) )
            {
                $data = $settingsForm->getValues();

                $config->saveConfig('base', 'tf_comments_rich_media_disable', (int) $data['comments_rich_media']);
                $config->saveConfig('base', 'tf_user_custom_html_disable', (int) $data['user_custom_html']);
                $config->saveConfig('base', 'tf_user_rich_media_disable', (int) $data['user_rich_media']);
                $config->saveConfig('base', 'tf_max_pic_size', round((float) $data['max_upload_size'], 2));
                $config->saveConfig('base', 'attch_file_max_size_mb', round((float) $data['attch_max_upload_size'], 2));

                if ( !empty($data['resource_list']) )
                {
                    $res = array_unique(preg_split('/' . PHP_EOL . '/', $data['resource_list']));
                    $config->saveConfig('base', 'tf_resource_list', json_encode(array_map('trim', $res)));
                }

                $extList = array();

                if ( !empty($data['attch_ext_list']) )
                {
                    $extList = array_unique(preg_split('/' . PHP_EOL . '/', $data['attch_ext_list']));
                }

                $config->saveConfig('base', 'attch_ext_list', json_encode(array_map('trim', $extList)));

                MT::getFeedback()->info($language->text('admin', 'settings_submit_success_message'));
                $this->redirect();
            }
            else
            {
                MT::getFeedback()->error($language->text('admin', 'settings_submit_error_message'));
            }

        }

        $userCustomHtml->setValue($config->getValue('base', 'tf_user_custom_html_disable'));
        $userRichMedia->setValue($config->getValue('base', 'tf_user_rich_media_disable'));
        $commentsRichMedia->setValue($config->getValue('base', 'tf_comments_rich_media_disable'));
        $maxUploadSize->setValue(round((float) $config->getValue('base', 'tf_max_pic_size'), 2));
        $resourceList->setValue(implode(PHP_EOL, json_decode($config->getValue('base', 'tf_resource_list'))));
        $attchMaxUploadSize->setValue(round((float) $config->getValue('base', 'attch_file_max_size_mb'), 2));
        $attchExtList->setValue(implode(PHP_EOL, json_decode($config->getValue('base', 'attch_ext_list'))));

        MT::getDocument()->setHeading(MT::getLanguage()->text('admin', 'heading_user_input_settings'));
        MT::getDocument()->setHeadingIconClass('mt_ic_gear_wheel');
    }

    public function user()
    {
        if ( !MT::getRequest()->isAjax() )
        {
            MT::getNavigation()->activateMenuItem(MT_Navigation::ADMIN_SETTINGS, 'admin', 'sidebar_menu_item_user_settings');
        }

        $language = MT::getLanguage();

        $avatarService = BOL_AvatarService::getInstance();

        if ( isset($_GET['del-avatar']) && in_array($_GET['del-avatar'], array(1, 2)) )
        {
            $del = $avatarService->deleteCustomDefaultAvatar((int) $_GET['del-avatar']);
            if ( $del )
            {
                MT::getFeedback()->info($language->text('admin', 'default_avatar_deleted'));
            }

            $this->redirect(MT::getRouter()->urlForRoute('admin_settings_user'));
        }

        $uploadMaxFilesize = (float) ini_get("upload_max_filesize");
        $postMaxSize = (float) ini_get("post_max_size");

        $maxUploadMaxFilesize = BOL_FileService::getInstance()->getUploadMaxFilesize();
        $this->assign('maxUploadMaxFilesize', $maxUploadMaxFilesize);

        $userSettingsForm = MT::getClassInstance('UserSettingsForm', $maxUploadMaxFilesize);
        $this->addForm($userSettingsForm);

        $conf = MT::getConfig();
        
        $avatarSize = $conf->getValue('base', 'avatar_size');
        $bigAvatarSize = $conf->getValue('base', 'avatar_big_size');
        $avatarUploadSize = $conf->getValue('base', 'avatar_max_upload_size');

        $userSettingsForm->getElement('avatar_max_upload_size')->setValue((float)$avatarUploadSize);
        $userSettingsForm->getElement('avatarSize')->setValue($avatarSize);
        $userSettingsForm->getElement('bigAvatarSize')->setValue($bigAvatarSize);
        $userSettingsForm->getElement('displayName')->setValue($conf->getValue('base', 'display_name_question'));

        // privacy
        $userSettingsForm->getElement('who_can_join')->setValue($conf->getValue('base', 'who_can_join'));
        $userSettingsForm->getElement('who_can_invite')->setValue($conf->getValue('base', 'who_can_invite'));
        $userSettingsForm->getElement('guests_can_view')->setValue($conf->getValue('base', 'guests_can_view'));
        $userSettingsForm->getElement('user_approve')->setValue($conf->getValue('base', 'mandatory_user_approve'));

        // profile questions 
        $userSettingsForm->getElement('user_view_presentation')->
                setValue((MT::getConfig()->getValue('base', 'user_view_presentation') == 'tabs'));

        $this->assign('displayConfirmEmail', !defined('MT_PLUGIN_XP'));

        if ( MT::getRequest()->isPost() && $userSettingsForm->isValid($_POST) )
        {
            if ( !empty($_FILES['avatar']['tmp_name']) && !UTIL_File::validateImage($_FILES['avatar']['name'])
                || !empty($_FILES['bigAvatar']['tmp_name']) && !UTIL_File::validateImage($_FILES['bigAvatar']['name']) )
            {
                MT::getFeedback()->error($language->text('base', 'not_valid_image'));
                $this->redirect();
            }

            $values = $userSettingsForm->getValues();
            $guestPassword = MT_Config::getInstance()->getValue('base', 'guests_can_view_password');

            if ( (int) $values['guests_can_view'] === 3 && empty($values['password']) && is_null($guestPassword) )
            {
                MT::getFeedback()->error($language->text('admin', 'permission_global_privacy_empty_pass_error_message'));
                $this->redirect();
            }
            else if ( (int) $values['guests_can_view'] === 3 && strlen(trim($values['password'])) < 4 && strlen(trim($values['password'])) > 0 )
            {
                MT::getFeedback()->error($language->text('admin', 'permission_global_privacy_pass_length_error_message'));
                $this->redirect();
            }
            
        

            $res = $userSettingsForm->process();
            MT::getFeedback()->info($language->text('admin', 'user_settings_updated'));
            $this->redirect();
        }

        $avatar = $avatarService->getDefaultAvatarUrl(1);
        $avatarBig = $avatarService->getDefaultAvatarUrl(2);
        $this->assign('avatar', $avatar);
        $this->assign('avatarBig', $avatarBig);

        $custom = json_decode($conf->getValue('base', 'default_avatar'), true);
        $this->assign('customAvatar', $custom);

        $language->addKeyForJs('admin', 'confirm_avatar_delete');

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getDocument()->setHeading(MT::getLanguage()->text('admin', 'heading_user_settings'));
            MT::getDocument()->setHeadingIconClass('mt_ic_gear_wheel');
        }

        MT::getNavigation()->deactivateMenuItems(MT_Navigation::ADMIN_SETTINGS);
    }

    public function page()
    {
        if ( !MT::getRequest()->isAjax() )
        {
            MT::getNavigation()->activateMenuItem(MT_Navigation::ADMIN_SETTINGS, 'admin', 'sidebar_menu_item_main_settings');
        }

        $language = MT::getLanguage();

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getDocument()->setHeading(MT::getLanguage()->text('admin', 'heading_page_settings'));
            MT::getDocument()->setHeadingIconClass('mt_ic_file');
        }

        $form = new Form('page_settings');
        $form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);
        $this->addForm($form);

        $headCode = new Textarea('head_code');
        $headCode->setLabel($language->text('admin', 'page_settings_form_headcode_label'));
        $headCode->setDescription($language->text('admin', 'page_settings_form_headcode_desc'));
        $form->addElement($headCode);

        $bottomCode = new Textarea('bottom_code');
        $bottomCode->setLabel($language->text('admin', 'page_settings_form_bottomcode_label'));
        $bottomCode->setDescription($language->text('admin', 'page_settings_form_bottomcode_desc'));
        $form->addElement($bottomCode);

        $favicon = new FileField('favicon');
        $favicon->setLabel($language->text('admin', 'page_settings_form_favicon_label'));
        $favicon->setDescription($language->text('admin', 'page_settings_form_favicon_desc'));
        $form->addElement($favicon);

        $enableFavicon = new CheckboxField('enable_favicon');
        $form->addElement($enableFavicon);

        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $form->addElement($submit);

        $faviconPath = MT::getPluginManager()->getPlugin('base')->getUserFilesDir() . 'favicon.ico';
        $faviconUrl = MT::getPluginManager()->getPlugin('base')->getUserFilesUrl() . 'favicon.ico';

        $this->assign('faviconSrc', $faviconUrl);

        if ( MT::getRequest()->isPost() )
        {
            if ( $form->isValid($_POST) )
            {
                $data = $form->getValues();
                MT::getConfig()->saveConfig('base', 'html_head_code', $data['head_code']);
                MT::getConfig()->saveConfig('base', 'html_prebody_code', $data['bottom_code']);

                if ( !empty($_FILES['favicon']['name']) )
                {
                    if ( (int) $_FILES['favicon']['error'] === 0 && is_uploaded_file($_FILES['favicon']['tmp_name']) && UTIL_File::getExtension($_FILES['favicon']['name']) === 'ico' )
                    {
                        if ( file_exists($faviconPath) )
                        {
                            @unlink($faviconPath);
                        }

                        @move_uploaded_file($_FILES['favicon']['tmp_name'], $faviconPath);

                        if ( file_exists($_FILES['favicon']['tmp_name']) )
                        {
                            @unlink($_FILES['favicon']['tmp_name']);
                        }
                    }
                    else
                    {
                        MT::getFeedback()->error($language->text('admin', 'page_settings_favicon_submit_error_message'));
                    }
                }

                MT::getConfig()->saveConfig('base', 'favicon', !empty($data['enable_favicon']));
                MT::getFeedback()->info($language->text('admin', 'settings_submit_success_message'));
            }
            else
            {
                MT::getFeedback()->error($language->text('admin', 'settings_submit_error_message'));
            }

            $this->redirect();
        }

        $headCode->setValue(MT::getConfig()->getValue('base', 'html_head_code'));
        $bottomCode->setValue(MT::getConfig()->getValue('base', 'html_prebody_code'));
        $enableFavicon->setValue((int) MT::getConfig()->getValue('base', 'favicon'));
        $this->assign('faviconEnabled', MT::getConfig()->getValue('base', 'favicon'));

        $script = "$('#{$enableFavicon->getId()}').change(function(){ if(this.checked){ $('#favicon_enabled').show();$('#favicon_desabled').hide(); $('{$favicon->getId()}').attr('disabled', true);}else{ $('#favicon_enabled').hide();$('#favicon_desabled').show(); $('{$favicon->getId()}').attr('disabled', false);} });";
        MT::getDocument()->addOnloadScript($script);
    }

    public function mail()
    {
        if ( defined('MT_PLUGIN_XP') )
        {
            throw new Redirect404Exception();
        }

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getNavigation()->activateMenuItem(MT_Navigation::ADMIN_SETTINGS, 'admin', 'sidebar_menu_item_main_settings');
        }

        $language = MT::getLanguage();

        $mailSettingsForm = new MailSettingsForm();
        $this->addForm($mailSettingsForm);

        $configs = MT::getConfig()->getValues('base');

        //Mail settings
        $mailSettingsForm->getElement('mailSmtpEnabled')->setValue((bool) $configs['mail_smtp_enabled']);

        $mailSettingsForm->getElement('mailSmtpHost')->setValue($configs['mail_smtp_host'])->setRequired(true);
        $mailSettingsForm->getElement('mailSmtpUser')->setValue($configs['mail_smtp_user']);
        $mailSettingsForm->getElement('mailSmtpPassword')->setValue($configs['mail_smtp_password']);
        $mailSettingsForm->getElement('mailSmtpPort')->setValue($configs['mail_smtp_port']);
        $mailSettingsForm->getElement('mailSmtpConnectionPrefix')->setValue($configs['mail_smtp_connection_prefix']);

        if ( MT::getRequest()->isPost() && $mailSettingsForm->isValid($_POST) )
        {
            $res = $mailSettingsForm->process();
            MT::getFeedback()->info($language->text('admin', 'mail_settings_updated'));
            $this->redirect();
        }

        if ( !MT::getRequest()->isAjax() )
        {
            MT::getDocument()->setHeading(MT::getLanguage()->text('admin', 'heading_mail_settings'));
            MT::getDocument()->setHeadingIconClass('mt_ic_mail');

            MT::getNavigation()->activateMenuItem(MT_Navigation::ADMIN_SETTINGS, 'admin', 'sidebar_menu_item_main_settings');
        }

        $smtpEnabled = false;
        if ( BOL_MailService::getInstance()->getTransfer() === BOL_MailService::TRANSFER_SMTP )
        {
            $smtpTestresponder = json_encode(MT::getRouter()->urlFor('ADMIN_CTRL_Settings', 'ajaxSmtpTestConnection'));
            $readyJs = "
                jQuery('#smtp_test_connection').click(function(){
                    window.MT.inProgressNode(this);
                    var self = this;
                    jQuery.get($smtpTestresponder, function(r){
                        window.MT.activateNode(self);
                        alert(r);
                    });
                });
            ";
            MT::getDocument()->addOnloadScript($readyJs);
            $smtpEnabled = true;
        }

        $this->assign('smtpEnabled', $smtpEnabled);
    }

    public function ajaxSmtpTestConnection()
    {
        if ( !MT::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        try
        {
            $result = BOL_MailService::getInstance()->smtpTestConnection();
        }
        catch ( LogicException $e )
        {
            exit($e->getMessage());
        }

        if ( $result )
        {
            $responce = MT::getLanguage()->text('admin', 'smtp_test_connection_success');
        }
        else
        {
            $responce = MT::getLanguage()->text('admin', 'smtp_test_connection_failed');
        }

        exit($responce);
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
            case 'sendVerifyEmail':

                $result = false;

                $email = trim($_POST["email"]);

                if ( UTIL_Validator::isEmailValid($email) )
                {
                    MT::getConfig()->saveConfig('base', 'unverify_site_email', $email);

                    $siteEmail = MT::getConfig()->getValue('base', 'site_email');

                    if ( $siteEmail !== $email )
                    {
                        $type = 'info';
                        BOL_EmailVerifyService::getInstance()->sendSiteVerificationMail(false);
                        $message = MT::getLanguage()->text('base', 'email_verify_verify_mail_was_sent');
                        $result = true;
                    }
                    else
                    {
                        $type = 'warning';
                        $message = MT::getLanguage()->text('admin', 'email_already_verify');
                    }
                }

                $responce = json_encode(array('result' => $result, 'type' => $type, 'message' => $message));

                break;
        }

        exit($responce);
    }
}

/**
 * Save Configurations form class
 */
class ConfigSaveForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('configSaveForm');

        $language = MT::getLanguage();

        $siteTitleField = new TextField('siteTitle');
        $siteTitleField->setRequired(true);
        $this->addElement($siteTitleField);

        $siteEmailField = new TextField('siteEmail');
        $siteEmailField->setRequired(true);
        $siteEmailField->addValidator(new EmailValidator());
        $this->addElement($siteEmailField);

        $taglineField = new TextField('tagline');
        $taglineField->setRequired(true);
        $this->addElement($taglineField);

        $descriptionField = new Textarea('description');
        $descriptionField->setRequired(true);
        $this->addElement($descriptionField);
        
        $dispalyCaptcha = new CheckboxField('enableCaptcha');
        $this->addElement($dispalyCaptcha);

        $timezoneField = new Selectbox('timezone');
        $timezoneField->setRequired(true);
        $timezoneField->setOptions(UTIL_DateTime::getTimezones());
        $this->addElement($timezoneField);

        $relativeTimeField = new CheckboxField('relativeTime');
        $this->addElement($relativeTimeField);

        $militaryTimeField = new CheckboxField('militaryTime');
        $this->addElement($militaryTimeField);

        // -- date format --
        $dateFieldFormat = new Selectbox("dateFieldFormat");
        $dateFieldFormat->setLabel($language->text('base', 'questions_config_date_field_format_label'));

        $dateFormatValue = MT::getConfig()->getValue('base', 'date_field_format');

        $dateFormatArray = array(BOL_QuestionService::DATE_FIELD_FORMAT_MONTH_DAY_YEAR, BOL_QuestionService::DATE_FIELD_FORMAT_DAY_MONTH_YEAR);

        $options = array();

        foreach ( $dateFormatArray as $key )
        {
            $options[$key] = $language->text('base', 'questions_config_date_field_format_' . $key);
        }

        $dateFieldFormat->setOptions($options);
        $dateFieldFormat->setHasInvitation(false);
        $dateFieldFormat->setValue($dateFormatValue);
        $dateFieldFormat->setRequired();

        $this->addElement($dateFieldFormat);
        // -- date format --

        $currencyField = new Selectbox('currency');
        $currList = BOL_BillingService::getInstance()->getCurrencies();
        foreach ( $currList as $key => $cur )
        {
            $currList[$key] = $key . ' (' . $cur . ')';
        }
        $currencyField->setOptions($currList);
        $currencyField->setLabel($language->text('admin', 'currency'));
        $currencyField->setRequired(true);
        $this->addElement($currencyField);

//        $imagesAllowPicUpload = new CheckboxField('tf-allow-pic-upload');
//
//        $imagesAllowPicUpload->setLabel(MT::getLanguage()->text('base', 'tf_allmt_pics'))
//            ->setValue(MT::getConfig()->getValue('base', 'tf_allmt_pic_upload'));
//
//        $this->addElement($imagesAllowPicUpload);
//
//        $imageMaxSizeField = new TextField('tf-max-image-size');
//
//        $imageMaxSizeField->setValue(MT::getConfig()->getValue('base', 'tf_max_pic_size'))
//            ->setLabel(MT::getLanguage()->text('base', 'tf_max_img_size'))
//            ->addValidator(new IntValidator())->setRequired();
//
//        $this->addElement($imageMaxSizeField);
        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $this->addElement($submit);
    }

    /**
     * Updates video plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $config = MT::getConfig();

        //begin update lang cache
        $siteName = $config->getValue('base', 'site_name');

        $config->saveConfig('base', 'site_name', $values['siteTitle']);

        if ( $siteName != $config->getValue('base', 'site_name') )
        {
            BOL_LanguageService::getInstance()->generateCacheForAllActiveLanguages();
        }

        if ( defined('MT_PLUGIN_XP') )
        {
            //end update lang cache
            $siteEmail = $config->getValue('base', 'unverify_site_email');

            if ( $siteEmail !== trim($values['siteEmail']) )
            {
                $config->saveConfig('base', 'unverify_site_email', $values['siteEmail']);
                BOL_EmailVerifyService::getInstance()->sendSiteVerificationMail();
            }
        }
        else
        {
            $config->saveConfig('base', 'site_email', $values['siteEmail']);
        }

//        join_display_photo_upload  	true  	Display photo upload on join page
//        join_photo_upload_set_required 	false 	Set required photo upload field on join page
//        join_display_terms_of_use

        $config->saveConfig('base', 'site_tagline', $values['tagline']);
        $config->saveConfig('base', 'site_description', $values['description']);
        $config->saveConfig('base', 'enable_captcha', $values['enableCaptcha']);
        $config->saveConfig('base', 'site_timezone', $values['timezone']);
        $config->saveConfig('base', 'site_use_relative_time', $values['relativeTime'] ? '1' : '0');
        $config->saveConfig('base', 'military_time', $values['militaryTime'] ? '1' : '0');
        $config->saveConfig('base', 'date_field_format', $values['dateFieldFormat']);
        $config->saveConfig('base', 'billing_currency', $values['currency']);
//        $config->saveConfig('base', 'tf_allmt_pic_upload', $values['tf-allow-pic-upload']);
//        $config->saveConfig('base', 'tf_max_pic_size', $values['tf-max-image-size']);

        return array('result' => true);
    }

}

/**
 * Save Configurations form class
 */
class UserSettingsForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct($maxUploadMaxFilesize)
    {
        parent::__construct('userSettingsForm');

        $this->setEnctype("multipart/form-data");

        $language = MT::getLanguage();

        // avatar size Field
        $avatarSize = new TextField('avatarSize');
        $avatarSize->setRequired(true);
        $validator = new IntValidator(40, 150);
        $validator->setErrorMessage($language->text('admin', 'user_settings_avatar_size_error', array('max' => 150)));
        $avatarSize->addValidator($validator);
        $this->addElement($avatarSize->setLabel($language->text('admin', 'user_settings_avatar_size_label')));

        // big avatar size Field
        $bigAvatarSize = new TextField('bigAvatarSize');
        $bigAvatarSize->setRequired(true);
        $validator = new IntValidator(150, 250);
        $validator->setErrorMessage($language->text('admin', 'user_settings_big_avatar_size_error', array('max' => 250)));
        $bigAvatarSize->addValidator($validator);
        $this->addElement($bigAvatarSize->setLabel($language->text('admin', 'user_settings_big_avatar_size_label')));
        
        // --- avatar max size

        $maxUploadMaxFilesizeValidator = new FloatValidator(0, $maxUploadMaxFilesize);
        $maxUploadMaxFilesizeValidator->setErrorMessage($language->text('admin', 'settings_max_upload_size_error'));
        
        $avatarMaxUploadSize = new TextField('avatar_max_upload_size');
        $avatarMaxUploadSize->setLabel($language->text('admin', 'input_settings_avatar_max_upload_size_label'));
        $avatarMaxUploadSize->addValidator($maxUploadMaxFilesizeValidator);
        $this->addElement($avatarMaxUploadSize);
        // --- avatar max size
        
        if ( !defined('MT_PLUGIN_XP') )
        {
            // confirm Email
            $confirmEmail = new CheckboxField('confirmEmail');
            $confirmEmail->setValue(MT::getConfig()->getValue('base', 'confirm_email'));
            $this->addElement($confirmEmail->setLabel($language->text('admin', 'user_settings_confirm_email')));
        }

        // display name Field
        $displayNameField = new Selectbox('displayName');
        $displayNameField->setRequired(true);

        $questions = array(
            'username' => $language->text('base', 'questions_question_username_label'),
            'realname' => $language->text('base', 'questions_question_realname_label')
        );

        $displayNameField->setHasInvitation(false);
        $displayNameField->setOptions($questions);
        $this->addElement($displayNameField->setLabel($language->text('admin', 'user_settings_display_name')));

        $avatar = new FileField('avatar');
        $this->addElement($avatar);

        $bigAvatar = new FileField('bigAvatar');
        $this->addElement($bigAvatar);

        // --

        $joinConfigField = new Selectbox('join_display_photo_upload');

        $options = array(
            BOL_UserService::CONFIG_JOIN_DISPLAY_PHOTO_UPLOAD => $language->text('base', 'config_join_display_photo_upload_display_label'),
            BOL_UserService::CONFIG_JOIN_DISPLAY_AND_SET_REQUIRED_PHOTO_UPLOAD => $language->text('base', 'config_join_display_photo_upload_display_and_require_label'),
            BOL_UserService::CONFIG_JOIN_NOT_DISPLAY_PHOTO_UPLOAD => $language->text('base', 'config_join_display_photo_upload_not_display_label')
        );

        $joinConfigField->addOptions($options);
        $joinConfigField->setHasInvitation(false);
        $joinConfigField->setValue(MT::getConfig()->getValue('base', 'join_display_photo_upload'));
        $this->addElement($joinConfigField);

        // --

        $joinConfigField = new CheckboxField('join_display_terms_of_use');
        $joinConfigField->setValue(MT::getConfig()->getValue('base', 'join_display_terms_of_use'));
        $this->addElement($joinConfigField);

        //--- privacy -----///
        $config = MT::getConfig();
        $baseConfigs = $config->getValues('base');

        $userApprove = new CheckboxField('user_approve');
        $userApprove->setLabel($language->text('admin', 'permissions_index_user_approve'));
        $this->addElement($userApprove);

        $whoCanJoin = new RadioField('who_can_join');
        $whoCanJoin->addOptions(array('1' => $language->text('admin', 'permissions_index_anyone_can_join'), '2' => $language->text('admin', 'permissions_index_by_invitation_only_can_join')));
        $whoCanJoin->setLabel($language->text('admin', 'permissions_index_who_can_join'));
        $this->addElement($whoCanJoin);

        $whoCanInvite = new RadioField('who_can_invite');
        $whoCanInvite->addOptions(array('1' => $language->text('admin', 'permissions_index_all_users_can_invate'), '2' => $language->text('admin', 'permissions_index_admin_only_can_invate')));
        $whoCanInvite->setLabel($language->text('admin', 'permissions_index_who_can_invite'));
        $this->addElement($whoCanInvite);

        $guestsCanView = new RadioField('guests_can_view');
        $guestsCanView->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '2' => $language->text('admin', 'permissions_index_no'), '3' => $language->text('admin', 'permissions_index_with_password')));
        $guestsCanView->setLabel($language->text('admin', 'permissions_index_guests_can_view_site'));
        $guestsCanView->setDescription($language->text('admin', 'permissions_idex_if_not_yes_will_override_settings'));
        $this->addElement($guestsCanView);

        $password = new TextField('password');
        $password->setHasInvitation(true);
        if($baseConfigs['guests_can_view'] == 3)
        {
            $password->setInvitation($language->text('admin', 'change_password'));
        }
        else
        {
            $password->setInvitation($language->text('admin', 'add_password'));
        }
        $this->addElement($password);
        // --- //
        
        //-- profile questions --//
        $userViewPresentationnew = new CheckboxField("user_view_presentation");
        $userViewPresentationnew->setLabel($language->text('base', 'questions_config_user_view_presentation_label'));
        $userViewPresentationnew->setDescription($language->text('base', 'questions_config_user_view_presentation_description'));

        $this->addElement($userViewPresentationnew);
        // --- //

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $this->addElement($submit);
    }

    /**
     * Updates user settings configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = MT::getConfig();

        $config->saveConfig('base', 'avatar_size', $values['avatarSize']);
        $config->saveConfig('base', 'avatar_big_size', $values['bigAvatarSize']);
        $config->saveConfig('base', 'display_name_question', $values['displayName']);

        $config->saveConfig('base', 'join_display_photo_upload', $values['join_display_photo_upload']);
        $config->saveConfig('base', 'join_display_terms_of_use', $values['join_display_terms_of_use']);
        
        $config->saveConfig('base', 'avatar_max_upload_size', round((float) $values['avatar_max_upload_size'], 2));

        if ( !defined('MT_PLUGIN_XP') )
        {
            $config->saveConfig('base', 'confirm_email', $values['confirmEmail']);
        }

        $avatarService = BOL_AvatarService::getInstance();

        if ( isset($_FILES['avatar']['tmp_name']) )
        {
            $avatarService->setCustomDefaultAvatar(1, $_FILES['avatar']);
        }

        if ( isset($_FILES['bigAvatar']['tmp_name']) )
        {
            $avatarService->setCustomDefaultAvatar(2, $_FILES['bigAvatar']);
        }

        // privacy
        $config->saveConfig('base', 'who_can_join', (int) $values['who_can_join']);
        $config->saveConfig('base', 'who_can_invite', (int) $values['who_can_invite']);
        $config->saveConfig('base', 'mandatory_user_approve', ((bool) $values['user_approve'] ? 1 : 0));

        
        
        if((int) $values['guests_can_view'] == 3)
        {
            $adminEmail = MT::getUser()->getEmail();
            $senderMail = $config->getValue('base', 'site_email');
            $mail = MT::getMailer()->createMail();
            $mail->addRecipientEmail($adminEmail);
            $mail->setSender($senderMail);
            $mail->setSenderSuffix(false);
            $mail->setSubject(MT::getLanguage()->text( 'admin', 'site_password_letter_subject', array()));
            $mail->setTextContent( MT::getLanguage()->text( 'admin', 'site_password_letter_template_text', array('password' => $values['password'])));
            $mail->setHtmlContent( MT::getLanguage()->text( 'admin', 'site_password_letter_template_html', array('password' => $values['password'])));
            try
            {
                MT::getMailer()->send($mail);
            }
            catch (Exception $e)
            {
                $logger = MT::getLogger('admin.send_password_message');
                $logger->addEntry($e->getMessage());
                $logger->writeLog();
            }
            
            $values['password'] = crypt($values['password'], MT_PASSWORD_SALT);            
            $config->saveConfig('base', 'guests_can_view_password', $values['password']);
        }
        else
        {
            $config->saveConfig('base', 'guests_can_view_password', null);

        }
        
        $config->saveConfig('base', 'guests_can_view', (int) $values['guests_can_view']);

        // profile questions 
        isset($_POST['user_view_presentation'])
            ? $config->saveConfig('base', 'user_view_presentation', 'tabs')
            : $config->saveConfig('base', 'user_view_presentation', 'table');

        return array('result' => true);
    }
}

/**
 * File extension Validator
 *
 * @author Alex Ermashev <alexermashev@gmail.com>
 * @package mt_core
 * @since 1.8.4
 */
class FileExtensionValidator extends MT_Validator
{
    /**
     * List of disallowed extensions
     *
     * @var array
     */
    protected $disallowedExtensions = array(
        'php*',
        'phtml'
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->errorMessage = MT::getLanguage()->text('admin', 'wrong_file_extension', array(
            'extensions' => implode(',', $this->disallowedExtensions)
        ));
    }

    public function isValid( $value )
    {
        $values = explode(PHP_EOL, $value);

        foreach($values as $extension)
        {
            foreach($this->disallowedExtensions as $disallowedExtensions)
            {
                if ( preg_match('/' . $disallowedExtensions . '/i', $extension) )
                {
                    return false;
                }
            }
        }

        return true;
    }
}

/**
 * Save Configurations form class
 */
class MailSettingsForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('mailSettingsForm');

        $language = MT::getLanguage();

        // Mail Settings
        $smtpField = new CheckboxField('mailSmtpEnabled');
        $this->addElement($smtpField);

        $smtpField = new TextField('mailSmtpHost');
        $this->addElement($smtpField);

        $smtpField = new TextField('mailSmtpUser');
        $this->addElement($smtpField);

        $smtpField = new TextField('mailSmtpPassword');
        $this->addElement($smtpField);

        $smtpField = new TextField('mailSmtpPort');
        $this->addElement($smtpField);

        $smtpField = new Selectbox('mailSmtpConnectionPrefix');
        $smtpField->setHasInvitation(true);
        $smtpField->setInvitation(MT::getLanguage()->text('admin', 'mail_smtp_secure_invitation'));
        $smtpField->addOption('ssl', 'SSL');
        $smtpField->addOption('tls', 'TLS');
        $this->addElement($smtpField);

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $this->addElement($submit);
    }

    /**
     * Updates user settings configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $config = MT::getConfig();

        $config->saveConfig('base', 'mail_smtp_enabled', $values['mailSmtpEnabled'] ? '1' : '0');
        $config->saveConfig('base', 'mail_smtp_host', $values['mailSmtpHost']);
        $config->saveConfig('base', 'mail_smtp_user', $values['mailSmtpUser']);
        $config->saveConfig('base', 'mail_smtp_password', $values['mailSmtpPassword']);
        $config->saveConfig('base', 'mail_smtp_port', $values['mailSmtpPort']);
        $config->saveConfig('base', 'mail_smtp_connection_prefix', $values['mailSmtpConnectionPrefix']);

        return array('result' => true);
    }
}
