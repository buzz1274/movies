define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        Interface = require('helper/interface');

    module.exports = Backbone.Model.extend({
        url:'/movies/',
        idAttribute: "movie_id",
        loaned: function(loaned_to) {
            this.save({media_id: this.get('Movie').media_id,
                       loaned_to: loaned_to},
                {url:'/media/loaned',
                    success: function() {
                        Interface.messagePopup('success', 'Movie marked as loaned');
                    },
                    error: function() {
                        Interface.messagePopup('error', 'Error adding movie to loaned');
                    }
                }
            );
        }
    });
});