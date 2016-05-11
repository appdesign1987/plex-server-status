<?php

	$config_path = "/home/jeroen/config.bak.2"; //path to config file, recommend you place it outside of web root
	
	Ini_Set( 'display_errors', false);
	include("lib/phpseclib0.3.5/Net/SSH2.php");
	$config = parse_ini_file($config_path, true);
	
	$local_pfsense_ip = $config['network']['local_pfsense_ip'];
	$local_server_ip = $config['network']['local_server_ip'];
	$pfsense_if_name = $config['network']['pfsense_if_name'];
	$wan_domain = $config['network']['wan_domain'];
	//$plex_server_ip = $config['network']['plex_server_ip'];
	$ssh_username = $config['credentials']['ssh_username'];
	$ssh_password = $config['credentials']['ssh_password'];
	//$plex_username = $config['credentials']['plex_username'];
	//$plex_password = $config['credentials']['plex_password'];
	$forecast_api = $config['api_keys']['forecast_api'];
	$sabnzbd_api = $config['api_keys']['sabnzbd_api'];
	$weather_lat = $config['misc']['weather_lat'];
	$weather_long = $config['misc']['weather_long'];
	//$plex_port = $config['network']['plex_port'];
	//$zpools = $config['zpools'];
	$filesystems = $config['filesystems'];

	// Set the path for the Plex Token
//$plexTokenCache = '../misc/plex_token.txt';
// Check to see if the plex token exists and is younger than one week
// if not grab it and write it to our caches folder
//if (file_exists($plexTokenCache) && (filemtime($plexTokenCache) > (time() - 60 * 60 * 24 * 7))) {
	//$plexToken = file_get_contents("../misc/plex_token.txt");
//} else {
//	file_put_contents($plexTokenCache, getPlexToken());
//	$plexToken = file_get_contents("../misc/plex_token.txt");
//}
	

if (strpos(strtolower(PHP_OS), "Darwin") === false)
	$loads = sys_getloadavg();
else
	$loads = Array(0.55,0.7,1);

function getCpuUsage()
{
	$top = shell_exec('top -n 0');
	$findme = 'idle';
	$cpuIdleStart = strpos($top, $findme);
	$cpuIdle = substr($top, ($cpuIdleStart - 7), 2);
	$cpuUsage = 100 - $cpuIdle;
	return $cpuUsage;
}

function makeCpuBars()
{
	printBar(getCpuUsage(), "Usage");
}	


function byteFormat($bytes, $unit = "", $decimals = 2) {
	$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 
			'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
 
	$value = 0;
	if ($bytes > 0) {
		// Generate automatic prefix by bytes 
		// If wrong prefix given
		if (!array_key_exists($unit, $units)) {
			$pow = floor(log($bytes)/log(1024));
			$unit = array_search($pow, $units);
		}
 
		// Calculate byte value by prefix
		$value = ($bytes/pow(1024,floor($units[$unit])));
	}
 
	// If decimals is not numeric or decimals is less than 0 
	// then set default value
	if (!is_numeric($decimals) || $decimals < 0) {
		$decimals = 2;
	}
 
	// Format output
	return sprintf('%.' . $decimals . 'f '.$unit, $value);
  }

function makeDiskBars()
{
	global $filesystems;
	foreach ($filesystems as $fs_index => $fs_info){
		$fs = explode(",",$fs_info);
	
	printDiskBarGB(getDiskspace($fs[0]), $fs[1], getDiskspaceUsed($fs[0]), disk_total_space($fs[0]));
}
}

function makeRamBars()
{
	printRamBar(getFreeRam()[0],getFreeRam()[1],getFreeRam()[2],getFreeRam()[3]);
}

function makeLoadBars()
{
	printBar(getLoad(0), "1 min");
	printBar(getLoad(1), "5 min");
	printBar(getLoad(2), "15 min");
}

