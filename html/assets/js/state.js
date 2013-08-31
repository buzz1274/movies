var UrlParams = {
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
        },
        convert_to_time:function(minutes) {
            if(minutes < 60) {
                return minutes + "mins";
            } else {
                time = parseInt(minutes / 60);
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
        }
    },
    parse:function(query_string) {
        if(query_string == undefined || !query_string) {
            UrlParams.reset(true);
        } else {
            this.qs = query_string;
            var page_in_params = false;
            query_string.split('&').forEach(function(argument) {
                if(argument) {
                    fragment = argument.split('=');
                    if(fragment[0] == 'gid' || fragment[0] == 'cid') {
                        UrlParams.Params[fragment[0]] = "";
                        fragment[1].split(',').forEach(function(id) {
                            if(UrlParams.Params[fragment[0]] == "") {
                                UrlParams.Params[fragment[0]] += id;
                            } else {
                                UrlParams.Params[fragment[0]] += "," + id;
                            }
                        });
                    } else if(fragment[0] == 'id') {
                        //console.log("OPEN MOVIE PANE");
                    } else if (fragment[0] == 'search') {
                        UrlParams.Params['search'] = decodeURIComponent(fragment[1]);
                    } else {
                        UrlParams.Params[fragment[0]] = fragment[1];
                    }
                    if(fragment[0] == 'p') {
                        page_in_params = true;
                    }
                }
            });
            if(!page_in_params) {
                UrlParams.Params.p = 1;
            }
        }
    },
    fill_form:function() {
        if(UrlParams.Params.search) {
            $('#search_input').val(decodeURIComponent(UrlParams.Params.search));
        }
        if(UrlParams.Params.watched == 1 || UrlParams.Params.watched == 0) {
            $('#watched_'+UrlParams.Params.watched).attr('checked', 'checked');
        }
        if(UrlParams.Params.favourites == 1 || UrlParams.Params.favourites == 0) {
            $('#favourites_'+UrlParams.Params.favourites).attr('checked', 'checked');
        }
        if(UrlParams.Params.search_type == 'keyword' ||
            UrlParams.Params.search_type == 'cast' ||
            UrlParams.Params.search_type == 'title') {
            $('#search_type_'+UrlParams.Params.search_type).attr('checked', 'checked');
        }
        if(UrlParams.Params.hd == 1 || UrlParams.Params.hd == 0) {
            $('#hd_'+UrlParams.Params.hd).attr('checked', 'checked');
        }
        if(UrlParams.Params.gid) {
            UrlParams.Params.gid.split(',').forEach(function(gid) {
                $('input[name="genre[]"][value='+gid+']').attr("checked",true);
            });
        }
        if(UrlParams.Params.cid) {
            UrlParams.Params.cid.split(',').forEach(function(cid) {
                $('input[name="certificate[]"][value='+cid+']').attr("checked",true);
            });
        }
        _.each(UrlParams.SliderValues, function(value, key) {
            if(UrlParams.SliderValues[key].active && UrlParams.Params[key]) {
                values = UrlParams.Params[key].split(',');
                if(values[0] && values[1]) {
                    var movieSearchView = new MovieSearchView({model: null, user:User});
                    UrlParams.SliderValues[key].current_min = values[0];
                    UrlParams.SliderValues[key].current_max = values[1];
                    movieSearchView.render_slider(key);
                }
            }
        });
    },
    query_string:function() {
        var qs = '';
        _.each(UrlParams.DefaultParams, function(value, key) {
            if((UrlParams.Params[key] != UrlParams.DefaultParams[key]) &&
                (key != 'asc' && key != 's' && key != 'imdb_rating' &&
                    key != 'runtime' && key != 'release_year')) {
                var param = false;
                if(key == 'search') {
                    param = encodeURIComponent(UrlParams.Params[key].toString());
                } else if(typeof UrlParams.Params[key] != 'undefined') {
                    param = UrlParams.Params[key].toString();
                }
                if(param) {
                    qs += key+'='+param+"&";
                }
            }
            if(key == 'imdb_rating' || key == 'release_year' ||
                key == 'runtime') {
                values = UrlParams.Params[key].split(',');
                if(values[0] && values[1] &&
                    (UrlParams.SliderValues[key].active)) {
                    qs += key+'='+UrlParams.Params[key]+"&";
                }
            }
        });
        if(!(UrlParams.Params['s'] == 'title' && UrlParams.Params['asc'])) {
            qs += 's='+UrlParams.Params['s']+'&asc='+
                UrlParams.Params['asc']+'&';
        }
        this.qs = qs.slice(0, -1);

        return this.qs;
    },
    remove_page_from_query_string:function() {
        qs = this.qs.replace(/&?p=[0-9]{1,}&?/gm, '');
        return qs.length ? '&'+qs : '';
    },
    parse_search_form:function() {
        UrlParams.Params.gid = "";
        UrlParams.Params.cid = "";
        UrlParams.Params.search = $('#search_input').val();
        UrlParams.Params.watched = $('input:radio[name=watched]:checked').val();
        UrlParams.Params.search_type = $('input:radio[name=search_type]:checked').val();
        UrlParams.Params.hd = $('input:radio[name=hd]:checked').val();
        UrlParams.Params.favourites = $('input:radio[name=favourites]:checked').val();
        $('input:checkbox[name="genre[]"]:checked').each(function() {
            if(UrlParams.Params.gid == "") {
                UrlParams.Params.gid += $(this).val();
            } else {
                UrlParams.Params.gid += "," + $(this).val();
            }
        });
        $('input:checkbox[name="certificate[]"]:checked').each(function() {
            if(UrlParams.Params.cid == "") {
                UrlParams.Params.cid += $(this).val();
            } else {
                UrlParams.Params.cid += "," + $(this).val();
            }
        });
        _.each(UrlParams.SliderValues, function(value, key) {
            if(typeof UrlParams.SliderValues[key] == 'object' &&
                UrlParams.SliderValues[key].active) {
                UrlParams.Params[key] =
                    UrlParams.SliderValues[key].current_min+","+
                        UrlParams.SliderValues[key].current_max;
            }
        });
    },
    reset:function(reset_sliders) {
        _.each(UrlParams.DefaultParams, function(value, key) {
            UrlParams.Params[key] = value;
        });
        if(reset_sliders) {
            _.each(UrlParams.SliderValues, function(value, key) {
                if(typeof UrlParams.SliderValues[key] == 'object') {
                    UrlParams.SliderValues[key].active = false;
                }
            });
        }
    }
};