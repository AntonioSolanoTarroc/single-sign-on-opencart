jQuery(document).ready(function($) {

    /* Autodetect API Connection Handler */
    $('#oneallsso_connection_autodetect').click(function() {
        var url = new URL(window.location + "");   
        var data = {
            'route': 'extension/module/oneallsso',
            'do': 'autodetect_api_connection',
            'user_token': url.searchParams.get("user_token")
        };

        jQuery.get('index.php', data, function(responseJson) {
            var response = JSON.parse(responseJson);

            $('#oneallsso_api_handler').val(response.handler).change();
            $('#oneallsso_api_port').val (response.port).change();

            var message_container = jQuery('#oneallsso_connection_autodetect_result');
            message_container.html(response.status_message);
            message_container.addClass("alert");

            if (response.status && response.status == "success") {
                message_container.addClass("alert-success");
                message_container.removeClass("alert-danger");
            } else {
                message_container.addClass("alert-danger");
                message_container.removeClass("alert-success");
            }
        });
        return false;
    });

    /* Test API Settings */
    $('#oneallsso_connection_verify').click(function() {
        var url = new URL(window.location + "");        
        var data = {
            'route' : 'extension/module/oneallsso',
            'do' : 'verify_api_settings',
            'user_token' :  url.searchParams.get("user_token"),           
            'oneallsso_api_handler' : jQuery("#oneallsso_api_handler").val(),
            'oneallsso_api_port' : jQuery("#oneallsso_api_port").val(),
            'oneallsso_api_subdomain' : jQuery("#oneallsso_api_subdomain").val(),
            'oneallsso_api_public_key' : jQuery("#oneallsso_api_public_key").val(),
            'oneallsso_api_private_key' :  jQuery("#oneallsso_api_private_key").val()
        };

        jQuery.get('index.php', data, function(responseJson) {
            var response = JSON.parse(responseJson);
            var message_container = jQuery('#oneallsso_connection_verify_result');

            message_container.html(response.status_message);
            message_container.addClass("alert");

            console.log(response.status);
            if (response.status && response.status == "success") {
                message_container.addClass("alert-success");
                message_container.removeClass("alert-danger");
            } else {
                message_container.addClass("alert-danger");
                message_container.removeClass("alert-success");
            }

        });
        return false;
    });
});
