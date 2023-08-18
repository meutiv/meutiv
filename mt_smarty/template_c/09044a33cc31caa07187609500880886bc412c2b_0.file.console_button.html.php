<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/console_button.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402be23313_28235518',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '09044a33cc31caa07187609500880886bc412c2b' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/console_button.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402be23313_28235518 (Smarty_Internal_Template $_smarty_tpl) {
?><a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" class="mt_console_item_link" onclick="<?php echo $_smarty_tpl->tpl_vars['onClick']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</a><?php }
}
