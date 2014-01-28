define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone');

    var MovieSummary = Backbone.Model.extend({
        url:'/movies/summary/'
    });

    return new MovieSummary();

});