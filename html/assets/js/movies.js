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
window.MovieSearchView = Backbone.View.extend({
    tagName:"div",
    template:_.template($('#tpl-movie-search').html()),
    events: {
        'mouseover img.icon': 'icon_over',
        'mouseout img.icon': 'icon_out',
        'click #submitButton': 'search',
        'click #resetButton': 'reset',
        'keypress #search_input': 'search_on_enter',
        'click #xls_icon': 'download',
    },
    render:function () {
        //assign correct values ot URLParams.SliderValues
        $(this.$el).empty().append(this.template(this.model.toJSON()));

        return this;
    },
    reset:function() {
        UrlParams.reset();
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    download:function() {
        alert("COMING SOON");
    },
    search_on_enter:function(e) {
        if(e.keyCode == 13) {
            this.search();
        }
    },
    search: function() {
        var search = $('#search_input').val();
        UrlParams.reset();
        UrlParams.Params.search = search;
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    icon_over:function() {
        $('#content').css('cursor', 'pointer');
    },
    icon_out:function() {
        $('#content').css('cursor', 'auto');
    },
    render_slider:function(id) {
        $(function() {
            $("#"+id+"_slider_range").slider({
                range: true,
                min: UrlParams.SliderValues[id].min,
                max: UrlParams.SliderValues[id].max,
                values: [UrlParams.SliderValues[id].current_min,
                         UrlParams.SliderValues[id].current_max],
                slide: function(event, ui) {
                    var id = $(event.target).parent().attr('id');
                    UrlParams.SliderValues[id].current_min = ui.values[0];
                    UrlParams.SliderValues[id].current_max = ui.values[1];
                    if(id == 'runtime') {
                        start = UrlParams.SliderValues.convert_to_time(ui.values[0]);
                        end = UrlParams.SliderValues.convert_to_time(ui.values[1]);
                    } else {
                        start = ui.values[0];
                        end = ui.values[1];
                    }
                    $("#"+id+"_label").html(start+" - "+end);
                },

            });
            if(id == 'runtime') {
                start = UrlParams.SliderValues.convert_to_time(
                                $("#"+id+"_slider_range").slider("values",0));
                end = UrlParams.SliderValues.convert_to_time(
                                $("#"+id+"_slider_range").slider("values",1));
            } else {
                start = $("#"+id+"_slider_range").slider("values",0);
                end = $("#"+id+"_slider_range").slider("values",1);
            }
            $("#"+id+"_label").html(start + " - " + end);
        });
    }
});
window.MovieHeaderView = Backbone.View.extend({
    tagName:"thead",
    template:_.template($('#tpl-movie-list-header').html()),
    events: {
        'click span.sort_link': 'sort',
    },
    render:function () {
        $(this.el).append(this.template());
        return this;
    },
    sort:function(ev) {
        if(UrlParams.Params.s == $(ev.currentTarget).attr('data-sort_order')) {
            UrlParams.Params.asc = UrlParams.Params.asc == 1 ? 0 : 1;
        } else {
            UrlParams.Params.s = $(ev.currentTarget).attr('data-sort_order');
            UrlParams.Params.asc = UrlParams.SortDefaults[UrlParams.Params.s];
        }
        UrlParams.Params.p = 1;
        app.navigate(UrlParams.query_string(), {'trigger':true});
    },
    display_sort_icons:function() {
        $('.sort_icon').remove();
        if(UrlParams.Params.asc == 1) {
            $("#"+UrlParams.Params.s+"_sort").prepend(
                '<span class="sort_icon">'+
                '<i class="icon-chevron-up"></i></span>');
        } else {
            $("#"+UrlParams.Params.s+"_sort").prepend(
                '<span class="sort_icon">'+
                '<i class="icon-chevron-down"></i></span>');
        }
    }
});
window.MoviePagingView = Backbone.View.extend({
    tagName:"div",
    template:_.template($('#tpl-movie-paging').html()),
    events: {
        'click img.paging_link': 'paging',
    },
    render: function(query_string) {
        $(this.el).append(this.template(this.model.toJSON()));
        return this;
    },
    paging:function(ev) {
        var paging_method = $(ev.currentTarget).attr('data_link_action');
        if(paging_method == 'first') {
            UrlParams.Params.p = 1;
        } else if(paging_method == 'last') {
            UrlParams.Params.p = Summary.totalPages;
        } else if(paging_method == 'prev' &&
                  UrlParams.Params.p > 1) {
            UrlParams.Params.p = parseInt(UrlParams.Params.p) - 1;
        } else if(paging_method == 'next' &&
                  UrlParams.Params.p < Summary.totalPages) {
            UrlParams.Params.p = parseInt(UrlParams.Params.p) + 1;
        }
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
        'click li.detail_link': 'details',
    },
    template:_.template($('#tpl-movie-list-item').html()),
    details:function() {
        console.log("here");
        imdb_id = $('li.detail_link', this.el).attr('data-imdb_id');
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
        var movieSummary = new MovieSummary();
        var movieSearchView = new MovieSearchView({model:movieSummary});
        var movieHeaderView = new MovieHeaderView();
        var moviePagingView = new MoviePagingView({model:movieSummary});
        UrlParams.parse(query_string);
        movieSummary.fetch({
            async:false,
            data:UrlParams.Params,
            success: function() {
                $('#movies_table').empty().append(movieHeaderView.render().el);
                $('#movies_search').empty().prepend(movieSearchView.render().el);
                $('#pagination').empty().append(moviePagingView.render().el);

                movieSearchView.render_slider('imdb_rating');
                movieSearchView.render_slider('runtime');
                movieSearchView.render_slider('release_year');

            }
        });
        var movieList = new MovieCollection();
        var movieListView = new MovieListView({model:movieList});
        movieList.fetch({
            async:false,
            data:UrlParams.Params,
            success: function() {
                $('#movies_table').append(movieListView.render().el);
                $('#movies_table').css('display', 'block');
            }
        });
        UrlParams.fill_form();
        movieHeaderView.display_sort_icons();
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
    qs:'',
    Params: {
        'p': null,
        's': null,
        'asc': null,
        'pid': null,
        'gid': null,
        'search': null,
        'imdb_rating':null,
    },
    DefaultParams: {
        'p':1,
        's':'title',
        'asc':1,
        'gid':0,
        'pid':0,
        'search':'',
        'imdb_rating':'',
    },
    SortDefaults: {
        'title': 1,
        'release_year': 0,
        'imdb_rating': 0,
        'runtime': 0,
        'filesize': 0,
        'date_added': 0,
        'hd': 1,
        'watched': 1,
        'cert':1
    },
    SliderValues: {
        imdb_rating: {
            min:2,
            max:9,
            current_min:2,
            current_max:9,
        },
        runtime: {
            min:22,
            max:229,
            current_min:22,
            current_max:229,
        },
        release_year: {
            min:1945,
            max:2012,
            current_min:1945,
            current_max:2012,
        },
       convert_to_time:function(minutes) {
            if(minutes < 60) {
                return minutes + "mins";
            } else {
                time = parseInt(minutes / 60);
                if(time == 1) {
                    time += "hr"
                } else {
                    time += "hrs"
                }
                if(minutes % 60) {
                    time += " " + (minutes % 60) + "mins";
                }
                return time;
            }
        },
    },
    parse:function(query_string) {
        if(query_string == undefined || !query_string) {
            UrlParams.reset();
        } else {
            this.qs = query_string;
            var page_in_params = false;
            query_string.split('&').forEach(function(argument) {
                if(argument) {
                    fragment = argument.split('=');
                    UrlParams.Params[fragment[0]] = fragment[1];
                    if(fragment[0] == 'p') {
                        page_in_params = true;
                    }
                }
            });
            if(!page_in_params) {
                UrlParams.Params.p = 1;
            }
        }
    },
    fill_form:function() {
        if(UrlParams.Params.search) {
            $('#search_input').val(UrlParams.Params.search);
        }
    },
    query_string:function() {
        var qs = '';
        _.each(UrlParams.DefaultParams, function(value, key) {
            if((UrlParams.Params[key] != UrlParams.DefaultParams[key]) &&
                (key != 'asc' && key != 's')) {
                qs += key+'='+UrlParams.Params[key]+"&";
            }
        });
        if(!(UrlParams.Params['s'] == 'title' && UrlParams.Params['asc'])) {
            qs += 's='+UrlParams.Params['s']+'&asc='+
                            UrlParams.Params['asc']+'&';
        }
        this.qs = qs.slice(0, -1);
        return this.qs;
    },
    remove_page_from_query_string:function() {
        return this.qs.replace(/&?p=[0-9]{1,}&?/gm, '');
    },
    reset:function() {
        _.each(UrlParams.DefaultParams, function(value, key) {
            UrlParams.Params[key] = value;
        });
        _.each(UrlParams.SliderValues, function(value, key) {
            UrlParams.SliderValues[key].current_min =
                UrlParams.SliderValues[key].min;
            UrlParams.SliderValues[key].current_max =
                UrlParams.SliderValues[key].max;
        });
    }
};
UrlParams.reset();
var app = new AppRouter();
Backbone.history.start();