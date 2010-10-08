<html>
<head>
</head>
<body>
<?

	define('IN_PHPBB', true);
	$phpbb_root_path = '../../forum/';
	include($phpbb_root_path . 'extension.inc');
	include($phpbb_root_path . 'common.'.$phpEx);

	//
	// Récupération des utilisateurs n'ayant pas de comptes dans le forum
	//

	$tabUsersPC = array();
	$tabUsersForum = array();

	$sql = "SELECT pseudo FROM phpl_membres order by id;";
	$result = mysql_query($sql);
	while ($row=mysql_fetch_array($result))
	{
		$tabUsersPC[] = $row['pseudo'];
	}
	
	$sqlForum = "SELECT username FROM ".USERS_TABLE.";";
	$resultForum = mysql_query($sqlForum);
	while ($rowForum=mysql_fetch_array($resultForum))
	{
		$tabUsersForum[] = $rowForum['username'];
	}	

	$count = count($tabUsersPC); 
	for($i=0; $i<$count; $i++)
	{
		$existeDansForum = false;
		$countForum = count($tabUsersForum); 
		for($j=0; $j<$countForum; $j++)
		{	
			if(strcasecmp($tabUsersPC[$i], $tabUsersForum[$j]) == 0)
			{
				$existeDansForum = true;
				break;
			}
		}	
		
		if(!$existeDansForum)
		{
				//
				// ajout du nouveau membre dans le forum
				// 
				
				$query1 = 'SELECT MAX(user_id) AS total FROM '.USERS_TABLE.';';
				$result1 = mysql_query($query1);
				$row1 = mysql_fetch_array($result1);

				$id_user_phpbb = $row1['total'] + 1;        

				$registred = time();

				$sql = "SELECT mot_de_passe, mail FROM phpl_membres WHERE pseudo='$tabUsersPC[$i]';";
				$result = mysql_query($sql);
				$row=mysql_fetch_array($result);
				$mot_de_passe=$row['mot_de_passe'];
				$mail=$row['mail'];
				   
				$query2 = "INSERT INTO ".USERS_TABLE."
				  (
				    user_id,
				    user_active,
				    user_actkey,
				    username,
				    user_password,
				    user_session_time,
				    user_session_page,
				    user_lastvisit,
				    user_regdate,
				    user_level,
				    user_posts,
				    user_style,
				    user_lang,
				    user_dateformat,
				    user_new_privmsg,
				    user_unread_privmsg,
				    user_last_privmsg,
				    user_viewemail,
				    user_attachsig,
				    user_allowhtml,
				    user_allowbbcode,
				    user_allowsmile,
				    user_allowavatar,
				    user_allow_pm,
				    user_allow_viewonline,
				    user_notify,
				    user_notify_pm,
				    user_popup_pm,
				    user_rank,
				    user_avatar_type,
				    user_email
				  )
				  VALUES
				  (
				    '".$id_user_phpbb."',
				    '1',
				    '',
				    '".$tabUsersPC[$i]."',
				    '".$mot_de_passe."', 
				    '0',
				    '0',
				    '0',
				    '".$registred."', 
				    '0',
				    '0',
				    '1',
				    'french',
				    'd M Y h:i a',
				    '0',
				    '0',
				    '0',
				    '0',
				    '1',
				    '0',
				    '1',
				    '1',
				    '1',
				    '1',
				    '1',
				    '0',
				    '1',
				    '1',
				    '0',
				    '0',
				    '".$mail."'
				  );";
				$result2 = mysql_query($query2) or die ("probleme " .mysql_error());
				 
				$query3 = "INSERT INTO ".GROUPS_TABLE." (group_name, group_description, group_single_user, group_moderator) VALUES ('".$tabUsersPC[$i]."', 'Personal User', 1, 0)";
				$result3 = mysql_query($query3) or die ("probleme " .mysql_error());
				 
				$group_id = mysql_insert_id();
				 
				$query4 = "INSERT INTO ".USER_GROUP_TABLE."
				  (
				    group_id,
				    user_id,
				    user_pending
				  )
				  VALUES
				  (
				    '".$group_id."',
				    '".$id_user_phpbb."',
				    '0'
				  );";
				$result4 = mysql_query($query4) or die ("probleme " .mysql_error());		
				
				echo "- compte ".$tabUsersPC[$i]." créé !<br />";
		}
	}

?>
</body>
</html>
