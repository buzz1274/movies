window.MovieSearchView = Backbone.View.extend({
    tagName:"div",
    template:_.template($('#tpl-movie-search').html()),
    events: {
        'mouseover img.icon': 'icon_over',
        'mouseout img.icon': 'icon_out',
        'click #submitButton': 'search',
        'click #luckyButton': 'lucky',
        'click #resetButton': 'reset',
        'keypress #search_input': 'autocomplete',
        'click #download': 'download'
    },
    initialize: function() {
        _.bindAll(this, 'render');
        this.options.user.bind("change:authenticated", this.render);
        if(typeof this.model != 'undefined') {
            this.model.bind("change:watched", this.render);
            this.model.bind("change:favourites", this.render);
        }
    },
    render:function () {
        if(this.model) {
            summary = this.model.toJSON();
        }
        user = this.options.user.toJSON();

        if(typeof summary.totalMovies == "number" &&
           summary.totalMovies != 0 &&
           summary.totalMovies != null) {

            State.SliderValues['imdb_rating'].current_min =
                Math.floor(summary.min_imdb_rating);
            State.SliderValues['imdb_rating'].current_max =
                Math.ceil(summary.max_imdb_rating);
            State.SliderValues['release_year'].current_min =
                summary.min_release_year;
            State.SliderValues['release_year'].current_max =
                summary.max_release_year;
            State.SliderValues['runtime'].current_min =
                summary.min_runtime;
            State.SliderValues['runtime'].current_max =
                summary.max_runtime;
        }

        $(this.$el).empty().append(this.template(summary, user));

        this.render_slider('imdb_rating');
        this.render_slider('runtime');
        this.render_slider('release_year');

        State.fill_form();

        return this;
    },
    reset:function() {
        State.reset(true);
        app.navigate(State.query_string(), {'trigger':true});
    },
    download:function() {
        search = window.location.hash.slice(1, window.location.hash.length);
        window.location = "/movies/csv?"+search;
    },
    lucky:function(e) {
        State.reset(false);
        State.parse_search_form();
        $.ajax({
            url:'/movies/lucky/',
            data:State.Params,
            dataType: "json",
            async:true,
            success:function(data) {
                app.navigate('/#id='+data['movieID'], {'trigger':true});
            },
            error: function(data) {
                app.navigate(State.query_string(), {'trigger':true});
            }
        });
    },
    autocomplete:function(e) {
        if(e.keyCode == 13) {
            this.search(e, false);
        } else {
            var search = $('#search_input').val() + String.fromCharCode(e.keyCode);

            if(search.length >= 3) {
                $.ajax({
                    url:'/movies/autocomplete/',
                    data:{'search':search},
                    dataType: "json",
                    async:true,
                    success:function(data) {
                        if(data) {
                            $("#search_input").autocomplete({
                                source: data.dropdown,
                                select: function() {
                                    State.reset(false);
                                    _.each(data.results, function(result) {
                                        if(result.keyword == $('#search_input').val()) {
                                            app.navigate('#search_type='+result.search_type+
                                                         '&search='+encodeURIComponent(result.keyword),
                                                         {'trigger':true});

                                        }
                                    });
                                }
                            })
                        }
                    }
                });
            }
        }
    },
    search:function(e, lucky) {
        State.reset(false);
        State.parse_search_form();
        app.navigate(State.query_string(), {'trigger':true});
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
                min: State.SliderValues[id].min,
                max: State.SliderValues[id].max,
                values: [State.SliderValues[id].current_min,
                         State.SliderValues[id].current_max],
                slide: function(event, ui) {
                    var id = $(event.target).parent().attr('id');
                    State.SliderValues[id].current_min = ui.values[0];
                    State.SliderValues[id].current_max = ui.values[1];
                    State.SliderValues[id].active = true;
                    if(id == 'runtime') {
                        start = helper.convert_to_time(ui.values[0]);
                        end = helper.convert_to_time(ui.values[1]);
                    } else {
                        start = ui.values[0];
                        end = ui.values[1];
                    }
                    $("#"+id+"_label").html(start+" - "+end);
                }
            });
            if(id == 'runtime') {
                start = helper.convert_to_time(
                                $("#"+id+"_slider_range").slider("values",0));
                end = helper.convert_to_time(
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
        'click span.sort_link': 'sort'
    },
    initialize: function() {
        _.bindAll(this, 'render');
        this.options.user.bind("change:authenticated", this.render);
    },
    render:function () {
        var user = this.options.user.toJSON();
        $(this.el).html(this.template(user));
        return this;
    },
    sort:function(ev) {
        if(State.Params.s == $(ev.currentTarget).attr('data-sort_order')) {
            State.Params.asc = State.Params.asc == 1 ? 0 : 1;
        } else {
            State.Params.s = $(ev.currentTarget).attr('data-sort_order');
            State.Params.asc = State.SortDefaults[State.Params.s];
        }
        State.Params.p = 1;
        app.navigate(State.query_string(), {'trigger':true});
    },
    display_sort_icons:function() {
        $('.sort_icon').remove();
        if(State.Params.asc == 1) {
            $("#"+State.Params.s+"_sort").prepend(
                '<span class="sort_icon">'+
                '<i class="icon-chevron-up"></i></span>');
        } else {
            $("#"+State.Params.s+"_sort").prepend(
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
            State.Params.p = 1;
        } else if(paging_method == 'last') {
            State.Params.p = Summary.totalPages;
        } else if(paging_method == 'prev' &&
                  State.Params.p > 1) {
            State.Params.p = parseInt(State.Params.p) - 1;
        } else if(paging_method == 'next' &&
                  State.Params.p < Summary.totalPages) {
            State.Params.p = parseInt(State.Params.p) + 1;
        }
        app.navigate(State.query_string(), {'trigger':true});
    }
});
window.MovieListView = Backbone.View.extend({
    tagName:"tbody",
    events: {
        'click span.genre_link': 'genreSearch',
        'click span.keyword_link': 'keywordSearch',
        'click span.director_link': 'personSearch',
        'click span.actor_link': 'personSearch',
        'click span.media_link': 'mediaSearch',
        'click span.edit_media': 'editMedia',
        'click span.show-all-link': 'showAll'
    },
    initialize:function() {
        this.options.user.bind("change:authenticated", this.render, this);
    },
    editMedia:function(ev) {
        alert("EDIT MEDIA ---- COMING SOON");
    },
    keywordSearch:function(ev) {
        State.reset(true);
        State.Params.kid = $(ev.currentTarget).attr('data-keyword_id');
        State.Params.search = $(ev.currentTarget).attr('data-keyword');
        State.Params.search_type = 'keyword';
        app.navigate(State.query_string(), {'trigger':true});
    },
    personSearch:function (ev) {
        State.reset(true);
        State.Params.pid = $(ev.currentTarget).attr('data-person_id');
        State.Params.search = $(ev.currentTarget).attr('data-person_name');
        State.Params.search_type = 'cast';
        app.navigate(State.query_string(), {'trigger':true});
    },
    mediaSearch: function(ev) {
        State.reset(true);
        State.Params.mid = $(ev.currentTarget).attr('data-media_id');
        app.navigate(State.query_string(), {'trigger':true});
    },
    genreSearch:function (ev) {
        State.reset(true);
        State.Params.gid = $(ev.currentTarget).attr('data-genre_id');
        app.navigate(State.query_string(), {'trigger':true});
    },
    showAll:function(ev) {
        var id = $(ev.currentTarget).attr('data-movie-id');
        var type = $(ev.currentTarget).attr('id').match(/keyword|cast/g);

        if($('#all_'+type+'_'+id).css('display') == 'none') {
            $('#all_'+type+'_'+id).css('display', 'block');
            $('#show_all_'+type+'_'+id).html('<a class="btn btn-mini">Hide All</a>');
        } else {
            $('#all_'+type+'_'+id).css('display', 'none');
            $('#show_all_'+type+'_'+id).html('<a class="btn btn-mini">Show All</a>');
        }
    },
    render:function (eventName) {
        $('#movies_table > tbody').html('');
        if(this.model.models.length) {
            $('#movies_table').addClass('table-condensed');
            _.each(this.model.models, function (movie) {
                $(this.el).append(new MovieListItemView({model:movie, user: this.options.user,
                                                         summary: this.options.summary}).render().el);
            }, this);
        } else {
            $('#movies_table').removeClass('table-condensed');
            $(this.el).append(_.template($('#tpl-movie-list-no-results').html()));
        }
        return this;
    }
});
window.MovieListItemView = Backbone.View.extend({
    tagName:"tr",
    events: {
        'mouseover': 'mouseoverrow',
        'mouseout': 'mouseoutrow',
        'click li.favourite_link': 'favourite',
        'click li.detail_link': 'details'
    },
    template:_.template($('#tpl-movie-list-item').html()),
    initialize: function() {
        this.Movie = this.model.get('Movie');
        this.summary = this.options.summary;
        _.bindAll(this, "render");
        this.options.user.bind("change:authenticated", this.render);
        this.model.bind('change', this.render);
    },
    attributes: function() {
        var Movie = this.model.get('Movie');
        return {
            id: Movie.imdb_id
        };
    },
    details:function() {
        if($('tr#movie_'+this.Movie.movie_id).html()) {
            $('tr#movie_'+this.Movie.movie_id).html('');
        } else {
            app.movieDetails(this.Movie.movie_id, this.el);
        }
    },
    favourite:function() {
        var Movie = this.model.get('Movie');
        var parent = this;
        Movie.favourite = !Movie.favourite;

        this.model.save({},
            {url:'/user/favourite/:id/',
                success: function(model, response) {
                    if(Movie.favourite) {
                        var message = 'Movie added to favourites';
                    } else {
                        var message = 'Movie removed from favourites';
                    }
                    interface_helper.message_popup('success', message);
                    model.set(Movie);

                    var not_favourites = parent.summary.get('not_favourites');
                    var favourites = parent.summary.get('favourites');

                    if(Movie.favourite) {
                        parent.summary.set({not_favourites: not_favourites - 1,
                                            favourites: favourites + 1});
                    } else {
                        parent.summary.set({not_favourites: not_favourites + 1,
                                            favourites: favourites - 1});
                    }
                },
                error: function(model, response) {
                    if(Movie.favourite) {
                        var message = 'Error adding movie to favourites';
                    } else {
                        var message = 'Error removing movie from favourites';
                    }
                    interface_helper.message_popup('error', message);
                    Movie.favourite = !Movie.favourite;
                    model.set(Movie);
                }
            }
        );

        $('#movies_table > tbody').children('tr').css("background-color","");
    },
    render:function (eventName) {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    mouseoverrow: function() {
        this.$el.css("background-color","#BADA55");
    },
    mouseoutrow: function() {
        this.$el.css("background-color","");
    }
});
window.MovieView = Backbone.View.extend({
    el:$("#movies_table"),
    tagname:"tr",
    events: {
        'click a.watched_link': 'watched'
    },
    template:_.template($('#tpl-movie-details').html()),
    initialize: function() {
        this.options.user.bind("change:authenticated", _.bind(this.rerender, this));
        this.model.bind("change", _.bind(this.render, this));
    },
    watched:function() {
        /*
        Watched = this.model.get('Watched');
        Watched[_.size(Watched)] = {'date_watched': '2013-09-03',
                                    'movie_id': 25, 'user_id': 1};
        */

        console.log(this.model);

        User.watched(this.model);

        //this.model.save();

        //Watched.set(Watched);
        //var Watched = this.model.get('Watched');
        //console.log(this.model);
        //Movie.set.Watched[3] = {''}
    },
    rerender:function() {
        var Movie = this.model.get('Movie');
        if($('tr#movie_'+Movie.movie_id).html()) {
            app.movieDetails(Movie.movie_id, this.element);
        }
    },
    render:function() {
        var Movie = this.model.get('Movie');
        if($('tr#movie_'+Movie.movie_id).html()) {
            $('tr#movie_'+Movie.movie_id).html('');
        }
        $('#'+Movie.imdb_id).after(this.template(this.model.toJSON(), this.options.user));
        return this;
    }
});