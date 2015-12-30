define(function(require) {
    "use strict";

    var Backbone = require("backbone"),
        $ = require("jquery"),
        Interface = require("helper/interface"),
        summary = require("models/movie/summary"),
        User = Backbone.Model.extend({
        url:'/user/',
        idAttribute: "user_id",
        poll: function() {
            var authenticated = this.get('authenticated');
            if(authenticated) {
                this.fetch({
                    url:'/user/',
                    success: function(model) {
                        if(authenticated == true &&
                           model.get('authenticated') == false) {
                            Backbone.history.navigate('auto_logout',
                                                      {'trigger':true});
                        }
                    },
                    error: function() {
                        Backbone.history.navigate('auto_logout',
                                                  {'trigger':true});
                    }
                });
            }
        },
        authenticate:function(action, username, password) {
            var data = null;
            if(action === 'login') {
                data = {username: username, password: password};
            }

            Interface.loginPopup(false);

            this.save(data,
                {url:'/user/'+action+'/',
                    success: function(model, response) {
                        if(action === 'logout') {
                            Interface.messagePopup('success', 'You have logged out');
                        }
                        model.set({username: null, password: null,
                                   name: response.name,
                                   admin: response.admin,
                                   authenticated: response.authenticated});
                        Backbone.history.loadUrl();
                    },
                    error: function(model, response) {
                        var body = JSON.parse(response.responseText);
                        if(body.error_type == 'invalid_credentials') {
                            Interface.loginPopup(true, body.error_message);
                        } else {
                            Interface.messagePopup();
                        }
                    }
                }
            );
        },
        favourite:function(movie) {
            var Movie = movie.get('Movie');
            Movie.favourite = !Movie.favourite;
            var data = {'movie_id': Movie.movie_id,
                        'favourite' : Movie.favourite};

            this.save(data,
                {url:'/user/favourite/',
                    success: function() {
                        var message = '',
                            not_favourites = summary.get('not_favourites'),
                            favourites = summary.get('favourites');

                        if(Movie.favourite) {
                            message = 'Movie added to favourites';
                        } else {
                            message = 'Movie removed from favourites';
                        }

                        Interface.messagePopup('success', message);
                        movie.set(Movie);

                        if(Movie.favourite) {
                            summary.set({not_favourites: not_favourites - 1,
                                         favourites: favourites + 1});
                        } else {
                            summary.set({not_favourites: not_favourites + 1,
                                         favourites: favourites - 1});
                        }
                    },
                    error: function() {
                        if(Movie.favourite) {
                            var message = 'Error adding movie to favourites';
                        } else {
                            var message = 'Error removing movie from favourites';
                        }
                        Interface.messagePopup('error', message);
                        Movie.favourite = !Movie.favourite;
                        movie.set(Movie);
                    }
                }
            );
        },
        watched:function(movie_model, movie_summary, watched_id) {
            var movie = movie_model.get('Movie');
            var watched = movie_model.get('Watched');
            var total_watched = _.size(watched);

            var data = {'movie_id': movie.movie_id,
                        'watched_id': watched_id};

            this.save(data,
                {url:'/user/watched/',
                    success: function(model, response) {
                        if(!watched_id) {
                            watched[total_watched] = {date_watched: response.date_watched,
                                                      id: response.id};
                            movie_model.set(watched);
                        } else {
                            _.each(watched, function(movie_watched, key) {
                                if(typeof movie_watched !== 'undefined' &&
                                   movie_watched.id == watched_id) {
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
                        Interface.messagePopup('success', message);
                    },
                    error: function() {
                        if(!watched_id) {
                            var message = 'Error adding movie to watched';
                        } else {
                            var message = 'Error removing movie from watched';
                        }
                        Interface.messagePopup('error', message);
                        Movie.favourite = !Movie.favourite;
                    }
                }
            );
        }
    });

    return new User();

});