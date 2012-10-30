<script type="text/template" id="tpl-movie-search">
    <form id="search" class="well" onsubmit="return false;">
        <fieldset>
            <legend>
                Search Movies&nbsp;(<%= totalMovies %>)
            </legend>
            <ul class="nav nav-list">
                <li class="nav-header">
                    Keywords
                </li>
                <li>
                    <input type="text" class="span12"
                           autocomplete="off"
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
                            <input type="radio" value="hd"
                                   name="definition">
                            HD
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="sd"
                                   name="definition">
                            SD
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="definition">
                            All&nbsp;(<%= totalMovies %>)
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
                                   name="watched">
                            Yes
                        </label>
                    </span>
                    <span class="offset4 span4">
                        <label class="radiobutton inline">
                            <input type="radio" value="0"
                                   name="watched">
                            No
                        </label>
                    </span>
                    <span class="offset8 span4">
                        <label class="radiobutton inline">
                            <input type="radio"checked="checked"
                                   name="watched">
                            All&nbsp;(<%= totalMovies %>)
                        </label>
                    </span>
                </li>
                <li class="nav-header">
                    Certificate
                </li>
                <?php echo $this->App->checkboxFormatter($certificates, 'certificate') ?>
                <li class="nav-header">
                    IMDB Rating
                <li class="span12">
                    <span class="span4" id="imdb_rating">
                        <div id="imdb_rating_slider_range"></div>
                    </span>
                    <span class="span8 slider_label">
                        <input type="text" id="imdb_rating_value"
                               style="display:none;" value="" />
                        <p style="font-size:1.20em;">&nbsp;
                            <em id="imdb_rating_label"></em>
                            (<%= totalMovies %>)
                        </p>
                    </span>
                </li>
                <li class="nav-header">
                    Runtime
                </li>
                <li class="span12">
                    <span class="span4" id="runtime">
                        <div id="runtime_slider_range"></div>
                    </span>
                    <span class="span8 slider_label">
                        <input type="text" id="runtime_value"
                               style="display:none;" value="" />
                        <p style="font-size:1.20em;">&nbsp;
                            <em id="runtime_label"></em>
                            (<%= totalMovies %>)
                        </p>
                    </span>
                </li>
                <li class="nav-header">
                    Year of Release
                </li>
                <li class="span12">
                    <span class="span4" id="release_year">
                        <div id="release_year_slider_range"></div>
                    </span>
                    <span class="span8 slider_label">
                        <input type="text" id="release_year_value"
                               style="display:none;" value="" />
                        <p style="font-size:1.20em;">&nbsp;
                            <em id="release_year_label"></em>
                            (<%= totalMovies %>)
                        </p>
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