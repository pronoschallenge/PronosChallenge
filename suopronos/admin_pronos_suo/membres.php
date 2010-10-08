<?php
//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                       */
// Copyright (c) 2004    Alexis MANGIN                                  */
// http://phpleague.univert.org                                         */
//                                                                      */
// This program is free software. You can redistribute it and/or modify */
// it under the terms of the GNU General Public License as published by */
// the Free Software Foundation; either version 2 of the License.       */
//                                                                      */
//***********************************************************************/
// Support technique : http://phpleague.univert.org/forum               */
//                                                                      */
//***********************************************************************/
?>
<br />
<?
define('IN_PHPBB', true);
$phpbb_root_path = '../../forum/';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

     if (isset($_POST['action'])) {$action=$_POST['action'];} 
     elseif (isset($_GET['action'])) {$action=$_GET['action'];}
	 else {$action='';}
	 
	 if($action=='supprimer') {
		if (isset($_GET['id']) && isset($_GET['username'])) {
			 
			 $id=$_GET['id'];
			 $username=$_GET['username'];			 
			 
			 //
			 // Suppression de l'utilisateur dans la base PronosChallenge
			 //
			 
			 mysql_query("DELETE FROM phpl_membres WHERE id='$id'") or die (mysql_error());
			
			 //
			 // Suppression de l'utilisateur dans le forum
			 //
			
			// récupération de l'id à partir du login
			$sql = "SELECT user_id 
				FROM " . USERS_TABLE . "  
				WHERE username = '$username'";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain group information for this user', '', __LINE__, __FILE__, $sql);
			}

			$row_username = $db->sql_fetchrow($result);			
			
			$user_id = $row_username['user_id'];
			
			$sql = "SELECT g.group_id 
				FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g  
				WHERE ug.user_id = $user_id 
					AND g.group_id = ug.group_id 
					AND g.group_single_user = 1";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain group information for this user', '', __LINE__, __FILE__, $sql);
			}

			$row = $db->sql_fetchrow($result);
			
			$sql = "UPDATE " . POSTS_TABLE . "
				SET poster_id = " . DELETED . ", post_username = '" . str_replace("\\'", "''", addslashes($username)) . "' 
				WHERE poster_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update posts for this user', '', __LINE__, __FILE__, $sql);
			}

			$sql = "UPDATE " . TOPICS_TABLE . "
				SET topic_poster = " . DELETED . " 
				WHERE topic_poster = $user_id";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update topics for this user', '', __LINE__, __FILE__, $sql);
			}
			
			$sql = "UPDATE " . VOTE_USERS_TABLE . "
				SET vote_user_id = " . DELETED . "
				WHERE vote_user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update votes for this user', '', __LINE__, __FILE__, $sql);
			}
			
			$sql = "SELECT group_id
				FROM " . GROUPS_TABLE . "
				WHERE group_moderator = $user_id";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not select groups where user was moderator', '', __LINE__, __FILE__, $sql);
			}
			
			while ( $row_group = $db->sql_fetchrow($result) )
			{
				$group_moderator[] = $row_group['group_id'];
			}
			
			if ( count($group_moderator) )
			{
				$update_moderator_id = implode(', ', $group_moderator);
				
				$sql = "UPDATE " . GROUPS_TABLE . "
					SET group_moderator = " . $userdata['user_id'] . "
					WHERE group_moderator IN ($update_moderator_id)";
				if( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not update group moderators', '', __LINE__, __FILE__, $sql);
				}
			}

			$sql = "DELETE FROM " . USERS_TABLE . "
				WHERE user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete user', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . USER_GROUP_TABLE . "
				WHERE user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete user from user_group table', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . GROUPS_TABLE . "
				WHERE group_id = " . $row['group_id'];
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete group for this user', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
				WHERE group_id = " . $row['group_id'];
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete group for this user', '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
				WHERE user_id = $user_id";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete user from topic watch table', '', __LINE__, __FILE__, $sql);
			}
			
			$sql = "DELETE FROM " . BANLIST_TABLE . "
				WHERE ban_userid = $user_id";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete user from banlist table', '', __LINE__, __FILE__, $sql);
			}

			$sql = "SELECT privmsgs_id
				FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_from_userid = $user_id 
					OR privmsgs_to_userid = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not select all users private messages', '', __LINE__, __FILE__, $sql);
			}

			// This little bit of code directly from the private messaging section.
			while ( $row_privmsgs = $db->sql_fetchrow($result) )
			{
				$mark_list[] = $row_privmsgs['privmsgs_id'];
			}
			
			if ( count($mark_list) )
			{
				$delete_sql_id = implode(', ', $mark_list);
				
				$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
					WHERE privmsgs_text_id IN ($delete_sql_id)";
				$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_id IN ($delete_sql_id)";
				
				if ( !$db->sql_query($delete_sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete private message info', '', __LINE__, __FILE__, $delete_sql);
				}
				
				if ( !$db->sql_query($delete_text_sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete private message text', '', __LINE__, __FILE__, $delete_text_sql);
				}
			}			
			
			 echo "<div style=\"color: black; font-size:10pt; font-family: verdana;\"><img src=\"images/success.gif\" />&nbsp;Suppression du membre effectué</div><br />";
			  
		} else {
			echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\"><img src=\"images/error.gif\" />&nbsp;Erreur lors de la suppression de l'utilisateur : id introuvable !</div><br />";
		}
	 }
?>

<br />

<div align="left">
	<a href="index.php?page=ajout_membre">
		<img src="./adduser.gif" border="0" />
		&nbsp;Ajouter un pronostiqueur
	</a>
</div>

<br />
<?php
$requete="SELECT * FROM phpl_membres order by id";
$resultats=mysql_query($requete);
$nb_membres=mysql_num_rows( $resultats );
?>
<table border="0" width="100%">
  <tr>
    <td width="1%" bgcolor="#C0C0C0">
      &nbsp;</td>
    <td width="3%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">id</font></td>
    <td width="12%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">pseudo</font></td>
    <td width="20%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">mail</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">nom</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">prénom</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">adresse</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">code postal</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">ville</font></td>
    <td width="11%" bgcolor="#C0C0C0">
      <p align="center"><font face="Verdana" size="2">Tel</font></td>
  </tr>
<?php
$i=0;
while ($row=mysql_fetch_array($resultats))
{
if (($i%2)==0) $color="#FFFFFF";
else  $color="#CCCCCC";
?>
  <tr>
    <td width="1%" bgcolor="<?php print $color?>">
      <p align="center"><a onclick="return confirm('Etes-vous sûr de vouloir supprimer ce membre ?')" href="index.php?page=membres&action=supprimer&id=<?php print $row["id"] ?>&username=<?php print $row["pseudo"] ?>"><img src="images/delete.png" border="0" /></a></td>  
    <td width="3%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["id"] ?></font></td>
    <td width="12%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["pseudo"] ?></font></td>
    <td width="20%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["mail"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["nom"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["prenom"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["adresse"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["code_postal"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["ville"] ?></font></td>
    <td width="11%" bgcolor="<?php print $color?>">
      <p align="center"><font face="Verdana" size="2"><?php print $row["mobile"] ?></font></td>
  </tr>
<?php
$i++;
}

?></table>

<textarea rows="11" cols="57">
<?php
$requete="SELECT * FROM phpl_membres order by id";
$resultats=mysql_query($requete)  or die ("probleme " .mysql_error());;
while ($row=mysql_fetch_array($resultats))
{print $row['mail']; echo ";";}
?>
</textarea>
