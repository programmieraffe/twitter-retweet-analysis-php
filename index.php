<html>
<head>
  <meta charset="utf-8"/>
  <style type="text/css">
    body{
      font-family: 'Arial';
    }
    tbody tr:nth-child(odd) {
      background-color: #ccc;
    }
    td,th{
      padding:5px;
    }
  </style>
</head>
<body>
<?php
$html = file_get_contents("tweets.html");

$doc = new DOMDocument();
// supress warnings:
@$doc->loadHTML($html);

// get twitter user name
$xpath = new \DOMXpath($doc);
// search for to get profile username: <link rel="canonical" href="https://twitter.com/m_andrasch">
$canonical_link = $xpath->query('//link[@rel="canonical"]'); // returns DOMNodelist
$profile_name_url = $canonical_link->item(0)->getAttribute('href');

$last_slash_pos = strrpos($profile_name_url, "/");
$profile_name = substr($profile_name_url, $last_slash_pos+1);

//$item->getAttribute('href');

// get all divs which have data-retweeter attribute value "th_koeln"

$articles = $xpath->query('//div[@data-retweeter="'.$profile_name.'"]');

?>
<h2>Twitter retweet analysis</h2>
<?php
// 2DO: current date of analysis
// 2DO: date of oldest tweet
echo "<p>Total retweets: ".$articles->length."</p>";

$retweeted_authors = array();

foreach($articles as $container) {
    $name = $container->getAttribute('data-name');
    $screen_name = $container->getAttribute('data-screen-name');
    $retweeted_authors[$screen_name]['name'] = $name;
    $retweeted_authors[$screen_name]['screen-name'] = $screen_name;
    $retweeted_authors[$screen_name]['tweets'][] = " "; //2DO: you can add tweet content here if it matters
  }

// sort array by most tweets
function cmp($a, $b){
    return (count($b['tweets']) - count($a['tweets']));
}
usort($retweeted_authors, 'cmp');

?>
<table>
  <tr>
    <th>Rank</th>
    <th>Account</th>
    <th>Retweeted</th>
  </tr>
<?php
$i = 1;

echo "<p>Total retweeted persons/accounts: ".count($retweeted_authors)."</p>";

foreach ($retweeted_authors as $author ) {
  echo "<tr>";
    echo "<td>".$i."</td>";
    echo "<td><a href='".$author['screen-name']."' target='_blank'>".$author['name']." (@".$author['screen-name'].")</a></td>";
    echo "<td>".count($author['tweets'])."</td>";
    echo "</tr>";
  $i++;
}
echo "</table>";
