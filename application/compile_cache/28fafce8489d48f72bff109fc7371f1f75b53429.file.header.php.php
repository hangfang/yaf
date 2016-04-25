<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 13:00:03
         compiled from "C:\xampp\htdocs\yaf\application\views\common\weui\header.php" */ ?>
<?php /*%%SmartyHeaderCode:31213571e137a18aa56-76221766%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '28fafce8489d48f72bff109fc7371f1f75b53429' => 
    array (
      0 => 'C:\\xampp\\htdocs\\yaf\\application\\views\\common\\weui\\header.php',
      1 => 1461589200,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31213571e137a18aa56-76221766',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571e137a18aa55_48747505',
  'variables' => 
  array (
    'data' => 0,
    'environ' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571e137a18aa55_48747505')) {function content_571e137a18aa55_48747505($_smarty_tpl) {?><!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        var _speedMark = new Date();
    </script>
    <meta charset="utf-8">
    <title><?php echo $_smarty_tpl->tpl_vars['data']->value['title'];?>
</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap-theme.min.css">-->
    <script src="/static/public/js/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/static/weui/css/weui.css"/>
    <link rel="stylesheet" href="/static/weui/css/common.css"/>
    <style type="text/css">
        body,button, input, select, textarea,h1 ,h2, h3, h4, h5, h6 { font-family: Microsoft YaHei,'宋体' , Tahoma, Helvetica, Arial, "\5b8b\4f53", sans-serif;}
        p.weui_tabbar_label {padding: 0; margin:0;}
        .weui_tabbar_icon + .weui_tabbar_label {margin: 0;}
    </style>
    <?php if ($_smarty_tpl->tpl_vars['environ']->value=='production'){?>
    <script>
        window.onerror = function(){return true;};
    </script>
    <?php }?>
</head>
<body>
    <div id="container"><?php }} ?>