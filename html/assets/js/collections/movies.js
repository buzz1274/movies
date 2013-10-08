window.MovieCollection = Backbone.Collection.extend({
    model:Movie,
    url:'/movies/'
});
window.UserMovieDownloadedCollection = Backbone.Collection.extend({
    model:UserMovieDownloaded,
    url:'/user/downloaded/'
});