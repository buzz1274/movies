window.Login = Backbone.Model.extend({
    url:'/login/'
});
window.Movie = Backbone.Model.extend({
    url:'/movies/',
    idAttribute: "movie_id"
});
window.MovieSummary = Backbone.Model.extend({
   url:'/movies/summary/'
});
window.MovieCollection = Backbone.Collection.extend({
    model:Movie,
    url:'/movies/'
});