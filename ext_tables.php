<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
t3lib_extMgm::addPlugin(array('LLL:EXT:wow_signature/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","WOW - Signature Admin");
if(TYPO3_MODE=="BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wowsignature_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wowsignature_pi1_wizicon.php';

$tempColumns = Array (
	"tx_wowsignature_sig_setup" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:wow_signature/locallang_db.xml:tx_wowcharacter_characters.tx_wowsignature_sig_setup",		
		"config" => Array (
			"type" => "text",
			"wrap" => "OFF",
			"cols" => "80",	
			"rows" => "50",
		)
	),
	"tx_wowsignature_sig_files" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:wow_signature/locallang_db.xml:tx_wowcharacter_characters.tx_wowsignature_sig_files",		
		"config" => Array (
			"type" => "group",
			"internal_type" => "file",
			"allowed" => "gif,png,jpeg,jpg,ttf",
			"max_size" => 500,	
			"uploadfolder" => "uploads/tx_wowsignature",
			"size" => 5,	
			"minitems" => 0,
			"maxitems" => 10,
		)
	),
);

t3lib_div::loadTCA("tx_wowcharacter_characters");
t3lib_extMgm::addTCAcolumns("tx_wowcharacter_characters",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_wowcharacter_characters","tx_wowsignature_sig_setup;;;;1-1-1, tx_wowsignature_sig_files");
?>