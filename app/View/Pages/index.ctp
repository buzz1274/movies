<!DOCTYPE html>
<html>
    <head>
        <title>Movies</title>
        <link media="all" rel="stylesheet" type="text/css"
              href="/assets/css/styles.css" />
    </head>
    <body>
        <div id="content">
            <table id="movies_table" cellspacing="0" cellpadding="4">
            </table>
        </div>

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
                <a class="detailLink" href='javascript:void(0);'>
                    <img src='/assets/image/magnifying.png'
                         width='20' height='20'>
                </a>
            </td>
        </script>

        <script type="text/tenplate" id="tpl-movie-list-header">
            <tr>
                <th colspan="9" style="text-align:center;">
                    <span style="float:left;">
                        <a class="prevLink" href="javascript:void(0);">
                            &laquo;Prev
                        </a>
                    </span>
                    <%= startOffset %> to
                    <%= endOffset %> of
                    <%= totalMovies %> Movies
                    <span style="float:right;">
                        <a class="nextLink" href="javascript:void(0);">
                            Next&raquo;
                        </a>
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
                <th style="width:35%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="title">
                        Title
                    </a>
                </th>
                <th style="width:1%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="release_year">
                        Year
                    </a>
                </th>
                <th style="width:1%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="imdb_rating">
                        Rating
                    </a>
                </th>
                <th style="width:8%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="runtime">
                        Runtime
                    </a>
                </th>
                <th style="width:5%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="filesize">
                        Size(GB)
                    </a>
                </th>
                <th style="width:10%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="date_added">
                        Downloaded
                    </a>
                </th>
                <th style="width:1%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="hd">
                        HD
                    </a>
                </th>
                <th style="width:1%">
                    <a href="javascript:void(0);" class="sortLink"
                       data-sort_order="watched">
                        Watched
                    </a>
                </th>
                <th style="width:1%;text-align:center;">-</th>
            </tr>
        </script>

        <script type="text/template" id="tpl-movie-details">
            <h1>HERE</h1>
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