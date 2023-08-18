<?php

function smarty_function_offline_now( $params, $smarty )
{
    $chatNowMarkup = '';
    if ( MT::getUser()->isAuthenticated() && isset($params['userId']) && MT::getUser()->getId() != $params['userId'])
    {
        $allowChat = MT::getEventManager()->call('base.online_nmt_click', array('userId'=>MT::getUser()->getId(), 'onlineUserId'=>$params['userId']));

        if ($allowChat)
        {
            $chatNowMarkup = '<span id="mt_chat_nmt_'.$params['userId'].'" class="mt_lbutton mt_green" onclick="MT.trigger(\'base.online_nmt_click\', [ \'' . $params['userId'] . '\' ] );" >' . MT::getLanguage()->text('mailbox', 'user_list_chat_offline') . '</span><span id="mt_preloader_content_'.$params['userId'].'" class="mt_preloader_content mt_hidden"></span>';
        }
    }

    $buttonMarkup = '<div class="mt_miniic_live">'.$chatNowMarkup.'</div>';

    return $buttonMarkup;
}
?>
