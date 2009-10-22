<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jobe <jobe@jobesoft.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wow_character').'inc/class.tx_wowcharacter_character.php');/*characters*/
require_once(t3lib_extMgm::extPath('wow_signature').'inc/class.tx_wowsignature_signature.php');/*signature*/


/**
 * Plugin 'WOW - Signature Admin' for the 'wow_signature' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowsignature
 */
class tx_wowsignature_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_wowsignature_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wowsignature_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'wow_signature';	// The extension key.
	var $pi_checkCHash = true;
	
	private $sig = null;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf){try{
	
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		
		$this->cObj->start(array_merge(
			array_flaten($this->flexform,'ff'),
			array_flaten($this->piVars,'pivar'),
			array_flaten($extConf,'ext'),
			array(
				'now' => time(),
			)
		));
		
		//print('<pre>');var_dump($this->cObj->data);die('</pre>');/*DEBUG*/

    return $this->pi_wrapInBaseClass($this->cObj->COBJ_ARRAY($conf));
		
	}catch (Exception $e){
		return $e->getMessage();
	}}

	/**
	 * Returns the signature image
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function signature($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$uid = $this->piVars['id'];
		$char = new tx_wowcharacter_character(array('where'=>array('uid'=>$uid)));
		$this->sig = new tx_wowsignature_signature($char);
		header("Content-type: image/png");
		imagepng($this->sig->create());
	}
	
}

function array_flaten($data,$prefix=''){
	if(is_array($data)&&count($data)){
		foreach($data as $k => $v){
			$k = strtr($k,array('.'=>''));// strip dots
			if(is_array($v)){
				$tmp = array_merge($tmp?$tmp:array(),array_flaten($v,$prefix.'.'.$k));
			}else{
				$tmp[$prefix.'.'.$k] = $v;
			}
		}
	}
	return is_array($tmp)?$tmp:array();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_signature/pi1/class.tx_wowsignature_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_signature/pi1/class.tx_wowsignature_pi1.php']);
}

?>