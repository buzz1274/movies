define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        MovieDetailsTemplate = require('text!templates/movie/details.html'),
        Interface = require('helper/interface'),
        State = require('helper/state'),
        summary = require('models/movie/summary'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        template:_.template(MovieDetailsTemplate),
        events: {
            'click a.watched_link': 'watched',
            'click a.unwatched_link': 'watched',
            'click span.genre_link': 'genreSearch',
            'click span.keyword_link': 'keywordSearch',
            'click span.director_link': 'personSearch',
            'click span.actor_link': 'personSearch',
            'click span.media_link': 'mediaSearch',
            'click span.show-all-link': 'showAll'
        },
        initialize: function() {
            this.el = this.options.el;
            this.movie_summary = this.options.movie_summary;
            this.model.bind("change", _.bind(this.render, this));
        },
        watched:function(ev) {
            user.watched(this.model, this.movie_summary,
                         $(ev.target).parent().attr('data-watched-id'));
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
        keywordSearch:function(ev) {
            State.reset(true);
            State.setStateParams('kid', $(ev.currentTarget).attr('data-keyword_id'));
            State.setStateParams('search', $(ev.currentTarget).attr('data-keyword'));
            State.setStateParams('search_type', 'keyword');
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        personSearch:function (ev) {
            State.reset(true);
            State.setStateParams('pid', $(ev.currentTarget).attr('data-person_id'));
            State.setStateParams('search', $(ev.currentTarget).attr('data-person_name'));
            State.setStateParams('search_type', 'cast');
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        mediaSearch: function(ev) {
            State.reset(true);
            State.setStateParams('mid', $(ev.currentTarget).attr('data-media_id'));
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        genreSearch:function (ev) {
            State.reset(true);
            State.setStateParams('gid', $(ev.currentTarget).attr('data-genre_id'));
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        },
        render:function() {
            $(this.el).html(this.template({movie:this.model.toJSON(),
                                           user: user.toJSON()}));
            return this;
        }
    });
});