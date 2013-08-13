window.MovieUser = Backbone.Model.extend({
    url:'/user/',
    idAttribute: "user_id"
});
window.LoginView = Backbone.View.extend({
    tagName:"div",
    template:_.template($('#tpl-login').html()),
    events: {
        'click #cancelButton': 'hide_login_popup',
        'click #loginButton': 'login'
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
    logout:function() {
        this.authenticate('logout');
    },
    login:function() {
        this.authenticate('login');
    },
    authenticate:function(action) {
        var hide_login_popup = false;
        this.model.save({},
            {url:'/user/'+action+'/',
                success: function(model, response) {
                    this.hide_login_popup = true;
                    if(action == 'login') {
                        $('#authenticated_name').html(model.attributes.name);
                        $('#authenticated').css('display', 'block');
                        $('#login_link').css('display', 'none');
                    } else if(action == 'logout') {
                        $('#authenticated').css('display', 'none');
                        $('#login_link').css('display', 'block');
                    }
                    model.set();
                },
                error: function(model, response) {
                    console.log("error");
                    //error message
                }
            });

        if(this.hide_login_popup) {
            this.hide_login_popup();
        }
    }
});