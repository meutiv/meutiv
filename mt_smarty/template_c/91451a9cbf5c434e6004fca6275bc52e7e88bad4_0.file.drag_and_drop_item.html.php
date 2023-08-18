<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/drag_and_drop_item.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bd133b5_91115381',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '91451a9cbf5c434e6004fca6275bc52e7e88bad4' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/drag_and_drop_item.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bd133b5_91115381 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.block_decorator.php','function'=>'smarty_block_block_decorator',),));
?>
<div class="mt_dnd_widget <?php echo $_smarty_tpl->tpl_vars['box']->value['uniqName'];?>
">

    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('block_decorator', array('name'=>'box','capEnabled'=>$_smarty_tpl->tpl_vars['box']->value['shmt_title'],'capContent'=>$_smarty_tpl->tpl_vars['box']->value['capContent'],'capAddClass'=>"mt_dnd_configurable_component clearfix",'label'=>$_smarty_tpl->tpl_vars['box']->value['title'],'iconClass'=>$_smarty_tpl->tpl_vars['box']->value['icon'],'type'=>$_smarty_tpl->tpl_vars['box']->value['type'],'addClass'=>"mt_stdmargin clearfix ".((string)$_smarty_tpl->tpl_vars['box']->value['uniqName']),'toolbar'=>$_smarty_tpl->tpl_vars['box']->value['toolbar']));
$_block_repeat=true;
echo smarty_block_block_decorator(array('name'=>'box','capEnabled'=>$_smarty_tpl->tpl_vars['box']->value['shmt_title'],'capContent'=>$_smarty_tpl->tpl_vars['box']->value['capContent'],'capAddClass'=>"mt_dnd_configurable_component clearfix",'label'=>$_smarty_tpl->tpl_vars['box']->value['title'],'iconClass'=>$_smarty_tpl->tpl_vars['box']->value['icon'],'type'=>$_smarty_tpl->tpl_vars['box']->value['type'],'addClass'=>"mt_stdmargin clearfix ".((string)$_smarty_tpl->tpl_vars['box']->value['uniqName']),'toolbar'=>$_smarty_tpl->tpl_vars['box']->value['toolbar']), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
    <?php echo $_smarty_tpl->tpl_vars['content']->value;?>
 <?php $_block_repeat=false;
echo smarty_block_block_decorator(array('name'=>'box','capEnabled'=>$_smarty_tpl->tpl_vars['box']->value['shmt_title'],'capContent'=>$_smarty_tpl->tpl_vars['box']->value['capContent'],'capAddClass'=>"mt_dnd_configurable_component clearfix",'label'=>$_smarty_tpl->tpl_vars['box']->value['title'],'iconClass'=>$_smarty_tpl->tpl_vars['box']->value['icon'],'type'=>$_smarty_tpl->tpl_vars['box']->value['type'],'addClass'=>"mt_stdmargin clearfix ".((string)$_smarty_tpl->tpl_vars['box']->value['uniqName']),'toolbar'=>$_smarty_tpl->tpl_vars['box']->value['toolbar']), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>

</div><?php }
}
