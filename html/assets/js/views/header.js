define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        Backbone = require('backbone'),
        $ = require('bootstrap'),
        HeaderTemplate = require('text!templates/header.html'),
        Interface = require('helper/interface');

    module.exports = Backbone.View.extend({
        el:$("#navbar"),
        template:_.template(HeaderTemplate),
        events: {
            'click span#logout_link': 'authenticate',
            //'click #login_button': 'authenticate',
            'keypress #username': 'authenticate',
            'keypress #password': 'authenticate',
            'click span#login_popup_link': 'loginPopup',
            'click #cancel_button': 'loginPopup'
        },
        initialize: function() {
            _.bindAll(this, "eventCatcher");
            this.model.bind("change:authenticated", this.render, this);
            this.render();
            return this;
        },
        eventCatcher: function() {
            alert("HERE");
        },
        render:function() {
            //$("#login_button").click(this.authenticate);
            $('#login_button').on('click', this.authenticate);
            $(this.el).html('').append(this.template(this.model.toJSON()));
        },
        loginPopup:function(ev) {
            Interface.loginPopup((ev.target.id == 'login_popup_link'))
        },
        authenticate:function(ev) {
            console.log("HERE")
            if(typeof(ev.keyCode) != 'undefined' && ev.keyCode != 13) {
                return;
            } else if(ev.target.id == 'login_button' || ev.keyCode == 13) {
                var action = 'login',
                    username = $('#username').val(),
                    password =  $('#password').val();
            } else {
                var action = 'logout',
                    username = null,
                    password = null;
            }
            this.model.authenticate(action, username, password);
        }
    });
});