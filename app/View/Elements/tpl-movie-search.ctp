<script type="text/template" id="tpl-movie-search">
    <section id="slider_init_values"
             data-min-imdb-rating="<?php echo $summary['min_imdb_rating']; ?>"
             data-max-imdb-rating="<?php echo $summary['max_imdb_rating']; ?>"
             data-min-runtime="<?php echo $summary['min_runtime']; ?>"
             data-max-runtime="<?php echo $summary['max_runtime']; ?>"
             data-min-release-year="<?php echo $summary['min_release_year']; ?>"
             data-max-release-year="<?php echo $summary['max_release_year']; ?>" />
    <form id="search" class="well" onsubmit="return false;">
        <fieldset>
            <legend>
                Search Movies
                (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                <i id="download" class="icon-download-alt"></i>
            </legend>
            <ul class="nav nav-list">
                <li class="nav-header">
                    Keywords
                </li>
                <li class="span12" style="border-bottom:1px solid #e5e5e5;padding-bottom:5px;margin-bottom:10px;">
                    <input type="text" class="span12" autocomplete="off"
                           id="search_input">
                    <span class="offset0 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="cast" id="search_type" name="search_type">
                            Cast
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="keyword" id="search_type" name="search_type">
                            Keyword
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="all" id="search_type" name="search_type">
                            All
                        </label>
                    </span>
                    <span class="pull-right">
                        <a style="color:#000;">Advanced Search</a>
                    </span>
                </li>
                <li class="nav-header">
                    Genre
                </li>
                <?php echo $this->App->checkboxFormatter($genres, 'genre') ?>
                <li class="nav-header">
                    Picture Quality
                </li>
                <li class="span12">
                    <span class="offset0 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="1" id="hd_1" name="hd">
                            HD
                            <span>
                                (<%= hd != 0 && hd != null ? hd : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0" id="hd_0" name="hd">
                            SD
                            <span>
                                (<%= sd != 0 && sd != null ? sd : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="hd" value="all">
                            All
                            <span>
                                (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                            </span>
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
                            Yes
                            <span id="watched_yes">
                                (<%= watched != 0 && watched != null ? watched : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0"
                                   id="watched_0" name="watched">
                            No
                            <span id="watched_no">
                                (<%= not_watched != 0 && not_watched != null ? not_watched : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="watched" value="all">
                            All
                            <span>
                                (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                            </span>
                        </label>
                    </span>
                </li>
                <li class="nav-header">
                    Favourties
                </li>
                <li class="span12">
                    <span class=" offset0 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="1"
                                   id="favourites_1" name="favourites">
                            Yes
                            <span id="favourites_yes">
                                (<%= favourites != 0 && favourites != null ? favourites : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0"
                                   id="favourites_0" name="favourites">
                            No
                            <span id="favourites_no">
                                (<%= not_favourites != 0 && not_favourites != null ? not_favourites : '-' %>)
                            </span>
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="favourites" value="all">
                            All
                            <span>
                                (<%= totalMovies != 0 && totalMovies != null ? totalMovies : '-' %>)
                            </span>
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
                    <button type="reset" class="btn" id="luckyButton"
                            title="Return a random movie that matches the search criteria.">
                        I'm Feeling Lucky
                    </button>
                    <button type="reset" class="btn" id="resetButton">
                        Reset
                    </button>
                </li>
            </ul>
        </fieldset>
    </form>
</script>