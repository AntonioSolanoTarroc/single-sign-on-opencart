var OneallSso = {

    /**
     * Extract URL Parameter
     *
     * @param param
     *
     * @returns {string}
     */
    'get_url_param': function get_url_param(param) {

        var scripts, url;

        scripts = document.getElementsByTagName('script');
        url = scripts[scripts.length - 1].src;

        var regex, results;
        param = param.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        regex = new RegExp("[\\?&]" + param + "=([^&#]*)");
        results = regex.exec(url);
        return (results == null ? '' : results[1]);
    }
};

var _oneall_sso_session_token = window._oneall_sso_session_token || '';

if (OneallSso.get_url_param('sso_session_token')) {
    _oneall_sso_session_token = OneallSso.get_url_param('sso_session_token');
}

/* Initiates the OneAll asynchronous queue */
var _oneall = window._oneall || [];

/* ===== This part is for users that are logged in */
if (typeof _oneall_sso_session_token === 'string' && _oneall_sso_session_token.length > 0) {
    /* Attaches the SSO session token to the user */
    _oneall.push(['single_sign_on', 'do_register_sso_session', _oneall_sso_session_token]);
}
else {
    /* Sets the SSO callback uri */
    _oneall.push(['single_sign_on', 'set_callback_uri', window.location.href]);

    /* Redirects the user to the callback_uri if he is logged in on another of your websites */
    _oneall.push(['single_sign_on', 'do_check_for_sso_session']);
}
