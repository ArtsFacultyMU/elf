require(['core/yui'], function (Y) {
    Y.use('node', 'transition', 'anim', 'moodle-core-dialogue', function () {
        Y.on('domready', function () {
            var topMargin = 0;
            if (Y.one('body').getData("header") == "full") {
                refreshFullDockPos(Y);
                window.onscroll = function () {
                    refreshFullDockPos(Y);
                }
            }

            if (Y.one('body').getData("header") == "compact") {
                refreshCompactDockPos(Y);
                window.onscroll = function () {
                    refreshCompactDockPos(Y);
                }
            }

            if (Y.one('.login-menu')) {
                Y.one('#login-menu-shibboleth').on('click', function (e) {
                    e.preventDefault();
                    Y.one('#login-menu-manual').removeClass('login-menu-selected');
                    Y.one('#login-menu-shibboleth').addClass('login-menu-selected');
                    Y.one('#login-content-manual').hide();
                    Y.one('#login-content-shibboleth').show();
                });

                Y.one('#login-menu-manual').on('click', function (e) {
                    e.preventDefault();
                    Y.one('#login-menu-shibboleth').removeClass('login-menu-selected');
                    Y.one('#login-menu-manual').addClass('login-menu-selected');
                    Y.one('#login-content-shibboleth').hide();
                    Y.one('#login-content-manual').show();
                });
            }

            if (Y.one('#allow-cookies a.button')) {
                Y.one('#allow-cookies a.button').on('click', function (e) {
                    e.preventDefault();
                    setCookie('cookiepolicy', true, 356);
                    Y.one('#allow-cookies').hide();
                    Y.one('#page-footer').removeClass('allow-cookies');
                    if (Y.one('#scroll-up')) {Y.one('#scroll-up').removeClass('allow-cookies');}
                });
            }

            if (Y.one('.hide-blocks-btn')) {
                if (sessionStorage.getItem('hideblocks') == 'true') {
                    Y.one('body').addClass('hiddenblocks');
                    Y.all('.hide-blocks-btn img').setAttribute('src', Y.one('.hide-blocks-btn img').getData('hidden'));
                } else {
                    Y.one('body').removeClass('hiddenblocks');
                    Y.all('.hide-blocks-btn img').setAttribute('src', Y.one('.hide-blocks-btn img').getData('show'));
                }
                Y.all('.hide-blocks-btn').on('click', function (e) {
                    e.preventDefault();
                    if (sessionStorage.getItem('hideblocks') == 'true') {
                        Y.one('body').removeClass('hiddenblocks');
                        Y.all('.hide-blocks-btn img').setAttribute('src', Y.one('.hide-blocks-btn img').getData('show'));
                        sessionStorage.setItem('hideblocks', 'false')
                    } else {
                        Y.one('body').addClass('hiddenblocks');
                        Y.all('.hide-blocks-btn img').setAttribute('src', Y.one('.hide-blocks-btn img').getData('hidden'));
                        sessionStorage.setItem('hideblocks', 'true')
                    }
                });
            }

            if (Y.one('#upper-banner')) {
                if (Y.one('#banner-menu-news')) {
                    Y.one('#banner-menu-news').on('click', function (e) {
                        if (!$('#banner-content').is(":visible"))
                            return true;
                        e.preventDefault();
                        Y.all('#banner-menu > a').removeClass('banner-menu-item-selected');
                        Y.one('#banner-menu-news').addClass('banner-menu-item-selected');
                        Y.all('#banner-content > div').hide();
                        Y.one('#banner-content-news').show();
                    });
                }
                if (Y.one('#banner-menu-teachers')) {
                    Y.one('#banner-menu-teachers').on('click', function (e) {
                        if (!$('#banner-content').is(":visible"))
                            return true;
                        e.preventDefault();
                        Y.all('#banner-menu > a').removeClass('banner-menu-item-selected');
                        Y.one('#banner-menu-teachers').addClass('banner-menu-item-selected');
                        Y.all('#banner-content > div').hide();
                        Y.one('#banner-content-teachers').show();
                    });
                }
                if (Y.one('#banner-menu-students')) {
                    Y.one('#banner-menu-students').on('click', function (e) {
                        if (!$('#banner-content').is(":visible"))
                            return true;
                        e.preventDefault();
                        Y.all('#banner-menu > a').removeClass('banner-menu-item-selected');
                        Y.one('#banner-menu-students').addClass('banner-menu-item-selected');
                        Y.all('#banner-content > div').hide();
                        Y.one('#banner-content-students').show();
                    });
                }
                if (Y.one('#banner-menu-sos')) {
                    Y.one('#banner-menu-sos').on('click', function (e) {
                        if (!$('#banner-content').is(":visible"))
                            return true;
                        e.preventDefault();
                        Y.all('#banner-menu > a').removeClass('banner-menu-item-selected');
                        Y.one('#banner-menu-sos').addClass('banner-menu-item-selected');
                        Y.all('#banner-content > div').hide();
                        Y.one('#banner-content-sos').show();
                    });
                }
            }

	    if (Y.one('#scroll-up')) {
                Y.one('#scroll-up').on('click', function (e) {
                    e.preventDefault();
                    a = new Y.Anim({
                        node: Y.UA.gecko ? 'html' : 'body',
                        to: {scrollTop: 0},
                        duration: 0.8,
                        easing: Y.Easing.easeBoth
                    });
                    a.run();
                });
	    }

            if (Y.one('#login-help')) {
                Y.one('#login-help').on('click', function (e) {
                    e.preventDefault();
                    var config = {
                        bodyContent: Y.one(".signuppanel"),
                        draggable: false,
                        modal: true,
                        closeButton: true,
                        width: '90%'
                    };

                    var dialogue = new M.core.dialogue(config);
                    dialogue.show();
                });
            }
        });
    });

});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return false;
}

function refreshFullDockPos(Y) {
    var topMargin = 90 - get_scroll_y();
    if (topMargin < 0)
        topMargin = 0;
    if (Y.one('#dock .dockeditem_container'))
        Y.one('#dock .dockeditem_container').setStyle('marginTop', topMargin);
    if (get_scroll_y() < 100) {
        if (Y.one('.navbar-inner')) {
            Y.one('.navbar-inner').removeClass('navbar-inner-docked');
            Y.one('#header').setStyle('height', '100px');
        }
        if (Y.one('#scroll-up')) {Y.one('#scroll-up').hide();}
    } else {
        if (Y.one('.navbar-inner')) {
            Y.one('.navbar-inner').addClass('navbar-inner-docked');
            Y.one('#header').setStyle('height', '144px');
        }
        if (Y.one('#scroll-up')) {Y.one('#scroll-up').show();}
    }
}

function refreshCompactDockPos(Y) {
    var topMargin = 51 - get_scroll_y();
    if (topMargin < 0)
        topMargin = 0;
    if (Y.one('#dock .dockeditem_container'))
        Y.one('#dock .dockeditem_container').setStyle('marginTop', topMargin);
    if (get_scroll_y() < 101) {
        if (Y.one('#scroll-up')) {Y.one('#scroll-up').hide();}
    } else {
        if (Y.one('#scroll-up')) {Y.one('#scroll-up').show();}
    }
}

function get_scroll_y() {
    return window.scrollY || window.pageYOffset || document.documentElement.scrollTop;
}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function emptyStr(str) {
    return (!str || /^\s*$/.test(str));
}
