<?php
date_default_timezone_set('America/Chicago');

/*
					 __          ________       _______ _    _ ______ _____      _____       _______       
					 \ \        / /  ____|   /\|__   __| |  | |  ____|  __ \    |  __ \   /\|__   __|/\    
					  \ \  /\  / /| |__     /  \  | |  | |__| | |__  | |__) |   | |  | | /  \  | |  /  \   
					   \ \/  \/ / |  __|   / /\ \ | |  |  __  |  __| |  _  /    | |  | |/ /\ \ | | / /\ \  
					    \  /\  /  | |____ / ____ \| |  | |  | | |____| | \ \    | |__| / ____ \| |/ ____ \ 
					     \/  \/   |______/_/    \_\_|  |_|  |_|______|_|  \_\   |_____/_/    \_\_/_/    \_\

	PULL FIELDS FROM WEATHER JSON
*/
$weather_json = file_get_contents('weather.json');
$weather = json_decode($weather_json);

$c_dt = date('r',$weather->current->dt);
$c_sunrise = date('h:i A',$weather->current->sunrise);
$c_sunset = date('h:i A',$weather->current->sunset);
$c_temp = number_format($weather->current->temp,0);
$c_feels_like = $weather->current->feels_like;
$c_pressure = number_format($weather->current->pressure * 0.0295301,1) . " IN";			// convert millibars to inches
$c_humidity = $weather->current->humidity;
$c_dew_point = number_format($weather->current->dew_point,0);
$c_uvi = $weather->current->uvi;
$c_clouds = $weather->current->clouds;
$c_visibility = $weather->current->visibility;
$c_wind_speed = $weather->current->wind_speed;
$c_wind_deg = $weather->current->wind_deg;

switch(intval($c_wind_deg / 22.5))
{
	case 1: $wind_direction = "N"; break;
	case 2: $wind_direction = "NNE"; break;
	case 3: $wind_direction = "NE"; break;
	case 4: $wind_direction = "ENE"; break;
	case 5: $wind_direction = "E"; break;
	case 6: $wind_direction = "ESE"; break;
	case 7: $wind_direction = "SE"; break;
	case 8: $wind_direction = "SSE"; break;
	case 9: $wind_direction = "S"; break;
	case 10: $wind_direction = "SSW"; break;
	case 11: $wind_direction = "SW"; break;
	case 12: $wind_direction = "WSW"; break;
	case 13: $wind_direction = "W"; break;
	case 14: $wind_direction = "WNW"; break;
	case 15: $wind_direction = "NW"; break;
	case 16: $wind_direction = "NNW"; break;
	case 17: $wind_direction = "N"; break;
	default: $wind_direciton = ""; break;
}
$c_weather_desc = $weather->current->weather[0]->description;
$c_icon = $weather->current->weather[0]->icon;

$d_temp_hi = number_format($weather->daily[0]->temp->max,0);
$d_temp_lo = number_format($weather->daily[0]->temp->min,0);

$weather_alert = "";
$alert_color = "bgblue";
$footer_div = "";
if(count($weather->alerts) > 0)
{
	foreach($weather->alerts as $alert)
	{
		$alert_color = "bgred";
		$desc_chunks = explode("*",$alert->description);
		$weather_alert .= "&nbsp;&nbsp;" . $desc_chunks[0] . " " . $desc_chunks[1] . " " . $desc_chunks[4] . "<br>";
	}
} else {
	$alert_color = "bggreen";
}
if(strlen($weather_alert) > 0)
{
	$footer_div = "<div class=\"alert $alert_color\">$weather_alert</div>";
} else {
	$footer_div = "<div class=\"foot $alert_color\">Copyright &copy; 2022</div>";
}
$moonrise = date('h:i A',$weather->daily[0]->moonrise);
$moonset = date('h:i A',$weather->daily[0]->moonset);

$dayone_weather = "<b>TODAY</b>: " . $weather->daily[0]->weather[0]->main . " with a High of " . number_format($weather->daily[0]->temp->max,0) . "&#0176; F and a Low of " . number_format($weather->daily[0]->temp->min,0) . "&#0176; F &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp; ";
$daytwo_weather = "<b>TOMORROW</b>: " . $weather->daily[1]->weather[0]->main . " with a High of " . number_format($weather->daily[1]->temp->max,0) . "&#0176; F and a Low of " . number_format($weather->daily[1]->temp->min,0) . "&#0176; F &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp; ";
$thirdday = "<b>" . strtoupper(date('l',$weather->daily[2]->dt)) . "</b>";

