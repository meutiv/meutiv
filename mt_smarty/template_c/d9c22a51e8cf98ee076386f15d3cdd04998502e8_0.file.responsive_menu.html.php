<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/responsive_menu.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402be39b35_91986700',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9c22a51e8cf98ee076386f15d3cdd04998502e8' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/responsive_menu.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402be39b35_91986700 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="mt_responsive_menu mt_left" id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">
    <ul class="mt_main_menu clearfix" data-el="list"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?><li class="<?php echo $_smarty_tpl->tpl_vars['item']->value['class'];
if (!empty($_smarty_tpl->tpl_vars['item']->value['active'])) {?> active<?php }?>" data-el="item"><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['new_window']) {?> target="_blank" <?php }?>><span><?php echo $_smarty_tpl->tpl_vars['item']->value['label'];?>
</span></a></li><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></ul>
    <div class="mt_menu_more_wrap mt_cursor_pointer">
        <div class="mt_menu_more">
            <div class="mt_menu_more_cont">
                <ul class="mt_menu_more_list" data-el="more-list">

                </ul>
            </div>
        </div>
    </div>
</div><?php }
}
