<?php

if( @$_GET['ll'] )
{
	require_once( "phpcoord/phpcoord-2.3.php" );
	list( $lat,$long ) = preg_split( '/,/', preg_replace( '/\s+/','', $_GET["ll"] ));
	#print "<p>Lat,long: $lat $long</p>";
	
	$ll2w = new LatLng( $lat,$long );
	$ll2w->WGS84ToOSGB36();
	$os2w = $ll2w->toOSRef();
	
	$bt_e = 529218;
	$bt_n = 181928;
	
	$e = $os2w->easting;
	$n = $os2w->northing;
	
	$off_e = $e-$bt_e;
	$off_n = $n-$bt_n;
	
	#print "Easting: $e, Northing: $n<br />\n";
	#print "Noff: $off_n, Eoff: $off_e\n";
	#print "<hr />";
	$RAD_TO_DEG = 360 / (2 * 3.14159265359);
	$NORTH = 211;
	$CAMERA_HEIGHT = 183; # guestimate
	$effective_height = $CAMERA_HEIGHT - @$_GET["tallness"]*0.5;
	
	if( $off_n < 0 )
	{
		if( $off_e < 0 ) { $ang = $NORTH-90-atan(-$off_n/-$off_e) * $RAD_TO_DEG; }
		else { $ang = $NORTH+90+atan(-$off_n/$off_e) * $RAD_TO_DEG; }
	}
	else
	{
		if( $off_e < 0 ) { $ang = $NORTH-atan(-$off_e/$off_n) * $RAD_TO_DEG; }
		else { $ang = $NORTH+atan($off_e/$off_n) * $RAD_TO_DEG; }
	}
	$dist = round( sqrt( $off_n*$off_n + $off_e*$off_e ) );
	$vang = atan( $effective_height / $dist )*$RAD_TO_DEG;
	#print "ANG: ".$ang."<br />\n";	
	#print "DIST: ".$dist."m<br />";
	#print "VANG: ".$vang;
	
	if( $dist > 10000 )
	{
	#	print "<p>Nb. This is over 10km away from the BT tower, so may not be so easy to see.</p>";
	}

	$fov = 8;


	$url = "http://btlondon2012.co.uk/pano.html?view.hlookat=$ang&view.vlookat=$vang&view.fov=$fov";
	#print "<!DOCTYPE html>\n";
	$title = "$lat,$long";
	if( @$_GET["title"] )
	{
		$title = $_GET["title"];
	}
	print "<!DOCTYPE html>\n";
	print "<html style='height:100%;width:100%'>";
	print "<title>$title</title>";

	print "<body style='margin:0;height:100%;width:100%;overflow:hidden'>";

	print "<table style='height:100%;width:100%' cellpadding='0' cellspacing='0' border='1' >";
	print "<tr><td style='height:100%'>";
	print "<iframe src='$url' style='width:100%;height:100%;overflow:hidden'></iframe>";
	print "</td></tr>";

	print "<tr><td style='height:220px;'>";
	print "&nbsp;";
	print "</td></tr></table>";

	print "<div style='width:100%; position:fixed; height:220px; bottom:0px; left:0px; background-color:black'>";
	print "<div style='width:100%; overflow-x:scroll;white-space:pre'><nobr>";
	print "<img style='border-top:solid 2px black;border-right:solid 2px black;' src='http://maps.googleapis.com/maps/api/staticmap?&size=200x200&markers=color:blue%7Clabel:B%7C51.5215,-0.1389&markers=color:red%7Clabel:X%7C$lat,$long&sensor=false&maptype=hybrid' />";
	if( @$_GET["pic"] )
	{
		print "<img style='height:200px;border-left: solid 2px #000; border-top:solid 2px #000' src='".$_GET["pic"]."' />";
	}
	for( $h=0;$h<360;$h+=45 )
	{
		print "<img style='border-left: solid 2px black;border-top:solid 2px black' src='http://maps.googleapis.com/maps/api/streetview?size=200x200&location=$lat,$long&sensor=false&key=AIzaSyBBzPDPmNjo3F2sw-Zw-HAsR9aov0SX-_A&pitch=30&heading=$h' />";
	}
	#print "<div style='position:absolute;top:10px;left:204px;font-size:80%'><div style='padding:5px;background-color:#000; color:#fff'>North</div></div>";
	#print "<div style='position:absolute;top:10px;left:406px;font-size:80%'><div style='padding:5px;background-color:#000; color:#fff'>East</div></div>";
	#print "<div style='position:absolute;top:10px;left:608px;font-size:80%'><div style='padding:5px;background-color:#000; color:#fff'>South</div></div>";
	#print "<div style='position:absolute;top:10px;left:810px;font-size:80%'><div style='padding:5px;background-color:#000; color:#fff'>West</div></div>";
	print "</nobr>";
	print "</div>";

	print "</div>";



	print "</div>";
	print "</td></tr>";
	print "</table>"; 
	print "<div style='position:fixed;top:0;left:0px'><div style='padding:5px;margin: 5px; background-color:#000; color:#fff'>$title<br />".sprintf( "%0.1f",$dist/1000)."km</div></div>";
	print "</body>";
#http://btlondon2012.co.uk/pano.html?view.hlookat=6.7150&view.vlookat=4.1479&view.fov=5.8210
	exit;
}





