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
                            <span class="director_link link"
                               data-person_id="<%= director.person_id %>">
                                <a></1><%= director.person_name %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="actors clearfix">
                    <dt><strong>Actors(s):</strong></dt>
                    <% _.each(Actor, function(actor) { %>
                        <dd class="pull-left">
                            <span class="actor_link link"
                               data-person_id="<%= actor.person_id %>">
                                <a><%= actor.person_name %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="genres clearfix">
                    <dt><strong>Genre(s):</strong></dt>
                    <% _.each(Genre, function(genre) { %>
                        <dd class="pull-left">
                            <span class="genre_link link"
                               data-genre_id="<%= genre.genre_id %>">
                                <a><%= genre.genre %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="genres clearfix">
                    <dt><strong>Keyword(s):</strong></dt>
                    <% _.each(Keyword, function(keyword) { %>
                        <dd class="pull-left">
                            <span class="keyword_link link"
                               data-keyword_id="<%= keyword.keyword_id %>">
                                <a><%= keyword.keyword %></a>
                            </span>
                        </dd>
                    <% }); %>
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
                                    n/a
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
                                        <img height="15" width="15"
                                             src='/assets/img/tick.png' />
                                    <% } else { %>
                                        <img height="15" width="15"
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
                                            <img height="15" width="15"
                                                 src='/assets/img/tick.png' />
                                        <% } else { %>
                                            <img height="15" width="15"
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
                                            <img height="15" width="15"
                                                 src='/assets/img/tick.png' />
                                        <% } else { %>
                                            <img height="15" width="15"
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