define(function(require, exports, module) {
    "use strict";

    var State = require('helper/state');

    module.exports =  {
        convertToTime:function(minutes) {
            if(minutes < 1 || !parseInt(minutes, 10)) {
                return false;
            }

            if(minutes < 60) {
                return minutes + "mins";
            }

            var time = parseInt((minutes / 60), 10);

            if(time === 1) {
                time += "hr";
            } else {
                time += "hrs";
            }

            if(minutes % 60 > 1) {
                time += " " + (minutes % 60) + "mins";
            }

            return time;

        },
        removePageFromQueryString:function() {
            var qs = State.getQueryString().replace(/&?p=[0-9]+&?/gm, '');
            qs = qs.replace(/&+/gm, '&');
            if(qs.length > 0 && qs[(qs.length - 1)] !== '&') {
                qs = qs+'&';
            }
            return qs;
        }
    };
});
