<?php

# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of µForum project: http://uforum.ecyseo.net
#
# @update     2013-11-22
# @copyright  2013 Cyril MAGUIRE and contributors (Special Thanks to Stephen Taylor http://stephentaylor.x10.mx)
# @copyright  2011-2013  Frédéric Kaplon and contributors
# @copyright   ~   2008  Okkin  Avetenebrae
# @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
# @licence    http://www.cecill.info/licences/Licence_CeCILL_V2.1-fr.txt CONTRAT DE LICENCE DE LOGICIEL LIBRE CeCILL version 2.1
# @link       http://uforum.ecsyeo.net   µForum
# @version    Release: @package_version@
#
# ------------------- END LICENSE BLOCK -------------------
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('DEBUG',0); // 0 = pas de débugage, 1 = débugage activé

# ---------------- CONFIGURATION ----------------------------
/**
* Configuration à modifier avant la première installation
*/
define('LANG','fr');
define('STYLE_COLOR','default');
define('PROJECT','µForum');
//A changer à l'installation pour plus de sécurité puis ne plus y toucher
define('SECURITY_SALT','DSKQJfmi879fdiznKSDJ56SD8734QRer980ZOIDQ');
//A changer pour plus de sécurité
//Pensez à changer également le nom du dossier s'il existe
define('CAPTCHA','captcha');
# ---------------- FIN CONFIGURATION ----------------------
# ---------------- DEFINITIONS ----------------------------
# Définition des constantes d'adressage
define('DS', DIRECTORY_SEPARATOR);
define('MU_ROOT', dirname(__FILE__).DS);
define('MU_BASE_URL',Tools::baseURL());
define('GOTO_INDEX','<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0; URL='.MU_BASE_URL.'"></head></html>');
/**
* Déclaration des répertoires
*/
define('MU_DATA', MU_ROOT.'data'.DS);
define('MU_THREAD', MU_DATA.'messages'.DS);
define('MU_MEMBER', MU_DATA.'membres'.DS);
define('MU_URL_MEMBER', MU_BASE_URL.'upload/');
define('MU_BACK', MU_ROOT.'backup'.DS);

define('MU_LANG', MU_ROOT.'lang'.DS);

define('MU_JS', MU_ROOT.'js'.DS);
define('MU_THEMES', MU_ROOT.'themes'.DS);
define('MU_URL_THEMES', str_replace(MU_ROOT,'',MU_THEMES));
define('MU_CSS', 'css'.DS);
define('MU_IMG', MU_ROOT.'img'.DS);
define('MU_UPLOAD', MU_ROOT.'upload'.DS);

/**
* Fixe les date en Français
*/
define('CHARSET','UTF-8');
define('REPLACE_FLAGS',ENT_QUOTES);
setlocale(LC_TIME, 'fr_FR.utf8','fra');
date_default_timezone_set('Europe/Paris');
/*
* Version de µForum
*/
define('VERSION','2.5');

$trademarkBlock = '
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of µForum project: http://uforum.ecyseo.net
#
# @update     '.date('Y-m-d').'
# @copyright  2013  Cyril MAGUIRE and contributors
# @copyright  2011-2013  Frédéric Kaplon and contributors
# @copyright   ~   2008  Okkin  Avetenebrae
# @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
# @licence    http://www.cecill.info/licences/Licence_CeCILL_V2.1-fr.txt CONTRAT DE LICENCE DE LOGICIEL LIBRE CeCILL version 2.1
# @link       http://uforum.ecsyeo.net   µForum
# @version    Release: @package_version@
#
# ------------------- END LICENSE BLOCK -------------------
';
define('TM',$trademarkBlock);
$LICENCEFONT = '/* ------------------FONTS------------------------------------ */'."\n";
$LICENCEFONT .= '/* Font Awesome'."\n";
$LICENCEFONT .= '#   Copyright (C) 2012 by Dave Gandy'."\n";
$LICENCEFONT .= '#   Author:    Dave Gandy'."\n";
$LICENCEFONT .= '#   License:   SIL ()'."\n";
$LICENCEFONT .= '#   Homepage:  http://fortawesome.github.com/Font-Awesome/'."\n";
$LICENCEFONT .= '*/'."\n";
define('LICENCEFONT',$LICENCEFONT);

# ---------------- TRADUCTION ----------------------
Tools::mklang();
Tools::loadlang('fr');
/**
* Vérification de la version de php
*/
if (version_compare(PHP_VERSION, '5.3', '<')) {
	Tools::loadlang('fr');
    exit(PHP_VERIF);
}

# ---------------- CLASSES STATIQUES-------------------
/**
* Outils
*/
class Tools {
	/**
	 * TRADUCTIONS
	 */
	public static function loadlang($lang) {
		$LANG=array();		
		if(!file_exists(MU_LANG.$lang.'.php')) {
			require MU_LANG.'fr.php';
		} else {
			require MU_LANG.$lang.'.php';
		}
		foreach($LANG as $key => $value) {
			if(!defined($key)) define($key,$value);
		}
	}
	/**
	*
	* NETTOIE LES NOMS D'UTILISATEURS
	*/
	public static function cleanUser($str,$charset='utf-8') {
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
			$str = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $str);
		    return $str;
	}
	/**
	 * Méthode qui convertit une chaine de caractères au format valide pour une url
	 *
	 * @param	str			chaine de caractères à formater
	 * @return	string		nom d'url valide
	 **/
	public static function title2url($str) {

		$str = strtolower(self::removeAccents($str,CHARSET));
		$str = preg_replace('/[^[:alnum:]]+/',' ',$str);
		return strtr(trim($str), ' ', '-');
	}
	/**
	 * Méthode qui retourne le nom du navigateur utilisé
	 */
	public static function getUserAgent() {
		$nav = array('Chrome','Trident','Opera','Firefox');
		foreach ($nav as $key => $value) {
			if (strpos($_SERVER['HTTP_USER_AGENT'], $value) !== false) {
				if ($value == 'Trident') $value = 'IE';
				return $value;
			}
		}
		return false; 
	}
	/**
	*
	* SUPPRIME LES CARACTERES SPÉCIAUX
	*/
	public static function removeAccents($str,$charset='utf-8') {
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
			$str = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $str);
		    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml|uro)\;#', '\1', $str);
		    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); # pour les ligatures e.g. '&oelig;'
		    $str = preg_replace('#\&[^;]+\;#', '', $str); # supprime les autres caractères
		    return $str;
	}
	public static function correctAccents($str,$charset='utf-8') {
		$str = preg_replace('#\&amp\;([A-za-z])(acute|cedil|circ|grave|ring|tilde|uml|uro)\;#', '&$1$2;', $str);
		$str = preg_replace('#\&amp\;([A-za-z]{2})(?:lig)\;#', '&$1$2;', $str); # pour les ligatures e.g. '&oelig;'
		$str = preg_replace('#\&amp\;([A-za-z]{5})\;#', '&$1$2;', $str); # pour les lettres comme µ
		return html_entity_decode($str,ENT_NOQUOTES,CHARSET);
	}
	public static function displayAccents($str) {
		$caracteres=array('é','è','ë','ê','à','â','ä','î','ï','ô','ö','û','ü','ù','É','È','Ë','Ê','À','Â','Ä','Î','Ï','Ô','Ö','Û','Ü','Ù');
		$entities=array('&eacute;', '&egrave;','&euml;','&ecirc;','&agrave;','&acirc;','&auml;','&icirc;', '&iuml;','&ocirc;', '&ouml;','&ucirc;', '&uuml;','&ugrave;','&Eacute;', '&Egrave;','&Euml;','&Ecirc;','&Agrave;','&Acirc;','&Auml;','&Icirc;', '&Iuml;','&Ocirc;', '&Ouml;','&Ucirc;', '&Uuml;','&Ugrave;');
		return str_replace($caracteres,$entities,$str);
	}
	/**
	 * Méthode qui convertit une chaine de caractères au format valide pour un nom de fichier
	 *
	 * @param	str			chaine de caractères à formater
	 * @return	string		nom de fichier valide
	 **/
	public static function title2filename($str) {

		$str = strtolower(self::removeAccents($str,CHARSET));
		$str = str_replace('|','',$str);
		$str = preg_replace('/\.{2,}/', '.', $str);
		$str = preg_replace('/[^[:alnum:]|.|_]+/',' ',$str);
		return strtr(ltrim(trim($str),'.'), ' ', '-');
	}
	/**
	*
	* RETOURNE L'URL de base
	*/
	public static function baseURL() {
		$dir = dirname($_SERVER['SCRIPT_NAME']);
		$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')?	'https://' : "http://";
		$servername = $_SERVER['HTTP_HOST'];
		$serverport = (preg_match('/:[0-9]+/', $servername) OR $_SERVER['SERVER_PORT'])=='80' ? '' : ':'.$_SERVER['SERVER_PORT'];
		$racine = rtrim($protocol.$servername.$serverport.$dir, '/').'/';
		return $racine;
	}
	/**
	 * Méthode qui retourne le type de compression disponible
	 *
	 * @return	stout
	 * @author	Stephane F., Amaury Graillat
	 **/
	public static function httpEncoding() {
		if(headers_sent()){
			$encoding = false;
		}elseif(strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false){
			$encoding = 'gzip';
		}else{
			$encoding = false;
		}
		return $encoding;
	}
	/**
	*
	* RETOURNE LES PARAMETRES DE L'URL
	*/
	public static function getURLParams() {
		$params = false;
		if (!empty($_GET)) {
			foreach ($_GET as $key => $value) {
				$params .= $key.'='.$value.'&';
			}
			$params = substr($params,0,-1);
		}
		if (!empty($_SERVER['QUERY_STRING']) && empty($_GET)) {
			$params = $_SERVER['QUERY_STRING'];
		}
	    return ($params == false ? '' : '?'.$params);
	} 
	/**
	 * Méthode permettant d'encoder une image en base64
	 * 
	 * @param $filename string le chemin vers le fichier image
	 * @param $filetype string l'extension de l'image
	 * @return string
	 * 
	 * @author luke at lukeoliff.com
	 */
	public static function base64_encode_image ($filename,$filetype) {
	    if (is_string($filename) ) {
	        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
	        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
	    }
	}
	
	static	$names = array('smile'=> SMILE,'wink'=> WINK,'laugh'=> LAUGH,'indifferent'=> INDIFFERENT,'sad'=> SAD,'wry'=> WRY,'tongue'=> TONGUE,'sorry'=> SORRY,'arrow'=> ARROW,'icon'=> ICON,'icon-big'=> PROJECT,'mail' => MAIL,'window' => WINDOW,'clip' => CLIP,'avatar'=> AVATAR);

	static	$img = array(
		'smile' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACt0lEQVQ4jW2TTWhUZxSGn3vnzkySiTNREwnmx8ZKEUUJWLPQ/kDpIiB0I7Sl7aLbcSEUSTbdl5KoSKHOxp2UUixKF0KgPynYVYq0lGQi6czciIkmM47j/OTOvff7vjkuguNEfeHA93Le7z1wfiwRoRP3fxkfEJE0MIkwIQAiCwJzCJmDZ/8rdeqtTgP35vEpO7ZrJjn8Pt39R+lKjQHgV122SotU78+jw/r0Wx8vzb5ikPv52NXUyHvpvjc+JBrvhZaPiNoWWVGwu1B+nXLhV56485kjn9871zZY+enoTGrk3al9hz8CXQH1GNE1aHnbZeweLCcJ0X5wdvNw8Rblwp+zx7/8f9rK/nB4KBJPrb156jyW2oBgnYU/5kA0J98ZB+Dvv/4Fy2Hig0mIDyHOINn5i4ReZdjW2qRTg+NYXgGpLSINl3x2hfxyAcImhE3yywXy2RWk4SK1RaxmgT1DJ9DKpB2tzJlETxetahZUBcS0myqhv2NCEjQgbCKhz67eAZQyZxytzZG4KSP+ox2fAUT5r+fKJx4Frc2Yo5Rp6sajWKS11RZ+euEaACb3/Ws5QEuXUMpEHK2M26yXxxOxVjuply/vqPwyB/D8IlqZnK2Vvv2k/BRRIaJCFv550H6/HJ25YrGCVua2rZTJPNhsYEIfdMDo6AHu3F1jY7NK4HkEnsfGZpU7d9cYSEVBB5jQZ/VhFa1NJnL9d6++9Ns3CaX06f6EhdP/Nt1dCe65ZbJuhfy6Ry2IMzY8wP6hUeywzNJqnVIlmD37rXezvcq3vu69OrInmj64v4/u5CB28hCW07vdfd2gVcvhVdbIrVdZLQaZTy4FL1b5OW5Md0/FIswc2OuwLxmhr8cGoLJlKNYMbknhh63pz66oV4/pOX78KjYokEZkUmCC7XteEGEOyHzxnd7o1D8DrtiWZZW1vjgAAAAASUVORK5CYII=',

		'wink' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACtUlEQVQ4jW2TT2hUVxTGf+/Nm3+ZOBPTRIL5Z2woQVECSQUtrZsuBMGNoqIuup0uCkWSjfsiiYoU6my6K6W0SoILQWlLBBdCihRKMoY4ZiKZaDLT8WUyyct77977rosx42j84MI5537fd/9wjqG1phEv7g62a63TwAk0RzSA1tMa7qPJ7D/9X6mRbzQa5CcOj5iRXWPJruPE2w4SS/UB4FbybJZmqLyYQvrV0c/Ozo7vMMjdOXQr1f1VumXf14SjzRC4aC1qJCMMZgzhVikv/Mnr/FTmwMW5b+sG878fHEt1fzmyZ+AUSBvE/2i5DoFTO8ZswrCSEG4DazcvZyYpLzwcP/zNs1Ej++tAZyiaKnx67DsMsQLeMlquQeCClm/vaYEZw7BaINqJtjrITl3Dd+wuS0qVbu0dxHAW0FsLINbeCd/in8czAHx+bBA8GyPu0No5RCH7IG1JoU4mmmIElSwIG7TiQzyfXwJgeKgf/C2077KruR0h1ElLSnUgqspo9xVoxR93HteFZ88cfc9IC7cWCJdoGKRUfZYQaktuvIqEgk0Azl/+uS5QuZ/eq23nAIEsIYQKWVKo/Fa1PJiIBADIpzd2POFjNcctIoXKmVLIe6/La2jho4XP9L9L9fjD1bhXLNpIoe6ZQqjM0uoGyndBevT09PLoSYGV1Qqe4+A5DiurFR49KdCeCoP0UL7L4ssKUqpM6Je/nersXz8khJBftCUMrLZh4rEEc/ky2bzN82WHdS9KX1c7ezt7MP0ys4tVSrY3fvqqM1Fv5ckrzbe6W8Pp/XtbiCc7MJP9GFZz7fflBsF6DscukFuusFj0Mueue+9aeRu3R+MjkRBjvZ9Y7EmGaGkyAbA3FcV1Rb4kcP1g9MJNsXOYtvHb95EODWm0PqHhCLV5ntaa+0Dm0o9ypZH/Bgvug8m/BV65AAAAAElFTkSuQmCC',

		'laugh' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACvklEQVQ4jXWTT2jTZxjHP7/klz+NSRM7WwtpXWupirJSqFQYVC8eBMXDHNvcPHjxEJGCh+bixYOn1MFwwx42cQeHykQRFAoKLR4EhaHUrnPWJiurtjZNY5Ka/Jr3ffN4iNYE8QPP4YHv8314Hp7HEhFqmb3Z2ywiMWAfQr8AiDwUGEUY2XxoIl2rt2oNUtd7hlzeUKKxbQ8NG3bgD3cC4ORSvElPkpsdQ5cL8S3f/D38kcHza1+cD7fvjkU69uLxBaHiIKKqIssDLj/KKZBJ3mE5NTay/Yenx9cMnl3dkQi3Dwy1bDsIOgtqCdF5qBSrbVwBLLsRPBvAXs/LyRtkkuPDPUen49bUH9uibl94ruvLQSy1AKsvuPzLOQC+O/YVAFd+vQ7A4ROD4IsiditTY2cpF7NtLq1NLNzai1VMIvlJZCX1YUPlUjXeISspJD+JVUrSFO1DKxOztTL71wX8VHJToLIghsPxCwBU/r9U7Vybl0tI2SEUbEYps9/W2mz3mQzizIMYAEzyN6zgVkQ5a3kdysHnAa1Np62UKemVee/4+KN6ERN8ilDQR99OG6WM29bKpEqFTG8wYNO95yKhzZ2fLPT4m1j6Z4KZ8SMUc4toZZ7bWunby5nXvS2RBqYf/4zrrxKB5i4wq+D21RkU0zMUi3k2RRpYXMyilbltjZ5pinpt11x/d5AlJ0IxNEDXrqN4wh31Y+f+Y+bB71hzV2nfGODek2WcVdNmiQi3TkcS0SZ7aOvAIAtP7/Lvs2kKhXz93KFGutrCbOo5wJN7F5h95Qx/nSjF1075xqng+Y7W9bHuvkP4m3vA7a9fgHFw0hNM3b9Ear4w8u2Pqx9O+T1/xhuGvG4Sn39m09LoJhJwAZB9Y1jMG1JphVOuxL//SX38TO+5fNLbKhBDZJ9AP9V/fijCKDBy5JxeqNW/BWFuUNI4hK7zAAAAAElFTkSuQmCC',

		'indifferent' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACgklEQVQ4jY2TSWiTURDHf1/yZWmtTe0ixbRo3SiKElx6EBcQhULBi6K4HLzGg+AhuXjx4ClVEA8WvHgSEZciKBQEK+KpiojGVGqTWHCpqWntkuZL3nvfeAjGaov4v8wb3n/+M2/ejCUiLMTYg0iLiESBboQuARAZEhhA6Ft7+M3EQr61UCB7f2vM41+eqG/bR03zZoKhDgCc6SyFiSTTY4Po8mx849F3vYsERu9uuRZq3xttWHMAX6AOXAcRVSFZPvAEUc4s+cxjJrODfZtOvj9TFRi5vTkRat8TW9l5CPQUqO+IngF3vpLGU4tl14OvGewVfEn2k8887d16+kPcSt3sDHsDoU/rdp3FUuNQ+szQkwEQzc7dEQBePH8Nlk3X/m4IhBG7ldTgJcrzU20erU001BrBms8gM0lkLks6NUJ6OAPlIpSLpIczpFMjyFwWmUliFTM0hrejlYnaWpmeZbVB3OkUqCkQU22qlJ0/fkhKc1AuImWH5XUtKGV6bK3NpoDJI87XavDx2A0ATPb6kj7KIeADrU2HrZQp6rmvfq9bqGbSI1f/yPy3D+DqCZQyXlsrky3O5iPL/O4i0r8w7+TQyozaWulHk/kfkdrmYPXy3uPRJYMOH1xfPedyBbQyj6yBi41hv+351LWhDq/HAsDTvK1im3ZUys2/rNjvryq9cIVnbydxSqbNEhEeXmhIhBvtWOeqIP+D5FiBsW9O75FEMV4d5f7zddfaG33RjhabGr9n6XeXXNLjJT7mSn3HLpd+j/Iv3InXxPxeEqubbFbWe2morQhNFQy5GUN2QuGU3fiJK2rxMv3CrXP+VoEoIt0CXVT2eUiEAaDv1FU9vpD/E0IIRZyy9OiQAAAAAElFTkSuQmCC',

		'sad' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACqUlEQVQ4jW2TPWyTVxiFn8++/osT2wQHQUNKA2oVgYoikBiQKAuVqFAZ2qr8FAlWM1TqYC9ZOjtFQgxY6tIyIEAgECoIKoZ06sCI0ghRiAkBHJw4jpPY/ux77/d2+BrXIT3be3XuOUfvjyMidGPm7uiAiGSAowgHBEDkscBDhMLOr5/Md/OdboHi7b3ZQLgvn9h+mFh6D9HkMABurUh9fpLazASmvZL75Nu/xjcIPL/16eXk0GeZ1EdHCEV6wXMR0T7JCUEginZXqEw/YrE4Udj93dPzHYFnN/bkk0OHsltGjoOpgl5AzDJ4Dd8m0IOjEhBKg9rE28k7VKb/GN977u+cM3V1ZDAYSb7edfB7HD0HrTeIWQLPBTH/5lQQiOKoFEQGEbWVqYmfaDeq25UxNtO/YxSnMY00p0Evcf3X3wA4efYLAK5feeDX576EVhUn1qB/cD+vp37PKKPtsXhPFK82BboKYjtNlba7bkLSWoV2E2m79PUOoLU9poyxuyO2grilzudT2V8AsMWfO7UT/xgzmfWVtEskBMbYYaW1bZrVUjjo1TtO5tmldc7v1wCemUdrG1RG22JzpTIaD3sbSBug4mB8o4Zbxmj7XBlt7i9WlkZ70lE/ncQoLVpevFpgZXkJgL5Eil0fptm8ySEeaANQLtcx2t5XWtvC7LvVsW0JCAYc3pQWmS032HfwK5LpIQBqC7M8+fM2SvcST8ewnvDybQ1jbMEREe79mMoP9qvsyAd+iuC2z7GlR+vSd79NztSZeeeOf5Nv5jqrfGes9/JQfygzPKCIhQP/24JGy+PFXIuX5VbhxIXWf6u8hpu5WDYcJL9js2JLIkiqxxeq1i3lZUtxXuO2vdzpi3rjMa3h2g/hrQIZRI4KHMC/58ciPAQKZy6ZuW7+P55nU0QMP9uoAAAAAElFTkSuQmCC',

		'wry' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC9UlEQVQ4jWWTX2ibZRTGf9+XL0n/JlmT/qFpM1uHlE3DLNoLhw5awV2JtjCtc2W3FVF70cDqrXbSIgwvlhsvFJExlQ0vBoOpU5rdVNbCWMfQ2nSs29rENKbpvubL+345XoTVzD5w4OWc530eDuccQ0Soxd0fD7eKyDhwDGFAAETmBa4gJHtHbmZr+UatQPpifNL0Nc8Euo5SHzlEXbAHgFIhzaPsLQp3r6HLxcQzx5dm9wgs//DcuWD3K+Ohp17F62+CSgkRVSUZXjDrUKUiuZWrbKavJQ+euPPersAfFw7NBLtfnmzrex10HtTfiN6Cil21MRswrAB4I2Dt48GtS+RWfp2Nn/ozYdz+ti/q8QfXfll5lq/OXyWTLfDS8xEQl6/PDPAwW2Lko+uAiWFYZDYdEu+/yZHoAmU732UkTrRfuL564LhtV92mpqYAmJiY4GjcAGCn7kVGR0dxSjaffHqGcDhMe3s7MfO371j4snexuPChPB0LSSqVkrGxMRkcHJTh4WHx+/3i9XollUrJ9PS0xONx6e/vl97ukBR+/0DmznYuWlq7B/1ujpOvhRkaGnpipKdPRgH25se68LtZtHZ7LKXcHb390Hf67Rbe6NcAuBVhX6iBaEczAKcGfbgVwfJUW+poa8bdyaKU67G0ctM7xdzhRl+Fvp7gE06inOqHFt+evF3KoJW7bGqlL2/m/kFUGVFl5hfv7b7/H7W1TCaPVu5lUyk3eW9jG7dcAu0Qi+1n7sYa6xsFHNvGsW3WNwrM3VijNegF7eCWS6w+KKC1m/R887NdXPppulEpfSTSaGBFXqC+rpE76Ry303n+um+z5fjp6WqlMxrDLOdYWi2SzTuzI5/ZF3dX+dLHTee6W7zjvZ0h6gMdmIEDGFZTtWe9TWVrGTu/xvL9AqsZJ/nW585/q/wY3yfqJ30eZvaHLdoCHkINJgD5Ry6ZLZd0VlEqVxLvnFV7j+kxzk/4OgTGETkmMED1nudFuAIk3/1Cr9fy/wUBjo0aF2abWAAAAABJRU5ErkJggg==',

		'tongue' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACx0lEQVQ4jX2TTWhcdRTFf+/Nm6/MJFPrxAZmSps2JiXBMiBGMH6guAikuKkIbV24nS4EF5m1CxFJupAuOtCNCxERpeKiEPAjKnVh3EjNTG3MzDR02qaZTj7m482b9/+/d7sYGtKmeHYXzj0H7rnHEBH2Yu2HzKCIZIFphEkBEFkSWEDIHzt9vbaXb+wVqFw5OWuG+ucG0m8QTU4QSQwD4OxUaNeW2VlbRLvN3Oh7hfl9AqvfvXApcfj17IGjbxMMx8F3EFE9khEEM4JymtTLP7JZWcyPn/v3/K7AyjcTc4nDr80+d+Id0FugHiC6Ab7dszH7MKwBCCbBeoa7y99TL/86f/KD/3JG8asTqUA4UT3+yocYah26d1j6ZQFE89KrGQD+uvY3GBaTb01DOIVYQxQXL+DaW2lTay+bGMpg2GWksYy0KpSKK5RulMHtgNuhdKNMqbiCtCpIYxmjU+Zg6kW08rKWVt5MrC+Cv1MEtQXi7R5VXOexhKTbAreDuA798UGU8mYsrb3xsFdHnHu7y2dmvwDAq1x+6oxyCAdBa2/YUsrr6Na9UMBv7zrplYuPOT85A/i6hlJewNLKq3Sa9Uws5O8j/R9sZwOtvFVLK311s76d6UtGAPiz2MJXDUbSB4hELAAcR7Na3cYMDvDyeByAjY02WnlXjYVPDqZCllmdfD5OwDTw+ye4Wb7P6h+3efRkxk6UkZlBxo4dwmwW8Hzh9382cbpeOvDlz3az8NOnMaX0VDJmYDjrJKNNNn8b4/jImxyKT+BXXTJTaxjOOvgehVtNalvd+dOf2VdMgFMfb+du3XfyhbU2tt1FdO+Fh0dTDI+mehFqRbvd5XqpQfmunX93rpODJ8r0bS46Gwowd+RZi1inn+q1MQDSUzdpRRtUagrH9XNnP1f7y/QIX38UGhLIIjItMEmvz0siLAD59y/q9b38hzrNcm0ko8GmAAAAAElFTkSuQmCC',

		'sorry' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACpklEQVQ4jX2TT2jTZxjHP7/kl6Q2NYm1LS211VYU8R9xwx7GVBg7FIRdNjbUHbzGg+AhuXjxnCoMDwa8eBKRjZWBQmFs3WCnCnWMLI5oknY606aLadKk+eX3vm8eD8UY1+H39sDn+X5fnvd5LBGhW8s/RAdFJAZMI0wJgMiCwBxCavLzP9a6eavboPD98bjHvzMZ2nOGHQNH6AlPAOBUCzTW0lSX59HuRuLgl3/ObDN49t2xW+Gx07HIvk/xBfqg7SCitiDLB54elLNBOf8jrwrzqcMX/rrUMcjeP5IMj52KDx36DHQF1L+IrkF7cyvG04tlh8A3APYuXqZnKed/mTl+8WnCytw9NOoNhF/s/+gyllqB1j8s/DwHojn5cRSAR7/9DpbN1CfTEBhF7GEy89dxNyt7PFqbWHg4irWZR2pppF4gl8mSe5IHtwluk9yTPLlMFqkXkFoaq5mnf/RDtDIxWytzNtjbQ7uaAVUBMZ2hiuu880PSqoPbRFyHnX2DKGXO2lqbwwFTRpxip/lc/A4ApnD7f2uUQ8AHWpsJWynT1PWi39tudJJ09uY7yf+tAdp6DaWM19bKFJob5WjQ3yb/d4XFdHEb3K0Pjo4wOb6LTaeEVuaZrZV++Kq8Hu0d6GExXcT6Nfxeg0WKTIwEKZUaaGUe2kqZ1PPV+tWR0BbQ128BMLk3QiR8EID1apb88joAG4BxHZZeVtHapCwR4cG1SHK0344HPMLjp7X3vuDEgRCNVpvlVWfmi2Qz0Vnl2at9t8b6fbGJQZvg8Ak8oQNYwfFOoyn+RL34mNxKi6VSK/XVjdbbVX6jbxM74n4vyb27bYZCXiK9HgAqDUOpZiisKRy3nTj/jdp+TG9074p/WCCGyLTAFFv3vCDCHJD6+qZe6eZfA2K8WtbNliWgAAAAAElFTkSuQmCC',

		'arrow' => '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',

		'icon' => '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',

		'icon-big' => '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',

		'mail' => 'R0lGODlhDgAKAJEAAGmGlqq2vuns7v///yH5BAAAAAAALAAAAAAOAAoAAAIkhI8Jw+03gJh0SjOErNouW23aJwQbEFBY6U3pyHLu+MqughsFADs=',

		'window' => 'R0lGODlhDgAMAKIEAKOxusLK0FF3i////93d3QAAAAAAAAAAACH5BAEAAAQALAAAAAAOAAwAAAMpCLqsRCDIScGLIuudMf2BgDWNGA5oqpqC6g7su2JyKgJtDT95LfCcYAIAOw==',

		'clip' => 'R0lGODlhDgAMAKIAAP39/bKztMXGx9bW1mRlaIWGiebm5p+foSH5BAAAAAAALAAAAAAOAAwAAANECLorV4apo9w4RIZgbCTDEgjXGAADqIzpSJBvuA2FKwQxYBTXIbw4koI2+AFzil3IMAiGGAUI4CeUADAE3tOqGwwi1gQAOw==',

		'avatar' => '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'
		);
	/**
	*
	* CRÉATION DES IMAGES
	*/
	public static function img($nr, $class='',$icon=false,$smile=false,$dim='') {
		
		return  ($icon === true) ? 'data:image/png;base64,'.self::$img[$nr] :'<img src="data:image/png;base64,'.self::$img[$nr].'" alt="'.self::$names[$nr].'"'.($class!=''?' class="' .$class. '"':'').''.($dim!=''?' width="' .$dim. '" height="' .$dim. '"':'').''.($smile!=''?' rel="'.$nr.'"':'').' />';
	}
	/**
	* CRÉATION DU FICHIER LANG
	*/
	public static function mklang() {
		$LANG = array(
			# Installation & Configuration
			'PHP_VERIF' => 'Vous devez disposer d\'un serveur équipé de PHP 5.3 ou plus !',
			'THEME' => 'Thème',
			'LANGUAGE' => 'Langue',
			'PLUGIN' => 'Plugin',
			'CONFIG_GZIP' => 'Activer la compression GZIP',
			'CONFIG' => 'Configuration',
			'REDIRECT' => 'Redirection vers',
			'POWEREDBY' => 'Propulsé par <a id=\"bottom\" name=\"bottom\" href=\"http://uforum.ecyseo.net\" rel=\"tooltip\" title=\"Forum sans Sql\">µForum</a>',
			'DEFAULT_SUB'=>'Le Forum sans BDD',
			'BBCODE_WELCOM_TXT' => "[b][i]Bienvenue sur µforum[/i][/b]

			Ce forum multithread est basé sur des fichiers uniquement (pas de base de données sql).
			
			[u][b]Il intègre plusieurs fonctionnalités :[/b][/u] [i](★ = Nouveauté)[/i]

			[c]✔ Gestion des membres par login / mot de passe (par cookies).
			✔ 4 niveaux d'utilisateurs : Administrateur, Modérateur, Membre, Anonyme.
			✔ Mode privé / public, pour autoriser les non-membres.
			✔ Liste des membres.
			✔ Profil utilisateur (+ édition).
			✔ Messagerie privée entre les membres.
			✔ Upload d'avatar et de pièces jointes (avec filtre d'extensions).
			✔ Smileys et BBCodes (ajout automatique des balises fermantes manquantes).
			★ Coupure des chaines trop longues sans couper les phrases !
			✔ Skins.
			✔ Liens automatiques.
			★ Html5 et css3 (Bootstrap de twitter).
			✔ Affichage des connectés.
			✔ Coloration syntaxique du code.
			✔ Gestion des options d'administration.
			✔ Système simple de sauvegarde et restauration. (revu)
			★ Captcha lors de l'inscription.
			★ Protection des mails, sur la liste des membres, pour contrer le spam.   
			★ Indicateur de message (Status Icône).  
			★ Date de naissance + Âge affiché si celle-ci renseignée.
			★ Date picker (Inscription et édition du profil). 
			★ Méta description pour le SEO.[/c]",
			'WELCOME_TXT' => "<b><i>Bienvenue sur µforum</i></b> <br /> <br />Ce forum multithread est basé sur des fichiers uniquement (pas de base de données sql).  <br /><ins><b>Il intègre plusieurs fonctionnalités :</b></ins> <i>(★ = Nouveauté)</i> <br /> <br /><pre>✔ Gestion des membres par login / mot de passe (par cookies). <br />✔ 4 niveaux d'utilisateurs : Administrateur, Modérateur, Membre, Anonyme. <br />✔ Mode privé / public, pour autoriser les non-membres. <br />✔ Liste des membres. <br />✔ Profil utilisateur (+ édition). <br />✔ Messagerie privée entre les membres. <br />✔ Upload d'avatar et de pièces jointes (avec filtre d'extensions). <br />✔ Smileys et BBCodes (ajout automatique des balises fermantes manquantes). <br />★ Coupure des chaines trop longues sans couper les phrases ! <br />✔ Skins. <br />✔ Liens automatiques. <br />★ Html5 et css3 (Bootstrap de twitter). <br />✔ Affichage des connectés. <br />✔ Coloration syntaxique du code. <br />✔ Gestion des options d'administration. <br />✔ Système simple de sauvegarde et restauration. (revu) <br />★ Captcha lors de l'inscription. <br />★ Protection des mails, sur la liste des membres, pour contrer le spam.    <br />★ Indicateur de message (Status Icône).   <br />★ Date de naissance + Âge affiché si celle-ci renseignée. <br />★ Date picker (Inscription et édition du profil).  <br />★ Méta description pour le SEO.<br />&nbsp;</pre>&nbsp;</div>",
			'FORUM_RULES' => 'Règles du forum',
			'FORUM_RULES_TXT' => '[b][u]Conditions d\'utilisation des forums[/u][/b]

[b]Propos racistes[/b] :
Les propos racistes ne sont pas tolérés sur le forum, sous aucune forme.

[b]Propos agressifs, vulgaires & injurieux[/b] :
Merci d\'utiliser un langage correct et respectable sur le forum, tout abus sera sanctionné.

[b]Propos diffamatoires[/b] :
Les propos diffamatoires ne sont pas tolérés sur le forum.

[b]Piratage & Hacking[/b] :
Il est interdit d\'inciter au piratage et au hacking sur le forum, par quelque biais que ce soit.

[b]Charme & Pornographie[/b] :
La pornographie n\'est pas tolérée sur le forum. Des textes ou images évoquant l\'érotisme ou la nudité sont acceptés uniquement sur les topics clairement identifiés comme pouvant héberger ce type de contenu, et créés par des administrateurs du forum.

[b]Freepost[/b] :
Le freepost (« parler pour ne rien dire ») est toléré, mais merci de ne pas en abuser de manière systématique.

[b]Signature dans les messages[/b] :
Les signatures affichées dans vos messages sont tolérées, à partir du moment où elles ne vont pas à l\'encontre de la bonne lisibilité du forum. Si un modérateur vous demande de la modifier ou de la réduire, merci de le faire. Les signatures répondent aux mêmes règles que les messages en terme de modération.

[b]Discussions politiques[/b] :
Les discussions tournant autour de la politique sont acceptées, à partir du moment où vous faites preuve d\'ouverture d\'esprit, que vous n\'entravez pas les débats, et que vous ne faites pas de propagande systématique.

[b]Discussions sur les religions[/b] :
Les discussions axées sur la religion sont tolérées à partir du moment où elles sont posées, et respectueuses de toutes les opinions et de tous les cultes. Le prosélytisme n\'est en aucun cas toléré sur le forum.

[b]Langage SMS & Majuscule[/b]s :
[u]Le langage SMS est à proscrire[/u]. Merci d\'utiliser un français correct et de ne pas abuser d\'abréviations. Merci aussi de faire autant que possible attention à l\'orthographe, à la grammaire et à la ponctuation.
Inversement, merci aux intégristes de la langue française de ne pas agresser les internautes ayant une orthographe bancale, et de les aider cordialement. Merci de ne pas rédiger vos titres et messages en majuscules.

[b]Multi-Comptes[/b] :
L\'utilisation de plusieurs comptes est à éviter, merci donc de n\'utiliser qu\'un seul compte.

[b]Publicité[/b] :
La publicité n\'est pas acceptée sur les forums, que ce soit pour des sites commerciaux ou personnels. Après un certain temps passé sur les forums, vous pouvez évoquer vos blogs ou sites persos, si ses évocations restent dans le sujet des topics auxquels vous participez. Si un topic dédié à la publicité existe, vous pouvez y faire la votre.
Les sites qui proposent des ventes ou quoi que ce soit en rapport avec le système pyramidal ne sont en aucun cas tolérés sur les forums. Merci donc de ne pas les relayer.',
			'INFORMATION' => 'Information',
			'PARAMS' => 'Paramètres',
			'GENERAL_PARAM' => 'Paramètres Généraux',
			'SAVE_BACKUP' => 'Créer une sauvegarde',
			'SAVE' => 'Sauvegarde',
			'RESTORE_FROM_BACKUP' => 'Restaurer depuis une sauvegarde',
			'RESTORE' => 'Restauration',
			'ADMIN' => 'Admin',
			'ARCHIVE_REC' => 'Archive créée avec succès !',
			'DOWNLOAD_ARCHIVE' => 'Télécharger l\'archive',
			'RESTAURATION_FORUM' => 'Restauration du forum',
			'UPLOAD_BACKUP' => 'Envoyer une sauvegarde à restaurer',
			'CONFIG_OPTIONS' => 'Options de configuration',
			'TITLE_LOGO' => 'Titre du forum / Logo',
			'SUBTITLE' => 'Sous Titre du forum',
			'NAME_AND_URL' => 'Nom &amp; Url du site',
			'META_DESCRIPTION' => 'Méta-description',
			'INDEX_MAX_MSG' => 'Max. messages en index',
			'TOPIC_MAX_MSG' => 'Max. messages par topic',
			'MEM_MAX_DISP' => 'Max. membres affichés',
			'LANG' => 'Langue',
			'MAX_AVATAR_WEIGHT' => 'Poids max. d\'un avatar',
			'ALLOWED_EXT' => 'Extensions autorisées',
			'PRIVATE_MODE' => 'Forum mode privé',
			'SHOW_SIGNATURES' => 'Afficher les signatures',
			'WELCOME_MSG' => 'Message d\'accueil',
			'REC' => 'Enregistrer',
			'NO_RESULT' => 'Aucun résultat',
			'DID_YOU_MEAN' => 'Vous vouliez dire : ',
			'RESULT_FOR' => 'Résultat pour : ',
			'CHANGE_SECURITY_SALT' => 'Vous devriez modifier la chaîne de sécurité (SECURITY_SALT) du fichier index.php',
			'CHANGE_CAPTCHA_DIR_NAME' => 'Vous devriez modifier le nom du dossier captcha et le nom de la constante CAPTCHA du fichier index.php',
			'L_YES' => 'oui',
			'L_NO' => 'non',
			'THREAD' => 'Fil de discussion',
			'THREADS' => 'Fils de discussion',
			'L_THREADS' => 'Discussions',
			 
			# Comptes
			'JOIN_COMMUNITY' => 'Rejoindre notre communauté',
			'REGISTER' => 'Créer un compte',
			'NAME' => 'Nom',
			'PASSWORD' => 'Mot de Passe',
			'USER_LOGIN' => 'Nom d\'utilisateur',
			'USER' => 'Identifiant',
			'BIRTHDAY' => 'Date d\'Anniversaire',
			'BORN_ON' => 'Né le',
			'BIRTH' => 'Naissance',
			'YEARS_OLD' => 'ans',
			'CONNECT' => 'S\'identifier',
			'EMAIL' => 'Adresse Mail',
			'EMAIL_URL' => 'Mail / Site Web',
			'WEBSITE' => 'Site Web',
			'SIGNATURE' => 'Signature',
			'SIGNATURE_MSG' => 'Aucune mise en forme possible et limitée à 150 caractères',
			'AVATAR' => 'Avatar',
			'CHECKING_CODE' => 'Code de vérification',
			'SIGN_UP' => 'S\'inscrire',
			'MENDATORY_FIELDS' => 'Les champs indiqués en vert sont obligatoires.',
			'CHAR_NOT_ALLOWED' => 'Si l\'identifiant comporte les caractères suivant:',
			'CHAR_NOT_ALLOWED_BIS' => 'ou des espaces, ils seront automatiquement retirés.',
			'EDIT_PROFIL' => 'Édition du profil',
			'EDIT_MY_PROFIL' => 'Modifier mon profil',
			'SAVE_PROFIL' => 'Sauvegarder mon profil',
			'PROFIL' => 'Profil',
			'PROFIL_OF' => 'Profil de',
			'REGISTRED_ON' => 'Inscrit(e) le',
			'SIGNATURE' => 'Signature',
			'DISPLAY_PROFIL' => 'Afficher le profil',
			'SEND_AN_EMAIL' => 'Envoyer un mail',
			'ACTIVE_JAVASCRIPT_TO_SEE_EMAIL' => 'Activer JavaScript pour afficher le mail',
			'SIGNATURE_OF' => 'Signature de',
			'MINI_PROFIL_OF' => 'Mini profil de',
			'MY_PERSONAL_FILES' => 'Mes Fichiers personnels',
			'PERSONAL_FILES' => 'Fichiers personnels',
			'FILE' => 'fichier',
			'PRIVATE_INBOX' => 'Messagerie Privée',
			'PRIVATE_MSG' => 'Message privé',
			'EMPTY_MAILBOX' => 'Vider votre boite',

			# Threads
			'ADD_MAIN_CAT' => 'Ajouter une catégorie principale',
			'MAIN_CAT_TITLE' => 'Titre de la catégorie principale',
			'ADD_CAT' => 'Ajouter une sous-catégorie',
			'CAT_TITLE' => 'Titre de la catégorie',
			'CAT_SUBTITLE' => 'Soustitre de la catégorie',
			'EDIT_MAIN_CAT' => 'Modifier les catégories principales',
			'EDIT_CAT' => 'Modifier les catégories',
			'NO_CAT' => 'Aucune catégorie',
			'MAIN_CAT_POSITION' => 'Position de la catégorie principale',
			'CAT_POSITION' => 'Position de la catégorie',
			'ADD_FORUM' => 'Ajouter un forum',
			'FORUM_TITLE' => 'Titre du forum',
			'FORUM_POSITION' => 'Position du forum',
			'DISPLAY_FORUM' => 'Voir le forum',
			   
			# Topics
			'POST' => 'Article',
			'REPLY' => 'Commentaire',
			'ADD' => 'Ajouter',
			'EDIT' => 'Modifier',
			'EDIT_TITLE' => 'Modifier le titre',
			'DEL' => 'Supprimer',
			'TITLE' => 'Titre',
			'CONTENT' => 'Message',
			'MORE' => 'En lire plus...',
			'TRIP' => 'Laisser vide si Anonyme',
			'NONE' => 'Aucune donnée actuellement',
			'L_NONE' => 'aucun',
			'UNCATEGORIZED' => 'Non classé',
			'REPLIED' => 'répondus à',
			'LIST_OF_ALL_TOPICS' => 'Lister toutes les discussions',
			'TITLE_SUBJECT' => 'Titre du sujet',
			'DISPLAY_TOPIC' => 'Afficher le sujet',
			'STARTED_ON' => 'Débuté le',
			'BY' => 'Par',
			'L_ON' => 'Le',
			'GOTO_LAST_MSG' => 'Aller au dernier message',
			'DEL_MSG' => 'Supprimer le sujet ?',
			'DEL_MSG_COMPLEMENT' => 'Cela supprimera également les forum associés',
			'FOUNDER' => 'Fondateur',
			'MODERATOR' => 'Modérateur',
			'ANSWER_FROM' => 'La réponse postée par',
			'WHOLE_TOPIC' => 'Tout le sujet',
			'QUOTE_MSG_FROM' => 'Citer le message de',
			'DOWNLOAD' => 'Télécharger',
			'TOPIC_UNKNONW' => 'Sujet inexistant',
			'BLOCKQUOTE' => 'Citation',
			'EDIT_BY' => 'Modifié par',
			'MODO' => 'Modo',
			'DEL_MEMBER' => 'Supprimer le membre',
			'DEL_THIS_USER' => 'Supprimer cet utilisateur',
			'DEL_THIS_FILE' => 'Supprimer ce fichier',
			'NEW_TOPIC' => 'Nouveau sujet',
			'ANSWER' => 'Répondre',
			'CHANGE' => 'Edition',
			'TO' => 'à',
			'PINNED' => 'Épinglé',
			'USER_MENDATORY' => 'Utilisateur (obligatoire)',
			'ATTACH_FILE' => 'Joindre un fichier',
			'SEND' => 'Envoyer',
			'WE_HAVE' => 'Nous avons',
			'IN' => 'dans',
			'OF' => 'de',
			'TOPIC' => 'Sujet',
			'WELCOME_TO' => 'Bienvenue à notre nouveau membre',
			'TOTAL_MB' => 'Total Membre',
			'WHO_IS_ONLINE' => 'Qui est en ligne ?',
			'MB_ONLINE' => 'Membres actuellement connectés',
			'GUESTS' => 'Visiteurs',
			'GUEST' => 'Visiteur',
			'LEGEND' => 'Légende',
			'NO_UNREAD_MSG' => 'Ne contient pas de message non lu',
			'UNREAD_MSG' => 'Contient des messages non lus',
			'ATTACHMENT' => 'Pièce jointe',
			'WROTE' => ' a écrit :',
			'NO_REPLY' => 'Aucune réponse',
			'REPLY' => 'Réponse',
			'REPLIES' => 'Réponses',
			 
			# BBCODE
			'FORMATING' => 'Formatage',
			'BOLD' => 'Gras',
			'ITALIC' => 'Italique',
			'UNDERLINE'=> 'Souligné',
			'STROKE_THROUGH' => 'Barré',
			'QUOTE' => 'Citation',
			'CODE' => 'Code',
			'LINK' => 'Insérer un lien',
			'PICTURE' => 'Insérer une image',
			'VIDEO' => 'Insérer une vidéo',
			'SMILEYS' => 'Smileys',
			'SMILE' => 'Sourire',
			'WINK' => 'Clin d\'oeil',
			'ICON' => 'Icône',
			'LAUGH' => 'Rire',
			'INDIFFERENT' => 'Indifférent',
			'SAD' => 'Triste',
			'WRY' => 'Ironique',
			'TONGUE' => 'Tire la langue',
			'SORRY' => 'Désolé',
			'ARROW' => 'Tu sors',
			'MAIL' => 'mail',
			'CLIP' => 'clip',
			'WINDOW' => 'Fenêtre',
			'AUTHOR' => 'Auteur',
			'TXT_REPLACEMENT' => 'Texte de remplacement',
			'URL' => 'URL',
			'SRC' => 'Source',
			'LEVEL' => 'Quel est le niveau du titre (de 1 à 4) ?',
			'WHO' => 'Quel est l\\\'auteur de la citation ?',
			'LINK_ADDRESS' => 'Quelle est l\\\'url du lien ?',
			'LINK_PICTURE' => 'Quelle est l\\\'url  de l\\\'image ?',
			'LINK_VIDEO' => 'Quelle est l\\\'url de la vidéo (de la forme //youtu.be/xxxxx) ?',

			# Navigation
			'LOGOUT' => 'Déconnexion',
			'LOGIN' => 'Connexion',
			'VIEW' => 'Affichage',
			'SEARCH' => 'Recherche',
			'LINK' => 'Lien',
			'CATEGORY' => 'Catégorie',
			'ARCHIVE' => 'Archive',
			'URL' => 'URL',
			'FEED' => 'Fil rss',
			'WELCOME' => 'Bienvenue',
			'ARCHIVES' => 'Archives',
			'QUIT' => 'Quitter la session',
			'LIST_OF_MEMBERS' => 'Liste des membres',
			'MEMBERS' => 'Membres',
			'MEMBER' => 'Membre',
			'CLOSE' => 'Fermer',
			'HOME_FORUM' => 'Accueil du Forum',
			'HOME' => 'Accueil',
			'FORUMS' => 'Forums',
			'STATISTICS' => 'Statistiques',
			'TOP' => 'Haut de page',	
			'JUMP_TO'=>'Atteindre',
			'GO'=>'Aller',

			#Pagination
			'L_PAGINATION_FIRST_TITLE' => 'Aller à la première page',
			'L_PAGINATION_FIRST' => '«',
			'L_PAGINATION_PREVIOUS_TITLE' => 'Page précédente',
			'L_PAGINATION_PREVIOUS'=> 'précédente',
			'L_PAGINATION_NEXT_TITLE'=> 'page suivante',
			'L_PAGINATION_NEXT' => 'suivante',
			'L_PAGINATION_LAST_TITLE' => 'Aller à la dernière page',
			'L_PAGINATION_LAST'=> '»',
			'L_PAGINATION' => 'page %s sur %s',
			 
			# Messages
			'MESSAGES' => 'Messages',
			'MESSAGE' => 'Message',
			'LAST_MSG' => 'Dernier message',
			'CONFIRM' => 'Ok',
			'YES' => 'Oui',
			'NO' => 'Non',
			'LOCKED' => 'Fermé',
			'ERRLEN' => 'est trop long ou trop court',
			'ERRBOT' => 'CAPTCHA incorrect',
			'NOTFOUND' => 'Oops! Cette page n\'existe plus :(',
			'ERRNOTMATCH' => 'Les mots de passe ne correspondent pas',
			'SEND_PRIVATE_MSG' => 'Envoyer un message privé',
			'NEW_PRIVATE_MSG' => 'Nouveau Message Privé',
			'BECAREFUL' => 'Attention : l\'identifiant ',
			'CASE_SENSITIVE' => 'est sensible à la casse !',
			'JS_UNAVAILABLE' => 'Javascript désactivé détecté',
			'JS_UNAVAILABLE_MSG' => 'Vous avez actuellement désactivé le javascript. Plusieurs fonctionnalités peuvent ne pas marcher. Veuillez réactiver le javascript pour accéder à toutes les fonctionnalités.',
			'PRIVATE_MSG_DEL' => 'La messagerie a bien été vidée.',
			'MSG_PRIVATE_REC' => 'Le message a bien été envoyé',
			'MSG_IF_NOT_SPAMMER' => 'Si vous n\'êtes pas un robot',
			'CLICK_HERE' => 'cliquez ici',

			# Confirmations
			'MKTHEME' => 'Création du répertoire de thèmes',
			'MKCSS' => 'Création du répertoire css',
			'MKLANG' => 'Création du répertoire lang',
			'MKBAK' => 'Création du répertoire backup',
			'MKUPL' => 'Création du répertoire upload',
			'MKDATA' => 'Création du répertoire data',
			'MKBAN' => 'Création du répertoire data/ban',
			'MKMBR' => 'Création du répertoire membres',
			'MKMSG' => 'Création du répertoire messages',
			'MKJS' => 'Création du répertoire js',
			'MKIMG' => 'Installation des images réussie',
			'BACKUP_DONE' => 'Votre sauvegarde a été envoyée et décompressée. La restauration est terminée',
			'MSG_DATA_REC' => 'Données enregistrées avec succès',
			'MSG_DATA_DEL' => 'Données supprimées avec succès',
			'MSG_FILE_DEL' => 'Le fichier a été supprimé',
			'MSG_COME_BACK_IN' => 'Revenez nous voir dans',
			'MSG_MIN_OR_NOT' => 'minutes ou pas...',

			# Erreurs
			'ERROR' => 'Erreur',
			'ERROR_MKTHEME' => 'Echec à la création du répertoire de thèmes',
			'ERROR_MKCSS' => 'Echec à la création du répertoire css',
			'ERROR_MKLANG' => 'Echec à la création du répertoire lang',
			'ERROR_MKBAK' => 'Echec à la création du répertoire backup',
			'ERROR_MKUPL' => 'Echec à la création du répertoire upload',
			'ERROR_MKDATA' => 'Echec à la création du répertoire data',
			'ERROR_MKBAN' => 'Echec à la création du répertoire data/ban',
			'ERROR_MKMBR' => 'Echec à la création du répertoire membres',
			'ERROR_MKMSG' => 'Echec à la création du répertoire messages',
			'ERROR_MKJS' => 'Echec à la création du répertoire js',
			'ERROR_MKIMG' => 'Echec à l\'installation des images',
			'ERROR_AVATAR_CREATION' => 'Erreur d\'écriture de l\'avatar !',
			'ERROR_EMPTY_AVATAR' => 'Vous n\'avez pas chargé d\'avatar !',
			'ERROR_ATTACHMENT_NOT_REC' => 'La pièce jointe n\'a pas pu être enregistrée !',
			'ERROR_OVERWEIGHT_AVATAR' => 'Avatar trop gros',
			'ERROR_PROHIBITED_FILE' => 'Type de fichier interdit !',
			'ERROR_TYPE_NOT_ZIP_FILE' => 'Le fichier que vous essayez d\'envoyer n\'est pas un fichier au format .zip. Merci de recommencer.',
			'ERROR_ZIP_NOT_OPEN' => 'L\'archive zip n\'a pas pu être ouverte.',
			'ERROR_DURING_UPLOAD' => 'Une erreur est survenue lors de l\'envoi. Merci de recommencer.',
			'ERROR_FILE_UNKNOWN' => 'Ce fichier n\'existe pas.',
			'ERROR_WRONG_PASSWORD' => 'Mauvais mot de passe pour',
			'L_ERR_WRONG_PASSWORD' => 'Mauvais mot de passe',
			'ERROR_USER_ALREADY_EXISTS' => 'Cet utilisateur existe déjà !',
			'ERROR_INVALID_EMAIL' => 'Vous avez fourni une adresse mail non valide !',
			'ERROR_FILL_FIELDS' => 'Merci de remplir les champs Identifiant, Mot de passe, adresse mail et date de naissance !',
			'ERROR_INVALID_PSEUDO' => 'Vous n\'avez pas indiqué de pseudonyme valide.',
			'ERROR_PSEUDO_ALREADY_USED' => 'Un membre est déjà inscrit sous ce pseudonyme.',
			'ERROR_INVALID_TOPIC' => 'Ce sujet n\'existe pas.',
			'ERROR_EMPTY_PSEUDO' => 'Vous n\'avez pas indiqué de pseudonyme.',
			'ERROR_THEME_NOTFOUND' => 'Le theme principal est introuvable',
			'ERROR_FILE_NOTFOUND' => 'Le fichier cible est introuvable',
			'ERROR_CAT_TITLE' => 'Le nom de la catégorie ne peut pas être vide',
			'ERROR_CAT_TYPE' => 'La catégorie principale est erronée',
			'PAGE_NOT_FOUND' => 'La page que vous demandez n\'existe pas ou n\'existe plus',
			'ERROR_INVALID_BIRTHDAY' => 'Format de date de naissance invalide',

			# Temps
			'DAY' => 'Jour',
			'HOUR' => 'heure',
			'MINUTE' => 'minute',
			'SECOND' => 'seconde',
			'PLURAL' => 's',
			'AGO'         => 'avant',
			'L_JANUARY'   => 'Janvier',
			'L_FEBRUARY'  => 'Février',
			'L_MARCH'     => 'Mars',
			'L_APRIL'     => 'Avril',
			'L_MAY'       => 'Mai',
			'L_JUNE'      => 'Juin',
			'L_JULY'      => 'Juillet',
			'L_AUGUST'    => 'Août',
			'L_SEPTEMBER' => 'Septembre',
			'L_OCTOBER'   => 'Octobre',
			'L_NOVEMBER'  => 'Novembre',
			'L_DECEMBER'  => 'Décembre',
			'L_MONDAY'    => 'Lundi',
			'L_TUESDAY'   => 'Mardi',
			'L_WEDNESDAY' => 'Mercredi',
			'L_THURSDAY'  => 'Jeudi',
			'L_FRIDAY'    => 'Vendredi',
			'L_SATURDAY'  => 'Samedi',
			'L_SUNDAY'    => 'Dim',
			'L_MOND'    => 'Lun',
			'L_TUES'   => 'Mar',
			'L_WEDN' => 'Mer',
			'L_THUR'  => 'Jeu',
			'L_FRID'    => 'Ven',
			'L_SATU'  => 'Sam',
			'L_SUND'    => 'Dim',

			# Captcha
			'L_CAPTCHA' => 'Répondez à la question :',
			'CANT_OPEN_CAPTCHA_FILE' => 'Impossible d\'ouvrir le fichier de questions : ',
			'CAPTCHA_WRONG_ANSWER' => 'La réponse n\'est pas la bonne. Merci de réessayer.',

			# Debug
			'L_ARRAY'                     => 'Tableau',
			'L_OBJECT'                    => 'Objet',
			'L_TYPE'                      => 'Variable de type',
			'L_CALL'                      => 'Appel de la méthode de debug',
			'L_LINE'                      => 'ligne',
			'L_OF_FILE'                   => 'du fichier',
			'L_YOUR_TRAC'                 => 'VOTRE TRACEUR',
			'L_YOUR_FLOW'                 => 'VOTRE FLUX',
			'L_TRAC'                      => 'TRACEUR',
			'L_FLOW'                      => 'FLUX',
		);

		$fr ='<?php
		'.TM."\n".'$LANG = array('."\n";
				foreach ($LANG as $key => $value) {
					$fr .= '\''.$key.'\' => "'.$value.'",'."\n";
				}
		$fr .= ');'."\n".'?>';

		if (!is_dir(MU_LANG)) {
			if(!mkdir(MU_LANG)) {
				echo $LANG['ERROR_MKLANG'];
				exit();
			}
		}
		if (!file_exists(MU_LANG.'fr.php')) {
			file_put_contents(MU_LANG.'fr.php', $fr);
		}
	} 


	/**
	 * Méthode qui traite les champ de type input
	 *
	**/
	public static function input($label, $name, $value='', $type='text', $placeholder='', $maxlength='', $readonly=false, $class='', $icon='', $require='',$onclick=false,$onfocus=false,$size='') {

			if($require) $class .= ' '.$require;

			$form = '<label class="control-label" for="'.$name.'">' .$label. '</label>';
			if($icon)
				$form .= '<span class="'.$icon.'">&nbsp;</span>';	
			else
				$form .= '';			               		      
			if($readonly)
				$form .= '<input id="'.$name.'" name="'.$name.'" type="'.$type.'" class="readonly'.($class!=''?' '.$class:'').'" value="'.$value.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($size!=''?' size="'.$size.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').' readonly="readonly"'.($onclick ? ' onclick="'.$onclick.'"' : '').($onfocus ? ' onfocus="'.$onfocus.'"' : '').'/>';
			else
				$form .= '<input id="'.$name.'" name="'.$name.'" type="'.$type.'"'.($class!=''?' class="'.$class.'"':'').' value="'.$value.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($size!=''?' size="'.$size.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').($onclick ? ' onclick="'.$onclick.'"' : '').($onfocus ? ' onfocus="'.$onfocus.'"' : '').'/>';	
			return $form;
	}
	/**
	 * Méthode qui traite une zone de texte
	 *
	**/	
	public static function textarea($label, $name, $value='', $cols='', $rows='', $placeholder='', $maxlength='', $readonly=false, $class='') {
	    $form = '<label class="control-label" for="'.$name.'">'.$label.'</label>';
		if($readonly)
			$form .= '<textarea id="'.$name.'" name="'.$name.'" class="readonly" cols="'.$cols.'" rows="'.$rows.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').' readonly="readonly">'.$value.'</textarea>';
		else
			$form .= '<textarea id="'.$name.'" name="'.$name.'"'.($class!=''?' class="'.$class.'"':'').' cols="'.$cols.'" rows="'.$rows.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').'>'.$value.'</textarea>';
		return $form;
	}	
	/**
	 * Méthode qui affiche une liste de sélection
	 *
	 * @param	label		label de la liste
	 * @param	name		nom de la liste
	 * @param	array		valeurs de la liste sous forme de tableau (nom, valeur)
	 * @param	selected	valeur par défaut
	 * @param	readonly	vrai si la liste est en lecture seule (par défaut à faux)
	 * @param	class		class css à utiliser pour formater l'affichage
	 * @param	id			si à vrai génère un id
	 * @return	stdout
	 **/
	public static function select($label, $name, $array, $selected='', $readonly=false, $class='', $id=true) {

		if(!is_array($array)) $array=array();

		$id = ($id?' id="id_'.$name.'"':'');
			echo '<label class="control-label" for="'.$name.'">' .$label. '</label>';
		if($readonly)
			echo '<select'.$id.' name="'.$name.'" disabled="disabled" class="readonly">'."\n";
		else
			echo '<select'.$id.' name="'.$name.'"'.($class!=''?' class="'.$class.'"':'').'>'."\n";
		foreach($array as $a => $b) {
			if(is_array($b)) {
				echo '<optgroup label="'.$a.'">'."\n";
				foreach($b as $c=>$d) {
					if($c == $selected)
						echo "\t".'<option value="'.$c.'" selected="selected">'.$d.'</option>'."\n";
					else
						echo "\t".'<option value="'.$c.'">'.$d.'</option>'."\n";
				}
				echo '</optgroup>'."\n";
			} else {
				if($a == $selected)
					echo "\t".'<option value="'.$a.'" selected="selected">'.$b.'</option>'."\n";
				else
					echo "\t".'<option value="'.$a.'">'.$b.'</option>'."\n";
			}
		}
		echo '</select>'."\n";
	}
	/**
	 * Méthode qui encode une chaine de caractère en hexadecimal
	 *
	 * @parm	s		chaine de caractères à encoder
	 * @return	string	chane de caractères encodée en hexadecimal
	 * @author	Stephane F
	 **/
	public static function encodeBin2Hex($s) {
		$encode = '';
		for ($i = 0; $i < strlen($s); $i++) {
			$encode .= '%' . bin2hex($s[$i]);
		}
		return $encode;
	}

	public static function protect_email($txt) {
		// mail recognition and obfuscation
		$txt = preg_replace('#(([0-9a-zA-Z\./~\-_]+)@([0-9a-z/~\-_]+\.[0-9a-z\./~\-_]+))#i', '<a href="mailto:$2@$3">$2@$3</a>', $txt); 
		if(preg_match_all('/<a.+href=[\'"]mailto:([\._a-zA-Z0-9-@]+)((\?.*)?)[\'"]>([\._a-zA-Z0-9-@]+)<\/a>/i', $txt, $matches)) {
			foreach($matches[0] as $k => $v) {
				$string = self::encodeBin2Hex('document.write(\'<a href="mailto:'.$matches[1][$k].'" title="'.$matches[1][$k].'">'.self::img('mail').'</a>\')');
				$txt = str_replace($matches[0][$k], '<script type="text/javascript">eval(unescape(\''.$string.'\'))</script>' , $txt);
			}
		}
		if(preg_match_all('#(([0-9a-zA-Z\./~\-_]+)@([0-9a-z/~\-_]+\.[0-9a-z\./~\-_]+))#i', $txt, $matches))
		{
			foreach($matches[0] as $k => $v) {
				$string = self::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
				$txt = str_replace($matches[0][$k], '<script type="text/javascript">eval(unescape(\''.$string.'\'))</script>' , $txt);
			}
		}
		return $txt;	
	}

	/**
	 * Méthode qui nettoie les champs
	 *
	**/
	public static function clean($text) {

		$text = utf8_encode(htmlentities($text,ENT_NOQUOTES,'UTF-8'));
		$text = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $text); 
		$text = str_replace("\0", '', $text);
		if(get_magic_quotes_gpc())
			$text = stripslashes($text);
		$text = self::correctAccents(self::_html(trim($text)));
		$text = str_replace('Â�', '', $text);

		return $text;
	}

	public static function _html($string) {
		return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
	}
}
/**
* BBCode
*/
class BBCHelper {