$wt = "http://en.wikipedia.org/wiki/British_Library";
$postcode = "SW7 2AZ";
$lookup = false;
if( @$_GET["wikithing"] )
{
	$wt = urldecode($_GET["wikithing"] );
	$lookup=true;
}
if( @$_GET["postcode"] )
{
	$postcode = urldecode($_GET["postcode"] );
	$lookup=true;
}
print "<!DOCTYPE html>\n";
print "<html style='height:100%;width:100%'>";
print "
<img src='https://www.southampton.ac.uk/images/bg_logo_small.png' style='margin:15px;float:right' />
<h1>TowerHack</h1>
<p>A mash-up by <a href='http://users.ecs.soton.ac.uk/cjg/'>Christopher Gutteridge</a> (<a href='http://twitter.com/cgutteridge'>@cgutteridge</a>) at the <a href='http://www.soton.ac.uk/'>University of Southampton</a>. This uses Ordnance Survy postcode data or data from <a href='http://dbpedia.org/'>DBPedia</a> to try to work out the correct orientation on a panoramic picture of London taken from the BT Tower. (open data for the win!). Oh, and it throws in a Google map + street view n/s/e/w views and an image from wikipedia if it can find one.</p>

<form>
Lat/Long: <input size='30' value='51.538333, -0.013333' name='ll' /> <input type='submit' />
</form>
<form>
Postcode: <input size='9' value='".htmlspecialchars($postcode)."' name='postcode' />
<input type='submit' />
</form>

<form>
Wikipedia thing:
<input size='70' name='wikithing' value=\"".htmlspecialchars($wt)."\" />
<input type='submit' />
</form>
";
print "<p>PRO TIP: obviously tall things are more likely to be visible. Street level is obscured. The camera should center more or less on the ground location of the thing, but you may need to pan slightly in some cases, such is life.</p>";

if(!$lookup)
{
	print "<h2 style='margin:0px'>Quick list of things from wikipedia which are more or less visible:</h2>";
	readfile( "list.html" );
	print "<p>Thanks to Graeme Earl for the suggestion of this hacky little app.</p>";
	print "<p>For extra points, if wikipedia mentions the number of floors then the camera angle is adjusted to around half way up based on an estimated 4m per floor.</p>";
	exit;
}


require_once( "arc2/ARC2.php" );
require_once( "Graphite/Graphite.php" );

if( @$_GET["postcode"] )
{
	$postcode = urldecode($_GET["postcode"] );
	print "<p>$postcode</p>";
	$postcode = preg_replace( '/\s+/','',$postcode);
	$uri = "http://data.ordnancesurvey.co.uk/id/postcodeunit/$postcode";
	print "<p>URI: $uri</p>";
	$graph = new Graphite();
	$n = $graph->load( $uri );
	print "<p>Got $n triples</p>";
	if( $n==0) { exit; }
	$thing = $graph->resource( $uri );
	$title = $postcode;
	$lat = $thing->getLiteral( "geo:lat" );
	$long = $thing->getLiteral( "geo:long" );
	header( "Location: http://lemur.ecs.soton.ac.uk/~cjg/towerhack/?ll=$lat,$long&title=".urlencode($title)."&pic=".urlencode($pic) );
	exit;
}


print "<p>WIKI URL: $wt</p>";
$id = substr( $wt, 29 );
print "<p>WIKI ID: $id</p>";
$uri = "http://dbpedia.org/resource/$id";
print "<p>URI: $uri</p>";
$graph = new Graphite();
$n = $graph->load( $uri );
print "<p>Got $n triples</p>";
if( $n==0) { exit; }
$thing = $graph->resource( $uri );
if( ! ( $thing->has( "geo:lat" ) && $thing->has( "geo:long" ) ) )
{
	print "<p>No spatial info for this thing.</p>"; 
	if( !$thing->has( "http://dbpedia.org/ontology/wikiPageRedirects" ) )
	{
		#print $graph->dump();
		exit;
	}
	$newuri = $thing->get(  "http://dbpedia.org/ontology/wikiPageRedirects" );
	print "<p>NEWURI: $newuri</p>";
	$n = $graph->load( $newuri );
	print "<p>Got $n triples</p>";
	if( $n==0) { exit; }
	$thing = $graph->resource( $newuri );
	if( ! ( $thing->has( "geo:lat" ) && $thing->has( "geo:long" ) ) )
	{
		print "<p>Still no spatial data. Giving up.</p>";
		exit;
	}
}
$pic = "";
$tallness = 0;
$STOREY_HEIGHT = 4.0;
if( $thing->has( "http://dbpedia.org/ontology/floorCount" ) )
{
	$tallness = $thing->getLiteral( "http://dbpedia.org/ontology/floorCount" ) * $STOREY_HEIGHT;
}
	
if( $thing->has( "foaf:depiction" ) )
{
	$pic = $thing->get( "foaf:depiction" );
}
$title = $thing->label();
#$ll = "51.508056, -0.128056"; # trafal
#$ll = "51.5033, -0.1197"; # london eye
#$ll = "51.500705,-0.124575"; # big ben
#list( $lat,$long ) = preg_split( "/,/", $ll );
#print "($lat :: $long)<br />";
$lat = $thing->getLiteral( "geo:lat" );
$long = $thing->getLiteral( "geo:long" );
header( "Location: http://lemur.ecs.soton.ac.uk/~cjg/towerhack/?ll=$lat,$long&title=".urlencode($title)."&pic=".urlencode($pic)."&tallness=$tallness" );
exit;
