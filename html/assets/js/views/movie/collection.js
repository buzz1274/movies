define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        MovieView = require('views/movie/movie'),
        State = require('helper/state'),
        NoResultsTemplate = require('text!templates/movie/td_no_results.html'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        tagName:"tbody",
        initialize:function() {
            user.bind("change:authenticated", this.render, this);
        },
        render:function () {
            this.MovieView = MovieView;
            $('#movies_table > tbody').html('');
            if(this.model.models.length) {
                $('#movies_table').addClass('table-condensed');
                _.each(this.model.models, function (movie) {
                    var MovieView = new this.MovieView({model:movie});
                    $(this.el).append(MovieView.render().el);
                    if(State.getState().Params.id) {
                        MovieView.details();
                    }
                }, this);
            } else {
                $('#movies_table').removeClass('table-condensed');
                $(this.el).append(_.template(NoResultsTemplate));
            }

            $('#movies_table').css('display', 'block');

            return this;
        }
    });
});