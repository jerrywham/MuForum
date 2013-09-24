<?php
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of µForum project: http://uforum.byethost5.com
#
# @update     23-09-2013
# @copyright  2011-2013  Frédéric Kaplon, Cyril MAGUIRE and contributors
# @copyright   ~   2008  Okkin  Avetenebrae
# @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
# @link       http://uforum.byethost5.com   µForum
# @version    Release: @package_version@
#
# ------------------- END LICENSE BLOCK -------------------
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

# ---------------- DEFINITIONS ----------------------------
# Définition des constantes d'adressage
define('DS', DIRECTORY_SEPARATOR);
define('MU_ROOT', dirname(__FILE__).DS);
define('MU_BASE_URL',Tools::baseURL());
define('GOTO_INDEX','<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0; URL='.MU_BASE_URL.'"></head></html>');
/**
 * Configuration
 */
define('LANG','fr');
define('STYLE_COLOR','default');
define('PROJECT','µForum');
//A changer à l'installation pour plus de sécurité puis ne plus y toucher
define('SECURITY_SALT','DSKQJfmi879fdiznKSDJ56SD8734QRer980ZOIDQ');
/**
*
* Déclaration des répertoires
*/
define('MU_DATA', MU_ROOT.'data'.DS);
define('MU_THREAD', MU_DATA.'messages'.DS);
define('MU_MEMBER', MU_DATA.'membres'.DS);
define('MU_URL_MEMBER', MU_BASE_URL.'upload/');
define('MU_BACK', MU_ROOT.'backup'.DS);

define('MU_LANG', MU_ROOT.'lang'.DS);

define('MU_JS', MU_ROOT.'js'.DS);
define('MU_CSS', MU_ROOT.'css'.DS);
define('MU_IMG', MU_ROOT.'img'.DS);
define('MU_UPLOAD', MU_ROOT.'upload'.DS);

/**
*
* Fixe les date en Français
*/
setlocale(LC_TIME, 'fr_FR.utf8','fra');
date_default_timezone_set('Europe/Paris');
/*
** Version de µForum
*/
define('VERSION','0.9.8.alpha');

$trademarkBlock = '
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of µForum project: http://uforum.byethost5.com
#
# @update     '.date('Y-m-d').'
# @copyright  2013  Frédéric Kaplon, Cyril MAGUIRE and contributors
# @copyright  2011-2013  Frédéric Kaplon and contributors
# @copyright   ~   2008  Okkin  Avetenebrae
# @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
# @link       http://uforum.byethost5.com   µForum
# @version    Release: @package_version@
#
# ------------------- END LICENSE BLOCK -------------------
';
define('TM',$trademarkBlock);

# ---------------- TRADUCTION ----------------------
Tools::mklang();
if(!file_exists(MU_LANG.LANG.'.php')) {
	require MU_LANG.'fr.php';
} else {
	require MU_LANG.LANG.'.php';
}
foreach($LANG as $key => $value) {
	if(!defined($key)) define($key,$value);
}
/**
*
* Vérification de la version de php
*/
if (version_compare(PHP_VERSION, '5.3', '<')) {
    exit(PHP_VERIF);
}

# ---------------- FONCTION DE BANNISSEMENT ---------------
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# Copyright (c) 2013 SebSauvage
# See http://sebsauvage.net/paste/?36dbd6c6be607e0c#M5uR8ixXo5rXBpXx32gOATLraHPffhBJEeqiDl1dMhs
#
# Instructions d'utilisation:
# • Faites un require_once de ce script.
# • à l'endroit où vous testez la validité du mot de passe:
#     • Si ban_canLogin()==false, l'utilisateur est banni. Ne testez même pas le mot de passe: Rejetez l'utilisateur.
#    • Si ban_canLogin()==true, vérifiez le mot de passe.
#          • Si le mot de passe est ok, appelez ban_loginOk(), sinon appelez ban_loginFailed()
# La lib s'occupe de compter le nombre d'échecs et de gérer la durée de bannissement 
# (bannissement/levée de ban).
# Cette lib créé un sous-répertoire "data" qui contient les données de bannissement 
# (ipbans.php) et un log de connexion (log.txt).
#
# Exemple
#        if (!ban_canLogin()) { $pass=false; }
#        if($pass){ ban_loginOk(); echo connect("success",array("username"=>$this->username)); }
#        else{ ban_loginFailed(); echo connect("error","Incorrect Username or Password"); }
# ------------------- END LICENSE BLOCK -------------------

# date_default_timezone_set('Europe/Paris');
$GLOBALS['config']['DATADIR'] = MU_DATA.'ban'; // Data subdirectory
$GLOBALS['config']['IPBANS_FILENAME'] = $GLOBALS['config']['DATADIR'].'/ipbans.php'; // File storage for failures and bans.
$GLOBALS['config']['BAN_AFTER'] = 3; // Ban IP after this many failures.
$GLOBALS['config']['BAN_DURATION'] = 1800; // Ban duration for IP address after login failures (in seconds) (1800 sec. = 30 minutes)
if (is_dir(MU_DATA) && !is_dir($GLOBALS['config']['DATADIR'])) { mkdir($GLOBALS['config']['DATADIR'],0705); chmod($GLOBALS['config']['DATADIR'],0705); }
if (is_dir(MU_DATA) && !is_file($GLOBALS['config']['DATADIR'].'/.htaccess')) { file_put_contents($GLOBALS['config']['DATADIR'].'/.htaccess',"Allow from none\nDeny from all\n"); } // Protect data files.

function logm($message)
{
    $t = strval(date('Y/m/d_H:i:s')).' - '.$_SERVER["REMOTE_ADDR"].' - '.strval($message)."\n";
    file_put_contents($GLOBALS['config']['DATADIR'].'/log.txt',$t,FILE_APPEND);
}

// ------------------------------------------------------------------------------------------
// Brute force protection system
// Several consecutive failed logins will ban the IP address for 30 minutes.
if (is_dir(MU_DATA) && !is_file($GLOBALS['config']['IPBANS_FILENAME'])) {
	file_put_contents($GLOBALS['config']['IPBANS_FILENAME'], "<?php\n\$GLOBALS['IPBANS']=".var_export(array('FAILURES'=>array(),'BANS'=>array()),true).";\n?>");
	include $GLOBALS['config']['IPBANS_FILENAME'];
} else {
	$GLOBALS['IPBANS']=array('FAILURES'=>array(),'BANS'=>array());
}

// Signal a failed login. Will ban the IP if too many failures:
function ban_loginFailed()
{
    $ip=$_SERVER["REMOTE_ADDR"]; $gb=$GLOBALS['IPBANS'];
    if (!isset($gb['FAILURES'][$ip])) $gb['FAILURES'][$ip]=0;
    $gb['FAILURES'][$ip]++;
    if ($gb['FAILURES'][$ip]>($GLOBALS['config']['BAN_AFTER']-1))
    {
        $gb['BANS'][$ip]=time()+$GLOBALS['config']['BAN_DURATION'];
        logm('IP address banned from login');
    }
    $GLOBALS['IPBANS'] = $gb;
    file_put_contents($GLOBALS['config']['IPBANS_FILENAME'], "<?php\n\$GLOBALS['IPBANS']=".var_export($gb,true).";\n?>");
}

// Signals a successful login. Resets failed login counter.
function ban_loginOk()
{
    $ip=$_SERVER["REMOTE_ADDR"]; $gb=$GLOBALS['IPBANS'];
    unset($gb['FAILURES'][$ip]); unset($gb['BANS'][$ip]);
    $GLOBALS['IPBANS'] = $gb;
    file_put_contents($GLOBALS['config']['IPBANS_FILENAME'], "<?php\n\$GLOBALS['IPBANS']=".var_export($gb,true).";\n?>");
    logm('Login ok.');
}

// Checks if the user CAN login. If 'true', the user can try to login.
function ban_canLogin()
{
    $ip=$_SERVER["REMOTE_ADDR"]; $gb=$GLOBALS['IPBANS'];
    if (isset($gb['BANS'][$ip]))
    {
        // User is banned. Check if the ban has expired:
        if ($gb['BANS'][$ip]<=time())
        { // Ban expired, user can try to login again.
            logm('Ban lifted.');
            unset($gb['FAILURES'][$ip]); unset($gb['BANS'][$ip]);
            file_put_contents($GLOBALS['config']['IPBANS_FILENAME'], "<?php\n\$GLOBALS['IPBANS']=".var_export($gb,true).";\n?>");
            return true; // Ban has expired, user can login.
        }
        return false; // User is banned.
    }
    return true; // User is not banned.
}
# ------------ FIN FONCTION DE BANNISSEMENT -----------
# ---------------- CLASSES STATIQUES-------------------
/**
 * Outils
 */
