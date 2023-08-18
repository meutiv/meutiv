<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_packages/themes/simplicity/master_pages/general.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bdca023_90886932',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '05d05cbf86d44461d25f44f1f2d2d33f815cca27' => 
    array (
      0 => '/var/www/html/meutiv/mt_packages/themes/simplicity/master_pages/general.html',
      1 => 1684957311,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bdca023_90886932 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.component.php','function'=>'smarty_function_component',),1=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.add_content.php','function'=>'smarty_function_add_content',),2=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.text.php','function'=>'smarty_function_text',),3=>array('file'=>'/var/www/html/meutiv/mt_smarty/plugin/function.decorator.php','function'=>'smarty_function_decorator',),));
?>
<div class="mt_page_wrap">
    <div class="mt_menu_fullpage">
        <div class="mt_menu_fullpage_wrap"><?php echo $_smarty_tpl->tpl_vars['main_menu']->value;?>
</div>
    </div>
    <div class="mt_site_panel clearfix">
        <a class="mt_logo mt_left" href="<?php echo $_smarty_tpl->tpl_vars['siteUrl']->value;?>
"></a>
        <div class="mt_nav_btn"></div>
        <div class="mt_console_right">
            <?php echo smarty_function_component(array('class'=>'BASE_CMP_Console'),$_smarty_tpl);?>

        </div>
        <div class="mt_menu_wrap"><?php echo smarty_function_component(array('class'=>'BASE_CMP_MainMenu','responsive'=>true),$_smarty_tpl);?>
</div>
    </div>
    <div class="mt_header">
        <div class="mt_header_pic"></div>
    </div>
        <div class="mt_page_padding">
        <div class="mt_page_container">
            <div class="mt_canvas">
                <div class="mt_page mt_bg_color clearfix">
                    <h1 class="mt_stdmargin <?php echo $_smarty_tpl->tpl_vars['heading_icon_class']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['heading']->value;?>
</h1>
                    <div class="mt_content">
                        <?php echo smarty_function_add_content(array('key'=>'base.add_page_top_content'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['content']->value;?>
 <?php echo smarty_function_add_content(array('key'=>'base.add_page_bottom_content'),$_smarty_tpl);?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt_footer">
    <div class="mt_canvas">
        <div class="mt_page clearfix">
            <?php echo $_smarty_tpl->tpl_vars['bottom_menu']->value;?>

            <div class="mt_copyright">
                <?php echo smarty_function_text(array('key'=>'base+copyright'),$_smarty_tpl);?>

            </div>
            <div style="float:right;padding-bottom: 30px;">
                <?php echo $_smarty_tpl->tpl_vars['bottomPoweredByLink']->value;?>

            </div>
        </div>
    </div>
</div>
<?php echo smarty_function_decorator(array('name'=>'floatbox'),$_smarty_tpl);?>

<?php echo '<script'; ?>
 type="text/javascript">
    $(window).scroll(function() {
        var $menuwrappos = $('.mt_menu_wrap').offset().top;
        if ($(this).scrollTop() > $menuwrappos) {
            $('.mt_page_wrap').addClass('mt_hidden_menu');
        } else {
            $('.mt_page_wrap').removeClass('mt_hidden_menu');
        }
    });
    $('.mt_nav_btn').click(function() {
        if ($('body').hasClass('mt_menu_active')) {
            $('body').removeClass('mt_menu_active');
        } else {
            $('body').addClass('mt_menu_active');
        }
    })
    $('.mt_menu_fullpage_wrap a').click(function() {
        $('body').removeClass('mt_menu_active');
    })
<?php echo '</script'; ?>
><?php }
}
