define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        MovieDownloaded = require('models/movie/download')

    module.exports = Backbone.Collection.extend({
        model:MovieDownloaded,
        url:'/user/downloaded/'
    });
});