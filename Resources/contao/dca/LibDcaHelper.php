<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 22.11.2018
 * Time: 15:37
 */

namespace Home\LibrareeBundle\Resources\contao\dca;


class LibDcaHelper
{


    // # --- closures --------------------------------------------------------------

    /**
     * set the closure dca
     * @param $dca
     */
    public static function setClosureDca($dca)
    {
        $dca
            ->addConfig('closure')
            ->addField('idref', 'ancestor_id')
            ->addField('idref', 'descendant_id')
            ->addField('integer', 'path_length', array('exclude'=>false))
        ;
    }

    // # --- portfolio --------------------------------------------------------------

    /**
     * set the base portfolio dca
     * @param $dca
     * @param $table - ohne suffix '_pin' oder '_portfolio'; dieser wird in der funktion hinzugefügt
     */
    public static function setLibPortfolioBase($dca, $table)
    {
        $dca
            ->addConfig('tree', array(
                'ctable' => array($table . '_pin'),
            ))
            ->addList('base')
            ->addSorting('tree')
        ;
    }

    /**
     * set the standard operations for libraree portfolio elements
     * @param $dca
     * @param $table - ohne suffix '_pin' oder '_portfolio'; dieser wird in der funktion hinzugefügt
     */
    public static function setLibPortfolioOperationsStandard($dca, $table)
    {
        $dca
            ->addOperation('edit', 'edit', array('href' => 'table=' . $table . '_pin'),'_first')
            ->addOperation('editheader', 'editheader')
            ->addOperation('copy')
            ->addOperation('delete','delete',array('attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'))
            ->addOperation('show')
        ;

        #-- Global_Operations
        $dca
            ->addGlobalOperation('toggleNodes')
            ->addGlobalOperation('all')
        ;
    }

    // # --- pin --------------------------------------------------------------

    /**
     * set the base pin dca
     * @param $dca
     * @param $table - ohne suffix '_pin' oder '_portfolio'; dieser wird in der funktion hinzugefügt
     */
    public static function setLibPinBase($dca, $table)
    {
        $dca
            ->addConfig('liste', array(
                'ptable' => $table . '_portfolio',
            ))
            ->addList('base')
            ->addSorting('liste')
        ;
    }

    /**
     * set the standard operations for libraree pin elements
     * @param $dca
     */
    public static function setLibPinOperationsStandard($dca)
    {
        $dca
            ->addOperation('edit', 'edit', array(),'_first')
            ->addOperation('copy')
            ->addOperation('delete','delete',array(
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'
            ))
            ->addOperation('toggle', 'toggle')
            ->addOperation('show')
        ;
    }

    // # --- taxonomy --------------------------------------------------------------

    /**
     * set the base taxonomy dca
     * @param $dca
     * @param $table - ohne suffix '_pin' oder '_portfolio'; dieser wird in der funktion hinzugefügt
     */
    public static function setLibTaxonomyDca($dca, $table)
    {
        $dca
            ->addConfig('liste', array())
            ->addList('base')
            ->addSorting('liste')
        ;
        $dca
            ->addField('id', 'id')
            ->addField('pid', 'pin_id', array(
                'foreignKey' => $table . '_pin.id',
                'relation' => array(
                    'type'=>'belongsTo',
                    'load'=>'eager',
                    'table'=> $table . '_pin',
                    'field'=>'id',
                ),
            ))
            ->addField('tstamp', 'tstamp')
            ->addField('name', 'name', array())
            ->addField('alias', 'alias')
            ->addField('pid', 'taxonomie_id', array(
                'foreignKey' => 'tl_taxonomee.id',
                'relation' => array(
                    'type'=>'belongsTo',
                    'load'=>'eager',
                    'table'=>'tl_taxonomee',
                    'field'=>'id',
                ),
            ))
        ;
        $dca
            ->addPaletteGroup('default', array('name', 'alias', 'pin_id', 'taxonomie_id'))
        ;
    }

    // # --- global --------------------------------------------------------------

    /**
     * set the standard fields for libraree elements
     * @param $dca
     * @param $table - ohne suffix '_pin' oder '_portfolio'; dieser wird in der funktion hinzugefügt
     */
    public static function setLibFieldsStandard($dca, $table)
    {
        $dca
            ->addField('id', 'id')
            ->addField('pid', 'pid', array(
                'foreignKey'    => $table . '_portfolio.id',
                'relation'      => array(
                    'type'  => 'belongsTo',
                    'load'  => 'eager',
                    'table' => $table . '_portfolio',
                    'field' => 'id',
                ),
            ))
            ->addField('tstamp', 'tstamp')
            ->addField('name','title', array(
                'eval' => array(
                    'maxlength' => 90,
                    'mandatory' => true,
                    'tl_class'  => 'w50'
                )
            ))
            ->addField('name', 'name', array(
                'eval' => array(
                    'maxlength' => 90,
                    'tl_class'  => 'w50'
                )
            ))
            ->addField('published', 'published')
            ->addField('alias','alias', array(
                'eval' => array(
                    'tl_class' => 'w50',
                ),
            ))
            ->addField('sorting','sorting')
        ;
    }
}