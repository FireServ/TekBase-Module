<?php
	if (preg_match("/index.php/i", $_SERVER['PHP_SELF'])) { 
		Header("Location: ../index.php");
		die();
	}
	
	function module_list($ids, $menu_iconbar, $vara, $varb, $varc, $vard, $vare, $varf, $varg, $varh, $vari, $varj, $vark, $varl, $varm, $varn, $varo, $varp, $varq, $varr, $vars) {
		global $prefix, $db, $member;
	}
	
	require_once("libraries/TeamSpeak3/TeamSpeak3.php");
	
	if(isset($_GET['action']) && isset($_GET['ts']) && is_numeric($_GET['ts']) && $_GET['action'] == 'restore'){
		echo '
			<div style="width:600px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox">Dabei gehen alle aktuellen Einstellungen verloren und der Server wird dann auf den Stand zum Zeitpunkt des ausgewählten Backups zurückgesetzt! Es wird empfohlen vorher ein aktuelles Backup des Servers zu erstellen.<br />			  <div class="dataspace"></div><center><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<div class="dataspace"></div>
					<center>
						Hiermit stellen Sie das Backup vom <b>'.date('d.m.Y H:i:s', $_GET['ts']).'</b> wieder her.
						<div class="dataspace"></div>
						Um die Wiederherstellung zu bestätigen, geben Sie bitte das Wort <b>RESTORE</b> in das untere Feld ein und bestätigen Sie es mit einem klick auf den Button.
						<div class="dataspace"></div>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3" method="post">
							<input name="restore_confirm" type="text" class="inputfield" style="width:100px;" maxlength="20" value="">
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<input type="hidden" name="restore">
							<input type="hidden" name="ID" value="'.$_GET['ID'].'">
							<a href="javascript:document.form_one.submit();" class="button_form">Backup wiederherstellen</a>
						</form>
					</center>
				</div>
				</div>';
	}else if(isset($_GET['action']) && isset($_GET['ts']) && is_numeric($_GET['ts']) && $_GET['action'] == 'delete'){
		echo '
			<div style="width:600px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox"><center><b>Dieser Vorgang kann nicht rückgängig gemacht werden! Ein einmal gelöschtes Backup kann unter keinen Umständen wieder hergestellt werden.</b></center></div>
					</div>
					<div class="dataspace"></div>
					<div class="dataspace"></div>
					<center>
						Hiermit <b>löschen</b> Sie das Backup vom <b>'.date('d.m.Y H:i:s', $_GET['ts']).'</b>.
						<div class="dataspace"></div>
						Um das Backup zu löschen, geben Sie bitte das Wort <b>LÖSCHEN</b> in das untere Feld ein und bestätigen Sie es mit einem klick auf den Button.
						<div class="dataspace"></div>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3" method="post">
							<input name="delete_confirm" type="text" class="inputfield" style="width:100px;" maxlength="20" value="">
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<input type="hidden" name="delete">
							<input type="hidden" name="ID" value="'.$_GET['ID'].'">
							<a href="javascript:document.form_one.submit();" class="button_form">Backup löschen</a>
						</form>
					</center>
				</div>
				</div>';
	}else{
		$memberID = $memstats['id'];
		$tsip = $options['serverip'];
		$port = $options['serverport'];
			
		include("members/header.php");
		
		$membermsg = member_title("voice", "Voiceserver - ".$tsip.":".$port."", "Hier habt ihr die Möglichkeit Backups zu erstellen und wiederherzustellen.", $iconset, "");
		echo ''.$membermsg.'';
		
		echo $menu_iconbar;
			
		$masterServer = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_teamspeak WHERE id = '".$options['tserverid']."'"));
		
		if(isset($_POST['backup'])){
		
			$backups = $db->sql_query("SELECT * FROM ".$prefix."_voiceserver_backup WHERE kd='".$memberID."' AND sid='".$port."' ORDER BY ID DESC");
			$row = $db->sql_fetchrow($backups);
			$anzahl = $db->sql_numrows($backups);
			$time = time()-$row['date'];
			
			$optionDelay = $db->sql_fetchrow($db->sql_query("SELECT value FROM ".$prefix."_voiceserver_backup_options WHERE opt = 'delay'"));
			$optionQuantity = $db->sql_fetchrow($db->sql_query("SELECT value FROM ".$prefix."_voiceserver_backup_options WHERE opt = 'quantity'"));
			
			if($anzahl < $optionQuantity['value']){
				if($anzahl < 1 OR $time >= $optionDelay['value']){
					try{
					
						//Query Verbindung zum Teamspeak Server aufbauen
						$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
						
						//Snapshot erstellen
						$snapshot = $ts3_VirtualServer->request("serversnapshotcreate")->toString(true);
						
					}catch(TeamSpeak3_Exception $e){
						$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00001", $iconset);
						echo $membermsgtwo;
					}
					if(isset($snapshot)){
						$time = time();
					
						$fp = @fopen("members/ownmodules/Teamspeak3/backups/".$memberID."_".$time.".txt","wb");
						if($fp){
							fwrite($fp,$snapshot);
							fclose($fp);
							
							//Snapshot in die DB eintragen
							$query = $db->sql_query("INSERT INTO ".$prefix."_voiceserver_backup (ID, kd, sid, date) VALUES (NULL, ".$memberID.", ".$port.", '".$time."')");
							
							//Nachricht an Kunden ausgeben
							$membermsgtwo = member_ok("Ein Backup wurde erstellt!", $iconset);
							echo $membermsgtwo;
						}else{
							$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00002", $iconset);
							echo $membermsgtwo;
						}
					}
				}else{
						$membermsgtwo = member_error("Es kann nur alle ".$optionDelay['value']." Sekunden ein Backup erstellt werden.", $iconset);
						echo $membermsgtwo;
				}
			}else{
				$membermsgtwo = member_error("Es können maximal ".$optionQuantity['value']." Backups erstellt werden.", $iconset);
				echo $membermsgtwo;
			}
		}
		
		
		if(isset($_POST['restore']) && isset($_POST['restore_confirm'])){
			if($_POST['restore_confirm'] == 'RESTORE'){
				//Query Verbindung zum Teamspeak Server aufbauen
				$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
				
				$query = $db->sql_query("SELECT * FROM ".$prefix."_voiceserver_backup WHERE kd='".$memberID."' AND ID='".$_POST['ID']."' ORDER BY ID DESC");
				
				$result = $db->sql_fetchrow($query);
				
				$anzahl = $db->sql_numrows($query);
				
				if($anzahl != 1){
					$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00003", $iconset);
					echo $membermsgtwo;
				}else{
					$datei = @file("members/ownmodules/Teamspeak3/backups/".$memberID."_".$result['date'].".txt");
					if($datei){
						//Backup Wiederherstellen
						$restore = $ts3_VirtualServer->request("serversnapshotdeploy " . $datei[0])->toList();
						
						//Nachricht an Kunden ausgeben
						$membermsgtwo = member_ok("Das Backup wurde erfolgreich wiederhergestellt!", $iconset);
						echo $membermsgtwo;
					}else{
						$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00004", $iconset);
						echo $membermsgtwo;
					}
				}
			}else{
				$membermsgtwo = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
				echo $membermsgtwo;
			}
		}
		
		if(isset($_POST['delete']) && isset($_POST['delete_confirm'])){
			if($_POST['delete_confirm'] == 'LÖSCHEN'){
			
				$query = $db->sql_query("SELECT * FROM ".$prefix."_voiceserver_backup WHERE kd='".$memberID."' AND ID='".$_POST['ID']."' ORDER BY ID DESC");
				
				$result = $db->sql_fetchrow($query);
				
				$anzahl = $db->sql_numrows($query);
				
				if($anzahl != 1){
					$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00003", $iconset);
					echo $membermsgtwo;
				}else{
					$fp = @fopen("members/ownmodules/Teamspeak3/backups/".$memberID."_".$result['date'].".txt","wb");
					if($fp){
						fclose($fp);
						
						$dateiDel = @unlink("members/ownmodules/Teamspeak3/backups/".$memberID."_".$result['date'].".txt");
						if($dateiDel){
							$query = $db->sql_query("DELETE FROM ".$prefix."_voiceserver_backup WHERE ID = ".$_POST['ID']."");
							if($query){
								//Nachricht an Kunden ausgeben
								$membermsgtwo = member_ok("Das Backup wurde gelöscht!", $iconset);
								echo $membermsgtwo;
							}else{
								$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00004", $iconset);
								echo $membermsgtwo;
							}
						}else{
							$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00004", $iconset);
							echo $membermsgtwo;
						}
					}else{
						$membermsgtwo = member_error("Fehlgeschlagen! Bitte kontaktieren Sie den Support über unser <a href=\"members.php?op=membersSupport\">Ticketsystem</a>! CODE 00002", $iconset);
						echo $membermsgtwo;
					}
				}
			}else{
				$membermsgtwo = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
				echo $membermsgtwo;
			}
		}
		
		if(isset($_POST['upload'])){
			print_r($_FILES);
			echo '<br />';
			print_r($_POST);
		}
		
		echo '
			<script type="text/javascript">
				$(document).ready(function(){
					
				var sortcol=1;var sorttyp="desc"	
					
					$("#datatablea").dataTable({"sPaginationType": "full_numbers","aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0,2,3 ] }],"bAutoWidth": true,"aaSorting": [[sortcol,sorttyp]],"aLengthMenu": [10, 25, 50, 100],"iDisplayLength": 10,
						"oLanguage": {"sLengthMenu": "Zeige _MENU_ Einträge pro Seite","sZeroRecords": "Keine Einträge vorhanden!","sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen","sInfoEmpty": "Zeige 0 bis 0 von 0 Einträgen","sInfoFiltered": "(gefiltert aus _MAX_ Einträgen)","sSearch": "Suche:"}
					});	
				});
			</script>
			<table cellspacing="0" cellpadding="0" style="width:100%;">
				<tbody>
					<tr>
						<td style="width:50%;">
							<div class="infobox" style="min-width:300px;max-width:100%;margin-right:10px;">
								<div class="infobox_header">Backup erstellen</div>
								<div class="infobox_content" style="min-height:80px;">
									<b>Information</b>
									<div class="dataspace"></div>
									Hier können Sie ein Backup von Ihren Server Einstellungen, Rechten und Channel vornehmen.
									<div class="dataspace"></div>
									<div class="dataspace"></div>
									<form name="form_backup" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3" method="post">
										<input type="hidden" name="backup">
										<a href="javascript:document.form_backup.submit();" class="button_form">Backup jetzt erstellen</a>
									</form>
									<div class="clear"></div>
								</div>
							</div>
						</td>
						<td style="width:50%;">
							<div class="infobox" style="max-width:100%;">
								<div class="infobox_header">Informationen</div>
								<div class="infobox_content" style="min-height:80px;">
								<div>Hier können Backups von den Serereinstellungen, Channel, Rechten und Benutzern erstellt werden. 
									<div class="dataspace"></div> Es werden <b>keine</b> Icons oder die mittels Dateibrowser hochgeladenen Dateien gespeichert.</div> 
									<!--<table cellspacing="0" cellpadding="0" class="inputtable">
										<form name="form_upload" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3" enctype="multipart/form-data" method="post">
											<tbody>
												<tr>
													<td>Datei: </td>
													<td><input name="file" type="file" class="inputfield"><input type="hidden" name="upload"></td>
												</tr>
												<tr>
													<td></td>
													<td>
														<div class="dataspace"></div>
														<a href="javascript:document.form_upload.submit();" class="button_form">Hochladen</a>
													</td>
												</tr>
											</tbody>
										</form>
									</table>-->
								</div>
							</div>
						</td>
						</tr>
						<tr>
						<td colspan="2" style="width:100%;">
							<div class="dataspace"></div>
							<div class="infobox" style="min-width:500px;max-width:100%;">
								<div class="infobox_header">Backups</div>
								<div class="infobox_content">
									<b>Information</b>
									<div class="dataspace"></div>
									Hier können Sie ein zuvor erstelltes Backup wiederherstellen. 
									<div class="dataspace"></div>
									<b>Achtung:</b> Dabei gehen alle aktuellen Einstellungen verloren und der Server wird dann auf den Stand zum Zeitpunkt des ausgewählten Backups zurückgesetzt! Es wird empfohlen vorher ein aktuelles Backup des Servers zu erstellen.
									<div class="dataspace"></div>
									<div class="dataspace"></div>
									<table cellpadding="0" cellspacing="0" border="0" class="display " id="datatablea" width="100%" style="width: 100%;">
										<thead>
											<tr role="row">
												<th class="close " style="width: 12px;">
													ID
												</th>
												<th style="width: 307px;">
													Backup vom
												</th>
												<th style="width: 125px;">
												</th>
												<th style="width: 80px;">
												</th>
											</tr>
										</thead>
										<tbody role="alert">';
										
		$backups = $db->sql_query("SELECT * FROM ".$prefix."_voiceserver_backup WHERE kd='".$memberID."' AND sid='".$port."' ORDER BY ID DESC");
		while( $row = $db->sql_fetchrow($backups) ) 
		{ 
			echo	'
											<tr class="odd" style="height:40px;">
												<td class="close ">
													<div class="datatxt">&nbsp;'.$row['ID'].'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.date('d.m.Y H:i:s', $row['date']).'</div>
												</td>
												<td class=" ">
													<div class="dataimgcenter">
														<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&action=restore&ID='.$row['ID'].'&ts='.$row['date'].'" rel="facebox" class="button_form">Backup wiederherstellen</a>
													</div>
												</td>
												<td class=" ">
													<div class="dataimgcenter">
														<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&action=delete&ID='.$row['ID'].'&ts='.$row['date'].'" rel="facebox" class="button_form">Backup löschen</a>
													</div>
												</td>
											</tr>
					'; 
		}
		echo	'
										</tbody>
									</table>
									<div class="clear"></div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<center>Copyright © 2015 by <a href="https://www.fireserv.de">FireServ.de</a></center>	';
    include("members/footer.php");
	}
?>