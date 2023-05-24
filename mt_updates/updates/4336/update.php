<?php

if ( !file_exists(MT_DIR_USERFILES . 'plugins' . DS . 'base' . DS . 'favicon.ico') )
{
    @copy(MT_DIR_STATIC . 'favicon.ico', MT_DIR_USERFILES . 'plugins' . DS . 'base' . DS . 'favicon.ico');
}


UPDATE_LanguageService::getInstance()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'base');
