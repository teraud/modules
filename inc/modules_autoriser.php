<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/* pour que le pipeline ne rale pas ! */
function modules_autoriser(){}

/**
 * Autorisation à créer un module
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_module_creer_dist($faire,$type,$id,$qui,$opts){
    return (in_array($qui['statut'], array('0minirezo', '1comite', '6forum')) AND sql_countsel('spip_rubriques') > 0);
}

/**
 * Autorisation à modifier un module
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_module_modifier_dist($faire,$type,$id,$qui,$opts){
	/* on regarde si la personne connectee est un auteur lie a ce module */
	if (sql_getfetsel('id_auteur', 'spip_auteurs_liens', 'id_auteur='.intval($qui['id_auteur']).' AND id_objet='.intval($id).' AND objet="'.$type.'"')) {
		return true;
	}
	if ($qui['statut']=='0minirezo' AND !$qui['restreint'])
		return true;
	return false;
}

/**
 * Autorisation à supprimer un module
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_module_supprimer_dist($faire,$type,$id,$qui,$opts){
	return ($qui['statut']=='0minirezo' AND !$qui['restreint']);
}

/**
 * Autorisation à modifier un intervenant
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_intervenant_modifier_dist($faire,$type,$id,$qui,$opts){
	$intervenant = sql_fetsel('id_objet, objet', 'spip_intervenants', 'id_intervenant='.intval($id));
    if ($intervenant['id_objet'] == -$qui['id_auteur']) {
        return true;
    }

    return autoriser('modifier',$intervenant['objet'],$intervenant['id_objet'],$qui);
}

/**
 * Autorisation à supprimer un intervenant
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_intervenant_supprimer_dist($faire,$type,$id,$qui,$opts){
    return autoriser('modifier','intervenant',$id,$qui);
}

/**
 * Autorisation à créer un intervenant
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return bool
 */
function autoriser_intervenant_creer_dist($faire,$type,$id,$qui,$opts){
	if (!$opts['objet']) {
        return false;
    }
    return autoriser('creer',$opts['objet'],'',$qui);
}

?>
