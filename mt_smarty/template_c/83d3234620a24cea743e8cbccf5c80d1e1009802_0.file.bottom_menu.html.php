<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/bottom_menu.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bdc42e6_63158694',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '83d3234620a24cea743e8cbccf5c80d1e1009802' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/bottom_menu.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bdc42e6_63158694 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="mt_footer_menu">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'item', false, NULL, 'bottom_menu', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['total'];
?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['active']) {?> class="active" <?php }
if ($_smarty_tpl->tpl_vars['item']->value['new_window']) {?> target="_blank" <?php }?>><?php echo $_smarty_tpl->tpl_vars['item']->value['label'];?>
</a><?php if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_bottom_menu']->value['last'] : null)) {?> | <?php }?> <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div><?php }
}
