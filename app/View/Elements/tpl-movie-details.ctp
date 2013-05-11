<script type="text/template" id="tpl-movie-details">
    <tr id="movie_<%= Movie.movie_id %>">
        <td class="movie_details span12" colspan="10">
            <div class="span4">
                <% if(Movie.has_image) { %>
                    <img class="movie_image"
                         src="/assets/img/movies/<%= Movie.imdb_id %>.jpg" />
                <% } else { %>
                    <div class="no_image_holder">
                        <div>
                            <p>No Cover Image</p>
                        </div>
                    </div>
                <% } %>
            </div>
            <div class="span8">
                <%= Movie.synopsis %>
                <dl class="directors clearfix">
                    <dt><strong>Director(s):</strong></dt>
                    <% _.each(Director, function(director) { %>
                        <dd class="pull-left">
                            <% if (director.cast_image) { %>
                                <img class="cast_image"
                                     src="/assets/img/cast/<%= director.person_imdb_id %>.jpg" />
                            <% } else { %>
                                <img class="cast_image" src="/assets/img/no_photo.png" />
                            <% } %>
                            <a target="_blank" href="http://www.imdb.com/name/<%= director.person_imdb_id %>">
                                <%= director.person_name %></a>
                            <span class="director_link link"
                                  data-person_name="<%= director.person_name %>"
                                  data-person_id="<%= director.person_id %>">
                                  (<a><%= director.movie_count %></a>)
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="actors clearfix">
                    <dt><strong>Actors(s):</strong></dt>
                    <% cast_count = 0; var hidden = false; %>
                    <% _.each(Actor, function(actor) { %>
                        <% cast_count = cast_count + 1;
                           if(cast_count > 10 && !hidden) { hidden = true; %>
                            <div id="all_cast_<%= Movie.movie_id %>" style="display:none;">
                        <% } %>
                        <dd class="pull-left" style="line-height:32px;">
                            <% if (actor.cast_image) { %>
                                <img class="cast_image"
                                     src="/assets/img/cast/<%= actor.person_imdb_id %>.jpg" />
                            <% } else { %>
                                <img class="cast_image" src="/assets/img/no_photo.png" />
                            <% } %>
                            <a target="_blank" href="http://www.imdb.com/name/<%= actor.person_imdb_id %>">
                                <%= actor.person_name %></a>
                            <span class="actor_link link"
                                  data-person_name="<%= actor.person_name %>"
                                  data-person_id="<%= actor.person_id %>">
                                (<a><%= actor.movie_count %></a>)
                            </span>
                        </dd>
                    <% }); %>
                    <% if (hidden) {%>
                        </div>
                        <br style="clear:both;" />
                        <span id="show_all_cast_<%= Movie.movie_id %>"
                              data-movie-id="<%= Movie.movie_id %>"
                              class="pull-right show-all-link">
                            <a class="btn">Show All</a>
                        </span>
                    <% } %>
                </dl>
                <dl class="genres clearfix">
                    <dt><strong>Genre(s):</strong></dt>
                    <% _.each(Genre, function(genre) { %>
                        <dd class="pull-left">
                            <a target="_blank" href="http://www.imdb.com/genre/<%= genre.genre.toLowerCase() %>">
                                <%= genre.genre %>
                            </a>
                            <span class="genre_link link"
                               data-genre_id="<%= genre.genre_id %>">
                                (<a><%= 0 %></a>)
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="keywords clearfix">
                    <dt><strong>Keyword(s):</strong></dt>
                    <% keyword_count = 0; var hidden = false; %>
                    <% _.each(Keyword, function(keyword) { %>
                        <% keyword_count = keyword_count + 1;
                           if(keyword_count > 10 && !hidden) { hidden = true; %>
                                <div id="all_keyword_<%= Movie.movie_id %>" style="display:none;">
                        <% } %>
                        <dd class="pull-left">
                            <a target="_blank" href="http://www.imdb.com/keyword/<%= keyword.keyword.toLowerCase() %>">
                                <%= keyword.keyword %>
                            </a>
                            <span class="keyword_link link"
                                  data-keyword="<%= keyword.keyword %>"
                                  data-keyword_id="<%= keyword.keyword_id %>">
                                (<a><%= 0 %></a>)
                            </span>
                        </dd>
                    <% }); %>
                    <% if (hidden) {%>
                        </div>
                        <br style="clear:both;" />
                        <span id="show_all_keyword_<%= Movie.movie_id %>"
                              data-movie-id="<%= Movie.movie_id %>"
                              class="pull-right show-all-link">
                            <a class="btn">Show All</a>
                        </span>
                    <% } %>
                </dl>
                <dl>
                    <dt>
                        <strong>File:</strong>
                    </dt>
                    <dd>
                        <table class="table table-bordered table-condensed file">
                            <tr>
                                <td><strong>Filesize:</strong></td>
                                <td><%= Movie.filesize %>GB</td>
                                <td><strong>Resolution:</strong></td>
                                <td>
                                    <%= Movie.width %>x<%= Movie.height %>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Path:</strong></td>
                                <td colspan="3"><%= Movie.path %></td>
                            </tr>
                        </table>
                    </dd>
                </dl>
                <dl>
                    <dt>
                        <strong>Media:</strong>
                        <span class="edit_media">
                            <a class="menu_item"><i class="icon-edit" /></a>
                        </span>
                    </dt>
                    <dd>
                        <table class="table table-bordered table-condensed media">
                            <tr>
                                <td><strong>Archived:</strong></td>
                                <td>
                                    <% if(Movie.media_id) { %>
                                        <img class="centre tick_cross"
                                             src='/assets/img/tick.png' />
                                    <% } else { %>
                                        <img class="centre tick_cross"
                                             src='/assets/img/cross.png' />
                                    <% } %>
                                </td>
                                <td><strong>Storage:</strong></td>
                                <td>
                                    <% if(Media.Storage &&
                                          Media.Storage.media_storage) { %>
                                        <%= Media.Storage.media_storage %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                                <td><strong>Format:</strong></td>
                                <td>
                                    <% if(Media.MediaFormat &&
                                          Media.MediaFormat.media_format) { %>
                                        <%= Media.MediaFormat.media_format %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Region:</strong></td>
                                <td>
                                    <% if(Media.Region &&
                                          Media.Region.region) { %>
                                        <%= Media.Region.region %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                                <td><strong>Special Edition:</strong></td>
                                <td>
                                    <% if(Movie.media_id) { %>
                                        <% if(Media.special_edition) { %>
                                            <img class="centre tick_cross"
                                                 src='/assets/img/tick.png' />
                                        <% } else { %>
                                            <img class="centre tick_cross"
                                                 src='/assets/img/cross.png' />
                                        <% } %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                                <td><strong>Boxed Set:</strong></td>
                                <td>
                                    <% if(Movie.media_id) { %>
                                        <% if(Media.boxset) { %>
                                            <img class="centre tick_cross"
                                                 src='/assets/img/tick.png' />
                                        <% } else { %>
                                            <img class="centre tick_cross"
                                                 src='/assets/img/cross.png' />
                                        <% } %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Purchase Price:</strong></td>
                                <td>
                                    <% if(Media.purchase_price) { %>
                                        &pound;<%= Media.purchase_price %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                                <td><strong>Current Price:</strong></td>
                                <td>
                                    <% if(Media.current_price) { %>
                                        &pound;<%= Media.current_price %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                                <td><strong>Amazon:</strong></td>
                                <td>
                                    <% if(Media.amazon_asin) { %>
                                        <a href="http://www.amazon.co.uk/gp/offer-listing/<%= Media.amazon_asin %>/"
                                           class="black" target="_blank">
                                            <%= Media.amazon_asin %>
                                        </a>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Notes:</strong></td>
                                <td colspan="5">
                                    <% if(Media.notes) { %>
                                        <%= Media.notes %>
                                    <% } else { %>
                                        -
                                    <% } %>
                                </td>
                            </tr>
                        </table>
                    </dd>
                </dl>
            </div>
        </td>
    </tr>
</script>