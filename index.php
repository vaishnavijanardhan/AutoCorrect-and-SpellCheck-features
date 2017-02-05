<?php
include("urlMappingData.php");

header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

if ($query)
{
require_once('./Apache/Solr/Service.php');

$solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');

if (get_magic_quotes_gpc() == 1)
{
$query = stripslashes($query);
echo $query;
}


$additionalParameters = array('fl' => 'id, title, author, stream_size'  ,'wt' => 'json');
if (isset($_GET['mechanism'])) {

  if($_GET['mechanism'] == "default") {
    $additionalParameters = array('fl' => 'id, title, author, stream_size'  ,'wt' => 'json');
  }
  else {
    $additionalParameters = array('fl' => 'id, title, author, stream_size','sort' => 'pageRankFile desc', 'wt' => 'json');	
    }
}

try
{
  $results = $solr->search($query, 0, $limit, $additionalParameters);

}
catch (Exception $e)
{

die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}

?>
<html>
<head>
<title>Vaishnavi's Search </title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>


<script type="text/javascript"> 
    function display(suggestions, correctSpelling) {
        $('#suggestionsList').empty();
        var list = document.getElementById("suggestionsList");

        if(correctSpelling != null) {
            var li = document.createElement("li");
             li.innerHTML = "Did you mean: <a href='index.php?q="+correctSpelling+"'>" + correctSpelling + "</a>?";
             list.appendChild(li);
        }
        for(var i = 0; i < suggestions.length; i++) {
             var li = document.createElement("li");
             li.innerHTML = "<a href='index.php?q="+suggestions[i]+"'>" + suggestions[i] + "</a>";
             list.appendChild(li);
        }

    }

    var suggestions = [];
    function showData(data) {
        var initString = "";
        //data = '[["school","scale","scalable","shortcut","shield"],["ad","admission","admissions","adobe","adventurous"]]';
        data = JSON.parse(data);
        correctSpelling = data['spellCheck'];
        data = data['suggests'];
        for(var i = 0; i < data.length - 1; i++) {
            if(data[i][0])
                initString += data[i][0] + ' ';

        }
        for(var i = 0; i< data[data.length - 1].length; i++) {
            suggestions[i] = initString + data[data.length - 1][i];
        }
        display(suggestions, correctSpelling);
    }
    function doneTyping() {
        $.ajax( {
                            url : 'auto_fill.php' ,
                            type : 'GET',	
                            async : false,
                            data : { q : $('#q').val().trim() },
                            contentType:'json',
                            success: function(data) {
                                showData(data);
                            }
            });
    }
    $(document).ready(function(){
        var typingTimer;                
        var doneTypingInterval = 300;
        $("#q").keyup(function() {
             clearTimeout(typingTimer);
             typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });
        $("#q").on('keydown', function () {
            clearTimeout(typingTimer);
        });
        $(".suggestion").click(function(e){
            alert("CAme here");
            $("#q").text($(this).val());
        });
    });

</script>

</head>
<body>

<form  accept-charset="utf-8" method="get">
  <label for="q">Search:</label>
  <input id="q" name="q" type="text" autocomplete="off" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
Choose a search mechanishm:
<input type="radio" name="mechanism" value="page_rank" checked="checked"> PageRank
<input type="radio" name="mechanism" value="default"> Lucene

<input type="submit" value="Search" class="btn btn-primary"/>
</form>
<div id="suggestionsBox"> <ul id = "suggestionsList">

</ul></div>
<?php


if ($results)
{
$total = (int) $results->response->numFound;
$fst = min(1, $total);
$lst = min($limit, $total);
?>
<div>Results <?php echo $fst; ?> - <?php echo $lst;?> of <?php echo $total; ?>:</div>
<ol>

<?php
foreach ($results->response->docs as $row) {
  foreach ($row as $column => $value) {
      $id_present = false;
      $title_present = false;
      $stream_present = false;
      $author_present = false;
      $date_present = false;
}

}
?>

<?php

foreach ($results->response->docs as $row)
{
?>
<?php

foreach ($row as $column => $value)
{
    if (!strcmp($column,"id")) {
        $id_present = true;
    }
    else $id_present = false;

    if (!strcmp($column,"author")) {
        $author_present = true;
    }
    else $author_present = false;

    if (!strcmp($column,"title")) {
        $title_present = true;
    }
    else $title_present = false;

    if (!strcmp($column,"date")) {
        $date_present = true;
    }
    else $date_present = false;

    if (!strcmp($column,"stream_size")) {
        $stream_present = true;
    }
    else $stream_present = false;

}
?>

<?php foreach ($row as $column => $value) { ?>
        <!--<th><?php {echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); }?></th> -->
<?php if (!strcmp($column,"id")) { ?>
<a href="<?php echo $dict[($value)] ?>"> Link to Page </a><?php } ?>
<br>

<?php if ($stream_present && !strcmp($column,"stream_size")) {?>
 Size: <?php echo $value/1000; echo "KB <br>" ?> 
<?php } else if (!($stream_present)){
echo "Size:N/A  <br>" ; 
$stream_present = true;}?>
<?php if (!strcmp($column,"title")) {?>
 Title: <?php echo $value; ?> 
<?php }?>
<?php if ($author_present && !strcmp($column,"author")) {?>
 Author: <?php echo $value; ?> 
<?php } else if(!($author_present)) {
    echo "Author:N/A <br>";
 $author_present = true;}?>
<?php if ($date_present && !strcmp($column,"date")) {?>
 Date: <?php echo $value;?> 
<?php } else if(!($date_present)){echo "Date:N/A"; $date_present = true;}?>
<?php
}
?>
<?php
echo "<br>";
}
?>
      </tr>
</ol>
<?php
}
?>
</body>
</html>