$daythree_weather = $thirdday . ": " . $weather->daily[2]->weather[0]->main . " with a High of " . number_format($weather->daily[2]->temp->max,0) . "&#0176; F and a Low of " . number_format($weather->daily[2]->temp->min,0) . "&#0176; F &nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;";

$daily_rain = number_format($weather->daily[0]->rain/25.4,2);
$daily_rainchance = number_format($weather->daily[0]->pop * 100,0);

/*
					 __          __  _______ ______ _____      _____       _______       
					 \ \        / /\|__   __|  ____|  __ \    |  __ \   /\|__   __|/\    
					  \ \  /\  / /  \  | |  | |__  | |__) |   | |  | | /  \  | |  /  \   
					   \ \/  \/ / /\ \ | |  |  __| |  _  /    | |  | |/ /\ \ | | / /\ \  
					    \  /\  / ____ \| |  | |____| | \ \    | |__| / ____ \| |/ ____ \ 
					     \/  \/_/    \_\_|  |______|_|  \_\   |_____/_/    \_\_/_/    \_\

	PULL FIELDS FROM WATER JSON

	USGS:03302800:00060:00000		BLUE RIVER AT FREDERICKSBURG, IN				Discharge
	USGS:03302800:00065:00000		BLUE RIVER AT FREDERICKSBURG, IN				Gage Height
	USGS:03303000:00060:00000		BLUE RIVER NEAR WHITE CLOUD, IN					Discharge
	USGS:03303000:00065:00000		BLUE RIVER NEAR WHITE CLOUD, IN					Gage Height
	USGS:03303280:00010:00000		OHIO RIVER AT CANNELTON DAM AT CANNELTON, IN	Temperature
	USGS:03303280:00045:00000		OHIO RIVER AT CANNELTON DAM AT CANNELTON, IN	Precipitation
	USGS:03303280:00060:00000		OHIO RIVER AT CANNELTON DAM AT CANNELTON, IN	Discharge
	USGS:03303280:00065:00000		OHIO RIVER AT CANNELTON DAM AT CANNELTON, IN	Gage Height
	USGS:03318010:00010:00000		ROUGH RIVER NEAR FALLS OF ROUGH AT DAM, KY		Temperature
	USGS:03318010:00065:00000		ROUGH RIVER NEAR FALLS OF ROUGH AT DAM, KY		Gage Height
*/

$water_json = file_get_contents('water.json');
$water = json_decode($water_json);

foreach($water->value->timeSeries as $series)
{
	$usgs_criteria = $series->name;

	switch($usgs_criteria)
	{
		case "USGS:03302800:00060:00000": $discharge_bf = $series->values[0]->value[0]->value . " ft<sup>3</sup>/s"; break;
		case "USGS:03302800:00065:00000": $gage_bf = $series->values[0]->value[0]->value . " ft"; break;
		case "USGS:03303000:00060:00000": $discharge_bw = $series->values[0]->value[0]->value . " ft<sup>3</sup>/s"; break;
		case "USGS:03303000:00065:00000": $gage_bw = $series->values[0]->value[0]->value . " ft"; break;
		case "USGS:03303280:00010:00000": $temp_can = number_format(($series->values[0]->value[0]->value * 9 / 5) + 32,0) . "&#0176; F"; break;
		case "USGS:03303280:00045:00000": $precip_can = $series->values[0]->value[0]->value . " in"; break;
		case "USGS:03303280:00060:00000": $discharge_can = $series->values[0]->value[0]->value . " ft<sup>3</sup>/s"; break;
		case "USGS:03303280:00065:00000": $gage_can = $series->values[0]->value[0]->value . " ft"; break;
		case "USGS:03318010:00010:00000": $temp_rough = number_format(($series->values[0]->value[0]->value * 9 / 5) + 32,0) . "&#0176; F"; break;
		case "USGS:03318010:00065:00000": $gage_rough = $series->values[0]->value[0]->value . " ft"; break;
	}
}

