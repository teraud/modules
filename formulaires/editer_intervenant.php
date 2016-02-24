<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_intervenant_charger_dist($id_intervenant, $id_objet, $objet, $retour){

	if (intval($id_intervenant)) {
		$valeurs = sql_fetsel('*', 'spip_intervenants', 'id_intervenant='.intval($id_intervenant));
		$valeurs['temps'] = explode(' - ', $valeurs['temps']);
	}
	else {
		$valeurs['id_intervenant'] = $id_intervenant;
		$valeurs['id_objet'] = $id_objet;
		$valeurs['objet'] = $objet;
		$valeurs['temps'] = '';
		$valeurs['nom'] = '';
		$valeurs['prenom'] = '';
		$valeurs['pays'] = '';
		$valeurs['organisation'] = '';
		$valeurs['langue'] = '';
		$valeurs['autre_langue'] = '';
		$valeurs['confirme'] = '';
	}

	return $valeurs;
}

function formulaires_editer_intervenant_verifier_dist($id_intervenant, $id_objet, $objet, $retour){

	$erreurs = array();

	return $erreurs;
}

function formulaires_editer_intervenant_traiter_dist($id_intervenant, $id_objet, $objet, $retour){

	$temps = implode(' - ', _request('temps'));

	if (!intval($id_objet)) {
		$id_objet = -$GLOBALS['visiteur_session']['id_auteur'];
	}

	if (intval($id_intervenant)) {
		sql_updateq('spip_intervenants', array(
			'id_objet' => $id_objet,
			'objet' => $objet,
			'temps' => $temps,
			'nom' => _request('nom'),
			'prenom' => _request('prenom'),
			'pays' => _request('pays'),
			'organisation' => _request('organisation'),
			'langue' => _request('langue'),
			'autre_langue' => _request('autre_langue'),
			'confirme' => _request('confirme')),
			'id_intervenant='.intval($id_intervenant));
		
	}
	else {
		sql_insertq('spip_intervenants', array(
			'id_objet' => $id_objet,
			'objet' => $objet,
			'temps' => $temps,
			'nom' => _request('nom'),
			'prenom' => _request('prenom'),
			'pays' => _request('pays'),
			'organisation' => _request('organisation'),
			'langue' => _request('langue'),
			'autre_langue' => _request('autre_langue'),
			'confirme' => _request('confirme')
		));
	}

	$autoclose = "<script type='text/javascript'>if (window.jQuery) jQuery.modalboxclose();</script>";

	$res['message_ok'] = _T('info_modification_enregistree').$autoclose;

	$res['message_ok'].='<script type="text/javascript">if (window.jQuery) ajaxReload("intervenants");</script>';

	return $res;

}


?>
