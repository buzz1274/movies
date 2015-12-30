define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        $ = require('jquery'),
        MessagePopupTemplate = require('text!templates/message_popup.html'),
        AddMovieTemplate = require('text!templates/add_movie.html'),
        LoginTemplate = require('text!templates/login.html');

    module.exports = {
        scrollTop:function() {
            $("html, body").animate({ scrollTop: 0 }, "fast");
        },
        loadingImage: function(on) {
            if(on) {
                this.opaque(on);
                $('#loading').css('display', 'block');
            } else {
               this.opaque(on);
                $('#loading').css('display', 'none');
            }
        },
        opaque: function(on, bypassPopupCheck) {
            if(on) {
                $('#navbar').css('z-index', 125);
                $('#opaque').css('display', 'block');
            } else {
                if($('div.alert').css('display') !== 'block' || bypassPopupCheck) {
                    $('#navbar').css('z-index', 1050);
                    $('#opaque').css('display', 'none');
                }
            }
        },
        loginPopup:function(display, message) {
            if(display) {
                var template = _.template(LoginTemplate);
                this.opaque(true);
                $('#login_popup').append(template({message: message}));
                $('#username').val('');
                $('#password').val('');
            } else {
                $('#login_popup').html('');
                this.opaque(false);
            }
        },
        addMoviePopup: function(display, message, data) {
            if(display) {
                var template = _.template(AddMovieTemplate);
                this.opaque(true);

                $('#login_popup').append(template({message: message}));

                if(!data) {
                    $('#add_movie_imdb_id').val('');
                    $('#add_movie_hd').val('');
                    $('#add_movie_provider').val('');
                } else {
                    $('#add_movie_imdb_id').val(data.imdb_id);
                    $('#add_movie_hd').val(data.hd);
                    $('#add_movie_provider').val(data.provider);
                }

            } else {
                $('#login_popup').html('');
                this.opaque(false);
            }

        },
        messagePopup: function(type, message) {
            if(typeof type === 'undefined' || !type ||
               (type !== 'error' && type !== 'success')) {
                type = 'error';
            }
            if(typeof message === 'undefined' || !message) {
                message = 'An error has occurred';
            }
            var template = _.template(MessagePopupTemplate),
                viewportHeight = $(window).height();

            $('.message_popup_container').html(template({type: type,
                                                         message: message}));

            $('.message_popup_container').css({'position': 'fixed',
                                               'width': '500px',
                                               'top': ((viewportHeight / 2))  + 'px'});

            this.opaque(true);
        },
        displaySortIcons: function(ascending, sort) {
            $('.sort_icon').remove();
            if(ascending == 1) {
                $("#"+sort+"_sort").prepend(
                    '<span class="sort_icon">'+
                        '<i class="icon-chevron-up" />'+
                    '</span>');
            } else {
                $("#"+sort+"_sort").prepend(
                    '<span class="sort_icon">'+
                        '<i class="icon-chevron-down" />'+
                    '</span>');
            }
        }
    }
});
