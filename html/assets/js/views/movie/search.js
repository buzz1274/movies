define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        $ = require('jquery_ui'),
        Backbone = require('backbone'),
        State = require('helper/state'),
        Helper = require('helper/helper'),
        MovieSearchTemplate = require('text!templates/movie/search.html'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        el:"#movies_search",
        tagName:"div",
        template:_.template(MovieSearchTemplate),
        events: {
            'click #submitButton': 'search',
            'click #luckyButton': 'lucky',
            'click #resetButton': 'reset',
            'keypress #search_input': 'autoComplete',
            'focus #search_input': 'autoComplete',
            'click #download': 'download'
        },
        initialize: function() {
            this.user = user;

            _.bindAll(this, 'render');
            this.model.on('change',this.render, this);
            this.user.bind("change:authenticated", this.render);

            if(typeof this.model != 'undefined') {
                this.model.bind("change:watched", this.render);
                this.model.bind("change:favourites", this.render);
            }
        },
        render:function () {
            if(this.model) {
                var summary = this.model.toJSON();
            }

            State.setSliderValues(false);

            if(typeof summary.totalMovies == "number" &&
               summary.totalMovies != 0 && summary.totalMovies != null) {
                State.setSliderValues(summary);
            }

            $(this.el).empty().append(this.template({summary: summary,
                                                     user: this.user.toJSON()}));

            this.renderSlider('imdb_rating');
            this.renderSlider('runtime');
            this.renderSlider('release_year');

            this.fillForm();
        },
        search:function() {
            State.reset(false);
            State.populateWithSearchFormValues();
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        lucky:function() {
            var StateParams = State.getState().Params;
            var current_movie_id = StateParams.id;

            State.reset(false);
            State.populateWithSearchFormValues();
            StateParams = State.getState().Params;

            $.ajax({
                url:'/movies/lucky/',
                data:StateParams,
                dataType: "json",
                async:false,
                success:function(data) {
                    if(current_movie_id != data['movieID']) {
                        Backbone.history.navigate('/id='+data['movieID'],
                                                  {'trigger':true});
                    } else {
                        //FIX ME BUG -- #294
                        window.location.assign('/#id='+data['movieID']+"&"+
                                               State.constructQueryString());
                    }
                },
                error: function() {
                    Backbone.history.navigate(State.constructQueryString(),
                                              {'trigger':true});
                }
            });
        },
        reset:function() {
            State.reset(true);
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        download:function() {
            window.location = "/movies/csv?"+
                window.location.hash.slice(1, window.location.hash.length);
        },
        autoComplete:function(ev) {
            if(ev.keyCode == 13) {
                this.search(ev, false);
            } else {
                var search = $('#search_input').val() + String.fromCharCode(ev.keyCode);

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
                                                Backbone.history.navigate(
                                                    '#search_type='+result.search_type+
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
        fillForm:function() {
            var StateParams = State.getState();

            if(StateParams.Params.search) {
                $('#search_input').val(decodeURIComponent(StateParams.Params.search));
            }
            if(StateParams.Params.watched == 1 || StateParams.Params.watched == 0) {
                $('#watched_'+StateParams.Params.watched).attr('checked', 'checked');
            }
            if(StateParams.Params.favourites == 1 || StateParams.Params.favourites == 0) {
                $('#favourites_'+StateParams.Params.favourites).attr('checked', 'checked');
            }
            if(StateParams.Params.search_type == 'keyword' ||
                StateParams.Params.search_type == 'cast' ||
                StateParams.Params.search_type == 'title') {
                $('#search_type_'+StateParams.Params.search_type).attr('checked', 'checked');
            }
            if(StateParams.Params.hd == 1 || StateParams.Params.hd == 0) {
                $('#hd_'+StateParams.Params.hd).attr('checked', 'checked');
            }
            if(StateParams.Params.gid) {
                StateParams.Params.gid.split(',').forEach(function(gid) {
                    $('input[name="genre[]"][value='+gid+']').attr("checked",true);
                });
            }
            if(StateParams.Params.cid) {
                StateParams.Params.cid.split(',').forEach(function(cid) {
                    $('input[name="certificate[]"][value='+cid+']').attr("checked",true);
                });
            }
            _.each(StateParams.SliderValues, function(value, key) {
                if(StateParams.SliderValues[key].active && StateParams.Params[key]) {
                    var values = StateParams.Params[key].split(',');
                    if(values[0] && values[1]) {
                        var data = {};
                        data['min_'+key] = values[0];
                        data['max_'+key] = values[1];
                        State.setSliderValues(data);
                    }
                }
             });
        },
        renderSlider:function(id) {
            var StateParams = State.getState();
            $(function() {
                $("#"+id+"_slider_range").slider({
                    range: true,
                    min: StateParams.SliderValues[id].min,
                    max: StateParams.SliderValues[id].max,
                    values: [StateParams.SliderValues[id].current_min,
                             StateParams.SliderValues[id].current_max],
                    slide: function(ev, ui) {
                        var id = $(ev.target).parent().attr('id'),
                            data = {};

                        data['min_'+id] = ui.values[0];
                        data['max_'+id] = ui.values[1];
                        data['active_'+id] = true;

                        State.setSliderValues(data);

                        if(id == 'runtime') {
                            var start = Helper.convertToTime(ui.values[0]);
                            var end = Helper.convertToTime(ui.values[1]);
                        } else {
                            var start = ui.values[0];
                            var end = ui.values[1];
                        }
                        $("#"+id+"_label").html(start+" - "+end);
                    }
                });
                if(id == 'runtime') {
                    var start = Helper.convertToTime(
                                   $("#"+id+"_slider_range").slider("values",0));
                    var end = Helper.convertToTime(
                                   $("#"+id+"_slider_range").slider("values",1));
                } else {
                    var start = $("#"+id+"_slider_range").slider("values",0);
                    var end = $("#"+id+"_slider_range").slider("values",1);
                }
                $("#"+id+"_label").html(start + " - " + end);
            });
        }
    });
});