<?php
require_once('require/class.Connection.php');
require_once('require/class.Stats.php');
require_once('require/class.Language.php');
$Stats = new Stats();
$title = _("Statistics").' - '._("Most common Arrival Airport by Country");

if (!isset($filter_name)) $filter_name = '';
$airline_icao = (string)filter_input(INPUT_GET,'airline',FILTER_SANITIZE_STRING);
if ($airline_icao == 'all') {
    unset($_COOKIE['stats_airline_icao']);
    setcookie('stats_airline_icao', '', time()-3600);
    $airline_icao = '';
} elseif ($airline_icao == '' && isset($_COOKIE['stats_airline_icao'])) {
    $airline_icao = $_COOKIE['stats_airline_icao'];
} elseif ($airline_icao == '' && isset($globalFilter)) {
    if (isset($globalFilter['airline'])) $airline_icao = $globalFilter['airline'][0];
}
setcookie('stats_airline_icao',$airline_icao,time()+60*60*24,'/');
$year = filter_input(INPUT_GET,'year',FILTER_SANITIZE_NUMBER_INT);
$month = filter_input(INPUT_GET,'month',FILTER_SANITIZE_NUMBER_INT);
require_once('header.php');
include('statistics-sub-menu.php'); 

print '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<div class="info">
	  	<h1>'._("Most common Arrival Airport by Country").'</h1>
	  </div>
    	 <p>'._("Below are the <strong>Top 10</strong> most common countries of all the arrival airports.").'</p>';

$airport_country_array = $Stats->countAllArrivalCountries(true,$airline_icao,$filter_name,$year,$month);

print '<script>
    	google.load("visualization", "1", {packages:["geochart"]});
    	google.setOnLoadCallback(drawCharts);
    	$(window).resize(function(){
    		drawCharts();
    	});
    	function drawCharts() {
        
        var data = google.visualization.arrayToDataTable([ 
        	["'._("Country").'", "'._("# of times").'"],';

$country_data = '';
foreach($airport_country_array as $airport_item)
{
	$country_data .= '[ "'.$airport_item['airport_arrival_country'].'",'.$airport_item['airport_arrival_country_count'].'],';
}
$country_data = substr($country_data, 0, -1);
print $country_data;
?>

        ]);
    
        var options = {
        	legend: {position: "none"},
        	chartArea: {"width": "80%", "height": "60%"},
        	height:500,
        	colors: ["#8BA9D0","#1a3151"]
        };
    
        var chartCountry = new google.visualization.GeoChart(document.getElementById("chartCountry"));
        chartCountry.draw(data, options);
      }
    	</script>
    
    	<div id="chartCountry" class="chart" width="100%"></div>
    	
<?php

print '<div class="table-responsive">';
print '<table class="common-country table-striped">';
print '<thead>';
print '<th></th>';
print '<th>'._("Country").'</th>';
print '<th>'._("# of times").'</th>';
print '</thead>';
print '<tbody>';
$i = 1;
foreach($airport_country_array as $airport_item)
{
	print '<tr>';
	print '<td><strong>'.$i.'</strong></td>';
	print '<td>';
	print '<a href="'.$globalURL.'/country/'.strtolower(str_replace(" ", "-", $airport_item['airport_arrival_country'])).'">'.$airport_item['airport_arrival_country'].'</a>';
	print '</td>';
	print '<td>';
	print $airport_item['airport_arrival_country_count'];
	print '</td>';
	print '</tr>';
	$i++;
}
print '<tbody>';
print '</table>';
print '</div>';

require_once('footer.php');
?>