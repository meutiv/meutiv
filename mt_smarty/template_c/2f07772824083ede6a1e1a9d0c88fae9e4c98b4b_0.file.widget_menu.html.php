<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/widget_menu.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bda9146_70694204',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2f07772824083ede6a1e1a9d0c88fae9e4c98b4b' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/widget_menu.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bda9146_70694204 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="clearfix">
    <div class="mt_box_menu">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'tab');
$_smarty_tpl->tpl_vars['tab']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tab']->value) {
$_smarty_tpl->tpl_vars['tab']->do_else = false;
?>
        <a href="javascript://" id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" <?php if ((isset($_smarty_tpl->tpl_vars['tab']->value['active'])) && $_smarty_tpl->tpl_vars['tab']->value['active']) {?> class="active" <?php }?>><span><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</span></a> <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
</div><?php }
}
