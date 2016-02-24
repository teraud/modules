<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function modules_upgrade($nom_meta_base_version, $version_cible){

    $maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_modules')),
		array('maj_tables', array('spip_intervenants')),
	);
	
	$maj['1.1.0'] = array(
		array('maj_tables', array('spip_intervenants')),
	);
	$maj['1.2.0'] = array(
		array('sql_alter',  "TABLE spip_intervenants DROP en"),
		array('sql_alter',  "TABLE spip_intervenants DROP es"),
		array('sql_alter',  "TABLE spip_intervenants DROP pt"),
		array('sql_alter',  "TABLE spip_intervenants DROP ar"),
	);
	$maj['1.3.0'] = array(
		array('sql_alter',  "TABLE spip_modules DROP organisation_chef_de_file"),
		array('sql_alter',  "TABLE spip_modules DROP autres_organisations_france"),
		array('maj_tables', array('spip_modules')),
	);
	$maj['1.4.0'] = array(
		array('maj_tables', array('spip_intervenants')),
	);
	$maj['1.5.0'] = array(
		array('maj_tables', array('spip_modules')),
	);
	$maj['1.6.0'] = array(
		array('maj_tables', array('spip_modules')),
	);
	$maj['1.7.0'] = array(
		array('maj_tables', array('spip_modules')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function modules_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_modules");
	sql_drop_table("spip_intervenants");
	effacer_meta($nom_meta_base_version);
}

?>
