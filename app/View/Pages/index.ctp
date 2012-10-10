<!DOCTYPE html>
<html>
    <head>
        <title>Movies</title>
        <link media="all" rel="stylesheet" type="text/css"
              href="/assets/css/styles.css" />
    </head>
    <body>
        <div id="header">
            <a href='/'>Movies</a>
        </div>
        <div id="content">
            <table style="display:none;"
                   id="movies_table" cellspacing="0" cellpadding="1">
            </table>
            <span id="version">
                moviedb v0.60
            </span>
        </div>

        <script type="text/tenplate" id="tpl-movie-list-search">
            <img class="paging_link icon" data_link_action="first"
                 title="First Page" src="/assets/image/page-first.gif" />
            <img class="paging_link icon" data_link_action="prev"
                 title="Previous Page" src="/assets/image/page-prev.gif" />
            <img class="paging_link icon" data_link_action="next"
                 title="Next Page" src="/assets/image/page-next.gif" />
            <img class="paging_link icon" data_link_action="last"
                 title="Last Page" src="/assets/image/page-last.gif" />
            <span class="border" />
            <img id="xls_icon" class="icon" src="/assets/image/xls_icon.gif" />
            <span class="border" />
            <img id="advanced_search_icon" class="icon" title="Advanced Search"
                 src="/assets/image/magnifying.png" />
            <span class="border" />
            <input id="search_input" type="text" size="25"
                   placeholder="Movie, Keywords, Cast"/>
            Search
            <p id="result_count">
                Movies <%= startOffset %> to
                <%= endOffset %> of <%= totalMovies %>
            </p>
        </script>

        <script type="text/tenplate" id="tpl-movie-list-header">
            <tr>
                <th style="width:30%">
                    <span class="up_arrow" />
                    <span class="sort_link link" data-sort_order="title">
                        Title
                    </span>
                </th>
                <th style="width:5%">
                    <span class="sort_link link" data-sort_order="release_year">
                        Year
                    </span>
                </th>
                <th style="width:6%">
                    <span class="sort_link link" data-sort_order="imdb_rating">
                        Rating
                    </span>
                </th>
                <th style="width:8%">
                    <span class="sort_link link" data-sort_order="runtime">
                        Runtime
                    </span>
                </th>
                <th style="width:5%">
                    <span class="sort_link link" data-sort_order="cert">
                        Cert
                    </span>
                </th>
                <th style="width:5%">
                    <span class="sort_link link" data-sort_order="filesize">
                        GB
                    </span>
                </th>
                <th style="width:10%">
                    <span class="sort_link link" data-sort_order="date_added">
                        Purchased
                    </span>
                </th>
                <th style="width:4%">
                    <span class="sort_link link" data-sort_order="hd">
                        HD
                    </span>
                </th>
                <th style="width:5%">
                    <span class="sort_link link" data-sort_order="watched">
                        Seen
                    </span>
                </th>
                <th style="width:4%;text-align:center;">-</th>
            </tr>
        </script>

        <script type="text/template" id="tpl-movie-list-no-results">
            <tr>
                <td id="no_results" colspan="12">
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
            <td class='centre'>
                <% if(Movie.certificate) { %>
                    <img style="padding-top:5px;"
                         src='/assets/image/<%= Movie.certificate %>.png' />
                <% } else { %>
                    -
                <% } %>
            </td>
            <td class='centre'><%= Movie.filesize %></td>
            <td><%= Movie.date_added %></td>
            <td class='centre'>
                <% if(Movie.hd) { %>
                    <img style="padding-top:5px;" height="15" width="15"
                         src='/assets/image/ticked.png' />
                <% } else { %>
                    <img style="padding-top:5px;" height="15" width="15"
                         src='/assets/image/cross.png' />
                <% } %>
            </td>
            <td class='centre'>
                <% if(Movie.watched) { %>
                    <img style="padding-top:5px;" height="15" width="15"
                         src='/assets/image/ticked.png' />
                <% } else { %>
                    <img style="padding-top:5px;" height="15" width="15"
                         src='/assets/image/cross.png' />
                <% } %>
            </td>
            <td class='centre'>
                <span title="More Details" class="detail_link link"
                   data-imdb_id="<%= Movie.imdb_id %>">
                    <img src='/assets/image/magnifying.png'
                         width='20' height='20'>
                </span>
            </td>
        </script>

        <script type="text/tenplate" id="tpl-movie-list-footer">
            <span id="first_page_link" class="paging_link link"
                  data_link_action="first">
                &laquo;&laquo;First
            </span>
            <span id="prev_page_link" class="paging_link link"
                  data_link_action="prev">
                &laquo;Prev
            </span>
            <span id="result_count">
                <%= startOffset %> to
                <%= endOffset %> of
                <%= totalMovies %> Movies
            </span>
            <span id="next_page_link" class="paging_link link"
                  data_link_action="next">
                Next&raquo;
            </span>
            <span id="last_page_link" class="paging_link link"
                  data_link_action="last">
                Last&raquo;&raquo;
            </span>
        </script>

        <script type="text/template" id="tpl-movie-details">
            <tr id="<%= Movie.imdb_id %>">
                <td class="movie_details" colspan="10">
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
                                <span class="director_link link"
                                   data-person_id="<%= director.person_id %>">
                                    <%= director.person_name %>
                                </span>
                            </li>
                        <% }); %>
                    </ul><br />
                    <ul class="actors">
                        <li><strong>Actors(s):</strong></li>
                        <% _.each(Actor, function(actor) { %>
                            <li>
                                <span class="actor_link link"
                                   data-person_id="<%= actor.person_id %>">
                                    <%= actor.person_name %>
                                </span>
                            </li>
                        <% }); %>
                    </ul><br /><br /><br />
                    <ul class="genres">
                        <li><strong>Genre(s):</strong></li>
                        <% _.each(Genre, function(genre) { %>
                            <li>
                                <span class="genre_link link"
                                   data-genre_id="<%= genre.genre_id %>">
                                    <%= genre.genre %>
                                </span>
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