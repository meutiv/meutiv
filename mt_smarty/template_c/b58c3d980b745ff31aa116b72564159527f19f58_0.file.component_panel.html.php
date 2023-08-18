<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/controllers/component_panel.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bdb37e8_88903626',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b58c3d980b745ff31aa116b72564159527f19f58' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/controllers/component_panel.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bdb37e8_88903626 (Smarty_Internal_Template $_smarty_tpl) {
if (!empty($_smarty_tpl->tpl_vars['permissionMessage']->value)) {?>
<div class="mt_anno mt_center">
    <?php echo $_smarty_tpl->tpl_vars['permissionMessage']->value;?>

</div>
<?php } else { ?> <?php if ((isset($_smarty_tpl->tpl_vars['profileActionToolbar']->value))) {?> <?php echo $_smarty_tpl->tpl_vars['profileActionToolbar']->value;?>
 <?php }?> <?php echo $_smarty_tpl->tpl_vars['componentPanel']->value;?>
 <?php }
}
}
