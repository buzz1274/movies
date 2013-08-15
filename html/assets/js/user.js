window.MovieUser = Backbone.Model.extend({
    url:'/user/',
    idAttribute: "user_id"
});
window.LoginView = Backbone.View.extend({
    tagName:"div",
    template:_.template($('#tpl-login').html()),
    events: {
        'click #cancelButton': 'hide_login_popup',
        'click #loginButton': 'login',
        'keypress #username': 'login_on_enter',
        'keypress #password': 'login_on_enter'
    },
    render:function() {
        this.hide_login_popup();
        $(this.el).html(this.template());
        $('#login_error_message').css('display', 'none');
        $('#login_popup').css('display', 'block');
        interface_helper.opaque(true);
        return this;
    },
    hide_login_popup:function() {
        $('#login_popup').html('');
        interface_helper.opaque(false);
        $('#login_popup').css('display', 'none');
    },
    login_on_enter:function(e) {
        if(e.keyCode == 13) {
            this.login();
        }
    },
    logout:function() {
        this.authenticate('logout');
    },
    login:function() {
        this.authenticate('login');
    },
    authenticate:function(action) {
        var parent = this;
        var data = null;
        if(action == 'login') {
            data = {username: $('#username').val(),
                    password: $('#password').val()};
        }
        this.model.save(data,
            {url:'/user/'+action+'/',
             success: function(model, response) {
                parent.hide_login_popup();
                if(action == 'login') {
                    $('#authenticated_name').html(model.get('name'));
                    $('#authenticated').css('display', 'block');
                    $('#login_link').css('display', 'none');
                } else if(action == 'logout') {
                    $('#authenticated').css('display', 'none');
                    $('#login_link').css('display', 'block');
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
                    parent.hide_login_popup();
                    interface_helper.message_popup();
                }
            }
        });
    }
});