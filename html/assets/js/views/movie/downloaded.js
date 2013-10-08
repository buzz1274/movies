define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        ListMovieDownloadedTemplate = require('text!templates/movie/td_downloaded.html'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        tagName:"tr",
        template:_.template(ListMovieDownloadedTemplate),
        events: {
            'click a#cancel_download_link': 'download'
        },
        initialize:function() {
            this.model.bind('change', this.render, this);
        },
        download:function() {
            var downloadItem = this.model.toJSON();
            user.download(downloadItem.movie_id, downloadItem.download_id);
            this.model.set('status', 'Cancelled');
        },
        render:function() {
            this.$el.html(this.template({Movie: this.model.toJSON(),
                                         user: user.toJSON()}));
            return this;
        }
    });
});