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

require_once MT_DIR_ROOT . 'mt_includes/config.php';
require_once MT_DIR_ROOT . 'mt_includes/define.php';
require_once MT_DIR_UTIL . 'debug.php';
require_once MT_DIR_UTIL . 'string.php';
require_once MT_DIR_CORE . 'autoload.php';
require_once MT_DIR_CORE . 'exception.php';
require_once MT_DIR_INC . 'function.php';
require_once MT_DIR_CORE . 'mt.php';
require_once MT_DIR_CORE . 'plugin.php';
require_once MT_DIR_CORE . 'filter.php';

mb_internal_encoding('UTF-8');

if ( MT_DEBUG_MODE )
{
    ob_start();
}

spl_autoload_register(array('MT_Autoload', 'autoload'));
require_once MT_DIR_LIB_VENDOR . "autoload.php";

// adding standard package pointers
$autoloader = MT::getAutoloader();
$autoloader->addPackagePointer('MT', MT_DIR_CORE);
$autoloader->addPackagePointer('INC', MT_DIR_INC);
$autoloader->addPackagePointer('UTIL', MT_DIR_UTIL);
$autoloader->addPackagePointer('BOL', MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'bol');

// Force autoload of classes without package pointer
$classesToAutoload = array(
    'Form' => MT_DIR_CORE . 'form.php',
    'TextField' => MT_DIR_CORE . 'form_element.php',
    'HiddenField' => MT_DIR_CORE . 'form_element.php',
    'FormElement' => MT_DIR_CORE . 'form_element.php',
    'RequiredValidator' => MT_DIR_CORE . 'validator.php',
    'StringValidator' => MT_DIR_CORE . 'validator.php',
    'RegExpValidator' => MT_DIR_CORE . 'validator.php',
    'EmailValidator' => MT_DIR_CORE . 'validator.php',
    'UrlValidator' => MT_DIR_CORE . 'validator.php',
    'AlphaNumericValidator' => MT_DIR_CORE . 'validator.php',
    'IntValidator' => MT_DIR_CORE . 'validator.php',
    'InArrayValidator' => MT_DIR_CORE . 'validator.php',
    'FloatValidator' => MT_DIR_CORE . 'validator.php',
    'DateValidator' => MT_DIR_CORE . 'validator.php',
    'CaptchaValidator' => MT_DIR_CORE . 'validator.php',
    'RadioField' => MT_DIR_CORE . 'form_element.php',
    'CheckboxField' => MT_DIR_CORE . 'form_element.php',
    'Selectbox' => MT_DIR_CORE . 'form_element.php',
    'CheckboxGroup' => MT_DIR_CORE . 'form_element.php',
    'RadioField' => MT_DIR_CORE . 'form_element.php',
    'PasswordField' => MT_DIR_CORE . 'form_element.php',
    'Submit' => MT_DIR_CORE . 'form_element.php',
    'Button' => MT_DIR_CORE . 'form_element.php',
    'Textarea' => MT_DIR_CORE . 'form_element.php',
    'FileField' => MT_DIR_CORE . 'form_element.php',
    'TagsField' => MT_DIR_CORE . 'form_element.php',
    'SuggestField' => MT_DIR_CORE . 'form_element.php',
    'MultiFileField' => MT_DIR_CORE . 'form_element.php',
    'Multiselect' => MT_DIR_CORE . 'form_element.php',
    'CaptchaField' => MT_DIR_CORE . 'form_element.php',
    'InvitationFormElement' => MT_DIR_CORE . 'form_element.php',
    'Range' => MT_DIR_CORE . 'form_element.php',
    'WyswygRequiredValidator' => MT_DIR_CORE . 'validator.php',
    'DateField' => MT_DIR_CORE . 'form_element.php',
    'DateRangeInterface' => MT_DIR_CORE . 'form_element.php'
);

MT::getAutoloader()->addClassArray($classesToAutoload);

if ( defined("MT_URL_HOME") )
{
    MT::getRouter()->setBaseUrl(MT_URL_HOME);
}

if ( MT_PROFILER_ENABLE )
{
    UTIL_Profiler::getInstance();
}

require_once MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'file_log_writer.php';
require_once MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'db_log_writer.php';
require_once MT_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'err_output.php';

$errorManager = MT_ErrorManager::getInstance(MT_DEBUG_MODE);
$errorManager->setErrorOutput(new BASE_CLASS_ErrOutput());
