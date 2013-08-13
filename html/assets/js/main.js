UrlParams.reset(true);
var User = new MovieUser();
User.fetch({});
var app = new AppRouter();
Backbone.history.start();