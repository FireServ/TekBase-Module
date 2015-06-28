<?php
	if (preg_match("/index_groups.php/i", $_SERVER['PHP_SELF'])) { 
		Header("Location: ../index_groups.php");
		die();
	}
	
	function module_list($ids, $menu_iconbar, $vara, $varb, $varc, $vard, $vare, $varf, $varg, $varh, $vari, $varj, $vark, $varl, $varm, $varn, $varo, $varp, $varq, $varr, $vars) {
		global $prefix, $db, $member;
	}

	require_once("libraries/TeamSpeak3/TeamSpeak3.php");
	
	$masterServer = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_teamspeak WHERE id = '".$options['tserverid']."'"));
	
	
	$memberID = $memstats['id'];
	$tsip = $options['serverip'];
	$port = $options['serverport'];
	
	if(isset($_GET['action']) && $_GET['action'] == 'reset'){
		echo '<div style="width:300px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox">Wenn Sie diese Aktion ausführen, werden alle Gruppen und Rechte zurückgesetzt! Es wird empfohlen vorher ein aktuelles Backup des Servers zu erstellen.<br />			  <div class="dataspace"></div><center><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<center>
					
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups" method="post">
							<div class="dataspace"></div>
							<input type="hidden" name="reset">
							<input type="checkbox" name="confirm" id="confirm" class="check">
							<label for="confirm" class="checkLabel"><div class="checkBox">Ich bin mir darüber bewusst, dass dieser Vorgang alle Gruppen und Rechte zurücksetzt und, ohne Backup, nicht rückgängig gemacht werden kann.</div></label>
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Rechte und Gruppen zurücksetzen</a>
						</form>
					</center>
				</div>
				</div>';
	}elseif(isset($_GET['action']) && isset($_GET['gid']) && $_GET['action'] == 'delete' && is_numeric($_GET['gid'])){
		echo '
				<div style="width:300px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox">Wenn Sie diese Aktion ausführen, wird die Gruppe gelöscht! Es wird empfohlen vorher ein aktuelles Backup des Servers zu erstellen.<br />			  <div class="dataspace"></div><center><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<center>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups" method="post">
							<div class="dataspace"></div>
							<input type="hidden" name="gdel">
								<input type="checkbox" name="empty" id="empty" class="check">
								<label for="empty" class="checkLabel"><div class="checkBox">Gruppe löschen, auch wenn Clients in der Gruppe sind.</div></label>
							
							<div class="dataspace"></div>
							<input type="hidden" name="gid" value="'.$_GET['gid'].'">
							<input type="checkbox" name="confirm" id="confirm" class="check">
							<label for="confirm" class="checkLabel"><div class="checkBox">Ich bin mir darüber bewusst, dass dieser Vorgang alle Gruppen und Rechte zurücksetzt und, ohne Backup, nicht rückgängig gemacht werden kann.</div></label>
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Gruppe löschen</a>
						</form>
					</center>
				</div>
				</div>';
	}elseif(isset($_GET['action']) && $_GET['action'] == 'createGroup'){
		echo '
				<div style="width:300px;">
				<div class="popup_header">Gruppe hinzufügen</div>
				<div class="popup_content">
					<center>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups" method="post">
								<div class="dataspace"></div>
								<table>
									<tr>
										<td><label for="name">Gruppenname:</label></td>
										<td><input type="input" name="name" id="name" style="width:194px;" /></td>
									</tr>
									<tr>
										<td><label for="sgid">Vorlage:</label></td>
										<td>
											<select name="sgid" id="sgid" style="width:200px;">
											<option value="0">Keine</option>';
						try{
							//Query Verbindung zum Teamspeak Server aufbauen
							$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
							
							//Snapshot erstellen
							$arr_ServerGroup = $ts3_VirtualServer->serverGroupList();
							
							foreach($arr_ServerGroup as $ts3_Client){
								if($ts3_Client['type'] == 1){
									echo '<option value="'.$ts3_Client['sgid'].'">'.htmlspecialchars($ts3_Client['name']).'</option>';
								}
							}

			echo '
								</select>
								</td>
									</tr>
									<tr>
									<td>
									</td>
									<td>
								<input type="hidden" name="gadd">
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Gruppe erstellen</a>
							</td>
							</tr>
								</table>
						</form>
					</center>
				</div>
				</div>';
				}catch(TeamSpeak3_Exception $e){
							$message = $e->getMessage();
								$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator. Nachricht: ".$e->getMessage()."", $iconset);
								echo $membermsg;
						}
		}else{

		include("members/header.php");
		
		$membermsg = member_title("voice", "Voiceserver - ".$tsip.":".$port."", "Hier habt ihr die Möglichkeit Backups zu erstellen und wiederherzustellen.", $iconset, "");
		echo ''.$membermsg.'';
		
		echo $menu_iconbar;
		
		echo '<style>
					.check {
						height:0px;
						width:0px;
						visibility: hidden;
					}
					.checkBox {
						padding:8px;border:1px solid #56E459;font-size:11px;background-color:#CEFFCC;background-repeat:repeat-x;-moz-box-shadow: 0px 5px 7px -7px #000000;-webkit-box-shadow: 0px 5px 7px -7px #000000;box-shadow: 0px 5px 7px -7px #000000;-moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px;width:250px;
					}
					.checkBox:hover{
						cursor: pointer;
					}
					.check:checked + .checkLabel {
						font-weight: bold;
					}
				</style>';
		
			if(isset($_POST['reset'])){
				if(isset($_POST['confirm']) && $_POST['confirm']){
					try {
						$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
			
						$token = $ts3_VirtualServer->permReset();
					
						$membermsg = member_ok("&nbsp;&nbsp;Die Rechte und Gruppen wurden zurückgesetzt.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ihr neuer Admintoken lautet: <b>".$token."</b>", $iconset);
						echo $membermsg;
					}catch(TeamSpeak3_Exception $e){
						$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator.", $iconset);
						echo $membermsg;
					}
				}else{
					$membermsg = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
					echo $membermsg;
				}
			}
			
			if(isset($_POST['gdel'])){
				if(isset($_POST['confirm']) && isset($_POST['gid']) && $_POST['confirm'] && is_numeric($_POST['gid'])){
					try {

						$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
						if($_POST['empty']){
							$ts3_VirtualServer->serverGroupDelete($_POST['gid'], 1);
						}else{
							$ts3_VirtualServer->serverGroupDelete($_POST['gid']);
						}
					
						$membermsg = member_ok("&nbsp;&nbsp;Die Server Gruppe wurde gelöscht.", $iconset);
						echo $membermsg;
					}catch(TeamSpeak3_Exception $e){
						$message = $e->getMessage();
						if($message == 'access to default group is forbidden'){
							$membermsg = member_error("Eine Standardgruppe kann nicht gelöscht werden.", $iconset);
							echo $membermsg;
						}elseif($message == 'group is not empty'){
							$membermsg = member_error("Die Gruppe hat Mitglieder und kann nur gelöscht werden, wenn Sie dies bestätigen.", $iconset);
							echo $membermsg;
						}else{
							$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator. Nachricht: ".$e->getMessage()."", $iconset);
							echo $membermsg;
						}
					}
				}else{
					$membermsg = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
					echo $membermsg;
				}
			}
			if(isset($_POST['gadd'])){
				if(isset($_POST['sgid']) && is_numeric($_POST['sgid'])){
					if(isset($_POST['name']) && !empty($_POST['name'])){
						if(strlen($_POST['name']) <= 30){
							try {
								$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
								
								if($_POST['sgid'] == 0){
									$arr_ServerGroup = $ts3_VirtualServer->serverGroupCreate($_POST['name'],0x01);
									
									$membermsg = member_ok("&nbsp;&nbsp;Die Server Gruppe wurde erstellt.", $iconset);
									echo $membermsg;
								}else{
									$arr_ServerGroup = $ts3_VirtualServer->serverGroupList();
									$arr_new = array();
									
									foreach($arr_ServerGroup as $ts3_Client){
										if($ts3_Client['type'] == 1){
											$gruppen = array($ts3_Client['sgid']);
											$arr_new = array_merge($arr_new, $gruppen);
										}
									}
									
									if(in_array($_POST['sgid'], $arr_new)){
										$arr_ServerGroup = $ts3_VirtualServer->serverGroupCopy($_POST['sgid'],$_POST['name'],0,0x01);
										$membermsg = member_ok("&nbsp;&nbsp;Die Server Gruppe wurde erstellt.", $iconset);
										echo $membermsg;
									}else{
										$membermsg = member_error("Fehler: Die Ausgewählte Vorlagengruppe existiert nicht!", $iconset);
										echo $membermsg;
									}
								}
							}catch(TeamSpeak3_Exception $e){
								$message = $e->getMessage();
								if($message == 'access to default group is forbidden'){
									$membermsg = member_error("Eine Standardgruppe kann nicht gelöscht werden.", $iconset);
									echo $membermsg;
								}
								if($message == 'database duplicate entry'){
									$membermsg = member_error("Eine Gruppe mit diesem Namen existiert bereits.", $iconset);
									echo $membermsg;
								}elseif($message == 'group is not empty'){
									$membermsg = member_error("Die Gruppe hat Mitglieder und kann nur gelöscht werden, wenn Sie dies bestätigen.", $iconset);
									echo $membermsg;
								}else{
									$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator. Nachricht: ".$e->getMessage()."", $iconset);
									echo $membermsg;
								}
							}
						}else{
							$membermsg = member_error("Der Gruppenname ist zu lang.", $iconset);
							echo $membermsg;
						}
					}else{
						$membermsg = member_error("Sie haben keinen Gruppennamen angegeben.", $iconset);
						echo $membermsg;
					}
					
				}else{
					$membermsg = member_error("Fehler: Die Vorlagengruppe ist falsch!", $iconset);
					echo $membermsg;
				}
			}
			
			try{
			//Query Verbindung zum Teamspeak Server aufbauen
			$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
			
			//Snapshot erstellen
			$arr_ServerGroup = $ts3_VirtualServer->serverGroupList();
			
			echo '
						
			<script type="text/javascript">
				$(document).ready(function(){
					
				var sortcol=0;var sorttyp="asc"	
					
					$("#datatablea").dataTable({"sPaginationType": "full_numbers","aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] }],"bAutoWidth": true,"aaSorting": [[sortcol,sorttyp]],"aLengthMenu": [10, 25, 50, 100],"iDisplayLength": 50,
						"oLanguage": {"sLengthMenu": "Zeige _MENU_ Einträge pro Seite","sZeroRecords": "Keine Einträge vorhanden!","sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen","sInfoEmpty": "Zeige 0 bis 0 von 0 Einträgen","sInfoFiltered": "(gefiltert aus _MAX_ Einträgen)","sSearch": "Suche:"}
					});	
				});
			</script>
							<div class="infobox" style="min-width:300px;max-width:400px;">
								<div class="infobox_header">Gruppen zurücksetzen</div>
								<div class="infobox_content">
									<b>Information</b>
									<div class="dataspace"></div>
									Hier können Sie die Gruppen und Rechte des Servers zurücksetzen.
									<div class="dataspace"></div>
									<div class="dataspace"></div>
										<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups&action=reset" rel="facebox" class="button_form">Rechte und Gruppen zurücksetzen</a>
									<div class="clear"></div>
								</div>
							</div>
							
									<div class="dataspace"></div>
									<div>
										<div class="iconlist">
											<div class="dataspace"></div>
											<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups&action=createGroup"  rel="facebox"><img class="imghover" src="members/iconsets/clean/m_users.png" title="" style="background-color:#800732;"><br>Neue Gruppe</a>
											<div class="dataspace"></div>
										</div>
									 </div>
									  <div class="clear"></div>
									  <div class="smallline"></div>
									<div class="dataspace"></div>
						<table cellpadding="0" cellspacing="0" border="0" class="display " id="datatablea" width="100%" style="width: 100%;">
										<thead>
											<tr role="row">
												<th class=" " style="width: 12px;">
													ID
												</th>
												<th style="">
													Name
												</th>
												<th style="">
													Mitglieder
												</th>
												<th style="">
												</th>
											</tr>
										</thead>
										<tbody role="alert">';
						foreach($arr_ServerGroup as $ts3_Client){
							if($ts3_Client['type'] == 1){
								echo '
											<tr class="odd" style="height:40px;">
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Client['sgid']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Client['name']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">';
												
												$groupmember = $ts3_VirtualServer->serverGroupClientList($ts3_Client['sgid']);
												echo count($groupmember);
											echo'
													</div>
												</td>
												<td class=" " style="width:120px;">
													<div class="dataimgcenter">';
														if($ts3_Client['name'] == 'Guest'){
														}else{
															echo'<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups&action=delete&gid='.$ts3_Client['sgid'].'" rel="facebox" class="button_form">Gruppe löschen</a>';
														}
										echo'		</div>
												</td>
											</tr>
										';
							}
						}
							echo '</tbody>
									</table>';
									
				}catch(TeamSpeak3_Exception $e){
					$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator.", $iconset);
					echo $membermsg;
				}
    include("members/footer.php");
	}
?>