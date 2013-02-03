$(function() {
    var availableTags = [];
    $("#search_input").autocomplete({
        source: availableTags
    });
});