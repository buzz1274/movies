define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        Interface = require('helper/interface'),
        State = require('helper/state'),
        MovieCollection = require('collections/movie'),
        MovieCollectionView = require('views/movie/collection'),
        TableHeaderView = require('views/th'),
        MoviePagingView = require('views/paging'),
        movieSummary = require('models/movie/summary'),
        user = require('models/user/user');

    module.exports = Backbone.Router.extend({
        routes:{
            "/user/login": "login",
            "/user/logout": "logout",
            "auto_logout": "autoLogout",
            "file_error": "fileError",
            'login': "login",
            "":"list",
            "#":"list",
            "*query_string": "list"
        },
        list: function (query_string) {
            Interface.loadingImage(true);
            Interface.scrollTop();
            var tableHeaderView = new TableHeaderView({model:null,
                                                       template:'MovieHeaderTemplate'}),
                movieCollection = new MovieCollection(),
                movieCollectionView = new MovieCollectionView({model: movieCollection}),
                moviePagingView = new MoviePagingView({model: movieSummary});

            State.populateWithQueryStringValues(query_string);

            movieSummary.fetch({
                data: State.getState().Params,
                success: function () {
                    $('#movies_table').empty().append(tableHeaderView.render());

                    if (!movieSummary.get('totalMovies')) {
                        Interface.loadingImage(false);
                        $('#movies_table').append(movieCollectionView.render().el);
                    } else {
                        movieCollection.fetch({
                            data: State.getState().Params,
                            success: function () {
                                $('#movies_table').append(movieCollectionView.render().el);
                                $('#pagination').empty().append(moviePagingView.render('summary').el);

                                Interface.loadingImage(false);
                            },
                            error: function () {
                                //display error page
                                Interface.loadingImage(false);
                            }
                        });
                    }
                },
                error: function() {
                    //display error page
                    Interface.loadingImage(false);
                }
            });
        },
        autoLogout:function() {
            if(!user.get('authenticated')) {
                Interface.messagePopup('error',
                        'You have been automatically logged out');
            }
            Backbone.history.navigate('/', {'trigger':true});
        },
        fileError:function() {
            Interface.messagePopup('error',
                         'An occurred downloading the file');
            Backbone.history.navigate(State.constructQueryString(),
                                      {'trigger':true});
        },
        login:function() {
            if(!user.get('authenticated')) {
                Interface.loginPopup(true);
            } else {
                Interface.loginPopup(false);
            }
            Backbone.history.navigate(State.constructQueryString(),
                                      {'trigger':true});
        }
    });
});