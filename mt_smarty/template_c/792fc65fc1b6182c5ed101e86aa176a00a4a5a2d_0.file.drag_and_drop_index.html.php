<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/views/components/drag_and_drop_index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bcfc714_58095654',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '792fc65fc1b6182c5ed101e86aa176a00a4a5a2d' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/views/components/drag_and_drop_index.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bcfc714_58095654 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.script.php','function'=>'smarty_block_script',),1=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.style.php','function'=>'smarty_block_style',),2=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.add_content.php','function'=>'smarty_function_add_content',),3=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/block.block_decorator.php','function'=>'smarty_block_block_decorator',),4=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.decorator.php','function'=>'smarty_function_decorator',),));
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('script', array());
$_block_repeat=true;
echo smarty_block_script(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?> DND_InterfaceFix.fix('.place_section'); <?php $_block_repeat=false;
echo smarty_block_script(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('style', array());
$_block_repeat=true;
echo smarty_block_style(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?> #place_components .component { float: left; } .configurable_component .mt_box_icons { float: right; padding-top: 6px; } .configurable_component h3 { float:
left; } <?php $_block_repeat=false;
echo smarty_block_style(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_function_add_content(array('key'=>'base.widget_panel.content.top','placeName'=>$_smarty_tpl->tpl_vars['placeName']->value),$_smarty_tpl);?>
 <?php echo smarty_function_add_content(array('key'=>'base.`$placeName`.content.top'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['allowCustomize']->value) {?> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('block_decorator', array('name'=>'box','addClass'=>'mt_highbox mt_stdmargin index_customize_box','type'=>"empty"));
$_block_repeat=true;
echo smarty_block_block_decorator(array('name'=>'box','addClass'=>'mt_highbox mt_stdmargin index_customize_box','type'=>"empty"), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
<div class="mt_center">
    <?php echo smarty_function_decorator(array('name'=>'button','langLabel'=>'base+widgets_customize_btn','class'=>'mt_ic_gear_wheel','id'=>"goto_customize_btn"),$_smarty_tpl);?>

</div>
<?php $_block_repeat=false;
echo smarty_block_block_decorator(array('name'=>'box','addClass'=>'mt_highbox mt_stdmargin index_customize_box','type'=>"empty"), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php }?>

<div class="mt_dragndrop_sections mt_stdmargin" id="place_sections">

    <div class="clearfix">
        <div class="mt_dragndrop_content">

            <div class="place_section">

                <?php if ((isset($_smarty_tpl->tpl_vars['componentList']->value['section']['top']))) {?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['componentList']->value['section']['top'], 'component');
$_smarty_tpl->tpl_vars['component']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['component']->value) {
$_smarty_tpl->tpl_vars['component']->do_else = false;
?> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dd_component'][0], array( array('uniqName'=>$_smarty_tpl->tpl_vars['component']->value['uniqName'],'render'=>true),$_smarty_tpl ) );?>
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <?php }?>

            </div>

            <div class="clearfix" style="overflow: hidden;">

                <div class="mt_left place_section <?php if ((isset($_smarty_tpl->tpl_vars['activeScheme']->value['leftCssClass']))) {
echo $_smarty_tpl->tpl_vars['activeScheme']->value['leftCssClass'];
}?>">

                    <?php if ((isset($_smarty_tpl->tpl_vars['componentList']->value['section']['left']))) {?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['componentList']->value['section']['left'], 'component');
$_smarty_tpl->tpl_vars['component']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['component']->value) {
$_smarty_tpl->tpl_vars['component']->do_else = false;
?> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dd_component'][0], array( array('uniqName'=>$_smarty_tpl->tpl_vars['component']->value['uniqName'],'render'=>true),$_smarty_tpl ) );?>
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <?php }?>

                </div>

                <div class="mt_right place_section <?php if ((isset($_smarty_tpl->tpl_vars['activeScheme']->value['rightCssClass']))) {
echo $_smarty_tpl->tpl_vars['activeScheme']->value['rightCssClass'];
}?>" mt_scheme_class="<?php if ((isset($_smarty_tpl->tpl_vars['activeScheme']->value['rightCssClass']))) {
echo $_smarty_tpl->tpl_vars['activeScheme']->value['rightCssClass'];
}?>" mt_place_section="right">

                    <?php if ((isset($_smarty_tpl->tpl_vars['componentList']->value['section']['right']))) {?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['componentList']->value['section']['right'], 'component');
$_smarty_tpl->tpl_vars['component']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['component']->value) {
$_smarty_tpl->tpl_vars['component']->do_else = false;
?> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dd_component'][0], array( array('uniqName'=>$_smarty_tpl->tpl_vars['component']->value['uniqName'],'render'=>true),$_smarty_tpl ) );?>
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <?php }?>

                </div>

            </div>

            <div class="place_section">

                <?php if ((isset($_smarty_tpl->tpl_vars['componentList']->value['section']['bottom']))) {?> <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['componentList']->value['section']['bottom'], 'component');
$_smarty_tpl->tpl_vars['component']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['component']->value) {
$_smarty_tpl->tpl_vars['component']->do_else = false;
?> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dd_component'][0], array( array('uniqName'=>$_smarty_tpl->tpl_vars['component']->value['uniqName'],'render'=>true),$_smarty_tpl ) );?>
 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> <?php }?>

            </div>

        </div>
        <?php echo smarty_function_add_content(array('key'=>'index.add_content_bottom'),$_smarty_tpl);?>

    </div>
</div>

<?php echo smarty_function_add_content(array('key'=>'base.widget_panel.content.bottom','placeName'=>$_smarty_tpl->tpl_vars['placeName']->value),$_smarty_tpl);?>
 <?php echo smarty_function_add_content(array('key'=>'base.`$placeName`.content.bottom'),$_smarty_tpl);
}
}
