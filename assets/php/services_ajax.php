<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include("functions.php");
	include("service.class.php");
	include("serviceSAB.class.php");
?>
<html lang="en">
	<script>
	// Enable bootstrap tooltips
	$(function ()
	        { $("[rel=tooltip]").tooltip();
	        });
	</script>
<?php 
$sabnzbdXML = simplexml_load_file('http://couch.jeroenvd.nl:8080/api?mode=qstatus&output=xml&apikey='.$sabnzbd_api);

if (($sabnzbdXML->state) == 'Downloading'):
	$timeleft = $sabnzbdXML->timeleft;
	$sabTitle = 'SABnzbd ('.$timeleft.')';
else:
	$sabTitle = 'SABnzbd';
endif;

$services = array(
	new service("Plex", 32400, "http://192.168.88.55:32400/web/index.html#!/dashboard"),
	new service("Donald", 8006, "http://192.168.1.200:8006"),
	new service("Dagobert", 8006, "http://192.168.1.81:8006"),
	new serviceSAB($sabTitle, 8080, "http://192.168.88.55:8080"),
	new service("Deluge", 8112, "http://192.168.88.55:8112"),
	new service("CouchPotato", 5050, "http://192.168.88.55:5050")
	#new service("Transmission", 9091, "http://d4rk.co:9091"),
	//new service("Subsonic",4040, "http://dashbad.com:4040")
	
);
?>
<table class ="center">
	<?php foreach($services as $service){ ?>
		<tr>
			<td style="text-align: right; padding-right:5px;" class="exoextralight"><?php echo $service->name; ?></td>
			<td style="text-align: left;"><?php echo $service->makeButton(); ?></td>
		</tr>
	<?php }?>
</table>
