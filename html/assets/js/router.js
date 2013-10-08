var AppRouter = Backbone.Router.extend({
    routes:{
        "/user/login": "login",
        "/user/logout": "logout",
        "download-queue*query_string": "download_queue",
        "file-error": "file_error",
        'login': "login",
        "":"list",
        "#":"list",
        "*query_string": "list"
    },
    list:function (query_string) {
        var movieSummary = interface_helper.search_form(query_string, User),
            movieHeaderView = new MovieHeaderView({model:null, user:User}),
            movieList = new MovieCollection(),
            movieListView = new MovieListView({model:movieList, user:User, summary:movieSummary}),
            moviePagingView = new MoviePagingView({model:movieSummary});

        movieList.fetch({
            async:true,
            data:State.Params,
            success: function() {
                try {
                    $('#movies_table').empty().append(movieHeaderView.render().el);
                    movieHeaderView.display_sort_icons();
                    $('#movies_table').append(movieListView.render().el);
                    $('#pagination').empty().append(moviePagingView.render().el);

                    $('#pagination').css('display', 'block');
                    $('#movies_table').css('display', 'block');
                    interface_helper.loadingImage(false);
                } catch(e) {
                    Backbone.history.loadUrl();
                }
            },
            error: function() {
                $('#movies_table').empty().append(movieHeaderView.render().el);
                $('#movies_table').append(movieListView.render().el);

                $('#pagination').css('display', 'none');
                $('#movies_table').css('display', 'block');
                interface_helper.loadingImage(false);
            }
        });
    },
    download_queue : function(query_string) {
        interface_helper.search_form(query_string);

        var downloadedList = new UserMovieDownloadedCollection(),
            downloadedHeaderView = new UserDownloadedHeaderView(),
            moviePagingView = new MoviePagingView(),
            downloadedView = new UserDownloadedView({model: downloadedList,
                                                     user: User});



        downloadedList.fetch({
            data:{'p': State.Params.p},
            success: function() {
                try {
                    $('#movies_table').empty().append(downloadedHeaderView.render().el);
                    $('#movies_table').append(downloadedView.render().el);
                    //$('#pagination').empty().append(moviePagingView.render().el);
                    $('#movies_table').css('display', 'block');
                    interface_helper.loadingImage(false);
                } catch(e) {
                    console.log(e);
                    //Backbone.history.loadUrl();
                }
            },
            error: function() {
                $('#movies_table').empty().append(downloadedHeaderView.render().el);
                $('#movies_table').append(downloadedView.render().el);
                $('#movies_table').css('display', 'block');
                interface_helper.loadingImage(false);
            }
        });
    },
    login:function() {
        this.list();
        if(!User.attributes.authenticated) {
            var header = new HeaderView({model: User});
            header.login_popup(false);
        } else {
            $('#login_popup').html('');
        }
    },
    file_error:function() {
        this.list();
        interface_helper.message_popup('error', 'An occurred downloading the file');
    }
});