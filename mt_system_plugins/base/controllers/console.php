<?php

class BASE_CTRL_Console extends MT_ActionController
{
    public function listRsp()
    {
        $request = json_decode($_POST['request'], true);

        if ( !MT::getUser()->isAuthenticated() )
        {
            echo json_encode(['items' => []]);

            exit;
        }

        $event = new BASE_CLASS_ConsoleListEvent('console.load_list', $request, $request['data']);
        MT::getEventManager()->trigger($event);

        $responce = array();
        $responce['items'] = $event->getList();

        $responce['data'] = $event->getData();
        $responce['markup'] = array();

        /* @var $document MT_AjaxDocument */
        $document = MT::getDocument();

        $responce['markup']['scriptFiles'] = $document->getScripts();
        $responce['markup']['onloadScript'] = $document->getOnloadScript();
        $responce['markup']['styleDeclarations'] = $document->getStyleDeclarations();
        $responce['markup']['styleSheets'] = $document->getStyleSheets();
        $responce['markup']['beforeIncludes'] = $document->getScriptBeforeIncludes();

        echo json_encode($responce);

        exit;
    }
}