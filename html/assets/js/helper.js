var loadingImage = function(on) {
    if(on) {
        $('#opaque').css('display', 'block');
        $('#loading').css('display', 'block');
    } else {
        $('#opaque').css('display', 'none');
        $('#loading').css('display', 'none');
    }
}