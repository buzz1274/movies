define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        MediaLoaned = require('models/media/loaned')

    module.exports = Backbone.Collection.extend({
        model:MediaLoaned,
        url:'/media/loaned/'
    });
});