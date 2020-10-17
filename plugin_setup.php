<?php


include_once "/opt/fpp/www/common.php";
include_once "functions.inc.php";
include_once "commonFunctions.inc.php";
include_once "MatrixFunctions.inc.php";


include_once 'version.inc';
$pluginName = basename(dirname(__FILE__));

//2.3 - Dec 4 2016 - Remove the mulitple demand messages code - was messing up

//2.2 - Dec 2 2016 - Abaility to send multiple on demand messages and have them send in a stream!

$fpp_matrixtools_Plugin = "fpp-matrixtools";
$fpp_matrixtools_Plugin_Script = "scripts/matrixtools";
$FPP_MATRIX_PLUGIN_ENABLED=false;
$logFile = $settings['logDirectory']."/".$pluginName.".log";

$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";


$gitURL = "https://github.com/FalconChristmas/FPP-Plugin-Matrix-Message.git";

logEntry("plugin update file: ".$pluginUpdateFile);

if(isset($_POST['updatePlugin']))
{
	logEntry("updating plugin...");
	$updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

	echo $updateResult."<br/> \n";
}


if(isset($_POST['submit']))
{
	$PLUGINS =  implode(',', $_POST["PLUGINS"]);

//	echo "Writring config fie <br/> \n";
	WriteSettingToFile("PLUGINS",$PLUGINS,$pluginName);
	
//	WriteSettingToFile("ENABLED",urlencode($_POST["ENABLED"]),$pluginName);
	WriteSettingToFile("FONT",urlencode($_POST["FONT"]),$pluginName);
    WriteSettingToFile("FONT_ANTIALIAS",urlencode($_POST["FONT_ANTIALIAS"]),$pluginName);
	WriteSettingToFile("FONT_SIZE",urlencode($_POST["FONT_SIZE"]),$pluginName);
    WriteSettingToFile("PIXELS_PER_SECOND",urlencode($_POST["PIXELS_PER_SECOND"]),$pluginName);
    WriteSettingToFile("DURATION",urlencode($_POST["DURATION"]),$pluginName);
	WriteSettingToFile("COLOR",urlencode($_POST["COLOR"]),$pluginName);
	WriteSettingToFile("FONT_SIZE",urlencode($_POST["FONT_SIZE"]),$pluginName);

	// @spadaman added.
	WriteSettingToFile("MAX_MESSAGES_PER_RUN",urlencode($_POST["MAX_MESSAGES_PER_RUN"]),$pluginName);
	WriteSettingToFile("EFFECT_FOR_DISPLAY",urlencode($_POST["EFFECT_FOR_DISPLAY"]),$pluginName);
	
	WriteSettingToFile("LAST_READ",urlencode($_POST["LAST_READ"]),$pluginName);
    if (isset($_POST["MESSAGE_TIMEOUT"])) {
        WriteSettingToFile("MESSAGE_TIMEOUT",urlencode($_POST["MESSAGE_TIMEOUT"]),$pluginName);
    } else {
        WriteSettingToFile("MESSAGE_TIMEOUT", 10, $pluginName);
    }
    
	WriteSettingToFile("MATRIX",urlencode($_POST["MATRIX"]),$pluginName);
    if (isset($_POST["INCLUDE_TIME"])) {
        WriteSettingToFile("INCLUDE_TIME",urlencode($_POST["INCLUDE_TIME"]),$pluginName);
    } else {
        WriteSettingToFile("INCLUDE_TIME", 0, $pluginName);
    }
	WriteSettingToFile("TIME_FORMAT",urlencode($_POST["TIME_FORMAT"]),$pluginName);
	WriteSettingToFile("HOUR_FORMAT",urlencode($_POST["HOUR_FORMAT"]),$pluginName);	
	WriteSettingToFile("OVERLAY_MODE",urlencode($_POST["OVERLAY_MODE"]),$pluginName);
    
    $pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
    if (file_exists($pluginConfigFile)) {
        $pluginSettings = parse_ini_file($pluginConfigFile);
    }
}

	
	
	
//	$PLUGINS = urldecode(ReadSettingFromFile("PLUGINS",$pluginName));
$PLUGINS = $pluginSettings['PLUGINS'];
//	$ENABLED = urldecode(ReadSettingFromFile("ENABLED",$pluginName));
$ENABLED = $pluginSettings['ENABLED'];
//	$Matrix = urldecode(ReadSettingFromFile("MATRIX",$pluginName));
$Matrix = urldecode($pluginSettings['MATRIX']);
//$MatrixHost = urldecode($pluginSettings['MATRIX_HOST']);
//if (!isset($MatrixHost) || $MatrixHost == "") {
//    $MatrixHost = $_SERVER['SERVER_ADDR'];
//}
//	$LAST_READ = urldecode(ReadSettingFromFile("LAST_READ",$pluginName));
$LAST_READ = $pluginSettings['LAST_READ'];
$FONT= urldecode($pluginSettings['FONT']);
$FONT_SIZE= $pluginSettings['FONT_SIZE'];
if (!isset($FONT_SIZE) || $FONT_SIZE == "") {
    $FONT_SIZE = 20;
}
$FONT_ANTIALIAS= $pluginSettings['FONT_ANTIALIAS'];
$PIXELS_PER_SECOND= $pluginSettings['PIXELS_PER_SECOND'];
$DURATION=10;
if (isset($pluginSettings['DURATION'])) {
    $DURATION=$pluginSettings['DURATION'];
}
$COLOR= urldecode($pluginSettings['COLOR']);

