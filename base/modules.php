<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function modules_declarer_tables_objets_sql($tables){
	$tables['spip_modules'] = array(
	
		'principale' => "oui",
		'field'=> array(
			"id_module"	=> "bigint(21) NOT NULL",
			"id_ue_edition"	=> "bigint(21) NOT NULL DEFAULT 0",
			"id_rubrique"	=> "bigint(21) NOT NULL DEFAULT 0",
			"date" => "date DEFAULT '0000-00-00' NOT NULL",
			"nom_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"prenom_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"organisation_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"adresse_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"code_postal_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"ville_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"email_referent"	=> "tinytext DEFAULT '' NOT NULL",
			"telephone_referent"	=> "varchar(20) DEFAULT '' NOT NULL",

			"id_organisation"	=> "bigint(21) NOT NULL",
			"complement_autres_organisations"	=> "text DEFAULT '' NOT NULL",
			"partenaires_internationaux"	=> "text DEFAULT '' NOT NULL",
			
			"titre_court"  => "tinytext DEFAULT '' NOT NULL",
			"presentation"	=> "text DEFAULT '' NOT NULL",
			"presentation_generale"	=> "text DEFAULT '' NOT NULL",
			
			"theme_temps_1"  => "tinytext DEFAULT '' NOT NULL",
			"presentation_temps_1"	=> "text DEFAULT '' NOT NULL",

			"theme_temps_2"  => "tinytext DEFAULT '' NOT NULL",
			"presentation_temps_2"	=> "text DEFAULT '' NOT NULL",

			"theme_temps_3"  => "tinytext DEFAULT '' NOT NULL",
			"presentation_temps_3"	=> "text DEFAULT '' NOT NULL",
			
			"documents_complementaires"	=> "text DEFAULT '' NOT NULL",
			"nombre_places_valide"	=> "bigint(21) NOT NULL DEFAULT 0",
			"nombre_places_total"	=> "bigint(21) NOT NULL DEFAULT 0",
			"statut" => "varchar(255) DEFAULT '0' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_module",
			"KEY id_rubrique" => "id_rubrique",
		),
		'titre' => "titre_court AS titre",

		'champs_editables' => array(
			"nom_referent", "prenom_referent", "organisation_referent", "adresse_referent", "code_postal_referent", "ville_referent",
			"email_referent", "telephone_referent", "complement_autres_organisations", "partenaires_internationaux",
			"titre_court", "presentation", "presentation_generale",
			"theme_temps_1", "presentation_temps_1", "theme_temps_2", "presentation_temps_2", "theme_temps_3", "presentation_temps_3",
			"documents_complementaires"
		),
		
		'champs_contenu' => array(
			"nom_referent", "prenom_referent", "organisation_referent", "adresse_referent", "code_postal_referent", "ville_referent",
			"email_referent", "telephone_referent", "complement_autres_organisations", "partenaires_internationaux",
			"titre_court", "presentation", "presentation_generale",
			"theme_temps_1", "presentation_temps_1", "theme_temps_2", "presentation_temps_2", "theme_temps_3", "presentation_temps_3",
			"documents_complementaires"
		),
		
		'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'statut_textes_instituer' => 	array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'texte_changer_statut' => 'module:texte_changer_statut',
		'tables_jointures' => array()
		
	);

	$tables['spip_intervenants'] = array(

		'principale' => "oui",
		'field'=> array(
			"id_intervenant" => "bigint(21) NOT NULL",
			"id_objet"	=> "bigint(21) NOT NULL DEFAULT 0",
			"objet" => "varchar(25) DEFAULT '' NOT NULL",
			"temps" => "tinytext DEFAULT '' NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			"prenom"	=> "tinytext DEFAULT '' NOT NULL",
			"pays" => "tinytext DEFAULT '' NOT NULL",
			"organisation" => "tinytext DEFAULT '' NOT NULL",			
			"langue" => "varchar(25) DEFAULT '' NOT NULL",
			"autre_langue" => "tinytext DEFAULT '' NOT NULL",
			"confirme" => "tinytext DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_intervenant",
			"KEY id_objet" => "id_objet",
		),
		'titre' => "nom AS titre",

		'champs_editables' => array(
			"nom", "prenom", "en", "es", "pt", "ar", "autre_langue"
		),
	
		'champs_contenu' => array(
			"nom", "prenom", "en", "es", "pt", "ar", "autre_langue"
		)
		
	);
	
	$tables[]['tables_jointures'][]= 'modules_liens';
	$tables[]['tables_jointures'][]= 'modules';
	
	return $tables;
}

function modules_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['modules'] = 'modules';
	$interfaces['table_des_tables']['intervenants'] = 'intervenants';
	
	$interfaces['table_des_traitements']['PRESENTATION_GENERALE'][]= "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	$interfaces['table_des_traitements']['PRESENTATION_TEMPS_1'][]= "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	$interfaces['table_des_traitements']['PRESENTATION_TEMPS_2'][]= "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	$interfaces['table_des_traitements']['PRESENTATION_TEMPS_3'][]= "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	$interfaces['table_des_traitements']['DOCUMENTS_COMPLEMENTAIRES'][]= "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";

	return $interfaces;
}



?>
