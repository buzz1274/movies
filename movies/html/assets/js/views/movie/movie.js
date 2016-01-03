define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        Movie = require('models/movie/movie'),
        MovieDetail = require('views/movie/details'),
        ListMovieTemplate = require('text!templates/movie/td_movie.html'),
        Interface = require('helper/interface'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        tagName: "tr",
        events: {
            'click li.favourite_link': 'favourite',
            'click li.detail_link': 'details',
            'click li.delete_link': 'delete',
            'click li.edit_link': 'edit'
        },
        template:_.template(ListMovieTemplate),
        initialize: function() {
            this.Movie = this.model.get('Movie');
            _.bindAll(this, "render");
            user.bind("change:authenticated", this.render);
            this.model.bind('change', this.render);
        },
        attributes: function() {
            var Movie = this.model.get('Movie');
            return {id: Movie.imdb_id,
                    class:"movie_summary"};
        },
        details: function () {
            if ($('tr#movie_' + this.Movie.movie_id).html()) {
                this.close_details(this.Movie.movie_id);
            } else {
                Interface.loadingImage(true);

                var movie_summary = this.model,
                    movie = new Movie();

                movie.fetch({
                    url: '/movies/' + this.Movie.movie_id + '/',
                    async: true,
                    success: function () {
                        var m = movie.get('Movie'),
                            element = 'movie_' + m.movie_id;

                        $('tr#' + m.imdb_id).after('<tr id="' + element + '"></tr>');

                        var movieDetail = new MovieDetail(
                                   {model: movie,
                                    movie_summary: movie_summary,
                                    el: '#' + element});

                        movieDetail.render();
                        Interface.loadingImage(false);
                    },
                    error: function () {
                        Interface.loadingImage(false);
                        Interface.messagePopup('error',
                            'An error occurred retrieving the movie details');
                    }
                });
            }
        },
        close_details: function (movie_id) {
            if ($('tr#movie_' + movie_id).html()) {
                $('tr#movie_' + movie_id).remove();
            }
        },
        favourite:function() {
            this.close_details(this.model.get('Movie').movie_id);
            user.favourite(this.model);
        },
        edit: function () {
            this.model.edit();
        },
        delete: function () {
            this.close_details(this.model.get('Movie').movie_id);
            this.model.delete(this);
        },
        render:function () {
            this.$el.html(this.template({'Movie': this.model.toJSON().Movie,
                                         user: user.toJSON()}));
            return this;
        }
    });
});