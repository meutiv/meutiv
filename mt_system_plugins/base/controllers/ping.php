<?php

class BASE_CTRL_Ping extends MT_ActionController
{
    const PING_EVENT = 'base.ping';

    public function index()
    {
        $request = json_decode($_POST['request'], true);
        $stack = $request['stack'];

        $responseStack = array();

        foreach ( $stack as $c )
        {
            $command = strip_tags(trim($c['command']));
            $params  = $c['params'];

            $event = new MT_Event(self::PING_EVENT . '.' . $command, $params);
            MT::getEventManager()->trigger($event);

            $event = new MT_Event(self::PING_EVENT, $c, $event->getData());
            MT::getEventManager()->trigger($event);

            $responseStack[] = array(
                'command' => $command,
                'result' => $event->getData()
            );
        }

        echo json_encode(array(
            'stack' => $responseStack
        ));

        exit;
    }
}