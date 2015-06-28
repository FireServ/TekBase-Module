<?php

if (preg_match("/tsbackup.php/i", $_SERVER['PHP_SELF'])) { 
    Header("Location: ../index.php");
	die();
}

if(is_admin($admin)) {

function admintest() {
    global $prefix, $db, $admin;

    include ("admin/header.php");

	$adminmsg = admin_title("backupall", ""._TSBACKUP."", ""._ASSISTENTAPPLIST."", $iconset, "");	
	echo ''.$adminmsg.'';
	
	if(isset($_POST['quantity']) OR isset($_POST['delay'])){
		if(is_numeric($_POST['quantity']) AND is_numeric($_POST['delay'])){
			$delay = $_POST['delay'];
			$quantity = $_POST['quantity'];
			
			$countQ = $db->sql_query("SELECT * FROM teklab_voiceserver_backup_options WHERE opt = 'delay' OR opt = 'quantity'");
			$count = $db->sql_numrows($countQ);
			
			if($count == 2){
				$query = $db->sql_query("UPDATE ".$prefix."_voiceserver_backup_options SET value = '".$_POST['delay']."' WHERE opt = 'delay'");
				$query2 = $db->sql_query("UPDATE ".$prefix."_voiceserver_backup_options SET value = '".$_POST['quantity']."' WHERE opt = 'quantity'");
			
				if($query AND $query2){
					$adminmsg = admin_ok("Die Einstellungen wurden geändert!", $iconset);
					echo $adminmsg;
				}else{
					$adminmsg = admin_error("FEHLER: Die Verbindung zur Datenbank konnte nicht hergestellt werden.", $iconset);
					echo $adminmsg;
				}
			}else{
				$adminmsg = admin_error("Es liegt ein Fehler in der Datenbank vor, bitte überprüfen Sie die Tabelle tekbase_voiceserver_backup_options.", $iconset);
				echo $adminmsg;
			}
		}else{
			$adminmsg = admin_error("<center>Eine der folgenden Anforderungen wurden nicht erfüllt:<li>Ein Feld ist leer</li><li>Der Wert darf nur aus Zahlen bestehen</li></center>", $iconset);
			echo $adminmsg;
		}
	}else{
		$optionDelay = $db->sql_fetchrow($db->sql_query("SELECT value FROM ".$prefix."_voiceserver_backup_options WHERE opt = 'delay'"));
		$delay = $optionDelay['value'];
		$optionQuantity = $db->sql_fetchrow($db->sql_query("SELECT value FROM ".$prefix."_voiceserver_backup_options WHERE opt = 'quantity'"));
		$quantity = $optionQuantity['value'];
	}
	
	echo '
		<form name="form_one" action="admin.php?op=admintsbackup" method="post">
			<table cellspacing="0" cellpadding="0" class="inputtable">
				<tbody>
					<tr><td style="width:180px;">Maximale Backupanzahl:</td><td><input type="text" name="quantity" class="inputfield" style="width:180px;" maxlength="255" value="'.$quantity.'"> Backups</td></tr>
					<tr><td style="width:180px;">Verzögerung zwischen Backups:</td><td><input type="text" name="delay" class="inputfield" style="width:180px;" maxlength="255" value="'.$delay.'"> Sekunden</td></tr>
				</tbody>
			</table>
			<div class="dataspace"></div>
			<a href="javascript:document.form_one.submit();" class="button_form">Speichern</a>
		</form>
		';
		
	
    include ("admin/footer.php");
}

switch ($op) {
	
	case "admintsbackup":
	admintest();
	break;

}
}else{
adminLogin($admin);
}

?>