<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="row">
            <div class="col-md-4">
                <div class="devapp-login-tile devapp-index-lgn">
                    <form id="devapp-login-form" method="post" role="form" action="{$layout.urlPath}index/index/login">
                        <input type="hidden" name="lgRdctH" value="{if isset($smarty.session.appURLRqt)}{$smarty.session.appURLRqt}{/if}" />
                        
                        <div class="row">
                            <div class="col-md-12">
                                <span class="devapp-typo-rlw"> Enter your credentials</span>
                            </div>
                        </div>
                        
                        <hr class="devapp-margin-top-10 devapp-margin-bottom-10" />
                        
                        <div class="row devapp-margin-top-25">
                            <div class="col-md-12">
                                <div class="input-group"> 
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                    <input type="text" class="form-control" id="devapp-user-input" name="loginUser" placeholder="Username" value="{if isset($smarty.post.loginUser)}{$smarty.post.loginUser}{/if}" />
                                </div>    
                            </div>     
                        </div>

                        <div class="row devapp-margin-top-15">
                            <div class="col-md-12">
                                <div class="input-group"> 
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                                    <input type="password" class="form-control" id="devapp-pass-input" name="loginPass" placeholder="Password" />
                                </div> 
                            </div>
                        </div>

                        <div class="row devapp-margin-top-13">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-warning"><span class="fas fa-sign-in"></span> Sign In</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-8 devapp-index-bnn">
                <div class="devapp-bck-index-hdr">
                    <h2><span class="fas fa-cloud devapp-vert-center"> </span> <span class="devapp-red">{strtoupper($smarty.const.APP_INSTANCE)}</span> Dev App</h2>
                    <p class="text-justify">
                        Dev application.
                    </p>
                </div>
            </div>       
        </div>
    </div>
</div>

<div class="row devapp-index-tile">
    <div class="col-md-10 col-md-offset-1">
        <div class="row">
            <div class="col-sm-4 devapp-margin-top-10">
                <div class="devapp-bck-index-rpt-tile">
                    <h2><span class="glyphicon glyphicon-list"></span> Reports</h2>
                    <hr class="devapp-margin-top-10 devapp-margin-bottom-10" />
                    <p class="text-justify devapp-typo-rlw">
                        Sample Text.
                    </p>
                </div>
            </div>

            <div class="col-sm-4 devapp-margin-top-10">
                <div class="devapp-bck-index-dsg-tile">
                    <h2><span class="glyphicon glyphicon-picture"></span> Design</h2>
                    <hr class="devapp-margin-top-10 devapp-margin-bottom-10" />
                    <p class="text-justify devapp-typo-rlw">
                        Sample Text.
                    </p>  
                </div>
            </div>

            <div class="col-sm-4 devapp-margin-top-10">
                <div class="devapp-bck-index-exp-tile">
                    <h2><span class="glyphicon glyphicon-fullscreen"></span> Expandable</h2>
                    <hr class="devapp-margin-top-10 devapp-margin-bottom-10" />
                    <p class="text-justify devapp-typo-rlw">
                        Sample Text.
                    </p>
                </div>
            </div>
        </div>
    </div>    
</div>

<div class="row devapp-index-footer">
    <div class="col-md-10 col-md-offset-1">
        <div class="text-center devapp-bck-index-btm">
            <h3><span class="fas fa-thumbs-up devapp-vert-center"> </span> Sample Text. <span class="devapp-red">{strtoupper($smarty.const.APP_INSTANCE)}</span></h3>
        </div>
    </div>
</div>
