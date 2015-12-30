define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        $ = require('jquery_ui'),
        Backbone = require('backbone'),
        State = require('helper/state'),
        Interface = require('helper/interface'),
        MovieHeaderTemplate = require('text!templates/movie/th_movie.html'),
        stateParams = State.getState().Params,
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        el:'#movies_table',
        tagName:"thead",
        events: {
            'click span.sort_link': 'sort',
            'click a#add_movie_popup_link': 'addMoviePopup'
        },
        initialize: function() {
            if(this.options.template === 'MovieHeaderTemplate') {
                this.template = _.template(MovieHeaderTemplate);
            }
            _.bindAll(this, 'render');
            user.bind("change:authenticated", this.render);
        },
        render:function () {
            $(this.el).html(this.template(user.toJSON()));
            Interface.displaySortIcons(stateParams.asc, stateParams.s);
        },
        addMoviePopup: function(ev) {
            ev.preventDefault();
            Interface.addMoviePopup((ev.target.id === 'add_movie_popup_link_child'));
        },
        sort:function(ev) {
            this.undelegateEvents();
            $(this.el).empty();

            if(stateParams.s === $(ev.currentTarget).attr('data-sort_order')) {
                State.setStateParams('asc',(stateParams.asc === 1 ? 0 : 1));
            } else {
                State.setStateParams('s',$(ev.currentTarget).attr('data-sort_order'));
                State.setStateParams('asc',
                    State.getState().SortDefaults[$(ev.currentTarget).attr('data-sort_order')]);
            }
            State.setStateParams('p', 1);
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        }
    });
});