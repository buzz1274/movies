define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        Backbone = require('backbone'),
        $ = require('bootstrap'),
        HeaderTemplate = require('text!templates/header.html'),
        Movie = require('models/movie/movie'),
        Interface = require('helper/interface');

    module.exports = Backbone.View.extend({
        el:$("#body"),
        template:_.template(HeaderTemplate),
        events: {
            'click span#logout_link': 'authenticate',
            'click #login_button': 'authenticate',
            'keypress #username': 'authenticate',
            'keypress #password': 'authenticate',
            'click #add_movie_button': 'addMovie',
            'keypress #imdb_id': 'addMovie',
            'click span#login_popup_link': 'loginPopup',
            'click #cancel_button': 'loginPopup'
        },
        initialize: function() {
            this.model.bind("change:authenticated", this.render, this);
            this.render();
            return this;
        },
        render:function() {
            $('#login_popup').remove();
            $('#opaque').remove();
            $('#navbar').remove();
            $(this.el).append(this.template(this.model.toJSON()));
        },
        loginPopup:function(ev) {
            ev.preventDefault();
            Interface.loginPopup((ev.target.id === 'login_popup_link'));
        },
        authenticate:function(ev) {
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
        },
        addMovie: function(ev) {
            if(ev.keyCode !== undefined && ev.keyCode !== 13) {
                return;
            }

            var movie = new Movie();

            movie.add($('#add_movie_imdb_id').val(),
                      $('#add_movie_provider').val(),
                      $("#add_movie_hd").val());

            Interface.addMoviePopup(false);

        },
    });
});