<?php

/**
 * La classe History suit l'historique du déplacement de l'utilisateur
 * Il stocke les urls rencontrées
 */
class History {
    /**
     * CURRENT_PAGE est la page courante
     */
    const CURRENT_PAGE = 1;
    
    /**
     * PREVIOUS_PAGE est la page précédente
     */
    const PREVIOUS_PAGE = 2;
    
    /**
     * Ajoute une url dans l'historique
     * @param $url l'url à ajouter, de la forme
     *      $url['c'] = controller
     *      $url['a'] = action
     *      $url['params'] = tableau des paramètres supplémentaires (peut ne pas exister si vide)
     */
    public static function put($url) {
        // récupère les urls à partir de la variable de session
        $urls = Session::param('history');
        
        if(!is_array($urls)) {
            $urls = array();
        }
        
        // on enregistre jusqu'à un max, après, on supprime les plus anciennes
        $maxUrls = Configuration::maxHistoryUrls();
        if(count($urls)>=$maxUrls) {
            unset($urls[$maxUrls-1]);
        }
        
        // on ajoute si la page courante est différente de la page précédente
        // ex. on enregistre pas si rafraichissement de la page
        if($url != $urls[0]) {
            array_unshift($urls, $url);
        }
        
        // met à jour l'historique
        Session::_param('history', $urls);
    }
    
    /**
     * Récupère une url déjà enregistré
     * @param $step nombre de retour en arrière
     *              PREVIOUS_PAGE par défaut
     *              attention si la page courante n'a pas été enregistré, la page précédente sera 0 !
     * @return une url
     */
    public static function back($step = History::PREVIOUS_PAGE, $default = false) {
        $urls = Session::param('history');
        if(!$default) {
        	Configuration::domain();
        }
        
        // vérification aux bornes
        if($step<1) {
            $step = 1;
        } elseif($step>count($urls)) {
            $step = count($urls);
        }
        
        if(count($urls)==1) {
        	$url = $default;
    	} else {
        	$url = $urls[$step-1];
    	}
    	
        return $url;
    }
    
    /**
     * Supprime une url ou toutes
     * @param $num la position de l'url à supprimer, 1 étant la première (page courante)
     *              si -1 (par défaut), supprime toutes les urls
     */
    public static function delete($num = -1) {
        $urls = Session::param('history');
        $nb_urls = count($urls);
        
        if($num == -1) {
            $urls = array();
        } elseif($num>0 && $num<=$nb_urls) {
            // déplace toutes les urls à partir de celle qu'on supprime
            for($i = $num-1; $i < $nb_urls-1; $i++) {
                $urls[$i] = $urls[$i+1];
            }
            unset($urls[$nb_urls-1]);
        }
        
        // met à jour l'historique
        Session::_param('history', $urls);
    }
}
