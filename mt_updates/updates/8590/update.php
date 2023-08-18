<?php

UPDATE_LanguageService::getInstance()->deleteLangKey('admin', 'input_settings_allmt_photo_upload_label');
UPDATE_ConfigService::getInstance()->deleteConfig('base', 'tf_allmt_pic_upload');