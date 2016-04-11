<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_module_charger_dist($id_module='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('module',$id_module,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

	if (!intval($valeurs['id_ue_edition'])) {
		$valeurs['id_ue_edition'] = sql_getfetsel('id_ue_edition', 'spip_ue_editions', 'statut="publie"', '', 'id_ue_edition desc', '0,1');
	}

	if (!_request('enregistrer')) {
		$valeurs['email_referent_confirmation'] = $valeurs['email_referent'];

		$autres_organisations_france = sql_allfetsel('id_organisation', 'spip_organisations_liens',
																'id_objet='.intval($id_module).' AND objet="module"');

		if (is_array($autres_organisations_france)) {
			foreach($autres_organisations_france as $id_organisation) {
				$valeurs['autres_organisations_france'][] = $id_organisation['id_organisation'];
			}
		}
		else {
			$valeurs['autres_organisations_france'] = '';
		}
	}

	if (!$valeurs['email_referent_confirmation']) $valeurs['email_referent_confirmation'] = '';
	if (!$valeurs['autres_organisations_france']) $valeurs['autres_organisations_france'] = '';

	include_spip('inc/securiser_action');
	$valeurs['cle_ajouter_document'] = calculer_cle_action('ajouter-document-module-' . $id_module);
	$valeurs['ajouter_document'] = isset($_FILES['ajouter_document']['name']) ? $_FILES['ajouter_document']['name'] : '';

	$valeurs['enregistrer'] = '';

	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_module_identifier_dist($id_module='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_module)));
}

/**
 * Une securite qui nous protege contre :
 * - les doubles validations de forums (derapages humains ou des brouteurs)
 * - les abus visant a mettre des forums malgre nous sur un article (??)
 * On installe un fichier temporaire dans _DIR_TMP (et pas _DIR_CACHE
 * afin de ne pas bugguer quand on vide le cache)
 * Le lock est leve au moment de l'insertion en base (inc-messforum)
 * Ce systeme n'est pas fonctionnel pour les forums sans previsu (notamment
 * si $afficher_previsu = 'non')
 *
 * http://doc.spip.org/@forum_fichier_tmp
 *
 * @param $arg
 * @return int
 */
function forum_fichier_tmp($arg){
# astuce : mt_rand pour autoriser les hits simultanes
	while (($alea = time()+@mt_rand())+intval($arg)
		AND @file_exists($f = _DIR_TMP . "forum_$alea.lck")){
	}
	;
	spip_touch($f);

# et maintenant on purge les locks de forums ouverts depuis > 4 h

	if ($dh = @opendir(_DIR_TMP)){
		while (($file = @readdir($dh))!==false){
			if (preg_match('/^forum_([0-9]+)\.lck$/', $file)
				AND (time()-@filemtime(_DIR_TMP . $file)>4*3600)
			)
				spip_unlink(_DIR_TMP . $file);
		}
	}
	return $alea;
}

/**
 * Lister les formats de documents joints acceptes dans les forum
 * @return array
 */
function forum_documents_acceptes(){
	$formats = 'pdf';
	if (!$formats) return array();
	if ($formats!=='*'){
		$formats = array_filter(preg_split(',[^a-zA-Z0-9/+_],', $formats));
	}
	else {
		include_spip('base/typedoc');
		$formats = array_keys($GLOBALS['tables_mime']);
	}
	sort($formats);
	return $formats;
}


function formulaires_editer_module_verifier_dist($id_module='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('module', $id_module, array('id_ue_edition', 'id_organisation', 'titre_court', 'presentation'));

	if(!_request('autres_organisations_france') && !_request('complement_autres_organisations')) {
		$erreurs['autres_organisations_france'] = _T('info_obligatoire');
	}

	if (!isset($GLOBALS['visiteur_session']['tmp_forum_document'])) {
		session_set('tmp_forum_document',
			sous_repertoire(_DIR_TMP, 'documents_forum') . md5(uniqid(rand())));
	}
	$tmp = $GLOBALS['visiteur_session']['tmp_forum_document'];
	$doc = &$_FILES['ajouter_document'];
	if (isset($_FILES['ajouter_document'])
		AND $_FILES['ajouter_document']['tmp_name']
	){
		// securite :
		// verifier si on possede la cle (ie on est autorise a poster)
		// (sinon tant pis) ; cf. charger.php pour la definition de la cle
		if (_request('cle_ajouter_document')!=calculer_cle_action($a = "ajouter-document-module-$id_module")){
			$erreurs['document_forum'] = _T('forum:documents_interdits_forum');
			unset($_FILES['ajouter_document']);
		}
		else {
			include_spip('inc/joindre_document');
			include_spip('action/ajouter_documents');
			list($extension, $doc['name']) = fixer_extension_document($doc);
			$acceptes = forum_documents_acceptes();

			if (!in_array($extension, $acceptes)){
				# normalement on n'arrive pas ici : pas d'upload si aucun format
				if (!$formats = join(', ', $acceptes)){
					$formats = '-'; //_L('aucun');
				}
				$erreurs['document_forum'] = _T('public:formats_acceptes', array('formats' => $formats));
			}
			else {
				include_spip('inc/getdocument');
				if (!deplacer_fichier_upload($doc['tmp_name'], $tmp . '.bin'))
					$erreurs['document_forum'] = _T('copie_document_impossible');

				#		else if (...)
				#		verifier le type_document autorise
				#		retailler eventuellement les photos
			}

			// si ok on stocke les meta donnees, sinon on efface
			if (isset($erreurs['document_forum'])){
				spip_unlink($tmp . '.bin');
				unset ($_FILES['ajouter_document']);
			}
			else {
				$doc['tmp_name'] = $tmp . '.bin';
				ecrire_fichier($tmp . '.txt', serialize($doc));
			}
		}
	} // restaurer le document uploade au tour precedent
	elseif (file_exists($tmp . '.bin')){
		if (_request('supprimer_document_ajoute')){
			spip_unlink($tmp . '.bin');
			spip_unlink($tmp . '.txt');
		}
		elseif (lire_fichier($tmp . '.txt', $meta)){
			$doc = @unserialize($meta);
		}
	}

	return $erreurs;
}

function formulaires_editer_module_traiter_dist($id_module='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$res = formulaires_editer_objet_traiter('module',$id_module,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

	sql_updateq('spip_modules',
		array('id_organisation' => _request('id_organisation'), 'id_ue_edition' => _request('id_ue_edition')),
		'id_module='.$res['id_module']);

	sql_delete('spip_organisations_liens', 'id_objet='.$res['id_module'].' AND objet="module"');

	foreach(_request('autres_organisations_france') as $id_organisation) {
		sql_insertq('spip_organisations_liens', array(
													'id_organisation' => $id_organisation,
													'id_objet' => $res['id_module'],
													'objet' => 'module'));
	}

	if (!intval($id_module)) {
		sql_updateq('spip_intervenants', array('id_objet' => $res['id_module']),
			'id_objet=-'.intval($GLOBALS['visiteur_session']['id_auteur']).' AND objet="module"');
	}

	// Ajouter un document
	if (isset($_FILES['ajouter_document'])
	AND $_FILES['ajouter_document']['tmp_name']) {
		$files[] = array('tmp_name'=>$_FILES['ajouter_document']['tmp_name'],'name'=>$_FILES['ajouter_document']['name']);
		$ajouter_documents = charger_fonction('ajouter_documents','action');
		$ajouter_documents(
			'new',
			$files,
			'module',
			$res['id_module'],
			'document');
		// supprimer le temporaire et ses meta donnees
		spip_unlink($_FILES['ajouter_document']['tmp_name']);
		spip_unlink(preg_replace(',\.bin$,',
			'.txt', $_FILES['ajouter_document']['tmp_name']));
	}

    /*
	if ($res['message_ok'] && !test_espace_prive()) {

		$res['message_ok'] = "Merci pour l’inscription de votre module. Dès sa validation par le CRID il apparaitra dans le tableau récapitulatif des modules, vous pourrez dès lors le compléter ou le modifier.";

		$envoyer_mail = charger_fonction('envoyer_mail','inc');

		$envoyer_mail('t.eraud@ritimo.org', "Mise à jour module", "Le module ".$res['id_module']." a été modifié.", "universite-si@crid.asso.fr", "X-Originating-IP: ".$GLOBALS['ip']);
	}
    */

	if (test_espace_prive()) {
		$res['redirect'] = generer_url_ecrire('module', 'id_module='.$res['id_module']);
	}

	return $res;
}


?>
