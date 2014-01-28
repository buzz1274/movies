define(function(require) {
    "use strict";

    var Backbone = require('backbone');

    var DownloadSummary = Backbone.Model.extend({
        url:'/user/downloaded/summary/'
    });

    return new DownloadSummary();

});