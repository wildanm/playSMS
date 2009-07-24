<?
if(!valid()){forcenoaccess();};

switch ($op)
{
    case "sms_custom_list":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Manage custom</h2>
	    <p>
	    <input type=button value=\"Add SMS custom\" onClick=\"javascript:linkto('menu.php?inc=feature_sms_custom&op=sms_custom_add')\" class=\"button\" />
	    <p>
	";
	if (!isadmin())
	{
	    $query_user_only = "WHERE uid='$uid'";
	}
	$db_query = "SELECT * FROM "._DB_PREF_."_featureCustom $query_user_only ORDER BY custom_keyword";
	$db_result = dba_query($db_query);
	$content .= "
    <table cellpadding=1 cellspacing=2 border=0 width=100%>
    <tr>
        <td class=box_title width=25>*</td>
        <td class=box_title width=100>Keyword</td>
        <td class=box_title>Exec</td>
        <td class=box_title width=100>User</td>	
        <td class=box_title width=75>Action</td>
    </tr>
	";
	$i=0;
	$maxlen=50;
	while ($db_row = dba_fetch_array($db_result))
	{
	    $i++;
            $td_class = ($i % 2) ? "box_text_odd" : "box_text_even";
	    $owner = uid2username($db_row[uid]);
	    $action = "<a href=menu.php?inc=feature_sms_custom&op=sms_custom_edit&custom_id=$db_row[custom_id]>$icon_edit</a>&nbsp;";
	    $action .= "<a href=\"javascript: ConfirmURL('Are you sure you want to delete SMS custom keyword `$db_row[custom_keyword]` ?','menu.php?inc=feature_sms_custom&op=sms_custom_del&custom_id=$db_row[custom_id]')\">$icon_delete</a>";
	    $custom_url = ( (strlen($db_row[custom_url]) > $maxlen) ? substr($db_row[custom_url],0,$maxlen)."..." : $db_row[custom_url] );
	    $content .= "
    <tr>
	<td class=$td_class>&nbsp;$i.</td>
	<td class=$td_class>$db_row[custom_keyword]</td>
	<td class=$td_class>".stripslashes($custom_url)."</td>
	<td class=$td_class>$owner</td>	
	<td class=$td_class align=center>$action</td>
    </tr>";	    
	}
	$content .= "</table>";
	echo $content;
	echo "
	    <p>
	    <input type=button value=\"Add SMS custom\" onClick=\"javascript:linkto('menu.php?inc=feature_sms_custom&op=sms_custom_add')\" class=\"button\" />
	";
	break;
    case "sms_custom_edit":
	$custom_id = $_GET[custom_id];
	$db_query = "SELECT * FROM "._DB_PREF_."_featureCustom WHERE custom_id='$custom_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$edit_custom_keyword = $db_row[custom_keyword];
	$edit_custom_url = stripslashes($db_row[custom_url]);
	$edit_custom_url = str_replace($feat_custom_path['bin'],'',$edit_custom_url);
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Edit SMS custom</h2>
	    <p>
	    <form action=menu.php?inc=feature_sms_custom&op=sms_custom_edit_yes method=post>
	    <input type=hidden name=edit_custom_id value=$custom_id>
	    <input type=hidden name=edit_custom_keyword value=$edit_custom_keyword>
	    <p>SMS custom keyword: <b>$edit_custom_keyword</b>
	    <p>Pass these parameter to custom URL field:
	    <p><b>{SMSDATETIME}</b> replaced by SMS incoming date/time
	    <p><b>{SMSSENDER}</b> replaced by sender number
	    <p><b>{CUSTOMKEYWORD}</b> replaced by custom keyword 
	    <p><b>{CUSTOMPARAM}</b> replaced by custom parameter passed to server from SMS
	    <p>SMS custom URL: <input type=text size=60 name=edit_custom_url value=\"$edit_custom_url\">
	    <p><input type=submit class=button value=Save>
	    </form>
	";
	echo $content;
	break;
    case "sms_custom_edit_yes":
	$edit_custom_id = $_POST[edit_custom_id];
	$edit_custom_keyword = $_POST[edit_custom_keyword];
	$edit_custom_url = $_POST[edit_custom_url];
	if ($edit_custom_id && $edit_custom_keyword && $edit_custom_url)
	{
	    $db_query = "UPDATE "._DB_PREF_."_featureCustom SET c_timestamp='".mktime()."',custom_url='$edit_custom_url' WHERE custom_keyword='$edit_custom_keyword' AND uid='$uid'";
	    if (@dba_affected_rows($db_query))
	    {
		$error_string = "SMS custom keyword `$edit_custom_keyword` has been saved";
	    }
	    else
	    {
	        $error_string = "Fail to save SMS custom keyword `$edit_custom_keyword`";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=feature_sms_custom&op=sms_custom_edit&custom_id=$edit_custom_id&err=".urlencode($error_string));
	break;
    case "sms_custom_del":
	$custom_id = $_GET[custom_id];
	$db_query = "SELECT custom_keyword FROM "._DB_PREF_."_featureCustom WHERE custom_id='$custom_id'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$keyword_name = $db_row[custom_keyword];
	if ($keyword_name)
	{
	    $db_query = "DELETE FROM "._DB_PREF_."_featureCustom WHERE custom_keyword='$keyword_name'";
	    if (@dba_affected_rows($db_query))
	    {
		$error_string = "SMS custom keyword `$keyword_name` has been deleted!";
	    }
	    else
	    {
		$error_string = "Fail to delete SMS custom keyword `$keyword_name`";
	    }
	}
	header ("Location: menu.php?inc=feature_sms_custom&op=sms_custom_list&err=".urlencode($error_string));
	break;
    case "sms_custom_add":
	if ($err)
	{
	    $content = "<p><font color=red>$err</font><p>";
	}
	$content .= "
	    <h2>Add SMS custom</h2>
	    <p>
	    <form action=menu.php?inc=feature_sms_custom&op=sms_custom_add_yes method=post>
	    <p>SMS custom keyword: <input type=text size=10 maxlength=10 name=add_custom_keyword value=\"$add_custom_keyword\">
	    <p>Pass these parameter to custom URL field:
	    <p><b>{SMSDATETIME}</b> replaced by SMS incoming date/time
	    <p><b>{SMSSENDER}</b> replaced by sender number
	    <p><b>{CUSTOMKEYWORD}</b> replaced by custom keyword 
	    <p><b>{CUSTOMPARAM}</b> replaced by custom parameter passed to server from SMS
	    <p>SMS custom URL: <input type=text size=60 maxlength=200 name=add_custom_url value=\"$add_custom_url\">
	    <p><input type=submit class=button value=Add>
	    </form>
	";
	echo $content;
	break;
    case "sms_custom_add_yes":
	$add_custom_keyword = strtoupper($_POST[add_custom_keyword]);
	$add_custom_url = $_POST[add_custom_url];
	if ($add_custom_keyword && $add_custom_url)
	{
	    if (checkavailablekeyword($add_custom_keyword))
	    {
		$db_query = "INSERT INTO "._DB_PREF_."_featureCustom (uid,custom_keyword,custom_url) VALUES ('$uid','$add_custom_keyword','$add_custom_url')";
		if ($new_uid = @dba_insert_id($db_query))
		{
	    	    $error_string = "SMS custom keyword `$add_custom_keyword` has been added";
		}
		else
		{
	    	    $error_string = "Fail to add SMS custom keyword `$add_custom_keyword`";
		}
	    }
	    else
	    {
		$error_string = "SMS keyword `$add_custom_keyword` already exists, reserved or use by other feature!";
	    }
	}
	else
	{
	    $error_string = "You must fill all fields!";
	}
	header ("Location: menu.php?inc=feature_sms_custom&op=sms_custom_add&err=".urlencode($error_string));
	break;
}

?>