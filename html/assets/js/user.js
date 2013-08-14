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
        $(this.el).html(this.template());
        $('#login_popup').css('display', 'block');
        $('#opaque').css('display', 'block');
        return this;
    },
    hide_login_popup:function() {
        $('#login_popup').html('');
        $('#opaque').css('display', 'none');
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
                if(action == 'login') {
                    $('#authenticated_name').html(model.get('name'));
                    $('#authenticated').css('display', 'block');
                    $('#login_link').css('display', 'none');
                } else if(action == 'logout') {
                    //display logged out message
                    $('#authenticated').css('display', 'none');
                    $('#login_link').css('display', 'block');
                }
                model.set({username: null, password: null,
                           name: response.name,
                           authenticated: response.authenticated});
                parent.hide_login_popup();
            },
            error: function(model, response) {
                console.log("error");
            }
        });
    }
});