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

var session_token = '';
/* Initiates the OneAll asynchronous queue */
var _oneall = window._oneall || [];

if (OneallSso.get_url_param('sso_session_token')) {
    session_token = OneallSso.get_url_param('sso_session_token');
}

/* ===== This part is for users that are logged in */
if (typeof session_token === 'string' && session_token.length > 0) {
    console.log('registering');
    /* Attaches the SSO session token to the user */
    _oneall.push(['single_sign_on', 'do_register_sso_session', _oneall_sso_session_token]);
}
