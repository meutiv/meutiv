<?php

MT::getRouter()->addRoute(new MT_Route('requirements', 'install', 'INSTALL_CTRL_Install', 'requirements'));
MT::getRouter()->addRoute(new MT_Route('site', 'install/site', 'INSTALL_CTRL_Install', 'site'));
MT::getRouter()->addRoute(new MT_Route('db', 'install/data-base', 'INSTALL_CTRL_Install', 'db'));

MT::getRouter()->addRoute(new MT_Route('install', 'install/installation', 'INSTALL_CTRL_Install', 'install'));
MT::getRouter()->addRoute(new MT_Route('install-action', 'install/installation/:action', 'INSTALL_CTRL_Install', 'install'));

MT::getRouter()->addRoute(new MT_Route('plugins', 'install/plugins', 'INSTALL_CTRL_Install', 'plugins'));
MT::getRouter()->addRoute(new MT_Route('finish', 'install/security', 'INSTALL_CTRL_Install', 'finish'));

function install_tpl_feedback_flag($flag, $class = 'error')
{
    if ( INSTALL::getFeedback()->getFlag($flag) )
    {
        return $class;
    }
    
    return '';
}

function install_tpl_feedback()
{
    $feedBack = new INSTALL_CMP_FeedBack();
    
    return $feedBack->render();
}
