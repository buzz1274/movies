UrlParams.reset(true);
var app = new AppRouter();
var User = new MovieUser();
t = User.fetch({});
t.done(function() {
    new HeaderView({model: User});
    Backbone.history.start();
});

