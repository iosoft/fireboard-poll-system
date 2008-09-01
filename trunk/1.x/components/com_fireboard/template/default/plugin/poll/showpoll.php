<?php
/**
* @adds Poll functionality to Fireboard forum component.
* @Fireboard Add-on
* @package Fireboard with Poll
* @author Mr. Ayan Debnath, INDIA. (a.k.a iosoft)
* @email ayan_don@hotmail.com (Please don't SPAM me)
* @version RC 4.0 showpoll.php 5.80KB 2007-12-25
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

// no direct access
defined('_VALID_MOS') or die('Restricted access');

// This sets the maximum pixels that it will take to represent the highest bar size.
// Chanhe it to fit with your template.
///////////////////////////////
$max_bar_pixels=150;  // in px
///////////////////////////////

global $poll_id, $my, $database;
$database->setQuery("SELECT title, fb_poll_type, published FROM #__polls WHERE id='{$poll_id}'");
$pollDetails=NULL;
$result=$database->loadObject($pollDetails);
if($pollDetails->published)
{
$pTitle=substr($pollDetails->title,3); /* Removing 'FB:' prefix */
$pType=$pollDetails->fb_poll_type;
?>
<link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/components/com_fireboard/template/default/plugin/poll/style.css" type="text/css">
<script language="Javascript">
function enableVote()
{
	document.voteform.vsubmit.disabled=false; /* for IE */
	document.getElementById("vsubmit").disabled=false; /* for non-IE */
}
function showResult(pollID)
{
	var exdate=new Date();
	expiredays=1;
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie="showResult_"+pollID+"=1; expires="+exdate.toGMTString();
	document.location.reload();
	return true;
}
</script >
<?php
global $fmessage, $id, $catid;
$query="SELECT count(*) as voted FROM #__fb_poll_result WHERE uid='{$my->id}' AND v_option IN (SELECT id FROM #__poll_data WHERE pollid='{$poll_id}')";
$database->setQuery($query);
$isVoted=(int)$database->loadResult();
if($_COOKIE["showResult_".$poll_id] || $isVoted || $my->id<1 || $fmessage->locked)  /* When to show the POLL Result */
{
$query="SELECT text, count(uid) AS votes FROM #__poll_data LEFT JOIN #__fb_poll_result ON id=v_option WHERE #__poll_data.pollid='{$poll_id}' AND text IS NOT NULL GROUP BY id ORDER BY id"; /* Most important SQL */
    
	$database->setQuery($query);
	$result=$database->loadObjectList();
	$num=count($result);
	if($num>0)
	{
		$i=0;
		$max = -1;
		$totalVotes=0;
		foreach ($result as $row)
		{
			$totalVotes+=$row->votes;
			if($row->votes > $max)
				$max = $row->votes;
			$i++;
		}
		?>
		<center>
		<table cellspacing="1"  border="0" class="fbstat">
		<col class="col1">
		<col class="col2">
		<col class="col3">
		<thead><tr>
			<th colspan="2" align="left"><?php echo $pTitle; ?></th>
			<th><div align="center"><b>%</b></div></th>
		</tr></thead>
		<tbody>		
		<?php
		$i=0;
		foreach ($result as $row)
		if(trim($row->text)!="")
		{
			$k=(($totalVotes>0) ? abs($row->votes/$max*$max_bar_pixels) : 0); /* Scalling the graph on scale */
			echo '<tr>';
			echo '<td>'.trim($row->text).'</td>';
			echo '<td style="width:'.$max_bar_pixels.'px;">'; /* Scalling the graph-column on scale */
			if($k)   echo '<img src="'.$mosConfig_live_site.'/components/com_fireboard/template/default/images/bar.gif" title="'.$row->votes.' vote(s)" alt="'.$row->votes.' vote(s)" style="margin-bottom:1px" height="15" width="'.$k.'">';
			echo '</td>';
			echo '<td><div align="center">';
			if($row->votes==$max)
				echo '<font color="red">';
			else
				echo '<font color="black">';
			echo (($totalVotes>0) ? round(($row->votes/$totalVotes * 100.0), 2) : 0);
			echo '%</font></div></td>';
			echo '</tr>';
		}
		echo '</tbody></table></center><div style="visibility:hidden;">Powered by <a href="http://gigahertz.byethost18.com/">GigaHertZ</a></div>';
	}
	if($_COOKIE["showResult_".$poll_id]==1) setcookie("showResult_".$poll_id, "", time()-3600);
} /* End of: Show the POLL Result */
else /* Show the POLL Form */
{
	$query="SELECT id, text FROM #__poll_data WHERE text IS NOT NULL AND pollid= ".$poll_id." ORDER BY id";
	$database->setQuery($query);
	$result=$database->loadObjectList();
	if(count($result) > 0)
	{
		?>
		<form method="post" action="<?php echo sefRelToAbs(JB_LIVEURLREL.'&func=post&do=addvote&id='.$id.'&catid='.$catid); ?>" name="voteform" id="voteform">
		<center>
		<input type="hidden" name="pID" id="pID" value="<?php echo $poll_id; ?>">
		<input type="hidden" name="userID" id="userID" value="<?php echo $my->id; ?>">
		<input type="hidden" name="pType" id="pType" value="<?php echo $pType; ?>">
		<table cellspacing="1"  border="0" class="fbstat">
		<col class="col2">
		<col class="col1">
		<thead><tr>
			<th colspan="2" align="left"><?php echo $pTitle; ?></th>
		</tr></thead>
		<tbody>
		<?php
		$i=0;
		foreach ($result as $row)
		if(trim($row->text)!="")
		{
			echo "<tr><td>";
			if($pType==1)
				echo '<input type="radio" name="myoption" value="'.$row->id.'" onClick="enableVote();">';
			else
				echo '<input type="checkbox" name="myoptions'.($i+1).'" id="myoptions'.($i+1).'" value="'.$row->id.'" onClick="enableVote();">';
			
			echo '</td><td width="100%">'.$row->text.'</td></tr>';
			$i++;
		}
		?>
		</tbody>
		</table>
		<br>
		<input type='submit' name='vsubmit' id='vsubmit' value='Vote' class='button' disabled>&nbsp;&nbsp;&nbsp;
		<input type='button' id='result' value='Show Result' class='button' onClick='showResult(<?php echo $poll_id; ?>);' >
		</center>
		</form><div style="visibility:hidden;">Powered by <a href="http://gigahertz.byethost18.com/">GigaHertZ</a></div>
		<?php
	} /* end of: if-count */
} /* end of: which report to print */
} /* end of: published/unpublished */
?>