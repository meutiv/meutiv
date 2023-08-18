<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/console_item.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402be1f742_60943513',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '17bd5b7e8f58427cdf65712b8aead9f2dec91a9a' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/console_item.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402be1f742_60943513 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.block_decorator.php','function'=>'smarty_block_block_decorator',),));
?>
<div id="<?php echo $_smarty_tpl->tpl_vars['item']->value['uniqId'];?>
" class="mt_console_item <?php echo $_smarty_tpl->tpl_vars['item']->value['class'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['hidden']) {?>style="display: none;" <?php }?>>
    <?php echo $_smarty_tpl->tpl_vars['item']->value['html'];?>
 <?php if (!empty($_smarty_tpl->tpl_vars['item']->value['content'])) {?>
    <div id="<?php echo $_smarty_tpl->tpl_vars['item']->value['content']['uniqId'];?>
" class="MT_ConsoleItemContent" style="display: none;">

        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('block_decorator', array('name'=>"tooltip",'addClass'=>"console_tooltip mt_tooltip_top_right"));
$_block_repeat=true;
echo smarty_block_block_decorator(array('name'=>"tooltip",'addClass'=>"console_tooltip mt_tooltip_top_right"), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?> <?php echo $_smarty_tpl->tpl_vars['item']->value['content']['html'];?>
 <?php $_block_repeat=false;
echo smarty_block_block_decorator(array('name'=>"tooltip",'addClass'=>"console_tooltip mt_tooltip_top_right"), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

    </div>
    <?php }?>
</div><?php }
}
