var AppRouter = Backbone.Router.extend({
    routes:{
        "/user/login": "login",
        "/user/logout": "logout",
        "file-error": "file_error",
        "":"list",
        "#":"list",
        "/movies/:imdb_id/":"movieDetails",
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
                            $('#pagination').css('display', 'none');
                            $('#movies_table').append(movieListView.render().el);
                            $('#movies_table').css('display', 'block');
                            interface_helper.loadingImage(false);
                        }
                    });
                }
            }
        });
    },
    movieDetails:function (movie_id, element) {
        interface_helper.loadingImage(true);
        var movie = new Movie();
        movie.url = '../../movies/'+movie_id+'/';
        var movieView = new MovieView({model:movie, user:User});
        movie.fetch({
            async:true,
            success: function() {
                $('tr#movie_'+movie_id).remove();
                movieView.render(element);
                interface_helper.loadingImage(false);
            }
        });
    },
    file_error:function() {
        this.list();
        interface_helper.message_popup('error', 'An error occurred whilst downloading the file.');
    }
});