	private static $searchopentags = '%(\[(([\*a-zA-Z0-9-]*))(=.*?)?\])%m';
	private static $searchclosetags = '%(\[((\/[\*a-zA-Z0-9-]*))\])%m';
	private static $searchoptions = '%(\[(([\*a-zA-Z0-9-]*))\])(.+)(\[((\/[\*a-zA-Z0-9-]*))\])%U';

	/**
	*
	* AIDE FORMATTAGE BBCODE (Éditeur)
	*/
	public static function formattingHelp($id='message') {
		$aSmileys = array_keys(Tools::$names);
		$smileys='';
		$s=array(':)',';)',':D',':|',':(','8(',':p',':$','->'); // smileys
		for($i=0;$i<sizeof($s);$i++) { $smileys .= "<li><a href=\"javascript:insert(' ".$s[$i]." ','','".$id."',false,false);\" title='".$s[$i]."'>".Tools::img($aSmileys[$i])."</a></li>"; }
		return $smileys;
	}
	public static function areaToFormat($id) {
		return $id;
	}
	/**
	*
	* SYNTAXE HIGHLITER
	*/
	public static function colorSyntax($txt) { 
			return trim(nl2br("<pre><code>" . str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$txt).'<br />&nbsp;</code></pre>&nbsp;'));
	}
	/**
	*
	* PARSER BBcode 
	*/
	public static function bbCode($text, $summary = false) {

		// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
		if (strpos($text, '[c]') !== false && strpos($text, '[/c]') !== false) {
			list($inside, $text) = self::extract_blocks($text, '[c]', '[/c]');
		}

	    /* smiley */
	    $pattern[] = '%:\)%';    $replace[] = Tools::img('smile','',false,true);
	    $pattern[] = '%;\)%';    $replace[] = Tools::img('wink','',false,true);
	    $pattern[] = '%:D%' ;    $replace[] = Tools::img('laugh','',false,true);    
	    $pattern[] = '%:\|%';    $replace[] = Tools::img('indifferent','',false,true);
	    $pattern[] = '%:\(%';    $replace[] = Tools::img('sad','',false,true);
	    $pattern[] = '%8\(%';    $replace[] = Tools::img('wry','',false,true); 
	    $pattern[] = '%:p%';     $replace[] = Tools::img('tongue','',false,true);
	    $pattern[] = '%:\$%';    $replace[] = Tools::img('sorry','',false,true);
	    $pattern[] = '% -&gt; %';  $replace[] = Tools::img('arrow','',false,true);

	    $pattern[] = '%\[sm=smile\]%';          $replace[] = Tools::img('smile','',false,true);
	    $pattern[] = '%\[sm=wink\]%';           $replace[] = Tools::img('wink','',false,true);
	    $pattern[] = '%\[sm=laugh\]%' ;         $replace[] = Tools::img('laugh','',false,true);    
	    $pattern[] = '%\[sm=indifferent\]%';    $replace[] = Tools::img('indifferent','',false,true);
	    $pattern[] = '%\[sm=sad\]%';            $replace[] = Tools::img('sad','',false,true);
	    $pattern[] = '%\[sm=wry\]%';            $replace[] = Tools::img('wry','',false,true); 
	    $pattern[] = '%\[sm=tongue\]%';         $replace[] = Tools::img('tongue','',false,true);
	    $pattern[] = '%\[sm=sorry\]%';          $replace[] = Tools::img('sorry','',false,true);
	    $pattern[] = '%\[sm=arrow\]%';          $replace[] = Tools::img('arrow','',false,true);
	    	
		$text = preg_replace($pattern, $replace, $text);

		preg_match_all(self::$searchclosetags, $text, $matchesclosetags);
		preg_match_all(self::$searchopentags, $text, $matchesopentags);
		preg_match_all(self::$searchoptions, $text, $matchesoptions);

		$opentags = $matchesopentags[3];
		$opentagskeys = array_flip($opentags);

		$closetags = $matchesclosetags[3];
		$options = array();
		foreach ($matchesoptions[3] as $k => $v) {
			$options[$v] = $matchesoptions[4][$k];
		}

		array_walk($matchesopentags[4], create_function('&$v,$k', '$v = str_replace("=", "", $v);'));
		$paramsopentags = $matchesopentags[4];

		$replaceOpentags = array();
		foreach ($opentags as $k => $v) {
			switch ($v) {
				case 'q':
					$replaceOpentags[] = '<blockquote><strong>'.$paramsopentags[$k].WROTE.'</strong><p class="text-quote">';
					break;
				case 'quote':
					$replaceOpentags[] = '<blockquote><strong>'.$paramsopentags[$k].WROTE.'</strong><p class="text-quote">';
					break;
				case 'img':
					if(!preg_match('%^((http|ftp)(s)?:)?\/\/%', $paramsopentags[$k])) {$src = 'http://'.$paramsopentags[$k];}
					else {$src = $paramsopentags[$k];}
					$replaceOpentags[] = '<img class="thumbnail" src="'.$src.'" alt="';
					break;
				case 'url':
					if(!preg_match('%^((http|ftp)(s)?:)?\/\/%', $paramsopentags[$k])) {$src = 'http://'.$paramsopentags[$k];}
					else {$src = $paramsopentags[$k];}
					$replaceOpentags[] = '<a href="'.$src.'" onclick="window.open(this.href);return false;">';
					break;
				case 'b':
					$replaceOpentags[] = '<strong>';
					break;
				case 'c':
					$replaceOpentags[] = '<pre>'.(isset($options['c']) ? $options['c'] : '');
					break;
				case 'u':
					$replaceOpentags[] = '<span style="text-decoration:underline">';
					break;
				case 'i':
					$replaceOpentags[] = '<span style="font-style:italic;">';
					break;
				case 's':
					$replaceOpentags[] = '<del>';
					break;
				case 'e':
					$replaceOpentags[] = '<p class="editby">'.EDIT_BY.' : ';
					break;
				case 'title':
					$replaceOpentags[] = '<h'.$paramsopentags[$k].'>';
					break;
				case 'youtube':
					if(!preg_match('%^http:\/\/youtu\.be\/%', $paramsopentags[$k])) {$src = 'http://youtu.be/'.$paramsopentags[$k];}
					else {$src = $paramsopentags[$k];}
					$replaceOpentags[] = '<div class="center"><iframe width="560" height="315" src="//www.youtube.com/embed/'.str_replace('http://youtu.be/','',$src).'" frameborder="0" allowfullscreen></iframe><br/><em><a href="'.$src.'" onclick="window.open(this.href);return false;">';
					break;
				default:
					$replaceOpentags[] = '';
			}
		}
		$replaceClosetags = array(
			'/q' => '</p></blockquote>',
			'/quote' => '</p></blockquote>',
			'/img' => '"/>',
			'/url' => '</a>',
			'/youtube' => '',
			'/b' => '</strong>',
			'/c' => '</pre>',
			'/u' => '</span>',
			'/i' => '</span>',
			'/s' => '</del>',
			'/e' => '</p>',
			'/title' => '</h',
			'/youtube' => '</a></em></div>',
		);

		if (count($opentags) != count($closetags)) {
			return $text;
		} else {
			$niveau = '';
			foreach ($opentags as $k => $v) {
				if ($v == 'title') {$niveau = $paramsopentags[$k];}
				$text = str_replace('['.$v.'='.$paramsopentags[$k].']', $replaceOpentags[$k], $text);
				$text = str_replace('['.$v.']', $replaceOpentags[$k], $text);
			}
			foreach ($closetags as $k => $v) {
				if ($v == '/titre' && !empty($niveau)) {$text = str_replace('['.$v.']', $replaceClosetags[$v].$niveau.'>', $text);}
				else {$text = isset($replaceClosetags[$v]) ? str_replace('['.$v.']', $replaceClosetags[$v], $text) : $text;}
			}
		}
		
		// If we split up the message before we have to concatenate it together again (code tags)
		if (isset($inside))
		{
			$parts = explode("\1", $text);
			$text = '';
			foreach ($parts as $i => $part)
			{
				$text .= $part;
				if (isset($inside[$i]))
				{
					$num_lines = (substr_count($inside[$i], "\n"));
					$text .= '<pre>'.trim($inside[$i], "\n\r").'</pre>';
				}
			}
		}
		return nl2br($text);
		
	}
	// From forum Fluxbb
	//
	// Extract blocks from a text with a starting and ending string
	// This function always matches the most outer block so nesting is possible
	//
	function extract_blocks($text, $start, $end, $retab = true)
	{
		global $pun_config;

		$code = array();
		$start_len = strlen($start);
		$end_len = strlen($end);
		$regex = '%(?:'.preg_quote($start, '%').'|'.preg_quote($end, '%').')%';
		$matches = array();

		if (preg_match_all($regex, $text, $matches))
		{
			$counter = $offset = 0;
			$start_pos = $end_pos = false;

			foreach ($matches[0] as $match)
			{
				if ($match == $start)
				{
					if ($counter == 0)
						$start_pos = strpos($text, $start);
					$counter++;
				}
				elseif ($match == $end)
				{
					$counter--;
					if ($counter == 0)
						$end_pos = strpos($text, $end, $offset + 1);
					$offset = strpos($text, $end, $offset + 1);
				}

				if ($start_pos !== false && $end_pos !== false)
				{
					$code[] = substr($text, $start_pos + $start_len,
						$end_pos - $start_pos - $start_len);
					$text = substr_replace($text, "\1", $start_pos,
						$end_pos - $start_pos + $end_len);
					$start_pos = $end_pos = false;
					$offset = 0;
				}
			}
		}

		if ($pun_config['o_indent_num_spaces'] != 8 && $retab)
		{
			$spaces = str_repeat(' ', $pun_config['o_indent_num_spaces']);
			$text = str_replace("\t", $spaces, $text);
		}

		return array($code, $text);
	}

	public static function verif($text,$origin,$session) {

		preg_match_all(self::$searchopentags, $text, $matchesopentags);
		preg_match_all(self::$searchclosetags, $text, $matchesclosetags);
	
		$opentags = $matchesopentags[3];
		$closetags = $matchesclosetags[3];

		if (count($opentags) != count($closetags)) {
			$r = array_diff($opentags, $closetags);
			$li = '<ul>';
			foreach ($r as $k => $v) {
				$li .= '<li>'.$v.' ?</li>';
			}
			$li .= '</ul>';
			$session->setMsg('<div class="text-warning">Il semble que des balises soient mal fermées '.$li.'</div>','error',false);
			header('location:index.php?'.$origin.'#id_stop');
			exit();
		}
		return true;
	}

	/**
	* tronquer_texte
	* Coupe une chaine sans couper les mots
	*
	* @param string $texte Texte à couper
	* @param integer $nbreCar Longueur à garder en nbre de caractères
	* @return string
	*/
	public static function tronquer_texte($texte, $nbchar) {
	    return (strlen($texte) > $nbchar ? substr(substr($texte,0,$nbchar),0,
	    strrpos(substr($texte,0,$nbchar),' ')).'…' : $texte);
	}
	/**
	*
	* DÉCODE LES FICHIERS
	*/
	public static function decode($txt) {
		return self::bbCode($txt);
	}

	public function parse($msg) {

		return str_replace(array('<b>','<i>','<ins>','<pre>','<code>','</b>','</i>','</ins>','</pre>','</code>','<br />','&nbsp;','</div>','<blockquote>',WROTE,'</blockquote>'), array('[b]','[i]','[u]','[c]','','[/b]','[/i]','[/u]','[/c]','',"\n",' ','','[q=',']','[/q]'), stripslashes($msg));
	}
}
/**
* Mise en forme des dates
*/
class MuDate {
	/**
	 * Méthode qui retourne la date en Français
	 * Exemple : 
	 *   echo "Nous sommes le ". datefr(mktime()); 
	 *   $hier=mktime()-3600*24; 
	 *   echo "<p>hier nous étions le ".datefr($hier); 
	 * Pour le premier du mois 
	 *   $lepremiermars2012=mktime(0, 0, 0, 3, 1, 2012); 
	 *   echo "<p>le 1/03/2012 donne ".datefr($lepremiermars2012); 
	 *
	**/
	public static function datefr($arg) { 
	    $Jour = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi','Samedi');
	    $Mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	    $datefr = $Jour[date('w')].' '.date('d').' '.$Mois[date('n')].' '.date('Y');
	    return $datefr; 
	} 
	/**
	 * Méthode qui retourne l'âge i18n
	 *
	**/
	public static function birthday($birthdate, $pattern = 'eu') {
	    $patterns = array(
	        'eu'    => 'd/m/Y',
	        'mysql' => 'Y-m-d',
	        'us'    => 'm/d/Y',
	    );

	    $now      = new DateTime();
	    $in       = DateTime::createFromFormat($patterns[$pattern], $birthdate);
	    $interval = $now->diff($in);
	    return $interval->y;
	}
}

# ---------------- CLASSES ----------------------
/**
* Cette classe permet de sécuriser le debugage PHP dans vos scripts (locaux
* et distant).
* 
* A l'utilisation il vous suffit de l'inclure dans vos script.
*
* @author Jacksay<studio@jacksay.com>
* @author Cyril MAGUIRE<contact@ecyseo.net>
*/
class Debug {

		private static $DEBUG_FLOW = '';
		private static $DEBUG_OUTPUT = '';
		private static $TRAC_NUM = 0;
		private static $debug_instance;
		private static $debug = false;
		protected $version='3.0';

		public function __construct() {

		}

		/****************************************************************************/
		/** CONFIGURATION **/

		// Vous pouvez ajouter votre ip pour un debuggage distant
		// attention cependant
		public static $allow_IP = array('::1','127.0.0.1');
		/* array('::1','127.0.0.1','88.161.204.85'); */

