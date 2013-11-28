define(function(require) {
    "use strict";

    var _ = require('underscore');
    var State = {
        qs:'',
        Params: {
            'p':null,
            's':null,
            'asc':null,
            'pid':null,
            'gid':null,
            'kid':null,
            'mid':null,
            'hd':null,
            'cid':null,
            'search':null,
            'search_type':null,
            'imdb_rating':null,
            'watched':null,
            'runtime':'',
            'favourites':null,
            'release_year':'',
            'id':null
        },
        DefaultParams: {
            'p':1,
            's':'title',
            'asc':1,
            'gid':0,
            'pid':0,
            'kid':0,
            'mid':0,
            'cid':0,
            'hd':'all',
            'favourites':'all',
            'watched':'all',
            'search_type':'all',
            'search':'',
            'imdb_rating':'',
            'runtime':'',
            'release_year':'',
            'id':0
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
            }
        }
    };

    return {
        getState:function() {return State;},
        setStateParams:function(key, value) {
            State.Params[key] = value;
        },
        reset:function(reset_sliders) {
            State.qs = '';
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
        },
        populateWithQueryStringValues:function(query_string) {
            if(query_string == undefined || !query_string) {
                this.reset(true);
            } else {
                State.qs = query_string;
                var page_in_params = false;
                query_string.split('&').forEach(function(argument) {
                    if(argument) {
                        var fragment = argument.split('=');
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
                            State.Params[fragment[0]] = fragment[1];
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
        populateWithSearchFormValues:function() {
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
                    if(State.SliderValues[key].current_min == State.SliderValues[key].min &&
                       State.SliderValues[key].current_max == State.SliderValues[key].max) {
                        State.SliderValues[key].active = false;
                        State.Params[key] = State.DefaultParams[key];
                    } else {
                        State.Params[key] = State.SliderValues[key].current_min+","+
                                            State.SliderValues[key].current_max;
                    }
                }
            });
        },
        constructQueryString:function() {
            var qs = '';
            _.each(State.DefaultParams, function(value, key) {
                if((State.Params[key] != State.DefaultParams[key]) &&
                    (key != 'asc' && key != 's' && key != 'imdb_rating' &&
                     key != 'runtime' && key != 'release_year')) {
                    var param = false;
                    if(key == 'search') {
                        param = encodeURIComponent(State.Params[key].toString());
                    } else if(typeof State.Params[key] != 'undefined' &&
                        State.Params[key] != null) {
                        param = State.Params[key].toString();
                    }
                    if(param) {
                        qs += key+'='+param+"&";
                    }
                }
                if(key == 'imdb_rating' || key == 'release_year' ||
                    key == 'runtime') {
                    var values = State.Params[key].split(',');
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
            State.qs = qs.slice(0, -1);

            return State.qs;
        },
        getQueryString:function() {
            return State.qs;
        },
        setSliderValues:function(data) {
            if(!data) {
                var data = $('#slider_init_values').html();

                State.SliderValues.imdb_rating.min = Math.floor($('section').data('min-imdb-rating'));
                State.SliderValues.imdb_rating.max = Math.ceil($('section').data('max-imdb-rating'));
                State.SliderValues.runtime.min = $('section').data('min-runtime');
                State.SliderValues.runtime.max = $('section').data('max-runtime');
                State.SliderValues.release_year.min = $('section').data('min-release-year');
                State.SliderValues.release_year.max = $('section').data('max-release-year');
            } else {
                _.each(State.SliderValues, function(value, key) {
                    if(data['min_'+key] && data['max_'+key]) {
                        State.SliderValues[key].current_min = Math.floor(data['min_'+key]);
                        State.SliderValues[key].current_max = Math.ceil(data['max_'+key]);
                        if(data['active_'+key]) {
                            State.SliderValues[key].active = true;
                        }
                    }
                });
            }
        }
    }
});