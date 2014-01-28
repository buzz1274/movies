"use strict";
require.config({
    baseUrl: '/assets/js/',
    paths: {
        "jquery": "http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min",
        "underscore": "//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.2.1/lodash.underscore",
        "backbone": "//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.0.0/backbone-min",
        "bootstrap": "http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min",
        "jquery_ui": "http://code.jquery.com/ui/1.9.1/jquery-ui.min",
        "text": "//cdnjs.cloudflare.com/ajax/libs/require-text/2.0.10/text",
        'QUnit': 'http://code.jquery.com/qunit/qunit-1.12.0'
    },

    shim: {
        "backbone": {
            exports: "Backbone",
            deps: ["jquery", "underscore"]
        },
        "bootstrap": {
            exports : "$",
            deps: ['jquery']
        },
        "jquery_ui": {
            exports : "$",
            deps: ['jquery']
        },
        "QUnit": {
            exports: 'QUnit',
            init: function() {
                QUnit.config.autoload = false;
                QUnit.config.autostart = false;
            }
        }
    }
});

require(['QUnit', 'tests/helper/helper_tests'],
    function(QUnit, helperTests) {
        QUnit.load();
        helperTests.helperTests();
    }
);