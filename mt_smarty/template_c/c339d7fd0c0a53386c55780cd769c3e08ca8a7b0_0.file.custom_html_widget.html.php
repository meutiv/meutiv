<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/custom_html_widget.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bd0c175_15469649',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c339d7fd0c0a53386c55780cd769c3e08ca8a7b0' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/custom_html_widget.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bd0c175_15469649 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.text.php','function'=>'smarty_function_text',),));
?>
<div class="mt_custom_html_widget">
    <?php if ($_smarty_tpl->tpl_vars['content']->value) {?> <?php echo $_smarty_tpl->tpl_vars['content']->value;?>
 <?php } else { ?>
    <div class="mt_nocontent">
        <?php echo smarty_function_text(array('key'=>"base+custom_html_widget_no_content"),$_smarty_tpl);?>

    </div>
    <?php }?>
</div><?php }
}
