<?php
/* Smarty version 4.3.2, created on 2023-08-09 00:28:43
  from '/var/www/html/meutiv/mt_system_plugins/base/decorators/avatar_item.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_64d3402bda2e61_57104781',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'de8d6aa7382a4a2d3c409f7a00a4e91752b4e0a8' => 
    array (
      0 => '/var/www/html/meutiv/mt_system_plugins/base/decorators/avatar_item.html',
      1 => 1689595670,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_64d3402bda2e61_57104781 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="mt_avatar<?php if (!empty($_smarty_tpl->tpl_vars['data']->value['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['data']->value['class'];
}?>">
    <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['isMarked'])) {?>
    <div class="mt_ic_bookmark mt_bookmark_icon"></div><?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['url'])) {?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['data']->value['url'];?>
"><img <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['title'])) {?> alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
" <?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['attrs'])) {
echo $_smarty_tpl->tpl_vars['data']->value['attrs'];
}?> src="<?php echo $_smarty_tpl->tpl_vars['data']->value['src'];?>
" /></a>
    <?php } else { ?>
    <img <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['title'])) {?> alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
" <?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['attrs'])) {
echo $_smarty_tpl->tpl_vars['data']->value['attrs'];
}?> src="<?php echo $_smarty_tpl->tpl_vars['data']->value['src'];?>
" /> <?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['label'])) {?><span class="mt_avatar_label" <?php if (!empty($_smarty_tpl->tpl_vars['data']->value['labelColor'])) {?> style="background-color: <?php echo $_smarty_tpl->tpl_vars['data']->value['labelColor'];?>
"
        <?php }?>><?php echo $_smarty_tpl->tpl_vars['data']->value['label'];?>
</span><?php }?>
</div><?php }
}
