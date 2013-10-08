State.reset(true);
var app = new AppRouter();
var User = new MovieUser();
User.fetch({}).done(function() {
    new HeaderView({model: User});
    Backbone.history.start();
    window.setInterval(function(){
        User.poll();
    }, 10000);
});

