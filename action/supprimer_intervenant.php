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

function action_supprimer_intervenant_dist($id_intervenant=0) {
	if (!$id_intervenant){
		$securiser_action = charger_fonction('securiser_action','inc');
		$id_intervenant = $securiser_action();
	}

    include_spip('inc/autoriser');
    if (autoriser('supprimer', 'intervenant', $id_intervenant)) {
	   sql_delete('spip_intervenants', 'id_intervenant='.intval($id_intervenant)); 
    }

	return true;
}

?>