$INCLUDE_TIME = urldecode($pluginSettings['INCLUDE_TIME']);
$TIME_FORMAT = urldecode($pluginSettings['TIME_FORMAT']);
$HOUR_FORMAT = urldecode($pluginSettings['HOUR_FORMAT']);

//@spadaman added.
$MAX_MESSAGES_PER_RUN = $pluginSettings['MAX_MESSAGES_PER_RUN'];
$EFFECT_FOR_DISPLAY = $pluginSettings['EFFECT_FOR_DISPLAY'];

if (isset($pluginSettings['DEBUG'])) {
    $DEBUG = urldecode($pluginSettings['DEBUG']);
} else {
    $DEBUG = false;
}
$overlayMode = urldecode($pluginSettings['OVERLAY_MODE']);

if($overlayMode == "") {
	$overlayMode = "1";
}
	
	if(file_exists($pluginDirectory."/".$fpp_matrixtools_Plugin."/".$fpp_matrixtools_Plugin_Script))
	{
		logEntry($pluginDirectory."/".$fpp_matrixtools_Plugin."/".$fpp_matrixtools_Plugin_Script." EXISTS: Enabling");
		$FPP_MATRIX_PLUGIN_ENABLED=true;

		createMatrixEventFile();
	} else {
		logEntry("FPP Matrix tools plugin is not installed, cannot use this plugin with out it");
		echo "FPP Matrix Tools plugin is not installed. Install the plugin and revisit this page to continue";
		exit(0);
	
	}

?>

<script language="Javascript">
function updateMatrixList() {
    var host = $('#MatrixHost').val();
    var url = 'http://' + host + '/api/overlays/models';
    $('#MATRIX').empty();
	$.ajax({
		url: url,
		dataType: 'json',
		success: function(data) {
           data.forEach(function (element, index) {
                        var o = new Option(element.Name, element.Name);
                        $(o).html(element.Name);
                        $('#MATRIX').append(o);
                    });
		}
	});
}
</script>

<div id="<?echo $pluginName;?>" class="settings">
<fieldset>
<legend><?php echo $pluginName." Version: ".$pluginVersion;?> Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE</li>
</ul>
<p>Configuration:
<ul>
<li>This plugin allows you to use the fpp-matrixtools plugin to output messages from the MessageQueue system</li>
<li>Select your plugins to output to your matrix below and click SAVE</li>
<li>Configure your Matrix first before selecting here</li>
</ul>



<form method="post" action="/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">


<?
echo "<input type=\"hidden\" name=\"LAST_READ\" value=\"".$LAST_READ."\"> \n";
$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";

