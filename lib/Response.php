<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * Response représente la requête http renvoyée à l'utilisateur
 */
class Response {
	private static $header = 'HTTP/1.0 200 OK';
	private static $body = '';
	
	public static function appendBody ($text) {
		self::$body = $text;
	}
	
	public static function send () {
		header (self::$header);
		echo self::$body;
	}
}
