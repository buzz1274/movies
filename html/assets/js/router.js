var AppRouter = Backbone.Router.extend({
    routes:{
        "":"list",
        "#":"list",
        "*query_string": "list",
        "/movies/:imdb_id/":"movieDetails"
    },
    list:function (query_string) {
        loadingImage(true);
        $(document).scrollTop(0);

        var movieSummary = new MovieSummary();
        var movieSearchView = new MovieSearchView({model:movieSummary, user:User});
        var movieHeaderView = new MovieHeaderView({model:null, user:User});
        var moviePagingView = new MoviePagingView({model:movieSummary});

        UrlParams.parse(query_string);
        movieSummary.fetch({
            async:true,
            data:UrlParams.Params,
            success: function() {
                var movieList = new MovieCollection();
                var movieListView = new MovieListView({model:movieList, user:User, summary:movieSummary});

                $('#movies_search').empty().prepend(movieSearchView.render().el);
                UrlParams.SliderValues.init();

                movieSearchView.render_slider('imdb_rating');
                movieSearchView.render_slider('runtime');
                movieSearchView.render_slider('release_year');

                UrlParams.fill_form();

                if(!movieSummary.get('total_movies')) {
                    $('#pagination').css('display', 'none');
                    $('#movies_table').empty().append(movieHeaderView.render().el);
                    $('#movies_table').append(movieListView.render().el);
                    $('#movies_table').css('display', 'block');
                    loadingImage(false);
                } else {
                    movieList.fetch({
                        async:true,
                        data:UrlParams.Params,
                        success: function() {
                            $('#movies_table').empty().append(movieHeaderView.render().el);
                            movieHeaderView.display_sort_icons();
                            $('#movies_table').append(movieListView.render().el);
                            $('#pagination').empty().append(moviePagingView.render().el);

                            $('#pagination').css('display', 'block');
                            $('#movies_table').css('display', 'block');
                            loadingImage(false);
                        },
                        error: function() {
                            $('#pagination').css('display', 'none');
                            $('#movies_table').append(movieListView.render().el);
                            $('#movies_table').css('display', 'block');
                            loadingImage(false);
                        }
                    });
                }
            }
        });
    },
    movieDetails:function (movie_id, element) {
        loadingImage(true);
        var movie = new Movie();
        movie.url = '../../movies/'+movie_id+'/';
        var movieView = new MovieView({model:movie, user:User});
        movie.fetch({
            async:true,
            success: function() {
                $('tr#movie_'+movie_id).remove();
                movieView.render(element);
                loadingImage(false);
            }
        });
    },
    authenticate:function(action) {
        var login = new LoginView({model: User})
        if(action == 'login') {
            $('#login_popup').append(login.render().el);
        } else if(action == 'logout') {
            login.authenticate('logout');
        }
    }
});