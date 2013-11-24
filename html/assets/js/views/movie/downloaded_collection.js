define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        DownloadedMovieView = require('views/movie/downloaded'),
        NoResultsTemplate = require('text!templates/movie/td_no_results.html'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        tagName:"tbody",
        render:function () {
            $('#movies_table > tbody').html('');
            if(this.model.models.length) {
                $('#movies_table').addClass('table-condensed');
                _.each(this.model.models, function(movie) {
                    var downloadedMovieView = new DownloadedMovieView({model:movie});
                    $(this.el).append(downloadedMovieView.render().el);
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