		/****************************************************************************/
		/**
	    * Equivalent à un var_dump mais en version sécurisée et en couleur.
	    *
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 1.0
	    */
	    private static function _trac( $mixedvar, $comment='',  $sub = 0, $index = false ){
	      $type = htmlentities(gettype($mixedvar),ENT_NOQUOTES,'UTF-8');

	      $r ='';
	      switch ($type) {
	        case 'NULL':$r .= '<em style="color: #0000a0; font-weight: bold;">NULL</em>';break;
	        case 'boolean':if($mixedvar) $r .= '<span style="color: #327333; font-weight: bold;">TRUE</span>';
	        else $r .= '<span style="color: #327333; font-weight: bold;">FALSE</span>';break;
	        case 'integer':$r .= '<span style="color: red; font-weight: bold;">'.$mixedvar.'</span>';break;
	        case 'double':$r .= '<span style="color: #e8008d; font-weight: bold;">'.$mixedvar.'</span>';break;
	        case 'string':$r .= '<span style="color: '.($index === true ? '#e84a00':'#000').';">\''.$mixedvar.'\'</span>';break;
	        case 'array':$r .= L_ARRAY.'('.count($mixedvar).') &nbsp;{'."\r\n\n";
	        foreach($mixedvar AS $k => $e) $r .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $sub+1).'['.self::_trac($k, $comment, $sub+1, true).'] =&gt; '.($k === 'GLOBALS' ? '* RECURSION *':self::_trac($e, $comment, $sub+1)).",\r\n";
	            $r .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $sub).'}';
	            break;
	        case 'object':$r .= L_OBJECT.' «<strong>'.htmlentities(get_class($mixedvar),ENT_NOQUOTES,'UTF-8').'</strong>»&nbsp;{'."\r\n\n";
	          $prop = get_object_vars($mixedvar);
	          foreach($prop AS $name => $val){
	            if($name == 'privates_variables'){
	              for($i = 0, $count = count($mixedvar->privates_variables); $i < $count; $i++) $r .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $sub+1).'<strong>'.htmlentities($get = $mixedvar->privates_variables[$i],ENT_NOQUOTES,'UTF-8').'</strong> =&gt; '.self::_trac($mixedvar->$get, $comment, $sub+1)."\r\n\n";
	              continue;
	            }
	            $r .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $sub+1).'<strong>'.htmlentities($name,ENT_NOQUOTES,'UTF-8').'</strong> =&gt; '.self::_trac($val, $comment, $sub+1)."\r\n\n";
	          }
	          $r .= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $sub).'}';break;
	        default:$r .= L_TYPE.' <strong>'.$type.'</strong>.';break;
	      }
	      $r = preg_replace('/\[(.*)\]/', '[<span class="jcktraker-id">$1</span>]', $r);
	      return $r;
	    }
	    /**
	    * Pour tracer une variable
	    *
	    * @author  Jacksay<studio@jacksay.com>
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 2.0
	    */
	    public static function trac( $mixedvar, $comment='',  $sub = 0 ) {
	      $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	      $printDebug = '';
	      foreach ($debug as $key => $value) {
	        if ($value['function'] == 'trac') {
	          $function = 'trac';
	          if (isset($value['line'])) {
	         	 	$line = $value['line'];
	          		$file = $value['file'];
	          } else {
	          		$line = $file = '';
	          }
	        }
	        if ($line == '') {
	          if ($value['function'] == 'd') {
	            $function = 'd';
	            $line = $value['line'];
	            $file = $value['file'];
	            break;
	          }
	        }
	      }
	      $printDebug .=  '<p class="jcktraker-backtrace">'."\n".'&nbsp;'.L_CALL.' <strong>'.$function.'()</strong> '.L_LINE.' '.$line. ' '.L_OF_FILE."\n\n".'&nbsp;<strong><em>'.$file.'</em></strong>'."\n\n".'<br/></p><br/>';
	      
	      $FILE = fopen( $file, 'r' );
	      $LINE = 0;
	      if ($comment == '') {
	          while ( ( $row = fgets( $FILE ) ) !== false ) {
	            if ( ++$LINE == $line ) {
	                $row = str_replace(array('<','>'), '', $row);
	                preg_match('/(?:.*)*d\((.*)\);(?:.*)*/U', $row, $match);
	                if (isset($match[1])) $comment = $match[1];
	                preg_match('/(?:.*)*Debug::trac\((.*)\);(?:.*)*/U',$row, $match);
	                if (isset($match[1])) $comment = $match[1];
	              break;
	            }
	          }
	          fclose( $FILE );
	      }
	      $r = self::_trac( $mixedvar, $comment, $sub);
	      $r .= "\n\n\n"; 
	      self::$DEBUG_OUTPUT .= '<pre id="jcktraker-backtrace-'.self::$TRAC_NUM.'">'."\n\n".$printDebug.'<strong class="jcktraker-blue">'.$comment.'</strong> = '. $r ."</pre>\n";
	      self::$TRAC_NUM++;
	    }
	    /**
	    * Pour décomposer une variable globale
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 1.0
	    */
	    private static function _color($value) {
	      return "\n\n".self::_trac($value)."\n\n\n";
	    }

	    /**
	    * Affiche une petite ligne pour suivre le fil de l'exécution.
	    * A utiliser dans un foreach par exemple pour savoir quel valeur prend une variable
	    *
	    * @author  Jacksay<studio@jacksay.com>
	    * @version 1.0
	    */
	    public static function flow( $message, $type=1 ) {
	      $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	      $printDebug = '';
	      foreach ($debug as $key => $value) {
	        if ($value['function'] == 'flow') {
	        	$function = 'flow';
	          if (isset($value['line'])) {
  	         	 	$line = $value['line'];
  	          		$file = $value['file'];
  	          } else {
  	          		$line = $file = '';
  	          }
	        }
	        if ($line == '') {
	          if ($value['function'] == 'f') {
	            $function = 'f';
	            $line = $value['line'];
	            $file = $value['file'];

	          }
	        }
	      }
	      $printDebug .=  '<p class="jcktraker-backtrace">'."\n".'&nbsp;'.L_CALL.' <strong>'.$function.'()</strong> '.L_LINE.' '.$line. ' '.L_OF_FILE."\n\n".'&nbsp;<strong><em>'.$file.'</em></strong>'."\n\n".'<br/></p><br/>';
	      

	      $FILE = fopen( $file, 'r' );
	      $LINE = 0;
	      $comment = '';
	          while ( ( $row = fgets( $FILE ) ) !== false ) {
	            if ( ++$LINE == $line ) {
	                $row = str_replace(array('<','>'), '', $row);
	                preg_match('/(?:.*)*f\((.*)\);(?:.*)*/U', $row, $match);
	                if (isset($match[1])) $comment = $match[1];
	                preg_match('/(?:.*)*Debug::flow\((.*)\);(?:.*)*/U',$row, $match);
	                if (isset($match[1])) $comment = $match[1];
	              break;
	            }
	          }
	          fclose( $FILE );
	     if ( self::$DEBUG_FLOW!=$printDebug ) {
	        self::$DEBUG_FLOW = $printDebug;
	        self::$DEBUG_OUTPUT .= self::$DEBUG_FLOW.'<p class="jcktraker-flow-'.$type.'">'.$comment.' = '.htmlentities($message,ENT_NOQUOTES,'UTF-8')."</p>\n";     
	     } else {
	        self::$DEBUG_OUTPUT .= '<p class="jcktraker-flow-'.$type.'">'.$comment.' = '.htmlentities($message,ENT_NOQUOTES,'UTF-8')."</p>\n";
	     }
	      self::$TRAC_NUM++;
	    }

	    /**
	    * Cette méthode est automatiquement appelée lorsque vous importez le fichier
	    * JckTraker.php dans votre script.
	    *
	    * @author  Jacksay<studio@jacksay.com>
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 2.0
	    */
	    public function init() {
	      if(in_array($_SERVER['REMOTE_ADDR'], self::$allow_IP)){
	        self::$debug = true;
	        //error_reporting(E_ALL);
	      } else {
	        self::$debug = false;
	        error_reporting(0);
	      }
	    }


	    /**
	    * Accesseur
	    *
	    * @author  Jacksay<studio@jacksay.com>
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 2.0
	    */
	    public static function getDebugInstance($default_lang) {
	      if(!isset (self::$debug_instance) ){
	        self::$debug_instance = new Debug($default_lang);self::$debug = true;
	        self::init();
	      }
	      return self::$debug_instance;
	    }

	    /**
	    * Elément clef, va afficher la barre de debug dans votre page.
	    * A placer juste avant la balise </body>
	    *
	    * @author  Jacksay<studio@jacksay.com>
	    * @author  Cyril MAGUIRE<contact@ecyseo.net>
	    * @version 2.0
	    */
	    public function printBar() {
	      if( !self::$debug ) return;
	      ?>
	      <!-- JCK TRAKER BOX v1.0 -->
	      <script type="text/javascript">
	      function jcktraker_hide(){
	        var sections = document.getElementsByName('jcktraker-section');
	        var num_sections = sections.length;
	        for( var i=0; i<num_sections; i++ ){
	          sections[i].style.display = 'none';    
	        }
	      }
	      function jcktraker_toogle( section, dispatcher ){
	        var section_blk = document.getElementById(section);
	        if( section_blk.style.display != 'block'){
	          jcktraker_hide();
	          section_blk.style.display = 'block';
	          dispatcher.style.fontWeight = 'bold';
	          dispatcher.style.backgroundColor = '#990000';
	          dispatcher.style.color = '#FFFFFF';
	        }
	        else {
	          section_blk.style.display = 'none';
	          dispatcher.style.fontWeight = "normal";
	          dispatcher.style.backgroundColor = '#000000';
	          dispatcher.style.color = '#FFFFFF';
	        }
	      }
	      </script>
	      <style type="text/css">
	      .jcktraker-blue {
	        color:#8bb5eb;
	      }
	      .jcktraker-id {
	        color:#e8008d;
	      }
	      #jcktraker-box {
	        z-index:99999;
	        position: fixed;
	        bottom: 0;
	        right: 0;
	        font-size: 10px;
	        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	        max-height: 100%;
	        max-width: 75%;
	        margin: 0;
	        padding: 0;
	        -moz-border-radius: .4em;
	        -moz-box-shadow: 0 0 5em #000;
	        border-radius: .4em;
	        box-shadow: 0 0 5em #000;
	      }
	      #jcktraker-box *{
	        margin: 0;
	        padding: 0;
	        border-radius: .4em;
	        -moz-border-radius: .4em;
	      }
	      #jcktraker-box pre{
	        color:#000;
	        margin: 0 2em;
	        border: dotted thin #999;
	        border-radius: .4em;
	        box-shadow: 0 0 1em #000 inset;
	        -moz-border-radius: .4em;
	        -moz-box-shadow: 0 0 5em #000 inset;
	        padding: .4em .6em;
	        background-color: #e4e4e4;
	        font-size: 1.2em;
	        white-space: pre;           /* CSS 2.0 */
	        white-space: pre-wrap;      /* CSS 2.1 */
	        white-space: pre-line;      /* CSS 3.0 */
	        white-space: -pre-wrap;     /* Opera 4-6 */
	        white-space: -o-pre-wrap;   /* Opera 7 */
	        white-space: -moz-pre-wrap; /* Mozilla */
	        white-space: -hp-pre-wrap;  /* HP Printers */
	        word-wrap: break-word;      /* IE 5+ */
	      }
	      #jcktraker-box p{
	        margin: 0 1em;
	      }
	      ul#jcktraker-menu li {
	        display: inline;
	        padding: 0 .4em;
	        line-height: 2em;
	      }
	      ul#jcktraker-menu li[onclick]:hover {
	        background: #990000;cursor: pointer;
	      }

	      #jcktraker-box div[name="jcktraker-section"] {
	        display: none;
	        white-space: pre-wrap;
	        overflow: hidden;
	        max-width: 100%;
	        max-height: 580px;
	        background: #111;
	        color: #fff;
	        opacity: .7;
	      }
	      #jcktraker-box div[name="jcktraker-section"]:hover {
	        opacity: 1;
	      }
	      #jcktraker-box div[name="jcktraker-section"] pre {
	        height: 460px;
	        overflow: scroll;
	      }
	      #jcktraker-own {
	        padding-bottom: 30px;
	      }
	      #jcktraker-menu {
	        background: #000;
	        color: #fff;
	        white-space:nowrap;
	        text-align: right;
	        -moz-border-radius: .4em 0 0 0;
	        border-radius: .4em 0 0 0;
	      }
	      .jcktraker-backtrace {
	        background-color: #e4a504;
	      }
	      .jcktraker-backtrace-close {
	        display: block;
	        position: relative;
	        background-color: red;
	        padding:5px;
	        float:right;
	        cursor: pointer;
	      }
	      #jcktraker-pre {
	        height: 530px;
	        overflow: scroll;
	      }

	      </style>
	      <div id="jcktraker-box">
	        <div id="jcktraker-post" name="jcktraker-section">
	          <strong>$_POST</strong>
	          <pre><?php echo self::_color($_POST); ?></pre>
	        </div>
	        <div id="jcktraker-files" name="jcktraker-section">
	          <strong>$_FILES</strong>
	          <pre><?php echo self::_color($_FILES); ?></pre>
	        </div>
	        <div id="jcktraker-get" name="jcktraker-section">
	          <strong>$_GET</strong>
	          <pre><?php echo self::_color($_GET); ?></pre>
	        </div>
	        <div id="jcktraker-server" name="jcktraker-section">
	          <strong>$_SERVER</strong>
	          <pre><?php echo self::_color($_SERVER); ?></pre>
	        </div>
	        <div id="jcktraker-session" name="jcktraker-section">
	          <strong>$_SESSION</strong>
	          <pre><?php if(isset($_SESSION)) echo self::_color($_SESSION); ?></pre>
	        </div>
	        <div id="jcktraker-cookie" name="jcktraker-section">
	          <strong>$_COOKIE</strong>
	          <pre><?php echo self::_color($_COOKIE); ?></pre>
	        </div>
	        <div id="jcktraker-request" name="jcktraker-section">
	          <strong>$_REQUEST</strong>
	          <pre><?php echo self::_color($_REQUEST); ?></pre>
	        </div>
	        <div id="jcktraker-own" name="jcktraker-section">
	          <strong><?php echo (self::$DEBUG_FLOW == '') ? L_YOUR_TRAC : L_YOUR_FLOW ?></strong>
	          <div id="jcktraker-pre">
	            <?php echo self::$DEBUG_OUTPUT; ?>

	          </div>
	        </div>
	        <ul id="jcktraker-menu">
	          <li><strong>ToolBarDebug <span>v <?php echo $this->version ?> </span></strong></li>
	          <li id="jacktraker_own_button" onclick="jcktraker_toogle('jcktraker-own', this)"><?php echo (self::$DEBUG_FLOW == '') ? L_TRAC : L_FLOW ?>(<?php echo self::$TRAC_NUM ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-post', this)">$_POST(<?php echo count($_POST) ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-files', this)">$_FILES(<?php echo count($_FILES) ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-get', this)">$_GET(<?php echo count($_GET) ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-server', this)">$_SERVER(<?php echo count($_SERVER) ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-session', this)"><?php if(isset ($_SESSION)) { echo '$_SESSION(',count($_SESSION),')'; } else { echo '<del>$_SESSION</del>';} ?></li>
	          <li onclick="jcktraker_toogle('jcktraker-cookie', this)">$_COOKIE(<?php echo count($_COOKIE) ?>)</li>
	          <li onclick="jcktraker_toogle('jcktraker-request', this)">$_REQUEST(<?php echo count($_REQUEST) ?>)</li>
	        </ul>
	      </div>
	      <?php if(!empty (self::$DEBUG_OUTPUT) ): ?>
	      <script type="text/javascript">jcktraker_toogle('jcktraker-own', document.getElementById('jacktraker_own_button'));</script>
    	<?php endif;
    }
}
/**
 * Définition des alias de la classe Debug
 */
if (DEBUG == 1) {
	/**
	* Dump variable
	* Alias of Debug::trac()
	*/
	if ( !function_exists( 'd' ) ) {
	  function d() {
	    call_user_func_array( array( 'Debug', 'trac' ), func_get_args() );
	  }
	}
	/**
	* Dump variable
	* Alias of Debug::flow()
	*/
	if ( !function_exists( 'f' ) ) {
	  function f() {
	    call_user_func_array( array( 'Debug', 'flow' ), func_get_args() );
	  }
	}
}
/**
* Sessions
*/
class Session {

	public $MsgId;
	
	public function __construct(){
		if (!isset($_SESSION))
			# On démarre la session
			session_start();
			define('session_domain',dirname(__FILE__));
		if (!isset($_SESSION['mu_lang']))
			 $_SESSION['mu_lang'] = LANG;
	}

	public function setMsg($msg,$type = 'success-msg',$fadeout=true) {
		$_SESSION['msg'] = array(
			'msg' => $msg,
			'type' => $type,
			'id' => mt_rand(1,50),
			'fadeout' => $fadeout
		);
	}

	public function msg() {
		if (isset($_SESSION['msg']['msg'])) {
			$_GET = array();
			$_POST = array();
			$id = ($_SESSION['msg']['fadeout']==true?$_SESSION['msg']['id']:'stop');
			$r = '<div id="msgFlash"><div class="msgFlash '.$_SESSION['msg']['type'].'" id="id_'.$id.'"><p>'.$_SESSION['msg']['msg'].'</p><p class="close-right" onclick="document.getElementById(\'id_'.$id.'\').className=\'closed\';">X</p></div></div>';
			$this->MsgId = 'id_'.$_SESSION['msg']['id'];
			$_SESSION['msg'] = array();
			return $r;
		}
	}

	public function MsgId() {
		return $this->MsgId;
	}

	public function write($key,$value) {
		$_SESSION[$key] = $value;
	}

	public function read($key=null) {
		if ($key){
			if (isset($_SESSION[$key])){
				return $_SESSION[$key];
			}else{
				return false;
			}
		}else {
			return $_SESSION;
		}
	}

	public function isLogged() {
		return (isset($_SESSION['user']->id) && $_SESSION['user']->duration > time());
	}

	public function user($key) {
		if ($this->read('user')) {
			if (isset($this->read('user')->$key)) {
				return $this->read('user')->$key;
			} else {
				return false;
			}
		}
		return false;
	}
} 
/**
* CAPTCHA
* D'après LionWiki 3.2.9, (c) Adam Zivner, licensed under GNU/GPL v2 (Plugin Captcha)
*/
class Captcha {

	private $question_file;
	public $session;
	
	public function __construct($lang,$session) {
		$this->session = $session;

		$this->question_file = CAPTCHA.DS;

		if (!is_dir($this->question_file)) {
			@mkdir($this->question_file);
		}
		$this->mkFrQuest();
		$this->mkEnQuest();

		if(file_exists($this->question_file.$lang."_questions.txt"))
			$this->question_file .= $lang."_questions.txt";
		else
			$this->question_file .= "en_questions.txt";

		$this->actionBegin();
	}

	private function mkFrQuest() {
		$txt = "# File for turing test questions. Structure of the file is very simple, first\n";
		$txt .= "# line of a record is \"--\" which indicates new record (question). Second line\n";
		$txt .= "# is question and third line is right answer. You can add more answers to third\n";
		$txt .= "# separated by comma. Everything else is ignored, so you can use it as comments.\n";
		$txt .= "# In that case, please use something like \"#\" or \"//\" to make it clear it\n";
		$txt .= "# is comment. Comparing answers is case insensitive.\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "De quelle couleur est le citron?\n";
		$txt .= "Jaune, jaune\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Combien font 4 fois 4?\n";
		$txt .= "16, seize, Seize, SEIZE\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "chat = Tom, souris = ?\n";
		$txt .= "Jerry, jerry\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "On prend la température avec un ...?\n";
		$txt .= "Thermomètre, thermomètre, thermometre, termometre, termomètre\n";
		$txt .= "--\n";
		$txt .= "Corrigez le mot : aurtografe\n";
		$txt .= "Orthographe, orthographe\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "22 moins 17?\n";
		$txt .= "5, cinq, Cinq, CINQ\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Je pense donc je ... ?\n";
		$txt .= "suis, SUIS\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Prénom d'Einstein?\n";
		$txt .= "Albert, albert, ALBERT\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Qui est le frère de Mario ?\n";
		$txt .= "Luiggi, luiggi\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Où se trouve la Tour Eiffel ?\n";
		$txt .= "Paris, paris, PARIS";

		if(!file_exists($this->question_file."fr_questions.txt")) {
			file_put_contents($this->question_file."fr_questions.txt", $txt);
		}
		if(!file_exists($this->question_file.'index.html')) {
			file_put_contents($this->question_file.'index.html', GOTO_INDEX);
		}
	}
	private function mkEnQuest() {
		$txt = "# File for turing test questions. Structure of the file is very simple, first\n";
		$txt .= "# line of a record is \"--\" which indicates new record (question). Second line\n";
		$txt .= "# is question and third line is right answer. You can add more answers to third\n";
		$txt .= "# separated by comma. Everything else is ignored, so you can use it as comments.\n";
		$txt .= "# In that case, please use something like \"#\" or \"//\" to make it clear it\n";
		$txt .= "# is comment. Comparing answers is case insensitive.\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "What color is lemon?\n";
		$txt .= "Yellow\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "How much is 4 times 4?\n";
		$txt .= "16, sixteen\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "cat - Tom, mouse -\n";
		$txt .= "Jerry\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Shortcut of World War 2?\n";
		$txt .= "WW2, WWII\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Correct spelling: univrsity\n";
		$txt .= "University\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Difference between 22 and 17?\n";
		$txt .= "5, five\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "I think, therefore I...\n";
		$txt .= "am\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "First name of Einstein?\n";
		$txt .= "Albert\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "How many moons has the Earth?\n";
		$txt .= "1, one\n";
		$txt .= "\n";
		$txt .= "--\n";
		$txt .= "Name of partner of Eve of Eden?\n";
		$txt .= "Adam";

		if(!file_exists($this->question_file."en_questions.txt")) {
			file_put_contents($this->question_file."en_questions.txt", $txt);
		}
	}
	public function actionBegin() {
		if(isset($_REQUEST["qid"]))
			$this->checkCaptcha();
	}

	/*
	 * Functions return number of questions in question file. Method is very simple, it just counts
	 * number of occurence of "--" at the begining of the line.
	 */

	private function questionCount() {
		$count = 0;
		$q = fopen($this->question_file, "r");

		if(!$q) {
			//return 0; // Oops
			$this->session->setMsg(CANT_OPEN_CAPTCHA_FILE.$this->question_file,'error');
			header('location: index.php?register=true');
			exit();
		}

		while($line = fgets($q))
			if(!strcmp(trim($line), "--"))
				$count++;
		fclose($q);
		return $count;
	}

	/*
	 * Function returns $line. line of $i. question. Convention is that 1. line is question and
	 * second line is answer(s). Numbering is Pascal-like, that means that getQuestion(1, 1) returns 1. line of 1. question.
	 */

	private function getQuestion($i, $line) {
		$count = 0;
		$q = fopen($this->question_file, "r");
		if(!$q) {
			//return 0; // Oops
			$this->session->setMsg(CANT_OPEN_CAPTCHA_FILE.$this->question_file,'error');
			header('location: index.php?register=true');
			exit();
		}
		$str = "";

		while($l = fgets($q)) {
			if(!strcmp(trim($l), "--")) {
				$count++;
				if($count == $i) {
					for($k = 0, $str = ""; $k < $line && $str = fgets($q); $k++);
					break;
				}
			}
		}
		fclose($q);
		return $str;
	}

	private function checkCaptcha() {
		$question_id = $_REQUEST["qid"];
		$answer = trim($_REQUEST["ans"]);

		// if(empty($question_id) || empty($answer) || !is_numeric($question_id))
		// 	return true;

		$right_answers = explode(",", $this->getQuestion($question_id, 2));

		$equals = false;

		foreach($right_answers as $a)
			if(!strcasecmp(trim($a), $answer)) {
				$equals = true;
				break;
			}

		if(!$equals) {
			$this->session->setMsg(CAPTCHA_WRONG_ANSWER,'error');
			header('location: index.php?register=true');
			exit();
		}
		return !$equals;
	}

	public function template() {
		$question_count = $this->questionCount();
		$question_id = rand(1, $question_count);
		$question_text = trim($this->getQuestion($question_id, 1));

		$html = '<span id="captcha-question">' . $question_text . "</span>";
		$html .= "<input type=\"hidden\" id=\"captcha-id\" name=\"qid\" value=\"$question_id\" /><input type=\"text\" id=\"captcha-input\" name=\"ans\" class=\"input input-success success\" value=\"\" />";
		return $html;
	}
}
/**
*
* SAUVEGARDE LES OBJETS
*/
class SaveObj {
	public $name= '';
	public function __construct() {}

	public function SaveObj($obj) {
		if (!empty($this->name)) {
			file_put_contents($this->name, utf8_encode(serialize($obj)),LOCK_EX);
		}
	}

	public function verifName($obj,$PATH) {
		$path = str_replace($this->whichDir($obj->time).'.dat', '', $obj->name);
		if ($path != $PATH) { $obj->name = $PATH.$this->whichDir($obj->time).'.dat';}
	}

	public function SaveMsgObj($obj) {
		foreach ($obj->mess as $key => $value) {
			file_put_contents(MU_MEMBER.md5($value->to.SECURITY_SALT).DS.$value->to.'.mp', serialize($obj),LOCK_EX);
		}
	}
	public function mkdirThread($time) {
		$dir = date('Ym', $time);
		if (!is_dir(MU_THREAD.$dir)) {mkdir(MU_THREAD.$dir);}
	}
	public function whichDir($time) {
		return date('Ym', $time).DS.$time;
	}
}
/**
 *
 * Gestion des membres 
 */
class Members extends SaveObj {

	public $members=array();

	public function __construct() {
		parent::__construct();
		$this->name = MU_MEMBER.'members.dat';
		if (is_file(MU_MEMBER.'members.dat')){
			$mb = unserialize(file_get_contents(MU_MEMBER.'members.dat'));
			if ($mb->members !== null){
				$this->members = $mb->members;	
			}
		}
	}
	public function addMember($name,$password,$mail,$quote='',$url='',$birthday,$pic='',$mod=0) {
		if(!count($this->members)) $mod=2;
		if (!isset($this->members[$name])) {$this->members[$name] = new stdClass();}
		$this->members[$name]->password = md5($password);//0
		$this->members[$name]->time = time();//1
		$this->members[$name]->mail = $mail;//2
		$this->members[$name]->quote = $quote;//3
		$this->members[$name]->url = $url;//4
		$this->members[$name]->birthday = $birthday;//5
		$this->members[$name]->pic = $pic;//6
		$shortpic = str_replace(MU_UPLOAD,'',$pic);
		$this->members[$name]->extension = substr($shortpic,strpos($shortpic,'.')+1);
		$this->members[$name]->mod = $mod;//7
		$this->members[$name]->post = 0;//8
		ksort($this->members);
		$this->saveObj($this);
	}
	public function removeMember($name) {
		if (isset($this->members[$name])) {unset($this->members[$name]);}
		ksort($this->members);
		$this->saveObj($this);
	}
	public function getMember($name) {
		return isset($this->members[$name]) ? $this->members[$name]:false;
	}
	public function isMember($name) {
		return isset($this->members[$name]); 
	}
	public function setMember($name,$mail,$quote='',$url='',$birthday,$pic='') {
		if($pic=='') $pic=$this->members[$name]->pic;
		$this->members[$name]->mail = $mail;//2
		$this->members[$name]->quote = $quote;//3
		$this->members[$name]->url = $url;//4
		$this->members[$name]->birthday = $birthday;//5
		$this->members[$name]->pic = $pic;//6
		$shortpic = str_replace(MU_UPLOAD,'',$pic);
		$this->members[$name]->extension = substr($shortpic,strpos($shortpic,'.')+1);
		$this->saveObj($this);
	}
	public function setForumPost($name) { 
		if (!isset($this->members[$name])) {$this->members[$name] = new stdClass;}
		$this->members[$name]->post++;
		$this->saveObj($this);
	}
	public function addTopic($auth) {	
		if(isset($this->members[$auth])) $this->members[$auth]->post++;
		$this->saveObj($this);
	}
	public function setMod($name) {
		if (!isset($this->members[$name])) {$this->members[$name] = new stdClass;}
		$this->members[$name]->mod=$this->members[$name]->mod?0:1;
		$this->saveObj($this);
	}
	public function listMember($nbrMax=15,$fromPage=1,$returnAll=true) {
		$tmp=array();
		foreach($this->members as $k=>$v) $tmp[]=$k;
		if ($returnAll) {return $tmp;}
		else {
			$nbMb = count($tmp);
			$nbPages = ceil($nbMb/$nbrMax);
			if ($nbPages == 0) $nbPages = 1;
			if ($fromPage>$nbPages) $fromPage = $nbPages;
			$to = $fromPage*$nbrMax;
			if ($to>$nbMb) $to = $nbMb;
			$from = ($fromPage*$nbrMax)-$nbrMax;
			for ($i=$from; $i < $to; $i++) { 
				$return[] = $tmp[$i];
			}
			return $return;
		}
		
	}
	public function checkMember($name,$pass) {
		$login=(isset($this->members[$name]));
		if($login) {
			if (!is_object($this->members[$name])) {$this->members[$name] = new stdClass;}
			$pass=($this->members[$name]->password!=$pass)?false:true;
			$mod=$this->members[$name]->mod;
			return array($login,$pass,$mod);
		}
		return array(0,0,0);
	}
}
/**
*
* CLASSE GLOBALE DE GESTION DU FORUM
*/
class Forum extends SaveObj {
	public $name;
	public $topics=array();
	
	public function __construct() {
		parent::__construct();
		$msg = scandir(MU_THREAD);
		foreach ($msg as $m) {
			if($m[0] != '.' && is_dir(MU_THREAD.$m)) {
				$mes = scandir(MU_THREAD.$m);
				foreach ($mes as $file) {
					$id = substr($file, -4);
					if ($id == '.dat') {
						$t = unserialize(file_get_contents(MU_THREAD.$m.DS.$file));
						$this->topics[$t->time] = $t;
					}
				}
			} else {
				$id = substr($m, -4);
				if ($id == '.dat' && $m != 'threads.dat') {
					$t = unserialize(file_get_contents(MU_THREAD.$m));
					$this->topics[$t->time] = $t;
				}
			}
		}
	}
	
	public function getPosts($topic,$showAll=false,$nbrMax=15,$fromPage=1) {
		if($s = implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
			$obj = unserialize($s);
		}
		$obj->nbPosts = count($obj->reply);
		if ($showAll) {
			return $obj;
		} else {
			// Retourne uniquement le nombre de posts nécessaires par page
			$nbPages = ceil($obj->nbPosts/$nbrMax);
			if ($nbPages == 0) $nbPages = 1;
			if ($fromPage>$nbPages) $fromPage = $nbPages;
			$to = $fromPage*$nbrMax;
			if ($to>$obj->nbPosts) $to = $obj->nbPosts;
			$from = ($fromPage*$nbrMax)-$nbrMax;
			for ($i=$from; $i < $to; $i++) { 
				$return[] = $obj->reply[$i];
			}
			$obj->reply = $return;
			return $obj;
		}
	}
	public function getPostsTitle($topic) {
		if($s = implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
			$obj = unserialize($s);
		}
		return $obj->title;
	}
	public function getPostAuth($topic) {
		if($s = implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
			$obj = unserialize($s);
		}
		return $obj->auth;
	}
	public function delTopic($id) {
		$t = $this->openTopic($id);
		if (is_object($t->infos)){
			$nbMsg = $t->infos->posts;
		} else {
			$nbMsg = 0;
		}
		$t->removeTopic();
		$t = unserialize(file_get_contents(MU_THREAD.'threads.dat'));
		$t->removeTopicFromThreads($id);
		$stat = new Stat();
		$stat->updateStats('msg',-$nbMsg);
		$stat->updateStats('topics',-1);
		if (isset($this->topics[$id])) {unset($this->topics[$id]);return true;}
		
	}
	public function setType($topic,$type) {
		$this->name = MU_THREAD.$this->whichDir($topic).'.dat';
		$this->topics[$topic]->type = $type;
		$t = $this->openTopic($topic);
		$t->setTopicType($type);
		unset($t);
		//$this->saveObj($this);
	}
	public function setTitle($topic,$title) {
		$this->name = MU_THREAD.$this->whichDir($topic).'.dat';
		$this->topics[$topic]->title=$title;
		$t = $this->openTopic($topic);
		$t->setTopicTitle($title);
		unset($t);
		// $this->lastSort();
		// $this->saveObj($this);
	}
	public function getallTopic($showAll=false,$main,$cat,$nbrMax=15,$fromPage=1) {
		$this->threads = unserialize(file_get_contents(MU_THREAD.'threads.dat'));
		$mainCats = $this->threads->getMainCats();
		$cats = $this->threads->getCats();
		unset($this->threads);
		if (array_key_exists($main, $mainCats) && array_key_exists($cat, $cats)) {
			$tmp=array();
			$return=array();
			$this->lastSort();
			foreach($this->topics as $k=>$v) {
				if (isset($v->reply) && $v->reply !== null){
					if (is_object($v)) $v = get_object_vars($v);
					krsort($v['reply']);
					if (in_array($v['time'], $cats[$cat])) {
						$current = current($v['reply']);
						if(!is_object($current)) {$current = new stdClass;} 
						$tmp[$v['type']][$v['time']] = array(
							'titre' => $v['title'],
							'auteur' => $v['auth'],
							'nombrePosts' => count($v['reply']),
							'dernierPar' => $current->auth,
							'dernierLe' => $current->time,
							'attachment' => $current->attach,
							'postType' => $v['type'],
							'topicID' => $v['time'],
						);
					}
				} else {
					if (in_array($v->time, $cats[$cat])) {
						if (isset($v->type)) {
							$tmp[$v->type][$v->time] = array(
							'titre' => $v->title,
							'auteur' => $v->auth,
							'nombrePosts' => 1,
							'dernierPar' => $v->auth,
							'dernierLe' => $v->time,
							'attachment' => $v->attach,
							'postType' => $v->type,
							'topicID' => $v->time,
						);
						} 
					}
				}
			}
			$pin = array();
			$norm = array();
			foreach ($tmp as $key => $array) {
				if ($key==1){
					$pin = $array;
				} else {
					$norm = $array;
				}
			}
			ksort($pin);
			krsort($norm);
			$tmp = array();
			foreach ($pin as $k => $v) {
				$tmp[] = $v;
			}
			foreach ($norm as $k => $v) {
				$tmp[] = $v;
			}
			if ($showAll) {
				return $tmp;
			} else {
				$nbTopics = count($tmp);
				$nbPages = ceil($nbTopics/$nbrMax);
				if ($nbPages == 0) $nbPages = 1;
				if ($fromPage>$nbPages && $nbPages) $fromPage = $nbPages;
				$to = $fromPage*$nbrMax;
				if ($to>$nbTopics) $to = $nbTopics;
				$from = ($fromPage*$nbrMax)-$nbrMax;
				for ($i=$from; $i < $to; $i++) { 
					$return[] = $tmp[$i];
				}
				return $return;
			}
		}else {
			return false;
		}
	}
	public function openTopic($topic) {
		if($s = @file_get_contents(MU_THREAD.$this->whichDir($topic).'.dat')) return unserialize($s);
		else return false;
	}
	public function lastSort() {
		$tmp = array();
		$keys = array_keys($this->topics);
		rsort($keys);
		foreach ($keys as $k => $v) {
			$tmp[$v] = $this->topics[$v];
		}
		$this->topics = $tmp;
	}
}
/**
 * CLASSES DE GESTION DES THREADS
 */
class Threads extends SaveObj {

	private $categories = array('main'=>array(),'sub'=>array());
	private $catPositions = array('main'=>array(),'sub'=>array(),'stat'=>array('main'=>0,'sub'=>0));
	private $forumPositions = array();
 	public $forum;

	public function __construct() {
		parent::__construct();
		$this->name = MU_THREAD.'threads.dat';
		if (is_file(MU_THREAD.'threads.dat')){
			$t = unserialize(file_get_contents(MU_THREAD.'threads.dat'));
			if ($this->categories !== null){
				$this->categories = $t->categories;
			}
			if ($this->forumPositions !== null){
				$this->forumPositions = $t->forumPositions;
			}
			if ($this->catPositions !== null){
				$this->catPositions = $t->catPositions;
			}
			if ($this->forum !== null){
				$this->forum = $t->forum;
			} else {
				$this->forum = new Forum();
			}
		}
	}
	public function getCategories($type='all') {
		if($type == 'all') {return $this->categories;}
		else{if(array_key_exists($type, $this->categories)) {
			return $this->categories[$type];
		}}
	}
	public function delThread($thread,$subcat) {
		$stat = new Stat();
		if (isset($this->categories['sub'][$thread][$subcat])) {
			unset($this->categories['sub'][$thread][$subcat]);
		}
		foreach($this->catPositions['sub'][$thread] as $key => $value) {
			if ($value == $subcat) {
				unset($this->catPositions['sub'][$thread][$key]);
				break;
			}
		}
		foreach ($this->forumPositions[$subcat] as $key => $forum) {
			if (is_file(MU_THREAD.$this->whichDir($forum).'.dat')) {unlink(MU_THREAD.$this->whichDir($forum).'.dat');}
			unset($this->forum->topics[$forum]);
			$stat->updateStats('topics',-1);
		}
		unset($this->forumPositions[$subcat]);
		$stat->updateStats('threads',-1);
		//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
		$this->forum = null;
		$this->SaveObj($this);
		return true;
	}
	public function removeTopicFromThreads($topic) {
		foreach ($this->forumPositions as $cat => $array) {
			if (in_array($topic, $array)) {
				foreach ($array as $key => $top) {
					if($top = $topic) {
						unset($this->forumPositions[$cat][$key]);
					}
				}
			}
		}
		//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
		$this->forum = null;
		$this->SaveObj($this);
	}
	public function getMainCats() {
		return $this->categories['main'];
	}
	public function getMainCatsPositions() {
		return array_flip($this->catPositions['main']);
	}
	public function getCats() {
		return $this->forumPositions;
	}
	public function getSubCatsPositions() {
		return $this->catPositions['sub'];
	}
	public function getallThreads($cat) {
		if ($cat != 'all' && array_key_exists($cat, $this->categories)) {
			return $this->categories['sub'][$cat];
		} else {
			return $this->categories['sub'];
		}
	}
	public function addMainCat($name) {
		$id = $this->nextMainCat();
		$this->categories['main'][$id] = $name;
		$this->catPositions['main'][] = $id;
		//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
		$this->forum = null;
		$this->SaveObj($this);
	}
	public function updateMainCat($name,$position,$oldposition,$id) {
		if(isset($this->categories['main'][$id])){
			$this->categories['main'][$id] = $name;
			if ($oldposition != $position) {
				if ($position == '' || $position <  0) {$position = 0;}
				if ($position > count($this->catPositions['main'])-1) {$position = count($this->catPositions['main']);}
				if(array_key_exists($position,$this->catPositions['main'])) {
					$tmp = $this->catPositions['main'][$position];
				}
				unset($this->catPositions['main'][$oldposition]);
				$this->catPositions['main'][$position] = $id;
				if (isset($tmp)) {
					$this->catPositions['main'][$oldposition] = $tmp;
				}
				ksort($this->catPositions['main']);
				$this->catPositions['main'] = array_values($this->catPositions['main']);
			}
			//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
			$this->forum = null;
			$this->SaveObj($this);
			return true;
		} else {
			return false;
		}
	}
	private function nextMainCat() {
		if($this->catPositions['stat']['main'] != 0) {
			$this->catPositions['stat']['main']++;
			return str_pad($this->catPositions['stat']['main'],3, '0', STR_PAD_LEFT);
		} else {
			$this->catPositions['stat']['main']++;
			return '001';
		}
	}
	public function addCat($name,$subtitle,$maincat) {
		$id = $this->nextCat();
		if (array_key_exists($maincat, $this->categories['main'])) {
			$this->categories['sub'][$maincat][$id] = array(
				'cat' => $name,
				'subtitle' => $subtitle
			);
			$this->catPositions['sub'][$maincat][] = $id;
			$this->forumPositions[$id] = array();
			//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
			$this->forum = null;
			$this->SaveObj($this);
			$stat = new Stat();
			$stat->updateStats('threads',1);
			return true;
		} else {
			return false;
		}
		
	}
	public function updateCat($name,$subtitle,$maincat,$position,$oldposition,$id,$oldmainid) {
		if (!isset($this->forumPositions[$id])) {$this->forumPositions[$id] = array();}
		if ($position == '' || $position <  0) {$position = 0;}
		if (array_key_exists($maincat, $this->categories['main'])) {
			//On déplace le forum
			if (isset($this->categories['sub'][$maincat][$id]) && $maincat != $oldmainid) {
				$tmp = $this->categories['sub'][$maincat][$id];
			}
			unset($this->categories['sub'][$oldmainid][$id]);
			$this->categories['sub'][$maincat][$id] = array(
				'cat' => $name,
				'subtitle' => $subtitle
			);
			if (isset($tmp) && $maincat != $oldmainid) {
				$this->categories['sub'][$oldmainid][$id] = $tmp;
			}
			//On lui attribue la nouvelle position
			//La catégorie principale est la même que précédemment
			if ($oldmainid == $maincat){
				if ($position > count($this->catPositions['sub'][$maincat])-1) {$position = count($this->catPositions['sub'][$maincat]);}
				if(array_key_exists($position,$this->catPositions['sub'][$maincat])) {
					$tmp = $this->catPositions['sub'][$maincat][$position];
				}
				$atmp = array_flip($this->catPositions['sub'][$oldmainid]);
				unset($this->catPositions['sub'][$oldmainid][$atmp[$id]]);
				$this->catPositions['sub'][$maincat][$position] = $id;
				if (isset($tmp)) {
					$this->catPositions['sub'][$oldmainid][$atmp[$id]] = $tmp;
				}
			}else {//La catégorie principale est différente
				if ($position > count($this->catPositions['sub'][$maincat])-1) {$position = count($this->catPositions['sub'][$maincat]);}
				if (isset($this->catPositions['sub'][$maincat]) && array_key_exists($position,$this->catPositions['sub'][$maincat])) {
					$tmp = $this->catPositions['sub'][$maincat][$position];
				}
				$atmp = array_flip($this->catPositions['sub'][$oldmainid]);
				unset($this->catPositions['sub'][$oldmainid][$atmp[$id]]);
				$this->catPositions['sub'][$maincat][$position] = $id;
				if (isset($tmp)) {
					$this->catPositions['sub'][$maincat][$oldmainid] = $tmp;
				}
			}
			foreach ($this->catPositions['sub'] as $main => $cat) {
				ksort($this->catPositions['sub'][$main]);
				$this->catPositions['sub'][$main] = array_values($this->catPositions['sub'][$main]);
			}
			//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
			$this->forum = null;
			$this->SaveObj($this);
			return true;
		} else {
			return false;
		}
	}
	private function nextCat() {
		if($this->catPositions['stat']['sub'] != 0) {
			$this->catPositions['stat']['sub']++;
			return str_pad($this->catPositions['stat']['sub'],3, '0', STR_PAD_LEFT);
		} else {
			$this->catPositions['stat']['sub']++;
			return '001';
		}
	}
	public function getTitleCat($mainAndcat) {
		return $this->categories['sub'][substr($mainAndcat,0,3)][substr($mainAndcat,-3)]['cat'];
	}
	public function addForum($cat,$forum) {
		$this->forumPositions[$cat][] = $forum;
		//On supprime l'objet forum pour ne pas le stoquer dans le fichier de threads
		$this->forum = null;
		$this->saveObj($this);
	}
}
/**
*
* CLASSE DE GESTION DES DISCUSSIONS
*/
class Topic extends SaveObj {
	public $title;
	public $time;
	public $auth;
	public $name;
	public $type=false;
	public $reply=array();
	public $pos=0;
	public $infos;

