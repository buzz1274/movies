define(function(require, exports, module) {
    "use strict";

    var _ = require('underscore'),
        $ = require('jquery'),
        MessagePopupTemplate = require('text!templates/message_popup.html'),
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
                $('#opaque').css('display', 'block');
            } else {
                if($('div.alert').css('display') != 'block' || bypassPopupCheck) {
                    $('#opaque').css('display', 'none');
                }
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
                var message = 'An error has occurred';
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
            if(ascending) {
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
