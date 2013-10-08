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
        var template = _.template($('#tpl-message').html()),
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
    search_form: function(query_string) {
        $(document).scrollTop(0);
        interface_helper.loadingImage(true);

        if(query_string) {
            State.parse(query_string);
        } else {
            State.reset();
        }

        var movieSummary = new MovieSummary();
        var movieSearchView = new MovieSearchView({model:movieSummary, user:User});

        movieSummary.fetch({
            data:State.Params,
            success: function() {
                $('#movies_search').empty().prepend(movieSearchView.render().el);
                State.SliderValues.init();

                movieSearchView.render_slider('imdb_rating');
                movieSearchView.render_slider('runtime');
                movieSearchView.render_slider('release_year');

                State.fill_form();
            },
            error:function() {
                console.log("ERROR GETTING SUMMARY");
                //display error message
            }
        })

        return movieSummary;
    }
}