	private $threads;

	public function __construct($main,$cat,$auth,$title,$content,$attach='',$type=false) {
		parent::__construct();
		$this->threads = unserialize(file_get_contents(MU_THREAD.'threads.dat'));
		$mainCats = $this->threads->getMainCats();
		$cats = $this->threads->getCats();
		if (array_key_exists($main, $mainCats) && array_key_exists($cat, $cats)) {
			$this->time=time();
			$this->mkdirThread($this->time);
			$this->name=MU_THREAD.$this->whichDir($this->time).'.dat';
			$this->addReply($auth,$content,$this->time,$attach);
			$this->title=Tools::clean($title);
			$this->type=$type;
			$this->auth=$auth;
			$this->threads->addForum($cat,$this->time);
			unset($this->threads);
			$stat = new Stat();
			$stat->updateStats('topics',1);
			$this->saveObj($this);
		} else {
			return false;
		}
	}
	public function removeTopic() {
		unlink($this->name);
		$stat = new Stat();
		$stat->updateStats('topics',-1);
	}
	public function updateTopic($time,$title,$auth,$post,$last,$ltime,$attach,$type) {
		$this->name = MU_THREAD.$this->whichDir($time).'.dat';
		if (!isset($this->topics[$time])) {$this->topics[$time] = new stdClass;}
		$this->topics[$time]->title = $title;
		$this->topics[$time]->auth = $auth;
		$this->topics[$time]->time = $ltime;
		$this->topics[$time]->attach = $attach;
		$this->topics[$time]->type = $type;
		$this->topics[$time]->post = htmlentities(strip_tags($post),ENT_NOQUOTES,'UTF-8');
		$this->topics[$time]->last = $last;
		$this->topics[$time]->attach = $attach;
		$this->saveObj($this);
	}
	public function addReply($auth,$content,$time,$attach='') {
		$name = $this->name;
		$id = count($this->reply);
		if ($id == -1) {$id = 0;}
		if (!isset($this->reply[$id])) {$this->reply[$id] = new stdClass;}
		$this->reply[$id]->auth =$auth;
		$this->reply[$id]->time =$time;
		$this->reply[$id]->content = htmlentities(strip_tags($content),ENT_NOQUOTES,'UTF-8');
		$this->reply[$id]->attach =$attach;
		$this->saveObj($this);
		$stat = new Stat();
		$stat->updateStats('msg',1);
		return $this->getlastReply();
	}
	public function removeReply($id) {
		$this->verifName($this,MU_THREAD);
		$tmp=array();
		foreach($this->reply as $r) if($r->time!=$id) $tmp[]=$r;
		$this->reply=$tmp;
		$this->infos->posts--;
		if($this->infos->posts<0) $this->infos->posts = 0;
		$this->saveObj($this);
		$stat = new Stat();
		$stat->updateStats('msg',-1);
	}
	public function getlastReply() {
		$this->verifName($this,MU_THREAD);
		return end($this->reply);
	}
	public function setReply($id,$title,$content,$attach='') {
		$this->verifName($this,MU_THREAD);
		if($title!='') $this->title=$title;
		foreach($this->reply as $k=>$r) { if($r->time==$id) {$this->reply[$k]->content=htmlentities($content,ENT_NOQUOTES,'UTF-8');}}
		$this->saveObj($this);
	}
	public function getReply($id) {
		$this->verifName($this,MU_THREAD);
		foreach($this->reply as $v) {
			if($v->time==$id) return $v;
		}
	}
	public function nextReply() {
		$this->verifName($this,MU_THREAD);
		if($this->pos<count($this->reply)){return $this->reply[$this->pos++];}
		else {$this->pos=0; return false;}
	}
	public function setTopicType($type) {
		$this->verifName($this,MU_THREAD);
		$this->type=$type;
		$this->saveObj($this);
	}
	public function setTopicTitle($title) {
		$this->verifName($this,MU_THREAD);
		$this->title=htmlentities(strip_tags($title),ENT_NOQUOTES,'UTF-8');
		$this->saveObj($this);
	}
	public function getInfo($type) {
		$this->verifName($this,MU_THREAD);
		$auths=array();
		$posts=0;
		$attach=0;
		$last=$this->getlastReply();
		foreach($this->reply as $v) {
			if(!in_array($v->auth,$auths)) $auths[]=$v->auth;
			if($v->attach!='') $attach=1;
			$posts++;
		}
		if($type) return array(count($auths),$auths);
		else {
			if (!isset($this->infos)) {$this->infos = new stdClass;}
			$this->infos->time = $this->time;
			$this->infos->title = $this->title;
			$this->infos->auth = $auths[0];
			$this->infos->posts = $posts;
			$this->infos->lastAuth = $last->auth;
			$this->infos->lastTime = $last->time;
			$this->infos->attach = $attach;
			$this->infos->type = $this->type;
			return $this->infos;//array($this->time,$this->title,$auths[0],$posts,$last->auth,$last->time,$attach,$this->type);
		}
	}
}
/**
*
* STATISTIQUES (Online)
*/
class Stat extends saveObj {
	public $name;
	public $conn=array();
	public $stats=array('threads'=>0,'topics'=>0,'msg'=>0);

	public function __construct() {
		parent::__construct();
		if (!is_file(MU_MEMBER.'connected.dat')) {
			$this->visit();
		}if (!is_file(MU_MEMBER.'stats.dat')) {
			$this->mkStats();
		}else {
			$t = unserialize(file_get_contents(MU_MEMBER.'stats.dat'));
			$this->conn = $t->conn;
			$this->stats = $t->stats;
		}
	}
	public function visit($id='') {
		$this->name = MU_MEMBER.'connected.dat';
		$this->stat[$_SERVER['REMOTE_ADDR']]=array('id'=>$id,'time'=>time());
		$this->saveObj($this);
	}
	public function updateVisit($id='') {
		$this->name = MU_MEMBER.'connected.dat';
		$r=$_SERVER['REMOTE_ADDR'];
		$connected=0;
		$mbConnected='';
		$this->stat[$r]=array('id'=>$id,'time'=>time());
		foreach($this->stat as $k=>$v) {
			if(((time()-$v['time'])>120) && $k!=$r) unset($this->stat[$k]);
			else {
				if($this->stat[$k]['id']!='') $mbConnected.=($r==$k)?$id.' ':'<a href="?private='.$this->stat[$k]['id'].'" rel="tooltip" title="'.SEND_PRIVATE_MSG.'">'.$this->stat[$k]['id'].'</a> ';
				else $connected++;
			}
		}
		$this->saveObj($this);
		return array('mbConnected'=>$mbConnected,'guestsConnected'=>$connected);
	}
	public function mkStats() {
		$this->name = MU_MEMBER.'stats.dat';
		$this->stats=array('threads'=>0,'topics'=>0,'msg'=>0);
		$this->saveObj($this);
	}
	public function updateStats($id='msg',$increment=1) {
		$this->name = MU_MEMBER.'stats.dat';
		if (array_key_exists($id, $this->stats)){
			$this->stats[$id] += $increment;
			$this->saveObj($this);
		} else {
			return $this->stats;
		}
	}
	public function getStats() {
			return unserialize(file_get_contents(MU_MEMBER.'stats.dat'));
	}
}
/**
*
* INSCRIPTION DES MESSAGES PRIVÉS DES MEMBRES
*/
class Messages extends saveObj {
	public $mess=array();
	public $name;
	public function __construct() {
		parent::__construct();
	}
	public function messages($name) {
		$this->name=MU_MEMBER.md5($name.SECURITY_SALT).'/'.$name.'.mp';
	}
	public function addMessage($from,$content,$mpTo) {
		$id = count($this->mess);
		if (!isset($this->mess[$id])) {$this->mess[$id] = new stdClass;}
		$this->mess[$id]->time = time();
		$this->mess[$id]->from = $from;
		$this->mess[$id]->content = $content;
		$this->mess[$id]->to = $mpTo;
		$this->saveMsgObj($this);
	}
	public function getMessage() {
		return $this->mess;
	}
}

# ---------------- FONCTION DE BANNISSEMENT ---------------
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# @update     2013-10-26 Cyril MAGUIRE
# Copyright (c) 2013 SebSauvage
# See http://sebsauvage.net/paste/?36dbd6c6be607e0c#M5uR8ixXo5rXBpXx32gOATLraHPffhBJEeqiDl1dMhs
#
# Instructions d'utilisation:
# • Faites un require_once de ce script.
# • Initialisez une instance de la classe $Ban = new BanYourAss(); en début de script
# • à l'endroit où vous testez la validité du mot de passe:
#     • Si $Ban->ban_canLogin()==false, l'utilisateur est banni. Ne testez même pas le mot de passe: Rejetez l'utilisateur.
#    • Si $Ban->ban_canLogin()==true, vérifiez le mot de passe.
#          • Si le mot de passe est ok, appelez $Ban->ban_loginOk(), sinon appelez $Ban->ban_loginFailed()
# La lib s'occupe de compter le nombre d'échecs et de gérer la durée de bannissement 
# (bannissement/levée de ban).
# Cette lib créé un sous-répertoire "data" qui contient les données de bannissement 
# (ipbans.php) et un log de connexion (log.txt).
#
# Exemple
#        $Ban = new BanYourAss();
#        if (!$Ban->ban_canLogin()) { $pass=false; }
#        if($pass){ $Ban->ban_loginOk(); echo connect("success",array("username"=>$this->username)); }
#        else{ $Ban->ban_loginFailed(); echo connect("error","Incorrect Username or Password"); }
# ------------------- END LICENSE BLOCK -------------------

class BanYourAss {

	private $ipbans;
	private $DATABANDIR;
	private $IPBANS_FILENAME='';
	private $BAN_AFTER = 3;// Ban IP after this many failures.
	private $BAN_DURATION = 1800; // Ban duration for IP address after login failures (in seconds) (1800 sec. = 30 minutes)

	// ------------------------------------------------------------------------------------------
	// Brute force protection system
	// Several consecutive failed logins will ban the IP address for 30 minutes.

	public function __construct() {
		if (!defined('MU_ROOT')) {
			setlocale(LC_TIME, 'fr_FR.utf8','fra');
			date_default_timezone_set('Europe/Paris');
			if (!defined('MSG_COME_BACK_IN')) {define('MSG_COME_BACK_IN','Revenez nous voir dans');}
			if (!defined('MSG_MIN_OR_NOT')) {define('MSG_MIN_OR_NOT','minutes ou pas...');}
			if (!defined('MSG_IF_NOT_SPAMMER')) {define('MSG_IF_NOT_SPAMMER','Si vous n\'êtes pas un robot');}
			if (!defined('CLICK_HERE')) {define('CLICK_HERE','cliquez ici');}
			if (!defined('SECURITY_SALT')) {define('SECURITY_SALT','DSKQJfmi879fdiznKSDJ56SD8734QRer980ZOIDQ');}
			$this->DATABANDIR = 'ban'; // Data subdirectory
		} else {
			if (defined('MU_DATA') && is_dir(substr(MU_DATA,0,-1))) {
				$this->DATABANDIR = MU_DATA.'ban'; // Data subdirectory
			}
		}
		$this->IPBANS_FILENAME = $this->DATABANDIR.'/ipbans.php'; // File storage for failures and bans.

		if (!is_dir($this->DATABANDIR)) { mkdir($this->DATABANDIR,0705); chmod($this->DATABANDIR,0705); }
		if (!is_file($this->DATABANDIR.'/.htaccess')) { file_put_contents($this->DATABANDIR.'/.htaccess',"Allow from none\nDeny from all\n"); } // Protect data files.
		if (!is_file($this->IPBANS_FILENAME)) {
			file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export(array('FAILURES'=>array(),'BANS'=>array(),'NOTSPAM'=>array()),true).";\n?>");
		}
		include $this->IPBANS_FILENAME;
		$this->ipbans = $IPBANS;
	}

	private function logm($message) {
	    $t = strval(date('Y/m/d_H:i:s')).' - '.$_SERVER["REMOTE_ADDR"].' - '.strval($message)."\n";
	    file_put_contents($this->DATABANDIR.'/log.txt',$t,FILE_APPEND);
	}
	public function notSpamCode() {
		if (is_file($this->IPBANS_FILENAME)) {include $this->IPBANS_FILENAME;}
		return (isset($this->ipbans['NOTSPAM'][$_SERVER['REMOTE_ADDR']]) && !empty($this->ipbans['NOTSPAM'][$_SERVER['REMOTE_ADDR']])) ? $this->ipbans['NOTSPAM'][$_SERVER['REMOTE_ADDR']]:false;
	}
	// Signal a failed login. Will ban the IP if too many failures:
	public function ban_loginFailed() {
	    $ip=$_SERVER["REMOTE_ADDR"]; 
	    $gb=$this->ipbans;
	    if (!isset($gb['FAILURES'][$ip])) {$gb['FAILURES'][$ip]=0;}
	    $gb['FAILURES'][$ip]++;
	    if ($gb['FAILURES'][$ip]>($this->BAN_AFTER-1))
	    {
	    	$notSpamCode = base64_encode($ip.time().SECURITY_SALT);
	        $gb['BANS'][$ip]=time()+$this->BAN_DURATION;
	    	if (!isset($gb['NOTSPAM'][$ip])) {$gb['NOTSPAM'][$ip]=$notSpamCode;}
	    	if (empty($gb['NOTSPAM'][$ip])) {$gb['NOTSPAM'][$ip]=$notSpamCode;}
	        $this->logm('IP address banned from login');
	    	file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export($gb,true).";\n?>");
	        echo MSG_COME_BACK_IN.'&nbsp;'.($this->BAN_DURATION/60).'&nbsp;'.MSG_MIN_OR_NOT;
			echo MSG_IF_NOT_SPAMMER.'<a href=index.php?notspam='.$notSpamCode.'>&nbsp;'.CLICK_HERE.'</a>';
			exit();
	    }
	    file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export($gb,true).";\n?>");
	}

	// Signals a successful login. Resets failed login counter.
	public function ban_loginOk() {
	    $ip=$_SERVER["REMOTE_ADDR"]; 
	    $gb=$this->ipbans;
	    unset($gb['FAILURES'][$ip]); unset($gb['BANS'][$ip]);unset($gb['NOTSPAM'][$ip]);
	    file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export($gb,true).";\n?>");
	    $this->logm('Login ok.');
	}

	// Checks if the user CAN login. If 'true', the user can try to login.
	public function ban_canLogin() {
	    $ip=$_SERVER["REMOTE_ADDR"]; 
	    $gb=$this->ipbans;
	    if (isset($gb['BANS'][$ip]))
	    {
	        // User is banned. Check if the ban has expired:
	        if ($gb['BANS'][$ip]<=time())
	        { // Ban expired, user can try to login again.
	            $this->logm('Ban lifted.');
	            unset($gb['FAILURES'][$ip]); unset($gb['BANS'][$ip]);unset($gb['NOTSPAM'][$ip]);
	            file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export($gb,true).";\n?>");
	            return true; // Ban has expired, user can login.
	        }
	        return false; // User is banned.
	    }
	    return true; // User is not banned.
	}

	public function liftBan($ip) {
		$gb = $this->ipbans;
		if (isset($gb['BANS'][$ip]))
   		{
            $this->logm('Ban lifted.');
            unset($gb['FAILURES'][$ip]); unset($gb['BANS'][$ip]);unset($gb['NOTSPAM'][$ip]);
            file_put_contents($this->IPBANS_FILENAME, "<?php\n\$IPBANS=".var_export($gb,true).";\n?>");
            return true; // Ban has expired, user can login.
	   	}
	}
}
# ------------ FIN FONCTION DE BANNISSEMENT -----------
/**
* Initialisation du forum
*/
class Init {
	public $MainError;
	public $errors;
	public $colors;
	public $cNames=array();
	public $theme = 'default';

	public $captcha;
	public $stat;
	public $session=null;
	public $gzip=0;

	public $uforum='[b]&micro;[/b]Forum';
	public $lang=LANG;
	public $metaDesc='Lightweight bulletin board without sql';
	public $nbrMsgIndex=15;
	public $nbMsgTopic=15;
	public $nbrMb=30;
	public $extensionsAutorises='gif,bmp,png,jpg,mp3,zip,rar,txt';
	public $extStr;
	public $maxAvatarSize=30720;
	public $forumMode=0;
	public $quoteMode=1;
	public $siteUrl=MU_BASE_URL;
	public $siteName='&micro;Forum';
	public $subtitle='Le Forum sans BDD';

	public $isMember;
	public $isAdmin;
	public $isOwner;
	public $cPass;
	public $cLogin;
	public $cStyle;
	public $haveMP; //Messages privés
	public $threads;
	public $thread;
	public $topicObj;
	public $topic;
	public $quote=null;
	public $showform=false;

	public $pages=1; //Nombre de pages totales de Topics 
	public $pagesMb=1; //Nombre de pages totales de Membres
	public $page=1; //Page en cours
	public $active='home';
	public $get_editpost;
	public $get_conf;
	public $get_topics;
	public $get_topic;
	public $get_memberlist;
	public $get_editprofil;
	public $get_private;
	public $get_restore;
	public $get_viewrules;
	public $showAll;
	public $searchMember;
	
	protected function __construct() {

		if (is_null($this->session)){ $this->session = new Session(); }

		if (is_file('config.php')) {
			require 'config.php';
			$this->uforum = $uforum;
			$this->lang = $lang;
			$this->metaDesc = $metaDesc;
			$this->nbrMsgIndex = abs($nbrMsgIndex);
			$this->nbMsgTopic = abs($nbMsgTopic);
			$this->nbrMb = abs($nbrMb);
			$this->extensionsAutorises = $extensionsAutorises;
			$this->maxAvatarSize = $maxAvatarSize;
			$this->forumMode = $forumMode;
			$this->quoteMode = $quoteMode;
			$this->siteUrl = $siteUrl;
			$this->siteName = $siteName;
			$this->siteBase = $siteBase;
			$this->subtitle = $subtitle;
			$this->theme = $theme;
			$this->gzip = $gzip;
		}

		if(!is_file('version') || file_get_contents('version')!=VERSION) {
			file_put_contents('version', VERSION);
			$this->mkDefaultTheme();
			if(!$this->mkressources()) {
				if (is_file('config.php')){include('config.php');}
				$config="<?php\n";
				$config.=TM."\n";
				$config.="\$uforum='".$this->uforum."';\n";
				$config.="\$lang='".$this->lang."';\n";
				$config.="\$metaDesc='".$this->metaDesc."';\n";
				$config.="\$nbrMsgIndex='".abs($this->nbrMsgIndex)."';\n";
				$config.="\$nbMsgTopic='".abs($this->nbMsgTopic)."';\n";
				$config.="\$nbrMb='".abs($this->nbrMb)."';\n";
				$config.="\$extensionsAutorises='".$this->extensionsAutorises."';\n";
				$config.="\$maxAvatarSize='".$this->maxAvatarSize."';\n";
				$config.="\$forumMode='".$this->forumMode."';\n";
				$config.="\$quoteMode='".$this->quoteMode."';\n";
				$config.="\$siteUrl='".$this->siteUrl."';\n";
				$config.="\$siteName='".$this->siteName."';\n";
				$config.="\$subtitle='".$this->subtitle."';\n";
				$config.="\$theme='".$this->theme."';\n";
				$config.="\$gzip='".$this->gzip."';\n";
				$config.="\$siteBase='".MU_BASE_URL."';\n?>";
				file_put_contents('config.php', utf8_encode($config));
			}
			$this->mkhtaccess();
			$this->mkjs();
			$this->mkcss();
			if(@copy('index.php','index.bak')) {
				unlink('index.php');
				rename('index.bak','index.php');
			}
		}
		if (!is_file(MU_MEMBER.'members.dat')) {
			$this->mkressources();
		}
		Tools::loadlang($this->lang);
		$this->members = new Members();
		$this->threads = new Threads();
		$this->captcha = new Captcha(LANG,$this->session);
		$this->stat = new Stat();
		$s = file_get_contents(MU_MEMBER.'connected.dat');
		$this->stat = unserialize($s);
		$Ban = new BanYourAss();

		/**
		*
		* GET & POST
		*/
		$gets=array('thread','topic','action','logout','memberlist','login','password','editprofil','email','birthday','site','signature','titre','message','threadID','topicID','postID','deluser','delfile','switchuser','thread','delforum','delpost','editpost','style','theme','gzip','private','delprivate','mpTo','backup','restore','read','conf','uftitle','nbmess','nbmessTopic','nbmb','maxav','exts','fmode','anonymous','qmode','postit','ufsite','uflang','ufsitename','ufsubtitle','ufmetadesc','rc','ntitle','pid','wco','register','page','searchMember','qid','ans','notspam','replypost','newcat','newcatsubtitle','editcat','editcatsubtitle','editcatposition','newmaincat','newcat','newcatsubtitle','newcatmaincat','editmaincat','editmaincatposition','editcat','editcatsubtitle','editcatposition','editcatmaincat','position','catid','maincatid','oldposition','id','viewforum','rules','viewrules');

		foreach($gets as $o) {
			$$o=(isset($_GET[$o]) && is_string($_GET[$o]))?$_GET[$o]:'';
			if(!$$o) $$o=(isset($_POST[$o]) && is_string($_POST[$o]))?$_POST[$o]:'';
		}

		if (isset($notspam) && $notspam == $Ban->notSpamCode() && $Ban->notSpamCode() !== false) {
			$Ban->liftBan($_SERVER['REMOTE_ADDR']);
			$this->delCookies();
			header('location:index.php');
			exit();
		}

		if(isset($page) && $page != ''){ $this->page = $page;} else {$this->page = 1;}

		if(isset($showall)){ $this->showAll = $showall;}

		if(isset($searchMember)){ $this->searchMember = $searchMember;}

		$this->extStr=$this->extensionsAutorises;
		$this->extensionsAutorises= '/.'.str_replace(",","$|.",$this->extensionsAutorises).'$/i';
		// Chargement d'une pièce jointe
		if($pid) {
			$pid = base64_decode($pid);
			if(count($pid_name=explode('/',$pid))>2) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/force-download');
				header('Content-Length: ' .filesize($pid));
				header('Content-Disposition: attachment; filename='.basename($pid));
				@readfile($pid);
			} else {
				$this->session->setMsg(ERROR_FILE_UNKNOWN,'error');
				header('location: index.php');
				exit();
			}
		}
		if($rc) {
			$this->session->setMsg(base64_decode($rc),'error');
			header('location: index.php');
			exit();
		}
		//Première connexion et premier utilisateur validés
		if($wco) {
			header('location: index.php');
			exit();
		}
		$this->cPass=(isset($_COOKIE['CookiePassword']))?$_COOKIE['CookiePassword']:'';
		$this->cLogin=(isset($_COOKIE['CookieLogin']))?base64_decode($_COOKIE['CookieLogin']):'';
		$this->cStyle=(isset($_COOKIE['CookieStyle']))?$_COOKIE['CookieStyle']:'Defaut';
		$this->loginForCookie = str_replace(array("=",",",";","\t","\r","\n","\013","\014"),'',base64_encode($this->cLogin));
		if($topic && !$editpost && !empty($this->loginForCookie)) setCookie('uFread'.$topic.$this->loginForCookie,1,time()+2592000);

