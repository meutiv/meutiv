<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/decorators/box.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bd1bdc3_86303871',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e2427c74d1459013a9775049ec60d37dbe155ffe' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/decorators/box.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bd1bdc3_86303871 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.decorator.php','function'=>'smarty_function_decorator',),));
if ($_smarty_tpl->tpl_vars['data']->value['capEnabled']) {?>
<div class="mt_box_cap<?php echo $_smarty_tpl->tpl_vars['data']->value['capAddClass'];?>
">
    <div class="mt_box_cap_right">
        <div class="mt_box_cap_body">
            <h3 class="<?php echo $_smarty_tpl->tpl_vars['data']->value['iconClass'];?>
"><?php echo $_smarty_tpl->tpl_vars['data']->value['label'];?>
</h3><?php echo $_smarty_tpl->tpl_vars['data']->value['capContent'];?>

        </div>
    </div>
</div>
<?php }?>
<div class="mt_box<?php echo $_smarty_tpl->tpl_vars['data']->value['addClass'];?>
 mt_break_word" <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['style'])) {?> style="<?php echo $_smarty_tpl->tpl_vars['data']->value['style'];?>
" <?php }?>>
    <?php echo $_smarty_tpl->tpl_vars['data']->value['content'];?>
 <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['toolbar'])) {?>
    <div class="mt_box_toolbar_cont clearfix">
        <?php echo smarty_function_decorator(array('name'=>'box_toolbar','itemList'=>$_smarty_tpl->tpl_vars['data']->value['toolbar']),$_smarty_tpl);?>

    </div>
    <?php }?> <?php if (empty($_smarty_tpl->tpl_vars['data']->value['type'])) {?>
    <div class="mt_box_bottom_left"></div>
    <div class="mt_box_bottom_right"></div>
    <div class="mt_box_bottom_body"></div>
    <div class="mt_box_bottom_shadow"></div>
    <?php }?>
</div><?php }
}
