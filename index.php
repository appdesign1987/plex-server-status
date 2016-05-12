<!DOCTYPE html>
<?php
	Error_Reporting( E_ALL | E_STRICT );
	Ini_Set( 'display_errors', true);

	include("assets/php/functions.php");
	include('assets/php/Mobile_Detect.php');

	$detect = new Mobile_Detect;
	//$plexSessionXML = simplexml_load_file($config['network']['plex_server_ip'].'/status/sessions');
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Jeroenvd.nl - Dashboard</title>
		<meta name="author" content="dash">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Le styles -->
		<link href="assets/fonts/stylesheet.css" rel="stylesheet">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/css/bootstrap.min.css">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css" rel="stylesheet">
		<style type="text/css">
			body {
				text-align: center;
				/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#d4e4ef+0,86aecc+100;Grey+Gloss */
background: #d4e4ef; /* Old browsers */
background: -moz-linear-gradient(top,  #d4e4ef 0%, #86aecc 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top,  #d4e4ef 0%,#86aecc 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  #d4e4ef 0%,#86aecc 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d4e4ef', endColorstr='#86aecc',GradientType=0 ); /* IE6-9 */

			}
			.center {
				margin-left:auto;
				margin-right:auto;
			}
			.no-link-color 
				a {
					color:#999999;
				}
				a:hover {
					color:#999999;	
				}
			
			.exoextralight {
				font-family:"exoextralight";
			}
			.exolight {
				font-family:"exolight";
			}
			[data-icon]:before {
				font-family: 'MeteoconsRegular';
				content: attr(data-icon);
			}
			.exoregular {
				font-family:"exoregular";
			}
			/* Changes carousel slide transition to fade transition */
			.carousel {
				overflow: hidden;
			}
			.carousel .item {
				-webkit-transition: opacity 1s;
				-moz-transition: opacity 1s;
				-ms-transition: opacity 1s;
				-o-transition: opacity 1s;
				transition: opacity 1s;
			}
			.carousel .active.left, .carousel .active.right {
				left:0;
				opacity:0;
				z-index:2;
			}
			.carousel .next, .carousel .prev {
				left:0;
				opacity:1;
				z-index:1;
			}
			/* Disables shadowing on right and left sides of carousel images for a crisp look */
			.carousel-control.left {
				background-image: none;
			}
			.carousel-control.right {
				background-image: none;
			}
		</style>
		<link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon.png" />
		<link rel="shortcut icon" href="assets/ico/favicon.ico">
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="//code.jquery.com/jquery.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/js/bootstrap.min.js"></script>
		<script>
		// Enable bootstrap tooltips
		$(function () { 
			$("[rel=tooltip]").tooltip();
			$("[rel=popover]").popover();
			}); 
		// Auto refresh things
		(function($) {
			$(document).ready(function() {
				$.ajaxSetup({
		            		cache: false,
		            		beforeSend: function() {
		            			$('#left_column_top').show();
		            			$('#bandwidth').show();
		            			$('#ping').show();
		            			$('#services').show();
						$('#system_load').show();
						$('#disk_space').show();
						$('#other_disks').show();
						$('#system_ram').show();
		            		},
				            complete: function() {
				            	$('#left_column_top').show();
				            	$('#bandwidth').show();
				            	$('#ping').show();
				            	$('#services').show();
						$('#system_load').show();
						$('#disk_space').show();
						$('#other_disks').show();
						//$('#now_playing_title').show();
						//$('#now_playing').show();
						$('#system_ram').show();
						//$('#plex_movie_stats').show();
				            },
				            success: function() {
				            	$('#left_column_top').show();
				            	$('#bandwidth').show();
				            	$('#ping').show();
				            	$('#services').show();
						$('#system_load').show();
						$('#disk_space').show();
						$('#other_disks').show();
						$('#system_ram').show();
				            }
				});

				// Assign varibles to DOM sections
				var $left_column_top_refresh = $('#left_column_top');
				var $bandwidth_refresh = $('#bandwidth');
				var $ping_refresh = $('#ping');
				var $services_refresh = $('#services');
			        	var $system_load_refresh = $('#system_load');
			        	var $disk_space_refresh = $('#disk_space');
			        	var $other_disks_refresh = $('#other_disks');
			        	var $system_ram_refresh = $('#system_ram');

			        	// Load external php files & assign variables
			        	$left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
			        	$bandwidth_refresh.load("assets/php/bandwidth_ajax.php");
			        	$ping_refresh.load("assets/php/ping_ajax.php");
			        	$services_refresh.load("assets/php/services_ajax.php");
			        	$system_load_refresh.load("assets/php/system_load_ajax.php");
			        	$disk_space_refresh.load("assets/php/disk_space_ajax.php");
			        	$other_disks_refresh.load("assets/php/other_disks_ajax.php");
			      		$system_ram_refresh.load("assets/php/system_ram_ajax.php");
			        
			        	var refreshIdfastest = setInterval(function(){
			            	$system_load_refresh.load('assets/php/system_load_ajax.php');
			        	}, 5000); // 5 seconds

			        	var refreshId30 = setInterval(function(){
			        		$bandwidth_refresh.load("assets/php/bandwidth_ajax.php");
			        		$ping_refresh.load("assets/php/ping_ajax.php");
			        		$services_refresh.load("assets/php/services_ajax.php");
			        	}, 30000); // 30 seconds

			        	var refreshId60 = setInterval(function(){
			        		
			        	}, 60000); // 60 seconds

			        	var refreshIdslow = setInterval(function(){
			            	$disk_space_refresh.load('assets/php/disk_space_ajax.php');
			            	$system_ram_refresh.load('assets/php/system_ram_ajax.php')
			        	}, 300000); // 5 minutes

			        	var refreshtopleft = setInterval(function(){
			            	$left_column_top_refresh.load('assets/php/left_column_top_ajax.php');
			        	}, 300000); // 5 minutes

				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					// some code..
				} else {
					var resizeTimer;
					$(window).resize(function() {
						clearTimeout(resizeTimer);
						resizeTimer = setTimeout(doResizeNowPlaying, 100);
					});

					$(function(){
	   					clearTimeout(resizeTimer);
						resizeTimer = setTimeout(doResizeNowPlaying, 100);
					});
				}
		    	});
		})(jQuery);
		</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<!-- Left sidebar -->
						<div class="col-md-3" style="padding-top: 20px;">
							<!-- Weather-->
							<div class="panel panel-default">
							<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Weer
									</h4>
							</div>		
								<div class="panel-body">	
									<div id="left_column_top"></div>
								</div>
							</div>
							<!-- Bandwidth -->
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										<span id="ping" class="badge pull-right" rel="tooltip" data-toggle="tooltip" data-placement="left" title="Ping"></span>
										Bandwidth
									</h4>
								</div>
								<div class="panel-body" style="height:150px">
									<div id="bandwidth"></div>
								</div>
							</div>
							<!-- Services -->
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Services
									</h4>
								</div>
								<div id="services" class="panel-body">
								</div>
							</div>
						</div>
						<!-- Center Area -->
						<div class="col-md-6">
							<div id="now_playing_title"></div>
							<?php	echo '<div id="now_playing"';
							// Check to see if we're on a mobile device because overflow scrolling sucks on them.
							// If we're on a cellphone disable the overflow:auto feature.
							if ( $detect->isMobile() ):
								echo '>';
							else:
								echo ' style="overflow:auto;">';
							endif;
								echo '</div>';?>
							<hr class="visible-xs">
							<hr>
						</div>
						<!-- Right sidebar -->
						<?php echo '<div class="col-md-3"';
						// Only apply padding on top of this column if its not on a mobile device
						if ( $detect->isMobile() ):
							echo '>';
						else:
							echo ' style="padding-top: 20px;">';
						endif;?>
							<!-- Server info -->
							<div class="panel panel-default">
							<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Server Info
									</h4>
									</div>
								<div class="panel-body">
									<h4 class="exoextralight">Load</h4>
									<div id="system_load"></div>
									<hr>
									<h4 class="exoextralight">Memory</h4>
									<div id="system_ram" style="height:40px"></div>
									<hr>
									<h4 class="exoextralight">Local Disk space</h4>
									<div id="disk_space"></div>
									<hr>
									<h4 class="exoextralight">Other Disks</h4>
									<div id="other_disks"></div>
								</div>
							</div>
							<div class="panel panel-default">
							<div class="panel-heading">
									<h4 class="panel-title exoextralight">
										Hier komt nog wat
									</h4>
									</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Invisible php div-->
		</body>
</html>