/*
					  __  __  ______      _______ ______     _____       _______       
					 |  \/  |/ __ \ \    / /_   _|  ____|   |  __ \   /\|__   __|/\    
					 | \  / | |  | \ \  / /  | | | |__      | |  | | /  \  | |  /  \   
					 | |\/| | |  | |\ \/ /   | | |  __|     | |  | |/ /\ \ | | / /\ \  
					 | |  | | |__| | \  /   _| |_| |____    | |__| / ____ \| |/ ____ \ 
					 |_|  |_|\____/   \/   |_____|______|   |_____/_/    \_\_/_/    \_\

	PULL MOVIE DATA FROM HTML BLOB (MALCO)
*/

$movie_blob = file_get_contents('movie_trimmed');
$x = strpos($movie_blob,"SHOWTIMES");
$y = strpos($movie_blob,"--");
$movie_blob = trim(substr(substr($movie_blob,$x,($y - $x)),9));
$movie_blob = str_replace("       ","~",$movie_blob);
$movie_blob = str_replace("      ","^",$movie_blob);
$movie_blob = str_replace("   ","^",$movie_blob);

$movie_table .= "<table class='movie_table'>\n";
$movies=explode("~",$movie_blob);
foreach($movies as $movie)
{
	$fields = explode("^",$movie);
	$movie_table .= "<tr class='movie_table_row'><td>At the Malco 54</td></tr>\n";
	$movie_table .= "<tr class='movie_table_row'>";
	$movie_table .= "<td class='movie_cell'><span class='movie_title'>" . $fields[0] . "</span><br>&nbsp;&nbsp;&nbsp;" . rtrim($fields[2],"|") . "<br><br></td>";
//	$movie_table .= "<td class='movie_rating'>" . $fields[1] . "</td>";
//	$movie_table .= "<td class='movie_times'>" . $fields[2] . "</td>";
	$movie_table .="</tr>\n";
}
$movie_table .= "</table>\n";

/*
					  _    _ _______ __  __ _           _____ _______       _____ _______ 
					 | |  | |__   __|  \/  | |         / ____|__   __|/\   |  __ \__   __|
					 | |__| |  | |  | \  / | |        | (___    | |  /  \  | |__) | | |   
					 |  __  |  | |  | |\/| | |         \___ \   | | / /\ \ |  _  /  | |   
					 | |  | |  | |  | |  | | |____     ____) |  | |/ ____ \| | \ \  | |   
					 |_|  |_|  |_|  |_|  |_|______|   |_____/   |_/_/    \_\_|  \_\ |_|   

*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="refresh" content="120">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<script src="<?= SITE_SSL_URL ?>/js/jquery-2.2.4.min.js" type="text/javascript"></script>
	<script src="<?= SITE_SSL_URL ?>/js/jquery.notifybar.js" type="text/javascript"></script>
	<script language="JavaScript">
	function redirectHttpToHttps()
	{
		var httpURL= window.location.hostname + window.location.pathname + window.location.search;
		var httpsURL= "https://" + httpURL;
		if(window.location.href.substr(0,5) == "http:")
		{
			window.location.assign(httpsURL);
		}
	}
	redirectHttpToHttps();
	</script>
	<title>Time n Temp</title>
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	
	<!-- Bootstrap -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<link href="site.css" rel="stylesheet">
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://www.paypalobjects.com/api/checkout.js"></script>
	<script> 
		function addZero(i) {
			if (i < 10) {i = "0" + i}
			return i;
		}
		function display_ct6() {
			const weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
			const month = ["January","February","March","April","May","June","July","August","September","October","November","December"];
			var x = new Date()
			var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
			hours = x.getHours() % 12;
			hours = hours ? hours : 12;
			day = weekday[x.getDay()].substring(0,3).toUpperCase();
			var x1= day + " " + month[x.getMonth()].substring(0,3).toUpperCase() + " " + x.getDate() + ", " + x.getFullYear(); 
			x1 = x1 + " &nbsp; &nbsp; &nbsp; " +  hours + ":" +  addZero(x.getMinutes()) + ":" +  addZero(x.getSeconds()) + " " + ampm;
			document.getElementById('ct6').innerHTML = x1;
			
			display_c6();
		}
		function display_c6() {
			var refresh=1000; // Refresh rate in milli seconds
			mytime=setTimeout('display_ct6()',refresh)
		}
		display_c6()
	</script>

</head>

<body onload="display_c6()">
<?php 
?>

<div class='marquee marquee2'><span>
<?php 
/*						   _____ _    _ _____  _____  ______ _   _ _______   ____  _      ____   _____ _  __
						  / ____| |  | |  __ \|  __ \|  ____| \ | |__   __| |  _ \| |    / __ \ / ____| |/ /
						 | |    | |  | | |__) | |__) | |__  |  \| |  | |    | |_) | |   | |  | | |    | ' / 
						 | |    | |  | |  _  /|  _  /|  __| | . ` |  | |    |  _ <| |   | |  | | |    |  <  
						 | |____| |__| | | \ \| | \ \| |____| |\  |  | |    | |_) | |___| |__| | |____| . \ 
						  \_____|\____/|_|  \_\_|  \_\______|_| \_|  |_|    |____/|______\____/ \_____|_|\_\
*/
	echo $dayone_weather . $daytwo_weather . $daythree_weather;
