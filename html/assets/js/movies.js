window.Movie = Backbone.Model.extend({
    url:'../../movies/',
});
window.MovieSummary = Backbone.Model.extend({
   url:'../../movies/summary/',
});
window.MovieCollection = Backbone.Collection.extend({
    model:Movie,
    url:'../../movies/',
});

window.MovieSummaryView = Backbone.View.extend({
    tagName:"thead",
    summary:false,
    template:_.template($('#tpl-movie-list-header').html()),
    events: {
        'click span.paging_link': 'paging',
        'click span.sort_link': 'sort',
        'keypress #movie_title_search': 'searchOnEnter',
        'click span.advanced_search_link': 'advanced_search',
        "click input.searchButton": 'search',
        "click input.resetButton": 'reset',
    },
    initialize:function () {
        this.model.bind("reset", this.render, this);
    },
    render:function (eventName) {
        summary = this.model.toJSON();
        $(this.el).append(this.template(summary));
        return this;
    },
    update:function() {
        summary = this.model.toJSON();
        if(!summary.totalMovies) {
            $('#result_count').css('visibility', 'hidden');
        } else {
            $('#result_count').css('visibility', 'visible');
            $('#result_count').html(summary.startOffset + ' to '+
                                    summary.endOffset + ' of ' +
                                    summary.totalMovies + ' Movies');
        }
        if(page == 1) {
            $('#first_page_link').css('visibility', 'hidden');
            $('#prev_page_link').css('visibility', 'hidden');
        } else {
            $('#first_page_link').css('visibility', 'visible');
            $('#prev_page_link').css('visibility', 'visible');
        }
        if(page >= summary.totalPages) {
            $('#last_page_link').css('visibility', 'hidden');
            $('#next_page_link').css('visibility', 'hidden');
        } else {
            $('#last_page_link').css('visibility', 'visible');
            $('#next_page_link').css('visibility', 'visible');
        }
    },
    paging: function(ev) {
        var paging_method = $(ev.currentTarget).attr('data_link_action');
        if(paging_method == 'first') {
            page = 1;
        } else if(paging_method == 'last') {
            page = summary.totalPages;
        } else if(paging_method == 'prev' && page > 1) {
            page -= 1;
        } else if(paging_method == 'next' && page < summary.totalPages) {
            page += 1;
        }
        app.list(false);
    },
    sort: function(ev) {
        if(sort == $(ev.currentTarget).attr('data-sort_order')) {
            sort_ascending = !sort_ascending;
        } else {
            sort = $(ev.currentTarget).attr('data-sort_order');
            sort_ascending = sort_defaults[sort];
        }
        page = 1;
        app.list(false);
    },
    reset: function() {
        $('#movie_title_search').val('');
        page = 1;
        genre_id=0;
        person_id=0;
        sort = 'title';
        sort_ascending = true;
        search = '';
        app.list(false);
    },
    advanced_search: function() {
        alert("COMING SOON");
    },
    searchOnEnter: function(e) {
        if(e.keyCode == 13) {
            this.search();
        }
    },
    search: function() {
        page = 1;
        sort = 'title';
        sort_ascending = true;
        search = $('#movie_title_search').val();
        app.list(false);
    },
    stylePagingLinks: function () {
        console.log("style links");
    }
});

window.MovieListView = Backbone.View.extend({
    tagName:"tbody",
    events: {
        'click span.genre_link': 'genreSearch',
        'click span.director_link': 'personSearch',
        'click span.actor_link': 'personSearch',
    },
    personSearch:function (ev) {
        page = 1;
        person_id = $(ev.currentTarget).attr('data-person_id');
        app.list(false);
    },
    genreSearch:function (ev) {
        page = 1;
        genre_id = $(ev.currentTarget).attr('data-genre_id');
        app.list(false);
    },
    render:function (eventName) {
        $('#movies_table > tbody').html('');
         if(this.model.models.length) {
            _.each(this.model.models, function (movie) {
                $(this.el).append(new MovieListItemView({model:movie}).render().el);
            }, this);
         } else {
             $(this.el).append(_.template($('#tpl-movie-list-no-results').html()));
         }
        return this;
    },
});

window.MovieListItemView = Backbone.View.extend({
    tagName:"tr",
    events: {
        'mouseover': 'mouseoverrow',
        'mouseout': 'mouseoutrow',
        'click span.detail_link': 'details',
    },
    template:_.template($('#tpl-movie-list-item').html()),
    details:function() {
        imdb_id = $('span.detail_link', this.el).attr('data-imdb_id');
        if($('tr#'+imdb_id).html()) {
            $('#'+imdb_id).remove();
        } else {
            app.movieDetails(imdb_id, this.el);
        }
    },
    render:function (eventName) {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },
    mouseoverrow: function() {
        $(this.el).css("background-color","#BADA55");
    },
    mouseoutrow: function() {
        $(this.el).css("background-color","");
    },
});

window.MovieView = Backbone.View.extend({
    tagname:"tr",
    template:_.template($('#tpl-movie-details').html()),
    render:function (element) {
        $(element).after(this.template(this.model.toJSON()));
        return this;
    },
});

// Router
var AppRouter = Backbone.Router.extend({
    routes:{
        "":"list",
        "/movies/:imdb_id/":"movieDetails"
    },
    list:function (drawheader) {
        var drawheader = drawheader == undefined ? true : false;
        var movieSummary = new MovieSummary();
        var movieSummaryView = new MovieSummaryView({model:movieSummary});
        movieSummary.fetch({
            data:{page:page,
                  genre_id:genre_id,
                  person_id:person_id,
                  search:search},
            success: function() {
                if(drawheader) {
                    $('#movies_table').prepend(movieSummaryView.render().el);
                } else {
                    movieSummaryView.update();
                }
            }
        });
        var movieList = new MovieCollection();
        var movieListView = new MovieListView({model:movieList});
        movieList.fetch(
            {data:{page:page,
                   sort:sort,
                   search:search,
                   genre_id:genre_id,
                   person_id:person_id,
                   sort_ascending:sort_ascending},
             success: function() {
                $('#movies_table').append(movieListView.render().el);
                $('#movies_table').css('display', 'block');
                $('#version').css('display', 'block');
            }
        });
    },
    movieDetails:function (imdb_id, element) {
        var movie = new Movie();
        movie.url = '../../movies/'+imdb_id+'/';
        var movieView = new MovieView({model:movie});
        movie.fetch({
            success: function() {
                movieView.render(element);
            }
        });
    }
});
var sort_defaults = {
    'title': true,
    'release_year': false,
    'imdb_rating': false,
    'runtime': false,
    'filesize': false,
    'date_added': false,
    'hd': true,
    'watched': true
};
var search = '';
var page = 1;
var sort = 'title';
var sort_ascending = true;
var genre_id = 0;
var person_id = 0;
var app = new AppRouter();
Backbone.history.start();