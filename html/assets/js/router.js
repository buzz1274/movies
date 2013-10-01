var AppRouter = Backbone.Router.extend({
    routes:{
        "/user/login": "login",
        "/user/logout": "logout",
        "download-queue": "download_queue",
        "file-error": "file_error",
        'login': "login",
        "":"list",
        "#":"list",
        "*query_string": "list"
    },
    list:function (query_string) {

        interface_helper.loadingImage(true);
        $(document).scrollTop(0);

        var movieSummary = new MovieSummary();
        var movieSearchView = new MovieSearchView({model:movieSummary, user:User});
        var movieHeaderView = new MovieHeaderView({model:null, user:User});
        var moviePagingView = new MoviePagingView({model:movieSummary});

        State.parse(query_string);
        movieSummary.fetch({
            async:true,
            data:State.Params,
            success: function() {
                var movieList = new MovieCollection();
                var movieListView = new MovieListView({model:movieList, user:User, summary:movieSummary});

                $('#movies_search').empty().prepend(movieSearchView.render().el);
                State.SliderValues.init();

                movieSearchView.render_slider('imdb_rating');
                movieSearchView.render_slider('runtime');
                movieSearchView.render_slider('release_year');

                State.fill_form();

                if(!movieSummary.get('total_movies')) {
                    $('#pagination').css('display', 'none');
                    $('#movies_table').empty().append(movieHeaderView.render().el);
                    $('#movies_table').append(movieListView.render().el);
                    $('#movies_table').css('display', 'block');
                    interface_helper.loadingImage(false);
                } else {
                    movieList.fetch({
                        async:true,
                        data:State.Params,
                        success: function() {
                            $('#movies_table').empty().append(movieHeaderView.render().el);
                            movieHeaderView.display_sort_icons();
                            $('#movies_table').append(movieListView.render().el);
                            $('#pagination').empty().append(moviePagingView.render().el);

                            $('#pagination').css('display', 'block');
                            $('#movies_table').css('display', 'block');
                            interface_helper.loadingImage(false);
                        },
                        error: function() {
                            $('#movies_table').empty().append(movieHeaderView.render().el);
                            $('#pagination').css('display', 'none');
                            $('#movies_table').append(movieListView.render().el);
                            $('#movies_table').css('display', 'block');
                            interface_helper.loadingImage(false);
                        }
                    });
                }
            },
            error:function(m, response) {
                //display error message//
            }
        });
    },
    download_queue : function() {
        $('#movies_table').css('display', 'none');
        $('#pagination').css('display', 'none');
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