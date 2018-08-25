$(document).ready(function () 
{
    $('#devapp-user-input').focus();
    
    $('#devapp-login-form').validate({
        rules: {
            loginUser: { required: true },
            loginPass: { required: true }
        },
        errorElement: 'div'
    });
});