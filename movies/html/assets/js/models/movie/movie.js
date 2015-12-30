define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        Interface = require("helper/interface");

    module.exports = Backbone.Model.extend({
        url:'/movies/',
        idAttribute: "movie_id",
        add: function(imdb_id, provider, hd) {
            var data = {movie_id: false,
                        imdb_id: imdb_id,
                        provider: provider,
                        hd: hd}

            this.save(data,
                {url: '/movie/add/',
                 success: function() {
                    Interface.messagePopup('success',
                        'Movie queued for scraping');
                 },
                 error: function(d, response) {
                     if(response.status === 400 && response.responseText) {
                        var message = $.parseJSON(response.responseText)[0];

                        Interface.addMoviePopup(true, message, data);
                     } else {
                        Interface.messagePopup('error',
                             'An error occurred whilst adding a movie');
                     }
                 }
                }
            );
        },
        delete: function (callback) {
            var movie = this.get('Movie'),
                that = this;

            this.save({movie_id: movie.movie_id},
                {url: '/movie/delete/'+movie.movie_id+'/',
                    success: function() {
                        that.destroy();
                        callback(1);
                    },
                    error: function() {
                        callback(1)
                        Interface.messagePopup('error',
                            'An error occurred whilst deleting the movie');
                    }
                }
            );
        }
    });
});