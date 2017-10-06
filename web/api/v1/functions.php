<?php
include('Net/SSH2.php');
include('config.php');
include('db.php');

function getNodeData($nodeid) {

include('db.php');
$getnodedata = $dbh->prepare('SELECT * from nodes where id=:nodeid');
$getnodedata->bindParam(':nodeid', $nodeid);
$getnodedata->execute();
$result = $getnodedata->fetch();
return $result;

}

function createContainer($ctid, $os, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$create = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager create '.$ctid.' '.$os));
$netinit = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager net-init '.$ctid));
return '1';

}

function getConsoleDetails($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$details = $ssh->exec('/usr/bin/sudo /sbin/containermanager retrieveconsole ' . $ctid);
return $details;
}

function checkNodeStatus($accesskey, $hostname) {
include('db.php');

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
return false;
}

$check = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager'));
if (strpos($check, 'command not found') !== false){
return false;
} else {
return true;
}

}

function checkConsole($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$status = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager checkconsole ' . $ctid));
if ($status == "1") {
return "enabled";
} else {
return "disabled";
}
}

function toggleSession($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$status = checkConsole($ctid);
if ($status == "enabled") {
$execute = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager serialconsole ' . $ctid . ' 0'));
} else {
$execute = $ssh->exec('/usr/bin/sudo /sbin/containermanager serialconsole ' . $ctid . ' 1');
}
return $execute;
}

function getTemplates($nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}



$arr = explode("\n", trim($ssh->exec('/usr/bin/sudo /sbin/get_available_templates')));
return $arr;
}

function getPowerLevel($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$var = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager status ' . $ctid));
if (strpos($var, 'Online') !== false){

echo '<a href="" disabled="" class="btn btn-default"> <span class="status-light sl-green"> </span> ONLINE';
} else {
echo '<a href="" disabled="" class="btn btn-default"> <span class="status-light sl-red"> </span> OFFLINE';

}

}

function status($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$var = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager status ' . $ctid));
if (strpos($var, 'Online') !== false){

#echo '<a href="" disabled="" class="btn btn-default"> <span class="status-light sl-green"> </span> ONLINE';
$statusmsg = 'true';
} else {
#echo '<a href="" disabled="" class="btn btn-default"> <span class="status-light sl-red"> </span> OFFLINE';
$statusmsg = 'false';

}
return $statusmsg;
}


function getDisk($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager diskusage ' . $ctid));
}

function getos ($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}



return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager getos ' . $ctid));

}

function checktun ($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager checktun ' . $ctid));

}

function enabletun ($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager tuntap ' . $ctid . ' 1'));

}

function disabletun ($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager tuntap ' . $ctid . ' 0'));

}

function poweron($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager start ' . $ctid));
}

function poweroff($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager stop ' . $ctid));
}

function reboot($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager restart ' . $ctid));
}


function reinstall($ctid, $os, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

$act = trim($ssh->exec('/usr/bin/sudo /sbin/containermanager reinstall ' . $ctid . ' ' . $os));
trim($ssh->exec('/usr/bin/sudo /sbin/containermanager net-init ' . $ctid));

return $act;

}

function memoryusage($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return trim($ssh->exec('/usr/bin/sudo /sbin/containermanager memusage ' . $ctid));
}

function resetpass($ctid, $nodeid){
include('db.php');

$nodedata = getNodeData($nodeid);
$hostname = $nodedata['hostname'];
$accesskey = $nodedata['accesskey'];

$ssh = new Net_SSH2($hostname);
if (!$ssh->login('remote', $accesskey)) {
exit('System is under maintenance.');
}

return $ssh->exec('/usr/bin/sudo /sbin/containermanager resetpass ' . $ctid);
}

?>