		/**
		*
		* TEST DU MOT DE PASSE
		*/
		if (!empty($this->cLogin) && !empty($this->cPass)) {
			list($this->isMember,$goodpass,$this->isAdmin)=$this->members->checkMember($this->cLogin,$this->cPass);
			$this->haveMP=@file_exists(MU_MEMBER.md5($this->cLogin.SECURITY_SALT).'/'.$this->cLogin.'.mp');
			if (!$Ban->ban_canLogin()) { 
				$Ban->ban_loginFailed(); 
				$this->session->setMsg(L_ERR_WRONG_PASSWORD,'error');
				$this->isMember=0;$this->isAdmin=0;$this->isOwner=0;
				header('Location: index.php');
				exit();
			} else {
				if(!$this->isMember || !$goodpass) {
					$Ban->ban_loginFailed(); 
					if(!$goodpass) $this->errors .= ERROR_WRONG_PASSWORD.' '.$this->cLogin.' !<br>';
					if(!$this->isMember) $this->errors .= BECAREFUL.' '.$this->cLogin.' '.CASE_SENSITIVE.'<br>';
					$this->isMember=0;
					$this->isAdmin=0;
					$this->session->setMsg($this->errors,'error');
					$this->delCookies();
					header('Location: index.php');
					exit();
				} else {
					$Ban->ban_loginOk();
					if($this->isAdmin==2){$this->isAdmin=1;$this->isOwner=1;}	
				}
			}
		} else { $this->isMember=0;$this->isAdmin=0;$this->isOwner=0;}
		/**
		*
		* DÉCONNEXION
		*/
		if ($this->isMember && $logout) {
			$this->delCookies();
			header('Location: index.php');
			exit();
		}
		/**
		 * Choix du style
		 */
		if($style) { 
			setCookie('CookieStyle',$style,time()+(3600*24*30)); 
			$this->cStyle=$style; 
			$url = Tools::getURLParams();
			$url = substr($url,0,strpos($url, 'style='));
			if (in_array($url[strlen($url)-1],array('&','?'))) $url = substr($url,0,-1);
			header('Location: index.php'.$url);
			exit();
		}
		/**
		 * Suppression MP
		 */
		if($delprivate) { 
			unlink(MU_MEMBER.md5($this->cLogin.SECURITY_SALT).'/'.$this->cLogin.'.mp'); 
			$this->haveMP=0;
			$this->session->setMsg(PRIVATE_MSG_DEL);
			header('Location: index.php');
			exit();
		}
		if ($replypost) {
			if($topic && is_file(MU_THREAD.$this->whichDir($topic).'.dat')) {
				if($s = implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
					$this->topicObj = unserialize($s);
					$m = $this->topicObj->getReply($replypost);
					$m->content = preg_replace('!\[e\](.*)\[\/e\](\\r\\n)*!Ui','',$m->content);
					$this->quote = '[q='.$m->auth.']'.$m->content.'[/q]';
					$this->showform = true;
				}
			}
		}
		/**
		*
		* DIFFÉRENTES ACTIONS
		*/
		switch ($action) {
			case 'enter':
				if($Ban->ban_canLogin()) {
					setCookie('CookiePassword',md5($password),time()+(3600*24*30));
					setCookie('CookieLogin',base64_encode($login),time()+(3600*24*30));
					header('location:index.php');
					exit();
				}
				break;
			case 'newuser':
				if($qid) $this->captcha->actionBegin();
				// on nettoie le nom d'utilisateur
				$login = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $login);
				$login = Tools::clean($login);
				$avatar='';
				if(in_array($login,$this->members->listMember())) $this->errors .= ERROR_USER_ALREADY_EXISTS;
				else if($login != '' && $password != '' && $email != '' && $birthday != ''){
					$birth = explode('/',$birthday);
					if (!isset($birth[2])) {
						$this->errors .= ERROR_INVALID_BIRTHDAY;
					} else {
						if (!checkdate($birth[1], $birth[0], $birth[2])) {
							$this->errors .= ERROR_INVALID_BIRTHDAY;
						} else {	
							if((preg_match('/(^[0-9a-zA-Z_\.-]{1,}@[0-9a-zA-Z_\-]{1,}\.[0-9a-zA-Z_\-]{2,}$)/', $email)) && (strlen($login)<13)) {
								$memberDirUp = MU_UPLOAD.md5(SECURITY_SALT.$login);
								@mkdir($memberDirUp);
								$memberDir = MU_MEMBER.md5($login.SECURITY_SALT);
								@mkdir($memberDir);
								file_put_contents($memberDirUp.DS.'index.html', GOTO_INDEX);
								file_put_contents($memberDir.DS.'index.html', GOTO_INDEX);
								$avatar=$this->checkUpload($memberDirUp,1,$login);
								$this->members->addMember($login,$password,$email,Tools::clean($signature),$site,$birthday,$avatar);
								setCookie('CookiePassword', md5($password), time() + (3600 * 24 * 30));
								setCookie('CookieLogin', base64_encode($login), time() + (3600 * 24 * 30));
								header('Location: index.php?wco=true');
								exit();
							} else {
								$this->errors .= ERROR_INVALID_EMAIL;
							}
						}
					}
				} else {
					$this->errors .= ERROR_FILL_FIELDS;
				}
				if (!empty($this->errors)) {
					$this->session->setMsg($this->errors,'error');
					header('Location: index.php?register=true');
					exit();
				} else {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php');
					exit();
				}
				break;
			case 'editprofil':
				$memberDir = MU_UPLOAD.md5(SECURITY_SALT.$this->cLogin);
				if( preg_match('/(^[0-9a-zA-Z_\.-]{1,}@[0-9a-zA-Z_\-]{1,}\.[0-9a-zA-Z_\-]{2,}$)/', $email)) {
					$avatar=$this->checkUpload($memberDir,1);
					if($avatar && $this->errors!="") {
						header('Location: index.php?editprofil=1');
						exit();
					} else if(!$avatar) $avatar = "";
					$signature=Tools::clean($signature);
					$birth = explode('/',$birthday);
					if (!isset($birth[2])) {
						$this->errors .= ERROR_INVALID_BIRTHDAY;
					} else {
						if (!checkdate($birth[1], $birth[0], $birth[2])) {
							$this->errors .= ERROR_INVALID_BIRTHDAY;
						} else {
							$this->members->setMember($this->cLogin,$email,$signature,$site,$birthday,$avatar);
						}
					}
				} else { header('Location: index.php?editprofil=1'); exit(); }
				break;
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php');
					exit();
				}
			case 'newpost':
				if ($message !='' && $topicID != '' && ($this->isMember || !$this->forumMode)) {
					$anonymous=$anonymous?str_replace(array(" ", "\"", "'", "/", "&", "."), array("", "", "", "", "", ""), $anonymous):0;
					if(!$this->isMember && (!$anonymous || $anonymous=='')) {
						$this->errors .= ERROR_INVALID_PSEUDO;
					} else if ($this->members->isMember($anonymous)) {
					    $this->errors .= ERROR_PSEUDO_ALREADY_USED;
					} else {
						if($s = implode('', file(MU_THREAD.$this->whichDir($topicID).'.dat'))) {
							$this->tLogin=$this->cLogin?$this->cLogin:$anonymous;
							$this->topicObj = unserialize($s);
							$message = Tools::clean($message);
							$time = time();
							$this->topicObj->addReply($this->tLogin,$message,$time,$this->checkUpload(MU_UPLOAD.md5(SECURITY_SALT.$this->tLogin),0));
							//list($time,$title,$auth,$post,$last,$tlast,$attach,$postType)=
							$this->topicObj->getInfo(0);
							$this->topicObj->updateTopic($this->topicObj->infos->time,$ntitle,$this->topicObj->infos->auth,$this->topicObj->infos->posts,$this->topicObj->infos->lastAuth,$this->topicObj->infos->lastTime,$this->topicObj->infos->attach,$this->topicObj->infos->type);
							if($this->isMember) $this->members->setForumPost($this->cLogin);
							$this->page = ceil($this->topicObj->infos->posts/$this->nbMsgTopic);
							if(BBCHelper::verif($message,'viewforum='.$viewforum.'&topic='.$topicID.'&editpost='.$time.'&page='.$this->page,$this->session)){
								header('Location: ?viewforum='.$viewforum.'&topic='.$topicID.'&page='.$this->page);
								exit();
							}
						} else $this->errors .= ERROR_INVALID_TOPIC;
					}
				}
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php');
					exit();
				}
				break;
			case 'newtopic':
				if($titre!='' && $message!='' && ($this->isMember || !$this->forumMode)){
					if(!$this->isMember && !$anonymous) {
						$this->errors .= ERROR_EMPTY_PSEUDO;
					} else if ($this->members->isMember($anonymous)) {
					    $this->errors .= ERROR_PSEUDO_ALREADY_USED;
					} else {
						$this->tLogin=$this->cLogin?$this->cLogin:$anonymous;
						$postType=$postit?1:0;
						$message = Tools::clean($message);
						list($main,$cat) = explode('-', $viewforum);
						if ($this->topicObj = new Topic($main,$cat,$this->tLogin,$titre,$message,$this->checkUpload(MU_UPLOAD.md5(SECURITY_SALT.$this->tLogin),0),$postType)) {
							//list($time,$title,$auth,$posts,$lastAuth,$tlastTime,$attach,$type)=
							$this->topicObj->getInfo(0);
							$this->members->addTopic($this->topicObj->infos->auth);
							$this->topic=$this->topicObj->infos->time;
							setCookie('uFread'.$this->topic.$this->loginForCookie,1,time()+2592000);
							$this->session->setMsg(MSG_DATA_REC);
							header('Location: index.php?viewforum='.$viewforum.'#msgFlash');
							exit();
						} else {
							$this->MainError = 404;
							break;
						}
						
					}
				}
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php');
					exit();
				}
				break;
			case 'mp':
				$memberDir = MU_MEMBER.md5($mpTo.SECURITY_SALT);
				if(file_exists($memberDir.'/'.$mpTo.'.mp')) {
					$s=implode('', file($memberDir.'/'.$mpTo.'.mp'));
					$mpObj=unserialize($s);
				}
				else $mpObj= new Messages($mpTo);
				if($anonymous) $mpObj->addMessage($anonymous.' ('.$_SERVER['REMOTE_ADDR'].')',Tools::clean($message),$mpTo);
				else if(!$this->isMember) $this->errors.=ERROR_EMPTY_PSEUDO;
				else $mpObj->addMessage($this->cLogin,Tools::clean($message),$mpTo);
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_PRIVATE_REC);
					header('Location: index.php');
					exit();
				}
				break;
			case 'editoption':
				$tmp=$this->checkUpload('upload',0);
				if(($uftitle!=$this->uforum) || ($tmp)) {
					if(file_exists($this->uforum)) unlink($this->uforum);
					$this->uforum=$tmp?$tmp:$uftitle;
				}
				$this->nbrMsgIndex=$nbmess?abs($nbmess):abs($this->nbrMsgIndex);
				$this->nbMsgTopic=$nbmessTopic?abs($nbmessTopic):abs($this->nbMsgTopic);
				$this->nbrMb=$nbmb?abs($nbmb):abs($this->nbrMb);
				$this->extStr=$exts?$exts:$this->extStr;
				$this->maxAvatarSize=$maxav?($maxav*1024):$this->maxAvatarSize;
				$this->forumMode=($fmode=='on')?1:0;
				$this->quoteMode=($qmode=='on')?1:0;
				$this->siteUrl=$ufsite?$ufsite:'';
				$this->lang=$uflang?$uflang:'fr';
				$this->metaDesc=$ufmetadesc?$ufmetadesc:'';
				$this->siteName=$ufsitename?$ufsitename:'';
				$this->subtitle=$ufsubtitle?$ufsubtitle:'';
				$this->theme=$theme?$theme:'default';
				$this->gzip=$gzip?$gzip:false;
				$config ="<?\n"; 
				$config .=TM."\n";
				$config .="\$uforum='".$this->uforum."';\n";
				$config .="\$lang='".$this->lang."';\n";
				$config .="\$metaDesc='".$this->metaDesc."';\n";
				$config .="\$nbrMsgIndex='".abs($this->nbrMsgIndex)."';\n";
				$config .="\$nbMsgTopic='".abs($this->nbMsgTopic)."';\n";
				$config .="\$nbrMb='".abs($this->nbrMb)."';\n";
				$config .="\$extensionsAutorises='".$this->extStr."';\n";
				$config .="\$maxAvatarSize='".$this->maxAvatarSize."';\n";
				$config .="\$forumMode='".$this->forumMode."';\n";
				$config .="\$quoteMode='".$this->quoteMode."';\n";
				$config .="\$siteUrl='".$this->siteUrl."';\n";
				$config .="\$siteName='".$this->siteName."';\n";
				$config .="\$subtitle='".$this->subtitle."';\n";
				$config .="\$theme='".$this->theme."';\n";
				$config .="\$gzip='".$this->gzip."';\n";
				$config .="\$siteBase='".MU_BASE_URL."'\n?>";
				file_put_contents('config.php', utf8_encode($config));
				if(empty($message) && file_exists(MU_THREAD.'welcome.txt')) @unlink(MU_THREAD.'welcome.txt');
				else {
					file_put_contents(MU_THREAD.'welcome.txt', stripslashes($message));
				}
				if(empty($rules) && file_exists(MU_THREAD.'rules.txt')) @unlink(MU_THREAD.'rules.txt');
				else {
					file_put_contents(MU_THREAD.'rules.txt', stripslashes($rules));
				}
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php?conf=1');
					exit();
				}	
			case 'newmainthread':
				if ($this->isAdmin) {
					if (!empty($newmaincat)) {
						$newmaincat = Tools::clean($newmaincat);
					} else {
						$this->session->setMsg(ERROR_CAT_TITLE,'error');
						header('Location: index.php#msgFlash');
						exit();
					}
					$this->threads->addMainCat($newmaincat);
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php#msgFlash');
					exit();
				}
			break;
			case 'newthread':
				if ($this->isAdmin) {
					if (!empty($newcat)) {
						$newcat = Tools::clean($newcat);
					} else {
						$this->session->setMsg(ERROR_CAT_TITLE,'error');
						header('Location: index.php#msgFlash');
						exit();
					}
					if (!is_numeric($newcatmaincat)) {
						$this->session->setMsg(ERROR_CAT_TYPE,'error');
						header('Location: index.php#msgFlash');
						exit();
					}
					$newcatsubtitle = Tools::clean($newcatsubtitle);
					if($this->threads->addCat($newcat,$newcatsubtitle,$newcatmaincat)) {
						$this->session->setMsg(MSG_DATA_REC);
						header('Location: index.php#msgFlash');
						exit();
					}
				}
			break;
			case 'editmainthread':
				if ($this->isAdmin) {
					if (!empty($editmaincat)) {
						$editmaincat = Tools::clean($editmaincat);
					} else {
						$this->session->setMsg(ERROR_CAT_TITLE,'error');
						header('Location: index.php#msgFlash');
						exit();
					}
					if($this->threads->updateMainCat($editmaincat,intval($editmaincatposition),$oldposition,$id)) {
						$this->session->setMsg(MSG_DATA_REC);
						header('Location: index.php#msgFlash');
						exit();
					}
				}
			break;
			case 'editthread':
				if ($this->isAdmin) {
					if (!empty($editcat)) {
						$editcat = Tools::clean($editcat);
					} else {
						$this->session->setMsg(ERROR_CAT_TITLE,'error');
						header('Location: index.php#msgFlash');
						exit();
					}
					$editcatsubtitle = Tools::clean($editcatsubtitle);
					if($this->threads->updateCat($editcat,$editcatsubtitle,$editcatmaincat,intval($editcatposition),$position,$catid,$maincatid)) {
						$this->session->setMsg(MSG_DATA_REC);
						header('Location: index.php#msgFlash');
						exit();
					}
				}
			break;
			case 'editpost':
				if($topicID && $action=='editpost' && $postID && $message!='' && is_file(MU_THREAD.$this->whichDir($topicID).'.dat')) {
					if($s = implode('', file(MU_THREAD.$this->whichDir($topicID).'.dat'))) {
						$message = Tools::clean($message);
						$message = preg_replace('!\[e\](.*)\[\/e\](\\r\\n)*!Ui','',$message);
						$message = $message.'[e]'.$this->cLogin.' le '.date('d/m/Y \&\a\g\r\a\v\e\; H:i',time()).'[/e]';
						$this->topicObj = unserialize($s);
						$this->topicObj->setReply($postID,'',$message);
						$this->topicObj->getInfo(0);
						$topic=$topicID;
						if(BBCHelper::verif($message,'viewforum='.$viewforum.'&topic='.$topicID.'&editpost='.$postID.'&page='.$this->page,$this->session)){
							header('Location: ?viewforum='.$viewforum.'&topic='.$topic.'&page='.$this->page.'#p-'.$postID);
							exit();
						}
					}
				}
			break;
		}// Fin du switch
		if (!empty($this->errors)) {
			$this->session->setMsg($this->errors,'error');
			header('Location: index.php');
			exit();
		}

		/**
		*
		* TÂCHES ADMIN
		*/
		if($topic && $ntitle) { 
			if($this->cLogin == $this->threads->forum->getPostAuth($topic)){
				$this->threads->forum->setTitle($topic,Tools::clean($ntitle)); 
			}
		}
		if($delfile) { 
			if($this->cLogin || $this->isAdmin){
				$file = base64_decode($delfile);
				$dir=MU_UPLOAD.md5(SECURITY_SALT.$this->cLogin).'/';
				if (is_file($dir.$file)) {
					unlink($dir.$file);
					$this->session->setMsg($file.' : '.MSG_FILE_DEL);
					header('Location: ?editprofil=1');
					exit();
				}
			}
		}
		if ($delpost && $this->cLogin && !$this->isAdmin) {
			if($topic==$delpost) {//Suppression de la totalité de la discussion
				$this->topicObj = unserialize($s);
				if ($this->threads->forum->topics[$topic]->auth == $this->cLogin) {
					if($this->threads->forum->delTopic($topic)) {
						$this->session->setMsg(MSG_DATA_DEL);
						header('Location: index.php?viewforum='.$viewforum.'#msgFlash');
						exit();
					}
				}
			} else {//Suppression d'un message
				if(is_file(MU_THREAD.$this->whichDir($topic).'.dat') && $s=implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
					$this->topicObj = unserialize($s);					
					$r=$this->topicObj->getReply($delpost);
					if ($r->auth == $this->cLogin) {
						@unlink($r->attach);
						$this->topicObj->removeReply($delpost);
						$this->topicObj->getInfo(0);
						if(ceil($this->topicObj->infos->posts/$this->nbMsgTopic) == 1 ) $this->page = 1;
						$this->session->setMsg(MSG_DATA_DEL);
						header('Location: ?viewforum='.$viewforum.'#msgFlash');
						exit();
					} else {
						header('Location: ?viewforum='.$viewforum.'&topic='.$topic.'&page='.$this->page);
						exit();
					}
				}
			}
		}
		if($this->isAdmin) {
			if($deluser) { $this->members->removeMember($deluser); }
			else if($switchuser) { $this->members->setMod($switchuser); }
			else if($topic && $postit && !$action) { $type=$postit=="on"?1:0; $this->threads->forum->setType($topic,$type); }
			else if($topic && $ntitle) { $this->threads->forum->setTitle($topic,Tools::clean($ntitle)); }
			else if($topicID && $action=='editpost' && $postID && $message!='' && is_file(MU_THREAD.$this->whichDir($topicID).'.dat')) {
				if($s = implode('', file(MU_THREAD.$this->whichDir($topicID).'.dat'))) {
					$message = Tools::clean($message);
					$message = preg_replace('!\[e\](.*)\[\/e\](\\r\\n)*!Ui','',$message);
					$message = $message.'[e]'.$this->cLogin.' le '.date('d/m/Y \&\a\g\r\a\v\e\; H:i',time()).'[/e]';
					$this->topicObj = unserialize($s);
					$this->topicObj->setReply($postID,'',$message);
					$this->topicObj->getInfo(0);
					$topic=$topicID;
					if(BBCHelper::verif($message,'viewforum='.$viewforum.'&topic='.$topicID.'&editpost='.$postID.'&page='.$this->page,$this->session)){
						header('Location: ?viewforum='.$viewforum.'&topic='.$topic.'&page='.$this->page.'#p-'.$postID);
						exit();
					}
				}
			}
			else if($topic && $delpost) {
				if($topic==$delpost) {//Suppression de la totalité de la discussion
					if($this->threads->forum->delTopic($topic)) {
						$this->session->setMsg(MSG_DATA_DEL);
						header('Location: index.php?viewforum='.$viewforum.'#msgFlash');
						exit();
					}
				} else {//Suppression d'un message
					if(is_file(MU_THREAD.$this->whichDir($topic).'.dat') && $s=implode('', file(MU_THREAD.$this->whichDir($topic).'.dat'))) {
						$this->topicObj = unserialize($s);
						$r=$this->topicObj->getReply($delpost);
						@unlink($r->attach);
						$this->topicObj->removeReply($delpost);
						//list($time,$title,$auth,$post,$last,$tlast,$attach,$postType)=
						$this->topicObj->getInfo(0);
						//$this->forum->updateTopic($this->topicObj->infos->time,$this->topicObj->infos->title,$this->topicObj->infos->auth,$this->topicObj->infos->posts,$this->topicObj->infos->lastAuth,$this->topicObj->infos->lastTime,$this->topicObj->infos->attach,$this->topicObj->infos->type);
						if(ceil($this->topicObj->infos->posts/$this->nbMsgTopic) == 1 ) $this->page = 1;
						$this->session->setMsg(MSG_DATA_DEL);
						header('Location: ?viewforum='.$viewforum.'&topic='.$topic.'&page='.$this->page.'#msgFlash');
						exit();
					}
				}
			}
			else if($thread && $delforum) {
				if($this->threads->delThread($thread,$delforum)) {
					$this->session->setMsg(MSG_DATA_DEL);
					header('Location: index.php#msgFlash');
					exit();
				}
			} 
			else if($backup) {$r=$this->do_backup(MU_DATA, 'backup/data_' . date('Y-m-d-h\hi\ms\s'). '.zip');}
			else if($restore && $action=='restore') {
				if(@is_uploaded_file($_FILES['backup']['tmp_name'])) {
					$r=$this->restore_forum($_FILES['backup']['tmp_name']);
				}
				$restore=0;
				header('Location: index.php');
				exit();
			}
		}

		$this->get_editpost   = $editpost;
		$this->get_conf       = $conf;
		$this->get_topics     = $viewforum;
		$this->get_topic      = $topic;
		$this->get_memberlist = $memberlist;
		$this->get_editprofil = $editprofil;
		$this->get_private    = $private	;
		$this->get_restore    = $restore;
		$this->get_viewrules    = $viewrules;

		if (is_object($this->threads)) {
			$stats = $this->pagesMsg = $this->getStat();
		} else {
			$stats = $this->pagesMsg = 0;
		}
		
		$this->pages = $stats['topics'];
		$this->pagesMb = $stats['members'];
	}
	public function getStat() {
		$tmp=0;
		$arr=array(0,"");
		$stats = $this->stat->getStats();
		foreach($this->members->members as $k=>$v) $arr=($v->time>$arr[0])?array($v->time,$k):$arr;
		return array('members'=>count($this->members->members),'lastMb'=>$arr[1],'threads'=>$stats->stats['threads'],'topics'=>$stats->stats['topics'],'messages'=>($stats->stats['msg']-$stats->stats['topics']));
	}
	public function whichDir($time) {
		return date('Ym', $time).DS.$time;
	}
	private function delCookies() {
		if( isset($_COOKIE['CookiePassword'])) {unset($_COOKIE['CookiePassword']);}
		if( isset($_COOKIE['CookieLogin']) ) { unset($_COOKIE['CookieLogin']);}
		setcookie('CookiePassword',NULL,-1);
		setcookie('CookieLogin',NULL,-1);
	}
	/**
	*
	* INSTALLATEUR
	*/
	private function mkressources() {
		if (!file_exists('config.php') || !file_exists(MU_MEMBER) || !file_exists(MU_MEMBER.'members.dat')) {
			$config="<?";
			$config.=TM."\n";
			$config.="\$uforum='[b]&micro;[/b]Forum';\n";
			$config.="\$lang='fr';";
			$config.="\$metaDesc='Lightweight bulletin board without sql';\n";
			$config.="\$nbrMsgIndex=15;\n";
			$config.="\$nbMsgTopic=15;\n";
			$config.="\$nbrMb=30;\n";
			$config.="\$extensionsAutorises='gif,bmp,png,jpg,mp3,zip,rar,txt';\n";
			$config.="\$maxAvatarSize=30720;\n";
			$config.="\$forumMode=1;\n";
			$config.="\$quoteMode=1;\n";
			$config.="\$siteUrl='".MU_BASE_URL."';\n";
			$config.="\$siteName='&micro;Forum';\n";
			$config.="\$theme='default';\n";
			$config.="\$subtitle='Le Forum sans BDD';\n";
			$config.="\$gzip='0';\n";
			$config.="\$siteBase='".MU_BASE_URL."'\n;?>";
			file_put_contents('config.php', utf8_encode($config));

			$errors='';
			$errors.= (is_dir(MU_THEMES))? sprintf("&#10004;&nbsp;".MKTHEME.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKTHEME." .\n");
			$errors.= (@mkdir(MU_THEMES.$this->theme.DS.MU_CSS))? sprintf("&#10004;&nbsp;".MKCSS.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKCSS." .\n");
	        $errors.= (is_file(MU_LANG.LANG.'.php'))? sprintf("&#10004;&nbsp;".MKLANG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKLANG.".\n");
	        $errors.= (@mkdir(MU_BACK))? sprintf("&#10004;&nbsp;".MKBAK.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKBAK.".\n");
	        $errors.= (@mkdir(MU_UPLOAD))? sprintf("&#10004;&nbsp;".MKUPL.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKUPL.".\n");
			$errors.= (@mkdir(MU_DATA))? sprintf("&#10004;&nbsp;".MKDATA.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKDATA.".\n");
			$errors.= (@mkdir(MU_DATA.DS.'ban'))? sprintf("&#10004;&nbsp;".MKBAN.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKBAN.".\n");
			$errors.= (@mkdir(MU_MEMBER))? sprintf("&#10004;&nbsp;".MKMBR.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKMBR.".\n");
			$errors.= (@mkdir(MU_THREAD))? sprintf("&#10004;&nbsp;".MKMSG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKMSG.".\n");
			$errors.= (@mkdir(MU_JS))? sprintf("&#10004;&nbsp;".MKJS.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKJS.".\n");
			$errors.= (@mkdir(MU_IMG))? sprintf("&#10004;&nbsp;".MKIMG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKIMG.".\n");

			if (!file_exists(MU_MEMBER.'members.dat')) {
				$this->members = new Members();
				@file_put_contents(MU_MEMBER.'members.dat', serialize($this->members),LOCK_EX);
			}

			$this->errors = $errors;

			$this->session->setMsg($this->errors,'error',false);
			return true;
		}
		return false;
	}
	private function mkDefaultTheme() {
		$return = false;
		if(!is_dir(MU_THEMES)) {
			mkdir(MU_THEMES);
		}
		if(!is_dir(MU_THEMES.'default')) {
			mkdir(MU_THEMES.'default');
			$return = true;
		}
		return $return;
	}
	/**
	*
	* CRÉATION DES FICHIERS .HTACCESS
	*/
	private function mkhtaccess() {
		$s = $_SERVER['SCRIPT_NAME'];
		$mainHt = TM."\n";
		$mainHt .= "Allow from none\n";
		$mainHt .= "Deny from all\n";
		$mainHt .= "<Files *.dat>\n";
		$mainHt .= "order allow,deny\n";
		$mainHt .= "deny from all\n";
		$mainHt .= "</Files>\n";
		$mainHt .= "options -indexes \n";
		file_put_contents(MU_DATA.'index.html', GOTO_INDEX);
		file_put_contents(MU_UPLOAD.'index.html', GOTO_INDEX);
		if(!file_exists(MU_DATA.'membres/.htaccess')) {
			file_put_contents(MU_DATA.'membres/.htaccess', $mainHt);
			file_put_contents(MU_DATA.'membres/index.html', GOTO_INDEX);
		}
		if(!file_exists(MU_DATA.'messages/.htaccess')) {
			file_put_contents(MU_DATA.'messages/.htaccess', $mainHt);
			file_put_contents(MU_DATA.'messages/index.html', GOTO_INDEX);
		}
		if(!file_exists(MU_BACK.'.htaccess')) {
			$mainHt = TM."\n";
			$mainHt .= "AddType application/octet-stream zip";
			$mainHt .= "options -indexes";
			file_put_contents(MU_BACK.'.htaccess', $mainHt);
			file_put_contents(MU_BACK.'index.html', GOTO_INDEX);
		}	
	}

	/**
	*
	* CRÉATION DES FICHIERS JS
	*/
	private function mkjs() {
		if(!file_exists(MU_JS.'scripts.js')) {
			$js = '//<![CDATA[
			var activeSub=0;var SubNum=0;var timerID=null;var timerOn=false;var timecount=300;var what=null;var newbrowser=true;var check=false;var layerRef="";var tm="";var confirmMsg="Confirmez la suppression de ";var msie=navigator.userAgent.toLowerCase().indexOf("msie")+1;wmtt=null;document.onmousemove=updateWMTT;function init(){if(document.layers){layerRef="document.layers";styleSwitch="";visibleVar="show";what="ns4"}else{if(document.all){layerRef="document.all";styleSwitch=".style";visibleVar="visible";what="ie"}else{if(document.getElementById){layerRef="document.getElementByID";styleSwitch=".style";visibleVar="visible";what="moz"}else{what="none";newbrowser=false}}}check=true}function switchLayer(a){if(check){if(what=="none"){return}else{if(what=="moz"){if(document.getElementById(a).style.visibility=="visible"){document.getElementById(a).style.visibility="hidden";document.getElementById(a).style.display="none"}else{document.getElementById(a).style.visibility="visible";document.getElementById(a).style.display="block"}}else{if(document.all[a].style.visibility=="visible"){document.all[a].style.visibility="hidden";document.all[a].style.display="none"}else{document.all[a].style.visibility="visible";document.all[a].style.display="block"}}}scrollTo(\'form\');}else{scrollTo(\'form\');return}}function countInstances(c,b){var a=document.formulaire.message.value.split(c);var d=document.formulaire.message.value.split(b);return a.length+d.length-2}function insert(e,c){var b=document.getElementById("message");if(document.selection){var g=document.selection.createRange().text;document.formulaire.message.focus();var d=document.selection.createRange();if(c!=""){if(g==""){var f=countInstances(e,c);if(f%2!=0){d.text=d.text+c}else{d.text=d.text+e}}else{d.text=e+d.text+c}}else{d.text=d.text+e}}else{if(b.selectionStart|b.selectionStart==0){if(b.selectionEnd>b.value.length){b.selectionEnd=b.value.length}var h=b.selectionStart;var a=b.selectionEnd+e.length;b.value=b.value.slice(0,h)+e+b.value.slice(h);b.value=b.value.slice(0,a)+c+b.value.slice(a);b.selectionStart=h+e.length;b.selectionEnd=a;b.focus()}else{var d=document.formulaire.message;var f=countInstances(e,c);if(f%2!=0&&c!=""){d.value=d.value+c}else{d.value=d.value+e}}}}function updateWMTT(a){if(document.documentElement.scrollTop&&msie){x=window.event.x+document.documentElement.scrollLeft+10;y=window.event.y+document.documentElement.scrollTop+10}else{x=(document.all)?window.event.x+document.body.scrollLeft+10:(a.pageX+10)+"px";y=(document.all)?window.event.y+document.body.scrollTop+10:(a.pageY+10)+"px"}if(wmtt!=null){wmtt.style.left=x;wmtt.style.top=y}}function showWMTT(a){wmtt=document.getElementById(a);wmtt.style.display="block"}function hideWMTT(){wmtt.style.display="none";wmtt=null}function quote(c,f){var a=document.getElementById("td"+f).innerHTML;var b=new Array("<fieldset.*?>.*?</fieldset>","<br>|<br />","<small>.*?</small>|<pre>|</pre>|<font.*?>|</font>|&nbsp;","<code>","</code>","<b>","</b>","<i>","</i>","<u>","</u>","&amp;lt;|&lt;","&amp;gt;|&gt;","<hr>",\'<img(.*?)rel="(.*?)"(.*?)>\');var e=new Array("","\n","","[c]","[/c]","[b]","[/b]","[i]","[/i]","[u]","[/u]","<",">","[hr]","[sm=$2]");var d=0;for(i in b){regex=new RegExp(b[i],"gi");a=a.replace(regex,e[d++])}if(document.getElementById("form").style.visibility!="visible"){switchLayer("form")}document.getElementById("message").value+="[q="+c+"]"+a+"[/q]\n"}function blnk(b,a){document.getElementById(b).style.textDecoration=(a)?"none":"underline";a=a?0:1;tm=setTimeout(\'blnk("\'+b+\'",\'+a+")",1000)}function confirmLink(b,c){var a=confirm(confirmMsg+" :\n"+c);if(a){b.href+="&do=1"}return a};function scrollTo(hash) {location.hash = "#" + hash;}
			/*ONGLETS form http://www.supportduweb.com/*/
			function tab(name){if(document.getElementById(\'tab\'+ idTab) !==null) {document.getElementById(\'tab\'+ idTab).className = \'tabA tab\';document.getElementById(\'tab\'+ name).className = \'tabB tab\';document.getElementById(\'tabContent\'+ idTab).style.display = \'none\';document.getElementById(\'tabContent\'+ name).style.display = \'block\';
				idTab = name;}}
			/*VISUAL EFFECTS from http://www.pluxml.org */
			function setOpacity(obj,opacity){obj.style.minHeight=obj.style.minHeight;opacity=(opacity==100)?99.999:opacity;obj.style.filter="alpha(opacity="+opacity+")";obj.style.KHTMLOpacity=opacity/100;obj.style.MozOpacity=opacity/100;obj.style.opacity=opacity/100}function fadeOut(objId,opacity){var obj=document.getElementById(objId);var stop=document.getElementById(\'noToogle\');if(obj&&!stop){if(opacity==undefined){window.setTimeout("fadeOut(\'"+objId+"\',"+100+")",3000)}else{if(opacity>=0){setOpacity(obj,opacity);opacity-=10;window.setTimeout("fadeOut(\'"+objId+"\',"+opacity+")",100)}else{obj.style.display=\'none\'}}}}
			/*CALENDRIER Script featured on JavaScript Kit- http://www.javascriptkit.com */
			var ds_i_date=new Date();var ds_c_month=ds_i_date.getMonth()+1;var ds_c_year=ds_i_date.getFullYear() - 40;function ds_getel(id){return document.getElementById(id)}function ds_getleft(el){var tmp=el.offsetLeft;el=el.offsetParent;while(el){tmp+=el.offsetLeft;el=el.offsetParent}return tmp}function ds_gettop(el){var tmp=el.offsetTop;el=el.offsetParent;while(el){tmp+=el.offsetTop;el=el.offsetParent}return tmp}var ds_oe=ds_getel(\'ds_calclass\');var ds_ce=ds_getel(\'ds_conclass\');var ds_ob=\'\';function ds_ob_clean(){ds_ob=\'\'}function ds_ob_flush(){ds_oe.innerHTML=ds_ob;ds_ob_clean()}function ds_echo(t){ds_ob+=t}var ds_element;var ds_monthnames=[\''.L_JANUARY.'\',\''.L_FEBRUARY.'\',\''.L_MARCH.'\',\''.L_APRIL.'\',\''.L_MAY.'\',\''.L_JUNE.'\',\''.L_JULY.'\',\''.L_AUGUST.'\',\''.L_SEPTEMBER.'\',\''.L_OCTOBER.'\',\''.L_NOVEMBER.'\',\''.L_DECEMBER.'\'];var ds_daynames=[\''.L_SUND.'\',\''.L_MOND.'\',\''.L_TUES.'\',\''.L_WEDN.'\',\''.L_THUR.'\',\''.L_FRID.'\',\''.L_SATU.'\'];function ds_template_main_above(t){return\'<table cellpadding="3" cellspacing="1" class="ds_tbl">\'+\'<tr>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_py();">&lt;&lt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_pm();">&lt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_hi();" colspan="3">['.CLOSE.']</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_nm();">&gt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_ny();">&gt;&gt;</td>\'+\'</tr>\'+\'<tr>\'+\'<td colspan="7" class="ds_head">\'+t+\'</td>\'+\'</tr>\'+\'<tr>\'}function ds_template_day_row(t){return\'<td class="ds_subhead">\'+t+\'</td>\'}function ds_template_new_week(){return\'</tr><tr>\'}function ds_template_blank_cell(colspan){return\'<td colspan="\'+colspan+\'"></td>\'}function ds_template_day(d,m,y){return\'<td class="ds_cell" onclick="ds_onclick(\'+d+\',\'+m+\',\'+y+\')">\'+d+\'</td>\'}function ds_template_main_below(){return\'</tr>\'+\'</table></div>\'}function ds_draw_calendar(m,y){ds_ob_clean();ds_echo(ds_template_main_above(ds_monthnames[m-1]+\' \'+y));for(var i=0;i<7;i++){ds_echo(ds_template_day_row(ds_daynames[i]))}var ds_dc_date=new Date();ds_dc_date.setMonth(m-1);ds_dc_date.setFullYear(y);ds_dc_date.setDate(1);var days=0;if(m==1||m==3||m==5||m==7||m==8||m==10||m==12){days=31}else if(m==4||m==6||m==9||m==11){days=30}else{days=(y%4==0)?29:28}var first_day=ds_dc_date.getDay();var first_loop=1;ds_echo(ds_template_new_week());if(first_day!=0){ds_echo(ds_template_blank_cell(first_day))}var j=first_day;for(i=0;i<days;i++){if(j==0&&!first_loop){ds_echo(ds_template_new_week())}ds_echo(ds_template_day(i+1,m,y));first_loop=0;j++;j%=7}ds_echo(ds_template_main_below());ds_ob_flush()}function ds_sh(t){ds_element=t;var ds_sh_date=new Date();ds_c_month=ds_sh_date.getMonth()+1;ds_c_year=ds_sh_date.getFullYear() - 40;ds_draw_calendar(ds_c_month,ds_c_year);ds_ce.style.display=\'\';var the_left=ds_getleft(t);var the_top=ds_gettop(t)+t.offsetHeight;ds_ce.style.left=the_left+\'px\';ds_ce.style.top=the_top+\'px\'}function ds_hi(){ds_ce.style.display=\'none\'}function ds_nm(){ds_c_month++;if(ds_c_month>12){ds_c_month=1;ds_c_year++}ds_draw_calendar(ds_c_month,ds_c_year)}function ds_pm(){ds_c_month=ds_c_month-1;if(ds_c_month<1){ds_c_month=12;ds_c_year=ds_c_year-1}ds_draw_calendar(ds_c_month,ds_c_year)}function ds_ny(){ds_c_year++;ds_draw_calendar(ds_c_month,ds_c_year)}function ds_py(){ds_c_year=ds_c_year-1;ds_draw_calendar(ds_c_month,ds_c_year)}function ds_format_date(d,m,y){var m2=\'00\'+m;m2=m2.substr(m2.length-2);var d2=\'00\'+d;d2=d2.substr(d2.length-2);return d2+\'/\'+m2+\'/\'+y}function ds_onclick(d,m,y){ds_hi();if(typeof(ds_element.value)!=\'undefined\'){ds_element.value=ds_format_date(d,m,y)}else if(typeof(ds_element.innerHTML)!=\'undefined\'){ds_element.innerHTML=ds_format_date(d,m,y)}else{alert(ds_format_date(d,m,y))}}
		';

			file_put_contents(MU_JS.'scripts.js', $js);
		}	
	} 

	/**
	*
	* CRÉATION DES FEUILLES DE STYLE (css)
	*/
	private function mkcss() {

		$main = '*{margin:0;padding:0;border:0;text-decoration:none;font-weight:normal;font-style:normal;font-size:12px;font-family:Helvetica,Arial,sans-serif}span,p,ul,ol,table,td,th,hr,blockquote,input,textarea{margin-bottom:15px;line-height:1.3em}table{border-collapse:collapse}h1{margin-bottom:15px;font-size:1.8em;line-height:1.1}h2{margin-bottom:10px;font-size:1.4em;line-height:1.3}h3{margin-bottom:10px;font-size:1.1em;line-height:1.3}h4,h5,h6{margin-bottom:15px}strong,b{font-weight:bold}em,i{font-style:italic}u{text-decoration:underline}del,s,strike{text-decoration:line-through}code,pre,samp{overflow:auto;margin:5px 0 5px 0;padding:8px 13px;border:1px solid +BORDER+;background-color:#efefef;font-size:0.8em;font-family:"Courier New",Courier,"Lucida Sans Typewriter","Lucida Typewriter",monospace}li{margin-left:25px;line-height:30px}img{max-width:100%;height:auto}header,nav,section,article,aside,footer,details,figcaption,figure,audio,video,canvas{display:block;overflow:hidden}@font-face{font-family:\'fontello\';src:url(\'fonts/fontello.eot?36244670\');src:url(\'fonts/fontello.eot?36244670#iefix\')format(\'embedded-opentype\'),url(\'fonts/fontello.woff?36244670\')format(\'woff\'),url(\'fonts/fontello.ttf?36244670\')format(\'truetype\'),url(\'fonts/fontello.svg?36244670#fontello\')format(\'svg\');font-weight:normal;font-style:normal}[class^="icon-"]:before,[class*=" icon-"]:before{font-family:"fontello";font-style:normal;font-weight:normal;speak:none;display:inline-block;text-decoration:inherit;width:1em;margin-right:.2em;text-align:center;font-variant:normal;text-transform:none}.icon-mail:before{content:\'\65\'}.icon-smile:before{content:\'\e811\'}.icon-video:before{content:\'\e814\'}.icon-picture:before{content:\'\e813\'}.icon-info:before{content:\'\e828\'}.icon-home:before{content:\'\e80b\'}.icon-link:before{content:\'\e812\'}.icon-attach:before{content:\'\74\'}.icon-lock:before{content:\'\e825\'}.icon-pin:before{content:\'\e803\'}.icon-eye:before{content:\'\e800\'}.icon-code:before{content:\'\e815\'}.icon-pencil:before{content:\'\e801\'}.icon-edit:before{content:\'\e826\'}.icon-comment-empty:before{content:\'\e80c\'}.icon-chat-empty:before{content:\'\e81b\'}.icon-bell:before{content:\'\e821\'}.icon-attention:before{content:\'\e807\'}.icon-trash:before{content:\'\e808\'}.icon-folder-open-empty:before{content:\'\e80a\'}.icon-cog:before{content:\'\e818\'}.icon-user:before{content:\'\e81d\'}.icon-wrench:before{content:\'\e806\'}.icon-calendar:before{content:\'\e809\'}.icon-angle-up:before{content:\'\e80d\'}.icon-right-hand:before{content:\'\e824\'}.icon-ccw:before{content:\'\e81c\'}.icon-play-circled2:before{content:\'\e810\'}.icon-desktop:before{content:\'\e820\'}.icon-globe:before{content:\'\e822\'}.icon-leaf:before{content:\'\70\'}.icon-bold:before{content:\'\e816\'}.icon-italic:before{content:\'\e817\'}.icon-list:before{content:\'\e823\'}.icon-strike:before{content:\'\e81a\'}.icon-underline:before{content:\'\e819\'}.icon-paste:before{content:\'\e80f\'}.icon-off:before{content:\'\e81f\'}.icon-floppy:before{content:\'\e81e\'}.icon-megaphone:before{content:\'\e80e\'}.icon-key:before{content:\'\e827\'}.icon-cog-alt:before{content:\'\e805\'}body{background-color:+BGCOLOR+;color:+COLOR+;font-family:Helvetica,Arial,sans-serif}.wrapper{margin:40px auto;padding:30px 5% 50px 5%;min-width:310px;max-width:1425px;width:80%;background-color:+WRAPBGCOLOR+;box-shadow:0 2px 6px rgba(100,100,100,0.3);color:+WRAPCOLOR+;line-height:1.5em}a{outline:none;color:+COLORLINKS+}a:hover{color:+COLORLINKSHOVER+;text-decoration:none}.center{text-align:center}.underline{text-decoration:underline}.gradient{background:+BGGRADIENT+; border:1px solid +BORDERGRADIENT+; color:+COLORGRADIENT+; vertical-align:middle; padding:6px; margin-bottom:3px; text-align:left; font-weight:bold}header[role=banner]{overflow:hidden;padding:10px 0 10px 0;text-transform:uppercase}header[role=banner] h1{letter-spacing:12px;font-size:2em;line-height:1.3em}header[role=banner] p{font-weight:normal;font-size:13px;line-height:1.846153846}.maintitle{font-size:35px}nav[role=navigation]{margin-bottom:15px;padding:10px 0;border-bottom:1px solid +NAVBORDER+}#menu li{float:right;list-style-type:none}.breadcrumbs li{float:left;list-style-type:none;margin-right:-20px}.breadcrumbs li+li:before{content:" > ";color:+COLOR>+;font-size:12px;margin:0 3px;position:relative;top:-1px}.selectColor{position:relative;display:inline-block;border-radius:3px;width:16px;height:16px;line-height:18px;cursor:pointer}.image-right{float:right}.left{float:left}.Box{background:+BGBOX+;border:1px solid +BORDERBOX+;vertical-align:middle;margin-bottom:3px;text-align:left;margin-top:50px;border-radius:5px;padding:10px;}.privMsg{margin:20px;border-left:2px solid +BORDER+;padding-left:10px;}.tab{display:inline-block;margin:0 0 0 20px;padding:15px 15px 5px 15px;border:1px solid +BORDER+;cursor:pointer;-moz-border-radius-topright:5px;-webkit-border-top-right-radius:5px;border-top-right-radius:5px;-moz-border-radius-topleft:5px;-webkit-border-top-left-radius:5px;border-top-left-radius:5px;font-size:1.2em}.tab i{font-size:1.2em}.tabA{background:+BGTABA+;border-bottom:0px solid +BORDERTABA+;padding-bottom:6px}.tabB{background:+BGTABB+;border-bottom:1px solid +BORDERTABB+}.tabContent{background-color:+BGTABB+;border:1px solid +BORDERTABA+;margin-top:-16px;padding:15px;display:none;-moz-border-radius-topright:5px;-webkit-border-top-right-radius:5px;border-top-right-radius:5px;-moz-border-radius-topleft:5px;-webkit-border-top-left-radius:5px;border-top-left-radius:5px;z-index:35000;}#menu li form.text-right input{width:170px;height:10px;border:none;border-bottom:1px solid +BORDER+;margin-right:10px}#registration span,#registration i{font-size:1.5em}#registration textarea{height:50px;width:70%;margin:-20px 0 30px 25px}#registration input{padding:2px}#captcha-question{margin-right:20px}#captcha-question:after{content:\'\';display:block;margin-bottom:20px}#captcha-input{width:120px;margin-left:25px}#registration input.input-success,#registration textarea.input-success,#registration select.input-success,#registration .input-success{background:none;border-color:#18a011;height:10px;box-shadow:0 0 0 2px rgba(24,160,17,0.3),0 1px 2px rgba(0,0,0,0.2)inset}#registration input.input-gray,#registration textarea.input-gray,#registration select.input-gray,#registration .input-gray{border-color:#ccc;box-shadow:0 0 0 2px rgba(204,204,204,0.3),0 1px 2px rgba(0,0,0,0.2)inset}#registration input:focus,#registration textarea:focus{outline:0;border-color:#5ca9e4;box-shadow:0 0 0 2px rgba(70,161,231,0.3),0 1px 2px rgba(0,0,0,0.2)inset}input.input-search,input[type="search"]{padding-right:10px;padding-left:10px;margin-bottom:0;border-radius:15px}#registration .message-info{width:50%;margin:30px auto 10px 170px;background-color:#ebcd00;padding:10px}#registration pre{font-size:1.3em;background-color:#ebcd00;border:none;color:#111;}.lead pre, .lead pre code{background:none;color:#111;padding:10px;text-align:left;overflow-y:hidden;overflow-x:hidden;}section[role=main]{clear:both;margin-bottom:35px;padding:10px 0 15px 0;width:100%;text-align:left}section ul{list-style:none}img.icone{display:inline-block;padding:0;margin:-5px 0}.content{overflow:hidden}article{margin-bottom:40px;padding-bottom:20px;border-bottom:1px solid #efefef}article h1,article h1 a{margin-bottom:10px;color:#555;font-weight:bold}article h2,article h3{padding-top:10px}article header p,article footer p{margin:0;color:#666;font-size:0.85em}article header p a,article footer p a{color:#666}article section{margin:20px 0 20px 0;color:#000}.home article footer{margin-left:63px}article img{margin:0 5px}ul a img,ol a img{display:inline-block;margin-bottom:-10px;border:none}.block ul li{font-size:18px}.more{margin-top:15px}.rss{padding:0 0 0 20px;background:url(img/rss.png)0px 3px no-repeat}article footer .tags a{padding:0 4px 0 4px}article img{padding:5px;border:1px solid +BORDER+}.art-chapo{margin-bottom:15px}table{width:100%;border-radius:5px;}td,th{margin:0;padding:2px;border:1px solid +BORDER+;font-size:12px;border-collapse:collapse;}th{padding:10px;font-weight:bold;text-align:left}.noresult{padding:10px;}.profil > i{font-size:2em;display:inline-block;margin-left:100px;}#form-title .icon-pin{vertical-align:top;color:#111;display:inline-block;margin-top:5px}#form-title input[type=text]{background:none;border:none;padding:0;box-shadow:none;font-size:1.8em;}.Lien:link, .Lien:visited{color:+COLOR.Lien+; text-decoration:none}.Lien:hover{color:+COLOR.Lien+; text-decoration:underline}.LienNonLu:link, .LienNonLu:visited{color:+COLOR.LienNonLu+; text-decoration:none}.LienNonLu:hover{color:+COLOR.LienNonLu+; text-decoration:underline}.avatar{width:80px; height:80px}.avatarTD{vertical-align:top;text-align:left;padding-left:10px;width:18%; font-size:12px; font-family:Courrier,Monaco,monospaced; padding-bottom:10px}.tooltipTD{padding-left:5px; vertical-align:middle; font-size:9px; font-family:Courrier,Monaco,monospaced}.datePost{text-align:right;color:#9a9a9a; font-size:12px; font-family:Courrier,Monaco,monospaced;padding-top:5px; padding-right:3px}.mb-name{padding:10px 10px 20px 10px;border-bottom:1px solid +BORDER+;margin-bottom:20px}.mb-name a{font-size:1.8em}.mb-infos{color:#a1a1a1}#topics p,#topics span{margin:20px 10px}#topics p a{font-size:1.2em;}#topics p a.read{color:#555;}#topics p a.unread{color:#e85c40;font-weight:bold}#topics .icon-pin{color:#e85c40}#topics .mess{font-size:16px; text-align:center; vertical-align:middle}#topics .lastmsg{padding-left:10px;}.messageTD{padding:10px; font-size:14px}.messageTD div a {color:+COLORMENU+;}.admin{text-align:center}.admin a i{font-size:1.5em}.toggle{padding-top:10px; margin:0px; display:none; visibility:hidden}.toggleLink{text-decoration:none; display:block; padding:3px 3px 3px 6px; margin:2px}.toggleLink:link, .toggleLink:visited{color:#666; background:#e8ebed}.toggleLink:hover{background:#b1c5d0; color:#fff}.tooltip{position:absolute; border:1px solid #999; text-align:left; display:none; background-color:rgba(255,255,255,0.9); padding:6px; color:#666; font-size:11px; z-index:999; width:400px}@keyframes blink{0%{color:red;}100%{color:black;}}@-webkit-keyframes blink{0%{color:red;}100%{color:black;}}.blink{-webkit-animation:blink 0.5s linear infinite; -moz-animation:blink 0.5s linear infinite; -ms-animation:blink 0.5s linear infinite; -o-animation:blink 0.5s linear infinite; animation:blink 0.5s linear infinite;}.tr-bottom{height:35px}.signature{margin-top:40px;border-top:1px solid +BORDER+}.signature blockquote{margin:-1px auto 0 50px;padding-top:40px;background:none;border:none;border-left:1px solid +BORDER+}.text-quote {margin:10px auto auto 30px;border-left: 2px solid #a1a1a1;padding-left:10px;}#listfiles{float:right;margin-right:150px}.ds_box{background-color:#FFF;position:absolute;z-index:32767;border:none;}.ds_tbl{background-color:#FFF}.ds_head{background-color:rgb(223,240,250);color:rgb(18,77,114);font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:bold;text-align:center;letter-spacing:2px}.ds_subhead{background-color:rgb(18,77,114);color:rgb(223,240,250);font-size:12px;font-weight:bold;text-align:center;font-family:Arial,Helvetica,sans-serif;max-width:32px}.ds_cell{background-color:#EEE;color:#000;font-size:13px;text-align:center;font-family:Arial,Helvetica,sans-serif;padding:2px;cursor:pointer}.ds_cell:hover{background-color:#F3F3F3}#ds_calclass{border:none;}#ds_conclass{width:300px;border:none;}.cal{border:1px solid rgb(179,219,243);border-radius:5px;}.right{float:right}p.right{display:inline-block;font-size:2em;padding:0 7px 0 0;line-height:30px;margin-bottom:0}.clear{clear:both}.pagination{margin:10px;border:1px solid +BORDER+;max-width:300px}.p_page,.p_prev a,.p_first a,.p_last a,.p_next a,.p_current{display:inline-block;margin:0 5px 0 0;padding:0 7px 0 7px;border:1px solid #efefef;text-align:center}.p_current{color:#555}.p_page:first-letter,.p_prev a:first-letter,.p_first a:first-letter,.p_last a:first-letter,.p_next a:first-letter{text-transform:uppercase}ul.smileys{width:170px}.smileys li{float:left}.forms-inline-list li{display:inline-block;float:left}.forms-inline-list li:after{content:\'\';display:block;clear:both}form,fieldset{border:none}form p{margin-bottom:5px;text-align:left}label{display:block;width:250px;min-width:150px;font-weight:bold}input,select{margin-bottom:25px!important;padding:0 6px;height:30px!important;outline:none;border:1px solid #bbb;}input[type=text]{max-width:480px;width:90%}select{padding:5px 6px}.searchform input[type=text]{width:50%}input[type=submit],input[type=reset]{margin-bottom:0!important;padding:0 6px;width:auto;height:34px!important;border:1px solid #bbb;background-color:#ef4423;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px}input[type=submit]:hover,input[type=reset]:hover{border:1px solid #bbb;background-color:#bbb;text-decoration:none}textarea{display:block;margin:20px auto;padding:3px 6px;width:98%;height:300px;outline:none;border:1px solid #bbb;font-family:Arial,Helvetica,sans-serif;cursor:auto}textarea.meta-desc{height:50px}input:hover,select:hover{background-color:#efefef}input:focus,select:focus,textarea:focus{border:1px solid #77bace}.capcha-letter{font-weight:bold}.capcha-word{font-weight:bold}#id_rep{display:block;margin-top:5px}.forms-columnar:after{content:".";display:block;height:0;clear:both;visibility:hidden}.forms-columnar input[type="range"],.forms-columnar input[type="file"],.forms-columnar select[multiple="multiple"]{display:inline-block}.forms-columnar p{position:relative;padding-left:170px}.forms-columnar label{float:left;width:150px;text-align:right;top:0;left:0;position:absolute}.forms-columnar .forms-list,.forms-columnar .forms-inline-list{margin-left:170px}.forms-columnar .forms-list label,.forms-columnar .forms-inline-list label{position:static;0}.forms-columnar .forms-inline-list label{margin-right:1.65em}.forms-push{position:relative;padding-left:170px}.forms-section{font-weight:bold;border-bottom:1px solid #eee;padding:0 0 10px 0;margin-bottom:1em;line-height:1}.forms-columnar .forms-section{padding-left:170px}input[type="radio"],input[type="checkbox"]{position:relative;top:-1px}input[type="text"],input[type="password"],input[type="email"],input[type="url"],input[type="phone"],input[type="tel"],input[type="number"],input[type="datetime"],input[type="date"],input[type="search"],input[type="datetime-local"],textarea,select[multiple="multiple"]{position:relative;z-index:2;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif;border:1px solid #ccc;margin:0;padding:3px 2px;background-color:white;color:#333;font-size:1em;line-height:1;border-radius:1px;box-shadow:0 1px 2px rgba(0,0,0,0.1)inset;-webkit-transition:border ease.5s;-moz-transition:border ease.5s;-o-transition:border ease.5s;transition:border ease.5s}input[type="range"]{position:relative;top:3px}textarea{line-height:1.4em}select{margin-bottom:0!important}.btn{text-decoration:none;color:#000;border-radius:2px;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif;border:1px solid #ccc;border-bottom-color:#b3b3b3;line-height:1;padding:.7em 1.1em.6em 1.1em;font-weight:500;font-size:.85em;background-color:#f1f1f1;background-image:-moz-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-ms-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-webkit-gradient(linear,0 0,0 100%,from(#fcfcfc),to(#e0e0e0));background-image:-webkit-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-o-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:linear-gradient(top,#fcfcfc,#e0e0e0);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#fcfcfc\',endColorstr=\'#e0e0e0\',GradientType=0);text-shadow:0 1px 0 #fff;box-shadow:none}.btn:hover{color:#000;background:#e0e0e0}.btn-red{border-color:#c01415;border-bottom-color:#910f10;background-color:#e54546;background-image:-moz-linear-gradient(top,#ef6465,#d71618);background-image:-ms-linear-gradient(top,#ef6465,#d71618);background-image:-webkit-gradient(linear,0 0,0 100%,from(#ef6465),to(#d71618));background-image:-webkit-linear-gradient(top,#ef6465,#d71618);background-image:-o-linear-gradient(top,#ef6465,#d71618);background-image:linear-gradient(top,#ef6465,#d71618);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ef6465\',endColorstr=\'#d71618\',GradientType=0)}.btn-orange{border-color:#cd640b;border-bottom-color:#9c4c08;background-color:#ee7f22;background-image:-moz-linear-gradient(top,#f48a30,#e5700c);background-image:-ms-linear-gradient(top,#f48a30,#e5700c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#f48a30),to(#e5700c));background-image:-webkit-linear-gradient(top,#f48a30,#e5700c);background-image:-o-linear-gradient(top,#f48a30,#e5700c);background-image:linear-gradient(top,#f48a30,#e5700c);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f48a30\',endColorstr=\'#e5700c\',GradientType=0)}.btn-green{border-color:#5a6d2b;border-bottom-color:#3c491d;background-color:#7e993c;background-image:-moz-linear-gradient(top,#90af45,#63782f);background-image:-ms-linear-gradient(top,#90af45,#63782f);background-image:-webkit-gradient(linear,0 0,0 100%,from(#90af45),to(#63782f));background-image:-webkit-linear-gradient(top,#90af45,#63782f);background-image:-o-linear-gradient(top,#90af45,#63782f);background-image:linear-gradient(top,#90af45,#63782f);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#90af45\',endColorstr=\'#63782f\',GradientType=0)}.btn-blue{border-color:#104769;border-bottom-color:#09293d;background-color:#196ea2;background-image:-moz-linear-gradient(top,#1c7ab4,#155c88);background-image:-ms-linear-gradient(top,#1c7ab4,#155c88);background-image:-webkit-gradient(linear,0 0,0 100%,from(#1c7ab4),to(#155c88));background-image:-webkit-linear-gradient(top,#1c7ab4,#155c88);background-image:-o-linear-gradient(top,#1c7ab4,#155c88);background-image:linear-gradient(top,#1c7ab4,#155c88);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#1c7ab4\',endColorstr=\'#155c88\',GradientType=0)}.btn-yellow{border-color:#b7900b;border-bottom-color:#876a08;background-color:#e5b925;background-image:-moz-linear-gradient(top,#f3c835,#cfa30c);background-image:-ms-linear-gradient(top,#f3c835,#cfa30c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#f3c835),to(#cfa30c));background-image:-webkit-linear-gradient(top,#f3c835,#cfa30c);background-image:-o-linear-gradient(top,#f3c835,#cfa30c);background-image:linear-gradient(top,#f3c835,#cfa30c);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f3c835\',endColorstr=\'#cfa30c\',GradientType=0)}.btn-red,.btn-orange,.btn-green,.btn-blue,.btn-yellow{text-shadow:0-1px 0 rgba(0,0,0,0.24)}.btn-red,.btn-orange,.btn-green,.btn-blue,.btn-yellow{color:#fff}.btn-red:hover,.btn-orange:hover,.btn-green:hover,.btn-blue:hover,.btn-yellow:hover{color:rgba(255,255,255,0.8)}.btn-red:hover{background:#d71618}.btn-orange:hover{background:#e5700c}.btn-green:hover{background:#63782f}.btn-blue:hover{background:#155c88}.btn-yellow:hover{background:#cfa30c}.btn-small{font-size:.7em}.btn-big{font-size:1.2em;line-height:1.65em;padding-left:1.5em;padding-right:1.5em}.btn-group{margin-left:170px}.btn-group li{float:left;margin-left:1px}footer[role=contentinfo]{clear:both;padding:20px 0 40px 0;font-size:0.9em}footer[role=contentinfo] p{margin-bottom:5px;color:#444}footer[role=contentinfo] a{color:#ef4423}#toogle-list{list-style:square}footer div,footer ul,footer li{margin:0;padding:0;list-style-type:none}footer ul li{float:left;width:30%;margin-right:3%;border-right:1px solid #cfcfcf;height:150px}#last{border:none}footer ul{border-top:1px solid #aaa;padding-top:20px}footer h4{font-size:1.5em}footer p{line-height:20px}#footer{clear:both;padding-top:1.5em;margin:1.5em 0;font-size:.85em}#footer span{float:right}@media screen and (min-width:768px){.content{width:50%}aside[role=complementary]{width:45%}.nosidebar{width:100%}}@media screen and (max-width:768px){header[role=banner]{text-align:center}.content,.nosidebar,aside[role=complementary]{width:100%}}.video{position:relative;overflow:hidden;padding-bottom:56.25%;height:0}.video iframe,.video object,.video embed{position:absolute;top:0;left:0;width:100%;height:100%}.table-container{border-collapse:collapse;overflow-y:auto;margin:0 0 1em;width:100%;_overflow:auto}.table-container::-webkit-scrollbar{width:14px;height:14px;-webkit-appearance:none}.table-container::-webkit-scrollbar-thumb{border:3px solid #fff;border-radius:8px;background-color:rgba(0,0,0,.3)}@media screen and (max-width:768px){#social{margin:0;text-align:center}}.rememberme input{width:0}.date{float:left;text-align:center;margin:0 15px 0 0;display:block;padding:0}blockquote{background-color:#F5F6CE;padding:15px;margin:15px 0 15px 0;border:1px solid #FFCC00;font-size:0.9em}.msgFlash{position:absolute;width:20%;margin-bottom:10px;padding:15px 10px 5px 10px;-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;top:10px;right:50px;text-align:left;}header[role=banner]h1 a,article section a,nav a,.active{color:+COLORMENU+}.success-msg{border-color:#51a351#51a351#387038;border-color:rgba(0,0,0,0.1)rgba(0,0,0,0.1)rgba(0,0,0,0.25);background-color:#5bb75b;background-image:-moz-linear-gradient(top,#62c462,#51a351);background-image:-webkit-gradient(linear,0 0,0 100%,from(#62c462),to(#51a351));background-image:-webkit-linear-gradient(top,#62c462,#51a351);background-image:-o-linear-gradient(top,#62c462,#51a351);background-image:linear-gradient(to bottom,#62c462,#51a351);background-repeat:repeat-x;color:#fff;text-shadow:0-1px 0 rgba(0,0,0,0.25);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ff62c462\',endColorstr=\'#ff51a351\',GradientType=0);filter:progid:DXImageTransform.Microsoft.gradient(enabled=false);background-color:#51a351}.error{border-color:#bd362f#bd362f#802420;border-color:rgba(0,0,0,0.1)rgba(0,0,0,0.1)rgba(0,0,0,0.25);background-color:#da4f49;background-image:-moz-linear-gradient(top,#ee5f5b,#bd362f);background-image:-webkit-gradient(linear,0 0,0 100%,from(#ee5f5b),to(#bd362f));background-image:-webkit-linear-gradient(top,#ee5f5b,#bd362f);background-image:-o-linear-gradient(top,#ee5f5b,#bd362f);background-image:linear-gradient(to bottom,#ee5f5b,#bd362f);background-repeat:repeat-x;color:#ffffff;text-shadow:0-1px 0 rgba(0,0,0,0.25);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ffee5f5b\',endColorstr=\'#ffbd362f\',GradientType=0);filter:progid:DXImageTransform.Microsoft.gradient(enabled=false);background-color:#bd362f}.error-input{border-color:#ef4423}.error-form-label{padding:8px 25px 7px 25px;background-color:#ffc0cb;color:#ef4423}.close-right{float:right;margin-top:-35px;cursor:pointer}.close{display:none}.warning{padding:5px;background-color:#e2e468;border:1px solid red}.editby{margin-top:10px;font-style:italic;color:#a1a1a1}.error li:hover{background-color:transparent}span.color{padding:5px}.color-orange{color:#ff8e1f}#menu li a, .breadcrumbs li a, footer a{color:+COLORMENU+;}.label,.label-badge{border-radius:2em;border:1px solid +BORDER+;font-size:.7em;display:inline-block;position:relative;top:-1px;line-height:1;padding:3px 8px;color:#000;background-color:#fff;text-decoration:none}.label-badge{top:-4px;left:-1px}.label-data{color:#999;background:0;border:0;padding:0}a.label:hover{color:#000;filter:alpha(opacity=60);-moz-opacity:.6;opacity:.6}.label-black{background-color:#000}.label-red{background-color:#ef6465}.label-orange{background-color:#f48a30}.label-green{background-color:#90af45}.label-blue{background-color:#1c7ab4}.label-yellow{background-color:#f3c835}.label-black,.label-red,.label-orange,.label-green,.label-blue,.label-yellow{border:0;color:#fff;padding:4px 8px}a.label-black:hover,a.label-red:hover,a.label-orange:hover,a.label-green:hover,a.label-blue:hover,a.label-yellow:hover{color:#fff}.label-small{font-size:.6em;padding:3px 5px}hr{border:1px solid +BORDER+}.closed{display:none;}pre{white-space:pre; white-space:pre-wrap; white-space:pre-line; white-space:-pre-wrap; white-space:-o-pre-wrap; white-space:-moz-pre-wrap; white-space:-hp-pre-wrap; word-wrap:break-word;background-color:#111;color:#ffdf3d;font-size:1.2em;max-width:800px;overflow-y:hidden;overflow-x:hidden;overflow:auto;}code{background:none;border:none;color:#ffdf3d;overflow-y:hidden;overflow-x:hidden;overflow:auto;}';
		
		$print = 'html{border:none;background:none}body{font-size:0.7em}#raccourcis,#icone,#menu,#footer,.col2,.noprint,#num_page,p.audio,p.video,header,nav,textarea,input,fieldset,legend,footer{display:none}header.print{display:block}header.print h1{text-align:center;font-size:5em;border:1px dotted black;padding:10px}.colmask{position:absolute;clear:both;left:0;width:96%;height:100%;overflow:visible}.col1{position:relative;left:0px;right:10px;padding:0 10px 1px 10px;overflow:visible;width:100%}.ttttpetit{font-size:50%}.tttpetit{font-size:60%}.ttpetit{font-size:70%}.tpetit{font-size:80%}.petit{font-size:90%}.grand{font-size:110%}.tgrand{font-size:120%}.ttgrand{font-size:130%}.tttgrand{font-size:140%}.ttttgrand{font-size:150%}.gros{font-size:160%}.tgros{font-size:170%}.ttgros{font-size:180%}.tttgros{font-size:190%}.ttttgros{font-size:200%}.arial{font-family:arial}.verdana{font-family:verdana}.times{font-family:times}.courrier{font-family:courrier}.impact{font-family:impact}.geneva{font-family:geneva}.optima{font-family:optima}.sans-serif{font-family:sans-serif}.rouge{color:#FF0001}.rose{color:#FF00FF}.violet{color:#6B35BB}.bleu{color:blue}.bleuclair,.bleuclair a{color:#54A2FF}.vert{color:green}.jaune{color:#F4C464}.orange{color:#FF8600}.marron{color:#6B3503}.gris{color:#777}a img{border:none}.centrer{text-align:center}.gauche{float:left}.droite{float:right}.droite img{margin:10px}.gauche img{margin:10px}.souligner{text-decoration:underline}.video{display:block;margin:auto}table,thead,tbody{border-collapse:collapse;border:1px solid#dedede}table{margin:auto;width:100%;font-size:0.56em;page-break-before:always}thead{background-color:#ECECEC}th,td{margin:0;padding:2px;border:1px solid#dedede;font-size:0.7em;font-family:"Courier new"}tr.pair{background:#fefefe;color:#1B1B1B}tr.impair{background:#C0C0C0;color:#777}#resa-head{border:1px solid#bbb;background-color:#dedede;padding:10px}.cal-title{font-weight:bold;font-size:1.3em;text-align:center}.resa-cal{border-collapse:collapse}.resa-cal tbody tr td{position:relative;height:10px}.resa-motif{text-align:center;border:3px solid#dedede;padding:0}.hours{min-width:30px;width:3%;text-align:center}.vide{min-width:20px;width:1%;border-top:none;border-right:none;border-bottom:none}.hour{min-width:10px;width:3%;border-bottom:none;text-align:center;font-weight:bold;font-size:1em}.desc{width:94%;text-align:center}.granularite{min-width:10px;width:1%;border-top:1px solid#dedede;border-left:none;border-bottom:none;text-align:right}.last{border-bottom:1px solid#dedede}.last-cell-motif{border-bottom:1px solid#000}.titreMotif{font-weight:bolder;font-size:1.5em}.timeExceeded{border-bottom:5px solid red}.mTimeExceeded{padding:10px;border:1px solid red;font-weight:bolder;background-color:#ffb40a}.linkTitle{color:#000}.imgDel{display:none}li{line-height:1.5em}.date{border-top:1px solid black;page-break-after:avoid;margin-top:2cm}.titre_rubrique{padding:2px;padding-left:15px}.cache-courriel a{color:#FF8600}.clefs{float:right;margin-right:50%;margin-top:50px}#note{color:red;font-style:italic;font-size:80%}';

		if(!file_exists(MU_THEMES.$this->theme.DS.MU_CSS.'main.css')) {
			$main = '/*'.TM.'*/'.LICENCEFONT.str_replace(array('{',';','}'),array("{\n\t",";\n\t","\n}\n"), $main);
			file_put_contents(MU_THEMES.$this->theme.DS.MU_CSS.'main.css', $main);
		}
		if(!file_exists(MU_THEMES.$this->theme.DS.MU_CSS.'print.css')) {
			$print = '/*'.TM."*/\n".str_replace(array('{',';','}'),array("{\n\t",";\n\t","\n}\n"), $print);
			file_put_contents(MU_THEMES.$this->theme.DS.MU_CSS.'print.css', $print);
		}
	}

	/**
	*
	* ÉXÉCUTE LA SAUVEGARDE
	*/
	private function do_backup($source, $destination) {
	    $msg='';
	    if (is_string($source)) $source_arr = array($source); // convert it to array

	    if (!extension_loaded('zip')) {
	        $this->session->setMsg(ERROR_TYPE_NOT_ZIP_FILE,'error');
		    header('location: index.php?conf=1');
			exit();
	    }

	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        $this->session->setMsg(ERROR_ZIP_NOT_OPEN,'error');
		    header('location: index.php?conf=1');
			exit();
	    }

	    foreach ($source_arr as $source)
	    {
	        if (!file_exists($source)) continue;
			$source = str_replace('\\', '/', realpath($source));

			if (is_dir($source) === true)
			{
			    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			    foreach ($files as $file)
			    {
			        $file = str_replace('\\', '/', realpath($file));

			        if (is_dir($file) === true)
			        {
			            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			        }
			        else if (is_file($file) === true)
			        {
			            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			        }
			    }
			}
			else if (is_file($source) === true)
			{
			    $zip->addFromString(basename($source), file_get_contents($source));
			}
	    }

	    $zip->close();
	    $msg= ARCHIVE_REC.' </p><p><a class="btn btn-success pull-right" href="'.$destination.'" title="'.DOWNLOAD.'"><i class="icon-download-alt icon-white"></i>&nbsp;'.DOWNLOAD_ARCHIVE.'</a>';
	    $this->session->setMsg($msg,'success-msg',false);
	    header('location: index.php?conf=1');
		exit();
	}
	/**
	*
	* RESTAURATION D'UN FICHIER DE SAUVEGARDE
	*/
	private function restore_forum() {
		$error='';
		$msg='';
		if($_FILES["backup"]["name"]) {
			$filename = $_FILES["backup"]["name"];
			$source = $_FILES["backup"]["tmp_name"];
			$type = $_FILES["backup"]["type"];

			$name = explode(".", $filename);
			$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
			foreach($accepted_types as $mime_type) {
				if($mime_type == $type) {
					$okay = true;
					break;
				} 
			}

			$continue = strtolower($name[1]) == 'zip' ? true : false;
			if(!$continue) {
				$error = ERROR_TYPE_NOT_ZIP_FILE;
			}

			$target_path = './'.$filename;  // Path ou sera stokée votre sauvegarde envoyée
			if(move_uploaded_file($source, $target_path)) {
				$zip = new ZipArchive();
				$x = $zip->open($target_path);
				if ($x === true) {
				$zip->extractTo(MU_DATA); // Path où sera décompressée l'archive
				$zip->close();
				unlink($target_path);
				}
				$msg = BACKUP_DONE;
			} else {	
				$error = ERROR_DURING_UPLOAD;
			}
		}
		if ($error != ''){
			$this->session->setMsg($error,'error');
		}else {
			$this->session->setMsg($msg);
		}
	    header('location: index.php');
		exit();

	}
	/**
	*
	* VÉRIFIE L'ENVOI D'AVATAR
	*/
	private function checkUpload($dir,$type=false,$login='') {
		if (empty($login)) $login = $this->cLogin;
		$error = '';
		if($type) {
			$match="/.gif$|.jpg$|.png$/i";
			$name='avatar';
			$size=$this->maxAvatarSize;
		} else {
			$match=$this->extensionsAutorises;
			$name='attachment';
			$size=1024*200;
		}
		$avatar='';
		if(is_uploaded_file($_FILES[$name]['tmp_name'])) {
			if(preg_match($match,$_FILES[$name]['name'],$match)) {
				if(($_FILES[$name]['size']<$size) || !$type){
					if (!is_dir($dir)) mkdir($dir);
					$avatar=$dir.DS.Tools::title2filename($_FILES[$name]['name']).$match[0];
					if (move_uploaded_file($_FILES[$name]['tmp_name'],$avatar)) { 
						if($type) { 
							$old=$this->members->getMember($login);
							if($old != false && $old->pic!='') unlink($old->pic);
						}
					} else $error=$type?ERROR_AVATAR_CREATION:ERROR_ATTACHMENT_NOT_REC;
				} else $error=ERROR_OVERWEIGHT_AVATAR;
			} else $error=ERROR_PROHIBITED_FILE;
		} else {
			if ( isset($_FILES['avatar']) && $_FILES['avatar']['name'] != '') {
				$error = ERROR_EMPTY_AVATAR;
			}
		} 
		if ($error != '') {
			$this->errors = $error;
			return false;
		} else {
			return $avatar;
		}
	}
}
/**
* Mise en forme du forum
*/
class Template extends Init {

	private $aThemes=array();
	private $aLang=array();
	private $template=array();
	private $install=false;
	private $loaded=false;

	public $cVals=array();

	public function __construct() {
			parent::__construct();

		$this->cVals['Defaut'] = array(
			'COLORMENU'=>'#e85c40',
			'BGCOLOR'=>'#e6e6e6',
			'WRAPBGCOLOR'=>'#fff',
			'WRAPCOLOR'=>'#222',
			'COLOR'=>'#444',
			'COLORLINKS'=>'#555',
			'COLORLINKSHOVER'=>'#e48569',
			'BGGRADIENT'=>'#f09684',
			'BORDERGRADIENT'=>'#999',
			'COLORGRADIENT'=>'#color: #000;text-shadow: 0px 1px 0px rgba(255, 255, 255, 0.6)',
			'BORDER'=>'#ddd',
			'BGTABA'=>'#ddd',
			'BORDERTABA'=>'#ddd',
			'BGTABB'=>'#fff',
			'BORDERTABB'=>'#fff',
			'NAVBORDER'=>'#efefef',
			'COLOR.Lien'=>'#91a5b0',
			'COLOR.LienNonLu'=>'#f90',
			'COLOR>'=>'#aaa',
			'BGBOX'=>'#fff',
			'BORDERBOX'=>'#999'
		);
		$this->cVals['Very dark gray'] = array(
			'COLORMENU'=>'#a2b381',
			'BGCOLOR'=>'#c5d0b1',
			'WRAPBGCOLOR'=>'#ebede8',
			'WRAPCOLOR'=>'#222',
			'COLOR'=>'#444',
			'COLORLINKS'=>'#555',
			'COLORLINKSHOVER'=>'#e48569',
			'BGGRADIENT'=>'#555',
			'BORDERGRADIENT'=>'#999',
			'COLORGRADIENT'=>'#c5d0b1',
			'BORDER'=>'#ddd',
			'BGTABA'=>'#ddd',
			'BORDERTABA'=>'#ddd',
			'BGTABB'=>'#fff',
			'BORDERTABB'=>'#fff',
			'NAVBORDER'=>'#efefef',
			'COLOR.Lien'=>'#91a5b0',
			'COLOR.LienNonLu'=>'#f90',
			'COLOR>'=>'#aaa',
			'BGBOX'=>'#fff',
			'BORDERBOX'=>'#999'
		);
		$this->cVals['Cyan'] = array(
			'COLORMENU'=>'#cc9999',
			'BGCOLOR'=>'#cde6e6',
			'WRAPBGCOLOR'=>'#fff',
			'WRAPCOLOR'=>'#222',
			'COLOR'=>'#444',
			'COLORLINKS'=>'#555',
			'COLORLINKSHOVER'=>'#e48569',
			'BGGRADIENT'=>'#99cccc',
			'BORDERGRADIENT'=>'#999',
			'COLORGRADIENT'=>'#000000;text-shadow: 0px 1px 0px rgba(255, 255, 255, 0.6)',
			'BORDER'=>'#ddd',
			'BGTABA'=>'#ddd',
			'BORDERTABA'=>'#ddd',
			'BGTABB'=>'#fff',
			'BORDERTABB'=>'#fff',
			'NAVBORDER'=>'#efefef',
			'COLOR.Lien'=>'#91a5b0',
			'COLOR.LienNonLu'=>'#f90',
			'COLOR>'=>'#aaa',
			'BGBOX'=>'#fff',
			'BORDERBOX'=>'#999'
		);
		$this->cVals['Purple'] = array(
			'COLORMENU'=>'#ef4423',
			'BGCOLOR'=>'#e6e6e6',
			'WRAPBGCOLOR'=>'#fff',
			'WRAPCOLOR'=>'#222',
			'COLOR'=>'#444',
			'COLORLINKS'=>'#555',
			'COLORLINKSHOVER'=>'#e48569',
			'BGGRADIENT'=>'#d0b1c5',
			'BORDERGRADIENT'=>'#999',
			'COLORGRADIENT'=>'#555',
			'BORDER'=>'#ddd',
			'BGTABA'=>'#ddd',
			'BORDERTABA'=>'#ddd',
			'BGTABB'=>'#fff',
			'BORDERTABB'=>'#fff',
			'NAVBORDER'=>'#efefef',
			'COLOR.Lien'=>'#91a5b0',
			'COLOR.LienNonLu'=>'#f90',
			'COLOR>'=>'#aaa',
			'BGBOX'=>'#fff',
			'BORDERBOX'=>'#999'
		);
		$this->cVals['Clean'] = array(
			'COLORMENU'=>'#ef4423',
			'BGCOLOR'=>'#e6e6e6',
			'WRAPBGCOLOR'=>'#fff',
			'WRAPCOLOR'=>'#222',
			'COLOR'=>'#444',
			'COLORLINKS'=>'#555',
			'COLORLINKSHOVER'=>'#e48569',
			'BGGRADIENT'=>'#f9f9f6',
			'BORDERGRADIENT'=>'#999',
			'COLORGRADIENT'=>'#999',
			'BORDER'=>'#ddd',
			'BGTABA'=>'#ddd',
			'BORDERTABA'=>'#ddd',
			'BGTABB'=>'#fff',
			'BORDERTABB'=>'#fff',
			'NAVBORDER'=>'#efefef',
			'COLOR.Lien'=>'#91a5b0',
			'COLOR.LienNonLu'=>'#f90',
			'COLOR>'=>'#aaa',
			'BGBOX'=>'#fff',
			'BORDERBOX'=>'#999'
		);
			if (!is_file(MU_THEMES.$this->theme.DS.'showPrivateMsg.php')) {
				$this->install=true;
				ob_start();
				$this->header();
				$this->menu();
				$this->menu_admin();
				$this->breadcrumbs();
				$this->content();
				$this->registrationForm();
				$this->replyForm();
				$this->recThreadsForm();
				$this->formattingHelp();
				$this->showThreads();
				$this->showTopics();
				$this->showPosts();
				$this->showPrivateMsg();
				$this->showMemberlist();
				$this->searchMember();
				$this->editProfilForm();
				$this->editConf();
				$this->frestore();
				$this->showRules();
				$this->footer();
				$this->e404();
				ob_get_clean();
				$this->install=false;
				header('location: index.php');
				exit();
			}
			$params = Tools::getURLParams();
			if ($params == '?css=themes/'.$this->theme.'/css/main.css') {
				$this->_loadCss();
			} else {
				$error = '';
				if ($this->MainError == 404) {
					$error = 404;
				}
				// !$this->forumMode = forum public
				if (empty($this->cLogin) && !$this->forumMode) {
					if (!empty($params) && !preg_match('!^(\?)viewforum=([0-9]{3}-[0-9]{3})(&(amp;)?topic=([0-9]{10,13}))?(&(amp;)?page=([0-9]+)?)?$!', $params) ) {
						$error = 404;
					}
				}
				if (empty($this->cLogin) && $this->forumMode && !empty($params)) {
					$error = 404;
				}

				$this->template = $this->setTemplate($error);

				# On démarre la bufferisation
				ob_start();
				ob_implicit_flush(0);

				# Traitements du thème
				if(MU_THEMES == '' or !is_dir(MU_THEMES)) {
					header('Content-Type: text/plain');
					echo ERROR_THEME_NOTFOUND.' ('.MU_THEMES.$this->theme.'.php) !';
				} elseif(file_exists(MU_THEMES.$this->theme.DS.$this->template['template'].'.php')) {
					# On impose le charset
					header('Content-Type: text/html; charset='.CHARSET);
					$this->showTemplate($this->template);
					# Insertion du template
					include(MU_THEMES.$this->theme.DS.$this->template['template'].'.php');
				} else {
					$this->showTemplate($this->template);
					if (!file_exists(MU_THEMES.$this->theme.DS.$this->template['template'].'.php')) {
						header('Content-Type: text/plain');
						echo ERROR_FILE_NOTFOUND.' ('.MU_THEMES.$this->theme.DS.$this->template['template'].'.php) !';
					} else {
						# Insertion du template
						include(MU_THEMES.$this->theme.DS.$this->template['template'].'.php');
					}
				}

				# Récuperation de la bufférisation
				$output = ob_get_clean();
				$output = Tools::correctAccents($output);

				# On applique la compression gzip si nécessaire et disponible
				if($this->gzip) {
					if($encoding=Tools::httpEncoding()) {
						header('Content-Encoding: '.$encoding);
						$output = gzencode($output,-1,FORCE_GZIP);
				    }
				}
				# Restitution écran
				echo $output;	
			}	
	}
	private function _loadCss() {
		if ($this->loaded) { return false; }
		$cssFile = MU_THEMES.$this->theme.DS.MU_CSS.'main.css';
		
		if (!is_file($cssFile)) return false;

		# On inclue le contenu et on le formate (suppression espaces, sauts de ligne, commentaires )
		$css = file_get_contents($cssFile);
		$css = str_replace(array('/*'.TM.'*/', "\n"), '', $css);
		$css = $this->_compress($css);
		$css = '/*'.TM.'*/'."\n".LICENCEFONT.$css;

	    # On force la mise en cache et le type du fichier
		header('Content-Type: text/css');
		header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + 31536000 ) . ' GMT');
		header("Cache-Control: public, max-age=31536000");

		echo $css;
	    $this->loaded = true;
	}

	/**
	 * Script from Kevin Rocher
	 * http://darklg.me/2010/02/compresser-ranger-css-php/
	 * Modifications Cyril MAGUIRE.
	 */
	private function _compress($buffer) {
		// A décommenter pour utiliser des variables dans le css
		// Les variables seront du type : +MAVARIABLE+
		// Pour les rechercher dans la css, rechercher un +
		if (isset($this->cVals) && isset($this->cVals[$this->cStyle])) {
			$variables_css = $this->cVals[$this->cStyle];
			// On remplace les variables par leur valeur
			foreach($variables_css as $code_variable => $valeur)
				$buffer = str_replace('+'.$code_variable.'+', $valeur, $buffer);
		}
		// Suppression des commentaires
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	 
		// Suppression des tabulations, espaces multiples, retours à la ligne, etc.
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $buffer);
		 
		// Suppression des derniers espaces inutiles
			$buffer = str_replace(array(' { ',' {','{ '), '{', $buffer);
			$buffer = str_replace(array(' } ',' }','} '), '}', $buffer);
			$buffer = str_replace(array(' : ',' :',': '), ':', $buffer);
		 
			return $buffer;
	}
	/**
	* TEXTE D'ACCUEIL
	*/
	public function welcomeText() {
		$buf='<!-- Welcome text -->';
		$buf.='<h4 class="forms-section">'.INFORMATION.'</h4>
	          <div class="lead">';
		if(!$wtp=@file_get_contents(MU_THREAD.'welcome.txt')) {
			$buf.= WELCOME_TXT;
		} else {
			$buf .= BBCHelper::decode(nl2br($wtp)).'</div>';
		}
		return $buf;
	}
	/**
	* REGLES DU FORUM
	*/
	public function rulesText() {
		$buf='<!-- Rules text -->';
		$buf.='<h1>'.FORUM_RULES.'</h1>
	          <div class="Box">';
		if(!$rtxt=@file_get_contents(MU_THREAD.'rules.txt')) {
			$buf.= FORUM_RULES_TXT;
		} else {
			$buf .= BBCHelper::decode(nl2br($rtxt)).'</div>';
		}
		return $buf;
	}
	/**
	 * PAGINATION
	 */
	private function pagination($perPage, $page, $pages) {
			$url = Tools::getURLParams();
			# on supprime le n° de page courante dans l'url
			$arg_url = substr($url,1);
			if(preg_match('/([?&]*page=[0-9]+)$/',$arg_url,$capture)) {
				$arg_url = str_replace($capture[1], '', $arg_url);
			}
			# Calcul des pages
			$prev_page = $page - 1;
			$next_page = $page + 1;
			$last_page = ceil($pages/$perPage);
			# Generation des URLs
			$f_url = MU_BASE_URL.'index.php'.(!empty($arg_url) ? '?': '').$arg_url; # Premiere page
			$arg = (!empty($arg_url)) ? '?'.$arg_url : '';
			$p_url = MU_BASE_URL.'index.php'.$arg.($prev_page<=1?'':((!empty($arg) ? '&' : '?').'page='.$prev_page)); # Page precedente
			$arg = !empty($arg_url) ? $arg_url.'&' : $arg_url;
			$n_url = MU_BASE_URL.'index.php?'.$arg.'page='.$next_page; # Page suivante
			$l_url = MU_BASE_URL.'index.php?'.$arg.'page='.$last_page; # Derniere page

			$pagination = '';
			# On effectue l'affichage
			if($page > 2) # Si la page active > 2 on affiche un lien 1ere page
				$pagination .= '<span class="p_first"><a href="'.$f_url.'" title="'.L_PAGINATION_FIRST_TITLE.'">'.L_PAGINATION_FIRST.'</a></span>&nbsp;';
			if($page > 1) # Si la page active > 1 on affiche un lien page precedente
				$pagination .= '<span class="p_prev"><a href="'.$p_url.'" title="'.L_PAGINATION_PREVIOUS_TITLE.'">'.L_PAGINATION_PREVIOUS.'</a></span>&nbsp;';
			# Affichage de la page courante
			ob_start();
			printf('<span class="p_page p_current">'.L_PAGINATION.'</span>',$page,$last_page);
			$pagination .= ob_get_clean();
			if($page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
				$pagination .= '&nbsp;<span class="p_next"><a href="'.$n_url.'" title="'.L_PAGINATION_NEXT_TITLE.'">'.L_PAGINATION_NEXT.'</a></span>';
			if(($page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
				$pagination .= '&nbsp;<span class="p_last"><a href="'.$l_url.'" title="'.L_PAGINATION_LAST_TITLE.'">'.L_PAGINATION_LAST.'</a></span>';

			return $pagination;
	}
	/**
	* LISTE LES FICHIERS ENVOYÉS DANS LE PROFIL DE L'UTILISATEUR
	*/
	private function listFiles() {
			$dir=MU_UPLOAD.md5(SECURITY_SALT.$this->cLogin).'/';
			$a=$this->members->getMember($this->cLogin);
			$list='<div class="files">';
			$list.='<h3>'.MY_PERSONAL_FILES.'</h3>';
			if($h=@dir($dir)) {
				$id=1;
				while (false !== ($f=$h->read())) {
					if (($f!='.') && ($f!='..') && ($f!=$this->cLogin.'.mp') && $f != 'index.html') {
						$cl=($a->pic!=($dir.urlencode($f)))?"'Lien'":"poster";
						$ext = strtolower(substr($f,-3));
						if($ext == 'peg') $ext = 'jpeg'; 
						if (in_array($ext,array('png','jpg','jpeg','bmp','ico','gif'))) {
							$imgbinary = fread(fopen($dir.'/'.urlencode($f), "r"), filesize($dir.'/'.urlencode($f)));
							$list.=$id. ' | <img class="'.$cl.'" src="data:image/'.$ext.';base64,'.base64_encode($imgbinary).'" title="'.$f.'" style="max-width:80px"/>&nbsp;<a href="?editprofil=1&amp;delfile='.base64_encode($f).'" onclick="return confirmLink(this,\''.$f.'\')" title="'.DEL_THIS_FILE.'" class="profil"><i class="icon-trash"></i></a><br />';
						} else {
							$list.=$id. ' | <a class="'.$cl.'" href="'.MU_URL_MEMBER.md5(SECURITY_SALT.$this->cLogin).'/'.urlencode($f).'" title="'.FILE.'">'.$f.'</a>&nbsp;<a href="?editprofil=1&amp;delfile='.base64_encode($f).'" onclick="return confirmLink(this,\''.$f.'\')" title="'.DEL_THIS_FILE.'" class="profil"><i class="icon-trash"></i></a><br />';
						}
						$id++;
					}
				}
				$h->close();
			}
			$list .= '</div>';
			return $list;
	}
	private function setHeader() {
		$header = new stdClass;
		if(preg_match('/.gif$|.jpg$|.png$/i',$this->uforum) && file_exists($this->uforum)) {
			$header->h1 = '<a href="'.MU_BASE_URL.'" title="'.htmlspecialchars($this->siteName, ENT_QUOTES).'"><img src="'.$this->uforum.'" alt="'.htmlspecialchars($this->siteName, ENT_QUOTES).'" /></a>';
			$header->title = '<title>'.htmlspecialchars($this->siteName, ENT_QUOTES).'</title>';
		} else {
			if ($this->uforum == '[b]&micro;[/b]Forum') {
				$header->h1 = '<a href="'.MU_BASE_URL.'" title="'.PROJECT.'">'.Tools::img('icon-big').'Forum</a>';
			} else {
				$header->h1 = BBCHelper::decode($this->uforum);
			}
			$bbcodes=array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[e]','[/e]','[hr]');
			$header->title = '<title>'.str_replace($bbcodes,'',$this->uforum).'</title>';
		}
		return $header;
	}
	private function setTemplate($error='') {
		if ($error) {
			$template['template'] = $error;
		} else {
			$template = array('options'=>0);
			if($this->isMember || !$this->forumMode) {
				if($this->get_editpost) $template['template'] = 'replyForm-editpost';
				else if($this->get_conf) $template['template'] = 'editConf';
				else if($this->get_topic) $template = array('template'=>'showPosts','options'=>$this->get_private);
				else if($this->get_memberlist) $template['template'] = 'showMemberlist';
				else if($this->searchMember) $template['template'] = 'searchMember';
				else if($this->get_editprofil)$template['template'] = 'editProfilForm';
				else if($this->get_private) $template = array('template'=>'replyForm-mp','options'=>$this->get_private);
				else if($this->get_restore) $template['template'] = 'frestore';
				else if($this->get_topics) $template['template'] = 'showTopics';
				else if($this->get_viewrules) $template['template'] = 'showRules';
				// MODE LIBRE
				else if(!$this->forumMode && !$this->isMember) $template['template'] = 'content';
				#on est connecté, alors on affiche uniquement la liste des forums
				else  $template['template'] =  'showThreads';
			} else {// MODE PRIVÉ
				$template['template'] = 'content';
			}
		}
		return $template;
	}
	private function showTemplate($template) {
		if (!is_file(MU_THEMES.$this->theme.DS.'header.php')) {
			$this->header();
		}
		if($this->haveMP) $this->showPrivateMsg();
		if (!is_file(MU_THEMES.$this->theme.DS.'footer.php')) {
			$this->footer();
		}
		switch ($template['template']) {
			case 404:
				$this->active = 'home';
				$this->e404();
				break;
			case 'replyForm-editpost':
				$this->active = 'editpost';
				$this->setReplyForm('editpost');
				break;
			case 'editConf':
				$this->active = 'editconf';
				$this->editConf();
				break;
			case 'showPosts':
				$this->active = 'posts';
				$this->showPosts();
				break;
			case 'showMemberlist':
				$this->active = 'mblist';
				$this->showMemberlist();
				break;
			case 'searchMember':
				$this->active = 'searchmb';
				$this->searchMember();
				break;
			case 'editProfilForm':
				$this->active = 'profil';
				$this->editProfilForm();
				break;
			case 'replyForm-mp':
				$this->active = 'mp';
				$this->setReplyForm('mp',$template['options']);
				break;
			case 'frestore':
				$this->active = 'restore';
				$this->frestore();
				break;
			case 'content':
				$this->active = 'home';
				$this->content();
				break;
			case 'showTopics':
				$this->active = 'forums';
				$this->showTopics();
				break;
			case 'showThreads':
				$this->active = 'forums';
				$this->showThreads();
				break;
			case 'showRules':
				$this->active = 'rules';
				$this->showRules();
				break;
		}
	}
	private function setFooter($stats) {
		$f['updateVisit']=($this->cLogin ? $this->stat->updateVisit($this->cLogin) : '');
		$f['mb']=array();
		if($stats['members']>1) {$f['mb']['singularPlural']='s';$f['conjug'][1]='ont';}
		else {$f['mb']['singularPlural']='';$f['mb']['conjug']='a';}//Total membres
		$f['m']=($stats['messages']>1)?'s':'';//Messages
		$f['s']=($stats['topics']>1)?'s':'';//Sujets
		$f['d']=($stats['threads']>1)?THREADS:THREAD;//Discussions
		$f['updateVisit']['mbConnected']=isset($f['updateVisit']['mbConnected'])?$f['updateVisit']['mbConnected']:L_NONE;
		$f['updateVisit']['guestsConnected']=isset($f['updateVisit']['guestsConnected'])?$f['updateVisit']['guestsConnected']:1;
		$f['stats'] = $stats;
		return $f;
	}
	/**
	 * INITIALISATION DES VARIABLES DU FORMULAIRE DE SOUSCRIPTION
	 */
	private function setRegistrationForm($form=array()) {
		$nav = Tools::getUserAgent();
		//input($label, $name, $value, $type, $placeholder, $maxlength, $readonly, $class, $icon, $require,onclick)
		$form['userLogin'] = Tools::input(USER_LOGIN, 'login', '', 'text', '', '20', '', 'width-30 input-success', 'icon-user', 'success');
		$form['password'] = Tools::input(PASSWORD, 'password', '', 'password', '', '50', '', 'width-30 input-success', 'icon-lock', 'success');
		$form['birthday'] = Tools::input(BIRTHDAY, 'birthday', '', 'date', 'Jour/Mois/Année', '10', ($nav != 'Chrome' ? true : false), 'width-20 input-success', 'icon-calendar', 'success',($nav != 'Chrome' ? 'ds_sh(this)':''),($nav != 'Chrome' ? 'ds_sh(this)':''));
		$form['email'] = Tools::input(EMAIL, 'email', '', 'email', '', '50', '', 'width-30 input-success', 'icon-mail', 'success');
		$form['website'] = Tools::input(WEBSITE, 'site', '', 'url', 'http://', '255', '', 'width-30', 'icon-globe');
		$form['signature'] = Tools::textarea(SIGNATURE, 'signature', '', '10', '2', SIGNATURE_MSG, '150', '', 'signature width-70');
		return $form;
	}
	/**
	 * INITIALISATION DES VARIABLES DU FORMULAIRE D'EDITION DU PROFIL
	 */
	private function setEditProfilForm($form=array()) {
		$mb=$this->members->getMember($this->cLogin);
		$form['avatar'] = ($mb->pic!='')? '<img src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="'.AVATAR.'"/>':Tools::img('avatar','img-polaroid');
		$form['title'] = EDIT_PROFIL.' ~ '.$this->cLogin;
		//input($label,$name,$value,$type,$placeholder,$maxlength,$readonly,$class,$icon,$require,$onclick)
		$form['birthday'] = Tools::input(BIRTHDAY, 'birthday', $mb->birthday, 'text', 'Jour/Mois/Année', '10', true, '', 'icon-calendar', true, 'ds_sh(this);');
		$form['email'] = Tools::input(EMAIL, 'email', $mb->mail, 'email', '', '50', '', '', 'icon-mail');
		$form['website'] = Tools::input(WEBSITE, 'site', $mb->url, 'url', 'http://', '255', '', 'input-xlarge', 'icon-globe');
		$form['signature'] = Tools::textarea(SIGNATURE, 'signature', $mb->quote, '10', '2', SIGNATURE_MSG, '150', '', 'signature width-70');
		return $form;
	}
	/**
	 * INITIALISATION DES VARIABLES DU FIL D'ARIANE
	 */
	private function setBreadcrumbs() {
		$form=array();
		$url = Tools::getURLParams();
		$url .= ($url)? '&':'?';

		$form['login'] = $this->isMember?$this->cLogin:GUEST;
		$form['selectColor'] = '';
		foreach($this->cVals as $k=>$v) $form['selectColor'] .= '<span onclick="window.location=\''.$url.'style='.$k.'\'" title="'.$k.'" class="selectColor" style="background-color: '.$v['BGGRADIENT'].($k=='Clean'?';border:1px solid #ddd;width:15px;height:15px;top:1px;':'').';">&nbsp;&nbsp;</span> ';
		$form['textClass'] = ($this->isAdmin)?'text-error':'text-info';
		return $form;
	}
	private function setBreadcrumbsLinks() {
		ob_start();
		if($this->get_viewrules){?><i class="icon-bell"></i>&nbsp;<?php echo FORUM_RULES?>
		<?php }
		if($this->get_editpost){if($this->get_topic){?><a href="<?php echo MU_BASE_URL;?>?viewforum=<?php echo $this->get_topics?>&amp;topic=<?php echo $this->get_topic?>"><i class="icon-megaphone"></i>&nbsp;<?php echo $this->threads->forum->getPostsTitle($this->get_topic)?></a></li><li><?php }?><i class="icon-pencil"></i>&nbsp;<?php echo EDIT?>
		<?php }else{if($this->get_conf){?><i class="icon-cog"></i>&nbsp;<?php echo CONFIG_OPTIONS?>
		<?php }else{if($this->get_topics){if($this->get_topic){?><a href="<?php echo MU_BASE_URL;?>?viewforum=<?php echo $this->get_topics?>"><?php }?><i class="icon-chat-empty"></i>&nbsp;<?php echo $this->threads->getTitleCat($this->get_topics);if($this->get_topic){?></a></li><li><i class="icon-comment-empty"></i>&nbsp;<?php echo $this->threads->forum->getPostsTitle($this->get_topic);?>
		<?php }else{if($this->get_memberlist){?><i class="icon-user"></i>&nbsp;<?php echo MEMBERS?>
		<?php }else{if($this->searchMember){?><i class="icon-user"></i>&nbsp;<?php echo RESULT_FOR.$this->searchMember?>
		<?php }else{if($this->get_editprofil){?><i class="icon-eye"></i>&nbsp;<?php echo EDIT_PROFIL?>
		<?php }else{if($this->get_private){ ?><i class="icon-leaf">&nbsp;<?php echo PRIVATE_MSG?></i>
		<?php }else{if($this->get_restore){?><i class="icon-ccw"></i>&nbsp;<?php echo RESTORE?>
		<?php }else{?>
		<?php }}}}}}}}} 
		$links = ob_get_clean();
		if(!empty($links)) return '<li>'.$links.'</li>';
	}
	private function setThreads() {
		$t['nombreThreads'] = $this->threads->getCats();
		$lastOn = $by = null;
		$t['nombrePosts'] = array();
		foreach ($t['nombreThreads'] as $cat => $replies) {
			$lastOn[$cat] = end($t['nombreThreads'][$cat]);
			if ($lastOn[$cat] != '') {$by[$cat] = $this->threads->forum->topics[$lastOn[$cat]]->auth;}
			else {$by[$cat] = NO_REPLY;}
			if (!isset($t['nombrePosts'][$cat])) {$t['nombrePosts'][$cat] = 0;}
			foreach ($replies as $key => $reply) {
				$t['nombrePosts'][$cat] += $this->threads->forum->topics[$reply]->infos != null ? ($this->threads->forum->topics[$reply]->infos->posts)-1 : 0;
			}
		}
		$t['stats'] = array('lastOn'=>$lastOn,'by'=>$by);
		$t['aMainPositions'] = $this->threads->getMainCatsPositions();
		$t['cats'] = $this->threads->getMainCats();
		$t['aSubPositions'] = $this->threads->getSubCatsPositions();
		$t['threads'] = $this->threads->getallThreads('all');
		return $t;
	}
	private function setSubCats($cat) {
		return $this->threads->getallThreads($cat);
	}
	private function setThreadTitle($cat,$maincat,$subcat,$ifTrue='', $ifFalse='') {
		echo '<a href="?viewforum='.$maincat.'-'.$subcat.'" title="'.DISPLAY_FORUM.'" '.((isset($_COOKIE["uTread".$maincat.'-'.$subcat.$this->loginForCookie.""]) && $this->cLogin) ? (!empty($ifTrue)? 'class="'.$ifTrue.'"':'') : ($this->cLogin ? (!empty($ifFalse)? 'class="'.$ifFalse.'"':'') : '')).'>'.stripslashes($cat).'</a>';
	}
	private function setThreadTitleLegend($legend) {
		echo stripslashes($legend);
	}
	private function setThreadLastMsg($maincat,$subcat,$lastOn,$class='') {
		if ($lastOn) echo '<a href="?viewforum='.$maincat.'-'.$subcat.'#bottom" '.($class!=''? 'class="'.$class.'"':'').' title="'.GOTO_LAST_MSG.'">'.date('d M Y à H:i',$lastOn).'</a>';
	}
	private function setThreadLastMsgBy($t,$class='') {
		if($t) echo $this->members->isMember($t)?'<a '.($class!=''? 'class="'.$class.'"':'').' href="index.php?private='.$t.'" title="'.SEND_PRIVATE_MSG.'">'.$t.'</a>':$t;
	}
	private function setTopics($val) {
		list($main,$cat) = explode('-',$this->get_topics);
		switch ($val) {
			case 'pagination':
				echo $this->pagination($this->nbrMsgIndex, $this->page, $this->pages);
				break;
			case 'topicList':
				return $this->threads->forum->getallTopic(false,$main,$cat,$this->nbrMsgIndex,$this->page);
				break;
			case 'reply':
				$this->setReplyForm('newtopic',count($this->threads->forum->getallTopic(false,$main,$cat,$this->nbrMsgIndex,$this->page)));
				break;
		}
	}
	private function setTopicList($t,$format) {
		if (isset($t) && $t!='') echo $format;
	}
	private function setTopicIcon($t,$ifTrue,$ifFalse) {
		 echo (isset($_COOKIE["uFread".$t['topicID'].$this->loginForCookie.""]) && $this->cLogin) ? $ifTrue : ($this->cLogin ? $ifFalse : '');
	}
	private function setTopicTitle($t,$ifTrue='', $ifFalse='') {
		$nbPages = $t['nombrePosts']/$this->nbMsgTopic;
		$linksToPages = '';
		if ($nbPages>1) {
			for ($i=1; $i < ($nbPages+1); $i++) { 
				$linksToPages .= '<a href="?viewforum='.$this->get_topics.'&amp;topic='.$t['topicID'].'&amp;page='.$i.'" title="'.DISPLAY_TOPIC.'">'.$i.'</a>&nbsp;';
			}
			if (!empty($linksToPages)) $linksToPages = '[&nbsp;'.$linksToPages.']';
		}
		
		echo '<a href="?viewforum='.$this->get_topics.'&amp;topic='.$t['topicID'].'" title="'.DISPLAY_TOPIC.'" '.((isset($_COOKIE["uFread".$t['topicID'].$this->loginForCookie.""]) && $this->cLogin) ? (!empty($ifTrue)? 'class="'.$ifTrue.'"':'') : ($this->cLogin ? (!empty($ifFalse)? 'class="'.$ifFalse.'"':'') : '')).'>'.stripslashes($t['titre']).'</a>&nbsp;'.$linksToPages;
	}
	private function setTopicStartonBy($t) {
		echo STARTED_ON.' '.date('d M Y', $t['topicID']).', '.BY.' ';
	}
	private function setTopicPrivate($t,$class='') {
		echo $this->members->isMember($t['auteur'])?'<a '.($class!=''? 'class="'.$class.'"':'').' href="index.php?private='.$t['auteur'].'" title="'.SEND_PRIVATE_MSG.'">'.$t['auteur'].'</a>':$t['auteur'];
	}
	private function setTopicLastMsg($t,$class='') {
		echo '<a href="?viewforum='.$this->get_topics.'&amp;topic='.$t['topicID'].'#bottom" '.($class!=''? 'class="'.$class.'"':'').' title="'.GOTO_LAST_MSG.'">'.date('d M Y à H:i',$t['dernierLe']).'</a>';
	}
	private function setTopicLastMsgBy($t,$class='') {
		echo $this->members->isMember($t['dernierPar'])?'<a '.($class!=''? 'class="'.$class.'"':'').' href="index.php?private='.$t['dernierPar'].'" title="'.SEND_PRIVATE_MSG.'">'.$t['dernierPar'].'</a>':$t['dernierPar'];
	}
	private function setPostPagination($topicObj) {
		if(ceil($topicObj->nbPosts/$this->nbMsgTopic) == 1 ) $this->page = 1;
		return $topicObj->pagination = $this->pagination($this->nbMsgTopic, $this->page, $topicObj->nbPosts);
	}
	private function setPost() {
		if (!is_file(MU_THREAD.$this->whichDir($this->get_topic).'.dat')) {return false;}
		if($this->topicObj = $this->threads->forum->getPosts($this->get_topic,false,$this->nbMsgTopic,$this->page)){
			$this->topicObj->getInfo(0);
			list($this->topicObj->num,$this->topicObj->auths)=$this->topicObj->getInfo(1);
			$this->topicObj->pagination = $this->setPostPagination($this->topicObj);
			$this->topicObj->avatars=array();
			$this->topicObj->quotes=array();
			$this->topicObj->modo=array();
			return $this->topicObj;
		}
		return false;
	}
	private function setOptions() {
		$mains = $this->threads->getCategories('main');
		$sub = $this->threads->getCategories('sub');
		foreach ($sub as $main => $cats) {
			foreach ($cats as $key => $value) {
				$options[$mains[$main]][$main.'-'.$key] = $value['cat'];
			}
		}
		return $options;
	}
	private function topicId() {
		echo $this->get_topic;
	}
	private function pinned($topicObj) {
		echo ($topicObj->infos->type ? 'window.location.search=\''.Tools::getUrlParams().'&amp;postit=off\'" checked="checked':'window.location.search=\''.Tools::getUrlParams().'&amp;postit=on\'');
	}
	private function postTitle($topicObj) {
		echo stripslashes($topicObj->infos->title);
	}
	private function dateOfAction($member) {
		echo date('d M Y à H:i',$member->time);
	}
	private function email($member) {
		echo Tools::protect_email($member->mail);
	}
	private function statut($mb) {
		$statut = new stdClass();
		if($mb->mod) {
			if ($mb->mod>1) {
				$statut->title = FOUNDER;
				$statut->class = 'red';
			} else {
				$statut->title = MODERATOR;
				$statut->class = 'green';
			}
		}else {
			$statut->title = MEMBER;
			$statut->class = 'blue';
		}
		return $statut;
	}
	private function pic($membre,$class="avatar") {
		return ($membre->pic!='')?'<img '.($class!=''? 'class="'.$class.'"':'').' src="'.Tools::base64_encode_image($membre->pic,$membre->extension).'" alt="avatar"/>':Tools::img('avatar',$class);
	}
	private function delMsg($cnt,$auth) {
		echo ($cnt || $this->page > 1)?ANSWER_FROM.' '.$auth:' '.WHOLE_TOPIC;
	}
	private function downloadAttach($attach,$class='') {
		$attachment = Tools::title2filename(substr($attach,strrpos($attach, '/')+1));
		echo '<a '.($class!=''? 'class="'.$class.'" ':'').'href="?pid='.base64_encode($attach).'" title="'.DOWNLOAD.'">'.$attachment.' '.Tools::img('clip').'</a>';
	}
	private function setMemberList() {
		$m = array();
		$m['wd']=$this->isAdmin?25:40;
		$m['pagination'] = $this->pagination($this->nbrMb, $this->page, $this->pagesMb);
		return $m;
	}
	private function setMbOfList($m, $class='img-circle') {
		$mb=$this->members->getMember($m);
		$mb->mail= Tools::protect_email($mb->mail);
		$mb->signature=($mb->quote!="")?BBCHelper::tronquer_texte($mb->quote, 50):"&nbsp;";
		if($mb->url!='') {
			if (!preg_match('|http://|',$mb->url)) $mb->url='http://'.$mb->url;
			$mb->url='&nbsp;&nbsp;<a href="'.$mb->url.'" title="'.$mb->url.'" onclick="window.open(this.href);return false;"><i class="icon-globe"></i></a>';
		}
		if($mb->birthday!='') {
			$mb->birthday = str_replace(' ', '', $mb->birthday);
			$mb->birthday = preg_replace('/([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+(.*)/i', '\\1 \\2 \\3 \\4 \\5', $mb->birthday);
		} else $mb->birthday = '&nbsp;';
		$mb->avatar=($mb->pic != '')?'<img width="40" height="40" src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="Avatar" '.($class!=''? 'class="'.$class.'"':'').' />':Tools::img('avatar',$class,false,false,40);
		return $mb;
	}
	private function setSearchMb() {
		// Calcul de la distance Levenshtein entre le mot recherché
		// avec les pseudos enregistrés
		// Aucune distance de trouvée pour le moment
		$shortest = -1;
		$find = array();
		$i=-1;
		// Boucle sur les des mots pour trouver le plus près
		foreach ($this->members->listMember() as $membre) {
		    // Calcule la distance avec le mot mis en entrée,
		    // et le mot courant
		    $lev = levenshtein($this->searchMember, $membre);
		    // Cherche une correspondance exacte
		    if ($lev == 0) {
		        // Le mot le plus près est celui-ci (correspondance exacte)
		        $closest = $membre;
		        $shortest = 0;
		        $find = array();
		        $find = $closest;
		        // On sort de la boucle ; nous avons trouvé une correspondance exacte
		        break;
		    }
		    // Si la distance est plus petite que la prochaine distance trouvée
		    // OU, si le prochain mot le plus près n'a pas encore été trouvé
		    if ($lev <= $shortest || $shortest < 0) {
		        // Définition du mot le plus près ainsi que la distance
		        $closest  = $membre;
		        $shortest = $lev;
		        $i++;
		        if ($i < 10) {
		        	$find[] = $closest;
		       	}
		    }
		}
		if ($shortest == 0) {
				$mb = $this->members->getMember($this->searchMember);
				$mb->mail= Tools::protect_email($mb->mail);
				$mb->signature=($mb->quote!="")?BBCHelper::tronquer_texte($mb->quote, 50):"&nbsp;";
			if($mb->url!='') {
				if (!preg_match('|http://|',$mb->url)) $mb->url='http://'.$mb->url;
				$mb->url='<a href="'.$mb->url.'" title="'.$mb->url.'" onclick="window.open(this.href);return false;"><i class="icon-globe"></i></a>';
			}
			if($mb->birthday!='') {
				$mb->birthday = str_replace(' ', '', $mb->birthday);
				$mb->birthday = preg_replace('/([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+(.*)/i', '\\1 \\2 \\3 \\4 \\5', $mb->birthday);
			} else $mb->birthday = '&nbsp;';
			$mb->avatar=($mb->pic != '')?'<img width="40" height="40" src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="Avatar" />':Tools::img('avatar','img-circle',false,false,40);
		} else {
			foreach ($find as $key => $m) {
				$mb = $m = $this->members->getMember($m);
				$m->name = $find[$key];
				$m->avatar=($m->pic != '')?'<img width="40" height="40" src="'.Tools::base64_encode_image($m->pic,$m->extension).'" alt="Avatar" />':Tools::img('avatar','img-circle',false,false,40);
				$find[$key] = $m;
			}
			// $mb = new Members();
			// $mb->avatar = null;
		}
		$mb->wd = $this->isAdmin?25:40;
		$mb->closest = $find;
		return $mb;
	}
	private function setPrivateMsg() {
		$s=implode('', file(MU_MEMBER.md5($this->cLogin.SECURITY_SALT).'/'.$this->cLogin.'.mp'));
		$mp = unserialize($s);
		$mess = $mp->getMessage();
		foreach($mess as $m) {
			if($this->members->isMember($m->from)) echo '<a class="Lien" href="?private='.$m->from.'" title="'.PRIVATE_MSG.'">'.$m->from.'</a> '.strtolower(L_ON).' '.date('d/m/Y @ H:i',$m->time).' <br />';
			else {
				$m->from=preg_replace("/(([0-9]{1,3}\.[0-9]{1,3})\.([0-9]{1,3}\.[0-9]{1,3}))/i","\\2.x.x",$m->from);
				echo $m->from.' '.strtolower(L_ON).' '.date('d/m/Y @ H:i',$m->time).' <br />';
			}
			echo '<div class="privMsg">'.stripslashes(BBCHelper::decode($m->content)).'</div><hr />';
		}
		echo '<p class="text-right"><a href="?private='.$m->from.'" class="btn btn-green"><i class="icon-comment-empty"></i> '. ANSWER.' '.TO.' '.$m->from.'</a>';
	}
	private function setReplyForm($type,$mpTo='') {
		$r = new stdClass();
		$r->edit=0; $r->join=0; $r->show=0;
		if($type=='newtopic') {
			$r->name= NEW_TOPIC;
			$r->join=1;
			$r->show=$mpTo?0:1;
		} else if($type=='newpost') {
			$r->name= ANSWER;
			$r->join=1;
		} else if($type=='editpost') {
			if($s = implode("", file(MU_THREAD.$this->whichDir($this->get_topic).'.dat'))) $topicObj = unserialize($s);
			else return false;
			$reply=$topicObj->getReply($this->get_editpost);
			$reply->content = preg_replace('!\[e\](.*)\[\/e\](\\r\\n)*!Ui','',$reply->content);
			$reply->content = html_entity_decode($reply->content,ENT_NOQUOTES,CHARSET);
			$r->name= CHANGE;
			$r->edit=1;
		} else {
			$r->name= SEND_PRIVATE_MSG.' '.TO.' '.$mpTo;
			$r->show=1;
		}
		$this->replyForm();
		if (in_array($this->template['template'],array('replyForm-mp','replyForm-editpost'))) {
			include(MU_THEMES.$this->theme.DS.'header.php');
			include(MU_THEMES.$this->theme.DS.'replyForm.php');
			include(MU_THEMES.$this->theme.DS.'footer.php');
			exit();
		}else {
			include(MU_THEMES.$this->theme.DS.'replyForm.php'); 
		}
	}
	private function searchDirFile($dirToScan=MU_THEMES,$type='dir') {
		if(is_dir($dirToScan)) {
			# On ouvre le repertoire
			if($dh = opendir($dirToScan)) {
				# Pour chaque entree du repertoire
				while(false !== ($file = readdir($dh))) {
					if($file[0]!='.') {
						$dir = is_dir($dirToScan.'/'.$file);
						if($dir && $type=='dir') {
							$this->aThemes[$file] = $file;
						}else {
							$file = str_replace('.php', '', $file);
							$this->aLang[$file] = $file;
						}
					}
				}
				# On ferme la ressource sur le repertoire
				closedir($dh);
			}
		}
	}
	/********************************************************
	 * VOUS POUVEZ MODIFIER LES LIGNES CI-DESSOUS
	 * POUR PERSONNALISER LE THEME
	 ********************************************************/
	public function e404() {
		$string =<<<END
<?php header("HTTP/1.0 404 Not Found");
include(dirname(__FILE__).'/header.php');?>

	<article>
		<h2><?php echo ERROR?></h2>
		<p><?php echo PAGE_NOT_FOUND?></p>
	</article>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'404.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'404.php', $string);
		}
	}

	public function formattingHelp($id='message') {
		$string =<<<END
		<!--<p class="forms-inline"><label><?php //echo SMILEY?></label></p>-->
		<ul class="forms-inline-list">
			<?php echo BBCHelper::formattingHelp('\$id'); ?>
		</ul>
		<p class="clear">&nbsp;</p>
		<!--<p><label><?php echo FORMATING?></label></p>-->
		<ul class="forms-inline btn-group"> 
		   <li><a class="btn" href="javascript:insert('[title=','[/title]','<?php echo BBCHelper::areaToFormat('$id')?>','<?php echo LEVEL?>','<?php echo TXT_REPLACEMENT?>')" rel="tooltip" title="<?php echo TITLE?> ( [title=1,2,3,4]<?php echo TXT_REPLACEMENT?>[/title] )"><i class="icon-angle-up"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[b]','[/b]','<?php echo BBCHelper::areaToFormat('$id')?>',false,false)" rel="tooltip" title="<?php echo BOLD?>"><i class="icon-bold"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[i]','[/i]','<?php echo BBCHelper::areaToFormat('$id')?>',false,false)" rel="tooltip" title="<?php echo ITALIC?>"><i class="icon-italic"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[u]','[/u]','<?php echo BBCHelper::areaToFormat('$id')?>',false,false)" rel="tooltip" title="<?php echo UNDERLINE?>"><i class="icon-underline"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[s]','[/s]','<?php echo BBCHelper::areaToFormat('$id')?>',false,false)" rel="tooltip" title="<?php echo STROKE_THROUGH?>"><i class="icon-strike"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[quote=','[/quote]','<?php echo BBCHelper::areaToFormat('$id')?>','<?php echo WHO?>','<?php echo TXT_REPLACEMENT?>')" rel="tooltip" title="<?php echo QUOTE?> ( [q=<?php echo AUTHOR?>]<?php echo TXT_REPLACEMENT?>[/q] )"><i class="icon-chat-empty"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[c]','[/c]','<?php echo BBCHelper::areaToFormat('$id')?>',false,false)" rel="tooltip" title="<?php echo CODE?>"><i class="icon-code"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[url=','[/url]','<?php echo BBCHelper::areaToFormat('$id')?>','<?php echo LINK_ADDRESS?>','<?php echo TXT_REPLACEMENT?>')" rel="tooltip" title="<?php echo LINK?> ( [url=<?php echo URL?>]<?php echo TXT_REPLACEMENT?>[/url] )"><i class="icon-link"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[img=','[/img]','<?php echo BBCHelper::areaToFormat('$id')?>','<?php echo LINK_PICTURE?>','<?php echo TXT_REPLACEMENT?>')" rel="tooltip" title="<?php echo PICTURE?> ( [img=<?php echo SRC?>]<?php echo TXT_REPLACEMENT?>[/img] )"><i class="icon-picture"></i></a></li>
		   <li><a class="btn" href="javascript:insert('[youtube=','[/youtube]','<?php echo BBCHelper::areaToFormat('$id')?>','<?php echo LINK_VIDEO?>','<?php echo TXT_REPLACEMENT?>')" rel="tooltip" title="<?php echo VIDEO?> ( [youtube=<?php SRC?>]<?php echo TXT_REPLACEMENT?>[/youtube] )"><i class="icon-video"></i></a></li>
		</ul><!-- /btn-group --> 
		<p class="clear">&nbsp;</p>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'formattingHelp.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'formattingHelp.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'formattingHelp.php');
		}
	}

	public function header() {
		$string =<<<END
<?php if(!defined('MU_ROOT')) exit; ?><!DOCTYPE html>
<html lang="<? echo \$this->lang; ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="<?php echo CHARSET ?>">
	<meta name="description" content="<? echo \$this->metaDesc; ?>">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <?php echo \$this->setHeader()->title ?>

    <link rel="icon" href="<?php echo Tools::img('icon','',true);?>" />
	<base href="<?php echo MU_BASE_URL; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo MU_BASE_URL.'?css='.MU_URL_THEMES.\$this->theme; ?>/css/main.css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="<?php echo MU_BASE_URL.MU_URL_THEMES.\$this->theme; ?>/css/print.css" media="print"/>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!--[if lt IE 9]>
	<script>
	var head = document.getElementsByTagName('head')[0], style = document.createElement('style');
	style.type = 'text/css';
	style.styleSheet.cssText = ':before,:after{content:none !important';
	head.appendChild(style);
	setTimeout(function(){ head.removeChild(style); }, 0);
	</script>
	<![endif]-->
</head>
<body id="top" onload="init();">

	<div id="wrapper" class="wrapper <?php echo \$this->active ?>">

    <header role="banner">
    
        <h1><?php echo \$this->setHeader()->h1?></h1>
        <p><?php echo \$this->subtitle; ?></p>
        
        <nav role="navigation"> 
       		<ul id="menu">
				<?php echo \$this->menu(); if(\$this->isMember || !\$this->forumMode){ echo \$this->menu_admin(); }?>
		
			</ul>
        </nav>

    </header>
    <noscript>
		<div class="message message-error">
			<span class="close"></span>
			<header><i class="icon-attention"></i> <?php echo JS_UNAVAILABLE?></header>
			<?php echo JS_UNAVAILABLE_MSG?>

		</div>
	</noscript>
    <nav role="navigation"> 
        <?php echo \$this->breadcrumbs(); 
        if(\$this->haveMP) include(dirname(__FILE__).'/showPrivateMsg.php');?>   

    </nav>
	
	<section><!-- Main -->
    <?php echo \$this->session->msg(); ?>

END;
		if (!is_file(MU_THEMES.$this->theme.DS.'header.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'header.php', $string);
		}
	}
	public function content() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
if(!\$this->forumMode) :// MODE LIBRE?>

			<nav class="onglets">
			<ul>
	            <li class="tabA tab" id="tabhome" onclick="javascript:tab('home');ds_hi();"><i class="icon-home"></i>&nbsp;<?php echo HOME?></li>
	            <li class="tabA tab" id="tabtopics" onclick="javascript:tab('topics');ds_hi();"><i class="icon-list"></i>&nbsp;<?php echo FORUMS?></li>
	            <li class="tabA tab" id="tabsignup" onclick="javascript:tab('signup');"><i class="icon-user"></i>&nbsp;<?php echo SIGN_UP?></li>
	        </ul>
	        </nav>
	        <div class="tabContents">
	            <div class="tabContent" id="tabContenthome">
				<?php echo \$this->welcomeText()?>
				</div>
				<div class="tabContent" id="tabContenttopics">
				<?php echo \$this->showThreads()?>
				</div>
				<div class="tabContent" id="tabContentsignup">
				<?php echo \$this->registrationForm()?>
				</div>
			</div>	
		<?php else :// MODE PRIVÉ?>

			<nav class="onglets">
			<ul>
		        <li class="tabA tab" id="tabhome" onclick="javascript:tab('home');ds_hi();"><i class="icon-home"></i>&nbsp;<?php echo HOME;?></li>
		        <li class="tabA tab" id="tabsignup" onclick="javascript:tab('signup');ds_hi();"><i class="icon-user"></i>&nbsp;<?php echo SIGN_UP;?></li>
		    </ul>
		    </nav>
		    <div class="tabContents">
		        <div class="tabContent" id="tabContenthome">
				<?php echo \$this->welcomeText();?>
				</div>
				<div class="tabContent" id="tabContentsignup">
				<?php echo \$this->registrationForm();?>
				</div>
			</div>
		<?php endif;?>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'content.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'content.php', $string);
		}
	}
	public function footer() {
		$string =<<<END
<?php \$f = \$this->setFooter(\$this->getStat())?>

</section><!-- Fin Main -->


<footer role="contentinfo">

	<ul class="Grid">

		<li>
			<h4><?php echo STATISTICS?></h4>
			
			<p>
				<?php echo WELCOME_TO?>, <span class="color-orange"><?php echo \$f['stats']['lastMb']?></span>
				<br />
				<?php echo TOTAL_MB.\$f['mb']['singularPlural'].': '. \$f['stats']['members']?>
				<br />
				<?php echo WE_HAVE.':<br/>'.\$f['stats']['messages'].' '.MESSAGE.\$f['m'].' '.IN.' '.\$f['stats']['topics'].' '.TOPIC.\$f['s'].' '.OF.' '.\$f['stats']['threads'].' '.\$f['d']?> 
				

			</p>
		</li>
		<li>
			<h4><?php echo LEGEND?></h4>
			<p>
			<i class="icon-folder-open-empty"></i> <?php echo NO_UNREAD_MSG?><br />
			<i class="icon-pin"></i> <?php echo PINNED?><br />
			<i class="icon-comment-empty"></i> <?php echo UNREAD_MSG?><br />
			<i class="icon-paste"></i> <?php echo ATTACHMENT?>
			</p>
		</li>
		<li id="last">
			<h4><?php echo WHO_IS_ONLINE?></h4>
			<p><?php echo MB_ONLINE?> : <b><?php echo \$f['updateVisit']['mbConnected']?></b>
			<br /><?php echo GUESTS?> : <?php echo \$f['updateVisit']['guestsConnected']?>

			</p>
		</li>
	 
	</ul>
	<div id="footer">
		© 2011-<?php echo date('Y').' '.\$this->siteName?>.
		<span><?php echo POWEREDBY.' v.'.VERSION?>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo Tools::baseURL().Tools::getUrlParams()?>#top" title="<?php echo TOP?>">
				<i class="icon-angle-up"></i>
			</a>
		</span>
	</div>
</footer>

</div><!-- wrapper-->
	
	<!-- Calendrier -->
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
		<tr><td id="ds_calclass"></td></tr>
	</table>
	<!-- Le javascript
	================================================== -->
	<script src="js/scripts.js"></script>
	<script type="text/javascript">
		//<!--
		<?php if (isset(\$_SERVER['argv'][0]) && \$_SERVER['argv'][0] == 'register=true') {
			\$idTab = 'signup';
		} else {
			\$idTab = 'home';
		} ?>
		var idTab = '<?php echo \$idTab;?>';
		tab(idTab);
		//-->
	</script>
	<?php \$MsgId = \$this->session->MsgId();
		if(!empty(\$MsgId)):?>

	<script src="js/visual.js"></script>
	<script>fadeOut("<?php echo \$MsgId?>");</script>
	<?php endif;
		if (DEBUG == 1) {
			 Debug::getDebugInstance(\$this->lang)->printBar();
		}
	?>

	</body>
</html>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'footer.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'footer.php', $string);
		}
	}
	/**
	* FORMULAIRE D'INSCRIPTION
	*/
	public function registrationForm() {
		$string =<<<END
<?php \$f = \$this->setRegistrationForm();?>

		<h2 class="forms-section"><?php echo JOIN_COMMUNITY ?></h2>
		<form action="index.php" method="post" enctype="multipart/form-data" autocomplete="off" class="form forms forms-columnar" id="registration">
			<input type="hidden" name="action" value="newuser" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo \$this->maxAvatarSize ?>" />
			<p class="forms-inline"><?php echo \$f['userLogin'] ?></p>
			<p class="forms-inline"><?php echo \$f['password'] ?></p>
			<p class="forms-inline"><?php echo \$f['birthday'] ?></p>
			<p class="forms-inline"><?php echo \$f['email'] ?></p>
			<p class="forms-inline"><?php echo \$f['website'] ?></p>
			<p class="forms-inline"><i class="icon-edit"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo \$f['signature'] ?></p>
			<p>
				<label for="avatar"><?php echo AVATAR;?> <span class="label label-red">&lt; <?php echo (\$this->maxAvatarSize/1024);?>ko</span></label>
				<i class="icon-picture"></i>&nbsp;&nbsp;&nbsp;
				<input type="file" id="avatar" name="avatar">
			</p>
			<p><label for="qid"><?php echo L_CAPTCHA;?></label><?php echo \$this->captcha->template();?></p>
			<p><button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo SIGN_UP;?></button></p>
			<div class="message message-info">
				<i class="icon-info"></i> <?php echo MENDATORY_FIELDS;?><br/>
				<?php echo CHAR_NOT_ALLOWED;?>
				<pre>/ \ &amp; " \' . ! ? :</pre> 
				<?php echo CHAR_NOT_ALLOWED_BIS;?>
			</div>
		</form>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'registrationForm.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'registrationForm.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'registrationForm.php');
		}
	}
	/**
	* ÉDITION DU PROFIL
	*/
	public function editProfilForm() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
	\$f = \$this->setEditProfilForm();?>

		<!-- Edit profil form -->
		<h2><?php echo \$f['title']; ?></h2>
			<ul>
				<li><figure><?php echo \$f['avatar'];?></figure></li>
				<li id="listfiles"><?php echo \$this->listFiles();?></li>
			</ul>
			<hr class="clear"/>
			<form action="index.php" method="post" enctype="multipart/form-data" class="forms forms-columnar clear" id="registration">
				<input type="hidden" name="action" value="editprofil" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo \$this->maxAvatarSize;?>" />
				<p class="forms-inline"><?php echo \$f['birthday']; ?></p>
				<p class="forms-inline"><?php echo \$f['email']; ?></p>
				<p class="forms-inline"><?php echo \$f['website']; ?></p>
				<p class="forms-inline"><i class="icon-edit"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo \$f['signature']; ?></p> 
				<p>
				<label for="avatar"><?php echo AVATAR;?> <span class="label label-red">&lt; <?php echo (\$this->maxAvatarSize/1024);?>ko</span></label><input type="file" id="avatar" name="avatar">
				</p>
				<p>
					<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo SAVE_PROFIL;?></button>
				</p>
			</form>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'editProfilForm.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'editProfilForm.php', $string);
		}
	}
	
	/**
	* AFFICHAGE FIL D'ARIANE (Breadcrumbs)
	*/
	public function breadcrumbs() {
		$string =<<<END
<?php \$f = \$this->setBreadcrumbs();?>
	<div class="image-right"><?php echo \$f['selectColor'];?></div>
	<ul class="breadcrumbs">
		<li><i class="icon-play-circled2"></i>&nbsp;<?php echo WELCOME;?> 
			<span class="<?php echo \$f['textClass']; ?>">
				<strong><?php echo \$f['login'];?></strong>
			</span>
			<?php if(\$this->haveMP) {?>&nbsp;<a  href="javascript:switchLayer('privatebox');" rel="tooltip" title="<?php echo NEW_PRIVATE_MSG;?>" role="button" class="blink" data-toggle="modal"><i class="icon-megaphone"></i></a><?php } ?>

		</li>
		<li><a href="<?php echo MU_BASE_URL;?>"><i class="icon-home"></i>&nbsp;<?php echo HOME;?></a></li>
		<?php echo \$this->setBreadcrumbsLinks();?>

	</ul>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'breadcrumbs.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'breadcrumbs.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'breadcrumbs.php');
		}
	}
	/**
	* AFFICHAGE DU MENU
	*/
	public function menu() {
		$string =<<<END
<?php \$stats=\$this->getStat();

		if(\$this->isMember) :?>
			<li><a href="?logout=1" title="<?php echo QUIT;?>"><i class="icon-off"></i> <?php echo LOGOUT;?></a></li>
			<li><a href="?editprofil=1" title="<?php echo EDIT_MY_PROFIL;?>"><i class="icon-eye"></i> <?php echo PROFIL;?></a></li>
			<li><a href="?memberlist=1" title="<?php echo LIST_OF_MEMBERS;?>"><i class="icon-user"></i> <?php echo MEMBERS;?></a></li>
			<?php if(!\$this->isOwner) :?>

			<li><a href="?viewrules=1" title="<?php echo FORUM_RULES;?>"><i class="icon-bell"></i> <?php echo FORUM_RULES;?></a></li>
			<li><a href="index.php" title="<?php echo HOME;?>"><i class="icon-home"></i> <?php echo FORUMS;?></a></li>
			<?php endif;?>
		<?php else :?>

			<li>
				<form action="index.php" method="post" autocomplete="off" class="text-right">
					<input type="hidden" name="action" value="enter" />
					<span class="icon-smile"></span><input type="text" name="login" placeholder="<?php echo USER;?>" class="log">
					<span class="icon-key"></span><input type="password" name="password" placeholder="<?php echo PASSWORD;?>" class="log">
					<button type="submit" class="btn btn-info"><i class="icon-right-hand"></i> <?php echo CONNECT;?></button>
				</form>
			</li>	
		<?php endif;?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'menu.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'menu.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'menu.php');
		}
	}
	/**
	* NAVIGATION (Admin seulement!)
	*/
	public function menu_admin() {
		$string =<<<END
<?php if(\$this->isAdmin && \$this->isOwner) :?>
	
			<li><a href="?conf=1" title="<?php echo GENERAL_PARAM;?>"><i class="icon-wrench"></i> <?php echo CONFIG;?></a></li>
			<li><a href="?backup=1" title="<?php echo SAVE_BACKUP;?>"><i class="icon-floppy"></i> <?php echo SAVE;?></a></li>
			<li><a href="?restore=1" title="<?php echo RESTORE_FROM_BACKUP;?>"><i class="icon-ccw"></i> <?php echo RESTORE;?></a></li>
			<li><a href="?viewrules=1" title="<?php echo FORUM_RULES;?>"><i class="icon-bell"></i> <?php echo FORUM_RULES;?></a></li>
			<li><a href="index.php" title="<?php echo HOME;?>"><i class="icon-home"></i> <?php echo FORUMS;?></a></li>
<?php endif;?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'menu_admin.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'menu_admin.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'menu_admin.php');
		}
	}
	/**
	* AFFICHAGE DE LA LISTE DES CATEGORIES DE CONVERSATION (Forum home)
	*/
	public function showThreads() {
		$string =<<<END
<?php if (\$this->forumMode && \$this->cLogin) include(dirname(__FILE__).'/header.php');
 if (!\$this->forumMode && \$this->cLogin) include(dirname(__FILE__).'/header.php');
	\$t = \$this->setThreads();
	foreach (\$t['aMainPositions'] as \$id => \$c) {?>
	
		<h1><?php echo \$t['cats'][\$id]?></h1>
		<table id="topics">
			<thead>
				<tr class="info gradient">
					<th style="width:60%;"></th>
					<th style="width:5%; text-align:center;"><?php echo L_THREADS;?></th>
					<th style="width:5%; text-align:center;"><?php echo MESSAGES;?></th>
					<th style="width:30%;"><?php echo LAST_MSG;?></th>
					<?php if(\$this->isAdmin) :?><th style="width:5%"><?php echo ADMIN;?></th><?php endif; ?>

				</tr>
			</thead>
			<tbody>

 		<?php if (isset(\$t['aSubPositions'][\$id])) : 
 				foreach(\$t['aSubPositions'][\$id] as \$k => \$f) :?>

			<tr>
				<td>
					<p class="left">
						<?php \$this->setThreadTitle(\$t['threads'][\$id][\$f]['cat'],\$id,\$f,'read','unread')?>

					<br/>
						<?php \$this->setThreadTitleLegend(\$t['threads'][\$id][\$f]['subtitle'])?>

					</p>
				</td>
				<td class="mess"><?php echo count(\$t['nombreThreads'][\$f]);?></td>
				<td class="mess"><?php echo \$t['nombrePosts'][\$f];?></td>
				<td class="lastmsg">
					<i><?php echo L_ON;?> :</i> 
					<?php \$this->setThreadLastMsg(\$id,\$f,\$t['stats']['lastOn'][\$f]);?><br />
					<i><?php echo BY;?>:</i>
					<?php \$this->setThreadLastMsgBy(\$t['stats']['by'][\$f],'Lien');?>

				</td>
				<?php if(\$this->isAdmin) :?>

				<td class="admin">
					<a href="?thread=<?php echo \$id;?>&amp;delforum=<?php echo \$f;?>" onclick="return confirmLink(this,'<?php echo str_replace("'","\'",\$t['threads'][\$id][\$f]['cat'])?>'+'\n'+'<?php echo DEL_MSG_COMPLEMENT;?>');" rel="tooltip" title="<?php echo DEL_MSG;?>"><i class="icon-trash"></i></a>
				</td><?php endif;?>

			</tr>
		<?php endforeach;
		endif;?>
			
			</tbody>
		</table>
	<?php }
	if(\$this->isAdmin) :\$this->recThreadsForm();endif;
	if (\$this->forumMode && \$this->cLogin) include(dirname(__FILE__).'/footer.php');
	if (!\$this->forumMode && \$this->cLogin) include(dirname(__FILE__).'/footer.php');?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showThreads.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showThreads.php', $string);
		}
		if (!$this->forumMode && !$this->isMember && !$this->cLogin) {include(MU_THEMES.$this->theme.DS.'showThreads.php');}
	}
	/**
	* FORMULAIRE DE RÉPONSE
	*/
	public function recThreadsForm() {
		$string =<<<END
				<?php \$t = \$this->setThreads(); ?>

			<!-- Record form -->	
			<p><a class="btn btn-big" href="javascript:switchLayer('form');" title="formulaire"><?php echo EDIT?></a></p>
			<div class="toggle" id="form">	
			<div class="Box">
			<h2 class="forms-section"><?php echo ADD_MAIN_CAT?></h2>

				<form id="formulaire-main" action="index.php#bottom" method="post" class="forms forms-columnar">
					<input type="hidden" name="action" value="newmainthread" />
					<p><?php echo  Tools::input(MAIN_CAT_TITLE, 'newmaincat', '', 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p>
						<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo SEND?></button>
					</p>
				</form>
			</div>
			<div class="Box">		
			<h2 class="forms-section"><?php echo ADD_CAT?></h2>

				<form id="formulaire-cat" action="index.php#bottom" method="post" class="forms forms-columnar">
					<input type="hidden" name="action" value="newthread" />
					<p><?php echo  Tools::input(CAT_TITLE, 'newcat', '', 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p><?php echo  Tools::input(CAT_SUBTITLE, 'newcatsubtitle', '', 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p class="forms-inline"><?php echo Tools::select(MAIN_CAT_TITLE, 'newcatmaincat', \$t['cats'], '', false, '', true)?></p>
					<p>&nbsp;</p>
					<p>
						<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo SEND?></button>
					</p>
				</form>
			</div>
			<div class="Box">
			<h2 class="forms-section"><?php echo EDIT_MAIN_CAT?></h2>

				<?php foreach (\$t['cats'] as \$id => \$cat) {?>

				<form id="formulaire-editmain" action="index.php#bottom" method="post" class="forms forms-columnar">
					<input type="hidden" name="action" value="editmainthread" />
					<?php if (empty(\$t)) {?>

					<p><?php echo NO_CAT ?></p>
					<?php }?>

					<input type="hidden" name="id" value="<?php echo \$id ?>" />
					<input type="hidden" name="oldposition" value="<?php echo \$t['aMainPositions'][\$id] ?>" />
					<p><?php echo  Tools::input(MAIN_CAT_TITLE, 'editmaincat', \$cat, 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p><?php echo  Tools::input(MAIN_CAT_POSITION, 'editmaincatposition', \$t['aMainPositions'][\$id], 'text', '', '255' ,false,'small','','required',false,false,10)?></p>
					<p>
						<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo EDIT?></button>
					</p>
				</form>
				<p>&nbsp;</p>
				<hr/>
				<?php } ?>

			</div>
			<div class="Box">
			<h2 class="forms-section"><?php echo EDIT_CAT?></h2>
				<?php foreach (\$t['threads'] as \$id => \$main) {
					\$pos = array_flip(\$t['aSubPositions'][\$id]);
					foreach (\$t['aSubPositions'][\$id] as \$key => \$cat) {?>

				<form id="formulaire-edit" action="index.php#bottom" method="post" class="forms forms-columnar">
					<input type="hidden" name="action" value="editthread" />
					<?php if (empty(\$t)) {?>

					<p><?php echo NO_CAT ?></p>
					<?php }?>

					<input type="hidden" name="position" value="<?php echo \$pos[\$cat] ?>" />
					<input type="hidden" name="catid" value="<?php echo \$cat ?>" />
					<input type="hidden" name="maincatid" value="<?php echo \$id ?>" />
					<p><?php echo  Tools::input(CAT_TITLE, 'editcat', \$main[\$cat]['cat'], 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p><?php echo  Tools::input(CAT_SUBTITLE, 'editcatsubtitle', \$main[\$cat]['subtitle'], 'text', '', '255' ,false,'width-40','','required',false,false,100)?></p>
					<p><?php echo  Tools::input(CAT_POSITION, 'editcatposition', \$pos[\$cat], 'text', '', '255' ,false,'small','','required',false,false,10)?></p>
					<p class="forms-inline"><?php echo Tools::select(MAIN_CAT_TITLE, 'editcatmaincat', \$t['cats'], \$id, false, '', true)?></p>
					<p>&nbsp;</p>
					<p>
						<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo EDIT?></button>
					</p>
				</form>
				<p>&nbsp;</p>
				<hr/>
				<?php }
				} ?>
					
			</div>
			</div>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'recThreadsForm.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'recThreadsForm.php', $string);
		}
		if (!$this->install) {
			include(MU_THEMES.$this->theme.DS.'recThreadsForm.php');
		}
	}
	/**
	* AFFICHAGE DE LA LISTE DES SUJETS
	*/
	public function showTopics() {
		$string =<<<END
<?php \$topicList = \$this->setTopics('topicList');
if (\$topicList === false) {include(dirname(__FILE__).'/404.php');exit();}
if (!empty(\$_GET) || \$this->cLogin) include(dirname(__FILE__).'/header.php');?>
		<p class="pagination"><?php \$this->setTopics('pagination');?></p>

		<table id="topics">
			<thead>
				<tr class="info gradient">
					<th style="width:60%;"><?php echo TITLE_SUBJECT;?></th>
					<th style="width:5%; text-align:center;"><?php echo REPLIES;?></th>
					<th style="width:30%;"><?php echo LAST_MSG;?></th>
					<?php if(\$this->isAdmin) :?><th style="width:5%"><?php echo ADMIN;?></th><?php endif; ?>

				</tr>
			</thead>
			<tbody>

 		<?php foreach(\$topicList as \$t) :
			?>
			<tr>
				<td><p class="left"><?php \$this->setTopicList(\$t['postType'],'<i class="icon-pin"></i> ');?>
					<?php \$this->setTopicList(\$t['attachment'],'<i class="icon-paste"></i> ');?>
					<?php \$this->setTopicIcon(\$t,'<i class="icon-folder-open-empty"></i>','<i class="icon-comment-empty" title="'.UNREAD_MSG.'"></i>');
					\$this->setTopicTitle(\$t,'read','unread')?>

					</p>
					<span class="image-right"><?php \$this->setTopicStartonBy(\$t);\$this->setTopicPrivate(\$t,'Lien');?></span>
				</td>
				<td class="mess"><?php echo \$t['nombrePosts']-1;?></td>
				<td class="lastmsg">
					<i><?php echo L_ON;?> :</i> 
					<?php \$this->setTopicLastMsg(\$t);?><br />
					<i><?php echo BY;?>:</i>
					<?php \$this->setTopicLastMsgBy(\$t,'Lien');?>

				</td>
				<?php if(\$this->isAdmin) :?>

				<td class="admin">
					<a href="?viewforum=<?php echo \$this->get_topics ?>&amp;topic=<?php echo \$t['topicID'];?>&amp;delpost=<?php echo \$t['topicID'];?>" onclick="return confirmLink(this,'<?php echo \$t['titre'];?>');" rel="tooltip" title="<?php echo DEL_MSG;?>"><i class="icon-trash"></i></a>
				</td><?php endif;?>

			</tr>
		<?php endforeach;?>
			
			</tbody>
		</table>
		<p class="pagination"><?php \$this->setTopics('pagination');?></p>

		<form action="index.php" method="post">
			<input type="hidden" name="action" value="jumpto" />
			<p class="forms-inline"><?php echo Tools::select(JUMP_TO, 'viewforum', \$this->setOptions(), \$this->get_topics, false, '', true)?>
				<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo GO?></button>
			</p>
		</form>
		<p>&nbsp;</p>

	   <?php if (!empty(\$_GET) || \$this->cLogin) {\$this->setTopics('reply'); include(dirname(__FILE__).'/footer.php');} ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showTopics.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showTopics.php', $string);
		}
	}
	/**
	* AFFICHAGE DE LA DISCUSSION
	*/
	public function showPosts() {
		$string =<<<END
<?php if(\$topic = \$this->setPost()):
	include(dirname(__FILE__).'/header.php');?>

			<p><?php echo \$topic->pagination; ?></p>
			<div class="gradient">
			<?php if(\$this->isAdmin || \$this->cLogin == \$topic->auth):?>

				<form action="index.php?viewforum=<?php echo \$this->get_topics ?>&amp;topic=<?php \$this->topicId()?>" name="sub" method="post" class="forms-inline" id="form-title">
					<input type="hidden" name="topicID" value="<?php \$this->topicId()?>" />
					<?php if(\$this->isAdmin): ?>

					<i class="icon-pin"></i> 
					<input id="check-modif-title" type="checkbox" onclick="<?php \$this->pinned(\$topic);?>"/> 
					<?php endif; ?>
					
					<input type="text" value="<?php \$this->postTitle(\$topic) ?>" size="40" name="ntitle" /> 
					<button type="submit" class="btn btn-blue"><i class="icon-pencil"></i>&nbsp;<?php echo EDIT_TITLE?></button>
				</form>
			<?php else: \$this->postTitle(\$topic);endif;?>
			
			</div>
			<?php 
			\$cnt=0;
			while(\$reply=\$topic->nextReply()) :
				if(\$this->members->isMember(\$reply->auth)) :
					\$mb=\$this->members->getMember(\$reply->auth);
					//Tooltip ?>
					
					<div class="tooltip" id="<?php echo Tools::cleanUser(\$reply->auth)?>">
						<table style="width: 100%">
							<tr><th class="formTD"><?php echo PROFIL_OF?> <b><?php echo \$reply->auth?></b></th><th></th></tr>
							<tr><td class="formTD"><?php echo REGISTRED_ON?></td><td class="tooltipTD"><?php \$this->dateOfAction(\$mb)?></td></tr>
							<tr><td class="formTD"><?php echo EMAIL?></td><td class="tooltipTD"><?php \$this->email(\$mb)?></td></tr>
						<?php if(!empty(\$mb->url)) ?><tr><td class="formTD"><?php echo WEBSITE?></td><td class="tooltipTD"><?php echo \$mb->url?></td></tr>
						<?php if(!empty(\$mb->birthday))?><tr><td class="formTD"><?php echo BORN_ON?></td><td class="tooltipTD"><?php echo \$mb->birthday?> <span class="badge"><?php echo MuDate::birthday(\$mb->birthday)?> <?php echo YEARS_OLD?></span></td></tr>
						<?php if(!empty(\$mb->quote)) ?><tr><td class="formTD"><?php echo SIGNATURE?></td><td class="tooltipTD"><?php echo \$mb->quote?></td></tr>
						</table>
					</div>
					<table class="width-100 table-bordered" id="p-<?php echo \$reply->time?>">
						<tr>
					<?php if(\$this->members->isMember(\$reply->auth)) :?>
						
							<td class="avatarTD" rowspan="2">
								<div class="mb-infos mb-name"><a class="LienNonLu" href="?private=<?php echo \$reply->auth?>" title="<?php echo SEND_PRIVATE_MSG?>"><?php echo \$reply->auth?></a></div>
								<a onmouseover="showWMTT('<?php echo \$reply->auth?>')" onmouseout="hideWMTT()" href="?private=<?php echo \$reply->auth?>" title=""><?php echo \$this->pic(\$mb,'avatar')?></a>
								
								<div><span class="label label-<?php echo \$this->statut(\$mb)->class?>"><?php echo \$this->statut(\$mb)->title?></div>
					<?php else :?>
						
							<td class="avatarTD" rowspan="2"><?php echo \$this->pic(\$mb,'avatar')?>
								<div class="mb-infos"><?php echo \$reply->auth?></div>
					<?php endif;
					if(!empty(\$mb->url)) :?>

								<div class="mb-infos"><i class="icon-globe"></i>&nbsp;<a href="<?php echo \$mb->url?>" onclick="window.open(this.href);return false;" title="<?php echo \$mb->url?>"><?php echo WEBSITE?></a></div>
					<?php endif; ?>

								<div class="mb-infos"><?php echo MESSAGES.' : '.\$mb->post?></div>
							<td>
								<div class="datePost"><a href="?topic=<?php echo \$this->get_topic?>&amp;page=<?php echo \$this->page.'#p-'.\$reply->time?>"><?php \$this->dateOfAction(\$reply)?></a></div>
							</td>
						</tr>
						<tr>
							<td class="messageTD">
								<div id="td<?php echo \$cnt?>"><?php echo BBCHelper::decode(\$reply->content)?></div>
					<?php if(\$this->quoteMode) :?>

								<div class="signature"><blockquote><p><?php echo \$mb->quote?></p></blockquote></div>
					<?php endif; ?>

							</td>
						</tr>
						<tr class="tr-bottom">
							<td>
								<?php if(!empty(\$this->cLogin) && !\$this->forumMode): ?>
								<a href="?viewforum=<?php echo \$this->get_topics ?>&amp;topic=<?php echo \$this->get_topic?>&amp;replypost=<?php echo \$reply->time?>&amp;page=<?php echo \$this->page?>#form" class="btn btn-small btn-orange" title="<?php echo QUOTE_MSG_FROM.' '.\$reply->auth?>" /><i class="icon-chat-empty"></i> <?php echo QUOTE?></a>
								<?php endif; ?>

							</td>
							<td>
					<?php if(\$this->isAdmin || (\$this->cLogin == \$reply->auth)) :?>
						
								<a class="btn btn-small" href="?viewforum=<?php echo \$this->get_topics ?>&amp;topic=<?php echo \$this->get_topic?>&amp;editpost=<?php echo \$reply->time?>&amp;page=<?php echo \$this->page?>" title="<?php echo EDIT?>">
									<i class="icon-pencil"></i> <?php echo EDIT?>
								</a>&nbsp;<a class="btn btn-small btn-red" href="?viewforum=<?php echo \$this->get_topics ?>&amp;topic=<?php echo \$this->get_topic?>&amp;delpost=<?php echo \$reply->time?>&amp;page=<?php echo \$this->page?>" title="<?php echo DEL?>" onclick="return confirmLink(this,'<?php \$this->delMsg(\$cnt,\$reply->auth)?>')">
									<i class="icon-trash"></i> <?php echo DEL?>
								</a>&nbsp;<a class="btn btn-small" href="javascript:switchLayer('form');" title="<?php echo ANSWER?>">
									<i class="icon-megaphone"></i> <?php echo ANSWER?>
								</a>
					<?php endif;	
					if(\$this->cLogin && \$this->cLogin != \$reply->auth && !\$this->isAdmin) :?>
						
								</a>&nbsp;<a class="btn btn-small" href="javascript:switchLayer('form');" title="<?php echo ANSWER?>">
									<i class="icon-megaphone"></i> <?php echo ANSWER?>
								</a>
					<?php endif;	
					if(!empty(\$reply->attach)) \$this->downloadAttach(\$reply->attach,'image-right')?>

							</td>
						</tr>
					</table>
			<?php \$cnt++;
				endif;
			endwhile?>
			<p><?php echo \$topic->pagination; ?></p>
			<?php if(!empty(\$this->cLogin)) : echo \$this->setReplyForm('newpost');endif;
		else:
		header("HTTP/1.0 404 Not Found");
		include(dirname(__FILE__).'/header.php');?>
			
			<div class="message">
				<span class="close"></span>
				<strong><?php echo TOPIC_UNKNONW?></strong>
			</div>
		<?php endif;
		include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showPosts.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showPosts.php', $string);
		}
	}
	/**
	* LISTE DES MEMBRES
	*/
	public function showMemberlist() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
		\$ml = \$this->setMemberList();?>

		<p class="pagination"><?php echo \$ml['pagination'] ?></p>
		<form action="index.php?searchMember" method="post" autocomplete="off" class="text-right">
			<input type="text" name="searchMember" placeholder="<?php echo SEARCH?>">
			<button type="submit" class="btn btn-blue"><i class="icon-right-hand"></i></button>
		</form>
		<table id="topics">
			<thead>
				<tr class="info gradient">
					<th style="width:15%;"><?php echo MEMBER?></th>
					<th style="width:20%;"><?php echo REGISTRED_ON?></th>
					<th style="width:<?php echo \$ml['wd']?>%;"><?php echo SIGNATURE?></th>
					<th style="width:13%;"><?php echo BIRTH?></th>
					<th style="width:12%;" class="admin"><?php echo EMAIL_URL?></th>
		<?php if(\$this->isAdmin){?>
			<th colspan="2" style="width:15%;" class="admin"><?php echo ADMIN?></th><?php } ?>

				</tr>
			</thead>
			<tbody>
		<?php foreach(\$this->members->listMember(\$this->nbrMb,\$this->page,false) as \$m) {
			\$mb = \$this->setMbOfList(\$m);?>

				<tr>
					<td class="center">
						<?php echo \$mb->avatar; ?>
			<?php if(\$m != \$this->cLogin) {?>

						<a class="Lien" href="?private=<?php echo \$m?>" title="<?php echo SEND_PRIVATE_MSG?>"><?php echo \$m?></a>
			<?php }else{?>

						<a class="Lien" href="#" onclick="return false;"><?php echo \$m?></a>
			<?php } ?>
					</td>
					<td><?php \$this->dateOfAction(\$mb)?></td>
					<td><?php echo \$mb->signature?></td>
					<td><?php echo \$mb->birthday?></td>
					<td style="text-align:center;"><?php echo \$mb->mail.'&nbsp;&nbsp;'.\$mb->url?></td>
			<?php 
			if(\$this->isAdmin) {
				if(\$mb->mod) {
					if(\$m==\$this->cLogin || \$mb->mod==2) {
						\$str=(\$mb->mod>1)? ADMIN:MODO;?>

					<td>&nbsp;</td>
					<td>
						<form>
							<input type="checkbox" checked="checked" onclick="this.checked='checked';"/>
							<?php echo \$str?>!
						</form>
					</td>
			<?php 	} else {?>
				
					<td class="admin">
						<a href="?memberlist=1&amp;deluser=<?php echo \$m?>" onclick="return confirmLink(this,'<?php echo \$m?>')" title="<?php echo DEL_THIS_USER?>"><i class="icon-trash"></i></a>
					</td>
					<td>
						<form>
							<input type="checkbox" checked="checked" onclick="window.location='?memberlist=1&amp;switchuser=<?php echo \$m?>';" /> 
							<?php echo MODO?>?
						</form>
					</td>
			<?php 	}
				} else {?>

					<td class="admin">
						<a href="?memberlist=1&amp;deluser=<?php echo \$m?>" onclick="return confirmLink(this,'<?php echo \$m?>')" title="<?php echo DEL_THIS_USER?>"><i class="icon-trash"></i></a>
					</td>
					<td>
						<form>
							<input type="checkbox" onclick="window.location='?memberlist=1&amp;switchuser=<?php echo \$m?>';"/> 
							<?php echo MODO?>?
						</form>
					</td>
			<?php }
			}?>
			
			</tr>
			<?php 
		}?>

			</tbody>
		</table>
		<p class="pagination"><?php echo \$ml['pagination'] ?></p>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showMemberlist.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showMemberlist.php', $string);
		}
	}
	/**
	* RECHERCHE D'UN MEMBRE
	*/
	public function searchMember() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
		\$mb = \$this->setSearchMb();
		?>
		
		<form action="index.php?searchMember" method="post" autocomplete="off" class="text-right">
			<input type="text" name="searchMember" placeholder="<?php echo SEARCH?>">
			<button type="submit" class="btn btn-blue"><i class="icon-right-hand"></i></button>
		</form>
		<table id="topics">
			<thead>
				<tr class="info gradient">
					<th style="width:15%;"><?php echo MEMBER?></th>
					<th style="width:20%;"><?php echo REGISTRED_ON?></th>
					<th style="width:<?php echo \$mb->wd?>%;"><?php echo SIGNATURE?></th>
					<th style="width:13%;"><?php echo BIRTH?></th>
					<th style="width:12%;"><?php echo EMAIL?></th>
				<?php if(\$this->isAdmin) {?><th colspan="2" style="width:15%;"><?php echo ADMIN?></th><?php } ?>
					
				</tr>
			</thead>
				<tr>
			<?php 
			if(count(\$mb->closest) == 1) {?>

					<td class="center">
						<?php echo \$mb->avatar; ?>
			<?php if(\$this->searchMember != \$this->cLogin){?>

						<a class="Lien" href="?private=<?php echo \$this->searchMember?>" title="<?php echo SEND_PRIVATE_MSG?>"><?php echo \$this->searchMember?></a> 
			<?php }else{?>
				
						<a class="Lien" href="#" onclick="return false;"><?php echo \$this->searchMember?></a>
			<?php } ?>

					</td>
					<td><?php \$this->dateOfAction(\$mb)?></td>
					<td><?php echo \$mb->signature?></td>
					<td><?php echo \$mb->birthday?></td>
					<td class="center"><?php echo \$mb->mail.' '.\$mb->url?></td>
			<?php
			if(\$this->isAdmin) {
				if(\$mb->mod) {
					if(\$mb==\$this->cLogin || \$mb->mod==2) {
						\$str=(\$mb->mod>1)? ADMIN:MODO;?>
					
					<td>&nbsp;</td>
					<td>
						<form>
							<input type="checkbox" checked="checked" onclick="this.checked='checked'"/><?php echo \$str?>!
						</form>
					</td>
				<?php } else {?>
					
					<td class="admin">
						<a href="?memberlist=1&amp;deluser=<?php echo \$this->searchMember?>" onclick="return confirmLink(this,'<?php echo \$this->searchMember?>')" title="<?php echo DEL_MEMBER?>">
							<i class="icon-trash"></i>
						</a>
					</td>
					<td>
						<form>
							<input type="checkbox" checked="checked" onclick="window.location='?memberlist=1&amp;switchuser=<?php echo \$this->searchMember?>;" /> <?php echo MODO?>?</form></td>
				<?php }
				} else {?>

					<td class="admin">
						<a href="?memberlist=1&amp;deluser=<?php echo \$this->searchMember?>" onclick="return confirmLink(this,'<?php echo \$this->searchMember?>')" title="<?php echo DEL_THIS_USER?>">
							<i class="icon-trash"></i>
						</a>
					</td>
					<td>
						<form>
							<input type="checkbox" checked="checked" onclick="window.location='?memberlist=1&amp;switchuser=<?php echo \$this->searchMember?>;'"/> <?php echo MODO?>?</form></td>
				<?php }
			}?>
				
				</tr>
		<?php } else {?>
			
				<tr>
					<td colspan="<?php echo ((\$this->isAdmin) ? '7' : '5')?>" class="noresult">
						<?php echo NO_RESULT.'.&nbsp;'.DID_YOU_MEAN;?>
						<ul>
						<?php foreach (\$mb->closest as \$key => \$membre) {?>

							<li><?php echo \$membre->avatar; ?>&nbsp;<a href="<?php echo MU_BASE_URL?>?searchMember=<?php echo \$membre->name?>"><?php echo \$membre->name?></a> ?</li>
						<?php }?>

						</ul>
					</td>
				</tr>

		<?php }?>
		
		</table>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'searchMember.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'searchMember.php', $string);
		}
	}
	/**
	* AFFICHE LA FENÊTRE DES MESSAGES PRIVÉS
	*/
	public function showPrivateMsg() {
		$string =<<<END

		<div class="Box">
			<a class="toggleLink" href="javascript:switchLayer('privatebox');" title="<?php echo PRIVATE_MSG?>"><?php echo PRIVATE_INBOX?></a>
			<div class="toggle" id="privatebox">
				<?php \$this->setPrivateMsg();?>
				<a href="?delprivate=1" class="btn btn-red image-right"><i class="icon-trash"></i> <?php echo EMPTY_MAILBOX?></a><p/>
			</div>
		</div>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showPrivateMsg.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showPrivateMsg.php', $string);
		}
	}
	/**
	* FORMULAIRE DE RÉPONSE
	*/
	public function replyForm() {
		$string =<<<END
				<?php if(\$this->cLogin) :?>
		<!-- Reply form -->
		<?php if(\$r->edit || \$r->show) {?>
			
			<h4 class="forms-section"><?php echo \$r->name?></h4>
			<div class="Box">
		<?php } else {?>
			
			<p><a class="btn btn-big" href="javascript:switchLayer('form');" title="formulaire"><?php echo \$r->name?></a></p>
			<div class="toggle" id="form"<?php if(\$this->showform) {echo ' style="display:block;visibility:visible;"';} ?>>
		<?php }?>
			
				<br />
				<form id="formulaire" action="index.php#bottom" method="post" enctype="multipart/form-data" class="forms forms-columnar">
					<input type="hidden" name="action" value="<?php echo \$type?>" />
					<input type="hidden" name="page" value="<?php echo \$this->page?>" />
					<input type="hidden" name="viewforum" value="<?php echo \$this->get_topics?>" />
		<?php // Réponse
		if(\$type== 'newpost' || \$r->edit) {?><input type="hidden" name="topicID" value="<?php echo \$this->get_topic?>" />
		<?php // Mesage privé
		}if(\$mpTo){?><input type="hidden" name="mpTo" value="<?php echo \$mpTo?>" />
		<?php // Edition
		}if(\$r->edit){?><input type="hidden" name="postID" value="<?php echo \$this->get_editpost?>" />
		<?php // Nouveau Sujet
		}if(\$type== 'newtopic') {?>
				<p><?php echo  Tools::input(TITLE_SUBJECT, 'titre', '', 'text', '', '','','width-40','','',false,false,100)?></p>
				<?php if(\$this->isAdmin) {?><p><label for="postit"><i class="icon-pin"></i> <?php echo PINNED?></label>
				<input type="checkbox" id="postit" name="postit" value="1"></p><?php } ?>

		<?php } if(!\$this->cLogin){?><p><?php echo Tools::input(USER_MENDATORY, 'anonymous', '', 'text', '', '','','width-40')?></p>
		    <?php }echo \$this->formattingHelp();if(\$r->edit) {?>

				<p class="forms-inline"><?php echo Tools::textarea(MESSAGE, 'message', \$reply->content, '40', '10', '', '', '', 'width-70')?></p>
		<?php } else {?>

				<p class="forms-inline"><?php echo Tools::textarea(MESSAGE, 'message', (\$this->quote != null ? \$this->quote : ''), '40', '10', '', '', '', 'width-70')?></p>
		<?php } if(\$r->join) {?><p><?php echo Tools::input(ATTACH_FILE, 'attachment', '', 'file', '', '','','btn')?></p><?php }?>
		
				<p>
					<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i> <?php echo SEND?></button>
				</p>
			</form>
		</div>
		<?php endif; ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'replyForm.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'replyForm.php', $string);
		}
	}
	/**
	* FORMULAIRE DE RESTAURATION DE LA SAUVEGARDE
	*/
	public function frestore() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');?>
		<!-- Edit config form -->
		<div class="Box">
			<h4 class="forms-section"><?php echo RESTAURATION_FORUM?></h4>
			<div style="padding-top:10px">
				<form action="index.php" method="post" enctype="multipart/form-data" class="forms forms-columnar">
					<input type="hidden" name="restore" value="1" />
					<input type="hidden" name="action" value="restore" />
					<p class="forms-inline"><?php echo Tools::input(UPLOAD_BACKUP, 'backup', '', 'file', '', '','','width-40')?></p>
					<p><button type="submit" class="text-right btn btn-green"><i class="icon-right-hand"></i>&nbsp;<?php echo SEND?></button></p>
				</form>
			</div>
		</div>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'frestore.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'frestore.php', $string);
		} 
	}
	/**
	* ÉDITION DE LA CONFIGURATION
	*/
	function editConf() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
		\$this->searchDirFile();
		\$this->searchDirFile(MU_LANG,'file');
		if(!\$wtp=@file_get_contents(MU_THREAD.'welcome.txt')) \$wtp=Tools::clean(BBCHelper::parse(WELCOME_TXT));
		if(!\$rules=@file_get_contents(MU_THREAD.'rules.txt')) \$rules='';?>
		
		<!-- Edit config form -->
		<h2 class="forms-section"><?php echo CONFIG_OPTIONS?></h2>
		<div style="padding-top:10px;">
			<?php if (SECURITY_SALT == 'DSKQJfmi879fdiznKSDJ56SD8734QRer980ZOIDQ' && \$this->isAdmin) {?>
				<p class="warning"><?php echo CHANGE_SECURITY_SALT?></p>
			<?php }
				if (CAPTCHA == 'captcha' && \$this->isAdmin) {?>
				<p class="warning"><?php echo CHANGE_CAPTCHA_DIR_NAME?></p>
			<?php }?>
			<form action="index.php" method="post" enctype="multipart/form-data" class="forms forms-columnar">
				<input type="hidden" name="action" value="editoption" />
				<p class="forms-inline">
					<label><?php echo TITLE_LOGO?></label>
					<input type="text" name="uftitle" maxlength="60" value="<?php echo Tools::clean(\$this->uforum)?>" class="width-30" />
					&nbsp;<input type="file" name="attachment" class="width-40" />
				</p>
				<p class="forms-inline">
					<label><?php echo SUBTITLE?></label>
					<input type="text" name="ufsubtitle" maxlength="60" value="<?php echo Tools::clean(\$this->subtitle)?>" class="width-30" />
				</p>
				<p class="forms-inline">
					<label><?php echo NAME_AND_URL?></label>
					<input type="text" name="ufsitename" value="<?php echo Tools::clean(\$this->siteName)?>" placeholder="µForum" class="width-30" />
					&nbsp;<input type="url" maxlength="80" name="ufsite" value="<?php echo \$this->siteUrl?>" placeholder="http://…" class="width-40" />
				</p>
			    <p class="forms-inline"><?php echo Tools::textarea(META_DESCRIPTION, 'ufmetadesc', Tools::clean(\$this->metaDesc), '10', '2', 'Lightweight bulletin board without sql', '150', '', 'meta-desc')?></p>
			    <p class="forms-inline"><?php echo Tools::select(THEME, 'theme', \$this->aThemes, \$this->theme, false, '', true)?></p>
			    <p class="forms-inline"><?php echo Tools::select(CONFIG_GZIP, 'gzip', array('1'=>L_YES,'0'=>L_NO), \$this->gzip, false, '', true)?></p>
			    <p class="forms-inline"><?php echo Tools::input(INDEX_MAX_MSG, 'nbmess', \$this->nbrMsgIndex, 'number', '', '2', '', 'width-10')?></p>
			    <p class="forms-inline"><?php echo Tools::input(TOPIC_MAX_MSG, 'nbmessTopic', \$this->nbMsgTopic, 'number', '', '2', '', 'width-10')?></p>
			    <p class="forms-inline"><?php echo Tools::input(MEM_MAX_DISP, 'nbmb', \$this->nbrMb, 'number', '', '2', '', 'width-10')?></p>
			    <p class="forms-inline"><?php echo Tools::select(LANGUAGE, 'uflang', \$this->aLang, \$this->lang, false, '', true)?></p>
			    <p class="forms-inline"><?php echo Tools::input(MAX_AVATAR_WEIGHT, 'maxav', (\$this->maxAvatarSize/1024), 'number', '', '10', '', 'width-10', 'resize-small')?></p>
			     <p class="forms-inline"><?php echo Tools::input(ALLOWED_EXT, 'exts', Tools::clean(\$this->extStr), 'text', '', '50', '', 'width-40')?></p>
				<p>
					<label><?php echo PRIVATE_MODE?></label>
					<input name="fmode" type="checkbox" <?php echo \$this->forumMode?'checked="checked" ':''?>/>
				</p>
				<p>
					<label><?php echo SHOW_SIGNATURES?></label>
				<input name="qmode" type="checkbox" <?php echo \$this->quoteMode?'checked="checked" ':''?>/>
				</p>
					<?php echo \$this->formattingHelp()?>
				<p class="forms-inline"><?php echo Tools::textarea(WELCOME_MSG, 'message', \$wtp, '40', '20', '', '', '', 'width-70')?></p>
					<?php echo \$this->formattingHelp('rules')?>
				<p class="forms-inline"><?php echo Tools::textarea(FORUM_RULES, 'rules', \$rules, '40', '20', '', '', '', 'width-70')?></p>
				<p class="text-right">
					<button type="submit" class="btn btn-green"><i class="icon-right-hand"></i>&nbsp;<?php echo REC?></button>
				</p>
			</form>
		</div>
<?php include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'editConf.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'editConf.php', $string);
		}
	}
	/**
	* FORMULAIRE DE RESTAURATION DE LA SAUVEGARDE
	*/
	public function showRules() {
		$string =<<<END
<?php include(dirname(__FILE__).'/header.php');
\$this->rulesText();
include(dirname(__FILE__).'/footer.php'); ?>
END;
		if (!is_file(MU_THEMES.$this->theme.DS.'showRules.php')) {
			file_put_contents(MU_THEMES.$this->theme.DS.'showRules.php', $string);
		} 
	}
}
/**
* INITIALISATION
*/
header('Content-Type: text/html; charset='.CHARSET);
new Template();
?>
