window.MovieUser = Backbone.Model.extend({
    url:'/user/',
    idAttribute: "user_id",
    watched:function(movie_model, user, summary, movie_summary, watched_id) {
        var movie = movie_model.get('Movie');
        var watched = movie_model.get('Watched');
        var total_watched = _.size(watched);

        var data = {'movie_id': movie.movie_id,
                    'watched_id': watched_id};

        user.save(data,
           {url:'/user/watched/',
            success: function(model, response) {
               if(!watched_id) {
                    watched[total_watched] = {'date_watched': response.date_watched,
                                              'id': response.id};
                    movie_model.set(watched);
                } else {
                    _.each(watched, function(movie_watched, key) {
                        if(movie_watched.id == watched_id) {
                            watched.splice(key, 1);
                            movie_model.save(watched);
                        }
                        if(!_.size(watched)) {
                            $('#watched_'+movie.movie_id).remove();
                        }
                    });
                }

                var mw = movie_summary.get('Movie');
                var w = summary.get('watched');
                var nw = summary.get('not_watched');

                if(!total_watched && _.size(watched)) {
                    summary.set({watched:++w, not_watched:--nw});
                    mw.watched = true;
                    movie_summary.save(mw);
                } else if(total_watched && !_.size(watched)) {
                    summary.set({watched:--w, not_watched:++nw});
                    mw.watched = false;
                    movie_summary.save(mw);
                }
                if(total_watched < _.size(watched)) {
                    var message = 'Movie added to watched';
                } else {
                    var message = 'Movie removed from watched';
                }
                interface_helper.message_popup('success', message);
             },
             error: function() {
                if(!watched_id) {
                    var message = 'Error adding movie to watched';
                } else {
                    var message = 'Error removing movie from watched';
                }
                interface_helper.message_popup('error', message);
                Movie.favourite = !Movie.favourite;
             }}
        );
    },
    favourite:function(movie_model, summary) {

    }
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
        if(this.model.get('authenticated')) {
            //this.poll();
        }
        this.model.bind("change:authenticated", this.render, this);
        this.render();
        return this;
    },
    poll: function() {
        var parent = this;
        setTimeout(function(){
            $.ajax({url: "user/", success: function(data){
                parent.poll();
            }, dataType: "json"});
        }, 30000);
    },
    render:function() {
        $(this.el).html('').append(this.template(this.model.toJSON()));
    },
    login_popup:function(e) {
        if(!e || e.target.id == 'login_popup_link') {
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
                    $('#login_popup').html('');
                    Backbone.history.loadUrl();
                },
                error: function(model, response) {
                    body = JSON.parse(response.responseText);
                    if(body.error_type == 'invalid_credentials') {
                        $('#username').val('');
                        $('#password').val('');
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