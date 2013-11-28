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
        tagName:"tr",
        events: {
            'click li.favourite_link': 'favourite',
            'click li.queue_download_link': 'download',
            'click li.detail_link': 'details'
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
        details:function() {
            if($('tr#movie_'+this.Movie.movie_id).html()) {
                $('tr#movie_'+this.Movie.movie_id).remove();
            } else {
                Interface.loadingImage(true);

                var movie_summary = this.model,
                    movie = new Movie();

                movie.fetch({
                    url:'/movies/'+this.Movie.movie_id+'/',
                    async:true,
                    success: function() {
                        var m = movie.get('Movie');
                        var element = 'movie_'+m.movie_id;

                        $('tr#'+m.imdb_id).after('<tr id="'+element+'"></tr>');
                        var movieDetail = new MovieDetail({model:movie,
                                                           movie_summary: movie_summary,
                                                           el:'#'+element});
                        movieDetail.render();
                        Interface.loadingImage(false);
                    }
                });
            }
        },
        favourite:function() {
            user.favourite(this.model);
        },
        download:function() {
            user.download(this.model.get('Movie').movie_id);
        },
        render:function () {
            this.$el.html(this.template({'Movie': this.model.toJSON().Movie,
                                         user: user.toJSON()}));
            return this;
        }
    });
});