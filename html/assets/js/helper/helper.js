var helper = {
    convert_to_time:function(minutes) {
        if(minutes < 60) {
            return minutes + "mins";
        } else {
            time = parseInt(minutes / 60);
            if(time == 1) {
                time += "hr"
            } else {
                time += "hrs"
            }
            if(minutes % 60) {
                time += " " + (minutes % 60) + "mins";
            }
            return time;
        }
    }
}