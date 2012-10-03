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
    id:"movies_thead",
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
        if(UrlParams.Params.p == 1) {
            $('#first_page_link').css('visibility', 'hidden');
            $('#prev_page_link').css('visibility', 'hidden');
        } else {
            $('#first_page_link').css('visibility', 'visible');
            $('#prev_page_link').css('visibility', 'visible');
        }
        if(UrlParams.Params.p >= summary.totalPages) {
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
            UrlParams.Params.p = 1;
        } else if(paging_method == 'last') {
            UrlParams.Params.p = summary.totalPages;
        } else if(paging_method == 'prev' &&
                  UrlParams.Params.p > 1) {
            UrlParams.Params.p = parseInt(UrlParams.Params.p) - 1;
        } else if(paging_method == 'next' &&
                  UrlParams.Params.p < summary.totalPages) {
            UrlParams.Params.p = parseInt(UrlParams.Params.p) + 1;
        }
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    sort: function(ev) {
        if(UrlParams.Params.s == $(ev.currentTarget).attr('data-sort_order')) {
            UrlParams.Params.asc = !UrlParams.Params.asc
        } else {
            UrlParams.Params.s = $(ev.currentTarget).attr('data-sort_order');
            UrlParams.Params.asc = UrlParams.SortDefaults[UrlParams.Params.s];
        }
        UrlParams.Params.p = 1;
        $('.down_arrow').remove();
        $('.up_arrow').remove();
        if(UrlParams.Params.asc) {
            $(ev.currentTarget).prepend('<span class="up_arrow" />');
        } else {
            $(ev.currentTarget).prepend('<span class="down_arrow" />');
        }
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    reset: function() {
        $('#movie_title_search').val('');
        UrlParams.reset();
        app.navigate(UrlParams.query_string(), {'trigger':true});
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
        UrlParams.reset();
        UrlParams.Params.search = $('#movie_title_search').val();
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
});

window.MovieListView = Backbone.View.extend({
    tagName:"tbody",
    events: {
        'click span.genre_link': 'genreSearch',
        'click span.director_link': 'personSearch',
        'click span.actor_link': 'personSearch',
    },
    personSearch:function (ev) {
        UrlParams.reset();
        UrlParams.Params.pid = $(ev.currentTarget).attr('data-person_id');
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    genreSearch:function (ev) {
        UrlParams.reset();
        UrlParams.Params.gid = $(ev.currentTarget).attr('data-genre_id');
        app.navigate(UrlParams.query_string(), {'trigger':true});
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
var AppRouter = Backbone.Router.extend({
    routes:{
        "":"list",
        "#":"list",
        "*query_string": "list",
        "/movies/:imdb_id/":"movieDetails"
    },
    list:function (query_string) {
        var drawheader = !Boolean($('#movies_thead').html());
        var movieSummary = new MovieSummary();
        var movieSummaryView = new MovieSummaryView({model:movieSummary});
        UrlParams.parse(query_string);
        movieSummary.fetch({
            data:UrlParams.Params,
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
            {data:UrlParams.Params,
             success: function() {
                $('#movies_table').append(movieListView.render().el);
                $('#movies_table').css('display', 'block');
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
var UrlParams = {
    Params: {
        'p': null, /*page*/
        's': null, /*sort*/
        'asc': null, /*ascending*/
        'pid': null, /*person id*/
        'gid': null, /*genre id*/
        'search': null /*search*/
    },
    DefaultParams: {
        'p':1,
        's':'title',
        'asc':true,
        'gid':0,
        'pid':0,
        'search':''
    },
    SortDefaults: {
        'title': true,
        'release_year': false,
        'imdb_rating': false,
        'runtime': false,
        'filesize': false,
        'date_added': false,
        'hd': true,
        'watched': true
    },
    parse:function(query_string) {
        if(query_string == undefined || !query_string) {
            UrlParams.reset();
        } else {
            query_string.split('&').forEach(function(argument) {
                if(argument) {
                    fragment = argument.split('=');
                    UrlParams.Params[fragment[0]] = fragment[1];
                }
            });
        }
    },
    query_string:function() {
        var query_string = '';
        _.each(UrlParams.DefaultParams, function(value, key) {
            if((UrlParams.Params[key] != UrlParams.DefaultParams[key]) ||
                (key == 'asc' || key == 's')) {
                query_string += key+'='+UrlParams.Params[key]+"&";
            }
        });
        return query_string.slice(0, -1);
    },
    reset:function() {
        _.each(UrlParams.DefaultParams, function(value, key) {
            UrlParams.Params[key] = value;
        });
    }
};

UrlParams.reset();
var app = new AppRouter();
Backbone.history.start();