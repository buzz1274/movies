<script type="text/template" id="tpl-movie-paging">
    <% if(totalPages > 1) { %>
        <ul>
            <% if(page <= 1) {%>
                <li class="disabled"><a>First</a></li>
                <li class="disabled"><a>Prev</a></li>
            <% } else { %>
                <li>
                    <a href="#p=1&<%= State.remove_page_from_query_string() %>">
                        First
                    </a>
                </li>
                <li>
                    <a href="#p=<%= (parseInt(page) - 1) %><%= State.remove_page_from_query_string() %>">
                        Prev
                    </a>
                </li>
            <% } %>
            <% if(totalPages > 10 && page > 6) {
                var loopEnd = parseInt(page) + 5;
                if(loopEnd > totalPages) {
                    loopEnd = totalPages;
                }
                var loopStart = parseInt(loopEnd) - 9;
            } else {
                var loopStart = 1;
                var loopEnd = 10;
            }
            %>
            <% for(i = loopStart; i <= loopEnd; i++) { %>
                <li class="<% if(page == i) {%>disabled<%}%>">
                    <% if(page == i) { %>
                        <a><%= i %></a>
                    <% } else if(i <= totalPages) { %>
                        <a href="#p=<%= i %>&<%= State.remove_page_from_query_string() %>">
                            <%= i %>
                        </a>
                    <% } %>
                </li>
            <% } %>
            <% if(page >= totalPages) {%>
                <li class="disabled"><a>Next</a></li>
                <li class="disabled"><a>Last</a></li>
            <% } else { %>
                <li>
                    <a href="#p=<%= (parseInt(page) + 1) %><%= State.remove_page_from_query_string() %>">
                        Next
                    </a>
                </li>
                <li>
                    <a href="#p=<%= totalPages %><%= State.remove_page_from_query_string() %>">
                        Last
                    </a>
                </li>
            <% } %>
        </ul>
    <% } %>
</script>
