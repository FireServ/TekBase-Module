<?php
	if (preg_match("/index_ban.php/i", $_SERVER['PHP_SELF'])) { 
		Header("Location: ../index_groups.php");
		die();
	}
	
	function module_list($ids, $menu_iconbar, $vara, $varb, $varc, $vard, $vare, $varf, $varg, $varh, $vari, $varj, $vark, $varl, $varm, $varn, $varo, $varp, $varq, $varr, $vars) {
		global $prefix, $db, $member;
	}
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
	
	require_once("libraries/TeamSpeak3/TeamSpeak3.php");
	
	$masterServer = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_teamspeak WHERE id = '".$options['tserverid']."'"));
	
	
	$memberID = $memstats['id'];
	$tsip = $options['serverip'];
	$port = $options['serverport'];
	
	if(isset($_GET['action']) && $_GET['action'] == 'delAll'){
		echo '<div style="width:300px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox">Wenn Sie diese Aktion ausführen, werden alle Banns vom Server entfernt! Es wird empfohlen vorher ein aktuelles Backup des Servers zu erstellen.<br />			  <div class="dataspace"></div><center><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<center>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban" method="post">
							<div class="dataspace"></div>
							<input type="hidden" name="delAll">
							<label for="confirm" class="checkLabel"><input type="checkbox" name="confirm" id="confirm" class="check"><div class="checkBox"><div class="checkText">Ich bin mir darüber bewusst, dass dieser Vorgang alle Banns entfernt und, ohne Backup, nicht rückgängig gemacht werden kann.</div></div></label>
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Alle Banns entfernen</a>
						</form>
					</center>
				</div>
				</div>';
	}elseif(isset($_GET['action']) && $_GET['action'] == 'banDelete' && isset($_GET['bid']) && is_numeric($_GET['bid'])){
		echo '<div style="width:300px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox"><center>Wenn Sie diese Aktion ausführen, wird der Bann aufgehoben! <br />			  <div class="dataspace"></div><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<center>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban" method="post">
							<div class="dataspace"></div>
							<input type="hidden" name="banDelete">
							<input type="hidden" name="bid" value="'.$_GET['bid'].'">
							<label for="confirm" class="checkLabel"><input type="checkbox" name="confirm" id="confirm" class="check"><div class="checkBox"><div class="checkText">Ich möchte den Bann aufheben!</div></div></label>
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Bann aufheben</a>
						</form>
					</center>
				</div>
				</div>';
	}elseif(isset($_GET['action']) && $_GET['action'] == 'delComplaint' && isset($_GET['fid']) && is_numeric($_GET['fid']) && isset($_GET['tid']) && is_numeric($_GET['tid'])){
		echo '<div style="width:300px;">
				<div class="popup_header">Bestätigung</div>
				<div class="popup_content">
					<div id="profilwarningbox" class="open">
						<div class="errorbox"><center>Wenn Sie diese Aktion ausführen, wird die Beschwerde entfernt! <br />			  <div class="dataspace"></div><b>Dieser Vorgang kann nicht rückgängig gemacht werden!</b></center></div>
					</div>
					<div class="dataspace"></div>
					<center>
						<form name="form_one" action="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban" method="post">
							<div class="dataspace"></div>
							<input type="hidden" name="delComplaint">
							<input type="hidden" name="fid" value="'.$_GET['fid'].'">
							<input type="hidden" name="tid" value="'.$_GET['tid'].'">
							<label for="confirm" class="checkLabel"><input type="checkbox" name="confirm" id="confirm" class="check"><div class="checkBox"><div class="checkText">Ich möchte die Beschwerde entfernen!</div></div></label>
							<div class="dataspace"></div>
							<div class="dataspace"></div>
							<a href="javascript:document.form_one.submit();" class="button_form">Beschwerde entfernen</a>
						</form>
					</center>
				</div>
				</div>';
	}elseif(isset($_GET['action']) && $_GET['action'] == 'add'){
		echo 'ok';
	}else{	
	
	include("members/header.php");
	$membermsg = member_title("voice", "Voiceserver - ".$tsip.":".$port."", "Hier habt ihr die Möglichkeit Backups zu erstellen und wiederherzustellen.", $iconset, "");
	echo ''.$membermsg.'';
		
	echo $menu_iconbar;
	if(isset($_POST['delAll'])){
		if(isset($_POST['confirm']) && $_POST['confirm']){
			try {
				$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
	
				$ts3_VirtualServer->banListClear();
			
				$membermsg = member_ok("&nbsp;&nbsp;Die Bannliste wurde geleert. ", $iconset);
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
	if(isset($_POST['banDelete']) && isset($_POST['bid']) && is_numeric($_POST['bid'])){
		if(isset($_POST['confirm']) && $_POST['confirm']){
			try {
				$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
	
				$ts3_VirtualServer->banDelete($_POST['bid']);
			
				$membermsg = member_ok("&nbsp;&nbsp;Der Bann wurde aufgehoben. ", $iconset);
				echo $membermsg;
			}catch(TeamSpeak3_Exception $e){
				if($e->getMessage() == 'invalid ban id'){
					$membermsg = member_error("Der Bann konnte nicht aufgehoben werden.", $iconset);
					echo $membermsg;
				}else{
					$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator.", $iconset);
					echo $membermsg;
				}
			}
		}else{
			$membermsg = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
			echo $membermsg;
		}
	}
	
	if(isset($_POST['delComplaint']) && isset($_POST['tid']) && is_numeric($_POST['tid']) && isset($_POST['fid']) && is_numeric($_POST['fid'])){
		if(isset($_POST['confirm']) && $_POST['confirm']){
			try {
				$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
	
				$ts3_VirtualServer->complaintDelete($_POST['tid'], $_POST['fid']);
			
				$membermsg = member_ok("&nbsp;&nbsp;Die Beschwerde wurde entfernt. ", $iconset);
				echo $membermsg;
			}catch(TeamSpeak3_Exception $e){
				if($e->getMessage() == 'invalid clientID'){
					$membermsg = member_error("Die Beschwerde konnte nicht aufgehoben werden.", $iconset);
					echo $membermsg;
				}else{
					$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator.", $iconset);
					echo $membermsg;
				}
			}
		}else{
			$membermsg = member_error("Die Bestätigung ist fehlgeschlagen, bitte versuchen Sie es erneut!", $iconset);
			echo $membermsg;
		}
	}
	echo '
			<style>
				.check {
					height: 0;
					width: 0;
				}
				.checkBox {
					padding:8px;
					padding-left:8px;
					border:1px solid #56E459;
					font-size:11px;
					background-color:#CEFFCC;
					background-repeat:no-repeat;
					-moz-box-shadow: 0px 5px 7px -7px #000000;
					-webkit-box-shadow: 0px 5px 7px -7px #000000;
					box-shadow: 0px 5px 7px -7px #000000;
					-moz-border-radius: 4px;
					-webkit-border-radius: 4px;
					border-radius: 4px;width:250px;
					background: url("members/iconsets/clean/error.png") no-repeat;
					background-position: left center;
					background-origin: content-box;
				}
				.checkBox:hover{
					cursor: pointer;
				}
				.checkText{
					padding-left:20px;
				}
				.check:checked + .checkBox {
					font-weight: bold;
					background: url("members/iconsets/clean/ok.png") no-repeat;
					background-position: left center;
					background-origin: content-box;
				}
			</style>
						
			<script type="text/javascript">
				$(document).ready(function(){
					
				var sortcol=0;var sorttyp="asc"	
					
					$("#datatablea").dataTable({"sPaginationType": "full_numbers","aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0 ] }],"bAutoWidth": true,"aaSorting": [[sortcol,sorttyp]],"aLengthMenu": [10, 25, 50, 100],"iDisplayLength": 50,
						"oLanguage": {"sLengthMenu": "Zeige _MENU_ Einträge pro Seite","sZeroRecords": "Keine Einträge vorhanden!","sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen","sInfoEmpty": "Zeige 0 bis 0 von 0 Einträgen","sInfoFiltered": "(gefiltert aus _MAX_ Einträgen)","sSearch": "Suche:"}
					});	
				});
			</script>
			<div class="infobox">
			<div class="infobox_header">Banns</div>
								<div class="infobox_content">
									<div class="dataspace"></div>
									<div>
										<div class="iconlist">
											<div class="dataspace"></div>
											<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban&action=add"  rel="facebox"><img class="imghover" src="members/iconsets/clean/m_users.png" title="" style="background-color:#800732;"><br>Bann hinzufügen</a>
											<div class="dataspace"></div>
										</div>
										<div class="iconlist">
											<div class="dataspace"></div>
											<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban&action=delAll"  rel="facebox"><img class="imghover" src="members/iconsets/clean/m_users.png" title="" style="background-color:#800732;"><br>Bannliste leeren</a>
											<div class="dataspace"></div>
										</div>
									 </div>
									  <div class="clear"></div>
									  <div class="smallline"></div>
									<div class="dataspace"></div>
						<table cellpadding="0" cellspacing="0" border="0" class="display " id="datatablea" width="100%" style="width: 100%;">
										<thead>
											<tr role="row">
												<th class="close " style="width: 12px;">
													ID
												</th>
												<th style="">
													Gebannt von
												</th>
												<th style="">
													Gebannte IP
												</th>
												<th style="">
													Gebannte Eindeutige ID
												</th>
												<th style="">
													Grund
												</th> 
												<th style="">
													Gebannt am
												</th>
												<th style="">
													Gebannt bis
												</th>
												<th style="">
												</th>
											</tr>
										</thead>
										<tbody role="alert">';
										try{
						//Query Verbindung zum Teamspeak Server aufbauen
						$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
						
						//Snapshot erstellen
						$arr_BanList = $ts3_VirtualServer->banList();	
						
						#print '<pre>' . htmlspecialchars(print_r($arr_BanList, true)) . '</pre>';

						foreach($arr_BanList as $ts3_Bans){
							$bname = $ts3_VirtualServer->clientGetNameByDbid($ts3_Bans['invokercldbid']);
							if($ts3_Bans['duration'] == '0'){
								$banEnd = 'Permanent';
							}else{
								$time = time()+$ts3_Bans['duration'];
								$banEnd = date('d.m.Y H:i:s', $time);
							}
							
								echo '
											<tr class="odd" style="height:40px;">
												<td class="close ">
													<div class="datatxt">'.htmlspecialchars($ts3_Bans['banid']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($bname['name']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Bans['ip']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Bans['uid']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Bans['reason']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.date('d.m.Y H:i:s', $ts3_Bans['created']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.$banEnd.'</div>
												</td>
												<td><div class="dataimgcenter">';
												echo'<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban&action=banDelete&bid='.$ts3_Bans['banid'].'" rel="facebox" class="button_form">Bann aufheben</a>';
												echo'</div></td>
											</tr>
										';
							
						}
						}catch(TeamSpeak3_Exception $e){
										if($e->getMessage() == 'database empty result set'){
											
										}else{
											$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator. Nachricht: ", $iconset);
											echo $membermsg;
										}
										
						} 
							echo '</tbody>
									</table>
									<div class="clear"></div>
									</div>
									</div>
									
											<div class="dataspace"></div>';
									echo '
						
			<script type="text/javascript">
				$(document).ready(function(){
					
				var sortcol=0;var sorttyp="asc"	
					
					$("#datatableb").dataTable({"sPaginationType": "full_numbers","aoColumnDefs": [{ "bSortable": false, "aTargets": [ ] }],"bAutoWidth": true,"aaSorting": [[sortcol,sorttyp]],"aLengthMenu": [10, 25, 50, 100],"iDisplayLength": 50,
						"oLanguage": {"sLengthMenu": "Zeige _MENU_ Einträge pro Seite","sZeroRecords": "Keine Einträge vorhanden!","sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen","sInfoEmpty": "Zeige 0 bis 0 von 0 Einträgen","sInfoFiltered": "(gefiltert aus _MAX_ Einträgen)","sSearch": "Suche:"}
					});	
				});
			</script>
			<div class="infobox">
			<div class="infobox_header">Beschwerden</div>
								<div class="infobox_content">
									<div class="dataspace"></div>
									<div>
										<div class="iconlist">
											<div class="dataspace"></div>
											<a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=groups&action=reset"  rel="facebox"><img class="imghover" src="members/iconsets/clean/m_users.png" title="" style="background-color:#800732;"><br>Alle Beschwerden entfernen</a>
											<div class="dataspace"></div>
										</div>
									 </div>
									  <div class="clear"></div>
									  <div class="smallline"></div>
									<div class="dataspace"></div>
						<table cellpadding="0" cellspacing="0" border="0" class="display " id="datatableb" width="100%" style="width: 100%;">
										<thead>
											<tr role="row">
												<th class=" ">
													Eingereicht von
												</th>
												<th style="">
													Eingereicht gegen
												</th>
												<th style="">
													Beschwerde
												</th>
												<th style="">
													Datum
												</th>
												<th style=""> 
												</th>
											</tr>
										</thead>
										<tbody role="alert">';
										try{
						//Query Verbindung zum Teamspeak Server aufbauen
						$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$masterServer['admin'].":".$masterServer['passwd']."@".$masterServer['serverip'].":".$masterServer['queryport']."/?server_port=".$port."");
						
						//Snapshot erstellen
						$arr_ComplaintList = $ts3_VirtualServer->complaintList();	
						
						#print '<pre>' . htmlspecialchars(print_r($arr_ComplaintList, true)) . '</pre>';

						foreach($arr_ComplaintList as $ts3_Complaints){
							$fname = $ts3_VirtualServer->clientGetNameByDbid($ts3_Complaints['fcldbid']);
							$tname = $ts3_VirtualServer->clientGetNameByDbid($ts3_Complaints['tcldbid']);
								echo '
											<tr class="odd" style="height:40px;">
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($fname['name']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($tname['name']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.htmlspecialchars($ts3_Complaints['message']).'</div>
												</td>
												<td class=" ">
													<div class="datatxt">'.date('d.m.Y H:i:s', $ts3_Complaints['timestamp']).'</div>
												</td>
												<td class=" ">
													<div class="dataimgcenter"><a href="members.php?op=membersVoicesmore&ids='.$options['id'].'&voice=Teamspeak3&mod=ban&action=delComplaint&tid='.$ts3_Complaints['tcldbid'].'&fid='.$ts3_Complaints['fcldbid'].'" rel="facebox" class="button_form">Beschwerde entfernen</a></div>
												</td>
											</tr>
										';
							
						}
						}catch(TeamSpeak3_Exception $e){
										if($e->getMessage() == 'database empty result set'){
											
										}else{
											$membermsg = member_error("Verbindung zum Teamspeak Server fehlgeschlagen, bitte kontaktieren Sie einen Administrator. Nachricht: ", $iconset);
											echo $membermsg;
										}
										
						} 
							echo '</tbody>
									</table>
									<div class="clear"></div>
									</div>
									</div>';
				
		include("members/footer.php");
	}