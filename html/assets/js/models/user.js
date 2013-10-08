window.MovieUser = Backbone.Model.extend({
    url:'/user/',
    idAttribute: "user_id",
    poll: function() {
        var authenticated = this.get('authenticated');
        if(authenticated) {
            this.fetch({
                url:'/user/',
                async:true,
                success: function(model) {
                    if(authenticated == true &&
                        model.get('authenticated') == false) {
                        interface_helper.message_popup('error',
                            'You have been automatically logged out');
                    }
                }
            });
        }
    },
    authenticate:function(e, headerView) {
        if(typeof(e.keyCode) != 'undefined' && e.keyCode != 13) {
            return;
        } else if(e.target.id == 'loginButton' || e.keyCode == 13) {
            var action = 'login'
        } else {
            var action = 'logout';
        }
        this.headerView = headerView;
        var parent = this, data = null;
        if(action == 'login') {
            data = {username: $('#username').val(),
                password: $('#password').val()};
        }
        this.save(data,
            {url:'/user/'+action+'/',
                success: function(model, response) {
                    parent.headerView.login_popup(e);
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
                        parent.headerView.login_popup(e);
                        interface_helper.message_popup();
                    }
                }
            }
        );
    },
    watched:function(movie_model, summary, movie_summary, watched_id) {
        var movie = movie_model.get('Movie');
        var watched = movie_model.get('Watched');
        var total_watched = _.size(watched);

        var data = {'movie_id': movie.movie_id,
            'watched_id': watched_id};

        this.save(data,
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
    download:function(movie_id, download_id) {
        var data = {'movie_id': movie_id,
            'download_id': download_id};

        User.save(data,
            {url:'/user/downloaded/',
                success:function() {
                    if(download_id) {
                        var message = 'Movie download cancelled';
                    } else {
                        var message = 'Movie queued for download';
                    }
                    interface_helper.message_popup('success', message);
                },
                error:function(m, r) {
                    if(download_id) {
                        var message = 'An error occurred removing movie from download queue';
                    } else {
                        var message = 'An error occurred queuing movie for download';
                    }
                    interface_helper.message_popup('error', message);
                }}
        );
    },
    favourite:function(movie, summary) {
        var Movie = movie.get('Movie');
        Movie.favourite = !Movie.favourite;
        var data = {'movie_id': Movie.movie_id,
            'favourite' : Movie.favourite};

        this.save(data,
            {url:'/user/favourite/',
                success: function(model) {
                    if(Movie.favourite) {
                        var message = 'Movie added to favourites';
                    } else {
                        var message = 'Movie removed from favourites';
                    }
                    interface_helper.message_popup('success', message);
                    movie.set(Movie);

                    var not_favourites = summary.get('not_favourites');
                    var favourites = summary.get('favourites');

                    if(Movie.favourite) {
                        summary.set({not_favourites: not_favourites - 1,
                            favourites: favourites + 1});
                    } else {
                        summary.set({not_favourites: not_favourites + 1,
                            favourites: favourites - 1});
                    }
                },
                error: function(model) {
                    if(Movie.favourite) {
                        var message = 'Error adding movie to favourites';
                    } else {
                        var message = 'Error removing movie from favourites';
                    }
                    interface_helper.message_popup('error', message);
                    Movie.favourite = !Movie.favourite;
                    movie.set(Movie);
                }
            }
        );
        $('#movies_table > tbody').children('tr').css("background-color","");
    }
});