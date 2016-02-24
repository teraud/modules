<?php

/**
 * Ajouter les modules sur les vues de rubriques
 *
 * @param 
 * @return 
**/
/*
function modules_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'rubrique'
	  AND $e['edition'] == false) {
		  
		$id_rubrique = $flux['args']['id_rubrique'];
		
		$bouton = '';
		if (autoriser('creermoduledans','rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T('module:icone_creer_module'), generer_url_ecrire('module_edit', "id_rubrique=$id_rubrique"), "module-24.png", "new", 'right')
					. "<br class='nettoyeur' />";
		}

		$lister_objets = charger_fonction('lister_objets','inc');	
		$flux['data'] .= $lister_objets('modules',array('titre'=>_T('module:titre_modules_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'nom'));
		$flux['data'] .= $bouton;
			
	}

	return $flux;
}
*/

function modules_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les modules
	if ($e['type'] == 'module' AND !$e['edition']) {
		$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			#'editable'=>autoriser('associerauteurs',$e['type'],$e['id_objet'])?'oui':'non'
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
}
?>
