<!DOCTYPE HTML>
<html lang="en">

    <head>
        <meta charset=utf-8>
        <title>{$layout.configs.app_name|default:'devi&#241;a App'}</title>
        {if isset($appRefresh)}<meta http-equiv="refresh" content="{$appRefresh.time}">{/if}
        <meta name="viewport"    content="width=device-width, initial-scale=1">
        <meta name="description" content="Dev Application">
        <meta name="publisher"   content="Dev Companyf">
        <meta name="author"      content="Rolando Gonzalez">
        <meta name="keywords"    content="Dev app">

        <link href="{$layout.pathCSS}style.css" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
        <script src="{$layout.urlPath}Public/Js/Modernizr.js" type="text/javascript"></script>
    </head>

    <body class="{if $smarty.session.auth}devapp-{strtoupper($smarty.const.APP_INSTANCE)}-body-auth{else}devapp-{strtoupper($smarty.const.APP_INSTANCE)}-body-no-auth{/if}">
        {if $smarty.session.auth}
            <nav id="menu" class="devapp-menu" role="navigation">
                <ul class="metismenu">
                    {foreach item = tm from = $layout.mainMenu}					
                        <li>
                            <a href="#"><span class="fas fa-{$tm.icon} fa-lg"></span>{$tm.title}<b class="glyphicon arrow"></b></a>

                            <ul class="devapp-submenu">
                                {foreach item = sm from = $tm.subMenu}
                                    <li><a href="{$sm.link}" >{$sm.title}</a></li>
                                {/foreach}
                            </ul>
                        </li>
                    {/foreach}
                </ul>
            </nav>
        {/if}
        
        <div class="container-fluid">
            <noscript>For a correct application functionality, you must enable JavaScript on your Browser.</noscript>
            
            {if $smarty.session.auth}
                <div class="row devapp-bck-white wrap push" id="devapp-title-panel">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-2 col-sm-1 col-md-1 devapp-center"><a href="#menu" class="fas fa-bars fa-2x menu-link"></a></div>

                            {if isset($devAppFileMenu)}
                                <div class="col-xs-1">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" id="devapp-file-menu-btn">Menu {if isset($devAppFileMenuBdg)}<span class="badge">{$devAppFileMenuBdg}</span>{/if} <span class="fas fa-caret-down"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        {foreach item = c from = $devAppFileMenu}
                                            <li>
                                                <a href="{if isset($c.link)}{$layout.urlPath}{$c.link}/{if isset($c.meth)}{$c.meth}{/if}{if isset($c.args) && isset($c.meth)}{foreach item = arg from = $c.args}/{$arg}{/foreach}{/if}{elseif isset($c.modalLink)}#{$c.modalLink}{else}#{/if}" {if isset($c.cssClass)}class="{$c.cssClass}"{/if} {if isset($c.attr)}{$c.attr}{/if} {if isset($c.modalLink)}data-toggle="modal"{/if} title="{if isset($c.linkTitle)}{$c.linkTitle}{else}{if isset($c.menuTitle)}{$c.menuTitle}{/if}{/if}"> 
                                                    {if isset($c.imgName)}<img src="{$layout.pathIMG}{$c.imgName}.png" class="devapp-export-img" alt="{if isset($c.linkTitle)}{$c.linkTitle}{/if}" />{elseif isset($c.icon)}<span class="fas fa-{$c.icon}"></span>{/if} {if isset($c.menuTitle)}{$c.menuTitle}{/if}
                                                </a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                                
                                <div class="col-sm-5 col-md-6 devapp-page-title hidden-xs">
                                    <h3><span class="devapp-red">{strtoupper($smarty.const.APP_INSTANCE)}</span> {$title|default:'devi&#241;a Production App'}</h3>
                                </div>
                            {else}
                                <div class="col-sm-6 col-md-7 devapp-page-title hidden-xs">
                                    <h3><span class="devapp-red">{strtoupper($smarty.const.APP_INSTANCE)}</span> {$title|default:'devi&#241;a Production App'}</h3>
                                </div>
                            {/if}
                            
                            <div class="col-xs-4 col-sm-2 col-md-2 text-right devapp-navbar-color"><span><h4><small><i class="hidden-sm hidden-xs devapp-font-16 fas fa-user-circle"></i> {$smarty.session.userN}</small></h4></span></div>
                            
                            <div class="col-xs-2 col-sm-1 col-md-1 devapp-navbar-color text-right devapp-border-right" id="devapp-app-ntf-div">
                                {if isset($_appNotification)}
                                    <a href="#devapp-app-msg-modal" data-toggle="modal">
                                        <span class="fas fa-envelope fa-lg devapp-red">
                                            <span class="badge devapp-bck-red devapp-ntf-bdg">{count($_appNotification)}</span>
                                        </span>
                                    </a>
                                {else}
                                    <span class="fas fa-envelope fa-lg"></span>
                                {/if}
                            </div>
                            
                            <div class="col-xs-4 col-sm-2 col-md-1 devapp-navbar-color">
                                <span><h4><small><a href="{$layout.urlPath}logged/logout" class="fas fa-sign-out-alt devapp-navbar-color"><span class="devapp-logout"> Log out</span></a></small></h4></span>
                            </div>    
                        </div>
                    </div>    
                </div>
            {/if}

            <div class="row wrap push" id="devapp-content-panel">
                <div class="col-xs-12">
                    {if isset($_appErrorMsg)}
                        <div class="row">
                            <div class="col-md-{$devAppColMd} col-md-offset-{$devAppColMdOff}">
                                <div class="alert alert-danger text-center">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>{$_appErrorMsg}
                                </div>
                            </div>
                        </div>
                    {/if}  

                    {if isset($_appWrnMsg)}
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="alert alert-warning text-center">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>{$_appWrnMsg}
                                </div>
                            </div>
                        </div>        
                    {/if}
                
                    {if isset($_appInfoMsg)}
                        <div class="row devapp-margin-top-10">
                            <div class="col-md-6 col-lg-4 col-md-offset-3 col-lg-offset-4">
                                <div class="alert alert-info text-center">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>{$_appInfoMsg}
                                </div>
                            </div>
                        </div>
                    {/if}

                    <div class="devapp-margin-top-10">{include file = $content}</div>

                </div>
            </div>
        </div>
              
        {if isset($_appNotification)}            
            <div id="devapp-app-msg-modal" class="modal fade">
                <div class="modal-dialog devapp-modal-dialog-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Notification Message</h4>
                        </div>

                        <div class="modal-body">
                            <table class="table table-hover" id="devapp-app-ntf-table">
                                <thead>
                                    <tr>
                                        <th>Message</th>
                                        <th>Take Action</th>
                                        <th>Message Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach item = a from = $_appNotification}
                                        <tr>
                                            <td>{$a.MSG}</td>
                                            <td><a href="{$layout.urlPath}{$a.MSG_LINK}" class="btn btn-xs btn-warning">Check notification</a></td>
                                            <td>{$a.FDATE}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>      
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal"><span class="far fa-check-square"></span> Ok</button>
                        </div>
                    </div>             
                </div> 
            </div>
        {/if}

        <script src="{$layout.urlPath}Public/Js/Jquery.js"            type="text/javascript"></script>
        <script src="{$layout.urlPath}Public/Js/Validate.js"          type="text/javascript"></script>
        <script src="{$layout.urlPath}Public/Js/MetisMenu.js"         type="text/javascript"></script>
        <script src="{$layout.urlPath}Public/Js/Bootstrap.js"         type="text/javascript"></script>
        
        <script type="text/javascript">var _url_ = '{$layout.urlPath}';</script>
        
        {if isset($jsValue)}<script type="text/javascript">var _var_ = '{json_encode($jsValue, JSON_HEX_APOS)}'; </script>{/if}

        {if isset($layout.jsPlugin) && count($layout.jsPlugin)}
            {foreach item=jsPlugin from=$layout.jsPlugin}
                <script src="{$jsPlugin}" type="text/javascript"></script>
            {/foreach}
        {/if}
        
        {if isset($layout.js) && count($layout.js)}
            {foreach item=js from=$layout.js}
                <script src="{$js}" type="text/javascript"></script>
            {/foreach}
        {/if}

    </body>
</html>