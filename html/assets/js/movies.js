window.Movie = Backbone.Model.extend();
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
    events: {
        'click a.prevLink': 'prev',
        'click a.nextLink': 'next',
        'click a.sortLink': 'sort',
        'click a.advancedSearchLink': 'advanced_search',
        "click input.searchButton": 'search',
        "click input.resetButton": 'reset',
    },

    initialize:function () {
        this.model.bind("reset", this.render, this);
    },

    template:_.template($('#tpl-movie-list-header').html()),

    render:function (eventName) {
        summary = this.model.toJSON();
        $(this.el).append(this.template(summary));
        console.log(search);
        $('#movie_title_search').val(search);
        return this;
    },
    sort: function(ev) {
        if(sort == $(ev.currentTarget).attr('data-sort_order')) {
            sort_ascending = !sort_ascending;
        } else {
            sort = $(ev.currentTarget).attr('data-sort_order');
            sort_ascending = 1;
        }
        page = 1;
        app.list();
    },
    next: function() {
        if(page < summary.totalPages) {
            page += 1;
            app.list();
        }
        this.stylePagingLinks();
    },
    prev: function() {
        if(page > 1) {
            page -= 1;
            app.list();
        }
        this.stylePagingLinks();
    },
    reset: function() {
        page = 1;
        sort = 'title';
        sort_ascending = true;
        search = '';
        app.list();
    },
    advanced_search: function() {
        alert("COMING SOON");
    },
    search: function() {
        console.log("HERE");
        page = 1;
        sort = 'title';
        sort_ascending = true;
        search = $('#movie_title_search').val();
        app.list();
    },
    stylePagingLinks: function () {
        console.log("style links");
    }
});

window.MovieListView = Backbone.View.extend({
    tagName:"tbody",

    initialize:function () {
        this.model.bind("reset", this.render, this);
    },

    render:function (eventName) {
        _.each(this.model.models, function (movie) {
            $(this.el).append(new MovieListItemView({model:movie}).render().el);
        }, this);
        return this;
    },
});

window.MovieListItemView = Backbone.View.extend({
    tagName:"tr",
    events: {
        'mouseover': 'mouseoverrow',
        'mouseout': 'mouseoutrow',
        'click a.detailLink': 'details',
    },

    template:_.template($('#tpl-movie-list-item').html()),

    render:function (eventName) {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },
    details: function() {
        alert("SHOW MOVIE DETAILS");
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

    render:function (eventName) {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    }
});

// Router
var AppRouter = Backbone.Router.extend({

    routes:{
        "":"list",
        "movies/:id":"movieDetails"
    },

    list:function () {
        $('#movies_table').html('');
        var movieSummary = new MovieSummary();
        var movieSummaryView = new MovieSummaryView({model:movieSummary});
        movieSummary.fetch({
            data:{page:page,
                  search:search},
            success: function() {
                $('#movies_table').prepend(movieSummaryView.render().el);
            }
        });
        this.movieList = new MovieCollection();
        this.movieListView = new MovieListView({model:this.movieList});
        this.movieList.fetch({data:{page:page,
                                    sort:sort,
                                    search:search,
                                    sort_ascending:sort_ascending}});

        $('#movies_table').append(this.movieListView.render().el);
    },

    movieDetails:function (id) {
        this.movie = this.movieList.get(id);
        this.movieView = new movieView({model:this.movie});
        $('#movies').html(this.movieView.render().el);
    }
});

var search = '';
var page = 1;
var sort = 'title';
var sort_ascending = true;
var app = new AppRouter();
Backbone.history.start();