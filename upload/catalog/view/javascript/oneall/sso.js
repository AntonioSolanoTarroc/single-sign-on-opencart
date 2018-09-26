(function () {
    var scripts, url, regex, results, session_token;

    /* This is the url of the current script */
    scripts = document.getElementsByTagName('script');
    url = scripts[scripts.length - 1].src;
  
    /* Extract the session token */
    regex = new RegExp("[\\?&]sso_session_token=([^&#]*)");
    results = regex.exec(url);

    /* Session token */
    session_token = (results == null ? '' : results[1]);

    /* Initiates the OneAll asynchronous queue */
    var _oneall = window._oneall || [];

    /* This part is for users that are logged in */
    if (session_token.length > 0)
    {
        /* Attaches the SSO session token to the user */
        _oneall.push(['single_sign_on', 'do_register_sso_session', session_token]);
    }
    else
    {
        /* Sets the SSO callback uri */
        _oneall.push(['single_sign_on', 'set_callback_uri', window.location.href]);

        /* Redirects the user to the callback_uri if he is logged in on another website */
        _oneall.push(['single_sign_on', 'do_check_for_sso_session']);
    }    
} ());




