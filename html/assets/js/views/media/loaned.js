define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('jquery'),
        _ = require('underscore'),
        ListMovieDownloadedTemplate = require('text!templates/media/td_loaned.html'),
        user = require('models/user/user');

    module.exports = Backbone.View.extend({
        tagName:"tr",
        template:_.template(ListMovieDownloadedTemplate),
        events: {},
        initialize:function() {
            this.model.bind('change', this.render, this);
        },
        render:function() {
            this.$el.html(this.template({loaned: this.model.toJSON(),
                                         user: user.toJSON()}));
            return this;
        }
    });
});