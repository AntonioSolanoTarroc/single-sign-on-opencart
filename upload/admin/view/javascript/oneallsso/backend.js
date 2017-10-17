jQuery(document).ready(function($) {

    /* Autodetect API Connection Handler */
    $('#oasso_connection_autodetect').click(function() {
        var url = new URL(window.location + "");
        var token = url.searchParams.get("token");
        var data = {
            'route' : 'extension/module/oneallsso',
            'do' : 'autodetect_api_connection',
            'token' : token
        };

        jQuery.get('index.php', data, function(responseJson) {

            var response = JSON.parse(responseJson);

            $('#oasso_handler option[selected]').attr('selected', false);
            $('#oasso_handler option[value="' + response.handler + '"]').attr('selected', true);

            $('#oasso_port option[selected]').attr('selected', false);
            $('#oasso_port option[value="' + response.port + '"]').attr('selected', true);

            var message_container = jQuery('#oasso_connection_autodetect_result');
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
    $('#oasso_connection_verify').click(function() {
        var oasso_handler = jQuery("#oasso_handler").val();
        var oasso_port = jQuery("#oasso_port").val();
        var oasso_api_subdomain = jQuery("#oasso_api_subdomain").val();
        var oasso_public_key = jQuery("#oasso_public_key").val();
        var oasso_private_key = jQuery("#oasso_private_key").val();
        var url = new URL(window.location + "");

        var token = url.searchParams.get("token");
        var data = {
            'route' : 'extension/module/oneallsso',
            'token' : token,
            'do' : 'verify_api_settings',
            'oneall_api_handler' : oasso_handler,
            'oneall_api_port' : oasso_port,
            'oneall_subdomain' : oasso_api_subdomain,
            'oneall_public' : oasso_public_key,
            'oneall_private' : oasso_private_key
        };

        jQuery.get('index.php', data, function(responseJson) {
            var response = JSON.parse(responseJson);
            var message_container = jQuery('#oasso_connection_verify_result');

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
