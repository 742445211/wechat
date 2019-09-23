<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);
Encore\Admin\Admin::style('.content-wrapper, .right-side {background-color : #F8F8F8}');
Encore\Admin\Admin::style(
    '.content-wrapper, .right-side {background-color : #F8F8F8} 
            a.dropdown-toggle {color : #969696}
            .pagination>.active>span {background-color: #FC7501;border-color: #FC7501;}
            .pagination>.active>span:hover {background-color: #FC7501;border-color: #FC7501;}
            .btn-dropbox,.btn-instagram {color : #808080;background-color : #ECECEC}
            .btn-dropbox:hover,.btn-instagram:hover {color : #000000;background-color : rgb(238,238,238)}
            .btn-twitter {color : #808080;background-color : #ECECEC}
            .btn-twitter:hover {color : #000000;background-color : rgb(238,238,238)}
            .btn-dropbox.active {color : #ffffff;background-color : #FC7501}
            .btn-twitter:active {color : #ffffff;background-color : #FC7501}
            .open>.dropdown-toggle.btn-instagram {color : #ffffff;background-color : #FC7501}'
);
