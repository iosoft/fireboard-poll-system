<?php
/**
* @adds Poll functionality to Fireboard forum component.
* @Fireboard Add-on
* @package Fireboard with Poll
* @author Mr. Ayan Debnath, INDIA. (a.k.a iosoft)
* @email ayan_don@hotmail.com (Please don't SPAM me)
* @version RC 4.0 fb_poll_form.php 7.43KB 2007-12-25
* @Copyright (C) 2007 - 2008 Future iOsoft Technology,INDIA. All rights reserved
* @This work is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 2.5 India License.
* @license http://creativecommons.org/licenses/by-nc-nd/2.5/in/
*
* Based on Fireboard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
* @link http://www.bestofjoomla.com
**/

require_once('./../../../../../../configuration.php'); /* Don't change, Keep it like this */
if(!session_id()) session_start();
if(isset($_SESSION['kEy'])) /* to Stop Direct Access */
{
?>
<html>
<head><Title>Forum-Poll form</Title>
<link rel="icon" href="<?php echo $mosConfig_live_site; ?>/components/com_fireboard/template/default/images/english/emoticons/poll.gif" type="image/gif">
<link rel="shortcut icon" href="<?php echo $mosConfig_live_site; ?>/components/com_fireboard/template/default/images/english/emoticons/poll.gif" type="image/gif">
<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/components/com_fireboard/template/default/plugin/poll/style.css" type="text/css">
<?php
if($_POST) /* Process the FORM */
{
	$dbhost = $mosConfig_host;
	$dbuser = $mosConfig_user;
	$dbpass = $mosConfig_password;
	$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('<font color="red">Error connecting to Database</font>');
	$dbname = $mosConfig_db;
	mysql_select_db($dbname);
	////////////////////////////
	mysql_query("BEGIN");
	////////////////////////////
	$title = 'FB:'.$_POST['polltitle'];
	$pType = $_POST['polltype'];
	$query="INSERT INTO ".$mosConfig_dbprefix."polls (title, published, access, lag, fb_poll_type) VALUES ('".$title."', 0, 0, 86400, ".$pType.")";
	mysql_query($query) or die('<font color="red">Error, insert query failed</font>');
	////////////////////////////
	$poll_ID=mysql_insert_id();
	////////////////////////////
	$pOptions[0] = $_POST['polloption1'];$pOptions[1] = $_POST['polloption2'];$pOptions[2] = $_POST['polloption3'];$pOptions[3] = $_POST['polloption4'];$pOptions[4] = $_POST['polloption5'];$pOptions[5] = $_POST['polloption6'];$pOptions[6] = $_POST['polloption7'];$pOptions[7] = $_POST['polloption8'];$pOptions[8] = $_POST['polloption9'];$pOptions[9] = $_POST['polloption10'];$pOptions[10] = $_POST['polloption11'];$pOptions[11] = $_POST['polloption12'];
	for($i=0,$insQ="";$i<12;$i++)
	{
		$pOptions[$i]=trim($pOptions[$i]);
		if($pOptions[$i]!="")
		{		
			$query="INSERT INTO ".$mosConfig_dbprefix."poll_data (pollid, text) VALUES (".$poll_ID.",'".$pOptions[$i]."')"; 
			$result=mysql_query($query);
		}
	}
	mysql_query("COMMIT");
	////////////////////////////
	$_SESSION['kEy']=""; session_unset(); /* Destroying the Session */
	mysql_close($conn);
	?> 
	<script language="javascript">
		/* Success: return info */
		opener.document.getElementById("pollID").value = <?php echo $poll_ID; ?>;
		opener.document.getElementById("addPoll").checked=true;
		opener.document.getElementById("addPoll").disabled=true;
		opener.document.getElementById("addPollBt").disabled=true;
		opener.document.getElementById("PollStatus").innerHTML="<font color='green'><b>Poll has been successfully attached.</b></font>";
		self.close();
	</script>
	<?php
	exit;
} /* End of Submit process */
else /* Show the FORM */
{
?>
<script language="Javascript">
function validateMe()
{
	if(window.opener==null  ||  window.opener.document.getElementById("Token1").value!="<?php echo md5($_SESSION['kEy']); ?>") /* Hacking */
		document.getElementById("form_content").innerHTML="Direct Access to this location is not allowed.";
		
	document.getElementById("form_content").style.visibility="visible";
}
function validateForm()
{
	valid = true;
	if(document.pollform.polltitle.value=="")
	{
		alert("Missing Poll Title." );
		valid = false;
	}
	if(document.pollform.polloption1.value=="" || document.pollform.polloption2.value=="")
	{
		alert("Atleast 2 Options are MUST.");
		valid = false;
	}
	document.pollform.polltitle.focus();
	return valid;
}
function cancelMe()
{
	/* Cancel: return info */
	if(opener.document.getElementById("pollID").value=="") /* Form not been processed */
	{
		opener.document.getElementById("addPoll").checked=false;
		opener.document.getElementById("PollStatus").innerHTML="You have canceled the Poll.<br>Try again if you want...";
	}
}
</script>
</head>
<body onload="validateMe();" onunload="cancelMe();">
<div id="form_content" style="visibility: hidden;">
<form name="pollform" method="post" action="fb_poll_form.php" onSubmit="return validateForm()">
<table border="0" style="width:390px;" id="toolbar">
<tr>
	<td width="100%"><h3>Forum Poll: NEW</h3></td>
	<td><a href="#" onclick="document.pollform.submit();" class="toolbar">
			<img src="<?php echo $mosConfig_live_site; ?>/images/apply_f2.png" alt="Submit" title="Submit" border="0"></a></td>
	<td><a href="#" onclick="cancelMe(); self.close();" class="toolbar"><img src="<?php echo $mosConfig_live_site; ?>/images/cancel_f2.png" alt="Cancel" title="Cancel" border="0"></a></td>
</tr>
</table>
<table cellspacing="1"  border="0" class="fbstat" style="width:400px;" >
<col class="col1">
<col class="col1">
<col class="col2">
	<thead>
	<tr>
		<th colspan="3">Details</th>
	<tr>
	</thead>
	
	<tbody>
	<tr>
		<td>
			<font color="red">Title:</font>
		</td><td colspan="2">
			<input type="text" name="polltitle" size="50">
		</td>
	</tr><tr>
		<td>
			Poll Type:
		</td><td colspan="2">
			<input type="radio" name="polltype" value="1" checked>Single Choice
			<br>
			<input type="radio" name="polltype" value="9">Multiple Choice
		</td>
	</tr><tr>
		<td colspan="3">
			<br>Options:
		</td>	
	</tr><tr>
		<td></td><td><font color="red">1.</font></td><td><input type="text" name="polloption1" size="40"></td>
	</tr><tr>
		<td></td><td><font color="red">2.</font></td><td><input type="text" name="polloption2" size="40"></td>
	</tr><tr>
		<td></td><td>3.</td><td><input type="text" name="polloption3" size="40"></td>
	</tr><tr>
		<td></td><td>4.</td><td><input type="text" name="polloption4" size="40"></td>
	</tr><tr>
		<td></td><td>5.</td><td><input type="text" name="polloption5" size="40"></td>
	</tr><tr>
		<td></td><td>6.</td><td><input type="text" name="polloption6" size="40"></td>
	</tr><tr>
		<td></td><td>7.</td><td><input type="text" name="polloption7" size="40"></td>
	</tr><tr>
		<td></td><td>8.</td><td><input type="text" name="polloption8" size="40"></td>
	</tr><tr>
		<td></td><td>9.</td><td><input type="text" name="polloption9" size="40"></td>
	</tr><tr>
		<td></td><td>10.</td><td><input type="text" name="polloption10" size="40"></td>
	</tr><tr>
		<td></td><td>11.</td><td><input type="text" name="polloption11" size="40"></td>
	</tr><tr>
		<td></td><td>12.</td><td><input type="text" name="polloption12" size="40"></td>
	</tr>
	</tbody>
</table>
<br><hr>
</form>
</div>
</body>
</html>
<?php
} /* End of Form */
} /* End of Direct Access */
else 
	die('Restricted access');
?>