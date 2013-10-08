define(function(require, exports, module) {
    "use strict";

    var State = require('helper/state');

    module.exports =  {
        convertToTime:function(minutes) {
            if(minutes < 1 || !parseInt(minutes)) {
                return false;
            } else if(minutes < 60) {
                return minutes + "mins";
            } else {
                var time = parseInt(minutes / 60);
                if(time == 1) {
                    time += "hr"
                } else {
                    time += "hrs"
                }
                if(minutes % 60) {
                    time += " " + (minutes % 60) + "mins";
                }
                return time;
            }
        },
        removePageFromQueryString:function() {
            var qs = State.getQueryString().replace(/&?p=[0-9]{1,}&?/gm, '');
            qs = qs.replace(/&{1,}/gm, '&');
            if(qs[0] == '&') {
                qs = qs.slice(0);
            }
            return qs.length ? '&'+qs : '';
        }
    }
});
