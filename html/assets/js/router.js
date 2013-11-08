define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        Interface = require('helper/interface'),
        State = require('helper/state'),
        MovieCollection = require('collections/movie'),
        MovieCollectionView = require('views/movie/collection'),
        MovieDownloadedCollection = require('collections/downloaded'),
        MovieDownloadedCollectionView = require('views/movie/downloaded_collection'),
        TableHeaderView = require('views/th'),
        MoviePagingView = require('views/paging'),
        movieSummary = require('models/movie/summary'),
        downloadMovieSummary = require('models/movie/download_summary'),
        user = require('models/user/user');

    module.exports = Backbone.Router.extend({
        routes:{
            "/user/login": "login",
            "/user/logout": "logout",
            "download_queue*query_string": "downloadQueue",
            "auto_logout": "autoLogout",
            "file_error": "fileError",
            'login': "login",
            "":"list",
            "#":"list",
            "*query_string": "list"
        },
        list:function(query_string) {
            Interface.loadingImage(true);
            Interface.scrollTop();
            var tableHeaderView = new TableHeaderView({model:null,
                                                       template:'MovieHeaderTemplate'}),
                movieCollection = new MovieCollection(),
                movieCollectionView = new MovieCollectionView({model: movieCollection}),
                moviePagingView = new MoviePagingView();

            State.populateWithQueryStringValues(query_string);
            movieSummary.fetch({data:State.getState().Params});

            $('#movies_table').empty().append(tableHeaderView.render());

            movieCollection.fetch({
                data:State.getState().Params,
                success:function() {
                    $('#movies_table').append(movieCollectionView.render().el);
                    $('#pagination').empty().append(moviePagingView.render().el);

                    Interface.loadingImage(false);
                },
                error:function() {
                    $('#movies_table').append(movieCollectionView.render().el);

                    Interface.loadingImage(false);
                }
            })
        },
        downloadQueue:function(query_string) {
            Interface.loadingImage(true);
            Interface.scrollTop();
            var tableHeaderView = new TableHeaderView({model:null,
                                                       template:'MovieDownloadedHeaderTemplate'}),
                movieDownloadedCollection = new MovieDownloadedCollection(),
                movieDownloadedCollectionView = new MovieDownloadedCollectionView(
                                                        {model: movieDownloadedCollection}),
                moviePagingView = new MoviePagingView();

            State.populateWithQueryStringValues(query_string);
            movieSummary.fetch({data:null});
            downloadMovieSummary.fetch({});

            $('#movies_table').empty().append(tableHeaderView.render());

            movieDownloadedCollection.fetch({
                data:{'p': State.getState().Params.p},
                success: function() {
                    $('#movies_table').append(movieDownloadedCollectionView.render().el);
                    $('#pagination').empty().append(moviePagingView.render().el);
                    Interface.loadingImage(false);
                },
                error: function() {
                    $('#movies_table').append(movieDownloadedCollectionView.render().el);
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