function getFreeRam()
{
	$top = shell_exec('free -m');
	$output = preg_split('/[\s]/', $top);
		for ($i=count($output)-1; $i>=0; $i--) {
		if ($output[$i] == '') unset ($output[$i]);
		}
	$output = array_values($output);
	$totalRam = $output[7]/1000; // GB
	$freeRam = $output[16]/1000; // GB
	$usedRam = $totalRam - $freeRam;
	return array (sprintf('%.0f',($usedRam / $totalRam) * 100), 'Used Ram', $usedRam, $totalRam);
}

function getDiskspace($dir)
{
	$df = disk_free_space($dir);
	$dt = disk_total_space($dir);
	$du = $dt - $df;
	return sprintf('%.0f',($du / $dt) * 100);
}

function getDiskspaceUsed($dir)
{
	$df = disk_free_space($dir);
	$dt = disk_total_space($dir);
	$du = $dt - $df;
	return $du;
}

/*function zpoolHealth($name) //returns status of provided zpool
{
	$zpool = shell_exec('/sbin/zpool status '.$name);
        $findme = 'state:';
        $stateStart = strpos($zpool, $findme);
        $health = (substr($zpool, $stateStart + 7, 8)); // GB
	return $health;
}	

function zfsFilesystems($zpool) //returns 2 dimensional array of all filesystems in provided zpool, with name, used space and available space
{
		$output = shell_exec('/sbin/zfs get -r -o name,value -Hp used,avail '.$zpool);
        $zfs_fs_stats = preg_split('/[\n|\t]/',$output);
        $zfs_fs_stats_p = array_pop($zfs_fs_stats);
		$zfs_fs_array = array_chunk($zfs_fs_stats,4);
		return $zfs_fs_array;
}

function printZpools()
{
	global $zpools;
	foreach ($zpools as $index => $name) {
	$status = zpoolHealth($name);
	$fs = zfsFilesystems($name);
	$fs_avail = $fs[0][3];
	$fs_used = $fs[0][1];
	#foreach($fs as $fs_ind => $fss) {
	#	$fs_used += $fss[1];
	#	}
	$fs_total = $fs_used + $fs_avail;
	$fs_pct = number_format(($fs_used / $fs_total)*100);
	$online = $status == "ONLINE" ? 'True' : 'False';
	$zp = new zpool($name, $status, $online);
	echo '<table>';
		echo '<tr>';
			echo '<td style="text-align: right; padding-right:5px;" class="exoextralight">'.$zp->name.': '.number_format($fs_pct, 0) .'%</td>';
			echo '<td style="text-align: left;">'.$zp->makeButton().'</td>';
		echo '</tr>';
		echo '</table>';
			echo '<div id="zfs_'.$zp->name.'" class="collapse">';
				echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . byteFormat($fs_used, "GB", 0) . ' / ' . byteFormat($fs_total, "GB", 0) . '" class="progress">';
					echo '<div class="progress">';
  					echo '<div class="progress-bar" style="width: '.$fs_pct.'%"></div>';
  					echo '<span class="sr-only">'.$fs_pct.'% Complete</span>';
  					echo '</div>';
  				echo '</div>';
			foreach($fs as $fs_ind => $fss){
				$fss_n = $fss[0];
				$fss_u = $fss[1];
				$fss_p = number_format(($fss_u / $fs_total)*100);
				echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="'.$fss_n.': ' . byteFormat($fss_u, "GB", 0) . '" class="progress">';
					echo '<div class="progress">';
  					echo '<div class="progress-bar progress-bar-success" style="width: '.$fss_p.'%"></div>';
  					echo '<span class="sr-only">'.$fss_p.'% Complete</span>';
  					echo '</div>';
  				echo '</div>';
				}
			echo '</div>';
		
	}
}*/


function getLoad($id)
{
	return 100 * ($GLOBALS['loads'][$id] / 8);
}

