define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        Movie = require('models/movie/movie')

    module.exports = Backbone.Collection.extend({
        model:Movie,
        url:'/movies/'
    });
});