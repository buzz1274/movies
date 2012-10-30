<script type="text/template" id="tpl-movie-list-footer">
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