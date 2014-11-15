$('#register-btn').prop('disabled', true);
$( "#register-data" ).on('input', function() {
    $.get( "/ajax/?action=verifyGitHubToken&registerData=" + $( this).val(), function( data ) {
        if(data == "true") {
            $('#register-btn').prop('disabled', false);
        }
        else{
            $('#register-btn').prop('disabled', true);
        }
    });
});