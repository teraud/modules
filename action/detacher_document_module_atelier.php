<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_detacher_document_module_atelier_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	list($id_objet, $objet, $id_document) = explode('-', $args);
	
	sql_delete('spip_documents_liens', 'id_document='.intval($id_document).' AND id_objet='.intval($id_objet).' AND objet='.sql_quote($objet));

	return true;
}

?>
