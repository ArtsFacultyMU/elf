M.mod_newassignment = {};

M.mod_newassignment.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree = new Y.YUI2.widget.TreeView(htmlid);

        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }
        tree.render();
    });
};


M.mod_newassignment.init_grading_table = function(Y, error_text) {
    Y.use('node', function(Y) {
        checkboxes = Y.all('td.c0 input');
        checkboxes.each(function(node) {
            node.on('change', function(e) {
                rowelement = e.currentTarget.get('parentNode').get('parentNode');
                if (e.currentTarget.get('checked')) {
                    rowelement.setAttribute('class', 'selectedrow');
                } else {
                    rowelement.setAttribute('class', 'unselectedrow');
                }
            });

            rowelement = node.get('parentNode').get('parentNode');
            if (node.get('checked')) {
                rowelement.setAttribute('class', 'selectedrow');
            } else {
                rowelement.setAttribute('class', 'unselectedrow');
            }
        });

        var selectall = Y.one('th.c0 input');
        if (selectall) {
            selectall.on('change', function(e) {
                if (e.currentTarget.get('checked')) {
                    checkboxes = Y.all('td.c0 input');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', true);
                        rowelement.setAttribute('class', 'selectedrow');
                    });
                } else {
                    checkboxes = Y.all('td.c0 input');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', false);
                        rowelement.setAttribute('class', 'unselectedrow');
                    });
                }
            });
        }

        Y.use('node-menunav', function(Y) {
            var menus = Y.all('.gradingtable .actionmenu');

            menus.each(function(menu) {
                Y.on("contentready", function() {
                    this.plug(Y.Plugin.NodeMenuNav, {autoSubmenuDisplay: true});
                    var submenus = this.all('.yui3-loading');
                    submenus.each(function (n) {
                        n.removeClass('yui3-loading');
                    });

                }, "#" + menu.getAttribute('id'));


            });


        });
        
        if(Y.one('#id_savequickgrades')) {
            Y.one('#id_savequickgrades').on('click', function(e) {
                Y.all('.gradingtable tr').each(function(row) {
                   row.removeClass('quickgradeerror'); 
                });
                var showError = false;
                var rows = Y.all('.gradingtable tr textarea.quickgrade');
                rows.each(function(row) {
                    var tr = row.get('parentNode').get('parentNode');
                    if(!(/^\s*$/).test(row.get("value"))) {
                        if(tr.one('td.c2 select').get("value") == 'none') {
                            showError = true;
                            tr.addClass('quickgradeerror');
                        }
                    }
                    if(tr.one('td.c3 input.quickgrade')!=null) {
                        if(!(/^\s*$/).test(tr.one('td.c3 input.quickgrade').get("value"))) {
                            if(tr.one('td.c2 select').get("value") == 'none') {
                                showError = true;
                                tr.addClass('quickgradeerror');
                            }
                        }
                    }
                     if(tr.one('td.c3 select.quickgrade')!=null) {
                        if(tr.one('td.c3 select.quickgrade').get("value") != -1) {
                            if(tr.one('td.c2 select').get("value") == 'none') {
                                showError = true;
                                tr.addClass('quickgradeerror');
                            }
                        }
                    }
                });
                if(showError) {
                    alert(error_text);
                    e.preventDefault();
                }
            });
        }
        
        var quickgrade = Y.all('.gradingtable .quickgrade');
        quickgrade.each(function(quick) {
            quick.on('change', function(e) {
                this.get('parentNode').addClass('quickgrademodified');
            });
        });
    });
};

M.mod_newassignment.init_submission = function(Y, confirm_text, ok_text, cancel_text) {
    Y.use('node','panel','node-event-simulate', function(Y) {
       var cont = false;
       var panel = new Y.Panel({
            contentBox : Y.Node.create('<div id="dialog" />'),
            bodyContent: '<div class="message">'+confirm_text+'</div>',
            width      : 600,
            zIndex     : 6,
            centered   : true,
            modal      : true, // modal behavior
            render     : '.example',
            visible    : false, // make visible explicitly with .show()
            buttons    : {
                footer: [
                    {
                        name     : 'proceed',
                        label    : ok_text,
                        action   : function(e) {
                            e.preventDefault();
                            cont = true;
                            Y.one('#id_submitbutton').simulate('click');
                        }
                    },
                    {
                        name  : 'cancel',
                        label : cancel_text,
                        action: function(e) {
                            e.preventDefault();
                            this.hide();
                        }
                    }
                ]
            }
        });
        
        
        Y.one('#id_submitbutton').on('click',function(e) {
            if(cont)
                return;
            else {
                e.preventDefault();
                panel.show();
            }
       });
    });
}