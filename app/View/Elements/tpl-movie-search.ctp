<script type="text/template" id="tpl-movie-search">
    <form id="search" class="well" onsubmit="return false;">
        <fieldset>
            <legend>
                Search Movies&nbsp;
                (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                <i id="download" class="icon-download-alt"></i>
            </legend>
            <ul class="nav nav-list">
                <li class="nav-header">
                    Keywords
                </li>
                <li>
                    <input type="text" class="span12" autocomplete="off"
                           id="search_input">
                </li>
                <li class="nav-header">
                    Genre
                </li>
                <?php echo $this->App->checkboxFormatter($genres, 'genre') ?>
                <li class="nav-header">
                    Picture Quality
                </li>
                <li class="span12">
                    <span class=" offset0 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="1" id="hd_1" name="hd">
                            HD&nbsp;(<%= hd != 0 && hd != null ? hd : '-' %>)
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0" id="hd_0" name="hd">
                            SD&nbsp;(<%= sd != 0 && sd != null ? sd : '-' %>)
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="hd" value="all">
                            All&nbsp;(<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                        </label>
                    </span>
                </li>
                <li class="nav-header">
                    Seen
                </li>
                <li class="span12">
                    <span class=" offset0 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="1"
                                   id="watched_1" name="watched">
                            Yes&nbsp;(<%= watched != 0 && watched != null ? watched : '-' %>)
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0"
                                   id="watched_0" name="watched">
                            No&nbsp;(<%= not_watched != 0 && not_watched != null ? not_watched : '-' %>)
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="watched" value="all">
                            All&nbsp;(<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                        </label>
                    </span>
                </li>
                <li class="nav-header">
                    Certificate
                </li>
                <?php echo $this->App->checkboxFormatter($certificates,
                                                         'certificate') ?>
                <li class="nav-header">
                    IMDB Rating
                    <p class="slider_label">
                        &nbsp;<em id="imdb_rating_label"></em>
                        (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                    </p>
                <li class="span12">
                    <span class="span12" id="imdb_rating">
                        <div id="imdb_rating_slider_range"></div>
                    </span>
                </li>
                <li class="nav-header">
                    Runtime
                    <p class="slider_label">
                        &nbsp;<em id="runtime_label"></em>
                        (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                    </p>
                </li>
                <li class="span12">
                    <span class="span12" id="runtime">
                        <div id="runtime_slider_range"></div>
                    </span>
                </li>
                <li class="nav-header">
                    Year of Release
                    <p class="slider_label">
                        <em id="release_year_label"></em>
                        (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                    </p>
                </li>
                <li class="span12">
                    <span class="span12" id="release_year">
                        <div id="release_year_slider_range"></div>
                    </span>
                </li>
                <li>
                    <button type="submit" class="btn" id="submitButton">
                        Submit
                    </button>
                    <button type="reset" class="btn" id="resetButton">
                        Reset
                    </button>
                </li>
            </ul>
        </fieldset>
    </form>
</script>