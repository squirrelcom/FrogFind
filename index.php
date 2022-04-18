<?php
require_once('vendor/autoload.php');

$show_results = FALSE;
$results_html = "";
$final_result_html = "<hr>";

if(isset( $_GET['q'])) { // if there's a search query, show the results for it
    $query = urlencode($_GET["q"]);
    $show_results = TRUE;
    $search_url = "https://html.duckduckgo.com/html?q=" . $query;
    if(!$results_html = file_get_contents($search_url)) {
        $error_text .=  "Failed to get results, sorry :( <br>";
    }
    $simple_results=$results_html;
    $simple_results = str_replace( 'strong>', 'b>', $simple_results ); //change <strong> to <b>
    $simple_results = str_replace( 'em>', 'i>', $simple_results ); //change <em> to <i>
    $simple_results = clean_str($simple_results);

    $result_blocks = explode('<h2 class="result__title">', $simple_results);
    $total_results = count($result_blocks)-1;

    for ($x = 1; $x <= $total_results; $x++) {
        if(strpos($result_blocks[$x], '<a class="badge--ad">')===false) { //only return non ads
            // result link, redirected through our proxy
            $result_link = explode('class="result__a" href="', $result_blocks[$x])[1];
            $result_topline = explode('">', $result_link);
            $result_link = str_replace( '//duckduckgo.com/l/?uddg=', '/read.php?a=', $result_topline[0]);
            // result title
            $result_title = str_replace("</a>","",explode("\n", $result_topline[1]));
            // result display url
            $result_display_url = explode('class="result__url"', $result_blocks[$x])[1];
            $result_display_url = trim(explode("\n", $result_display_url)[1]);
            // result snippet
            $result_snippet = explode('class="result__snippet"', $result_blocks[$x])[1];
            $result_snippet = explode('">', $result_snippet)[1];
            $result_snippet = explode('</a>', $result_snippet)[0];

            $final_result_html .= "<br><a href='" . $result_link . "'><font size='4'><b>" . $result_title[0] . "</b></font><br><font color='#008000' size='2'>" 
                                . $result_display_url . "</font></a><br>" . $result_snippet . "<br><br><hr>";
        }
    }
}

//replace chars that old machines probably can't handle
function clean_str($str) {
    $str = str_replace( "‘", "'", $str );    
    $str = str_replace( "’", "'", $str );  
    $str = str_replace( "“", '"', $str ); 
    $str = str_replace( "”", '"', $str );
    $str = str_replace( "–", '-', $str );
    $str = str_replace( "&#x27;", "'", $str );

    return $str;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 2.0//EN">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<html>
<head>
	<title>seARch!</title>
</head>
<body>

<?php if($show_results) { // there's a search query in q, so show search results ?>

    <form action="/" method="get">
    <a href="/"><font size=6 color="#008000">seA</font><font size=6 color="#000000">Rch!</font></a> Leap again: <input type="text" size="30" name="q" value="<?php echo urldecode($query) ?>">
    <input type="submit" value="seARch!! it">
    </form>
    <hr>
    <br>
    <center>Search Results for <b><?php echo strip_tags(urldecode($query)) ?></b></center>
    <br>
    <?php echo $final_result_html ?>
    
<?php } else { // no search query, so show new search ?>
    <center><img src="/img/frogfind.gif" width="174" height="80" alt="seARch Logo"></center>
    <center><h3>seARch the web privately</h3></center>
    <br><br>
    <center>
    <form action="/" method="get">
    Leap to: <input type="text" size="30" name="q"><br>
    <input type="submit" value="seARch!! it">
    </center>
    <br><br><br>
</form>
</form>

<?php } ?>

</body>
</html>