?>
</span></div>

<div class='timestamp' id="ct6"><?php echo strtoupper(date("D  M  j,  Y")) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . strtoupper(date("g:i:s A")); ?></div>

<div id="current">
<table id="current_table">
	<tr>
		<!-- 01 to 03 --><th colspan=3 id='weeky_weather_cell'>
			<table class='week_weather'>
				<tr>
					<th><?=strtoupper(substr(date('r',$weather->daily[0]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[0]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[1]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[1]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[2]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[2]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[3]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[3]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[4]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[4]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[5]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[5]->weather[0]->icon;?>@2x.png'></th>
					<th><?=strtoupper(substr(date('r',$weather->daily[6]->dt),0,3));?><br><img src='https://openweathermap.org/img/wn/<?=$weather->daily[6]->weather[0]->icon;?>@2x.png'></th>
				</tr>
			</table>
		</th>
		<!-- 04 --><td class="blankcolumn divider">&nbsp;</td>
		<!-- 05 to 07 --><td colspan=3>
		<?php 
			echo strtoupper($c_weather_desc);
		?>
		</td>
		<!-- 08 --><td class="blankcolumn">&nbsp;</td>
		<!-- 09 --><td>UV:</td>
		<!-- 10 --><td class="blankcolumn">&nbsp;</td>
		<!-- 11 --><td><?=$c_uvi;?></td>


	</tr>
	<tr>
		<!-- 01 --><td>WIND:</td>
		<!-- 02 --><td class="blankcolumn">&nbsp;</td>
		<!-- 03 --><td><?=$c_wind_speed;?><span style='font-size:1.9rem;line-height:40px;vertical-align: middle;float:right;margin-top:5px;'> MPH<br><?=$wind_direction;?></span></td>
		<!-- 04 --><td class="blankcolumn divider">&nbsp;</td>
		<!-- 05 --><td>TEMP:</td>
		<!-- 06 --><td class="blankcolumn">&nbsp;</td>
		<!-- 07 --><td><?= $c_temp; ?>° F</td>
		<!-- 08 --><td class="blankcolumn">&nbsp;</td>
		<!-- 09 --><td>INDEX:</td>
		<!-- 10 --><td class="blankcolumn">&nbsp;</td>
		<!-- 11 --><td><?php echo number_format($c_feels_like,0) .  "° F" ?></td>
	</tr>
	<tr>
		<!-- 01 --><td>SUNRISE:</td>
		<!-- 02 --><td class="blankcolumn">&nbsp;</td>
		<!-- 03 --><td><?=$c_sunrise;?></td>
		<!-- 04 --><td class="blankcolumn divider">&nbsp;</td>
		<!-- 05 --><td>HIGH:</td>
		<!-- 06 --><td class="blankcolumn">&nbsp;</td>
		<!-- 07 --><td><?=$d_temp_hi;?>° F</td>
		<!-- 08 --><td class="blankcolumn">&nbsp;</td>
		<!-- 09 --><td>LOW:</td>
		<!-- 10 --><td class="blankcolumn">&nbsp;</td>
		<!-- 11 --><td><?=$d_temp_lo;?>° F</td>
	</tr>
	<tr>
		<!-- 01 --><td>SUNSET:</td>
		<!-- 02 --><td class="blankcolumn">&nbsp;</td>
		<!-- 03 --><td><?=$c_sunset;?></td>
		<!-- 04 --><td class="blankcolumn divider">&nbsp;</td>
		<!-- 05 --><td>HUM:</td>
		<!-- 06 --><td class="blankcolumn">&nbsp;</td>
		<!-- 07 --><td><?= $c_humidity; ?> &#0037;</td>
		<!-- 08 --><td class="blankcolumn">&nbsp;</td>
		<!-- 09 --><td>DEW:</td>
		<!-- 10 --><td class="blankcolumn">&nbsp;</td>
		<!-- 11 --><td><?= $c_dew_point; ?>° F</td>

	</tr>
	<tr>
		<!-- 01 --><td>MOONRISE:</td>
		<!-- 02 --><td class="blankcolumn">&nbsp;</td>
		<!-- 03 --><td><?=$moonrise;?></td>
		<!-- 04 --><td class="blankcolumn divider">&nbsp;</td>
		<!-- 05 --><td>BAR:</td>
		<!-- 06 --><td class="blankcolumn">&nbsp;</td>
		<!-- 07 --><td><?= $c_pressure; ?></td>
		<!-- 08 --><td class="blankcolumn">&nbsp;</td>
		<!-- 09 --><td>RAIN:</td>
		<!-- 10 --><td class="blankcolumn">&nbsp;</td>
		<!-- 11 --><td><?=$daily_rain;?> IN</td>
	</tr>
</table>
</div>

<?php
/*
							 ____  _     _    _ ______   ____  _      ____   _____ _  __
							|  _ \| |   | |  | |  ____| |  _ \| |    / __ \ / ____| |/ /
							| |_) | |   | |  | | |__    | |_) | |   | |  | | |    | ' / 
							|  _ <| |   | |  | |  __|   |  _ <| |   | |  | | |    |  <  
							| |_) | |___| |__| | |____  | |_) | |___| |__| | |____| . \ 
							|____/|______\____/|______| |____/|______\____/ \_____|_|\_\
*/
?>
<div class="scroll_up">
	<table width="100%">
		<tr>
		<td width="25%" valign="top">
			<table class='watertable'>
				<tr><th colspan=6><span class='vendor_name'>USGS Water Services</span><br><br></th></tr>
				<tr><th>Location</th><th>River</th><th>Temp</th><th>Gage</th><th>Precip</th><th>Disc</th></tr>
				<tr><td>Cannelton Dam</td><td>Ohio</td><td><?=$temp_can?></td><td><?=$gage_can?></td><td><?=$precip_can?></td><td><?=$discharge_can?></td></tr>
				<tr><td>Falls of Rough</td><td>Rough</td><td><?=$temp_rough?></td><td><?=$gage_can?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
				<tr><td>White Cloud</td><td>Blue</td><td>&nbsp;</td><td><?=$gage_bw?></td><td>&nbsp;</td><td><?=$discharge_bw?></td></tr>
				<tr><td>Fredericksburg</td><td>Blue</td><td>&nbsp;</td><td><?=$gage_bf?></td><td>&nbsp;</td><td><?=$discharge_bf?></td></tr>
				<tr class='tablenote'><td colspan=6>Updated every 6 hours</td></tr>
			</table>
		</td>

		<td class='adblock' width="25%">
			<!--
			<span class='vendor_name'>Advertisement 1</span><br><br>
			Value 1<br><br>
			Value 2<br><br>
			Your Ad Here! ads@YOUR_DOMAIN
			-->
			<img src='ad1.png'>
			<span class='tablenote'>Your Keyword Links Here! <?php include("email.txt") ?></span>
		</td>

		<td class='adblock' width="25%">
			<img src='ad2.png'>
			<span class='tablenote'>Your Keyword Links Here! <?php include("email.txt") ?></span>
		</td>

		<td width="25%" valign="top">
			<h3>
				<span class='vendor_name'>What's Playing at the Malco</span><br><br>
				<?=$movie_table?>
			</h3>
		</td></tr>
	</table>

</div>

<?php
/*
				              ______ ____   ____ _______ ______ _____  
				             |  ____/ __ \ / __ \__   __|  ____|  __ \ 
				             | |__ | |  | | |  | | | |  | |__  | |__) |
				             |  __|| |  | | |  | | | |  |  __| |  _  / 
				             | |   | |__| | |__| | | |  | |____| | \ \ 
				             |_|    \____/ \____/  |_|  |______|_|  \_\
*/
	echo $footer_div;

?>

</body>
</html>
<?php
?>
