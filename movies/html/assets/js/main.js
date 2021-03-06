require(["config"], function () {
    "use strict";

    require(["app", "router", "helper/state",
             "views/header", "views/movie/search",
             "models/movie/summary",
             "models/user/user", "bootstrap"],
        function (app, Router, State, HeaderView, MovieSearchView,
                 movieSummary, user, $) {
            $('#javascript_alert').remove();

            user.fetch({}).done(function () {

                State.reset(true);
                app.router = new Router();

                new HeaderView({model: user});
                new MovieSearchView({model: movieSummary});

                Backbone.history.start({pushState: false, root: app.root});

                setInterval(function () {
                    user.poll();
                }, 10000);

            });
        });
});