function printBar($value, $name = "")
{
	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($value, 0) . "%";
		echo '<div class="progress">';
			echo '<div class="progress-bar" style="width: ' . $value . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function printRamBar($percent, $name = "", $used, $total)
{
	if ($percent < 90)
	{
		$progress = "progress-bar";
	}
	else if (($percent >= 90) && ($percent < 95))
	{
		$progress = "progress-bar progress-bar-warning";
	}
	else
	{
		$progress = "progress-bar progress-bar-danger";
	}

	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($percent, 0) . "%";
		echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . number_format($used, 2) . ' GB / ' . number_format($total, 0) . ' GB" class="progress">';
			echo '<div class="'. $progress .'" style="width: ' . $percent . '%"></div>';
		echo '</div>';
	echo '</div>';
}


function printDiskBarGB($dup, $name = "", $dsu, $dts)
{
	if ($dup < 90)
	{
		$progress = "progress-bar";
	}
	else if (($dup >= 90) && ($dup < 95))
	{
		$progress = "progress-bar progress-bar-warning";
	}
	else
	{
		$progress = "progress-bar progress-bar-danger";
	}

	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($dup, 0) . "%";
		echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . byteFormat($dsu, "GB", 0) . ' / ' . byteFormat($dts, "GB", 0) . '" class="progress">';
			echo '<div class="'. $progress .'" style="width: ' . $dup . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function ping()
{
        $pingIP = '8.8.8.8';
        $avgPing = round(shell_exec("ping -c 5 " . $pingIP . " | grep dev | awk -F '/' '{print $5}'" ));
        return $avgPing;
}

function getNetwork() //returns wan_domain if you are outside your network, and local_server_ip if you are within the network
{
	global $local_server_ip;
	global $local_pfsense_ip;
	global $wan_domain;
	$clientIP = get_client_ip();
	if(preg_match("/192.168.88.*/",$clientIP))
		$network='http://'.$local_server_ip;
	else
		$network=$wan_domain;
	return $network;
}

function get_client_ip() 
{
	if ( isset($_SERVER["REMOTE_ADDR"])) { 
		$ipaddress = $_SERVER["REMOTE_ADDR"];
	}else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
		$ipaddress = $_SERVER["HTTP_CLIENT_IP"];
	} 
	return $ipaddress;
}




function makeBandwidthBars()
{
	$array = getBandwidth();
	$dPercent = sprintf('%.0f',($array[0] / 55) * 100);
	$uPercent = sprintf('%.0f',($array[1] / 5) * 100);
	printBandwidthBar($dPercent, 'Download', $array[0]);
	printBandwidthBar($uPercent, 'Upload', $array[1]);
}

function getBandwidth()
{
    global $local_pfsense_ip;
	global $ssh_username;
	global $ssh_password;
	global $pfsense_if_name;
	$ssh = new Net_SSH2($local_pfsense_ip);
	if (!$ssh->login($ssh_username,$ssh_password)) { // replace password and username with pfSense ssh username and password if you want to use this
		exit('Login Failed');
	}

	$dump = $ssh->exec('vnstat -i '.$pfsense_if_name.' -tr');
	$output = preg_split('/[\.|\s]/', $dump);
	for ($i=count($output)-1; $i>=0; $i--) {
		if ($output[$i] == '') unset ($output[$i]);
	}
	$output = array_values($output);
	$rxRate = $output[51];
	$rxFormat = $output[53];
	$txRate = $output[57];
	$txFormat = $output[59];
	if ($rxFormat == 'kbit/s') {
		$rxRateMB = $rxRate / 1024;
	} else {
		$rxRateMB = $rxRate;
	}
	if ($txFormat == 'kbit/s') {
		$txRateMB = $txRate / 1024;
	} else {
		$txRateMB = $txRate;
	}
	$rxRateMB = floatval($rxRateMB);
	$txRateMB = floatval($txRateMB);

	return  array($rxRateMB, $txRateMB);
}

function printBandwidthBar($percent, $name = "", $Mbps)
{
	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($Mbps,2) . " Mbps";
		echo '<div class="progress">';
			echo '<div class="progress-bar" style="width: ' . $percent . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function getDir($b)
{
   $dirs = array('N', 'NO', 'O', 'ZO', 'Z', 'ZW', 'W', 'NW', 'N');
   return $dirs[round($b/45)];
}

function makeWeatherSidebar()
{
    global $weather_lat;
	global $weather_long;
	global $forecast_api;
	$forecastExcludes = '?exclude=daily,flags&units=si&lang=nl';
	// Kennington, London
	$forecastLat = $weather_lat;
	$forecastLong = $weather_long;
	$currentForecast = json_decode(file_get_contents('https://api.forecast.io/forecast/'.$forecast_api.'/'.$forecastLat.','.$forecastLong.$forecastExcludes));

	$currentSummary = $currentForecast->currently->summary;
	$currentSummaryIcon = $currentForecast->currently->icon;
	$currentTemp = round($currentForecast->currently->temperature);
	$currentWindSpeed = round($currentForecast->currently->windSpeed);
	if ($currentWindSpeed > 0) {
		$currentWindBearing = $currentForecast->currently->windBearing;
	}
	$minutelySummary = $currentForecast->minutely->summary;
	$hourlySummary = $currentForecast->hourly->summary;
	// If there are alerts, make the alerts variables
	if (isset($currentForecast->alerts)) {
		$alertTitle = $currentForecast->alerts[0]->title;
		$alertExpires = $currentForecast->alerts[0]->expires;
		$alertDescription = $currentForecast->alerts[0]->description;
		$alertUri = $currentForecast->alerts[0]->uri;
	}
	// Make the array for weather icons
	$weatherIcons = [
		'clear-day' => 'B',
		'clear-night' => 'C',
		'rain' => 'R',
		'snow' => 'W',
		'sleet' => 'X',
		'wind' => 'F',
		'fog' => 'L',
		'cloudy' => 'N',
		'partly-cloudy-day' => 'H',
		'partly-cloudy-night' => 'I',
	];
	$weatherIcon = $weatherIcons[$currentSummaryIcon];
	// If there is a severe weather warning, display it
	//if (isset($currentForecast->alerts)) {
	//	echo '<div class="alert alert-warning alert-dismissable">';
	//	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
	//	echo '<strong><a href="'.$alertUri.'" class="alert-link">'.$alertTitle.'</a></strong>';
	//	echo '</div>';
	//}
	echo '<ul class="list-inline" style="margin-bottom:-20px">';
	echo '<li><h1 data-icon="'.$weatherIcon.'" style="font-size:500%;margin:0px -10px 20px -5px"></h1></li>';
	echo '<li><ul class="list-unstyled">';
	echo '<li><h1 class="exoregular" style="margin:0px">'.$currentTemp.'°</h1></li>';
	echo '<li><h4 class="exoregular" style="margin:0px;padding-right:10px;width:80px">'.$currentSummary.'</h4></li>';
	echo '</ul></li>';
	echo '</ul>';
	//if ($currentWindSpeed > 0) {
	//	$direction = getDir($currentWindBearing);
	//	echo '<h4 class="exoextralight" style="margin-top:0px">Wind: '.$currentWindSpeed.' mph ('.$direction.')</h4>';
	//}
	//echo '<h4 class="exoregular">Next Hour</h4>';
	//echo '<h5 class="exoextralight" style="margin-top:10px">'.$minutelySummary.'</h5>';
	echo '<h4 class="exoregular">Het weer t/m morgen</h4>';
	echo '<h5 class="exoextralight" style="margin-top:10px">'.$hourlySummary.'</h5>';
	echo '<p class="text-right no-link-color"><small><a href="http://forecast.io/#/f/',$forecastLat,',',$forecastLong,'">Forecast.io</a></small></p>';
}

?>
