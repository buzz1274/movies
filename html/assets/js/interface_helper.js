var interface_helper = {
    loadingImage: function(on) {
        if(on) {
            this.opaque(on);
            $('#loading').css('display', 'block');
        } else {
           this.opaque(on);
            $('#loading').css('display', 'none');
        }
    },
    opaque: function(on, bypass_popup_check) {
        if(on) {
            $('#opaque').css('display', 'block');
        } else {
            if($('div.alert').css('display') != 'block' || bypass_popup_check) {
                $('#opaque').css('display', 'none');
            }
        }
    },
    message_popup: function(type, message) {
        if(typeof type == 'undefined' || !type ||
           (type != 'error' && type != 'success')) {
            type = 'error'
        }
        if(typeof message == 'undefined' || !message) {
            var message = 'An error has occurred';
        }
        var template = _.template($('#tpl-message').html());
        $('.message_popup_container').html(template({type: type,
                                                     message: message}));
        this.opaque(true);
    }
}