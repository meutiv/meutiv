<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/avatar_user_list.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bd92179_32815713',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '522e87defcfa72a7295f97c53c87305f517876b1' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/avatar_user_list.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bd92179_32815713 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.text.php','function'=>'smarty_function_text',),1=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.decorator.php','function'=>'smarty_function_decorator',),));
?>
<div class="mt_lp_avatars<?php if (!empty($_smarty_tpl->tpl_vars['css_class']->value)) {?> <?php echo $_smarty_tpl->tpl_vars['css_class']->value;
}?>">
    <?php if (empty($_smarty_tpl->tpl_vars['users']->value)) {?>
    <div class="mt_nocontent"><?php echo smarty_function_text(array('key'=>'base+empty_user_avatar_list'),$_smarty_tpl);?>
</div>
    <?php } else { ?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['users']->value, 'user');
$_smarty_tpl->tpl_vars['user']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
$_smarty_tpl->tpl_vars['user']->do_else = false;
echo smarty_function_decorator(array('name'=>'avatar_item','data'=>$_smarty_tpl->tpl_vars['user']->value),$_smarty_tpl);
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
if (!empty($_smarty_tpl->tpl_vars['view_more_array']->value)) {?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['view_more_array']->value['url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['view_more_array']->value['title'];?>
" class="avatar_list_more_icon"></a><?php }?> <?php }?>

</div><?php }
}
