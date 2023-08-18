<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/sign_in.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bdffcd2_07194868',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3157e95aa8c454781afeb4a5bd817becabe78e9f' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/sign_in.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bdffcd2_07194868 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.form.php','function'=>'smarty_block_form',),1=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.block_decorator.php','function'=>'smarty_block_block_decorator',),2=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.input.php','function'=>'smarty_function_input',),3=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.submit.php','function'=>'smarty_function_submit',),4=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.label.php','function'=>'smarty_function_label',),5=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.url_for_route.php','function'=>'smarty_function_url_for_route',),6=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.text.php','function'=>'smarty_function_text',),7=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.component.php','function'=>'smarty_function_component',),));
?>
<div class="mt_sign_in_wrap">
    <h2><?php echo $_smarty_tpl->tpl_vars['siteName']->value;?>
</h2>
    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('form', array('name'=>'sign-in'));
$_block_repeat=true;
echo smarty_block_form(array('name'=>'sign-in'), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
    <div class="clearfix">
        <div class="mt_sign_in">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('block_decorator', array('name'=>'box','langLabel'=>'base+base_sign_in_cap_label'));
$_block_repeat=true;
echo smarty_block_block_decorator(array('name'=>'box','langLabel'=>'base+base_sign_in_cap_label'), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>

            <div class="mt_user_name">
                <?php echo smarty_function_input(array('name'=>'identity'),$_smarty_tpl);?>

            </div>
            <div class="mt_password">
                <?php echo smarty_function_input(array('name'=>'password'),$_smarty_tpl);?>

            </div>
            <div class="mt_form_options clearfix">
                <div class="mt_right">
                    <?php echo smarty_function_submit(array('name'=>'submit','class'=>'mt_positive'),$_smarty_tpl);?>

                </div>
                <p class="mt_remember_me"><?php echo smarty_function_input(array('name'=>'remember'),$_smarty_tpl);
echo smarty_function_label(array('name'=>'remember'),$_smarty_tpl);?>
</p>
                <p class="mt_forgot_pass"><a href="<?php echo smarty_function_url_for_route(array('for'=>'base_forgot_password'),$_smarty_tpl);?>
"><?php echo smarty_function_text(array('key'=>'base+forgot_password_label'),$_smarty_tpl);?>
</a></p>
            </div>
            <?php $_block_repeat=false;
echo smarty_block_block_decorator(array('name'=>'box','langLabel'=>'base+base_sign_in_cap_label'), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
            <div class="mt_connect_buttons">
                <?php echo smarty_function_component(array('class'=>'BASE_CMP_SignInButtonList'),$_smarty_tpl);?>

            </div>
        </div>
        <div class="mt_sign_up">
            <p><?php echo smarty_function_text(array('key'=>'base+base_sign_in_txt'),$_smarty_tpl);?>
</p>
            <hr>
            <p> <a href="<?php echo $_smarty_tpl->tpl_vars['joinUrl']->value;?>
"><?php echo smarty_function_text(array('key'=>'base+join_submit_button_join'),$_smarty_tpl);?>
</a></p>
        </div>
    </div>
    <?php $_block_repeat=false;
echo smarty_block_form(array('name'=>'sign-in'), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</div><?php }
}
