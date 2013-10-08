window.HeaderView = Backbone.View.extend({
    el:$("#navbar"),
    template:_.template($('#tpl-navbar').html()),
    events: {
        'click span#logout_link': 'authenticate',
        'click #loginButton': 'authenticate',
        'keypress #username': 'authenticate',
        'keypress #password': 'authenticate',
        'click span#login_popup_link': 'login_popup',
        'click #cancelButton': 'login_popup'
    },
    initialize: function() {
        this.model.bind("change:authenticated", this.render, this);
        this.render();
        return this;
    },
    render:function() {
        $(this.el).html('').append(this.template(this.model.toJSON()));
    },
    login_popup:function(e) {
        if(!e || e.target.id == 'login_popup_link') {
            interface_helper.opaque(true);
            $('#login_popup').append(_.template($('#tpl-login').html()));
        } else {
            $('#login_popup').html('');
            interface_helper.opaque(false);
        }
    },
    authenticate:function(e) {
        this.model.authenticate(e, this);
    }
});
window.UserDownloadedHeaderView = Backbone.View.extend({
    tagName:"thead",
    template:_.template($('#tpl-movie-downloaded-header').html()),
    events: {
        //'click span.sort_link': 'sort'
    },
    initialize: function() {
        _.bindAll(this, 'render');
    },
    render:function () {
        $(this.el).html(this.template());
        return this;
    },
    sort:function(ev) {
        /*
        if(State.Params.s == $(ev.currentTarget).attr('data-sort_order')) {
            State.Params.asc = State.Params.asc == 1 ? 0 : 1;
        } else {
            State.Params.s = $(ev.currentTarget).attr('data-sort_order');
            State.Params.asc = State.SortDefaults[State.Params.s];
        }
        State.Params.p = 1;
        app.navigate(State.query_string(), {'trigger':true});
        */
    },
    display_sort_icons:function() {
        /*
        $('.sort_icon').remove();
        if(State.Params.asc == 1) {
            $("#"+State.Params.s+"_sort").prepend(
                '<span class="sort_icon">'+
                    '<i class="icon-chevron-up"></i></span>');
        } else {
            $("#"+State.Params.s+"_sort").prepend(
                '<span class="sort_icon">'+
                    '<i class="icon-chevron-down"></i></span>');
        }
        */
    }
});
window.UserDownloadedView = Backbone.View.extend({
    tagName:"tbody",
    render:function () {
        $('#movies_table > tbody').html('');
        if(this.model.models.length) {
            $('#movies_table').addClass('table-condensed');
            _.each(this.model.models, function (movie) {
                var userDownloadedItemView =
                        new window.UserDownloadedItemView({model:movie,
                                                           user: this.options.user});
                $(this.el).append(userDownloadedItemView.render().el);
            }, this);
        } else {
            $('#movies_table').removeClass('table-condensed');
            $(this.el).append(_.template($('#tpl-movie-list-no-results').html()));
        }
        return this;
    }
});
window.UserDownloadedItemView = Backbone.View.extend({
    tagName:"tr",
    template:_.template($('#tpl-movie-downloaded').html()),
    events: {
        'mouseover': 'mouseoverrow',
        'mouseout': 'mouseoutrow',
        'click a#cancel_download_link': 'download'
    },
    initialize:function() {
        this.user = this.options.user;
        this.model.bind('change', this.render, this);
    },
    download:function() {
        var downloadItem = this.model.toJSON();
        User.download(downloadItem.movie_id, downloadItem.download_id);
        this.model.set('status', 'Cancelled');
    },
    mouseoverrow: function() {
        this.$el.css("background-color","#BADA55");
    },
    mouseoutrow: function() {
        this.$el.css("background-color","");
    },
    render:function() {
        this.$el.html(this.template(this.model.toJSON(), this.user.toJSON()));
        return this;
    }
});