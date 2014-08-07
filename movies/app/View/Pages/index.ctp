<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Movies</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css"
              href="//code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css"
              rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/assets/css/styles.css" />
    </head>
    <body id="body">
        <div class="container-fluid">
            <div id="javascript_alert">
                This site requires javascript. Please enable in your browser.
            </div>
            <div id="content" class="row-fluid">
                <div id="movies_search" class="span4"></div>
                <div id="movies_list" class="span8">
                    <div class="message_popup_container"></div>
                    <div id="loading"><img src="/assets/img/spinner.gif"></div>
                    <table class="table table-bordered table-condensed"
                           id="movies_table">
                    </table>
                    <div id="pagination" class="pagination"></div>
                </div>
            </div>
        </div>

        <section id="slider_init_values"
                 data-min-imdb-rating="<?php echo $summary['min_imdb_rating']; ?>"
                 data-max-imdb-rating="<?php echo $summary['max_imdb_rating']; ?>"
                 data-min-runtime="<?php echo $summary['min_runtime']; ?>"
                 data-max-runtime="<?php echo $summary['max_runtime']; ?>"
                 data-min-release-year="<?php echo $summary['min_release_year']; ?>"
                 data-max-release-year="<?php echo $summary['max_release_year']; ?>" />

        <script data-main="assets/js/main"
                src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.8/require.min.js">
        </script>

    </body>
</html>