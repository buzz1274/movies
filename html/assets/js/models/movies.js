window.Movie = Backbone.Model.extend({
    url:'/movies/',
    idAttribute: "movie_id"
});
window.MovieSummary = Backbone.Model.extend({
   url:'/movies/summary/'
});

window.UserMovieDownloaded = Backbone.Model.extend({
    url:'/user/downloaded/'
});