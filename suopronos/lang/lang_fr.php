	<?php

// ADMINISTRATION

// Setup

define("SETUP_HOST_NAME","Host name");
define("SETUP_DATABASE","Nom de la base de donnée");
define("SETUP_LOGIN","Identifiant");
define("SETUP_PASSWORD","Mot de passe");
define("SETUP_TYPE","Type d'installation");
define("SETUP_NORMALE","Installation normale");
define("SETUP_MAJ","Mise à jour de la version");
define("SETUP_TITRE_SITE","Titre de votre site (facultatif pour la màj de la v0.81)"); // 0.82
define("SETUP_URL_SITE","Adresse de votre site"); // 0.82
define("SETUP_PSEUDO","Login (pour l'administration et pour les pronostics facultatif pour la màj de la v0.81)"); // 0.82
define("SETUP_MDP","Mot de passe (pour l'administration et pour les pronostics facultatif pour la màj de la v0.81)"); // 0.82
define("SETUP_MAIL","E-mail (facultatif pour la màj de la v0.81)"); // 0.82
define("SETUP_ERREUR","Impossible d'effectuer la requête pour");
define("SETUP_ERREUR_2","Voici le message d'erreur renvoyé par la base de données");
define("SETUP_ID_INCORRECTS","Vos Identifiants sont incorrects !");
define("SETUP_TABLE","La table des");
define("SETUP_TABLE_2","a été correctement créée");
define("SETUP_TABLE_3","Créer la table des");
define("SETUP_TABLE_4","créer le compte de l'administrateur pour la gestion et les pronostics");
define("SETUP_TABLE_5","Le compte de l'administrateur a été correctement créé");
define("SETUP_CONFIRMATION","Vous avez bien configurez le script !");
define("SETUP_FIN","Pour plus de sécurité, vous devez à présent supprimer le fichiers install.php !
                    Ensuite rendez-vous dans l'<a href=\"admin\">administration</a> pour commencer à utiliser PhpLeague !");
define("SETUP_REMPLIR_CHAMP","Veuillez remplir tous les champs !");
define("SETUP_REPERTOIRE_SCRIPT","Répertoire du script");    // 0.82
define("SETUP_MAJ_ACHEVEE","Mise à jour achevée");    // 0.82
define("SETUP_MAJ_INCOH","Incohérence");    // 0.82
define("SETUP_MAJ_INCOH_2","a marqué sous le maillot de");    // 0.82


//Menu
define("MENU_FICHES_CLUBS","Fiches Clubs");
define("MENU_ID","Id");
define("MENU_NOM","Nom");
define("MENU_CREDITS","Crédits");
define("MENU_MEMBRES","Membres");

// Championnats
define("LEAGUE","Championnats");
define("DATE","Dates");
define("TEAM","Equipes");
define("MATCH","Matchs");
define("PARAMETRE","Paramètres");
define("GENERER","Générer"); //0.82
define("JOUEURS","Joueurs"); // 0.82
define("BUTEUR","Buteurs");
define("RESULT","Résultats");
define("EQUIPE","Equipes");
define("ADMIN_CHAMP_CREER","Créer un championnat");

// Groupes championnats
define("EDITER","Editer");
define("GR_LEAGUE","Groupes de championnats");
define("ADMIN_GR_CHAMP_CREER","Créer un groupe de championnats");
define("ADMIN_GR_CHAMP_GENERER","Générer");



// Création d'un groupe de championnat
define("ADMIN_GR_CHAMPIONNATS_CREA","Création d'un goupe de championnats");
define("ADMIN_GR_CHAMP_CREER_1","Nom du groupement");

//Suppression d'un groupe de championnats
define("ADMIN_GR_CHAMPIONNATS_SUPP","Suppression d'un goupe de championnats");
define("ADMIN_GR_CHAMPIONNATS_SUPP1","Etes vous sure de vouloir supprimer le goupe de championnat");
define("ADMIN_GR_CHAMPIONNATS_SUPP2"," ?");

//Edition d'un groupe de championnats
define("ADMIN_GR_CHAMP_EDIT","Edition du groupe de championnats");
define("ADMIN_GR_CHAMP_EDIT_1","Sélectionner les championnats à regrouper dans");
define("ADMIN_GR_CHAMP_EDIT_2","Sélection");
define("ADMIN_GR_CHAMP_EDIT_3","Championnats à retirer du groupe");
define("ADMIN_GR_CHAMP_EDIT_4","Mettre à jour");
define("ADMIN_GR_CHAMP_EDIT_5","Liste actuelle");



//Suppression de championnat
define("ADMIN_CHAMPIONNATS_SUPP","Suppression de championnat");
define("ADMIN_CHAMPIONNATS_SUPP1","Etes vous sure de vouloir supprimer le championnat");
define("ADMIN_CHAMPIONNATS_SUPP2","ainsi que toutes les rencontres attachées ?");

// Création de championnat
define("ADMIN_CHAMPIONNATS_CREA","Création d'un championnat");
define("ADMIN_CHAMPIONNATS_CREA2","Choisir");
define("ADMIN_JOURNEES_MSG3","Saison");
define("ADMIN_CHAMP_CREER_2","Division");
define("ADMIN_CHAMP_CREER_3","Créer");
define("ADMIN_CHAMP_CREER_4","Saison (1ère année)");

// Equipes
define("ADMIN_CLUB_NOM","Nom du club :");
define("ADMIN_CLUBS_CREE","Création des clubs");
define("ADMIN_EQUIPE_TITRE","Edition des équipes de");
define("ADMIN_EQUIPE_1","Clubs à supprimer :");
define("ADMIN_EQUIPE_2","Clubs à ajouter dans");
define("ADMIN_EQUIPE_3","(Choix multiple possible avec la touche SHIFT et CTRL)");
define("ADMIN_EQUIPE_4","Clubs à retirer de");

// Dates
define("ADMIN_DATES_TITRE","Dates des journées de");
define("ADMIN_JOURNEES_MSG9","Journée N°");
define("ADMIN_JOURNEES_MSG10","sous la forme <b>JJMMAAAA<b>");
define("ADMIN_DATES_1","Heure des matchs par défaut");
define("ADMIN_DATES_2","Attention, en faisant cette opération vous imposez la même heure à tous les matchs de la saison. Si vous ne voulez changer que la date ou l'heure d'un match ou d'une journée passez plutôt par les");
define("ADMIN_DATES_HEURES","h");
define("ADMIN_DATES_MINUTES","min");
define("ENVOI","Envoi");
define("BOUTON_CONSULT_CLASSEMENT","Afficher");
define("ADMIN_DATES_3","Votre championnat comporte un nombre d'équipes impaire. Rajoutez une équipe 'exempte'.");
define("ADMIN_DATES_4","Aucune");
define("ADMIN_DATES_5","Vous devez d'abord créer les équipes jouant dans ce championnat. Allez dans");


// Matchs
define("JOURNEE_MIROIR","Journée Miroir ? :");
define("ADMIN_MATCHS_TITRE","Matchs de");
define("ADMIN_COHERENCE_TITRE","Contrôle de cohérence du calendrier");
define("ADMIN_COHERENCE_MSG2","J");
define("ADMIN_COHERENCE_MSG3"," cohérente");
define("ADMIN_COHERENCE_MSG4","<b>incohérente ou incomplète</b>");
define("ADMIN_COHERENCE_MSG5","Ce championnat semble cohérent");
define("ADMIN_COHERENCE_MSG6","Ce championnat semble incohérent");
define("ADMIN_COHERENCE_MSG7","Effectuer un contrôle de cohérence");
define("DOMICILE","Domicile");
define("EXTERIEUR","Extérieur");
define("ADMIN_MATCHS_1","Les journées n'ont pas encore été créées. Allez d'abord dans");

// Paramètres
define("ADMIN_PARAM_MSG2","Points pour une victoire ?");
define("ADMIN_PARAM_MSG3","Points pour un nul ?");
define("ADMIN_PARAM_MSG4","Points pour une défaite ?");
define("ADMIN_PARAM_MSG5","Nombre d'équipe pour l'accession directe ?");
define("ADMIN_PARAM_MSG6","Nombre d'équipe pour l'accession en barrages ?");
define("ADMIN_PARAM_MSG7","Nombre d'équipe pour la rélégation ?");
define("ADMIN_PARAM_MSG8","Votre équipe préférée ?");
define("ADMIN_PARAM_TITRE","Paramètres de");
define("ADMIN_TAPVERT_TITRE","Tapis vert de");
define("ADMIN_PARAM_MSG9","Points prono exact");  
define("ADMIN_PARAM_MSG10","Points participation à un prono"); 
define("ADMIN_PARAM_MSG11","Pseudo du pronostiqueur référence");   
define("ADMIN_PARAM_MSG12","Indiquer le nombre d'heure entre la fin de validation de la grille et le match");
define("ADMIN_PARAM_MSG13","Pronostics de");
define("ADMIN_TAPVERT_MSG1","Ici, vous pouvez gérer les points de pénalité (sanctions administratives, forfaits, etc ...)");
define("ADMIN_TAPVERT_MSG3","Entrez les points de pénalité (Ex: -1, -2, ...)");
define("ADMIN_TAPVERT_MSG4","Activer les fiches clubs ?");
define("ADMIN_TAPVERT_MSG5","Activer les pronostics ?");
define("ADMIN_TAPVERT_MSG6","Activer les estimations dans la page du classement ? (Attention, cette option ralentit le chargement de la page)"); // 0.82


// Résultats
define("ADMIN_RESULTS_TITRE","Résultats de");
define("ADMIN_RESULTS_1","Exemptée");

// Buteurs
//define("ADMIN_JOUEURS_TITRE","Fiches joueurs");
define("ADMIN_BUTEUR_TITRE","Buteurs de");

//Graphiques
define("ADMIN_GRAPH_TITRE","Génération des graphiques et des pronos de");
define("ADMIN_GRAPH","La création des graphiques et des classements a été réalisée avec succès"); //0.82
define("ADMIN_GRAPH_PRONO","Le classement des membres a été réalisée avec succès"); //0.82
define("ADMIN_GRAPH_1","La création des graphiques a échoué, veuillez réessayer !");
define("ADMIN_GRAPH_2","Cette manoeuvre est à effectuer après chaque ajout de résultats. Elle peut prendre un certain temps...");
define("ADMIN_GRAPH_3","Evolution du classement de");
define("ADMIN_GRAPH_4","Erreur lors de la création de graphique, réessayez !<br />
                      Si le problème persiste modifier le max_execution_time dans php.ini.");
define("ADMIN_GRAPH_5","secondes");
define("ADMIN_GRAPH_6","en");

// Mini-classement
define("ADMIN_MINI_1","Mini-Classement");
define("ADMIN_MINI_2","Choisissez la présentation");
define("ADMIN_MINI_3","Choisissez le type de classement");
define("ADMIN_MINI_4","Choisissez le championnat");
define("ADMIN_MINI_5","Nombre d'équipe au dessus de l'équipe fétiche");
define("ADMIN_MINI_6","Editer le code");
define("ADMIN_MINI_7","Remarques");
define("ADMIN_MINI_8","Championnat non renseigné !");
define("ADMIN_MINI_9","Type de classement non renseigné !");
define("ADMIN_MINI_10","Présentation non renseignée !");
define("ADMIN_MINI_11","Code invalide !");
define("ADMIN_MINI_12","Aperçu");
define("ADMIN_MINI_13","Voici le code à ajouter dans vos pages :");
define("ADMIN_MINI_14","Nombre d'équipe en dessous de l'équipe fétiche");
define("ADMIN_MINI_15","Souhaitez-vous laisser le lien sur les équipes ?");
define("ADMIN_MINI_17","Ne pas afficher le classement complet");
define("ADMIN_MINI_18","Afficher le classement complet");
define("ADMIN_MINI_19","Nombre d'équipe au dessus non renseigné !");
define("ADMIN_MINI_20","Nombre d'équipe en dessous non renseigné !");
define("ADMIN_MINI_21","Le code est valide !<br /> Réajustez si besoin la taille de l'iframe.");     // 0.82
define("ADMIN_MINI_22","Couleur");
define("ADMIN_MINI_23","Barres");
define("ADMIN_MINI_24","Editeur");

// FICHIER admin/classe.php
define("ADMIN_CLASSE_TITRE","Edition des classes");
define("ADMIN_CLASSE_SUPP1","Suppression de la classe :");
define("ADMIN_CLASSE_BUTTON_SUPP","Suppression classe");
define("ADMIN_CLASSE_CREA","<b>Ajout</b> d'une classe");
define("ADMIN_CLASSE_NOM","Nom de la classe :");
define("ADMIN_CLASSE_BUTTON_CREA","Création classe");
define("ADMIN_CLASSE_BUTTON_MSG3","Pour <b>modifier</b> le nom d'une classe, utilisez PHPMyAdmin");
define("ADMIN_CLASSE_1","Classement des classes : 1 : 1er, 2 : 2e...");
define("ADMIN_CLASSE_2","<b>Paramètres enregistrés !</b>");
define("ADMIN_CLASSE_3","Vous n'avez pas le droit de supprimer cette classe car elle utilisée par");
define("ADMIN_CLASSE_4","renseignement(s). Supprimez le(s) renseignement(s) contenu(s) dans cette classe avant de la supprimer !");
define("ADMIN_CLASSE_5","Informations");
define("ADMIN_CLASSE_CLASSE","Classes");  // 0.82
define("ADMIN_CLASSE_RENS","Renseigenents");  // 0.82
define("ADMIN_CLASSE_GEST","Gestion des clubs");  // 0.82



// FICHIER admin/gestequipes.php
define("ADMIN_GESTEQUIPE_TITRE","Consultation des clubs");
define("ADMIN_GESTEQUIPE_2","Choisissez un club : ");
define("ADMIN_EQUIPE_17","Edition des renseignements de l'équipe");
define("ADMIN_GESTEQUIPE_1","Réglage des paramètres de : ");
define("ADMIN_GESTEQUIPE_3","Nom du renseignement");
define("ADMIN_GESTEQUIPE_4","Valeur du renseignement");
define("ADMIN_EQUIPE_5","Url");
define("ADMIN_EQUIPE_6","Afficher (=1)<br />ou non (=0)");
define("ADMIN_EQUIPE_7","Url logo : ");
define("ADMIN_EQUIPE_8", "Non renseigné");


// FICHIER admin/rens.php
define("ADMIN_RENS_TITRE","Edition des renseignements");
define("ADMIN_RENS_SUPP1","<b>Suppression</b> d'un renseignement ");
define("ADMIN_RENS_BUTTON_SUPP","Suppression renseignement");
define("ADMIN_RENS_CREA","<b>Ajout</b> d'un renseignement");
define("ADMIN_RENS_NOM","Nom du renseignement : ");
define("ADMIN_RENS_BUTTON_CREA","Création renseignements");
define("ADMIN_RENS_CREA2","<b>Création effectuée</b>");
define("ADMIN_RENS_SUPP2","<b>Suppression effectuée</b>");
define("ADMIN_RENS_1"," dans la classe : ");
define("ADMIN_RENS_2","Vous n'avez pas le droit de supprimer ce renseignement car il est utilisé ");
define("ADMIN_RENS_3"," fois dans les renseignements.");
define("ADMIN_RENS_4","<b>Insérez</b> les renseignements dans les classes :");
define("ADMIN_RENS_5","<b>Supprimez</b> les paramètres des renseignements (Choix multiple possible avec la touche SHIFT et CTRL) : ");
define("ADMIN_RENS_6"," dans ");
define("ADMIN_RENS_7","Ajouter");
define("ADMIN_RENS_8","Supprimer");
define("ADMIN_RENS_9","Ordonner les renseignements : 1 pour le 1er, 2 pour le 2e...");
define("ADMIN_RENS_10","<b>Editer</b> les renseignements");
define("ADMIN_RENS_11","Enregistrer");
define("ADMIN_RENS_12","Nom du renseignement");
define("ADMIN_RENS_13","Url du renseignement (facultatif)");
define("ADMIN_RENS_14","Renseignements à classer :");
define("ADMIN_RENS_15","Tous les renseignements sont classés");
define("ADMIN_RENS_16","Etes-vous sure de vouloir supprimer le renseignement");
define("ADMIN_RENS_17","Oui");
define("ADMIN_RENS_18","Non");

/* ZONE PUBLIQUE : CONSULTATION */


// Entete et index
define("CONSULT_HOME","Accueil");
define("CONSULT_CALENDAR","Calendriers");
define("CONSULT_CLASSEMENT","Classements");
define("CONSULT_BUTEUR","Buteurs");
define("CONSULT_DUEL","Duels");
define("MENU_UTILISATEUR","Menu utilisateur");
define("CONSULT_PRONOSTICS","Pronostics");      // 0.82




//classement
define("CONSULT_CLMNT_MSG1","Type de classement :");
define("ADMIN_TAPVERT_MSG2","Quel championnat :");
define("GENERAL","General");
define("ATTAQUE","Attaque");
define("DEFENSE","Défense");
define("GOALDIFF","Goal Average");
define("CONSULT_CLMNT_MSG2"," de la journée ");
define("CONSULT_CLMNT_MSG3"," à la journée ");
define("CONSULT_CLMNT_MSG4","Classement général - Journées ");
define("CONSULT_CLMNT_MSG5"," à ");
define("CONSULT_CLMNT_MSG6","Dernière journée (n°");
define("CONSULT_CLMNT_MSG61","Précédente journée (n°");
define("CONSULT_CLMNT_MSG62","Prochaine journée (n°");
define("CONSULT_CLMNT_MSG7","ESTIMATION DES SCORES DE LA PROCHAINE JOURNEE :");
define("CONSULT_CLMNT_MSG8","Estim. prochaine journée (n°");
define("CONSULT_CLMNT_MSG9","Prochaine journée : n° ");
define("CONSULT_CLMNT_MSG10","Classement à domicile - Journées ");
define("CONSULT_CLMNT_MSG11","Classement des attaques - Journées ");
define("CONSULT_CLMNT_MSG12","Classement des défenses - Journées ");
define("CONSULT_CLMNT_MSG13","Classement au Goal Average - Journées ");
define("CONSULT_CLMNT_MSG14","Classement à l'extérieur - Journées ");
define("CLMNT_POSITION","Pl");
define("CLMNT_EQUIPE","Equipe");
define("CLMNT_POINTS","Points");
define("CLMNT_JOUES"," J ");
define("CLMNT_VICTOIRES","V ");
define("CLMNT_NULS","N ");
define("CLMNT_DEFAITES","D ");
define("CLMNT_BUTSPOUR","BP ");
define("CLMNT_BUTSCONTRE","BC ");
define("CLMNT_DIFF","Diff. ");
define("EXEMPT","Exempt");
define("LEAGUE_LANGUAGE","french");

// Matchs
define("CONSULT_MATCHS","Consultation des calendriers");
define("CONSULT_MATCHS_MSG1","Quel championnat voulez vous consulter ?");
define("CONSULT_MATCHS_MSG2"," le ");


// FICHIER consult/equipes.php
define("CONSULT_INDEX_1","Consultation des équipes");
define("CONSULT_INDEX_2","Fondation");

// Detail equipe
define("VICTOIRE","VICTOIRE ");
define("NUL"," NUL ");
define("DEFAITE"," DEFAITE");
define("JOURNEE","N°");
define("DETAILEQ_TITRE","Choix équipe");
define("DETAILEQ_1","Equipe :");


// calendrier_1.php
define("CONSULT_CALENDAR_1","Cette journée n'existe pas");
define("CONSULT_CALENDAR_2","Journée précédente");
define("CONSULT_CALENDAR_3","Journée suivante");
define("CONSULT_CALENDAR_4","Matchs précédentsournée n°");
define("CONSULT_CALENDAR_5","Derniers résultats : journée n°");


// divers
define("RETOUR","Retour");

// FICHIER admin/clubs.php
define("ADMIN_CLUB_TITRE","Edition des clubs");
define("ADMIN_CLUB_SUPP1","<b>Suppression</b> d'un club ");
define("ADMIN_CLUB_BUTTON_SUPP","Suppression club");
define("ADMIN_CLUB_CREA","<b>Ajout</b> d'un club");
define("ADMIN_CLUB_BUTTON_CREA","Création club");
define("ADMIN_CLUB_BUTTON_MSG3","Pour <b>modifier</b> le nom d'un club, utilisez PHPMyAdmin");
define("ADMIN_CLUB_CREA2","<b>Création effectuée</b>");
define("ADMIN_CLUB_SUPP2","<b>Suppression effectuée</b>");




// *********************************************
// ***** NEW ITEMS ADDED DECEMBER 22th 2001 ****
// *********************************************


// consult/buteurs
define("CONSULT_BUTEUR_MSG1","Quel groupe de championnat ?");
define("CONSULT_BUTEUR_MSG2","Classement Buteurs");
define("CONSULT_BUTEUR_TITRE_1","Classement des buteurs");    // 0.82
define("CONSULT_BUTEUR_MSG3","Groupe de Championnats : ");
define("CONSULT_BUTEUR_MSG4","comprenant : ");
define("CONSULT_BUTEUR_MSG5","Quelle équipe ?");
define("DUEL_MSG1","Choisissez les adversaires : ");
define("DUEL_MSG2"," Duels");
define("DUEL_MSG3","Voici les probabilités de l'ordinateur ");
define("DUEL_MSG4","PROBABILITES : ");
define("DUEL_MSG5","Les probabilités affichées sont le reflet d'un calcul mathématique simple");

// consult/club
define("CONSULT_CLUB_1","Classement");
define("CONSULT_CLUB_2","Calendrier et résultats");
define("CONSULT_CLUB_3","Historique");
define("CONSULT_CLUB_4","Statistiques");

// Sécurité
define("ADMIN_SECURITE_CLUB","Etes vous sur de vouloir supprimer le club suivant :");
define("ADMIN_SECURITE_RENS","Etes vous sur de vouloir supprimer le renseignement suivant :");
define("ADMIN_SECURITE_SAISONS","Etes vous sur de vouloir supprimer la saison ");
define("ADMIN_SECURITE_SAISONS_2","ainsi que les championnats et les rencontres attachées");
define("ADMIN_SECURITE_CLASSE","Etes vous sur de vouloir supprimer la classe suivante :");
define("ADMIN_SECURITE_CHAMP","Etes vous sur de vouloir supprimer le championnat suivant :");

// 25/12/2003

//define("ADMIN_BUTEUR_TITRE","Joueurs de ");
define("ADMIN_BUTEUR_TITRE2","Buteurs de ");

// Joueurs et buteurs
define("ADMIN_BUTEURS_TITRE","Edition des Buteurs");
define("ADMIN_BUTEURS_MSG1","Quel championnat voulez vous saisir ?");
define("ADMIN_BUTEURS_MSG2","Quelle journée voulez vous saisir ?");
define("ADMIN_BUTEURS_LAST","Préc.");
define("ADMIN_BUTEURS_NEXT","Suiv.");
define("ADMIN_BUTEURS_MSG3","validation_et_buteur_suivant"); // laisser des _ à la place des espaces
define("ADMIN_JOUEURS_TITRE","Edition des Joueurs");
define("ADMIN_JOUEURS_MSG1","<b>Suppression</b> d'un joueur <br />Cette manipulation supprime également les buts marqués par le joueur au cours de ce championnat"); // 0.82
define("ADMIN_JOUEURS_MSG2","Suppression");
define("ADMIN_JOUEURS_MSG3","<b>Ajout</b> d'un joueur");
define("ADMIN_JOUEURS_MSG4","Prénom :  ");
define("ADMIN_JOUEURS_MSG5","Nom :  ");
define("ADMIN_JOUEURS_MSG6","Son équipe :  "); // 0.82
define("ADMIN_JOUEURS_MSG7","URL Photo : ");
define("ADMIN_JOUEURS_MSG8","Date de Naissance : (JJMMAAAA) ");
define("ADMIN_JOUEURS_MSG9","Position Terrain :  ");
define("ADMIN_JOUEURS_MSG10","Pour <b>modifier</b> le nom d'un joueur, utilisez PHPMyAdmin");
define("ADMIN_JOUEURS_1","<b>Entrez</b> les buteurs match par match. Pour <b>supprimer</b> un buteur, cliquez sur celui-ci.");
define("ADMIN_JOUEURS_2","Buts");
define("ADMIN_JOUEURS_3","équipe de");
define("ADMIN_JOUEURS_TRANSFERT","Transférer"); // 0.82
define("ADMIN_JOUEURS_TRANSFERT_VERS","à transférer vers"); // 0.82
define("ADMIN_JOUEURS_EDITER","Choisir le joueur à éditer :"); // 0.82
define("ADMIN_JOUEURS_EDITER_2","Edition du joueur :"); // 0.82


// Pronostics   // 0.82

// Menu identifié
define("PRONO_MENU_MON_COMPTE","MON COMPTE");
define("PRONO_MENU_MES_CLASSEMENTS","MES CLASSEMENTS");
define("PRONO_MENU_MES_PRONOS","MES PRONOSTICS");
define("PRONO_MENU_LES_CHAMPIONNATS","LES CHAMPIONNATS");
define("PRONO_MENU_BIENVENUE","");
define("PRONO_MENU_POINTS","POINTS");
define("PRONO_MENU_MON_PROFIL","MON PROFIL");
define("PRONO_MENU_DECONNEXION","Déconnexion");
define("PRONO_MENU_GENERAL","GENERAL");
define("PRONO_MENU_MOIS","MOIS");
define("PRONO_MENU_PROCHAINE_GRILLE","Pronos à valider");
define("PRONO_MENU_DERNIERE_GRILLE","Derniers pronos");
define("PRONO_MENU_MES_RESULTATS","Statistiques");
define("PRONO_MENU_REGLEMENT","Règlement");
define("PRONO_MENU_LOTS","Lots");
define("PRONO_MENU_BAREME","BAREME");
define("FORUM_LINK","Forum");
define("MON_COMPTE_LINK","Mon compte");
define("PRONO_MENU_CLASSEMENT_L1","Infos L1");

// Menu non identifié
define("PRONO_MENU_LOGIN","IDENTIFIANT");
define("PRONO_MENU_MDP","MOT DE PASSE");
define("PRONO_MENU_OUBLIE","Mot de passe oublié ?");

// Mot de passe oublié
define("PRONO_OUBLIE_PERDU","Vous avez oublié votre mot de passe ?");
define("PRONO_OUBLIE_TEXTE_1","Entrez votre adresse e-mail, un nouveau mot de passe vous sera alors envoyé.");
define("PRONO_OUBLIE_TEXTE_2","Votre nouveau mot de passe vous a été envoyé à l'adresse");
define("PRONO_OUBLIE_TEXTE_3","Le mot de passe ne peut vous être envoyé !<br />Renouvelez votre demande dans un instant, merci.");
define("PRONO_OUBLIE_TEXTE_4","Cette adresse email ne correspond à aucun compte...");


//accueil
define("PRONO_ACCUEIL_PRESENTATION","vous présente ses pronostics pour la prochaine grille");

// GRILLE
define("PRONO_GRILLE_PRONO","Prono");
define("PRONO_GRILLE_TEMPS","Temps");
define("PRONO_GRILLE_PRECEDENT","Matchs précédents");
define("PRONO_GRILLE_SUIVANT","Matchs suivants");
define("PRONO_GRILLE_SCORE","Score réel");
define("PRONO_GRILLE_EXPIRE","Expiré");

// Valider grille
define("PRONO_GRILLE_CONFIRME","Vos pronostics ont bien été enregistrés !");
define("PRONO_GRILLE_CONFIRME_SUITE","Vous pouvez les modifier jusqu'au début des matchs.");
define("PRONO_GRILLE_PROCHAINE","Prochaine grille");

define("PRONO_POINTS_HOURRA","Points Hourra");

// Classements
define("PRONO_CLASSEMENT_GENERAL","Classement général");
define("PRONO_CLASSEMENT_PSEUDO","Pseudo");
define("PRONO_CLASSEMENT_POINTS","Points");
define("PRONO_CLASSEMENT_REUSSITE","Réussite");
define("PRONO_CLASSEMENT_FRACTIONS","Nb OK / joués");
define("PRONO_CLASSEMENT_PARTICIPATIONS","Nb pronos");
define("PRONO_CLASSEMENT_GENERAL_MAJ","Classement général");
define("PRONO_CLASSEMENT_MOIS","Classement du mois");
define("PRONO_CLASSEMENT_30","Classement des 30 derniers jours");
define("PRONO_CLASSEMENT_HEBDO","Classement hebdo");
define("PRONO_CLASSEMENT_DERNIERE_JOURNEE","Classement de la dernière journée");
define("PRONO_CLASSEMENT_MOYENNE","Classement sur la moyenne");
define("PRONO_CLASSEMENT_HOURRA","Hourra classement");
define("PRONO_CLASSEMENT_HOURRA_DERNIERE_JOURNEE","Classement de la dernière journée Hourra");
define("PRONO_CLASSEMENT_MIXTE","Classement Mixte");
define("PRONO_CLASSEMENT_COMPLET","Classement complet");
define("PRONO_CLASSEMENT_SUITE","La suite...");
define("PRONO_CLASSEMENT_NON_CLASSE","NC");
define("PRONO_CLASSEMENT_PREMIER","er");   // Pour 1er    1st
define("PRONO_CLASSEMENT_SECOND","nd");   // Pour 2nd     2nd
define("PRONO_CLASSEMENT_TROIS","e");   // Pour 3e       3rd
define("PRONO_CLASSEMENT_AUTRES","e");   // Pour xe       xth

// Inscription
define("PRONO_INSCRIPTION_TITRE","Inscription");
define("PRONO_INSCRIPTION_MDP","Saisissez à nouveau votre mot de passe");
define("PRONO_INSCRIPTION_PSEUDO_UTILISE","Ce pseudo est déjà utilisé !");
define("PRONO_INSCRIPTION_PSEUDO_TAILLE","Votre pseudo doit contenir entre 4 et 20 caractères !");
define("PRONO_INSCRIPTION_MAIL_UTILISE","Cette adresse email est déja utilisée !");
define("PRONO_INSCRIPTION_MAIL_VIDE","Le champ adresse email ne peut être vide !");
define("PRONO_INSCRIPTION_MAIL_INVALIDE_1","L'adresse email");
define("PRONO_INSCRIPTION_MAIL_INVALIDE_2","n'est pas valide");
define("PRONO_INSCRIPTION_JS_PSEUDO","Veuillez indiquer votre pseudo !");
define("PRONO_INSCRIPTION_JS_MAIL","Veuillez indiquer votre adresse email !");
define("PRONO_INSCRIPTION_JS_MAILVALID","Entrez une adresse e-mail valide !");
define("PRONO_INSCRIPTION_JS_MDP","Veuillez indiquer deux fois votre mot de passe !");
define("PRONO_INSCRIPTION_JS_DIFF","ATTENTION, vos mots de passe sont différents !");
define("PRONO_INSCRIPTION_JS_NOM","Veuillez indiquer votre nom !");
define("PRONO_INSCRIPTION_JS_PRENOM","Veuillez indiquer votre prenom !");
define("PRONO_INSCRIPTION_JS_ADRESSE","Veuillez indiquer votre adresse !");
define("PRONO_INSCRIPTION_JS_POSTAL","Veuillez indiquer votre code postal !");
define("PRONO_INSCRIPTION_JS_VILLE","Veuillez indiquer votre ville !");
define("PRONO_INSCRIPTION_JS_PAYS","Veuillez indiquer votre pays !");
define("PRONO_INSCRIPTION_JS_NAISS_JOUR","Veuillez indiquer votre jour de naissance !");
define("PRONO_INSCRIPTION_JS_NAISS_MOIS","Veuillez indiquer votre mois de naissance !");
define("PRONO_INSCRIPTION_JS_NAISS_ANNEE","Veuillez indiquer votre année de naissance !");
define("PRONO_INSCRIPTION_JS_PROF","Veuillez indiquer votre profession !");

// Profil
define("PRONO_PROFIL_SUR","Etes vous sûr de vouloir suprimer le compte de");
define("PRONO_PROFIL_SUPP","Compte supprimé !");
define("PRONO_PROFIL_ANCIEN_MDP","Ancien mot de passe non renseigné");
define("PRONO_PROFIL_MDP_2_FOIS","Vous devez entrer le nouveau mot de passe 2 fois");
define("PRONO_PROFIL_MDP_2_FOIS_2","Vous devez entrer l'ancien mot de passe 2 fois");
define("PRONO_PROFIL_MDP_DIFF","Nouveaux mots de passe différents");
define("PRONO_PROFIL_MDP_ERREUR","Ancien mot de passe erroné");
define("PRONO_PROFIL_SUPP_2","Supprimer mon compte");
define("PRONO_PROFIL_TITRE","Compte de");
define("PRONO_PROFIL_ANCIEN_MDP_2","Ancien mot de passe (à compléter si vous changer de mot de passe)");
define("PRONO_PROFIL_NOUVEAU_MDP","Nouveau mot de passe");
define("PRONO_PROFIL_NOUVEAU_MDP_2","Resaisissez votre nouveau mot de passe");

// prono.inc
define("PRONO_INC_INSCRIPTION","INSCRIPTION");
define("PRONO_INC_HEBDO","CLASSEMENT HEBDO");
define("PRONO_INC_MOIS","CLASSEMENT DU MOIS");
define("PRONO_INC_GENERAL","Général");
define("PRONO_INC_DERNIERE_JOURNEE","Dernière journée");
define("PRONO_INC_COMPLET","Complet");
define("PRONO_INC_MOYENNE","Moyenne");
define("PRONO_INC_MIXTE","Mixte");
define("PRONO_INC_HOURRA","Hourra !");

// DECONNEXION
define("DECONNEXION","Déconnexion en cours...");

//Résultats
define("PRONO_RESULTATS_MOY","Moyenne");
define("PRONO_RESULTATS_REUSSITE","Réussite");
define("PRONO_RESULTATS_PROGR","Progression");
define("PRONO_RESULTATS_POINTS","Points");

// Bareme
define("PRONO_BAREME_TITRE","Barèmes");
define("PRONO_BAREME_TPS","Vous pouvez validé la grille jusqu'à");
define("PRONO_BAREME_TPS2","heure(s) avant le début du match");

// Fiches joueurs   //0.82
define("FICHE_AGE","Age");
define("FICHE_DATE","Né le");
define("FICHE_DETAIL","Détail des buts");
define("FICHE_BUTS","but(s)");




?>