//if($ENABLED== 1 || $ENABLED == "on") {
//		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
PrintSettingCheckbox("Matrix Message Plugin", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
//	} else {
//		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
//}

echo "<p/> \n";

// Does not work yet as we need to enable CORS on the api's
// echo "Matrix Host: ";
// echo "<input type='text' name='hostname' value='$MatrixHost' id='MatrixHost' onChange='updateMatrixList()'>";
// echo "<p/>\n";

echo "Matrix Name: ";
PrintMatrixList("MATRIX",$Matrix);

echo "<p/>\n";

echo "Overlay Mode: ";

PrintOverlayMode($overlayMode);


echo "<p/>\n";

echo "Include Time: ";

if($INCLUDE_TIME == 1 || $INCLUDE_TIME == "on") {
	echo "<input type=\"checkbox\" checked name=\"INCLUDE_TIME\"> \n";
	//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
} else {
	echo "<input type=\"checkbox\"  name=\"INCLUDE_TIME\"> \n";
}

echo "Time Format: ";
printTimeFormats("TIME_FORMAT",$TIME_FORMAT);


echo "Hour Format: ";
printHourFormats("HOUR_FORMAT",$HOUR_FORMAT);




echo "<p/> \n";
echo "Include Plugins in Matrix output: \n";
printPluginsInstalled();

echo "<p/> \n";

echo "Font:  \n";
printFontsInstalled("FONT",$FONT);

echo "<p/> \n";
echo "Font Size: \n";
printFontSizes("FONT_SIZE",$FONT_SIZE);
$aachecked = "";
if (isset($FONT_ANTIALIAS) && $FONT_ANTIALIAS == "1") {
    $aachecked = "checked";
}
echo "<input type='checkbox' name='FONT_ANTIALIAS' value='1' $aachecked >Anti-Aliased</input>";
echo "<p/> \n";

echo "Pixels per second: \n";
printPixelsPerSecond("PIXELS_PER_SECOND",$PIXELS_PER_SECOND);
echo "<p/> \n";
echo "Duration (s): \n";
echo "<input name='DURATION' type='number' min='0' max='300' value='" . $DURATION . "'/>";
echo "<p/> \n";

echo "Color: (#RRGGBB or common name 'red' or for a random color type 'random') \n";

if($COLOR == "") {
	//set a default color
	$COLOR = "yellow";
}
echo "<input type=\"text\" name=\"COLOR\" value=\"".$COLOR."\"> \n";
echo "<p/> \n";

//@spadaman added.
echo "<p/> \n";
echo "Max messages per run: \n";
echo "<input name='MAX_MESSAGES_PER_RUN' type='number' min='0' max='300' value='" . $MAX_MESSAGES_PER_RUN . "'/>";
echo "<p/> \n";
echo "<p/> \n";
echo "Effect to display when putting a message on the matrix: \n";
echo "<input name='EFFECT_FOR_DISPLAY' type='text' value='" . $EFFECT_FOR_DISPLAY . "'/>";
echo "<p/> \n";




//echo "<hr> \n";
//echo "Example text: \n";
//echo "<hr/> \n";
//$messageText="Font: ".$FONT." Example";
//echo "<marquee behavior=\"scroll\" scrollamount=\"5\" direction=\"left\" onmouseover=\"this.stop();\" onmouseout=\"this.start();\">\n";
//echo "<font face=\"".$FONT."\" size=\"+".$FONT_SIZE."\" color=\"".$COLOR."\"> \n";

//echo preg_replace('!\s+!', ' ', $messageText);
//echo "</font> \n";
//echo "</marquee> \n";
?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
<?
 if(file_exists($pluginUpdateFile))
 {
 	//echo "updating plugin included";
	include $pluginUpdateFile;
}
?>
<p>To report a bug, please file it against <?php echo $gitURL;?>
</form>

<form method="post" action="/plugin.php?plugin=<?echo $pluginName;?>&page=fontManagement.php">
<input id="fontManagement" name="Font Management" type="submit" value="Font Management">
</form>


</fieldset>
</div>
<br />
</html>

