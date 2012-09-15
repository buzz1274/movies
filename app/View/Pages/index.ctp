<!DOCTYPE html>
<html>
    <head>
        <title>Movies</title>
        <link media="all" rel="stylesheet" type="text/css"
              href="/assets/css/styles.css" />
    </head>
    <body>
        <div id="header">
            Movies
        </div>
        <div id="content">
            <table style="display:none;"
                   id="movies_table" cellspacing="0" cellpadding="4">
            </table>
            <span id="version">
                moviedb v0.5
            </span>
        </div>

        <script type="text/template" id="tpl-movie-list-no-results">
            <tr>
                <td id="no_results" colspan="9">
                    No Results. Please try another search
                </td>
            </tr>
        </script>

        <script type="text/template" id="tpl-movie-list-item">
            <td>
                <a href='http://www.imdb.com/title/<%= Movie.imdb_id %>/'
                   target='_blank'>
                    <%= Movie.title %>
                </a>
            </td>
            <td class='centre'><%= Movie.release_year %></td>
            <td class='centre'><%= Movie.imdb_rating %></td>
            <td><%= Movie.runtime %></td>
            <td><%= Movie.filesize %></td>
            <td><%= Movie.date_added %></td>
            <td class='centre'>-</td>
            <td class='centre'>-</td>
            <td>
                <a title="More Details" class="detailLink"
                   data-imdb_id="<%= Movie.imdb_id %>">
                    <img src='/assets/image/magnifying.png'
                         width='20' height='20'>
                </a>
            </td>
        </script>

        <script type="text/tenplate" id="tpl-movie-list-header">
            <tr>
                <th colspan="9" style="text-align:center;">
                    <span style="float:left;">
                        <a class="prevLink">&laquo;Prev</a>
                    </span>
                    <span style="float:right;">
                        <a class="nextLink">Next&raquo;</a>
                    </span>
                    <span id="result_count" style="display:inline;">
                        <%= startOffset %> to
                        <%= endOffset %> of
                        <%= totalMovies %> Movies
                    </span>
                </th>
            </tr>
            <tr>
                <th colspan="9">
                    <span style="float:right;">
                        <a href="javascript:void(0);"
                           class="advancedSearchLink">
                            Advanced Search</a>
                        <input type="text" size="25"
                               placeholder="Movie Title"
                               id="movie_title_search" />
                        <input type="button" value="Search"
                               class="searchButton" />
                        <input type="button" value="Reset"
                               class="resetButton" />
                    </span>
                </th>
            </tr>
            <tr>
                <th style="width:30%">
                    <a class="sortLink" data-sort_order="title">
                        Title
                    </a>
                </th>
                <th style="width:1%">
                    <a class="sortLink" data-sort_order="release_year">
                        Year
                    </a>
                </th>
                <th style="width:1%">
                    <a class="sortLink" data-sort_order="imdb_rating">
                        Rating
                    </a>
                </th>
                <th style="width:8%">
                    <a class="sortLink" data-sort_order="runtime">
                        Runtime
                    </a>
                </th>
                <th style="width:5%">
                    <a class="sortLink" data-sort_order="filesize">
                        Size(GB)
                    </a>
                </th>
                <th style="width:10%">
                    <a class="sortLink" data-sort_order="date_added">
                        Downloaded
                    </a>
                </th>
                <th style="width:1%">
                    <a class="sortLink" data-sort_order="hd">
                        HD
                    </a>
                </th>
                <th style="width:1%">
                    <a class="sortLink" data-sort_order="watched">
                        Watched
                    </a>
                </th>
                <th style="width:1%;text-align:center;">-</th>
            </tr>
        </script>

        <script type="text/template" id="tpl-movie-details">
            <tr id="<%= Movie.imdb_id %>">
                <td colspan="9">
                    <% if(Movie.has_image) { %>
                        <img class="move_image"
                             src="/assets/image/movies/<%= Movie.imdb_id %>.jpg" />
                    <% } else { %>
                        <div class="no_image_holder">
                            <div>
                                <p>No Image</p>
                            </div>
                        </div>
                    <% } %>
                    <div><%= Movie.synopsis %></div>
                    <ul class="directors">
                        <li><strong>Director(s):</strong></li>
                        <% _.each(Director, function(director) { %>
                            <li>
                                <a class="directorLink"
                                   data-person_id="<%= director.person_id %>">
                                    <%= director.person_name %>
                                </a>
                            </li>
                        <% }); %>
                    </ul><br />
                    <ul class="actors">
                        <li><strong>Actors(s):</strong></li>
                        <% _.each(Actor, function(actor) { %>
                            <li>
                                <a class="actorLink"
                                   data-person_id="<%= actor.person_id %>">
                                    <%= actor.person_name %>
                                </a>
                            </li>
                        <% }); %>
                    </ul><br /><br /><br />
                    <ul class="genres">
                        <li><strong>Genre(s):</strong></li>
                        <% _.each(Genre, function(genre) { %>
                            <li>
                                <a class="genreLink"
                                   data-genre_id="<%= genre.genre_id %>">
                                    <%= genre.genre %>
                                </a>
                            </li>
                        <% }); %>
                    </ul><br />
                    <div>
                        <strong>Path:</strong>&nbsp;y:\<%= Movie.path %>
                    </div>
                </td>
            </tr>
        </script>

        <script type="text/javascript"
            src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
        </script>
        <script type="text/javascript"
            src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js">
        </script>
        <script type="text/javascript"
            src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js">
        </script>
        <script type="text/javascript" src="/assets/js/movies.js">
        </script>
    </body>
</html>