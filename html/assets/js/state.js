var State = {
    qs:'',
    Params: {
        'p':null,
        's':null,
        'asc':null,
        'pid':null,
        'gid':null,
        'kid':null,
        'hd':null,
        'cid':null,
        'search':null,
        'search_type':null,
        'imdb_rating':null,
        'watched':null,
        'runtime':'',
        'favourites':null,
        'release_year':'',
        'lucky':null
    },
    DefaultParams: {
        'p':1,
        's':'title',
        'asc':1,
        'gid':0,
        'pid':0,
        'kid':0,
        'cid':0,
        'hd':'all',
        'favourites':'all',
        'watched':'all',
        'search_type':'all',
        'search':'',
        'imdb_rating':'',
        'runtime':'',
        'release_year':'',
        'lucky':0
    },
    SortDefaults: {
        'title': 1,
        'release_year': 0,
        'imdb_rating': 0,
        'runtime': 0,
        'filesize': 0,
        'date_added': 0,
        'hd': 1,
        'watched': 1,
        'cert':1
    },
    SliderValues: {
        imdb_rating: {
            active:false,
            min:false,
            max:false,
            current_min:false,
            current_max:false
        },
        runtime: {
            active:false,
            min:false,
            max:false,
            current_min:false,
            current_max:false
        },
        release_year: {
            active:false,
            min:false,
            max:false,
            current_min:false,
            current_max:false
        },
        init:function() {
            var data = $('#slider_init_values').html();
            this.imdb_rating.min = Math.floor($('section').data('min-imdb-rating'));
            this.imdb_rating.max = Math.ceil($('section').data('max-imdb-rating'));
            this.runtime.min = $('section').data('min-runtime');
            this.runtime.max = $('section').data('max-runtime');
            this.release_year.min = $('section').data('min-release-year');
            this.release_year.max = $('section').data('max-release-year');
        }
    },
    parse:function(query_string) {
        if(query_string == undefined || !query_string) {
            State.reset(true);
        } else {
            this.qs = query_string;
            var page_in_params = false;
            query_string.split('&').forEach(function(argument) {
                if(argument) {
                    fragment = argument.split('=');
                    if(fragment[0] == 'gid' || fragment[0] == 'cid') {
                        State.Params[fragment[0]] = "";
                        fragment[1].split(',').forEach(function(id) {
                            if(State.Params[fragment[0]] == "") {
                                State.Params[fragment[0]] += id;
                            } else {
                                State.Params[fragment[0]] += "," + id;
                            }
                        });
                    } else if(fragment[0] == 'id') {
                        //console.log("OPEN MOVIE PANE");
                    } else if (fragment[0] == 'search') {
                        State.Params['search'] = decodeURIComponent(fragment[1]);
                    } else {
                        State.Params[fragment[0]] = fragment[1];
                    }
                    if(fragment[0] == 'p') {
                        page_in_params = true;
                    }
                }
            });
            if(!page_in_params) {
                State.Params.p = 1;
            }
        }
    },
    fill_form:function() {
        if(State.Params.search) {
            $('#search_input').val(decodeURIComponent(State.Params.search));
        }
        if(State.Params.watched == 1 || State.Params.watched == 0) {
            $('#watched_'+State.Params.watched).attr('checked', 'checked');
        }
        if(State.Params.favourites == 1 || State.Params.favourites == 0) {
            $('#favourites_'+State.Params.favourites).attr('checked', 'checked');
        }
        if(State.Params.search_type == 'keyword' ||
            State.Params.search_type == 'cast' ||
            State.Params.search_type == 'title') {
            $('#search_type_'+State.Params.search_type).attr('checked', 'checked');
        }
        if(State.Params.hd == 1 || State.Params.hd == 0) {
            $('#hd_'+State.Params.hd).attr('checked', 'checked');
        }
        if(State.Params.gid) {
            State.Params.gid.split(',').forEach(function(gid) {
                $('input[name="genre[]"][value='+gid+']').attr("checked",true);
            });
        }
        if(State.Params.cid) {
            State.Params.cid.split(',').forEach(function(cid) {
                $('input[name="certificate[]"][value='+cid+']').attr("checked",true);
            });
        }
        _.each(State.SliderValues, function(value, key) {
            if(State.SliderValues[key].active && State.Params[key]) {
                values = State.Params[key].split(',');
                if(values[0] && values[1]) {
                    var movieSearchView = new MovieSearchView({model: null, user:User});
                    State.SliderValues[key].current_min = values[0];
                    State.SliderValues[key].current_max = values[1];
                    movieSearchView.render_slider(key);
                }
            }
        });
    },
    query_string:function() {
        var qs = '';
        _.each(State.DefaultParams, function(value, key) {
            if((State.Params[key] != State.DefaultParams[key]) &&
                (key != 'asc' && key != 's' && key != 'imdb_rating' &&
                    key != 'runtime' && key != 'release_year')) {
                var param = false;
                if(key == 'search') {
                    param = encodeURIComponent(State.Params[key].toString());
                } else if(typeof State.Params[key] != 'undefined') {
                    param = State.Params[key].toString();
                }
                if(param) {
                    qs += key+'='+param+"&";
                }
            }
            if(key == 'imdb_rating' || key == 'release_year' ||
                key == 'runtime') {
                values = State.Params[key].split(',');
                if(values[0] && values[1] &&
                    (State.SliderValues[key].active)) {
                    qs += key+'='+State.Params[key]+"&";
                }
            }
        });
        if(!(State.Params['s'] == 'title' && State.Params['asc'])) {
            qs += 's='+State.Params['s']+'&asc='+
                State.Params['asc']+'&';
        }
        this.qs = qs.slice(0, -1);

        return this.qs;
    },
    remove_page_from_query_string:function() {
        qs = this.qs.replace(/&?p=[0-9]{1,}&?/gm, '');
        return qs.length ? '&'+qs : '';
    },
    parse_search_form:function() {
        State.Params.gid = "";
        State.Params.cid = "";
        State.Params.search = $('#search_input').val();
        State.Params.watched = $('input:radio[name=watched]:checked').val();
        State.Params.search_type = $('input:radio[name=search_type]:checked').val();
        State.Params.hd = $('input:radio[name=hd]:checked').val();
        State.Params.favourites = $('input:radio[name=favourites]:checked').val();
        $('input:checkbox[name="genre[]"]:checked').each(function() {
            if(State.Params.gid == "") {
                State.Params.gid += $(this).val();
            } else {
                State.Params.gid += "," + $(this).val();
            }
        });
        $('input:checkbox[name="certificate[]"]:checked').each(function() {
            if(State.Params.cid == "") {
                State.Params.cid += $(this).val();
            } else {
                State.Params.cid += "," + $(this).val();
            }
        });
        _.each(State.SliderValues, function(value, key) {
            if(typeof State.SliderValues[key] == 'object' &&
                State.SliderValues[key].active) {
                State.Params[key] =
                    State.SliderValues[key].current_min+","+
                        State.SliderValues[key].current_max;
            }
        });
    },
    reset:function(reset_sliders) {
        _.each(State.DefaultParams, function(value, key) {
            State.Params[key] = value;
        });
        if(reset_sliders) {
            _.each(State.SliderValues, function(value, key) {
                if(typeof State.SliderValues[key] == 'object') {
                    State.SliderValues[key].active = false;
                }
            });
        }
    }
};