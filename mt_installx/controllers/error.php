<?php

class INSTALL_CTRL_Error extends INSTALL_ActionController
{
    public function notFound()
    {
        $this->redirect(MT::getRouter()->urlForRoute('requirements'));
    }
}