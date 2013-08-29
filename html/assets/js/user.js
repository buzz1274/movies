window.MovieUser = Backbone.Model.extend({
    url:'/user/',
    idAttribute: "user_id"
});
window.HeaderView = Backbone.View.extend({
    el:$("#navbar"),
    template:_.template($('#tpl-navbar').html()),
    events: {
        'click span#logout_link': 'authenticate',
        'click #loginButton': 'authenticate',
        'keypress #username': 'authenticate',
        'keypress #password': 'authenticate',
        'click span#login_popup_link': 'login_popup',
        'click #cancelButton': 'login_popup'
    },
    initialize: function() {
        this.model.bind("change:authenticated", this.render, this);
        this.render();
        return this;
    },
    render:function() {
        $(this.el).html('').append(this.template(this.model.toJSON()));
    },
    login_popup:function(e) {
        if(e.target.id == 'login_popup_link') {
            interface_helper.opaque(true);
            $('#login_popup').append(_.template($('#tpl-login').html()));
        } else {
            $('#login_popup').html('');
            interface_helper.opaque(false);
        }
    },
    authenticate:function(e) {
        if(typeof(e.keyCode) != 'undefined' && e.keyCode != 13) {
            return;
        } else if(e.target.id == 'loginButton' || e.keyCode == 13) {
            var action = 'login'
        } else {
            var action = 'logout';
        }
        var parent = this;
        var data = null;
        if(action == 'login') {
            data = {username: $('#username').val(),
                    password: $('#password').val()};
        }
        this.model.save(data,
            {url:'/user/'+action+'/',
                success: function(model, response) {
                    parent.login_popup(e);
                    if(action == 'logout') {
                        interface_helper.message_popup('success', 'You have logged out');
                    }
                    model.set({username: null, password: null,
                        name: response.name,
                        authenticated: response.authenticated});
                },
                error: function(model, response) {
                    body = JSON.parse(response.responseText);
                    if(body.error_type == 'invalid_credentials') {
                        $('#login_error_message').css('display', 'block').html(body.error_message);
                    } else {
                        parent.login_popup(e);
                        interface_helper.message_popup();
                    }
                }
            }
        );
    }
});