class Tools
{
	/**
	*
	* NETTOIE LES NOMS D'UTILISATEURS
	*/
	public static function cleanUser($str,$charset='utf-8') 
	{
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
			$str = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $str);
		    return $str;
	}
	/**
	*
	* SUPPRIME LES CARACTERES SPÉCIAUX
	*/
	public static function removeAccents($str,$charset='utf-8') 
	{
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
			$str = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $str);
		    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml|uro)\;#', '\1', $str);
		    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); # pour les ligatures e.g. '&oelig;'
		    $str = preg_replace('#\&[^;]+\;#', '', $str); # supprime les autres caractères
		    return $str;
	}
	/**
	*
	* RETOURNE L'URL de base
	*/
	public static function baseURL()
	{
		$dir = dirname($_SERVER['SCRIPT_NAME']);
		$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')?	'https://' : "http://";
		$servername = $_SERVER['HTTP_HOST'];
		$serverport = (preg_match('/:[0-9]+/', $servername) OR $_SERVER['SERVER_PORT'])=='80' ? '' : ':'.$_SERVER['SERVER_PORT'];
		$racine = rtrim($protocol.$servername.$serverport.$dir, '/').'/';
		return $racine;
	}
	/**
	*
	* RETOURNE LES PARAMETRES DE L'URL
	*/
	public static function getURLParams()
	{
		$params = false;
		if (!empty($_GET)) {
			foreach ($_GET as $key => $value) {
				$params .= $key.'='.$value.'&';
			}
			$params = substr($params,0,-1);
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
	/**
	* Méthode de debugage
	* 
	* @param var  la variable à analyser
	* @param html bool affiche la sortie au format html ou modifie les balises html par leur équivalent unicode
	* @param return bool affiche ou non le résultat qui peut ainsi être récupéré dans une variable
	* @param sub integer affiche le résultat entre des balises pre
	* @param way string sens de lecture du tableau debug_backtrace
	* @return string le résultat
	* 
	* @author unknown, JeromeJ, Cyril MAGUIRE
	*/
	public static function explain($var = null, $html = false, $return = false, $sub = 0, $way = 'normal'){
		$debug = debug_backtrace();
		$msg = '';
		if ($var === null && $sub == 0) {
			$msg = 'Variables globales';
			$var = array('_POST' => $_POST, '_GET' => $_GET, '_COOKIE' => $_COOKIE, '_SERVER' => $_SERVER);
			if (isset($_SESSION)) {
				$var['_SESSION'] = $_SESSION;
			}
			if (isset($_GLOBAL)) {
				$var['_GLOBAL'] = $_GLOBAL;
			}
		}
			// Recherche du nom de la variable passée en paramètre
			if ($way == 'normal') {
				$d = $debug;
			} else {
				$d = array_reverse($debug);
			}
			
			$file = fopen( $d[0]['file'], 'r' );
			$line = 0;
			$calledVar = '';
			while ( ( $row = fgets( $file ) ) !== false ) {
				if ( ++$line == $d[0]['line'] ) {
					if ($way == 'normal') {
						$row = str_replace(array('<','>'), '', $row);
						preg_match('/(?:.*)*explain\((.*)\);(?:.*)*/U',$row, $match);
						if (isset($match[1])) $calledVar = $match[1];
					} else {
						$f = preg_match('/(?:.*)*e\((.*)\);(?:.*)*/U', $row, $match);
						if (isset($match[1])) $calledVar = $match[1];
					}
					break;
				}
			}
			fclose( $file );
		if($sub == 0) {
			$r = '<pre style="border: 1px solid #e3af43; background-color: #f8edd5; padding: 10px; overflow: auto;">';
			$r .= '<p>Appel du debug dans le fichier <br/>"<strong>'.$debug[0]['file'].'</strong>" ligne '.$debug[1]['line'].'</p>
			<h2 style="margin-top:-30px;">Traces&nbsp;<span id="expfolderclose" onclick="document.getElementById(\'id-debug-backtrace\').className=\'expshow\';document.getElementById(\'expfolderclose\').className=\'expclose\';document.getElementById(\'expfolderopen\').className=\'expshow\';" style="font-size:1px;cursor:pointer;">&#9654;</span>&nbsp;<span id="expfolderopen" onclick="document.getElementById(\'id-debug-backtrace\').className=\'expclose\';document.getElementById(\'expfolderopen\').className=\'expclose\';document.getElementById(\'expfolderclose\').className=\'expshow\';" class="expclose" style="font-size:1px;cursor:pointer;">&#9660;</span></h2>
			<ol style="margin-top:-30px" id="id-debug-backtrace" class="expclose">';
			foreach ($debug as $k => $v) {
				if ($k>0 && isset($v['file']) && isset($v['line']) ) {
					$r .= '<li><strong>'.$v['file'].'</strong> ligne '.$v['line'].'</li>';
				}
			}
			$r .= '</ol><br/><strong><span style="color:#8bb5eb;">'.$calledVar.$msg.'</span></strong> = ';
		}else{
			$r = '';
		}
		$type = htmlentities(gettype($var));
		switch ($type) {
			case 'NULL':$r .= '<em style="color: #0000a0; font-weight: bold;">NULL</em>';break;
			case 'boolean':if($var) $r .= '<span style="color: #327333; font-weight: bold;">TRUE</span>';
			else $r .= '<span style="color: #327333; font-weight: bold;">FALSE</span>';break;
			case 'integer':$r .= '<span style="color: red; font-weight: bold;">'.$var.'</span>';break;
			case 'double':$r .= '<span style="color: #e8008d; font-weight: bold;">'.$var.'</span>';break;
			case 'string':$r .= '<span style="color: #e84a00;">\''.($html ? $var:htmlentities($var)).'\'</span>';break;
			case 'array':$r .= 'Tableau('.count($var).')'."\r\n".str_repeat("\t", $sub).'{'."\r\n";
				foreach($var AS $k => $e) $r .= str_repeat("\t", $sub+1).'['.self::explain($k, $html, true, $sub+1).'] =&gt; '.($k === 'GLOBALS' ? '* RECURSION *':self::explain($e, $html, true, $sub+1, $var)).",\r\n";
				$r .= str_repeat("\t", $sub).'}';
			break;
			case 'object':$r .= 'Objet «<strong>'.htmlentities(get_class($var)).'</strong>»'."\r\n".str_repeat("\t", $sub).'{'."\r\n";
				$prop = get_object_vars($var);
				foreach($prop AS $name => $val){
					if($name == 'privates_variables'){ # Hack (PS: il existe des biblio interne permettant d'étuexitr une classe)
						for($i = 0, $count = count($var->privates_variables); $i < $count; $i++) $r .= str_repeat("\t", $sub+1).'<strong>'.htmlentities($get = $var->privates_variables[$i]).'</strong> =&gt; '.self::explain($var->$get, $html, true, $sub+1)."\r\n";
						continue;
					}

					$r .= str_repeat("\t", $sub+1).'<strong>'.htmlentities($name).'</strong> =&gt; '.self::explain($val, $html, true, $sub+1)."\r\n";
				}
				$r .= str_repeat("\t", $sub).'}';break;
			default:$r .= 'Variable de type <strong>'.$type.'</strong>.';break;
		}
		if($sub == 0) $r .= '</pre>';
		if($return) return $r;
		else echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><style type="css"> .expshow{display:block;border:1px solid red;} .expclose{display:none;} pre {white-space: pre;white-space: pre-wrap;white-space: pre-line;white-space: -pre-wrap;white-space: -o-pre-wrap;white-space: -moz-pre-wrap;white-space: -hp-pre-wrap;word-wrap: break-word;}</style></head><body><div style="font-family: Helvetica, Arial, sans-serif;">'.$r.'</div></body></html>';
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
	public static function img($nr, $class='',$icon = false,$smile=false) 
	{
		return  ($icon === true) ? 'data:image/png;base64,'.self::$img[$nr] :'<img src="data:image/png;base64,'.self::$img[$nr].'" alt="'.self::$names[$nr].'"'.($class!=''?' class="' .$class. '"':'').''.($smile!=''?' rel="'.$nr.'"':'').' />';
	}
	/**
	* CRÉATION DU FICHIER LANG
	*/
	public static function mklang() 
	{
		$LANG = array(
			# Installation & Configuration
			'PHP_VERIF' => 'Vous devez disposer d\'un serveur équipé de PHP 5.3 ou plus !',
			'THEME' => 'Thème',
			'LANGUAGE' => 'Langue',
			'PLUGIN' => 'Plugin',
			'CONFIG' => 'Configuration',
			'REDIRECT' => 'Redirection vers',
			'POWEREDBY' => 'Propulsé par <a id=\"bottom\" name=\"bottom\" href=\"http://uforum.byethost5.com\" rel=\"tooltip\" title=\"Forum sans Sql\">µForum</a>',
			'BBCODE_WELCOM_TXT' => "[b][i]Bienvenue sur µforum[/i][/b]

			Ce forum monothread est basé sur des fichiers uniquement (pas de base de données sql).
			Le concept est un peu différent des autres forums puisque l'information la plus importante mise en avant pour reconnaître un utilisateur est son avatar (pour une fois qu'il sert à quelque chose..)

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
			'WELCOME_TXT' => "<b><i>Bienvenue sur µforum</i></b> <br /> <br />Ce forum monothread est basé sur des fichiers uniquement (pas de base de données sql). <br />Le concept est un peu différent des autres forums puisque l'information la plus importante mise en avant pour reconnaître un utilisateur est son avatar (pour une fois qu'il sert à quelque chose..) <br /> <br /><ins><b>Il intègre plusieurs fonctionnalités :</b></ins> <i>(★ = Nouveauté)</i> <br /> <br /><pre>✔ Gestion des membres par login / mot de passe (par cookies). <br />✔ 4 niveaux d'utilisateurs : Administrateur, Modérateur, Membre, Anonyme. <br />✔ Mode privé / public, pour autoriser les non-membres. <br />✔ Liste des membres. <br />✔ Profil utilisateur (+ édition). <br />✔ Messagerie privée entre les membres. <br />✔ Upload d'avatar et de pièces jointes (avec filtre d'extensions). <br />✔ Smileys et BBCodes (ajout automatique des balises fermantes manquantes). <br />★ Coupure des chaines trop longues sans couper les phrases ! <br />✔ Skins. <br />✔ Liens automatiques. <br />★ Html5 et css3 (Bootstrap de twitter). <br />✔ Affichage des connectés. <br />✔ Coloration syntaxique du code. <br />✔ Gestion des options d'administration. <br />✔ Système simple de sauvegarde et restauration. (revu) <br />★ Captcha lors de l'inscription. <br />★ Protection des mails, sur la liste des membres, pour contrer le spam.    <br />★ Indicateur de message (Status Icône).   <br />★ Date de naissance + Âge affiché si celle-ci renseignée. <br />★ Date picker (Inscription et édition du profil).  <br />★ Méta description pour le SEO.<br />&nbsp;</pre>&nbsp;</div>",
			'INFORMATION' => 'Information',
			'PARAMS' => 'Paramètres',
			'GENERAL_PARAM' => 'Paramètre Général',
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
			'FOUNDER' => 'Fondateur',
			'MODERATOR' => 'Modérateur',
			'ANSWER_FROM' => 'Réponse de',
			'WHOLE_TOPIC' => 'Tout le sujet',
			'QUOTE_MSG_FROM' => 'Citer le message de',
			'DOWNLOAD' => 'Télécharger',
			'TOPIC_UNKNONW' => 'Sujet inexistant',
			'BLOCKQUOTE' => 'Citation',
			'EDIT_BY' => 'Modifié par',
			'MODO' => 'Modo',
			'DEL_MEMBER' => 'Supprimer le membre',
			'DEL_THIS_USER' => 'Supprimer cet utilisateur',
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
			'TOPIC' => 'sujet',
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

			# Confirmations
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

			# Erreurs
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
			'ERROR_USER_ALREADY_EXISTS' => 'Cet utilisateur existe déjà !',
			'ERROR_INVALID_EMAIL' => 'Vous avez fourni une adresse mail non valide !',
			'ERROR_FILL_FIELDS' => 'Merci de remplir les champs Identifiant, Mot de passe, adresse mail et date de naissance !',
			'ERROR_INVALID_PSEUDO' => 'Vous n\'avez pas indiqué de pseudonyme valide.',
			'ERROR_PSEUDO_ALREADY_USED' => 'Un membre est déjà inscrit sous ce pseudonyme.',
			'ERROR_INVALID_TOPIC' => 'Ce sujet n\'existe pas.',
			'ERROR_EMPTY_PSEUDO' => 'Vous n\'avez pas indiqué de pseudonyme.',

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
			'CAPTCHA' => 'Répondez à la question :',
			'CANT_OPEN_CAPTCHA_FILE' => 'Impossible d\'ouvrir le fichier de questions : ',
			'CAPTCHA_WRONG_ANSWER' => 'La réponse n\'est pas la bonne. Merci de réessayer.',
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
	public static function input($label, $name, $value='', $type='text', $placeholder='', $maxlength='', $readonly=false, $class='', $icon='', $require='',$onclick=false,$onfocus=false) {

			if($require) $class .= ' '.$require;

			$form = '<label class="control-label" for="'.$name.'">' .$label. '</label>';
			if($icon)
				$form .= '<span class="'.$icon.'">&nbsp;</span>';	
			else
				$form .= '';			               		      
			if($readonly)
				$form .= '<input id="'.$name.'" name="'.$name.'" type="'.$type.'" class="readonly'.($class!=''?' '.$class:'').'" value="'.$value.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').' readonly="readonly"'.($onclick ? ' onclick="'.$onclick.'"' : '').($onfocus ? ' onfocus="'.$onfocus.'"' : '').'/>';
			else
				$form .= '<input id="'.$name.'" name="'.$name.'" type="'.$type.'"'.($class!=''?' class="'.$class.'"':'').' value="'.$value.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').($onclick ? ' onclick="'.$onclick.'"' : '').($onfocus ? ' onfocus="'.$onfocus.'"' : '').'/>';	
			return $form;
	}
	/**
	 * Méthode qui traite une zone de texte
	 *
	**/	
	public static function textarea($label, $name, $value='', $cols='', $rows='', $placeholder='', $maxlength='', $readonly=false, $class='') {
	    global $lang;
	    $form = '<label class="control-label" for="'.$name.'">'.$label.'</label>';
		if($readonly)
			$form .= '<textarea id="'.$name.'" name="'.$name.'" class="readonly" cols="'.$cols.'" rows="'.$rows.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').' readonly="readonly">'.$value.'</textarea>';
		else
			$form .= '<textarea id="'.$name.'" name="'.$name.'"'.($class!=''?' class="'.$class.'"':'').' cols="'.$cols.'" rows="'.$rows.'"'.($maxlength!=''?' maxlength="'.$maxlength.'"':'').($placeholder!=''?' placeholder="'.$placeholder.'"':'').'>'.$value.'</textarea>';
		return $form;
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
				$string = self::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
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
	public static function clean($text)
	{
		if(get_magic_quotes_gpc())
			$text = stripslashes($text);
		return htmlspecialchars(trim($text), ENT_QUOTES);
	}

}
// Raccourcis debug
function e($var=null, $html = false, $return = false, $sub = 0, $way = 'reverse') {
	Tools::explain($var, $html, $return, $sub, $way);
}
function ed($var=null, $html = false, $return = false, $sub = 0, $way = 'reverse') {
	Tools::explain($var, $html, $return, $sub, $way);
	exit();
}
/**
* BBCode
*/
class BBCHelper
{
	/**
	*
	* AIDE FORMATTAGE BBCODE (Éditeur)
	*/
	public static function formattingHelp($id='message') {
		$aSmileys = array_keys(Tools::$names);
		$buff = '';
		$smileys='';
		$s=array(':)',';)',':D',':|',':(','8(',':p',':$','->'); // smileys
		for($i=0;$i<sizeof($s);$i++) { $smileys .= "<li><a href=\"javascript:insert(' ".$s[$i]." ','',$id);\" title='".$s[$i]."'>".Tools::img($aSmileys[$i])."</a></li>"; }
		$buff .= '
		<p class="forms-inline"><label>'.SMILEYS.'</label></p>
		<ul class="forms-inline-list">
			'.$smileys.'
		</ul>
		<p><label style="margin-right:50px">'.FORMATING.'</label></p>
		<ul class="forms-inline-list btn-group"> 
		   <a class="btn" href="javascript:insert(\'[b]\',\'[/b]\')" rel="tooltip" title="'.BOLD.'"><i class="halflings bold"></i></a>
		   <a class="btn" href="javascript:insert(\'[i]\',\'[/i]\')" rel="tooltip" title="'.ITALIC.'"><i class="halflings italic"></i></a>
		   <a class="btn" href="javascript:insert(\'[u]\',\'[/u]\')" rel="tooltip" title="'.UNDERLINE.'"><i class="halflings text-width"></i></a>
		   <a class="btn" href="javascript:insert(\'[s]\',\'[/s]\')" rel="tooltip" title="'.STROKE_THROUGH.'"><i class="halflings text-height"></i></a>
		   <a class="btn" href="javascript:insert(\'[quote]\',\'[/quote]\')" rel="tooltip" title="'.QUOTE.'"><i class="halflings comments"></i></a>
		   <a class="btn" href="javascript:insert(\'[c]\',\'[/c]\')" rel="tooltip" title="'.CODE.'"><i class="halflings barcode"></i></a>
		   <a class="btn" href="javascript:insert(\'[url]\',\'[/url]\')" rel="tooltip" title="'.LINK.'"><i class="halflings share"></i></a>
		   <a class="btn" href="javascript:insert(\'[img]\',\'[/img]\')" rel="tooltip" title="'.PICTURE.'"><i class="halflings picture"></i></a>
		   <a class="btn" href="javascript:insert(\'[youtube]\',\'[/youtube]\')" rel="tooltip" title="'.VIDEO.'"><i class="halflings film"></i></a>
		</ul><!-- /btn-group --> 

		';
		return $buff;
	}
	/**
	*
	* SYNTAXE HILGHTER
	*/
	public static function colorSyntax($txt) { 
		if(preg_match('%\&lt;\?[php]?%',$txt)) {
			$txt = html_entity_decode($txt,ENT_QUOTES,'UTF-8');
			$txt = preg_replace("/(\r|\n)/i","\n",$txt);
			ob_start();
			@highlight_string($txt);
			$code = ob_get_contents();
			ob_end_clean();
			$code = preg_replace('%\(<code>|</code>\)%','',$code);
			$txt = '<pre>'.trim($code).'<br />&nbsp;</pre>&nbsp;';
		} else { // Sinon, traitement classique
			$txt = "<pre>" . $txt;
			$txt = preg_replace("/([a-zA-Z0-9\-\_]+)(\(+)([^\n\t]*)(\)+)/i", "<span class='text-info'>\\0</span>", $txt);
			$txt = preg_replace("/((\n|\t))([^\n\r]+)/i", "<span class='muted'>\\0</span>", $txt);	
			$txt = preg_replace("/\\$([a-zA-Z0-9]*)/i", "<span class='text-warning'>\\0</span>", $txt);
			$txt = preg_replace("/\"([^\n\r]+)\"/i", "<span class='text-error'>\\0</span>", $txt);
			$txt .= '<br />&nbsp;</pre>&nbsp;';
			return nl2br(trim($txt));
		}
		return $txt;
	}
	/**
	*
	* PARSER BBcode 
	*/
	public static function bbCode($text, $summary = true)
	{
		//the pattern to be matched
		//the replacement
		//global $pattern, $replace,$image_array,$img_names;

		$pattern[] = '%\[c\]([^\a]+?)\[/c\]%U';
		$replace[] = $summary? '\'[...]\'' : '\'<pre class="prettyprint linenums">\'.str_replace(\'<br />\', \'\', \'$1\').\'</pre>\'';

		$pattern[] = '%\[b\]([^\n]+?)\[/b\]%';
		$replace[] = '<b>$1</b>';

		$pattern[] = '%\[i\]([^\n]+?)\[/i\]%';
		$replace[] = '<i>$1</i>';

		$pattern[] = '%\[u\]([^\n]+?)\[/u\]%';
		$replace[] = '<ins>$1</ins>';

		$pattern[] = '%\[s\]([^\n]+?)\[/s\]%';
		$replace[] = '<del>$1</del>';

		$pattern[] = '%\[img\]([^\n\[]+?)\[/img\]%';
		$replace[] = '<img class="thumbnail" src="$1" alt=""/>';
		
		$pattern[] = '%\[url=([^\n\[]+?)\]([^\n]+?)\[/url\]%';
		$replace[] = '<a href="$1" onclick="window.open(this.href);return false;">$2</a>';
		
		$pattern[] = '%\[url\]([^\n]+?)\[/url\]%';
		$replace[] = '<a href="$1" onclick="window.open(this.href);return false;">$1</a>';	

		$pattern[] = '%\[youtube\]([-\w]{11})\[/youtube\]%';
		$replace[] = '<iframe class="thumbnail" width="560" height="315" src="http://www.youtube.com/embed/$1?rel=0" frameborder="0"></iframe>';

		$pattern[] = '%\[quote\](\d{4}-\d{2}-\d{8}[a-z\d]{5})\[/quote\]%U';
		$replace[] = '<blockquote>$1</blockquote>';
		
		$pattern[] = '%\[q=(.*)\](.*)\[/q\]%U';
		$replace[] = '<fieldset><legend>'.BLOCKQUOTE.' : $1</legend>$2</fieldset>';

		$pattern[] = '%\[e\]([^\n]+?)\[/e\]%';
		$replace[] = '<p class="muted">'.EDIT_BY.' : $1</p>';
		
	    /* smiley */
	    $pattern[] = '%:\)%';    $replace[] = Tools::img('smile','',false,true);
	    $pattern[] = '%;\)%';    $replace[] = Tools::img('wink','',false,true);
	    $pattern[] = '%:D%' ;    $replace[] = Tools::img('laugh','',false,true);    
	    $pattern[] = '%:\|%';    $replace[] = Tools::img('indifferent','',false,true);
	    $pattern[] = '%:\(%';    $replace[] = Tools::img('sad','',false,true);
	    $pattern[] = '%8\(%';    $replace[] = Tools::img('wry','',false,true); 
	    $pattern[] = '%:p%';     $replace[] = Tools::img('tongue','',false,true);
	    $pattern[] = '%:\$%';    $replace[] = Tools::img('sorry','',false,true);
	    $pattern[] = '%-&gt;%';  $replace[] = Tools::img('arrow','',false,true);

	    $pattern[] = '%\[sm=smile\]%';          $replace[] = Tools::img('smile','',false,true);
	    $pattern[] = '%\[sm=wink\]%';           $replace[] = Tools::img('wink','',false,true);
	    $pattern[] = '%\[sm=laugh\]%' ;         $replace[] = Tools::img('laugh','',false,true);    
	    $pattern[] = '%\[sm=indifferent\]%';    $replace[] = Tools::img('indifferent','',false,true);
	    $pattern[] = '%\[sm=sad\]%';            $replace[] = Tools::img('sad','',false,true);
	    $pattern[] = '%\[sm=wry\]%';            $replace[] = Tools::img('wry','',false,true); 
	    $pattern[] = '%\[sm=tongue\]%';         $replace[] = Tools::img('tongue','',false,true);
	    $pattern[] = '%\[sm=sorry\]%';          $replace[] = Tools::img('sorry','',false,true);
	    $pattern[] = '%\[sm=arrow\]%';          $replace[] = Tools::img('arrow','',false,true);
	    	
		return preg_replace($pattern, $replace, $text);
	}
	/**
	* tronquer_texte
	* Coupe une chaine sans couper les mots
	*
	* @param string $texte Texte à couper
	* @param integer $nbreCar Longueur à garder en nbre de caractères
	* @return string
	*/
	public static function tronquer_texte($texte, $nbchar)
	{
	    return (strlen($texte) > $nbchar ? substr(substr($texte,0,$nbchar),0,
	    strrpos(substr($texte,0,$nbchar),' ')).'…' : $texte);
	}
	/**
	*
	* DÉCODE LES FICHIERS
	*/
	public static function decode($txt) {

		$txt=str_replace ("\t", "     ", $txt);
		$txt=str_replace ("\r\n", " <br />", $txt);
		$res=preg_split("|\[c\].*\[/c\]|U", $txt);
		preg_match_all("|\[c\](.*)\[/c\](.*)|U",$txt,$code,PREG_SET_ORDER);
		$txt=self::bbCode($res[0]);
		for($i=0;$i<count($code);$i++) {
			$txt.=self::colorSyntax($code[$i][1]);
			$txt.=self::bbCode($res[$i+1]);
		}
		return $txt;
	}

	public function parse($msg) {
		return str_replace(array('<b>','<i>','<ins>','<pre>','</b>','</i>','</ins>','</pre>','<br />','&nbsp;','</div>'), array('[b]','[i]','[u]','[c]','[/b]','[/i]','[/u]','[/c]',"\n",' ',''), stripslashes($msg));
	}
}
/**
* Mise en forme des dates
*/
class MuDate
{
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
	public static function datefr($arg)
	{ 
	    $Jour = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi','Samedi');
	    $Mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	    $datefr = $Jour[date('w')].' '.date('d').' '.$Mois[date('n')].' '.date('Y');
	    return $datefr; 
	} 
	/**
	 * Méthode qui retourne l'âge i18n
	 *
	**/
	public static function birthday($birthdate, $pattern = 'eu')
	{
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

	public function setMsg($msg,$type = 'success-msg') {
		$_SESSION['msg'] = array(
			'msg' => $msg,
			'type' => $type,
			'id' => mt_rand(1,50)
		);
	}

	public function msg() {
		if (isset($_SESSION['msg']['msg'])) {
			$_GET = array();
			$_POST = array();
			$r = '<div class="msgFlash '.$_SESSION['msg']['type'].'" id="id_'.$_SESSION['msg']['id'].'"><p>'.$_SESSION['msg']['msg'].'</p><p class="close-right" onclick="document.getElementById(\'id_'.$_SESSION['msg']['id'].'\').className=\'closed\';">X</p></div>';
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

class Captcha
{
	private $question_file;
	public $session;
	
	public function __construct($lang,$session)
	{
		$this->session = $session;

		$this->question_file = "captcha".DS;

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
		$txt = "# File for turing test questions. Structure of the file is very simple, first
# line of a record is \"--\" which indicates new record (question). Second line
# is question and third line is right answer. You can add more answers to third
# separated by comma. Everything else is ignored, so you can use it as comments.
# In that case, please use something like \"#\" or \"//\" to make it clear it
# is comment. Comparing answers is case insensitive.

--
De quelle couleur est le citron?
Jaune, jaune

--
Combien font 4 fois 4?
16, seize, Seize, SEIZE

--
chat = Tom, souris = ?
Jerry, jerry

--
On me prend la température avec un ...?
Thermomètre, thermomètre, thermometre, termometre, termomètre

--
Corrigez le mot : omcaulogie
Oncologie, oncologie

--
22 moins 17?
5, cinq, Cinq, CINQ

--
Je pense donc je ... ?
suis

--
Prénom d'Einstein?
Albert

--
Les neutrophiles sont des globules ...?
Blancs, blancs

--
Qui est le frère de Mario ?
Luiggi, luiggi";

		if(!file_exists($this->question_file."fr_questions.txt")) {
			file_put_contents($this->question_file."fr_questions.txt", $txt);
		}
	}
	private function mkEnQuest() {
		$txt = "# File for turing test questions. Structure of the file is very simple, first
# line of a record is \"--\" which indicates new record (question). Second line
# is question and third line is right answer. You can add more answers to third
# separated by comma. Everything else is ignored, so you can use it as comments.
# In that case, please use something like \"#\" or \"//\" to make it clear it
# is comment. Comparing answers is case insensitive.

--
What color is lemon?
Yellow

--
How much is 4 times 4?
16, sixteen

--
cat - Tom, mouse -
Jerry

--
Shortcut of World War 2?
WW2, WWII

--
Correct spelling: univrsity
University

--
Difference between 22 and 17?
5, five

--
I think, therefore I...
am

--
First name of Einstein?
Albert

--
How many moons has the Earth?
1, one

--
Name of partner of Eve of Eden?
Adam";

		if(!file_exists($this->question_file."en_questions.txt")) {
			file_put_contents($this->question_file."en_questions.txt", $txt);
		}
	}
	public function actionBegin()
	{
		if(isset($_REQUEST["qid"]))
			$this->checkCaptcha();
	}

	/*
	 * Functions return number of questions in question file. Method is very simple, it just counts
	 * number of occurence of "--" at the begining of the line.
	 */

	private function questionCount()
	{
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

	private function getQuestion($i, $line)
	{
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

	private function checkCaptcha()
	{
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

	public function template()
	{
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
class SaveObj
{
	public $name= '';
	public function __construct() {
		if (!empty($this->name)) {
			file_put_contents($this->name, serialize($this));
		}
	}

	public function SaveObj($obj) {
		if (!empty($this->name)) {
			file_put_contents($this->name, serialize($obj));
		}
	}

	public function SaveMsgObj($obj) {
		foreach ($obj->mess as $key => $value) {
			file_put_contents(MU_MEMBER.md5($value->to.SECURITY_SALT).DS.$value->to.'.mp', serialize($obj));
		}
	}
}
/**
*
* CLASSE GLOBALE RETOURNANT TABLEAUX
*/
class Forum extends SaveObj
{
	public $name;
	public $topics=array();
	public $members=array();

	public function __construct() {
		$this->name= MU_MEMBER.'members.dat';
		if (is_file($this->name)){
			$mb = unserialize(file_get_contents(MU_MEMBER.'members.dat'));
			if ($mb->members !== null){
				$this->members= $mb->members;
			}else{
				$this->members = array();
			}
		}
		$msg = scandir(MU_THREAD);
		foreach ($msg as $m) {
			if (substr($m, -4) == '.dat') {
				$this->topics[substr($m,0, -4)] = unserialize(file_get_contents(MU_THREAD.$m));
			}
		}
		parent::__construct();
	}
	public function addMember($name,$password,$mail,$quote='',$url='',$birthday,$pic='',$mod=0) {
		if(!count($this->members)) $mod=2;
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
		unset($this->members[$name]);
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
	public function setPost($name) { 
		$this->members[$name]->post++;
		$this->saveObj($this);
	}
	public function getPosts($topic,$showAll=false,$nbrMax=15,$fromPage=1) {
		if($s = implode('', file(MU_THREAD.$topic.'.dat'))) {
			$obj = unserialize($s);
		}
		$obj->nbPosts = count($obj->reply);
		if ($showAll) {
			return $obj;
		} else {
			$nbPages = ceil($obj->nbPosts/$nbrMax);
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
		if($s = implode('', file(MU_THREAD.$topic.'.dat'))) {
			$obj = unserialize($s);
		}
		return $obj->title;
	}
	public function setMod($name) {
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
			$pass=($this->members[$name]->password!=$pass)?false:true;
			$mod=$this->members[$name]->mod;
			return array($login,$pass,$mod);
		}
		return array(0,0,0);
	}
	public function addTopic($title,$auth,$time,$attach,$type=false) {
		$this->topics[$time]->title = $title;
		$this->topics[$time]->auth = $auth;
		$this->topics[$time]->time = $time;
		$this->topics[$time]->attach = $attach;
		$this->topics[$time]->type = $type;
		$this->lastSort();
		if(isset($this->members[$auth])) $this->members[$auth]->post++;
		$this->saveObj($this);
	}
	public function updateTopic($time,$title,$auth,$post,$last,$ltime,$attach,$type) {
		$this->topics[$time]->title = $title;
		$this->topics[$time]->auth = $auth;
		$this->topics[$time]->time = $ltime;
		$this->topics[$time]->attach = $attach;
		$this->topics[$time]->type = $type;
		$this->topics[$time]->post = $post;
		$this->topics[$time]->last = $last;
		$this->topics[$time]->attach = $attach;
		$this->lastSort();
		$this->saveObj($this);
	}
	public function delTopic($id) {
		unset($this->topics[$id]);
		$this->saveObj($this);
	}
	public function setType($topic,$type) {
		$this->topics[$topic]->type = $type;
		$t = $this->openTopic($topic);
		$t->setType($type);
		unset($t);
		$this->lastSort();
		$this->saveObj($this);
	}
	public function setTitle($topic,$title) {
		$this->topics[$topic]->title=$title;
		$t = $this->openTopic($topic);
		$t->setTitle($title);
		unset($t);
		$this->lastSort();
		$this->saveObj($this);
	}
	public function getallTopic($showAll=false,$nbrMax=15,$fromPage=1) {
		$tmp=array();
		$return=array();
		foreach($this->topics as $k=>$v) {
			if (isset($v->reply) && $v->reply !== null){
				if (is_object($v)) $v = get_object_vars($v);
				krsort($v['reply']);
				$current = current($v['reply']);
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
			} else {
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
			if ($fromPage>$nbPages) $fromPage = $nbPages;
			$to = $fromPage*$nbrMax;
			if ($to>$nbTopics) $to = $nbTopics;
			$from = ($fromPage*$nbrMax)-$nbrMax;
			for ($i=$from; $i < $to; $i++) { 
				$return[] = $tmp[$i];
			}
			return $return;
		}
		
	}
	public function getStat() {
		$tmp=0;
		$arr=array(0,"");
		foreach($this->getallTopic(true) as $v) $tmp += $v['nombrePosts'];//$tmp+=$v[2];
		foreach($this->members as $k=>$v) $arr=($v->time>$arr[0])?array($v->time,$k):$arr;
		return array('members'=>count($this->members),'lastMb'=>$arr[1],'topics'=>count($this->topics),'messages'=>$tmp);
	}
	public function openTopic($topic) {
		if($s = @file_get_contents(MU_THREAD.$topic.'.dat')) return unserialize($s);
		else return false;
	}
	public function lastSort() {
		$keys = array_keys($this->topics);
		rsort($keys);
		foreach ($keys as $k => $v) {
			$tmp[$v] = $this->topics[$v];
		}
		$this->topics = $tmp;
	}
}
/**
*
* CLASSE DE GESTION DES DISCUSSIONS
*/
class Topic extends SaveObj
{
	public $title;
	public $time;
	public $auth;
	public $name;
	public $type=false;
	public $reply=array();
	public $pos=0;

	public function __construct($auth,$title,$content,$attach='',$type=false) {
		parent::__construct();
		$this->addReply($auth,$content,$attach);
		$this->title=Tools::clean($title);
		$this->time=time();
		$this->type=$type;
		$this->auth=$auth;
		$this->name=MU_THREAD.time().'.dat';
		$this->saveObj($this);
	}
	public function removeTopic() {
		unlink($this->name);
	}
	public function addReply($auth,$content,$attach='') {
		$t = time();
		$id = count($this->reply);
		if ($id == -1) $id = 0;
		$this->reply[$id]->auth =$auth;
		$this->reply[$id]->time =$t;
		$this->reply[$id]->content = $content;
		$this->reply[$id]->attach =$attach;
		$this->saveObj($this);
		return $this->getlastReply();
	}
	public function removeReply($id) {
		$tmp=array();
		foreach($this->reply as $r) if($r->time!=$id) $tmp[]=$r;
		$this->reply=$tmp;
		$this->saveObj($this);
	}
	public function getlastReply() {
		return end($this->reply);
	}
	public function setReply($id,$title,$content,$attach='') {
		if($title!='') $this->title=$title;
		foreach($this->reply as $k=>$r) { if($r->time==$id) $this->reply[$k]->content=$content;}
		$this->saveObj($this);
	}
	public function getReply($id) {
		foreach($this->reply as $v) {
			if($v->time==$id) return $v;
		}
	}
	public function nextReply() {
		if($this->pos<count($this->reply)){return $this->reply[$this->pos++];}
		else {$this->pos=0; return false;}
	}
	public function setType($type) {
		$this->type=$type;
		$this->saveObj($this);
	}
	public function setTitle($title) {
		$this->title=$title;
		$this->saveObj($this);
	}
	public function getInfo($type) {
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
		else return array($this->time,$this->title,$auths[0],$posts,$last->auth,$last->time,$attach,$this->type);
	}
}
/**
*
* STATISTIQUES (Online)
*/
class Visit extends saveObj
{
	public $name;
	public $conn=array();
	public function __construct() {
		$this->name = MU_MEMBER.'connected.dat';
		parent::__construct();
	}
	public function visit($id='') {
		$this->conn[$_SERVER['REMOTE_ADDR']]=array($id,time());
		$this->saveObj($this);
	}
	public function updateVisit($id='') {
		$r=$_SERVER['REMOTE_ADDR'];
		$cnt=0;
		$arr='';
		$this->conn[$r]=array($id,time());
		foreach($this->conn as $k=>$v) {
			if(((time()-$v[1])>120) && $k!=$r) unset($this->conn[$k]);
			else {
				if($this->conn[$k][0]!='') $arr.=($r==$k)?$id.' ':'<a href="?private='.$this->conn[$k][0].'" rel="tooltip" title="'.SEND_PRIVATE_MSG.'">'.$this->conn[$k][0].'</a> ';
				else $cnt++;
			}
		}
		$this->saveObj($this);
		return array($arr,$cnt);
	}
}
/**
*
* INSCRIPTION DES MESSAGES PRIVÉS DES MEMBRES
*/
class Messages extends saveObj
{
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

/**
* Initialisation du forum
*/
class Init
{
	public $errors;
	public $colors;
	public $cNames=array();
	public $cVals=array();

	public $forum;
	public $captcha;
	public $conn;
	public $session=null;

	public $uforum='[b]&micro;[/b]Forum';
	public $lang='fr';
	public $metaDesc='Lightweight bulletin board without sql';
	public $nbrMsgIndex=15;
	public $nbMsgTopic=15;
	public $nbrMb=30;
	public $extensionsAutorises='gif,bmp,png,jpg,mp3,zip,rar,txt';
	public $extStr;
	public $maxAvatarSize=30720;
	public $forumMode=1;
	public $quoteMode=1;
	public $siteUrl=MU_BASE_URL;
	public $siteName='&micro;Forum';

	public $isMember;
	public $isAdmin;
	public $isOwner;
	public $cPass;
	public $cLogin;
	public $cStyle;
	public $haveMP; //Messages privés
	public $topicObj;
	public $topic;

	public $pages=1; //Nombre de pages totales de Topics 
	public $pagesMb=1; //Nombre de pages totales de Membres
	public $page=1; //Page en cours
	public $get_editpost;
	public $get_conf;
	public $get_topic;
	public $get_memberlist;
	public $get_editprofil;
	public $get_private;
	public $get_restore;
	public $showAll;
	public $searchMember;
	
	protected function __construct()
	{
		/**
		*
		* Choix du style (en feature)
		*/
		$this->cNames=array('[lt]','[dk]','[lk]','[ct]','[bk]','[br]');
		$this->cVals['defaut']=array('e8ebed','b1c5d0','91a5b0','f90','eee','999');
		$this->cVals['green']=array('ebede8','c5d0b1','a5b091','f90','eee','999');
		$this->cVals['cyan']=array('e8edeb','b1d0c5','91b0a5','f90','eee','999');
		$this->cVals['purple']=array('ede8eb','d0b1c5','b091a5','f90','eee','999');
		$this->cVals['clean']=array('f9f9f6','f9f9f6','999','f90','333','fff');

		if (is_null($this->session)){
			$this->session = new Session();
		}
		if (is_file('config.php')) {
			require 'config.php';
			$this->uforum = $uforum;
			$this->lang = $lang;
			$this->metaDesc = $metaDesc;
			$this->nbrMsgIndex = $nbrMsgIndex;
			$this->nbMsgTopic = $nbMsgTopic;
			$this->nbrMb = $nbrMb;
			$this->extensionsAutorises = $extensionsAutorises;
			$this->maxAvatarSize = $maxAvatarSize;
			$this->forumMode = $forumMode;
			$this->quoteMode = $quoteMode;
			$this->siteUrl = $siteUrl;
			$this->siteName = $siteName;
			$this->siteBase = $siteBase;
		}
		//if(!is_file('css/main.css')) {$this->mkcss();}
		if(!is_file('version') || file_get_contents('version')!=VERSION) {
			file_put_contents('version', VERSION);
			if(!$this->mkressources()) {
				if (is_file('config.php')){include('config.php');}
				$config="<?php 
				".TM."\n"."
				\$uforum='$this->uforum';\n
				\$lang='$this->lang';\n
				\$metaDesc='$this->metaDesc';\n
				\$nbrMsgIndex=$this->nbrMsgIndex;\n
				\$nbMsgTopic=$this->nbMsgTopic;\n
				\$nbrMb=$this->nbrMb;\n
				\$extensionsAutorises='$this->extensionsAutorises';\n
				\$maxAvatarSize=$this->maxAvatarSize;\n
				\$forumMode=$this->forumMode;\n
				\$quoteMode=$this->quoteMode;\n
				\$siteUrl='$this->siteUrl';\n
				\$siteName='$this->siteName';\n
				\$siteBase='".MU_BASE_URL."'\n?>";
				file_put_contents('config.php', utf8_encode($config));
			}
			$this->mkhtaccess();
			$this->mkjs();
			$this->mkcss();
			if(@copy('index.php','index.bak')) {
				unlink('index.php');
				rename('index.bak','index.php');
				header('location: index.php');
				exit();
			}
		} else {
			$this->forum = new Forum();
			$this->captcha = new Captcha(LANG,$this->session);
			$this->conn = new Visit();
			$s = @file_get_contents(MU_MEMBER.'members.dat');
			$this->forum = unserialize($s);
			$s = @file_get_contents(MU_MEMBER.'connected.dat');
			$this->conn = unserialize($s);
		}
		/**
		*
		* GET & POST
		*/
		$gets=array('topic','action','logout','memberlist','login','password','editprofil','email','birthday','site','signature','titre','message','topicID','postID','deluser','switchuser','delpost','editpost','style','private','delprivate','mpTo','backup','restore','read','conf','uftitle','nbmess','nbmessTopic','nbmb','maxav','exts','fmode','anonymous','qmode','postit','ufsite','uflang','ufsitename','ufmetadesc','rc','ntitle','pid','wco','register','page','searchMember','qid','ans');
		foreach($gets as $o) {
			$$o=(isset($_GET[$o]) && is_string($_GET[$o]))?$_GET[$o]:'';
			if(!$$o) $$o=(isset($_POST[$o]) && is_string($_POST[$o]))?$_POST[$o]:'';
		}
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
		}if($wco) {
			header('location: index.php');
			exit();
		}
		if($topic && !$editpost) setCookie('uFread'.$topic,1,time()+2592000);
		$this->cPass=(isset($_COOKIE['CookiePassword']))?$_COOKIE['CookiePassword']:'';
		$this->cLogin=(isset($_COOKIE['CookieLogin']))?base64_decode($_COOKIE['CookieLogin']):'';
		$this->cStyle=(isset($_COOKIE['CookieStyle']))?$_COOKIE['CookieStyle']:'defaut';
		/**
		*
		* TEST DU MOT DE PASSE
		*/
		if (!empty($this->cLogin) && !empty($this->cPass)) {
			list($this->isMember,$goodpass,$this->isAdmin)=$this->forum->checkMember($this->cLogin,$this->cPass);
			$this->haveMP=@file_exists(MU_MEMBER.md5($this->cLogin.SECURITY_SALT).'/'.$this->cLogin.'.mp');
			if (!ban_canLogin()) { 
				ban_loginFailed(); 
				$this->session->setMsg(L_ERR_WRONG_PASSWORD,'error');
				$this->isMember=0;$this->isAdmin=0;$this->isOwner=0;
				header('Location: index.php');
				exit();
			} else {
				if(!$this->isMember || !$goodpass) {
					ban_loginFailed(); 
					if(!$goodpass) $this->errors .= ERROR_WRONG_PASSWORD.' '.$this->cLogin.' !<br>';
					if(!$this->isMember) $this->errors .= BECAREFUL.' '.$this->cLogin.' '.CASE_SENSITIVE.'<br>';
					$this->isMember=0;
					$this->isAdmin=0;
					setCookie('CookiePassword', '', time());
					setCookie('CookieLogin', '', time());
				} else {
					ban_loginOk();
					if($this->isAdmin==2){$this->isAdmin=1;$this->isOwner=1;}	
				}
			}
		} else { $this->isMember=0;$this->isAdmin=0;$this->isOwner=0;}
		/**
		*
		* DÉCONNEXION
		*/
		if ($this->isMember && $logout) {
			setCookie('CookiePassword', '', time());
			setCookie('CookieLogin', '', time());
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
		/**
		*
		* DIFFÉRENTES ACTIONS
		*/
		switch ($action) {
			case 'enter':
				setCookie('CookiePassword',md5($password),time()+(3600*24*30));
				setCookie('CookieLogin',base64_encode($login),time()+(3600*24*30));
				header('Location: index.php');
				exit();
				break;
			case 'newuser':
				if($qid) $this->captcha->actionBegin();
				// on nettoie le nom d'utilisateur
				$login = str_replace(array(" ", '"', "'", "/", "&", ".", "!", "?", ":"), array("", '', "", "", "", "", "", "", ""), $login);
				$login = Tools::clean($login);
				$avatar='';
				if(in_array($login,$this->forum->listMember())) $this->errors .= ERROR_USER_ALREADY_EXISTS;
				else if($login != '' && $password != '' && $email != '' && $birthday != ''){
					if((preg_match('/(^[0-9a-zA-Z_\.-]{1,}@[0-9a-zA-Z_\-]{1,}\.[0-9a-zA-Z_\-]{2,}$)/', $email)) && (strlen($login)<13)) {
						$memberDirUp = MU_UPLOAD.md5(SECURITY_SALT.$login);
						@mkdir($memberDirUp);
						$memberDir = MU_MEMBER.md5($login.SECURITY_SALT);
						@mkdir($memberDir);
						file_put_contents($memberDirUp.DS.'index.html', GOTO_INDEX);
						file_put_contents($memberDir.DS.'index.html', GOTO_INDEX);
						$avatar=$this->checkUpload($memberDirUp,1,$login);
						$this->forum->addMember($login,$password,$email,$signature,$site,$birthday,$avatar);
						setCookie('CookiePassword', md5($password), time() + (3600 * 24 * 30));
						setCookie('CookieLogin', base64_encode($login), time() + (3600 * 24 * 30));
						header('Location: index.php?wco=true');
						exit();
					} else {
						$this->errors .= ERROR_INVALID_EMAIL;
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
					$this->forum->setMember($this->cLogin,$email,$signature,$site,$birthday,$avatar);
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
					} else if ($this->forum->isMember($anonymous)) {
					    $this->errors .= ERROR_PSEUDO_ALREADY_USED;
					} else {
						if($s = implode('', file(MU_THREAD.$topicID.'.dat'))) {
							$this->tLogin=$this->cLogin?$this->cLogin:$anonymous;
							$this->topicObj = unserialize($s);
							$message = Tools::clean($message);
							$this->topicObj->addReply($this->tLogin,$message,$this->checkUpload(MU_UPLOAD.md5(SECURITY_SALT.$this->tLogin),0));
							list($time,$title,$auth,$post,$last,$tlast,$attach,$postType)=$this->topicObj->getInfo(0);
							$this->forum->updateTopic($time,$ntitle,$auth,$post,$last,$tlast,$attach,$postType);
							if($this->isMember) $this->forum->setPost($this->cLogin);
							header('Location: index.php?topic='.$topicID);
							exit();
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
					} else if ($this->forum->isMember($anonymous)) {
					    $this->errors .= ERROR_PSEUDO_ALREADY_USED;
					} else {
						$this->tLogin=$this->cLogin?$this->cLogin:$anonymous;
						$postType=$postit?1:0;
						$message = Tools::clean($message);
						$this->topicObj = new Topic($this->tLogin,$titre,$message,$this->checkUpload(MU_UPLOAD.md5(SECURITY_SALT.$this->tLogin),0),$postType);
						list($time,$title,$auth,$post,$last,$tlast,$attach,$postit)=$this->topicObj->getInfo(0);
						$this->forum->addTopic($title,$auth,$time,$attach,$postit);
						$this->topic=$time;
						setCookie('uFread'.$this->topic,1,time()+2592000);
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
				if($anonymous) $mpObj->addMessage($anonymous.' ('.$_SERVER['REMOTE_ADDR'].')',$message,$mpTo);
				else if(!$this->isMember) $this->errors.=ERROR_EMPTY_PSEUDO;
				else $mpObj->addMessage($this->cLogin,$message,$mpTo);
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
				$this->nbrMsgIndex=$nbmess?$nbmess:$this->nbrMsgIndex;
				$this->nbMsgTopic=$nbmessTopic?$nbmessTopic:$this->nbMsgTopic;
				$this->nbrMb=$nbmb?$nbmb:$this->nbrMb;
				$this->extStr=$exts?$exts:$this->extStr;
				$this->maxAvatarSize=$maxav?($maxav*1024):$this->maxAvatarSize;
				$this->forumMode=($fmode=='on')?1:0;
				$this->quoteMode=($qmode=='on')?1:0;
				$this->siteUrl=$ufsite?$ufsite:'';
				$this->lang=$uflang?$uflang:'fr';
				$this->metaDesc=$ufmetadesc?$ufmetadesc:'';
				$this->siteName=$ufsitename?$ufsitename:'';
				$config ="<? 
				".TM."\n"."
				\$uforum='$this->uforum';
				\$lang='$this->lang';
				\$metaDesc='$this->metaDesc';
				\$nbrMsgIndex=$this->nbrMsgIndex;
				\$nbMsgTopic=$this->nbMsgTopic;
				\$nbrMb=$this->nbrMb;
				\$extensionsAutorises='$this->extStr';
				\$maxAvatarSize=$this->maxAvatarSize;
				\$forumMode=$this->forumMode;
				\$quoteMode=$this->quoteMode;
				\$siteUrl='$this->siteUrl';
				\$siteName='$this->siteName';
				\$siteBase='".MU_BASE_URL."'\n?>";
				file_put_contents('config.php', utf8_encode($config));
				if(empty($message) && file_exists('welcome.txt')) @unlink('welcome.txt');
				else {
					file_put_contents('welcome.txt', stripslashes($message));
				}
				if (empty($this->errors)) {
					$this->session->setMsg(MSG_DATA_REC);
					header('Location: index.php?conf=1');
					exit();
				}
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
		if($this->isAdmin) {
			if($deluser) { $this->forum->removeMember($deluser); }
			else if($switchuser) { $this->forum->setMod($switchuser); }
			else if($topic && $postit && !$action) { $type=$postit=="on"?1:0; $this->forum->setType($topic,$type); }
			else if($topic && $ntitle) { $this->forum->setTitle($topic,$ntitle); }
			else if($topicID && $action=='editpost' && $postID && $message!='') {
				if($s = implode('', file(MU_THREAD.$topicID.'.dat'))) {
					$message = Tools::clean($message);
					$message = '[e]'.$this->cLogin.' le '.date('d/m/y \à H:i',time()).'[/e]'.$message;
					$this->topicObj = unserialize($s);
					$this->topicObj->setReply($postID,'',$message);
					$topic=$topicID;
				}
			}
			else if($topic && $delpost) {
				if($topic==$delpost) {
					if(@unlink(MU_THREAD.$topic.'.dat')) {
						$this->forum->delTopic($topic);
						$this->session->setMsg(MSG_DATA_DEL);
						header('Location: index.php');
						exit();
					}
				} else {
					if($s=implode('', file(MU_THREAD.$topic.'.dat'))) {
						$this->topicObj = unserialize($s);
						$r=$this->topicObj->getReply($delpost);
						@unlink($r[3]);
						$this->topicObj->removeReply($delpost);
						list($time,$title,$auth,$post,$last,$tlast,$attach,$postType)=$this->topicObj->getInfo(0);
						$this->forum->updateTopic($time,$title,$auth,$post,$last,$tlast,$attach,$postType);
					}
				}
			}
			else if($backup) {$r=$this->do_backup(MU_DATA, 'backup/data_' . date('d-m-Y-h:i:s'). '.zip');}
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
		$this->get_conf      = $conf;
		$this->get_topic      = $topic;
		$this->get_memberlist = $memberlist;
		$this->get_editprofil = $editprofil;
		$this->get_private    = $private	;
		$this->get_restore    = $restore;

		$stats = $this->pagesMsg = $this->forum->getStat();

		if(isset($page) && $page != ''){ $this->page = $page;} else {$this->page = 1;}
		$this->pages = $stats['topics'];
		$this->pagesMb = $stats['members'];
	}
	/**
	*
	* INSTALLATEUR
	*/
	private function mkressources() {
		if (!file_exists('config.php') || !file_exists(MU_MEMBER) || !file_exists(MU_MEMBER.'members.dat')) {
			$config="<? 
				".TM."\n"."
				\$uforum='[b]&micro;[/b]Forum';
				\$lang='fr';
				\$metaDesc='Lightweight bulletin board without sql';
				\$nbrMsgIndex=15;
				\$nbMsgTopic=15;
				\$nbrMb=30;
				\$extensionsAutorises='gif,bmp,png,jpg,mp3,zip,rar,txt';
				\$maxAvatarSize=30720;
				\$forumMode=1;
				\$quoteMode=1;
				\$siteUrl='';
				\$siteName='';
				\$siteBase='".MU_BASE_URL."'\n;?>";
			file_put_contents('config.php', utf8_encode($config));
			$errors='';
			$errors.= (@mkdir(MU_CSS))? sprintf("&#10004;&nbsp;".MKCSS.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKCSS." .\n");
	        $errors.= (is_file(MU_LANG.LANG.'.php'))? sprintf("&#10004;&nbsp;".MKLANG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKLANG.".\n");
	        $errors.= (@mkdir(MU_BACK))? sprintf("&#10004;&nbsp;".MKBAK.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKBAK.".\n");
	        $errors.= (@mkdir(MU_UPLOAD))? sprintf("&#10004;&nbsp;".MKUPL.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKUPL.".\n");
			$errors.= (@mkdir(MU_DATA))? sprintf("&#10004;&nbsp;".MKDATA.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKDATA.".\n");
			$errors.= (@mkdir(MU_DATA.DS.'ban'))? sprintf("&#10004;&nbsp;".MKBAN.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKBAN.".\n");
			$errors.= (@mkdir(MU_MEMBER))? sprintf("&#10004;&nbsp;".MKMBR.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKMBR.".\n");
			$errors.= (@mkdir(MU_THREAD))? sprintf("&#10004;&nbsp;".MKMSG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKMSG.".\n");
			$errors.= (@mkdir(MU_JS))? sprintf("&#10004;&nbsp;".MKJS.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKJS.".\n");
			$errors.= (@mkdir(MU_IMG))? sprintf("&#10004;&nbsp;".MKIMG.".\n") : sprintf("&#10008;&nbsp;".ERROR_MKIMG.".\n");

			$this->errors = $errors;

			$this->session->setMsg($this->errors,'error');
			return true;
		}
		return false;
	}

	/**
	*
	* CRÉATION DU FICHIER .HTACCESS
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
			var activeSub=0;var SubNum=0;var timerID=null;var timerOn=false;var timecount=300;var what=null;var newbrowser=true;var check=false;var layerRef="";var tm="";var confirmMsg="Confirmez la suppression de ";var msie=navigator.userAgent.toLowerCase().indexOf("msie")+1;wmtt=null;document.onmousemove=updateWMTT;function init(){if(document.layers){layerRef="document.layers";styleSwitch="";visibleVar="show";what="ns4"}else{if(document.all){layerRef="document.all";styleSwitch=".style";visibleVar="visible";what="ie"}else{if(document.getElementById){layerRef="document.getElementByID";styleSwitch=".style";visibleVar="visible";what="moz"}else{what="none";newbrowser=false}}}check=true}function switchLayer(a){if(check){if(what=="none"){return}else{if(what=="moz"){if(document.getElementById(a).style.visibility=="visible"){document.getElementById(a).style.visibility="hidden";document.getElementById(a).style.display="none"}else{document.getElementById(a).style.visibility="visible";document.getElementById(a).style.display="block"}}else{if(document.all[a].style.visibility=="visible"){document.all[a].style.visibility="hidden";document.all[a].style.display="none"}else{document.all[a].style.visibility="visible";document.all[a].style.display="block"}}}}else{return}}function countInstances(c,b){var a=document.formulaire.message.value.split(c);var d=document.formulaire.message.value.split(b);return a.length+d.length-2}function insert(e,c){var b=document.getElementById("message");if(document.selection){var g=document.selection.createRange().text;document.formulaire.message.focus();var d=document.selection.createRange();if(c!=""){if(g==""){var f=countInstances(e,c);if(f%2!=0){d.text=d.text+c}else{d.text=d.text+e}}else{d.text=e+d.text+c}}else{d.text=d.text+e}}else{if(b.selectionStart|b.selectionStart==0){if(b.selectionEnd>b.value.length){b.selectionEnd=b.value.length}var h=b.selectionStart;var a=b.selectionEnd+e.length;b.value=b.value.slice(0,h)+e+b.value.slice(h);b.value=b.value.slice(0,a)+c+b.value.slice(a);b.selectionStart=h+e.length;b.selectionEnd=a;b.focus()}else{var d=document.formulaire.message;var f=countInstances(e,c);if(f%2!=0&&c!=""){d.value=d.value+c}else{d.value=d.value+e}}}}function updateWMTT(a){if(document.documentElement.scrollTop&&msie){x=window.event.x+document.documentElement.scrollLeft+10;y=window.event.y+document.documentElement.scrollTop+10}else{x=(document.all)?window.event.x+document.body.scrollLeft+10:(a.pageX+10)+"px";y=(document.all)?window.event.y+document.body.scrollTop+10:(a.pageY+10)+"px"}if(wmtt!=null){wmtt.style.left=x;wmtt.style.top=y}}function showWMTT(a){wmtt=document.getElementById(a);wmtt.style.display="block"}function hideWMTT(){wmtt.style.display="none";wmtt=null}function quote(c,f){var a=document.getElementById("td"+f).innerHTML;var b=new Array("<fieldset.*?>.*?</fieldset>","<br>|<br />","<small>.*?</small>|<pre>|</pre>|<font.*?>|</font>|&nbsp;","<b>","</b>","<i>","</i>","<u>","</u>","&amp;lt;|&lt;","&amp;gt;|&gt;","<hr>",\'<img(.*?)rel="(.*?)"(.*?)>\');var e=new Array("","\n","","[b]","[/b]","[i]","[/i]","[u]","[/u]","<",">","[hr]","[sm=$2]");var d=0;for(i in b){regex=new RegExp(b[i],"gi");a=a.replace(regex,e[d++])}if(document.getElementById("form").style.visibility!="visible"){switchLayer("form")}document.getElementById("message").value+="[q="+c+"]"+a+"[/q]\n"}function blnk(b,a){document.getElementById(b).style.textDecoration=(a)?"none":"underline";a=a?0:1;tm=setTimeout(\'blnk("\'+b+\'",\'+a+")",1000)}function confirmLink(b,c){var a=confirm(confirmMsg+" :\n"+c);if(a){b.href+="&do=1"}return a};
			/*ONGLETS form http://www.supportduweb.com/*/
			function tab(name){if(document.getElementById(\'tab\'+ idTab) !==null) {document.getElementById(\'tab\'+ idTab).className = \'tabA tab\';document.getElementById(\'tab\'+ name).className = \'tabB tab\';document.getElementById(\'tabContent\'+ idTab).style.display = \'none\';document.getElementById(\'tabContent\'+ name).style.display = \'block\';
				idTab = name;}}
			/*VISUAL EFFECTS from http://www.pluxml.org */
			function setOpacity(obj,opacity){obj.style.minHeight=obj.style.minHeight;opacity=(opacity==100)?99.999:opacity;obj.style.filter="alpha(opacity="+opacity+")";obj.style.KHTMLOpacity=opacity/100;obj.style.MozOpacity=opacity/100;obj.style.opacity=opacity/100}function fadeOut(objId,opacity){var obj=document.getElementById(objId);var stop=document.getElementById(\'noToogle\');if(obj&&!stop){if(opacity==undefined){window.setTimeout("fadeOut(\'"+objId+"\',"+100+")",3000)}else{if(opacity>=0){setOpacity(obj,opacity);opacity-=10;window.setTimeout("fadeOut(\'"+objId+"\',"+opacity+")",100)}else{obj.style.display=\'none\'}}}}
			/*CALENDRIER Script featured on JavaScript Kit- http://www.javascriptkit.com */
			var ds_i_date=new Date();ds_c_month=ds_i_date.getMonth()+1;ds_c_year=ds_i_date.getFullYear()-40;function ds_getel(id){return document.getElementById(id)}function ds_getleft(el){var tmp=el.offsetLeft;el=el.offsetParent; while(el){tmp+=el.offsetLeft;el=el.offsetParent}return tmp}function ds_gettop(el){var tmp=el.offsetTop;el=el.offsetParent; while(el){tmp+=el.offsetTop;el=el.offsetParent}return tmp}var ds_oe=ds_getel(\'ds_calclass\');var ds_ce=ds_getel(\'ds_conclass\');var ds_ob=\'\';function ds_ob_clean(){ds_ob=\'\'}function ds_ob_flush(){ds_oe.innerHTML=ds_ob;ds_ob_clean()}function ds_echo(t){ds_ob+=t}var ds_element;var ds_monthnames=[\''.L_JANUARY.'\',\''.L_FEBRUARY.'\',\''.L_MARCH.'\',\''.L_APRIL.'\',\''.L_MAY.'\',\''.L_JUNE.'\',\''.L_JULY.'\',\''.L_AUGUST.'\',\''.L_SEPTEMBER.'\',\''.L_OCTOBER.'\',\''.L_NOVEMBER.'\',\''.L_DECEMBER.'\'];var ds_daynames=[\''.L_SUND.'\',\''.L_MOND.'\',\''.L_TUES.'\',\''.L_WEDN.'\',\''.L_THUR.'\',\''.L_FRID.'\',\''.L_SATU.'\'];function ds_template_main_above(t){return\'<table cellpadding="3" cellspacing="1" class="ds_tbl">\'+\'<tr>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_py();">&lt;&lt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_pm();">&lt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_hi();" colspan="3">['.CLOSE.']</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_nm();">&gt;</td>\'+\'<td class="ds_head" style="cursor: pointer" onclick="ds_ny();">&gt;&gt;</td>\'+\'</tr>\'+\'<tr>\'+\'<td colspan="7" class="ds_head">\'+t+\'</td>\'+\'</tr>\'+\'<tr>\'}function ds_template_day_row(t){return\'<td class="ds_subhead">\'+t+\'</td>\'}function ds_template_new_week(){return\'</tr><tr>\'}function ds_template_blank_cell(colspan){return\'<td colspan="\'+colspan+\'"></td>\'}function ds_template_day(d,m,y){return\'<td class="ds_cell" onclick="ds_onclick(\'+d+\',\'+m+\',\'+y+\')">\'+d+\'</td>\'}function ds_template_main_below(){return\'</tr>\'+\'</table>\'}function ds_draw_calendar(m,y){ds_ob_clean();ds_echo(ds_template_main_above(ds_monthnames[m-1]+\' \'+y));for(i=0;i<7;i++){ds_echo(ds_template_day_row(ds_daynames[i]))}var ds_dc_date=new Date();ds_dc_date.setMonth(m-1);ds_dc_date.setFullYear(y);ds_dc_date.setDate(1);if(m==1||m==3||m==5||m==7||m==8||m==10||m==12){days=31}else if(m==4||m==6||m==9||m==11){days=30}else{days=(y%4==0)?29:28}var first_day=ds_dc_date.getDay();var first_loop=1;ds_echo(ds_template_new_week());if(first_day!=0){ds_echo(ds_template_blank_cell(first_day))}var j=first_day;for(i=0;i<days;i++){if(j==0&&!first_loop){ds_echo(ds_template_new_week())}ds_echo(ds_template_day(i+1,m,y));first_loop=0;j++;j%=7}ds_echo(ds_template_main_below());ds_ob_flush();/*ds_ce.scrollIntoView()*/}function ds_sh(t){ds_element=t;var ds_sh_date=new Date();ds_c_month=ds_sh_date.getMonth()+1;ds_c_year=ds_sh_date.getFullYear()-40;ds_draw_calendar(ds_c_month,ds_c_year);ds_ce.style.display=\'\';the_left=ds_getleft(t);the_top=ds_gettop(t)+t.offsetHeight;ds_ce.style.left=the_left+\'px\';ds_ce.style.top=the_top+\'px\';/*ds_ce.scrollIntoView()*/}function ds_hi(){ds_ce.style.display=\'none\'}function ds_nm(){ds_c_month++;if(ds_c_month>12){ds_c_month=1;ds_c_year++}ds_draw_calendar(ds_c_month,ds_c_year)}function ds_pm(){ds_c_month=ds_c_month-1;if(ds_c_month<1){ds_c_month=12;ds_c_year=ds_c_year-1}ds_draw_calendar(ds_c_month,ds_c_year)}function ds_ny(){ds_c_year++;ds_draw_calendar(ds_c_month,ds_c_year)}function ds_py(){ds_c_year=ds_c_year-1;ds_draw_calendar(ds_c_month,ds_c_year)}function ds_format_date(d,m,y){m2=\'00\'+m;m2=m2.substr(m2.length-2);d2=\'00\'+d;d2=d2.substr(d2.length-2);return d2+\'-\'+m2+\'-\'+y}function ds_onclick(d,m,y){ds_hi();if(typeof(ds_element.value)!=\'undefined\'){ds_element.value=ds_format_date(d,m,y)}else if(typeof(ds_element.innerHTML)!=\'undefined\'){ds_element.innerHTML=ds_format_date(d,m,y)}else{alert(ds_format_date(d,m,y))}}
	';

			file_put_contents(MU_JS.'scripts.js', $js);
		}	
	} 

	/**
	*
	* CRÉATION DES FEUILLES DE STYLE (css)
	*/
	private function mkcss() {

		$default = '/* =Uforum -----------------------------------------------------------------------------*/ .Ligne{font-size:11px; border-bottom:1px solid [br]; padding-left:4px; vertical-align:middle} .mess{font-size:16px; border-bottom:1px solid [br]; text-align:center; vertical-align:middle} /* Formulaire */ .red{color:#c00} .blue{color:#00f} .orange{color:#f90} .grey{color:#aaa} .avatar{width:80px; height:80px} .avatarTD{width:18%; font-size:12px; font-family:Courrier,Monaco,monospaced; text-align:right; padding-bottom:10px} .messageTD{padding:10px; font-size:14px} .tooltipTD{padding-left:5px; vertical-align:middle; font-size:9px; font-family:Courrier,Monaco,monospaced} .formTD{background:[lt]; font-size:12px; color:#666; padding:3px 6px 3px 0px; vertical-align:middle; text-align:right; width:150px} .titreCol{background:[lt]; font-size:13px; color:#666; padding:4px; vertical-align:middle} .colDate{font-size:11px; color:#666; padding:4px; vertical-align:middle; border-bottom:1px solid [br]} /* BOUTONS */ .titreLien:link, .titreLien:visited{color:#fff; text-decoration:none} .titreLien:hover{color:#fff; text-decoration:underline} .bImage:link, .bImage:visited, .bImage:hover{padding:2px; opacity:.6} .bImage:hover{padding:2px; opacity:1} .Lien:link, .Lien:visited{color:[lk]; text-decoration:none} .Lien:hover{color:[lk]; text-decoration:underline} .LienNonLu:link, .LienNonLu:visited{color:[ct]; text-decoration:none} .LienNonLu:hover{color:[ct]; text-decoration:underline} .avatarTooltip:hover{cursor:help} .uForum{background:[dk]; display:block; text-align:left; border:1px solid [br]; padding:6px; margin-bottom:3px; color:#fff; font-size:30px; font-weight:100} .Box{background:#fff; border:1px solid [br]; vertical-align:middle; padding:4px; margin-bottom:3px; text-align:left} .titreDiv{background:[dk]; border:1px solid [br]; color:#fff; vertical-align:middle; padding:6px; margin-bottom:3px; text-align:left} .titrePost{text-align:left; font-size:14px; color:[dk]} .gradient{background:[dk]; border:1px solid [br]; color:#fff; vertical-align:middle; padding:6px; margin-bottom:3px; text-align:left; font-weight:bold} .datePost{color:#9a9a9a; font-size:12px; font-family:Courrier,Monaco,monospaced; text-align:right; padding-top:5px; padding-right:3px} .postMod{font-size:9px; font-family:Courrier,Monaco,monospaced; text-align:right; padding-top:6px; padding-right:3px; color:[dk]} .poster{color:#f90} .poster:link, .poster:visited{text-decoration:none} .poster:hover{text-decoration:underline} .attachLink{text-decoration:none; display:block; padding-top:5px; font-size:9px; font-family:Courrier,Monaco,monospaced; text-align:right} .attachLink:link, .attachLink:visited{color:#999} .attachLink:hover{color:[ct]} .toggle{padding-top:10px; margin:0px; display:none; visibility:hidden} .toggleLink{text-decoration:none; display:block; padding:3px 3px 3px 6px; margin:2px} .toggleLink:link, .toggleLink:visited{color:#666; background:[lt]} .toggleLink:hover{background:#b1c5d0; color:#fff} .tooltip{position:absolute; border:1px solid #999; text-align:left; display:none; background-color:rgba(255,255,255,0.9); padding:6px; color:#666; font-size:11px; z-index:999; width:400px} @keyframes blink { 0% { color: red; } 100% { color: black; } } @-webkit-keyframes blink { 0% { color: red; } 100% { color: black; } } .blink { -webkit-animation: blink 0.5s linear infinite; -moz-animation: blink 0.5s linear infinite; -ms-animation: blink 0.5s linear infinite; -o-animation: blink 0.5s linear infinite; animation: blink 0.5s linear infinite; } /* =Layout -----------------------------------------------------------------------------*/ .wrapper { margin: auto; max-width: 980px; padding: 36px 10px; } #main aside { background-color: #f6f6f6; } #blocks li { background-color: #f8f8f8; padding: 3.3em 0; text-align: center; } .well { display: block; background-color: #f8f8f8; padding: 3.3em 0; border-radius: 5px } .link-show-code { background-color: #eee; border-radius: 10px; color: #555; font-size: 12px; display: inline-block; line-height: 1; padding: 5px 11px; text-decoration: none; } .link-show-code:hover { background-color: #ef6465; color: #fff; } .link-show-code-active { background-color: #444; color: #fff; padding: 5px 14px; } .label {text-transform: uppercase; font-size: 9px !important; font-weight: bold} /* =Editor -----------------------------------------------------------------------------*/ ul.smileys { width: 170px } .smileys li{ float:left } /* =Header -----------------------------------------------------------------------------*/ #header { overflow: hidden; margin-bottom: 1.5em; border-bottom: 1px solid #eee; } #header h1 { float: left; margin: 0; } #header nav { padding-top: 10px; float: right; } /* =Footer -----------------------------------------------------------------------------*/ #footer { border-top: 1px solid #eee; padding-top: 1.5em; margin: 1.5em 0; font-size: .85em; } #footer span { float: right; } /* =Tablet (Portrait) -----------------------------------------------------------------------------*/ @media only screen and (min-width: 768px) and (max-width: 959px) { .wrapper { width: 748px; } } /* =Mobile (Portrait) -----------------------------------------------------------------------------*/ @media only screen and (max-width: 767px) { .wrapper { width: 300px; } #nav, #header h1 { float: none; } #header h1 { margin-bottom: .5em; } #nav ul li { margin: 0; float: none; margin-bottom: 1px; background-color: #f6f6f6; } #nav ul li a, #nav ul li span { display: block; padding: 2px 5px; } } /* =Mobile (Landscape) -----------------------------------------------------------------------------*/ @media only screen and (min-width: 480px) and (max-width: 767px) { .wrapper { width: 420px; } } ';
		$main = 'html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}html,body,div,span,object,iframe,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video,h1,h2,h3,h4,h5,h6{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;font-style:normal}a:active,a:hover{outline:0}button,input{line-height:normal}button,select{text-transform:none}article,aside,details,figcaption,figure,footer,header,hgroup,main,nav,section,summary{display:block}audio,canvas,video{display:inline-block}audio:not([controls]){display:none;height:0}blockquote,q{quotes:none}blockquote p:before,blockquote p:after,q:before,q:after{content:\'\';content:none}table{border-collapse:collapse;border-spacing:0}caption,th,td{text-align:left;vertical-align:top;font-weight:normal}thead th,thead td{font-weight:bold;vertical-align:bottom}a img,th img,td img{vertical-align:top}button,input,select,textarea{margin:0}textarea{overflow:auto;vertical-align:top}button{width:auto;overflow:visible}input[type=button],input[type=submit],button{cursor:pointer}input[type="radio"],input[type="checkbox"]{font-size:110%;box-sizing:border-box}input[type="search"]{-webkit-appearance:textfield;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}hr{display:block;height:1px;border:0;border-top:1px solid #ddd}.group:after{content:".";display:block;height:0;clear:both;visibility:hidden}body{background:#fff;color:#333;font-size:.875em;line-height:1.65em;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif}a{color:#369}a:focus,a:hover{color:#ef6465}h1,h2,h3,h4,h5,h6{color:#222;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif;font-weight:bold}h1{font-size:2.142em;line-height:1.1333em;margin-bottom:.2666em}h2{font-size:1.714em;line-height:1.1666em;margin-bottom:.4555em}h3{font-size:1.429em;line-height:1.4em;margin-bottom:.4em}h4{font-size:1.143em;line-height:1.65em;margin-bottom:.4555em}h5{font-size:1em;line-height:1.65em;margin-bottom:.5em}h6{font-size:.857em;line-height:1.5em;margin-bottom:.4555em;text-transform:uppercase}hgroup h1,hgroup h2,hgroup h3,hgroup h4,hgroup h5,hgroup h6{margin-bottom:0}hgroup{margin-bottom:.6em}.subheader{font-weight:300;color:#888}h1.subheader{font-size:1.357em;line-height:1.263em}h2.subheader{font-size:1.214em;line-height:1.412em}h3.subheader{font-size:1em;line-height:1.286em}h4.subheader,h5.subheader{font-size:.95em;line-height:1.385em}h6.subheader{font-size:.8em;line-height:1.364em}p,ul,ol,dl,dd,dt,blockquote,td,th{line-height:1.65em}ul,ol,ul ul,ol ol,ul ol,ol ul{margin:0 0 0 2em}ol ol li{list-style-type:lower-alpha}ol ol ol li{list-style-type:lower-roman}p,ul,ol,dl,blockquote,hr,pre,table,form,fieldset,figure{margin-bottom:1.65em}dl dt{font-weight:bold}dd{margin-left:1em}blockquote{margin-bottom:1.65em;position:relative;color:#777;padding-left:1.65em;margin-left:1.65em;border-left:1px solid #ddd}blockquote small,cite{color:#999;font-style:normal}blockquote p{margin-bottom:.5em}small,blockquote cite{font-size:.85em;line-height:1}blockquote .pull-right,.units-row blockquote .pull-right{float:none;text-align:right;display:block}address{font-style:italic}del{text-decoration:line-through}abbr[title],dfn[title]{border-bottom:1px dotted #000;cursor:help}strong,b{font-weight:bold}em,i{font-style:italic}sub,sup{font-size:.7em;line-height:0;position:relative}sup{top:-0.5em}sub{bottom:-0.25em}figcaption{font-size:.85em;font-style:italic}ins,mark{background-color:#fe5;color:#000;text-decoration:none}pre,code,kbd,samp{font-size:90%;font-family:Consolas,Monaco,monospace,sans-serif}pre{font-size:90%;color:#444;background:#f5f5f5;padding:.85em;overflow:auto}code{padding:2px 3px;display:inline-block;line-height:1;background:#f5f5f5;border:1px solid #ddd}kbd{padding:2px 6px 1px 6px;line-height:1;display:inline-block;border-radius:.3em;box-shadow:0 2px 0 rgba(0,0,0,0.2),0 0 0 1px #fff inset;background-color:#fafafa;border-color:#ccc #ccc white;border-style:solid solid none;border-width:1px 1px medium;color:#444;font-weight:normal;white-space:nowrap}input[type="text"],input[type="password"],input[type="email"],textarea{font-size:.95em}fieldset{padding:1.65em;margin-bottom:1.65em;border:1px solid #e3e3e3}legend{font-weight:bold;padding:0 1em}.com{color:#888}.lit{color:#195f91}.pun,.opn,.clo{color:#93a1a1}.fun{color:#005cb9}.str,.atv{color:#8a6343}.kwd,.linenums,.tag{color:#000}.typ,.atn,.dec,.var{color:#666}.pln{color:#5890ad}tfoot th,tfoot td{background-color:#f2f2f2}th,td{border-bottom:1px solid #eee;padding:.5em .8em}table caption{text-transform:uppercase;padding:0 1em;color:#999;font-size:.85em}table.table-flat td,table.table-flat th{border:0;padding:0}table.table-simple td,table.table-simple th{border:0;padding:.825em .7em .825em 0}table.table-simple caption{padding-left:0}table.table-bordered td,table.table-bordered th{border:1px solid #ddd}table.table-stroked td,table.table-stroked th{border-bottom:1px solid #eee}table.table-striped tbody tr:nth-child(odd) td{background-color:#f5f5f5}table.table-hovered tbody tr:hover td,table.table-hovered thead tr:hover th{background-color:#f6f6f6}.table-container{width:100%;overflow:auto;margin-bottom:1.65em}.table-container table{margin-bottom:0}.table-container::-webkit-scrollbar{-webkit-appearance:none;width:14px;height:14px}.table-container::-webkit-scrollbar-thumb{border-radius:8px;border:3px solid #fff;background-color:rgba(0,0,0,0.3)}.lists-simple{margin-left:0;list-style:none}.lists-simple ul,.lists-simple ol{list-style:none;margin-left:1.5em}.lists-dash{margin-left:18px}.lists-dash li{list-style-type:none}.lists-dash li:before{content:"\2013";position:relative;margin-left:-10px;left:-7px}.forms label{display:block;margin-bottom:1.65em}.forms input[type="text"],.forms input[type="password"],.forms input[type="email"],.forms input[type="url"],.forms input[type="phone"],.forms input[type="tel"],.forms input[type="number"],.forms input[type="datetime"],.forms input[type="date"],.forms input[type="search"],.forms input[type="range"],.forms input[type="file"],.forms input[type="datetime-local"],.forms textarea,.forms select,.forms button{display:block}.forms-inline input[type="text"],.forms-inline input[type="password"],.forms-inline input[type="email"],.forms-inline input[type="url"],.forms-inline input[type="phone"],.forms-inline input[type="tel"],.forms-inline input[type="number"],.forms-inline input[type="datetime"],.forms-inline input[type="date"],.forms-inline input[type="search"],.forms-inline input[type="range"],.forms-inline input[type="file"],.forms-inline input[type="datetime-local"],.forms-inline textarea,.forms-inline select,.forms-inline button,.forms-inline-list input[type="text"],.forms-inline-list input[type="password"],.forms-inline-list input[type="email"],.forms-inline-list input[type="url"],.forms-inline-list input[type="phone"],.forms-inline-list input[type="tel"],.forms-inline-list input[type="number"],.forms-inline-list input[type="datetime"],.forms-inline-list input[type="date"],.forms-inline-list input[type="search"],.forms-inline-list input[type="range"],.forms-inline-list input[type="file"],.forms-inline-list input[type="datetime-local"],.forms-inline-list textarea,.forms-inline-list select,.forms-inline-list button{display:inline-block}.forms-list,.forms-inline-list{margin:0;padding:0;margin-bottom:1.65em;list-style:none}.forms-list label,.forms-inline-list li,.forms-inline-list li label{display:inline-block;margin-bottom:0}.forms-inline-list li label{margin-right:1.65em}.forms-list li{margin-bottom:6px}.forms-desc{margin-top:4px;color:#999;font-size:.85em;line-height:1.4em}.forms fieldset{padding-bottom:.5em;border-radius:.5em}fieldset.forms-row{padding:0;border:0;margin-bottom:0}.forms-columnar:after{content:".";display:block;height:0;clear:both;visibility:hidden}.forms-columnar input[type="range"],.forms-columnar input[type="file"],.forms-columnar select[multiple="multiple"]{display:inline-block}.forms-columnar p{position:relative;padding-left:170px}.forms-columnar label{float:left;width:150px;text-align:right;top:0;left:0;position:absolute}.forms-columnar .forms-list,.forms-columnar .forms-inline-list{margin-left:170px}.forms-columnar .forms-list label,.forms-columnar .forms-inline-list label{position:static;float:none;width:auto;text-align:left;margin-right:0}.forms-columnar .forms-inline-list label{margin-right:1.65em}.forms-push{position:relative;padding-left:170px}.forms-section{font-weight:bold;border-bottom:1px solid #eee;padding:0 0 10px 0;margin-bottom:1em;line-height:1}.forms-columnar .forms-section{padding-left:170px}input[type="radio"],input[type="checkbox"]{position:relative;top:-1px}input[type="text"],input[type="password"],input[type="email"],input[type="url"],input[type="phone"],input[type="tel"],input[type="number"],input[type="datetime"],input[type="date"],input[type="search"],input[type="datetime-local"],textarea,select[multiple="multiple"]{position:relative;z-index:2;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif;border:1px solid #ccc;margin:0;padding:3px 2px;background-color:white;color:#333;font-size:1em;line-height:1;border-radius:1px;box-shadow:0 1px 2px rgba(0,0,0,0.1) inset;-webkit-transition:border ease .5s;-moz-transition:border ease .5s;-o-transition:border ease .5s;transition:border ease .5s}input[type="range"]{position:relative;top:3px}textarea{line-height:1.4em}select{margin-bottom:0!important}.error,.success{margin-left:5px;font-weight:normal;font-size:.85em}input.input-error,textarea.input-error,select.input-error,.input-error{border-color:#da3e5a;box-shadow:0 0 0 2px rgba(218,62,90,0.3),0 1px 2px rgba(0,0,0,0.2) inset}input.input-success,textarea.input-success,select.input-success,.input-success{border-color:#18a011;box-shadow:0 0 0 2px rgba(24,160,17,0.3),0 1px 2px rgba(0,0,0,0.2) inset}input.input-gray,textarea.input-gray,select.input-gray,.input-gray{border-color:#ccc;box-shadow:0 0 0 2px rgba(204,204,204,0.3),0 1px 2px rgba(0,0,0,0.2) inset}input:focus,textarea:focus{outline:0;border-color:#5ca9e4;box-shadow:0 0 0 2px rgba(70,161,231,0.3),0 1px 2px rgba(0,0,0,0.2) inset}input.input-search,input[type="search"]{padding-right:10px;padding-left:10px;margin-bottom:0;border-radius:15px}.input-append,.input-prepend{display:inline-block;background-color:#eee;height:23px;border:1px solid #ccc;margin:0;padding:1px 8px;color:#333;font-size:1em;line-height:23px}.input-prepend{margin-right:-1px}.input-append{position:relative;z-index:1;margin-left:-1px}:-moz-placeholder{color:#999}::-moz-placeholder{color:#999}:-ms-input-placeholder{color:#999}::-webkit-input-placeholder{color:#999;padding:2px}.color-black{color:#000}.color-gray-dark{color:#555}.color-gray{color:#777}.color-gray-light{color:#999}.color-white{color:#fff}.color-red,.error{color:#ef6465}.color-green,.success{color:#90af45}.color-orange{color:#f48a30}.color-green{color:#90af45}.color-blue{color:#1c7ab4}.color-yellow{color:#f3c835}a.color-white:focus,a.color-white:hover{color:#bfbfbf;color:rgba(255,255,255,0.6)}a.color-green:focus,a.color-green:hover,a.color-red:focus,a.color-red:hover,a.color-error:focus,a.color-error:hover{color:#000}.label,.label-badge{border-radius:2em;border:1px solid #ddd;font-size:.7em;display:inline-block;position:relative;top:-1px;line-height:1;padding:3px 8px;color:#000;background-color:#fff;text-decoration:none}.label-badge{top:-4px;left:-1px}.label-data{color:#999;background:0;border:0;padding:0}a.label:hover{color:#000;filter:alpha(opacity=60);-moz-opacity:.6;opacity:.6}.label-black{background-color:#000}.label-red{background-color:#ef6465}.label-orange{background-color:#f48a30}.label-green{background-color:#90af45}.label-blue{background-color:#1c7ab4}.label-yellow{background-color:#f3c835}.label-black,.label-red,.label-orange,.label-green,.label-blue,.label-yellow{border:0;color:#fff;padding:4px 8px}a.label-black:hover,a.label-red:hover,a.label-orange:hover,a.label-green:hover,a.label-blue:hover,a.label-yellow:hover{color:#fff}.label-small{font-size:.6em;padding:3px 5px}.btn{text-decoration:none;color:#000;border-radius:2px;font-family:\'PT Sans\',Arial,"Helvetica Neue",Helvetica,Tahoma,sans-serif;border:1px solid #ccc;border-bottom-color:#b3b3b3;line-height:1;padding:.7em 1.1em .6em 1.1em;font-weight:500;font-size:.85em;background-color:#f1f1f1;background-image:-moz-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-ms-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-webkit-gradient(linear,0 0,0 100%,from(#fcfcfc),to(#e0e0e0));background-image:-webkit-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:-o-linear-gradient(top,#fcfcfc,#e0e0e0);background-image:linear-gradient(top,#fcfcfc,#e0e0e0);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#fcfcfc\',endColorstr=\'#e0e0e0\',GradientType=0);text-shadow:0 1px 0 #fff;box-shadow:none}.btn:hover{color:#000;background:#e0e0e0}.btn-black{border-color:#000;background-color:#2e2e2e;background-image:-moz-linear-gradient(top,#4d4d4d,#000);background-image:-ms-linear-gradient(top,#4d4d4d,#000);background-image:-webkit-gradient(linear,0 0,0 100%,from(#4d4d4d),to(#000));background-image:-webkit-linear-gradient(top,#4d4d4d,#000);background-image:-o-linear-gradient(top,#4d4d4d,#000);background-image:linear-gradient(top,#4d4d4d,#000);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#4d4d4d\',endColorstr=\'#000\',GradientType=0)}.btn-red{border-color:#c01415;border-bottom-color:#910f10;background-color:#e54546;background-image:-moz-linear-gradient(top,#ef6465,#d71618);background-image:-ms-linear-gradient(top,#ef6465,#d71618);background-image:-webkit-gradient(linear,0 0,0 100%,from(#ef6465),to(#d71618));background-image:-webkit-linear-gradient(top,#ef6465,#d71618);background-image:-o-linear-gradient(top,#ef6465,#d71618);background-image:linear-gradient(top,#ef6465,#d71618);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ef6465\',endColorstr=\'#d71618\',GradientType=0)}.btn-orange{border-color:#cd640b;border-bottom-color:#9c4c08;background-color:#ee7f22;background-image:-moz-linear-gradient(top,#f48a30,#e5700c);background-image:-ms-linear-gradient(top,#f48a30,#e5700c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#f48a30),to(#e5700c));background-image:-webkit-linear-gradient(top,#f48a30,#e5700c);background-image:-o-linear-gradient(top,#f48a30,#e5700c);background-image:linear-gradient(top,#f48a30,#e5700c);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f48a30\',endColorstr=\'#e5700c\',GradientType=0)}.btn-green{border-color:#5a6d2b;border-bottom-color:#3c491d;background-color:#7e993c;background-image:-moz-linear-gradient(top,#90af45,#63782f);background-image:-ms-linear-gradient(top,#90af45,#63782f);background-image:-webkit-gradient(linear,0 0,0 100%,from(#90af45),to(#63782f));background-image:-webkit-linear-gradient(top,#90af45,#63782f);background-image:-o-linear-gradient(top,#90af45,#63782f);background-image:linear-gradient(top,#90af45,#63782f);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#90af45\',endColorstr=\'#63782f\',GradientType=0)}.btn-blue{border-color:#104769;border-bottom-color:#09293d;background-color:#196ea2;background-image:-moz-linear-gradient(top,#1c7ab4,#155c88);background-image:-ms-linear-gradient(top,#1c7ab4,#155c88);background-image:-webkit-gradient(linear,0 0,0 100%,from(#1c7ab4),to(#155c88));background-image:-webkit-linear-gradient(top,#1c7ab4,#155c88);background-image:-o-linear-gradient(top,#1c7ab4,#155c88);background-image:linear-gradient(top,#1c7ab4,#155c88);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#1c7ab4\',endColorstr=\'#155c88\',GradientType=0)}.btn-yellow{border-color:#b7900b;border-bottom-color:#876a08;background-color:#e5b925;background-image:-moz-linear-gradient(top,#f3c835,#cfa30c);background-image:-ms-linear-gradient(top,#f3c835,#cfa30c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#f3c835),to(#cfa30c));background-image:-webkit-linear-gradient(top,#f3c835,#cfa30c);background-image:-o-linear-gradient(top,#f3c835,#cfa30c);background-image:linear-gradient(top,#f3c835,#cfa30c);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f3c835\',endColorstr=\'#cfa30c\',GradientType=0)}.btn-black{text-shadow:0 -1px 0 #000}.btn-red,.btn-orange,.btn-green,.btn-blue,.btn-yellow{text-shadow:0 -1px 0 rgba(0,0,0,0.24)}.btn-black,.btn-red,.btn-orange,.btn-green,.btn-blue,.btn-yellow{color:#fff}.btn-black:hover,.btn-red:hover,.btn-orange:hover,.btn-green:hover,.btn-blue:hover,.btn-yellow:hover{color:rgba(255,255,255,0.8)}.btn-black:hover{background:#000}.btn-red:hover{background:#d71618}.btn-orange:hover{background:#e5700c}.btn-green:hover{background:#63782f}.btn-blue:hover{background:#155c88}.btn-yellow:hover{background:#cfa30c}.btn-small{font-size:.7em}.btn-big{font-size:1.2em;line-height:1.65em;padding-left:1.5em;padding-right:1.5em}.btn-round{border-radius:20px}.btn-active,.btn-active:hover,.btn.disabled,.btn[disabled],.btn-disabled,.btn-disabled:hover{filter:alpha(opacity=100);-moz-opacity:1;opacity:1;background:#d1d1d1;border:1px solid #b3b3b3;text-shadow:0 1px 1px #fff}.btn-active,.btn-active:hover{color:#666}.btn.disabled,.btn[disabled],.btn-disabled,.btn-disabled:hover{color:#999}.btn:focus .halflings,.btn:hover .halflings{color:#555}.btn-black:hover .halflings,.btn-red:hover .halflings,.btn-orange:hover .halflings,.btn-green:hover .halflings,.btn-blue:hover .halflings,.btn-yellow:hover .halflings{color:rgba(255,255,255,0.8)}.btn-disabled:hover .halflings{color:#999}.btn-active .halflings{color:#555}.btn-single,.btn-group{display:inline-block;margin-right:2px;vertical-align:bottom}.btn-single:after,.btn-group:after{content:".";display:block;height:0;clear:both;visibility:hidden}.btn-single>.btn,.btn-single>input,.btn-group>.btn,.btn-group>input{float:left;border-radius:0;margin-left:-1px}.btn-single>.btn{border-radius:4px}.btn-group>.btn:first-child{border-radius:4px 0 0 4px}.btn-group>.btn:last-child{border-radius:0 4px 4px 0}.btn-group>.btn.btn-round:first-child,.btn-group>.input-search:first-child{border-radius:15px 0 0 15px}.btn-group>.btn.btn-round:last-child,.btn-group>.input-search:last-child{border-radius:0 15px 15px 0}.btn-append,.btn-group .btn{padding:7px 1.1em 6px 1.1em}.btn-append{position:relative;top:-1px;margin-left:-2px;border-radius:0 4px 4px 0}@-moz-document url-prefix("http://"){input[type=submit].btn::-moz-focus-inner,button.btn::-moz-focus-inner{border:0;padding:0}}.first-letter::first-letter {font-size:4em;line-height:.75em;float:left;position:relative;padding-right:6px;margin-top:-2px;font-weight:normal;color:#333}.supersmall{font-size:.7em}.small{font-size:.85em}.big{font-size:1.2em}input.big{padding:2px 0;font-size:1.2em}.text-centered{text-align:center}.text-right{text-align:right}.text-uppercase{text-transform:uppercase}.nowrap{white-space:nowrap}.zero{margin:0!important;padding:0!important}.clear{clear:both}.last{margin-right:0!important}.pause{margin-bottom:.75em!important}.end{margin-bottom:0!important}.handle{cursor:move}.normal{font-weight:normal}.bold{font-weight:bold}.italic{font-style:italic}.req,.required{font-weight:normal;color:#ef6465}.highlight{background-color:#ffff9e!important}.close{padding:4px 6px;line-height:1;font-size:18px;cursor:pointer;color:#000;text-decoration:none;opacity:.4}.close:before{content:\'\00D7\'}.close:hover{color:#000;opacity:1}.image-left{float:left;margin:0 1em 1em 0}.image-right{float:right;margin:0 0 1em 1em}.image-left img,.image-right img{position:relative;top:.4em}.image-centered{text-align:center}.image-container:after{content:".";display:block;height:0;clear:both;visibility:hidden}.image-content{overflow:hidden}.nav-h,.nav-g{margin:20px 0 1.65em 0;height:50px;padding:10px;border:1px solid #dedede;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}.nav-h:after,.nav-g:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-h ul,.nav-g ul{list-style:none;margin:0}.nav-h ul:after,.nav-g ul:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-h ul li,.nav-g ul li{float:right;margin-right:1.5em}.nav-h ul li a,.nav-h ul li span,.nav-g ul li a,.nav-g ul li span{display:block}.nav-h ul li a,.nav-g ul li a{text-decoration:none}.nav-h ul li a:hover,.nav-g ul li a:hover{color:#ef6465;text-decoration:underline}.nav-h ul li span,.nav-g ul li span{color:#999}.nav-v{margin-bottom:1.65em}.nav-v ul{list-style:none;margin:0}.nav-v ul li{border-bottom:1px solid #eee}.nav-v ul li ul{margin-left:2em;font-size:.95em}.nav-v ul li ul li:last-child{border-bottom:0}.nav-v ul li ul li a,.nav-v ul li ul li span{padding:4px 0}.nav-v ul li a,.nav-v ul li span{display:block;padding:5px 0}.nav-v ul li a{text-decoration:none}.nav-v ul li a:hover{color:#ef6465;text-decoration:underline}.nav-v ul li span{color:#999}.nav-stacked ul{border:1px solid #eee;border-bottom:0}.nav-stacked ul li a,.nav-stacked ul li span{padding:5px 10px}.nav-stacked ul li a:hover{background-color:#f5f5f5}.nav-stats li{position:relative}.nav-stats li a,.nav-stats li span{padding-right:50px}.nav-stats .label,.nav-stats .label-badge{position:absolute;top:50%;margin-top:-8px;right:0}.nav-stats.nav-stacked .label,.nav-stats.nav-stacked .label-badge{right:4px}.nav-stats .label.label-data,.nav-stacked .label-data{margin-top:-6px;right:6px}.nav-v h1,.nav-v h2,.nav-v h3,.nav-v h4,.nav-v h5,.nav-v h6{margin-top:1.5em;margin-bottom:3px}.nav-v h1:first-child,.nav-v h2:first-child,.nav-v h3:first-child,.nav-v h4:first-child,.nav-v h5:first-child,.nav-v h6:first-child{margin-top:0}.breadcrumbs{margin-bottom:1.65em}.breadcrumbs:after{content:".";display:block;height:0;clear:both;visibility:hidden}.breadcrumbs, .breadcrumbs ul{font-size:.9em;color:#999;list-style:none;margin:0;}.breadcrumbs:after, .breadcrumbs ul:after{content:".";display:block;height:0;clear:both;visibility:hidden}.breadcrumbs li,.breadcrumbs ul li{float:left;margin-right:3px}.breadcrumbs li+li:before{content:" > ";color:#aaa;font-size:12px;margin:0 3px;position:relative;top:-1px}.breadcrumbs-sections li+li:before{content:" | ";top:0}.breadcrumbs-path li+li:before{content:" / ";top:0}.breadcrumbs li a, .breadcrumbs ul li a{color:#000;text-decoration:none}.breadcrumbs li a.active, .breadcrumbs ul li a.active{color:#999}.breadcrumbs li a:hover, .breadcrumbs ul li a:hover{color:#000;text-decoration:underline}.nav-tabs{border-bottom:1px solid #e3e3e3;margin-bottom:1.65em}.nav-tabs:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-tabs ul{list-style:none;margin:0}.nav-tabs ul:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-tabs ul li{float:left;margin-right:2px}.nav-tabs ul li a,.nav-tabs ul li span{display:block;line-height:1;padding:8px 12px 9px 12px}.nav-tabs ul li a{color:#999;text-decoration:none}.nav-tabs ul li a:focus,.nav-tabs ul li a:hover{color:#000;text-decoration:underline}.nav-tabs ul li .active,.nav-tabs ul li span{color:#000;background:#fff;margin-top:-2px;position:relative;padding:8px 11px 9px 11px;border:1px solid #ddd;border-bottom:1px solid #fff;bottom:-1px}.nav-tabs ul li .active{cursor:default}.nav-tabs-v{border:0;border-right:1px solid #e3e3e3}.nav-tabs-v ul li{float:none}.nav-tabs-v ul li span{margin-top:0;bottom:0;margin-right:-3px;border:1px solid #ddd;border-right:1px solid #fff}.nav-pills{margin-bottom:1.15em}.nav-pills:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-pills ul{list-style:none;margin:0}.nav-pills ul:after{content:".";display:block;height:0;clear:both;visibility:hidden}.nav-pills ul li{float:left;margin-right:.5em;margin-bottom:.6499999999999999em}.nav-pills ul li a,.nav-pills ul li span{display:block;padding:6px 15px;line-height:1;border-radius:15px}.nav-pills ul li a{color:#777;text-decoration:none;background-color:#f3f4f5}.nav-pills ul li a:hover{color:#555;text-decoration:underline}.nav-pills ul li .active,.nav-pills ul li .active:hover,.nav-pills ul li span{color:#777;padding:5px 14px;border:1px solid #ddd;background:0}.nav-pills ul li .active,.nav-pills ul li .active:hover{cursor:default;text-decoration:none}.pagination{position:relative;left:-9px;margin-left:0;list-style:none}.pagination:after{content:".";display:block;height:0;clear:both;visibility:hidden}.pagination li{float:left;margin-right:2px}.pagination li a,.pagination li span{display:block;padding:7px 9px;line-height:1;border-radius:2em;color:#000;text-decoration:none}.pagination span{border:1px solid #ddd}.pagination li a:focus,.pagination li a:hover{text-decoration:underline;background-color:#333;color:#fff}.pagination li.pagination-older{margin-left:7px}.pagination li.pagination-older a,.pagination li.pagination-newest a,.pagination li.pagination-older span,.pagination li.pagination-newest span{padding:5px 15px;border-radius:2em;border:1px solid #ddd}.pagination li.pagination-older span,.pagination li.pagination-newest span{border-color:#eee;color:#999}.pagination li.pagination-pull{float:right;margin-right:-7px;margin-left:.5em}.message{position:relative;padding:9px 13px;border:1px solid #f7dc7d;border-radius:5px;margin-bottom:1.65em;color:#9f7d09;background-color:#fdf7e2}.message-error{color:#c01415;border-color:#f9c0c1;background-color:#fdefef}.message-success{color:#546628;border-color:#d1dfae;background-color:#f0f5e5}.message-info{color:#124d72;border-color:#b3dbf3;background-color:#dff0fa}.message header{font-weight:bold;font-size:1.2em}.message .close{cursor:pointer;position:absolute;right:3px;top:6px}.units-container:after,.units-row-end:after,.units-row:after{content:".";display:block;height:0;clear:both;visibility:hidden}.units-container{padding-top:1px;margin-top:-1px}.units-container,.units-row-end,.units-row{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.units-row{margin-bottom:1.5em}.units-row-end{margin-bottom:0}.width-100,.unit-100{width:100%}.width-80,.unit-80{width:80%}.width-75,.unit-75{width:75%}.width-70,.unit-70{width:70%}.width-66,.unit-66{width:66.6%}.width-60,.unit-60{width:60%}.width-50,.unit-50{width:50%}.width-40,.unit-40{width:40%}.width-33,.unit-33{width:33.3%}.width-30,.unit-30{width:30%}.width-25,.unit-25{width:25%}.width-20,.unit-20{width:20%}input.width-100,input.unit-100{width:98.6%}textarea.width-100,textarea.unit-100{width:98.8%}select.width-100,select.unit-100{width:99.4%}.width-100,.width-80,.width-75,.width-70,.width-66,.width-60,.width-50,.width-40,.width-33,.width-30,.width-25,.width-20,.units-row .unit-100,.units-row .unit-80,.units-row .unit-75,.units-row .unit-70,.units-row .unit-66,.units-row .unit-60,.units-row .unit-50,.units-row .unit-40,.units-row .unit-33,.units-row .unit-30,.units-row .unit-25,.units-row .unit-20,.units-row-end .unit-100,.units-row-end .unit-80,.units-row-end .unit-75,.units-row-end .unit-70,.units-row-end .unit-66,.units-row-end .unit-60,.units-row-end .unit-50,.units-row-end .unit-40,.units-row-end .unit-33,.units-row-end .unit-30,.units-row-end .unit-25,.units-row-end .unit-20{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.units-row .unit-80,.units-row .unit-75,.units-row .unit-70,.units-row .unit-66,.units-row .unit-60,.units-row .unit-50,.units-row .unit-40,.units-row .unit-33,.units-row .unit-30,.units-row .unit-25,.units-row .unit-20,.units-row-end .unit-100,.units-row-end .unit-80,.units-row-end .unit-75,.units-row-end .unit-70,.units-row-end .unit-66,.units-row-end .unit-60,.units-row-end .unit-50,.units-row-end .unit-40,.units-row-end .unit-33,.units-row-end .unit-30,.units-row-end .unit-25,.units-row-end .unit-20{float:left;margin-left:3%}.units-row .unit-80:first-child,.units-row .unit-75:first-child,.units-row .unit-70:first-child,.units-row .unit-66:first-child,.units-row .unit-60:first-child,.units-row .unit-50:first-child,.units-row .unit-40:first-child,.units-row .unit-33:first-child,.units-row .unit-30:first-child,.units-row .unit-25:first-child,.units-row .unit-20:first-child,.units-row-end .unit-100:first-child,.units-row-end .unit-80:first-child,.units-row-end .unit-75:first-child,.units-row-end .unit-70:first-child,.units-row-end .unit-66:first-child,.units-row-end .unit-60:first-child,.units-row-end .unit-50:first-child,.units-row-end .unit-40:first-child,.units-row-end .unit-33:first-child,.units-row-end .unit-30:first-child,.units-row-end .unit-25:first-child,.units-row-end .unit-20:first-child{margin-left:0}.units-row .unit-80,.units-row-end .unit-80{width:79.4%}.units-row .unit-75,.units-row-end .unit-75{width:74.25%}.units-row .unit-70,.units-row-end .unit-70{width:69.1%}.units-row .unit-66,.units-row-end .unit-66{width:65.66666666666666%}.units-row .unit-60,.units-row-end .unit-60{width:58.800000000000004%}.units-row .unit-50,.units-row-end .unit-50{width:48.5%}.units-row .unit-40,.units-row-end .unit-40{width:38.2%}.units-row .unit-30,.units-row-end .unit-30{width:27.9%}.units-row .unit-33,.units-row-end .unit-33{width:31.333333333333332%}.units-row .unit-25,.units-row-end .unit-25{width:22.75%}.units-row .unit-20,.units-row-end .unit-20{width:17.6%}.unit-push-80,.unit-push-75,.unit-push-70,.unit-push-66,.unit-push-60,.unit-push-50,.unit-push-40,.unit-push-33,.unit-push-30,.unit-push-25,.unit-push-20{position:relative}.unit-push-30{left:30.9%}.unit-push-80{left:82.4%}.unit-push-75{left:77.25%}.unit-push-70{left:72.1%}.unit-push-66{left:68.66666666666666%}.unit-push-60{left:61.800000000000004%}.unit-push-50{left:51.5%}.unit-push-40{left:41.2%}.unit-push-33{left:34.33333333333333%}.unit-push-25{left:25.75%}.unit-push-20{left:20.6%}.unit-push-right{float:right}.centered,.unit-centered{float:none!important;margin:0 auto!important}.unit-padding{padding:1.65em}.units-padding .unit-100,.units-padding .unit-80,.units-padding .unit-75,.units-padding .unit-70,.units-padding .unit-66,.units-padding .unit-60,.units-padding .unit-50,.units-padding .unit-40,.units-padding .unit-33,.units-padding .unit-30,.units-padding .unit-25,.units-padding .unit-20{padding:1.65em}.units-split .unit-80,.units-split .unit-75,.units-split .unit-70,.units-split .unit-66,.units-split .unit-60,.units-split .unit-50,.units-split .unit-40,.units-split .unit-33,.units-split .unit-30,.units-split .unit-25,.units-split .unit-20{margin-left:0}.units-split .unit-80{width:80%}.units-split .unit-75{width:75%}.units-split .unit-70{width:70%}.units-split .unit-66{width:66.6%}.units-split .unit-60{width:60%}.units-split .unit-50{width:50%}.units-split .unit-40{width:40%}.units-split .unit-33{width:33.3%}.units-split .unit-30{width:30%}.units-split .unit-25{width:25%}.units-split .unit-20{width:20%}.blocks-2,.blocks-3,.blocks-4,.blocks-5,.blocks-6{padding-left:0;list-style:none;margin-left:-3%;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.blocks-2:after,.blocks-3:after,.blocks-4:after,.blocks-5:after,.blocks-6:after{content:".";display:block;height:0;clear:both;visibility:hidden}.blocks-2>li,.blocks-3>li,.blocks-4>li,.blocks-5>li,.blocks-6>li{height:auto;float:left;margin-bottom:1.65em;margin-left:3%;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.blocks-2>li{width:47%}.blocks-3>li{width:30.333333333333332%}.blocks-4>li{width:22%}.blocks-5>li{width:17%}.blocks-6>li{width:13.666666666666666%}.block-first{clear:both}@media(min-width:768px){.desktop-hide{display:none}}@media only screen and (max-width:767px){.mobile-text-centered{text-align:center}.mobile-hide{display:none}}img,video{max-width:100%;height:auto}img{-ms-interpolation-mode:bicubic}audio{width:100%}.video-wrapper{height:0;padding-bottom:56.25%;position:relative;margin-bottom:1.65em}.video-wrapper iframe,.video-wrapper object,.video-wrapper embed{position:absolute;top:0;left:0;width:100%;height:100%}@media only screen and (max-width:767px){.units-row .unit-80,.units-row .unit-75,.units-row .unit-70,.units-row .unit-66,.units-row .unit-60,.units-row .unit-50,.units-row .unit-40,.units-row .unit-33,.units-row .unit-30,.units-row .unit-25,.units-row .unit-20,.units-row-end .unit-80,.units-row-end .unit-75,.units-row-end .unit-70,.units-row-end .unit-66,.units-row-end .unit-60,.units-row-end .unit-50,.units-row-end .unit-40,.units-row-end .unit-33,.units-row-end .unit-30,.units-row-end .unit-25,.units-row-end .unit-20{width:100%;float:none;margin-left:0;margin-bottom:1.65em}.unit-push-80,.unit-push-75,.unit-push-70,.unit-push-66,.unit-push-60,.unit-push-50,.unit-push-40,.unit-push-33,.unit-push-30,.unit-push-25,.unit-push-20{left:0}.units-row-end .unit-push-right,.units-row .unit-push-right{float:none}.units-mobile-50 .unit-80,.units-mobile-50 .unit-75,.units-mobile-50 .unit-70,.units-mobile-50 .unit-66,.units-mobile-50 .unit-60,.units-mobile-50 .unit-40,.units-mobile-50 .unit-30,.units-mobile-50 .unit-33,.units-mobile-50 .unit-25,.units-mobile-50 .unit-20{float:left;margin-left:3%;width:48.5%}.units-mobile-50 .unit-80:first-child,.units-mobile-50 .unit-75:first-child,.units-mobile-50 .unit-70:first-child,.units-mobile-50 .unit-66:first-child,.units-mobile-50 .unit-60:first-child,.units-mobile-50 .unit-40:first-child,.units-mobile-50 .unit-30:first-child,.units-mobile-50 .unit-33:first-child,.units-mobile-50 .unit-25:first-child,.units-mobile-50 .unit-20:first-child{margin-left:0}}@media only screen and (max-width:767px){.blocks-2,.blocks-3,.blocks-4,.blocks-5,.blocks-6{margin-left:0;margin-bottom:1.65em}.blocks-2>li,.blocks-3>li,.blocks-4>li,.blocks-5>li,.blocks-6>li{float:none;margin-left:0;width:100%}.blocks-mobile-50>li,.blocks-mobile-33>li{float:left;margin-left:3%}.blocks-mobile-33,.blocks-mobile-50{margin-left:-3%}.blocks-mobile-50>li{width:47%}.blocks-mobile-33>li{width:30.333333333333332%}}@media only screen and (max-width:767px){.nav-h,.nav-h ul,.nav-h ul li,.nav-h,.nav-g,.nav-g ul,.nav-g ul li,.nav-g,.nav-v ul,.nav-v,.nav-tabs ul,.nav-pills,.nav-pills ul{float:none}.nav-h ul li,.nav-g ul li{margin:0;margin-bottom:1px}.nav-tabs ul li{float:none;margin-right:0}.nav-tabs ul li a,.nav-tabs ul li span,.nav-tabs ul li .active{margin-top:0;bottom:0;padding:8px 12px 9px 12px;border:1px solid #ddd;border-bottom:0}.nav-tabs-v{border-bottom:1px solid #ddd;border-right:0}.nav-tabs-v ul li span{margin-top:0;bottom:0;margin-right:0}}@media only screen and (max-width:767px){.forms-columnar label{float:none;text-align:left;width:auto;margin-bottom:0}.forms-push label{position:relative}.forms-push,.forms-columnar .forms-section{padding-left:0}.forms-columnar .forms-list,.forms-columnar .forms-inline-list{margin-left:0}}.tab{display:inline-block;margin:0 0 0 20px;padding:15px 15px 5px 15px;border:1px solid #dedede;cursor:pointer;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px;border-top-left-radius: 5px;}.tabA{background:#dedede;border-bottom:0px solid #dedede;padding-bottom:6px;}.tabB{background:#fff;border-bottom:1px solid #fff;}.tabContent{background-color:#fff;border:1px solid #dedede;margin-top:-24px;padding:15px;display:none;-moz-border-radius-topright: 5px;-webkit-border-top-right-radius: 5px;border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-webkit-border-top-left-radius: 5px;border-top-left-radius: 5px;}.ds_box{background-color:#FFF;border:1px solid rgb(179, 219, 243);position:absolute;z-index:32767;}.ds_tbl{background-color:#FFF;}.ds_head{background-color:rgb(223, 240, 250);color:rgb(18, 77, 114);font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:bold;text-align:center;letter-spacing:2px}.ds_subhead{background-color:rgb(18, 77, 114);color:rgb(223, 240, 250);font-size:12px;font-weight:bold;text-align:center;font-family:Arial,Helvetica,sans-serif;width:32px}.ds_cell{background-color:#EEE;color:#000;font-size:13px;text-align:center;font-family:Arial,Helvetica,sans-serif;padding:2px;cursor:pointer}.ds_cell:hover{background-color:#F3F3F3}#ds_calclass{border:1px solid rgb(179, 219, 243);-webkit-border-radius:25px;-moz-border-radius:25px;border-radius:25px;} #root{color:#a1a1a1;} #root a{text-decoration: none;} #root a img {width:32px;height:32px;}input[type="text"],input[type="password"],input[type="date"],input[type="email"],input[type="url"],input[type="phone"],input[type="tel"],input[type="number"],input[type="datetime"],input[type="datetime-local"],input[type="search"],input[type="range"], select, textarea {-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;}input[type="text"],input[type="password"],input[type="date"],input[type="email"],input[type="url"],input[type="phone"],input[type="tel"],input[type="number"],input[type="datetime"],input[type="datetime-local"],input[type="search"],input[type="range"] {height:25px;border: 1px solid #a1a1a1;}.msgFlash{position:absolute;width:70%;margin-bottom:10px;padding:15px 10px 5px 10px;-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px}.close-right{float:right;margin-top:-35px;cursor:pointer}.closed{display:none}.success-msg{border-color:#51a351#51a351#387038;border-color:rgba(0,0,0,0.1)rgba(0,0,0,0.1)rgba(0,0,0,0.25);background-color:#5bb75b;background-image:-moz-linear-gradient(top,#62c462,#51a351);background-image:-webkit-gradient(linear,0 0,0 100%,from(#62c462),to(#51a351));background-image:-webkit-linear-gradient(top,#62c462,#51a351);background-image:-o-linear-gradient(top,#62c462,#51a351);background-image:linear-gradient(to bottom,#62c462,#51a351);background-repeat:repeat-x;color:#fff;text-shadow:0-1px 0 rgba(0,0,0,0.25);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ff62c462\',endColorstr=\'#ff51a351\',GradientType=0);filter:progid:DXImageTransform.Microsoft.gradient(enabled=false);background-color:#51a351}.error{border-color:#bd362f#bd362f#802420;border-color:rgba(0,0,0,0.1)rgba(0,0,0,0.1)rgba(0,0,0,0.25);background-color:#da4f49;background-image:-moz-linear-gradient(top,#ee5f5b,#bd362f);background-image:-webkit-gradient(linear,0 0,0 100%,from(#ee5f5b),to(#bd362f));background-image:-webkit-linear-gradient(top,#ee5f5b,#bd362f);background-image:-o-linear-gradient(top,#ee5f5b,#bd362f);background-image:linear-gradient(to bottom,#ee5f5b,#bd362f);background-repeat:repeat-x;color:#fff;text-shadow:0-1px 0 rgba(0,0,0,0.25);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ffee5f5b\',endColorstr=\'#ffbd362f\',GradientType=0);filter:progid:DXImageTransform.Microsoft.gradient(enabled=false);background-color:#bd362f}article.unit-100{border: 1px solid #dedede;padding:10px;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}.nav-g ul li form.text-right input {width:170px;height:24px;}.btn-info{color:#fff;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);background-color:#49afcd;background-image:-moz-linear-gradient(top, #5bc0de, #2f96b4);background-image:-webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#2f96b4));background-image:-webkit-linear-gradient(top, #5bc0de, #2f96b4);background-image:-o-linear-gradient(top, #5bc0de, #2f96b4);background-image:linear-gradient(to bottom, #5bc0de, #2f96b4);background-repeat:repeat-x;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ff5bc0de\', endColorstr=\'#ff2f96b4\', GradientType=0);border-color:#2f96b4 #2f96b4 #1f6377;border-color:rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);*background-color:#2f96b4;filter:progid:DXImageTransform.Microsoft.gradient(enabled = false);}.btn-info:hover,.btn-info:focus,.btn-info:active,.btn-info.active,.btn-info.disabled,.btn-info[disabled]{color:#fff;background-color:#2f96b4;*background-color:#2a85a0;}.btn-info:active,.btn-info.active{background-color:#24748c \9;}.btn-info:hover .ok{color:#fff;}.signature{clear:both;margin-top: 40px;border-top: 1px solid #dedede;}.messageTD div fieldset {border-left:3px solid #dedede;}';

		$halflings = '/*! * * Project: GLYPHICONS HALFLINGS * Author: Jan Kovarik - www.glyphicons.com * Twitter: @jankovarik * */html,html .halflings{-webkit-font-smoothing:antialiased!important}@font-face{font-family:\'Glyphicons Halflings\';src:url(\'../fonts/glyphiconshalflings-regular.eot\');src:url(\'../fonts/glyphiconshalflings-regular.eot?#iefix\') format(\'embedded-opentype\'),url(\'../fonts/glyphiconshalflings-regular.woff\') format(\'woff\'),url(\'../fonts/glyphiconshalflings-regular.ttf\') format(\'truetype\'),url(\'../fonts/glyphiconshalflings-regular.svg#glyphicons_halflingsregular\') format(\'svg\');font-weight:normal;font-style:normal}.halflings{font-family:\'Glyphicons Halflings\';font-size:11px/1em;font-style:normal;display:inline-block;line-height:.8em}.halflings.big{position:relative;top:2px}.halflings.glass:before{content:"\e001"}.halflings.music:before{content:"\e002"}.halflings.search:before{content:"\e003"}.halflings.envelope:before{content:"\2709"}.halflings.heart:before{content:"\e005"}.halflings.star:before{content:"\e006"}.halflings.star-empty:before{content:"\e007"}.halflings.user:before{content:"\e008"}.halflings.film:before{content:"\e009"}.halflings.th-large:before{content:"\e010"}.halflings.th:before{content:"\e011"}.halflings.th-list:before{content:"\e012"}.halflings.ok:before{content:"\e013"}.halflings.remove:before{content:"\e014"}.halflings.zoom-in:before{content:"\e015"}.halflings.zoom-out:before{content:"\e016"}.halflings.off:before{content:"\e017"}.halflings.signal:before{content:"\e018"}.halflings.cog:before{content:"\e019"}.halflings.trash:before{content:"\e020"}.halflings.home:before{content:"\e021"}.halflings.file:before{content:"\e022"}.halflings.time:before{content:"\e023"}.halflings.road:before{content:"\e024"}.halflings.download-alt:before{content:"\e025"}.halflings.download:before{content:"\e026"}.halflings.upload:before{content:"\e027"}.halflings.inbox:before{content:"\e028"}.halflings.play-circle:before{content:"\e029"}.halflings.repeat:before{content:"\e030"}.halflings.refresh:before{content:"\e031"}.halflings.list-alt:before{content:"\e032"}.halflings.lock:before{content:"\e033"}.halflings.flag:before{content:"\e034"}.halflings.headphones:before{content:"\e035"}.halflings.volume-off:before{content:"\e036"}.halflings.volume-down:before{content:"\e037"}.halflings.volume-up:before{content:"\e038"}.halflings.qrcode:before{content:"\e039"}.halflings.barcode:before{content:"\e040"}.halflings.tag:before{content:"\e041"}.halflings.tags:before{content:"\e042"}.halflings.book:before{content:"\e043"}.halflings.bookmark:before{content:"\e044"}.halflings.print:before{content:"\e045"}.halflings.camera:before{content:"\e046"}.halflings.font:before{content:"\e047"}.halflings.bold:before{content:"\e048"}.halflings.italic:before{content:"\e049"}.halflings.text-height:before{content:"\e050"}.halflings.text-width:before{content:"\e051"}.halflings.align-left:before{content:"\e052"}.halflings.align-center:before{content:"\e053"}.halflings.align-right:before{content:"\e054"}.halflings.align-justify:before{content:"\e055"}.halflings.list:before{content:"\e056"}.halflings.indent-left:before{content:"\e057"}.halflings.indent-right:before{content:"\e058"}.halflings.facetime-video:before{content:"\e059"}.halflings.picture:before{content:"\e060"}.halflings.pencil:before{content:"\270f"}.halflings.map-marker:before{content:"\e062"}.halflings.adjust:before{content:"\e063"}.halflings.tint:before{content:"\e064"}.halflings.edit:before{content:"\e065"}.halflings.share:before{content:"\e066"}.halflings.check:before{content:"\e067"}.halflings.move:before{content:"\e068"}.halflings.step-backward:before{content:"\e069"}.halflings.fast-backward:before{content:"\e070"}.halflings.backward:before{content:"\e071"}.halflings.play:before{content:"\e072"}.halflings.pause:before{content:"\e073"}.halflings.stop:before{content:"\e074"}.halflings.forward:before{content:"\e075"}.halflings.fast-forward:before{content:"\e076"}.halflings.step-forward:before{content:"\e077"}.halflings.eject:before{content:"\e078"}.halflings.chevron-left:before{content:"\e079"}.halflings.chevron-right:before{content:"\e080"}.halflings.plus-sign:before{content:"\e081"}.halflings.minus-sign:before{content:"\e082"}.halflings.remove-sign:before{content:"\e083"}.halflings.ok-sign:before{content:"\e084"}.halflings.question-sign:before{content:"\e085"}.halflings.info-sign:before{content:"\e086"}.halflings.screenshot:before{content:"\e087"}.halflings.remove-circle:before{content:"\e088"}.halflings.ok-circle:before{content:"\e089"}.halflings.ban-circle:before{content:"\e090"}.halflings.arrow-left:before{content:"\e091"}.halflings.arrow-right:before{content:"\e092"}.halflings.arrow-up:before{content:"\e093"}.halflings.arrow-down:before{content:"\e094"}.halflings.share-alt:before{content:"\e095"}.halflings.resize-full:before{content:"\e096"}.halflings.resize-small:before{content:"\e097"}.halflings.plus:before{content:"\002b"}.halflings.minus:before{content:"\2212"}.halflings.asterisk:before{content:"\002a"}.halflings.exclamation-sign:before{content:"\e101"}.halflings.gift:before{content:"\e102"}.halflings.leaf:before{content:"\e103"}.halflings.fire:before{content:"\e104"}.halflings.eye-open:before{content:"\e105"}.halflings.eye-close:before{content:"\e106"}.halflings.warning-sign:before{content:"\e107"}.halflings.plane:before{content:"\e108"}.halflings.calendar:before{content:"\e109"}.halflings.random:before{content:"\e110"}.halflings.comments:before{content:"\e111"}.halflings.magnet:before{content:"\e113"}.halflings.chevron-up:before{content:"\e113"}.halflings.chevron-down:before{content:"\e114"}.halflings.retweet:before{content:"\e115"}.halflings.shopping-cart:before{content:"\e116"}.halflings.folder-close:before{content:"\e117"}.halflings.folder-open:before{content:"\e118"}.halflings.resize-vertical:before{content:"\e119"}.halflings.resize-horizontal:before{content:"\e120"}.halflings.hdd:before{content:"\e121"}.halflings.bullhorn:before{content:"\e122"}.halflings.bell:before{content:"\e123"}.halflings.certificate:before{content:"\e124"}.halflings.thumbs-up:before{content:"\e125"}.halflings.thumbs-down:before{content:"\e126"}.halflings.hand-right:before{content:"\e127"}.halflings.hand-left:before{content:"\e128"}.halflings.hand-top:before{content:"\e129"}.halflings.hand-down:before{content:"\e130"}.halflings.circle-arrow-right:before{content:"\e131"}.halflings.circle-arrow-left:before{content:"\e132"}.halflings.circle-arrow-top:before{content:"\e133"}.halflings.circle-arrow-down:before{content:"\e134"}.halflings.globe:before{content:"\e135"}.halflings.wrench:before{content:"\e136"}.halflings.tasks:before{content:"\e137"}.halflings.filter:before{content:"\e138"}.halflings.briefcase:before{content:"\e139"}.halflings.fullscreen:before{content:"\e140"}.halflings.dashboard:before{content:"\e141"}.halflings.paperclip:before{content:"\e142"}.halflings.heart-empty:before{content:"\e143"}.halflings.link:before{content:"\e144"}.halflings.phone:before{content:"\e145"}.halflings.pushpin:before{content:"\e146"}.halflings.euro:before{content:"\20ac"}.halflings.usd:before{content:"\e148"}.halflings.gbp:before{content:"\e149"}.halflings.sort:before{content:"\e150"}.halflings.sort-by-alphabet:before{content:"\e151"}.halflings.sort-by-alphabet-alt:before{content:"\e152"}.halflings.sort-by-order:before{content:"\e153"}.halflings.sort-by-order-alt:before{content:"\e154"}.halflings.sort-by-attributes:before{content:"\e155"}.halflings.sort-by-attributes-alt:before{content:"\e156"}.halflings.unchecked:before{content:"\e157"}.halflings.expand:before{content:"\e158"}.halflings.collapse:before{content:"\e159"}.halflings.collapse-top:before{content:"\e160"}';

		
		foreach($this->cVals as $k=>$v) {
			$css_copy = $default;
			for($i=0;$i<count($this->cNames);$i++) {$css_copy=str_replace($this->cNames[$i],'#'.$v[$i],$css_copy);}
			if($h=@fopen('css/style_'.$k.'.css','w')) {fputs($h,$css_copy);fclose($h);}
		}

		if(!file_exists(MU_CSS.'main.css')) {
			$css = $main."\n".$halflings;
			@file_put_contents(MU_CSS.'main.css', $css);
		}
	}

	/**
	*
	* ÉXÉCUTE LA SAUVEGARDE
	*/
	private function do_backup($source, $destination)
	{
	    $msg='';
	    if (is_string($source)) $source_arr = array($source); // convert it to array

	    if (!extension_loaded('zip')) {
	        $this->session->setMsg(ERROR_TYPE_NOT_ZIP_FILE,'error');
		    header('location: index.php');
			exit();
	    }

	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        $this->session->setMsg(ERROR_ZIP_NOT_OPEN,'error');
		    header('location: index.php');
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
	    $msg= ARCHIVE_REC.'  <a class="btn btn-success pull-right" href="'.$destination.'" title="'.DOWNLOAD.'"><i class="icon-download-alt icon-white"></i>&nbsp;'.DOWNLOAD_ARCHIVE.'</a>';
	    $this->session->setMsg($msg);
	    header('location: index.php');
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
	* VÉRIFIE L'ENVOIE D'AVATAR
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
			if(preg_match($match,$_FILES[$name]['name'])) {
				if(($_FILES[$name]['size']<$size) || !$type){
					if (move_uploaded_file($_FILES[$name]['tmp_name'],$dir.DS.str_replace(" ","",$_FILES[$name]['name']))) { 
						$avatar=$dir.DS.str_replace("+","",urlencode($_FILES[$name]['name']));
						if($type) { 
							$old=$this->forum->getMember($login);
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

	// A FAIRE
	// 
	// 	
	// private function _loadCss() {
	// 	if ($this->loaded) { return false; }
	// 	$url = Tools::baseURL();//PARSER L'URL !!!!!!!!!!!!!!!!!!!!!!!!
	// 	$cssFile = RT_ROOT.'css/'.$this->request->action;
		
	// 	if (!is_file($cssFile)) return false;

	// 	# On inclue le contenu et on le formate (suppression espaces, sauts de ligne, commentaires )
	// 	$css = file_get_contents($cssFile);
	// 	$css = str_replace(array('/*'.TM.'*/', "\n"), '', $css);
	// 	$css = $this->_compress($css);
	// 	$css = '/*'.TM.'*/'."\n".$css;

	// 	# On compresse si possible
	// 	if($encoding=Tools::httpEncoding()) {
	// 		header('Content-Encoding: '.$encoding);
	// 		$css = gzencode($css,-1,FORCE_GZIP);
	//     }

	//     # On force la mise en cache et le type du fichier
	// 	header('Content-Type: text/css');
	// 	header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + 31536000 ) . ' GMT');
	// 	header("Cache-Control: public, max-age=31536000");

	// 	echo $css;
	//     $this->loaded = true;
	// }

	// /**
	//  * Script from Kevin Rocher
	//  * http://darklg.me/2010/02/compresser-ranger-css-php/
	//  */
	// private function _compress($buffer) {
	// 	// A décommenter pour utiliser des variables dans le css
	// 	// Les variables seront du type : {MAVARIABLE}
	// 			// $variables_css = array(
	// 			//		'COULEURLIENS'=>'#ff410e'
	// 			// );
	// 			// // On remplace les variables par leur valeur
	// 			// foreach($variables_css as $code_variable => $valeur)
	// 			// 	$buffer = str_replace('{'.$code_variable.'}', $valeur, $buffer);
		 
	// 		// Suppression des commentaires
	// 		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		 
	// 		// Suppression des tabulations, espaces multiples, retours à la ligne, etc.
	// 		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $buffer);
		 
	// 	// Suppression des derniers espaces inutiles
	// 		$buffer = str_replace(array(' { ',' {','{ '), '{', $buffer);
	// 		$buffer = str_replace(array(' } ',' }','} '), '}', $buffer);
	// 		$buffer = str_replace(array(' : ',' :',': '), ':', $buffer);
		 
	// 		return $buffer;
	// }
}
/**
 * Mise en forme du forum
 */
class Template extends Init
{	
	public function __construct(){
		parent::__construct();
	}
	/**
	*
	* TEXTE D'ACCUEIL
	*/
	public function welcomeText() {
		$buf='<!-- Welcome text -->';
		$buf.='<h4 class="forms-section">'.INFORMATION.'</h4>
	          <div class="lead">';
		if(!$wtp=@file_get_contents('welcome.txt')) {
			$buf.= WELCOME_TXT;
		} else {
			$buf .= BBCHelper::decode($wtp).'</div>';
		}
		return $buf;
	}
	/**
	 * PAGINATION
	 */
	public function pagination($perPage, $page, $pages) {
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
	*
	* FORMULAIRE D'INSCRIPTION
	*/
	public function registerForm() {

		$form ='';
		$form .= '<h4 class="forms-section">'.JOIN_COMMUNITY.'</h4>
		<form action="index.php" method="post" enctype="multipart/form-data" autocomplete="off" class="form forms forms-columnar">';
		$form .= '<input type="hidden" name="action" value="newuser" />
		<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->maxAvatarSize.'" />
		' .
		     //input($label, $name, $value, $type, $placeholder, $maxlength, $readonly, $class, $icon, $require,onclick)
			'<p class="forms-inline">' .Tools::input(USER_LOGIN, 'login', '', 'text', '', '20', '', 'width-30 input-success', 'halflings user', 'success').'</p>
			<p class="forms-inline">' .Tools::input(PASSWORD, 'password', '', 'password', '', '50', '', 'width-30 input-success', 'halflings lock', 'success').'</p>
			<p class="forms-inline">' .Tools::input(BIRTHDAY, 'birthday', '', 'date', 'Jour/Mois/Année', '10', true, 'width-20 input-success', 'halflings calendar', 'success','ds_sh(this)','ds_sh(this)').'</p>
			<p class="forms-inline">' .Tools::input(EMAIL, 'email', '', 'email', '', '50', '', 'width-30 input-success', 'halflings envelope', 'success').'</p>
			<p class="forms-inline">' .Tools::input(WEBSITE, 'site', '', 'url', 'http://', '255', '', 'width-30', 'halflings globe').'</p>
			<p class="forms-inline"><i class="halflings link"></i>&nbsp;&nbsp;&nbsp;&nbsp;' .Tools::textarea(SIGNATURE, 'signature', '', '10', '2', SIGNATURE_MSG, '150', '', 'width-70'). '</p>
		<p>
		<label for="avatar">'.AVATAR.' <span class="label label-red">&lt; '.($this->maxAvatarSize/1024).'ko</span></label>
		<i class="halflings picture"></i>&nbsp;&nbsp;&nbsp;
		<input type="file" id="avatar" name="avatar">
		</p>';
		$form .= '<p><label for="qid">'.CAPTCHA.'</label>'.$this->captcha->template().'</p>';
		$form .= '<p><button type="submit" class="btn btn-green"><i class="halflings hand-right"></i> '.SIGN_UP.'</button></p>
		<div class="message message-info"><i class="halflings exclamation-sign"></i> '.MENDATORY_FIELDS.'
		'.CHAR_NOT_ALLOWED.'
		<pre>/ \ &amp; " \' . ! ? :</pre> '.CHAR_NOT_ALLOWED_BIS.'
		</div>
		</form>';

		return $form;
	}
	/**
	*
	* ÉDITION DU PROFIL
	*/
	public function editProfilForm() {

		$mb=$this->forum->getMember($this->cLogin);
		$avatar=($mb->pic!='')?'<figure><img src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="'.AVATAR.'"/></figure>':'<figure>'.Tools::img('avatar','img-polaroid').'</figure>';	
		
		$form = '<!-- Edit profil form -->';
	    $form .= '<h4 class="forms-section">'.EDIT_PROFIL.' ~ '.$this->cLogin.'</h4>';
		$form .= '<div class="units-container">
			<ul class="blocks-2">
			<li>'.$avatar.'</li>
			<li>'.$this->listFiles().'</li>
			</ul>
			<hr />
			<div class="units-row well">
			<form action="index.php" method="post" enctype="multipart/form-data" class=" forms forms-columnar">
			<input type="hidden" name="action" value="editprofil" />
			<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->maxAvatarSize.'" />

	  ' .//input($label,$name,$value,$type,$placeholder,$maxlength,$readonly,$class,$icon,$require,$onclick)

		'<p class="forms-inline">'.Tools::input(BIRTHDAY, 'birthday', $mb->birthday, 'text', 'Jour/Mois/Année', '10', true, '', 'halflings calendar', true, 'ds_sh(this);').'</p>
		<p class="forms-inline">'.Tools::input(EMAIL, 'email', $mb->mail, 'email', '', '50', '', '', 'halflings envelope').'</p>
		<p class="forms-inline">'.Tools::input(WEBSITE, 'site', $mb->url, 'url', 'http://', '255', '', 'input-xlarge', 'halflings globe').'</p>
		<p class="forms-inline">'.Tools::textarea(SIGNATURE, 'signature', $mb->quote, '10', '2', SIGNATURE_MSG, '150', '', 'width-70').'</p> 
		<p>
		<label for="avatar">'.AVATAR.' <span class="label label-red">&lt; '.($this->maxAvatarSize/1024).'ko</span></label>
			<input type="file" id="avatar" name="avatar">
		</p>
		<p>
			<button type="submit" class="btn btn-green"><i class="halflings hand-right"></i> '.SAVE_PROFIL.'</button>
		</p>
		</form>
		</div><!-- well -->';

			return $form;
	}
	/**
	*
	* LISTE LES FICHIERS ENVOYÉS DANS LE PROFIL DE L'UTILISATEUR
	*/
	public function listFiles() {
			$dir=MU_UPLOAD.md5(SECURITY_SALT.$this->cLogin).'/';
			$a=$this->forum->getMember($this->cLogin);
			$list='<div class="files">';
			$list.='<b>'.MY_PERSONAL_FILES.'</b><br /><hr />';
			$h=dir($dir);
			$id=1;
			while (false !== ($f=$h->read())) {
				if (($f!='.') && ($f!='..') && ($f!=$this->cLogin.'.mp') && $f != 'index.html') {
					$cl=($a->pic!=($dir.urlencode($f)))?"'Lien'":"poster";
					$ext = strtolower(substr($f,-3));
					if (in_array($ext,array('png','jpg','jpeg','bmp','ico','gif'))) {
						$imgbinary = fread(fopen($dir.'/'.urlencode($f), "r"), filesize($dir.'/'.urlencode($f)));
						$list.=$id. ' | <img class="'.$cl.'" src="data:image/'.$ext.';base64,'.base64_encode($imgbinary).'" title="'.$f.'" style="max-width:80px"/><br />';
					} else {
						$list.=$id. ' | <a class="'.$cl.'" href="'.MU_URL_MEMBER.md5(SECURITY_SALT.$this->cLogin).'/'.urlencode($f).'" title="'.FILE.'">'.$f.'</a><br />';
					}
					$id++;
				}
			}
			$h->close();
			$list .= '</div>';
			return $list;
	}
	/**
	*
	* AFFICHAGE FIL D'ARIANE (Breadcrumbs)
	*/
	public function breadcrumbs() {
		$url = Tools::getURLParams();
		$url .= ($url)? '&':'?';
		$mn='';	
		$mn .='<div class="image-right">';	
		foreach($this->cVals as $k=>$v) $mn .= '<span onclick="window.location=\''.$url.'style='.$k.'\'" title="'.$k.'" style="display: inline-block; border-radius: 3px; width: 16px; height: 16px; line-height: 18px; background-color: #'.$v[1].'; cursor: pointer;">&nbsp;&nbsp;</span> ';	
		$mn .= '</div>';
		$tLogin=$this->isMember?$this->cLogin:GUEST;
		$mn .= '<ul class="breadcrumbs"><li><i class="halflings play-circle"></i>&nbsp;'.WELCOME.' <span class="';	
		$mn .= ($this->isAdmin)?'text-error':'text-info';
		$mn .= '"><strong>'.$tLogin.'</strong></span>';	
		if($this->haveMP) $mn .= ' <a  href="javascript:switchLayer(\'privatebox\');" rel="tooltip" title="'.NEW_PRIVATE_MSG.'" role="button" class="blink" data-toggle="modal"> <i class="halflings inbox"></i></a><script>function blink(selector){$(selector).fadeOut("slow", function(){$(this).fadeIn("slow", function(){blink(this);});});}blink(".blink");</script>';
		$mn .= 	'</li>';
		$mn .= '<li><a href="'.MU_BASE_URL.'"><i class="halflings home"></i>&nbsp;'.HOME.'</a></li>';
		if($this->haveMP) $mn .= '<li><i class="halflings envelope"></i>&nbsp;'.PRIVATE_INBOX.'</li>';
		if($this->get_editpost) $mn .= '<li><i class="halflings pencil"></i>&nbsp;'.EDIT.'</li>';
		else if($this->get_conf) $mn .= '<li><i class="halflings cog"></i>&nbsp;'.CONFIG_OPTIONS.'</li>';
		else if($this->get_topic) $mn .= '<li><i class="halflings comments"></i>&nbsp;'.$this->forum->getPostsTitle($this->get_topic).'</li>';
		else if($this->get_memberlist) $mn .= '<li><i class="halflings user"></i>&nbsp;'.MEMBERS.'</li>';
		else if($this->searchMember) $mn .= '<li><i class="halflings user"></i>&nbsp;'.RESULT_FOR.$this->searchMember.'</li>';
		else if($this->get_editprofil) $mn .= '<li><i class="halflings eye-open"></i>&nbsp;'.EDIT_PROFIL.'</li>';
		else if($this->get_private) $mn .= '<li><i class="halflings leaf"></i>&nbsp;'.PRIVATE_MSG.'</li>';
		else if($this->get_restore) $mn .= '<li><i class="halflings refresh"></i>&nbsp;'.RESTORE.'</li>';
		
		$mn .= '
		       </ul>';
		return $mn;
	}
	/**
	*
	* AFFICHAGE DU MENU
	*/
	public function menu() {
		$mn='';
		$stats=$this->forum->getStat();
	//	if($this->nbrMsgIndex<$stats['topics']) $mn .='<li><a href="?showall=1" title="'.LIST_OF_ALL_TOPICS.'"><i class="halflings bookmark"></i> '.ARCHIVES.'</a></li>';	
		if($this->isMember) {
			$mn .='<li><a href="?logout=1" title="'.QUIT.'"><i class="halflings off"></i> '.LOGOUT.'</a></li>';
			$mn .='<li><a href="?editprofil=1" title="'.EDIT_MY_PROFIL.'"><i class="halflings eye-open"></i> '.PROFIL.'</a></li>';
			$mn .='<li><a href="?memberlist=1" title="'.LIST_OF_MEMBERS.'"><i class="halflings user"></i> '.MEMBERS.'</a></li>';
			if(!$this->isAdmin && !$this->isOwner) {
			$mn .='<li><a href="index.php" title="'.HOME.'"><i class="halflings home"></i> '.FORUMS.'</a></li>';
			}
		} else {
			$mn .='<li>
			<form action="index.php" method="post" autocomplete="off" class="text-right">
			<input type="hidden" name="action" value="enter" />
			<input type="text" name="login" placeholder="'.USER.'">
			<input type="password" name="password" placeholder="'.PASSWORD.'">
			<button type="submit" class="btn btn-info"><i class="halflings ok"></i> '.CONNECT.'</button>
			</form></li>';	
		}	
		return $mn;
	}
	/**
	*
	* NAVIGATION (Admin seulement!)
	*/
	public function menu_admin() {
		$mn='';
		if($this->isAdmin && $this->isOwner) {
			$mn .= '<li><a href="?conf=1" title="'.GENERAL_PARAM.'"><i class="halflings wrench"></i> '.CONFIG.'</a></li>';
			$mn .= '<li><a href="?backup=1" title="'.SAVE_BACKUP.'"><i class="halflings hdd"></i> '.SAVE.'</a></li>';
			$mn .= '<li><a href="?restore=1" title="'.RESTORE_FROM_BACKUP.'"><i class="halflings refresh"></i> '.RESTORE.'</a></li>';
			$mn .='<li><a href="index.php" title="'.HOME.'"><i class="halflings home"></i> '.FORUMS.'</a></li>';
		}
		return $mn;
	}
	/**
	*
	* AFFICHAGE DE LA LISTE DES SUJETS (Forum home)
	*/
	public function showTopics() {
		$pagination = '<p>'.$this->pagination($this->nbrMsgIndex, $this->page, $this->pages).'</p>';
		$buffer = '';
		$buffer .= $pagination;
		$buffer .= '<table class="table-bordered table-striped">
		<tr class="info">
		<td style="width:60%;">'.TITLE_SUBJECT.'</td>
		<td style="width:5%; text-align:center;">'.MESSAGES.'</td>
		<td style="width:30%;">'.LAST_MSG.'</td>';
		if($this->isAdmin) $buffer .= '<td style="width:5%">'.ADMIN.'</td>';
		$buffer .= '</tr>';

		$topicList=$this->forum->getallTopic(false,$this->nbrMsgIndex,$this->page);
 		foreach($topicList as $t) {
			$t['dernierLe'] = date('d M Y à H:i',$t['dernierLe']);
			$started = date('d M Y', $t['topicID']);
			$t['attachment']=($t['attachment']!='')?'<i class="halflings file"></i> ':'';
			$t['postType']=$t['postType']?'<i class="halflings star"></i> ':'';
			$statusIcon = (isset($_COOKIE["uFread".$t['topicID'].""]))?'<i class="halflings folder-open"></i>':'<i class="halflings fire"></i>';
			$buffer .= '<tr>';
			$buffer .= '<td>'.$t['postType'].$t['attachment'].$statusIcon.' <a href="?topic='.$t['topicID'].'" title="'.DISPLAY_TOPIC.'">'.stripslashes($t['titre']).'</a><br /><span class="image-right">'.STARTED_ON.' '.$started.', '.BY.' ';
			$buffer .= $this->forum->isMember($t['auteur'])?'<a class="Lien" href="index.php?private='.$t['auteur'].'" title="'.SEND_PRIVATE_MSG.'">'.$t['auteur'].'</a></span></td>':$t['auteur'].'</span></td>';
			$buffer .= '<td class="mess">'.$t['nombrePosts'].'</td>';
			$buffer .= '<td><i>'.L_ON.' :</i> <a href="?topic='.$t['topicID'].'#bottom" class="Lien" title="'.GOTO_LAST_MSG.'">'.$t['dernierLe'].'</a><br /><i>'.BY.':</i> ';
			$buffer .= $this->forum->isMember($t['dernierPar'])?'<a class="Lien" href="index.php?private='.$t['dernierPar'].'" title="'.SEND_PRIVATE_MSG.'">'.$t['dernierPar'].'</a></td>':$t['dernierPar'].'</td>';
			if($this->isAdmin) $buffer .= '<td><a href="?topic='.$t['topicID'].'&amp;delpost='.$t['topicID'].'" onclick="return confirmLink(this,\''.$t['titre'].'\');" rel="tooltip" title="'.DEL_MSG.'"><i class="halflings trash"></i></a></td>'."\n";
			$buffer .= '</tr>';
		}
		$buffer .= '</table>';
		$buffer .= $pagination;
		$buffer .= $this->replyForm('newtopic',count($topicList));	
		return $buffer;
	}
	/**
	*
	* AFFICHAGE DE LA DISCUSSION
	*/
	public function showPosts() {
		
		$buffer='';
		$avatars=array();
		$quotes=array();
		$modo=array();
		if($this->topicObj = $this->forum->getPosts($this->get_topic,false,$this->nbMsgTopic,$this->page)){
		$pagination = '<p>'.$this->pagination($this->nbMsgTopic, $this->page, $this->topicObj->nbPosts).'</p>';
		$buffer .= $pagination;
			list($time,$titre,$auteur,$posts,$last,$lasttime,$attach,$type)=$this->topicObj->getInfo(0);
			$buffer .= '<div class="gradient">';
			if($this->isAdmin) {
				$buffer .= '<form action="index.php?topic='.$this->get_topic.'" name="sub" method="post" class="forms-inline"><input type="hidden" name="topicID" value="'.$this->get_topic.'" />';
				$buffer .= '<i class="halflings star" style="color:#111;"></i> <input style="border:none;" type="checkbox" onclick="window.location=\'?topic='.$this->get_topic.'&postit='.($type?'off':'on').'\'"';/*** On épingle le sujet ou pas ***/
				$buffer .= $type?' checked="checked" /> ':'/> ';
				$buffer .= '<input type="text" value="'.stripslashes($titre).'" size="40" name="ntitle" /> <button type="submit" class="btn btn-blue"><i class="halflings pencil"></i>&nbsp;'.EDIT_TITLE.'</button></form>';/*** Modification du Titre du sujet ***/
			} else $buffer .= stripslashes($titre);
			$buffer .= '</div>';
			// tooltips
			list($num,$auths)=$this->topicObj->getInfo(1);
			foreach($auths as $m) {
				if($this->forum->isMember($m)) {
					$mb=$this->forum->getMember($m);
					$buffer .= '<div class="tooltip" id="'.Tools::cleanUser($m).'">';
					$buffer .= '<table style="width: 100%">';
					$buffer .= '<tr><th class="formTD">'.PROFIL_OF.' <b>'.$m.'</b></th></tr>';
					$buffer .= '<tr><td class="formTD">'.REGISTRED_ON.'</td><td class="tooltipTD">'.date('d M Y à H:i',$mb->time).'</td></tr>';
					$buffer .= '<tr><td class="formTD">'.EMAIL.'</td><td class="tooltipTD">'.Tools::protect_email($mb->mail).'</td></tr>';
					if(!empty($mb->url)) $buffer .= '<tr><td class="formTD">'.WEBSITE.'</td><td class="tooltipTD">'.$mb->url.'</td></tr>';
					if(!empty($mb->birthday)) $buffer .= '<tr><td class="formTD">'.BORN_ON.'</td><td class="tooltipTD">'.$mb->birthday.' <span class="badge">'.MuDate::birthday($mb->birthday).' '.YEARS_OLD.'</span></td></tr>';
					if(!empty($mb->quote)) {
						$buffer .= '<tr><td class="formTD">'.SIGNATURE.'</td><td class="tooltipTD">'.$mb->quote.'</td></tr>';
						if($this->quoteMode) $quotes[$m]=$mb->quote;
					}
					$buffer .= '</table></div>';
					if($mb->mod) $modo[$m]=($mb->mod>1)?'<span class="label label-red">'.FOUNDER.'</span>':'<span class="label label-green">'.MODERATOR.'</span>';
					else $modo[$m]='<span class="label label-blue">'.MEMBER.'</span>';
				} else $mb->pic='';
				// Avatar
				$shortpic = str_replace(MU_UPLOAD,'',$mb->pic);
				$avatars[$m]=($mb->pic!='')?'<img class="avatar" src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="avatar"/>':Tools::img('avatar','avatar');
			}
			$cnt=0;
			while($reply=$this->topicObj->nextReply()) {
				$mb=$this->forum->getMember($reply->auth);
				$buffer .= '<table class="width-100 table-bordered"><tr>';
				if($this->forum->isMember($reply->auth)) {
					$buffer .= '<td class="avatarTD" rowspan="2"><a onmouseover="showWMTT(\''.$reply->auth.'\')" onmouseout="hideWMTT()" href="?private='.$reply->auth.'" title="">'.$avatars[$reply->auth].'</a>';
					$buffer .= '<div class="datePost"><a class="LienNonLu" href="?private='.$reply->auth.'" title="'.SEND_PRIVATE_MSG.'">'.$reply->auth.'</a></div>';
					$buffer .= '<div>'.$modo[$reply->auth].'</div>';
				} else {
					$buffer .= '<td class="avatarTD" rowspan="2">'.$avatars[$reply->auth];
					$buffer .= '<div class="datePost">'.$reply->auth.'</div>';
				}
				if(!empty($mb->url)) $buffer .= '<div class="datePost"><i class="halflings share"></i>&nbsp;<a href="'.$mb->url.'" onclick="window.open(this.href);return false;" title="'.$mb->url.'">'.WEBSITE.'</a></div>';
				$buffer .= '<div class="datePost">'.MESSAGE.': '.$mb->post.'</div>';
				$buffer .= '<td><div class="datePost">'.date('d/m/Y H:i', $reply->time).'</div></td></tr>';
				$buffer .= '<tr><td class="messageTD"><div id="td'.$cnt.'">'.BBCHelper::decode($reply->content).'</div>';
				if(isset($quotes[$reply->auth])) $buffer .= '<div class="signature"><blockquote><p>'.$quotes[$reply->auth].'</p></blockquote></div>';
				$buffer .= "</td></tr><tr><td style='text-align: center'><a href='".$_SERVER['REQUEST_URI']."#bottom' class='btn btn-small btn-orange' onclick='quote(\"".$reply->auth."\",$cnt)' title='".QUOTE_MSG_FROM." ".$reply->auth."' /><i class='halflings comments'></i> ".QUOTE."</a></td><td>";
				if($this->isAdmin) {
					$delmsg = $cnt?ANSWER_FROM.' '.$reply->auth:' '.WHOLE_TOPIC;
					$buffer .= '<a class="btn btn-small" href="?topic='.$this->get_topic.'&amp;editpost='.$reply->time.'" title="'.EDIT.'"><i class="halflings pencil"></i> '.EDIT.'</a>&nbsp;<a class="btn btn-small btn-red" href="?topic='.$this->get_topic.'&amp;delpost='.$reply->time.'" title="'.DEL.'" onclick="return confirmLink(this,\''.$delmsg.'\')"><i class="halflings trash"></i> '.DEL.'</a>&nbsp;<a class="btn btn-small" href="javascript:switchLayer(\'form\');" title="'.ANSWER.'"><i class="halflings share-alt"></i> '.ANSWER.'</a>'."\n";
				}	
				if(!empty($reply->attach)){
					$attachment = explode('/', $reply->attach);
					$buffer .= '<a class="image-right" href="?pid='.base64_encode($reply->attach).'" title="'.DOWNLOAD.'">'.$attachment[3].' '.Tools::img('clip').'</a>';
				}	
				$buffer .= '</td></tr>';
				$buffer .= '</table>';
				$cnt++;
			}
			$buffer .= $pagination;
			$buffer .= $this->replyForm('newpost');	

		} else {
			$buffer .= '<div class="message">
			<span class="close"></span>
			<strong>'.TOPIC_UNKNONW.'</strong>
			</div>';
		}
		return $buffer;
	}
	/**
	*
	* LISTE DES MEMBRES
	*/
	public function showMemberlist() {
		$pagination = '<p>'.$this->pagination($this->nbrMb, $this->page, $this->pagesMb).'</p>';
		$form = '<form action="index.php?searchMember" method="post" autocomplete="off" class="text-right">
			<input type="text" name="searchMember" placeholder="'.SEARCH.'">
			<button type="submit" class="btn btn-info"><i class="halflings search"></i></button>
			</form>';
		$annu = '';
		$annu .= $pagination;
		$toolTip = '';
		$wd=$this->isAdmin?45:60;
		$annu .= $form;
		$annu .= '<table class="width-100 table-striped"><thead>
		<tr class="colorGrayDark">
		<td style="width:15%;">'.MEMBER.'</td>
		<td style="width:'.$wd.'%;">'.SIGNATURE.'</td>
		<td style="width:13%;">'.BIRTH.'</td>
		<td style="width:12%;">'.EMAIL.'</td>';
		if($this->isAdmin) $annu .= '<td colspan="2" style="width:15%;">'.ADMIN.'</td>';
			$annu .= '</tr></thead>';
			$mb=$this->forum->listMember($this->nbrMb,$this->page,false);
		foreach($mb as $m) {
			$mb=$this->forum->getMember($m);
			$mb->mail= Tools::protect_email($mb->mail);
			$signature=($mb->quote!="")?BBCHelper::tronquer_texte($mb->quote, 50):"&nbsp;";
			if($mb->url!='') {
				if (!preg_match('|http://|',$mb->url)) $mb->url='http://'.$mb->url;
				$mb->url='<a href="'.$mb->url.'" title="'.$mb->url.'">'.Tools::img('window').'</a>';
			}
			if($mb->birthday!='') {
				$mb->birthday = str_replace(' ', '', $mb->birthday);
				$mb->birthday = preg_replace('/([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+(.*)/i', '\\1 \\2 \\3 \\4 \\5', $mb->birthday);
			} else $mb->birthday = '&nbsp;';
			$avatar=($mb->pic != '')?'<img style="width:80px; height:80px;" src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="Avatar" />':Tools::img('avatar','img-circle');
			$toolTip .= '<div class="tooltip" id="'.$m.'">
			<p class="image-right"><span class="thumbnail">'.$avatar.'</span></p>
			<h4>'.MINI_PROFIL_OF.' <b>'.$m.'</b></h4>
			<p><b>'.REGISTRED_ON.' : </b> '.date('d M Y à H:i',$mb->time).'<br />';
			if(!empty($mb->mail)) $toolTip .= '<b>'.EMAIL.' : </b> '.$mb->mail.'<br />';
			if(!empty($mb->birthday)) $toolTip .= '<b>'.BORN_ON.' : </b> '.$mb->birthday.' <span class="label label-black">'.MuDate::birthday($mb->birthday).' '.YEARS_OLD.'</span><br />';
			if(!empty($mb->quote)) {
				$toolTip .= '<b>'.SIGNATURE.' : </b> <blockquote><p class="color-blue">'.$mb->quote.'</p></blockquote><br />';
			}
			    $toolTip .= '</p></div>';

			$annu .= '<tr>';
			$annu .= ($m != $this->cLogin) ? '<td><a class="Lien" href="?private='.$m.'" onmouseover="showWMTT(\''.$m.'\')" onmouseout="hideWMTT()" title="'.SEND_PRIVATE_MSG.'">'.$m.'</a></td>'."\n" : '<td><a class="Lien" href="#" onmouseover="showWMTT(\''.$m.'\')" onmouseout="hideWMTT()" onclick="return false;">'.$m."</td>\n";
			$annu .= '<td>'.$signature.'</td>';
			$annu .= '<td>'.$mb->birthday.'</td>';
			$annu .= '<td>'.$mb->mail.' '.$mb->url.'</td>';
			if($this->isAdmin) {
				if($mb->mod) {
					if($m==$this->cLogin || $mb->mod==2) {
						$str=($mb->mod>1)? ADMIN:MODO;
						$annu .= '<td>&nbsp;</td>';
						$annu .= "<td><form><input style='border:none;' type='checkbox' checked='checked' onclick=\"this.checked='checked'\"/>$str!</form></td>\n";
					} else {
						$annu .= "<td><a class='Lien' href='?memberlist=1&deluser=".$m."' onclick='return confirmLink(this,\"$m\")' title='".DEL_MEMBER."'><i class='halflings trash'></i></a></td>\n";
						$annu .= "<td><form><input style='border:none;' type='checkbox' checked='checked' onclick=\"window.location='?memberlist=1&switchuser=".$m."';\" /> ".MODO."?</form></td>\n";
					}
				} else {
					$annu .= "<td><a href='?memberlist=1&deluser=".$m."' onclick='return confirmLink(this,\"$m\")' title='".DEL_THIS_USER."'><i class='halflings trash'></i></a></td>\n";
					$annu .= "<td><form><input style='border:none;' type='checkbox' onclick=\"window.location='?memberlist=1&switchuser=".$m."';\"/> ".MODO."?</form></td>\n";
				}
			}
			$annu .= '</tr>';
		}
		$annu .= '</table>';
		$annu .= $toolTip;
		$annu .= $pagination;
		return $annu;
	}/**
	*
	* RECHERCHE D'UN MEMBRE
	*/
	public function searchMember() {
		$annu = '';
		$toolTip = '';
		$wd=$this->isAdmin?45:60;
		$annu .= '<table class="width-100 table-striped"><thead>
		<tr class="colorGrayDark">
			<td style="width:15%;">'.MEMBER.'</td>
			<td style="width:'.$wd.'%;">'.SIGNATURE.'</td>
			<td style="width:13%;">'.BIRTH.'</td>
			<td style="width:12%;">'.EMAIL.'</td>';
		if($this->isAdmin) $annu .= '<td colspan="2" style="width:15%;">'.ADMIN.'</td>';
			$annu .= '</tr></thead>';
			$mb=$this->forum->listMember();
			// Calcul de la distance Levenshtein entre le mot recherché
			// avec les pseudos enregistrés
			// Aucune distance de trouvée pour le moment
			$shortest = -1;
			// Boucle sur les des mots pour trouver le plus près
			foreach ($mb as $membre) {
			    // Calcule la distance avec le mot mis en entrée,
			    // et le mot courant
			    $lev = levenshtein($this->searchMember, $membre);
			    // Cherche une correspondance exacte
			    if ($lev == 0) {
			        // Le mot le plus près est celui-ci (correspondance exacte)
			        $closest = $membre;
			        $shortest = 0;
			        // On sort de la boucle ; nous avons trouvé une correspondance exacte
			        break;
			    }
			    // Si la distance est plus petite que la prochaine distance trouvée
			    // OU, si le prochain mot le plus près n'a pas encore été trouvé
			    if ($lev <= $shortest || $shortest < 0) {
			        // Définition du mot le plus près ainsi que la distance
			        $closest  = $membre;
			        $shortest = $lev;
			    }
			}
		//if (in_array($this->searchMember, $mb)) {
			if ($shortest == 0) {
			$mb=$this->forum->getMember($this->searchMember);
			$mb->mail= Tools::protect_email($mb->mail);
			$signature=($mb->quote!="")?BBCHelper::tronquer_texte($mb->quote, 50):"&nbsp;";
			if($mb->url!='') {
				if (!preg_match('|http://|',$mb->url)) $mb->url='http://'.$mb->url;
				$mb->url='<a href="'.$mb->url.'" title="'.$mb->url.'">'.Tools::img('window').'</a>';
			}
			if($mb->birthday!='') {
				$mb->birthday = str_replace(' ', '', $mb->birthday);
				$mb->birthday = preg_replace('/([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+([0-9]{2})+(.*)/i', '\\1 \\2 \\3 \\4 \\5', $mb->birthday);
			} else $mb->birthday = '&nbsp;';
			$avatar=($mb->pic != '')?'<img style="width:80px; height:80px;" src="'.Tools::base64_encode_image($mb->pic,$mb->extension).'" alt="Avatar" />':Tools::img('avatar','img-circle');
			$toolTip .= '<div class="tooltip" id="'.$this->searchMember.'">
			<p class="image-right"><span class="thumbnail">'.$avatar.'</span></p>
			<h4>'.MINI_PROFIL_OF.' <b>'.$this->searchMember.'</b></h4>
			<p><b>'.REGISTRED_ON.' : </b> '.date('d M Y à H:i',$mb->time).'<br />';
			if(!empty($mb->mail)) $toolTip .= '<b>'.EMAIL.' : </b> '.$mb->mail.'<br />';
			if(!empty($mb->birthday)) $toolTip .= '<b>'.BORN_ON.' : </b> '.$mb->birthday.' <span class="label label-black">'.MuDate::birthday($mb->birthday).' '.YEARS_OLD.'</span><br />';
			if(!empty($mb->quote)) {
				$toolTip .= '<b>'.SIGNATURE.' : </b> <blockquote><p class="color-blue">'.$mb->quote.'</p></blockquote><br />';
			}
			    $toolTip .= '</p></div>';

			$annu .= '<tr>';
			$annu .= ($this->searchMember != $this->cLogin) ? '<td><a class="Lien" href="?private='.$this->searchMember.'" onmouseover="showWMTT(\''.$this->searchMember.'\')" onmouseout="hideWMTT()" title="'.SEND_PRIVATE_MSG.'">'.$this->searchMember.'</a></td>'."\n" : '<td><a class="Lien" href="#" onmouseover="showWMTT(\''.$this->searchMember.'\')" onmouseout="hideWMTT()" onclick="return false;">'.$this->searchMember."</td>\n";
			$annu .= '<td>'.$signature.'</td>';
			$annu .= '<td>'.$mb->birthday.'</td>';
			$annu .= '<td>'.$mb->mail.' '.$mb->url.'</td>';
			if($this->isAdmin) {
				if($mb->mod) {
					if($m==$this->cLogin || $mb->mod==2) {
						$str=($mb->mod>1)? ADMIN:MODO;
						$annu .= '<td>&nbsp;</td>';
						$annu .= "<td><form><input style='border:none;' type='checkbox' checked='checked' onclick=\"this.checked='checked'\"/>$str!</form></td>\n";
					} else {
						$annu .= "<td><a class='Lien' href='?memberlist=1&deluser=".$this->searchMember."' onclick='return confirmLink(this,\"$this->searchMember\")' title='".DEL_MEMBER."'><i class='halflings trash'></i></a></td>\n";
						$annu .= "<td><form><input style='border:none;' type='checkbox' checked='checked' onclick=\"window.location='?memberlist=1&switchuser=".$this->searchMember."';\" /> ".MODO."?</form></td>\n";
					}
				} else {
					$annu .= "<td><a href='?memberlist=1&deluser=".$this->searchMember."' onclick='return confirmLink(this,\"$this->searchMember\")' title='".DEL_THIS_USER."'><i class='halflings trash'></i></a></td>\n";
					$annu .= "<td><form><input style='border:none;' type='checkbox' onclick=\"window.location='?memberlist=1&switchuser=".$this->searchMember."';\"/> ".MODO."?</form></td>\n";
				}
			}
			$annu .= '</tr>';
		} else {
			$annu .= '<tr><td colspan="'.(($this->isAdmin) ? '6' : '4').'">'.NO_RESULT.'.&nbsp;'.DID_YOU_MEAN.'<a href="'.MU_BASE_URL.'?searchMember='.$closest.'">'.$closest.'</a> ?</td></tr>';
		}
		$annu .= '</table>';
		$annu .= $toolTip;
		return $annu;
	}
	/**
	*
	* AFFICHE LE MODAL DES MESSAGES PRIVÉ
	*/
	public function showPrivateMsg() {
		$s=implode('', file(MU_MEMBER.md5($this->cLogin.SECURITY_SALT).'/'.$this->cLogin.'.mp'));
		$mp = unserialize($s);
		$mess=$mp->getMessage();
		$pvtBox = '<a name="private" id="private" title="'.PRIVATE_MSG.'"></a>
		<div class="Box">';
		$pvtBox .= "<a class='toggleLink' href=\"javascript:switchLayer('privatebox');\" title='message privé'>".PRIVATE_INBOX."</a>\n";
		$pvtBox .= "<div class='toggle' id='privatebox'>\n";
		foreach($mess as $m) {
			if($this->forum->isMember($m->from)) $pvtBox .= '<a class="Lien" href="?private='.$m->from.'" title="'.PRIVATE_MSG.'">'.$m->from.'</a> le '.date('d/m/Y @ H:i',$m->time).' <br />';
			else {
			$m->from=preg_replace("/(([0-9]{1,3}\.[0-9]{1,3})\.([0-9]{1,3}\.[0-9]{1,3}))/i","\\2.x.x",$m->from);
			$pvtBox .= $m->from.' le '.date('d/m/Y @ H:i',$m->time).' <br />';
			}
			$pvtBox .= stripslashes(BBCHelper::decode($m->content)).'<br /><hr />';
		}
		$pvtBox .= '<p class="text-right"><a href="?private='.$m->from.'" class="btn btn-green"><i class="halflings comments"></i> '.ANSWER.' '.TO.' '.$m->from.'</a>
		<a href="?delprivate=1" class="btn btn-red"><i class="halflings trash"></i> '.EMPTY_MAILBOX.'</a><p/>
		</div>
		</div>';
		return $pvtBox;
	}
	/**
	*
	* FORMULAIRE DE RÉPONSE
	*/
	public function replyForm($type,$mpTo='') {
		$edit=0; $join=0; $show=0;
		if($type=='newtopic') {
			$name= NEW_TOPIC;
			$join=1;
			$show=$mpTo?0:1;
		} else if($type=='newpost') {
			$name= ANSWER;
			$join=1;
		} else if($type=='editpost') {
			if($s = implode("", file(MU_THREAD.$this->get_topic.'.dat'))) $topicObj = unserialize($s);
			else return false;
			$reply=$topicObj->getReply($this->get_editpost);
			$reply->content = preg_replace('!\[e\](.*)\[\/e\](\\r\\n)*!Ui','',$reply->content);
			$name= CHANGE;
			$edit=1;
		} else {
			$name= SEND_PRIVATE_MSG.' '.TO.' '.$mpTo;
			$show=1;
		}
		$buffer = '<!-- Reply form -->';
		if($edit || $show) {
			$buffer .= '<h4 class="forms-section">'.$name.'</h4>';
			$buffer .= '<div class="Box"><div>';
		} else {
			$buffer .= "<a class='btn btn-big' href=\"javascript:switchLayer('form');\" title='formulaire'>$name</a>\n";
			$buffer .= '<div class="toggle" id="form">';
		}
		$buffer .= '<br /><form id="formulaire" action="index.php#bottom" method="post" enctype="multipart/form-data" class="forms forms-columnar">';
		$buffer .= '<input type="hidden" name="action" value="'.$type.'" />';
		// Réponse
		if($type== 'newpost' || $edit) $buffer .= '<input type="hidden" name="topicID" value="'.$this->get_topic.'" />';
		// Mesage privé
		if($mpTo) $buffer .= '<input type="hidden" name="mpTo" value="'.$mpTo.'" />';
		// Edition
		if($edit) $buffer .= '<input type="hidden" name="postID" value="'.$this->get_editpost.'" />';
		$buffer .= '<div>';
		// Nouveau Sujet
		if($type== 'newtopic') {
			$buffer .= '<p>'.Tools::input(TITLE_SUBJECT, 'titre', '', 'text', '', '','','width-40').'</p>';
			if($this->isAdmin) $buffer .= '<p><label for="postit"><i class="halflings star"></i> '.PINNED.'</label>
			<input type="checkbox" id="postit" name="postit" value="1"></p>';
		}
		if(!$this->cLogin) $buffer .= '<p>'.Tools::input(USER_MENDATORY, 'anonymous', '', 'text', '', '','','width-40').'</p>';
		    $buffer .= BBCHelper::formattingHelp();
		if($edit) {
			$buffer .= '<p class="forms-inline">'.Tools::textarea(MESSAGE, 'message', $reply->content, '40', '10', '', '', '', 'width-70').'</p>';
		} else {
			$buffer .= '<p class="forms-inline">'.Tools::textarea(MESSAGE, 'message', '', '40', '10', '', '', '', 'width-70').'</p>';
		}

		if($join) $buffer .= '<p>'.Tools::input(ATTACH_FILE, 'attachment', '', 'file', '', '','','width-40').'</p>';
		$buffer .= '<p><button type="submit" class="text-right btn btn-green"><i class="halflings arrow-right"></i> '.SEND.'</button></p>';
		$buffer .= '</div>
		</form>
		</div>';
		return $buffer;
	}
	/**
	*
	* FORMULAIRE DE RESTAURATION DE LA SAUVEGARDE
	*/
	public function frestore() {
		$form = '<!-- Edit config form -->';
		$form .= '<div class="Box">
			<h4 class="forms-section">'.RESTAURATION_FORUM.'</h4>
			<div style="padding-top:10px">
				<form action="index.php" method="post" enctype="multipart/form-data" class="forms forms-columnar">
					<input type="hidden" name="restore" value="1" />
					<input type="hidden" name="action" value="restore" />
			' .//input($label, $name, $value='', $type='text', $placeholder='', $maxlength='255', $readonly=false, $class='', $icon, $require)
					'<p class="forms-inline">'.Tools::input(UPLOAD_BACKUP, 'backup', '', 'file', '', '','','width-40').'</p>
					<p><button type="submit" class="text-right btn btn-green"><i class="halflings hand-right"></i>&nbsp;'.SEND.'</button></p>
				</form>
			</div>
		</div>';

		return $form;
	}
	/**
	*
	* ÉDITION DE LA CONFIGURATION
	*/
	function editConf() {
		$fmode = $this->forumMode?'checked="checked" ':'';
		$qmode = $this->quoteMode?'checked="checked" ':'';
		if(!$wtp=@file_get_contents('welcome.txt')) $wtp=Tools::clean(BBCHelper::parse(WELCOME_TXT));
		$form = '<!-- Edit config form -->';
		$form .= '<h4 class="forms-section">'.CONFIG_OPTIONS.'</h4>';
		$form .= '<div style="padding-top:10px;">';
		$form .= '<form action="index.php" method="post" enctype="multipart/form-data" class="forms forms-columnar">
		<input type="hidden" name="action" value="editoption" />
		<p class="forms-inline">
		<label>'.TITLE_LOGO.'</label>
		<input type="text" name="uftitle" maxlength="60" value="'.Tools::clean($this->uforum).'" class="width-30" />
		&nbsp;<input type="file" name="attachment" class="width-40" />
		</p>

		<p class="forms-inline">
		<label>'.NAME_AND_URL.'</label>
		<input type="text" name="ufsitename" value="'.Tools::clean($this->siteName).'" placeholder="µForum" class="width-30" />
		&nbsp;<input type="url" maxlength="80" name="ufsite" value="'.$this->siteUrl.'" placeholder="http://…" class="width-40" />
		</p>
		' .//input($label, $name, $value='', $type='text', $placeholder='', $maxlength='255', $readonly=false, $class='', $icon, $require)
		    '<p class="forms-inline">'.Tools::textarea(META_DESCRIPTION, 'ufmetadesc', Tools::clean($this->metaDesc), '10', '2', 'Lightweight bulletin board without sql', '150', '', 'width-70').'</p>
		    <p class="forms-inline">'.Tools::input(INDEX_MAX_MSG, 'nbmess', $this->nbrMsgIndex, 'number', '', '2', '', 'width-10').'</p>
		    <p class="forms-inline">'.Tools::input(TOPIC_MAX_MSG, 'nbmessTopic', $this->nbMsgTopic, 'number', '', '2', '', 'width-10').'</p>
		    <p class="forms-inline">'.Tools::input(MEM_MAX_DISP, 'nbmb', $this->nbrMb, 'number', '', '2', '', 'width-10').'</p>
		    <p class="forms-inline">'.Tools::input(LANGUAGE, 'uflang', $this->lang, 'text', '', '2', '', 'width-10').'</p>
		    <p class="forms-inline">'.Tools::input(MAX_AVATAR_WEIGHT, 'maxav', ($this->maxAvatarSize/1024), 'number', '', '10', '', 'width-10', 'resize-small').'</p>
		     <p class="forms-inline">'.Tools::input(ALLOWED_EXT, 'exts', Tools::clean($this->extStr), 'text', '', '50', '', 'width-40'). '</p>
		<p>
		<label>'.PRIVATE_MODE.'</label>
		<input name="fmode" type="checkbox" '.$fmode.'/>
		</p>
		<p>
		<label>'.SHOW_SIGNATURES.'</label>
		<input name="qmode" type="checkbox" '.$qmode.'/>
		</p>
		'.BBCHelper::formattingHelp().'
		<p class="forms-inline">'.Tools::textarea(WELCOME_MSG, 'message', $wtp, '40', '20', '', '', '', 'width-70'). '</p>

		<p class="text-right">
		<button type="submit" class="btn btn-green"><i class="halflings hand-right"></i>&nbsp;'.REC.'</button>
		</p>
		</form>';
		$form .= '</div>';

		return $form;
	}
}

/**
*
* INITIALISATION
*/
header('Content-Type: text/html; charset=utf-8');
$MF = new Template();//MF pour µForum

$stats=$MF->forum->getStat();

/**
*
* RENDUS HTML (Templates)
*/
// e($MF);
?><!DOCTYPE html>
<html lang="<? echo $MF->lang; ?>">
<head>
<meta charset="utf-8">
<meta name="description" content="<? echo $MF->metaDesc; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="<?php echo Tools::img('icon','',true);?>" />
<base href="<?php echo MU_BASE_URL; ?>" />
<link rel="stylesheet" type="text/css" href="css/style_<?php echo $MF->cStyle; ?>.css" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
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

<?php
if(preg_match('/.gif$|.jpg$|.png$/i',$MF->uforum) && file_exists($MF->uforum)) {
	$tmp='<a href="'.MU_BASE_URL.'" title="'.htmlspecialchars($MF->siteName, ENT_QUOTES).'"><img src="'.$MF->uforum.'" alt="'.htmlspecialchars($MF->siteName, ENT_QUOTES).'" /></a>';
echo '<title>'.htmlspecialchars($MF->siteName, ENT_QUOTES).'</title>';
} else {
	if ($MF->uforum == '[b]&micro;[/b]Forum') {
		$tmp='<a href="'.MU_BASE_URL.'" title="'.PROJECT.'">'.Tools::img('icon-big').'Forum</a>';
	} else {
		$tmp=BBCHelper::decode($MF->uforum);
	}
	$bbcodes=array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[e]','[/e]','[hr]');
	echo '<title>'.str_replace($bbcodes,'',$MF->uforum).'</title>';
}

echo '</head>';
echo '<body onload="init();">';
echo ' <!-- Navbar
================================================== -->
<div class="wrapper">

<header id="header" class="units-row">
<h1 id="root">'.$tmp.'</h1>
<nav class="nav-g unit-100">
<ul>';
                       echo $MF->menu(); if($MF->isMember || !$MF->forumMode){ echo $MF->menu_admin(); }
echo ' </ul>
</nav>
</header>

<div id="main">

<div class="units-row">
<article class="unit-100">
<noscript>
<div class="message message-error">
<span class="close"></span>
<header><i class="halflings warning-sign"></i> '.JS_UNAVAILABLE.'</header>
'.JS_UNAVAILABLE_MSG.'
</div>
</noscript>';

// message d'erreur (en cas de mauvais mot de passe, membre déjà existant etc...)
echo $MF->session->msg();
if($MF->isMember || !$MF->forumMode) {
    echo $MF->breadcrumbs();
	if($MF->haveMP) echo $MF->showPrivateMsg();	
	if($MF->get_editpost) echo $MF->replyForm('editpost');
	else if($MF->get_conf) echo $MF->editConf();
	else if($MF->get_topic) echo $MF->showPosts();
	else if($MF->get_memberlist) echo $MF->showMemberlist();
	else if($MF->searchMember) echo $MF->searchMember();
	else if($MF->get_editprofil) echo $MF->editProfilForm();
	else if($MF->get_private) echo $MF->replyForm('mp',$MF->get_private);
	else if($MF->get_restore) echo $MF->frestore();
	// MODE LIBRE
	else if(!$MF->forumMode && !$MF->isMember) {
		$st=1;
	    echo '
		<nav class="onglets">
		<ul>
            <li class="tabA tab" id="tabhome" onclick="javascript:tab(\'home\');"><i class="halflings home"></i>&nbsp;'.HOME.'</li>
            <li class="tabA tab" id="tabsignup" onclick="javascript:tab(\'signup\');"><i class="halflings user"></i>&nbsp;'.SIGN_UP.'</li>
            <li class="tabA tab" id="tabtopics" onclick="javascript:tab(\'topics\');"><i class="halflings th-list"></i>&nbsp;'.FORUMS.'</li>
        </ul>
        </nav>
        <div class="tabContents">
            <div class="tabContent" id="tabContenthome">
			'.$MF->welcomeText().'
			</div>
			<div class="tabContent" id="tabContentsignup">
			'.$MF->registerForm().'
			</div>
			<div class="tabContent" id="tabContenttopics">
			'.$MF->showTopics().'
			</div>
		</div>';	
	} else { #on est connecté, alors on affiche uniquement la liste des forums
		echo $MF->showTopics();
	}
} else {// MODE PRIVÉ
	echo '
	<nav class="onglets">
	<ul>
        <li class="tabA tab" id="tabhome" onclick="javascript:tab(\'home\');"><i class="halflings home"></i>&nbsp;'.HOME.'</li>
        <li class="tabA tab" id="tabsignup" onclick="javascript:tab(\'signup\');"><i class="halflings user"></i>&nbsp;'.SIGN_UP.'</li>
    </ul>
    </nav>
    <div class="tabContents">
        <div class="tabContent" id="tabContenthome">
		'.$MF->welcomeText().'
		</div>
		<div class="tabContent" id="tabContentsignup">
		'.$MF->registerForm().'
		</div>
	</div>';
}
$arr_cnct=$MF->conn->updateVisit($MF->cLogin);
$a=array();
echo '
<hr />

<div class="units-row units-split">

	<div class="unit-33"><h4>'.STATISTICS.'</h4>';
if($stats['members']>1) {$a[0]='s';$a[1]='ont';}
else {$a[0]='';$a[1]='a';}//Total membres
$m=($stats['messages']>1)?'s':'';//Messages
$s=($stats['topics']>1)?'s':'';//Sujets
$arr_cnct[0]=($arr_cnct[0])?$arr_cnct[0]:L_NONE;

		echo '<p>'.WE_HAVE.' '.$stats['messages'].' '.MESSAGE.$m.' '.IN.' '.$stats['topics'].' '.TOPIC.$s.'. <br />
		'.WELCOME_TO.', <span class="color-orange">'.$stats['lastMb'].'</span><br />
		'.TOTAL_MB.$a[0].': '. $stats['members'].'</p>
	</div>
	<div class="unit-33">
		<h4>'.LEGEND.'</h4>
		<p>
		<i class="halflings folder-open"></i> '.NO_UNREAD_MSG.'<br />
		<i class="halflings star"></i> '.PINNED.'<br />
		<i class="halflings fire"></i> '.UNREAD_MSG.'<br />
		<i class="halflings file"></i> '.ATTACHMENT.'
		</p>
	</div>
	<div class="unit-33">
	<h4>'.WHO_IS_ONLINE.'</h4>
	<p>'.MB_ONLINE.' : <b>'.$arr_cnct[0].'</b><br />'.GUESTS.' : '.$arr_cnct[1].'</p>
	</div>
';

echo ' 
</div>
	<footer id="footer">© 2011-'.date('Y').' '.$MF->siteName.'.
	<span>'.POWEREDBY.' v.'.VERSION.'&nbsp;&nbsp;
	&nbsp;&nbsp;<a href="' .Tools::baseURL(). '#top" title="'.TOP.'"><i class="halflings chevron-up"></i></a></span>
	</footer>
';
?>

		<!-- Calendrier -->
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
		<tr><td id="ds_calclass"></td></tr></table>
		<!-- Le javascript
		================================================== -->
		<script src="js/scripts.js"></script>
		<script type="text/javascript">//<!--
<?php if (isset($_SERVER['argv'][0]) && $_SERVER['argv'][0] == 'register=true') {
	$idTab = 'signup';
} else {
	$idTab = 'home';
} ?>
		var idTab = '<?php echo $idTab;?>';
		tab(idTab);
	//--></script>
		<?php $MsgId = $MF->session->MsgId();
			if(!empty($MsgId))
			echo '<script src="js/visual.js"></script>
			<script>fadeOut("'.$MsgId.'");</script>';
		?>
</body>
</html>
