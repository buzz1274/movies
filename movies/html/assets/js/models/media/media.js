define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        Interface = require('helper/interface');

    module.exports = Backbone.Model.extend({
        url:'/media/',
        idAttribute: "media_id",
        loaned: function(movie_id, media_id, loaned_to, media_loaned_id) {
            var result = null;

            this.save({media_id: media_id,
                       media_loaned_id: media_loaned_id,
                       loaned_to: loaned_to},
                      {url:'/media/loaned',
                       async:false,
                       success: function(data) {
                            Interface.loanedPopup(movie_id, false);
                            if(media_loaned_id) {
                                Interface.messagePopup('success', 'Movie Returned');
                            } else {
                                Interface.messagePopup('success', 'Movie loaned');
                            }
                            result = data.get('MediaLoaned');
                       },
                       error: function(data, response) {
                            if(response.responseText) {
                                var message = JSON.parse(response.responseText);

                                if(response.status == 409) {
                                    Interface.messagePopup('error', message.message);
                                    Interface.loanedPopup(movie_id, false, null);
                                } else {
                                    Interface.loanedPopup(movie_id, true, message.message);
                                }
                            } else {
                                Interface.loanedPopup(movie_id, false, null);
                                if(media_loaned_id) {
                                    Interface.messagePopup('error',
                                        'An error occurred marking movie as returned');
                                } else {
                                    Interface.messagePopup('error',
                                        'An error occurred marking movie as loaned');
                                }
                            }
                            result = false;
                       }
            });
            return result;
        }
    });
});