<?php

playsmsd();
getsmsinbox();
getsmsstatus();
execgwcustomcmd();
execcommoncustomcmd();

if ($_GET[op]=='daemon')
{
    echo "<h2><font color=green>playSMS server successfully refreshed</font></h2>";
    die();
}

$url = $_GET[url];
if (isset($url))
{
    $url = base64_decode($url);
    header ("Location: $url");
}
else
{
    echo "REFRESHED";
}

?>