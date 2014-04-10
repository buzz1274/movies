define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        $ = require('jquery'),
        MessagePopupTemplate = require('text!templates/message_popup.html'),
        LoginTemplate = require('text!templates/login.html'),
        LoanedTemplate = require('text!templates/movie/loaned.html');

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
                if($('div.alert').css('display') != 'block' || bypassPopupCheck) {
                    $('#navbar').css('z-index', 1050);
                    $('#opaque').css('display', 'none');
                }
            }
        },
        loanedPopup: function(movie_id, display, message) {
            if(display) {
                var template = _.template(LoanedTemplate);
                this.opaque(true);
                $('#loaned_popup_'+movie_id).append(template({message: message}));

                $('#loaned_popup_'+movie_id).css({'position': 'fixed',
                                                  'z-index':2000,
                                                  'left': '0px',
                                                  'top': '0px'});

            } else {
                $('#loaned_popup_'+movie_id).empty();
                this.opaque(false);
            }
        },
        loginPopup:function(display, message) {
            if(display) {
                var template = _.template(LoginTemplate);
                this.opaque(true);
                $('#username').val('');
                $('#password').val('');
                $('#login_popup').append(template({message: message}));
            } else {
                $('#login_popup').html('');
                this.opaque(false);
            }
        },
        messagePopup: function(type, message) {
            if(typeof type == 'undefined' || !type ||
               (type != 'error' && type != 'success')) {
                type = 'error'
            }
            if(typeof message == 'undefined' || !message) {
                message = 'An error has occurred';
            }
            var template = _.template(MessagePopupTemplate),
                viewportHeight = $(window).height(),
                viewportWidth = $(window).width();

            $('.message_popup_container').html(template({type: type,
                                                         message: message}));

            $('.message_popup_container').css({'position': 'fixed',
                                               'left': ((viewportWidth/2) - 100) + 'px',
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