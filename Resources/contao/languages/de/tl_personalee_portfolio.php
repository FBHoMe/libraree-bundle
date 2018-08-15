<?php

/**
 * tl_personalee_portfolio language file
 * DE Deutsch
 *
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

$moduleName = 'tl_personalee_portfolio';

// #-- Legend
$GLOBALS['TL_LANG'][$moduleName]['default_legend']      = 'Gruppe';
$GLOBALS['TL_LANG'][$moduleName]['published_legend']    = 'Veröffentlichung';

// #-- Fields
$GLOBALS['TL_LANG'][$moduleName]['name']        = array('Name', 'Der Gruppen-Name, wird im Backend angezeigt');
$GLOBALS['TL_LANG'][$moduleName]['title']       = array('Titel', 'Der Gruppen-Titel, wird auf der Webseite angezeigt');
$GLOBALS['TL_LANG'][$moduleName]['published']   = array('Veröffentlichen', 'Die Gruppe auf der Webseite angezeigen');
$GLOBALS['TL_LANG'][$moduleName]['alias']       = array('Alias', 'Der Alias ist eine eindeutige Referenz auf den Namen. Wenn nicht ausgefüllt, wird diese automatisch erstellt.');

// +-- Buttons
$GLOBALS['TL_LANG'][$moduleName]['new']         = array('Neue Gruppe', 'Eine neue Gruppe erstellen');
$GLOBALS['TL_LANG'][$moduleName]['show']        = array('Gruppe', 'Einträge der Gruppe ID %s anzeigen');
$GLOBALS['TL_LANG'][$moduleName]['copy']        = array('Gruppe duplizieren', 'Gruppe ID %s duplizieren');
$GLOBALS['TL_LANG'][$moduleName]['delete']      = array('Gruppe löschen', 'Gruppe ID %s löschen');
$GLOBALS['TL_LANG'][$moduleName]['edit']    	= array('Gruppe bearbeiten', 'Gruppe ID %s bearbeiten');

//$GLOBALS['TL_LANG'][$moduleName]['cut']    	 	= array('Klassifikation verschieben', 'Klassifikation ID %s verschieben');
//$GLOBALS['TL_LANG'][$moduleName]['copyChilds']  = array('Klassifikation mit Kindelementen duplizieren', 'Klassifikation ID %s inklusive Kindelemente duplizieren');