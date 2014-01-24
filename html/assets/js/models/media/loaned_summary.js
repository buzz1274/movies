define(function(require) {
    "use strict";

    var Backbone = require('backbone');

    var LoanedSummary = Backbone.Model.extend({
        url:'/media/loaned/summary'
    });

    return new LoanedSummary();

});