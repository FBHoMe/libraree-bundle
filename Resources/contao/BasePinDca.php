<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 22.11.2018
 * Time: 15:37
 */

namespace Home\LibrareeBundle\Resources\contao;

use Home\TaxonomeeBundle\Resources\contao\models\TaxonomeeModel;

class BasePinDca extends \Contao\Backend
{

    public function getKeywordOptions(\Contao\DC_Table $dc)
    {
        $return = array();

        if($dc->table){
            $searchAlias = strtolower(explode('_', $dc->table)[1]);

            $parent = TaxonomeeModel::findBy(array(
                TaxonomeeModel::getTable() . '.alias LIKE "' . $searchAlias . '"'
            ), null);

            if($parent){
                $parent = $parent->row();
            }

            if(is_array($parent) && count($parent) > 0){
                #-- get all pins with keywords portfolio as parent
                $options = TaxonomeeModel::findBy(array(
                    TaxonomeeModel::getTable() . ".pid = " . $parent['id']
                ), null);

                if($options){
                    $options = $options->fetchAll();
                }

                if(is_array($options) && count($options) > 0){
                    foreach ($options as $row){
                        $return[$row['id']] = $row['name'];
                    }
                    asort($return);
                }
            }
        }

        return $return;
    }

    public static function getTaxonomieFromTable($table)
    {
        $return = array();
        $searchAlias = strtolower(explode('_', $table)[1]);

        $parent = TaxonomeeModel::findBy(array(
            TaxonomeeModel::getTable() . '.alias LIKE "' . $searchAlias . '"'
        ), null);

        if($parent){
            $parent = $parent->row();
        }

        if(is_array($parent) && count($parent) > 0){
            #-- get all pins with keywords portfolio as parent
            $options = TaxonomeeModel::findBy(array(
                TaxonomeeModel::getTable() . ".pid = " . $parent['id']
            ), null);

            if($options){
                $options = $options->fetchAll();

                if(is_array($options) && count($options) > 0){
                    foreach ($options as $row){
                        $return[$row['id']] = $row;
                    }
                }
            }
        }

        return $return;
    }

    public function updateTaxLink($value, \Contao\DC_Table $dc)
    {
        $clientName = explode('_',$dc->table)[1];
        $linkTable = \Contao\Model::getClassFromTable('tl_' . $clientName . '_tax_link');

        #-- remove old entries
        $oldEntries = $linkTable::findBy(array(
            $linkTable::getTable() . '.pin_id = ' . $dc->activeRecord->id
        ), null);

        if($oldEntries instanceof \Contao\Model\Collection){
            foreach ($oldEntries as $oldEntry){
                $oldEntry->delete();
            }
        }

        #-- add new entries
        if($value && deserialize($value) && $dc->table && $dc->activeRecord->id){
            $taxIds = deserialize($value);
            $time = time();

            if(is_array($taxIds) && count($taxIds) > 0){
                foreach ($taxIds as $taxId){
                    $newLink = new $linkTable;
                    $newLink->__set('pin_id', $dc->activeRecord->id);
                    $newLink->__set('taxonomie_id', $taxId);
                    $newLink->__set('name', $dc->activeRecord->id . '_' . $taxId);
                    $newLink->__set('alias', $dc->activeRecord->id . '_' . $taxId);
                    $newLink->__set('tstamp', $time);
                    $newLink->save();
                }
            }
        }

        return $value;
    }

    public static function updateTaxLinkStatic($value, $table, $id)
    {
        $clientName = explode('_',$table)[1];
        $linkTable = \Contao\Model::getClassFromTable('tl_' . $clientName . '_tax_link');

        #-- remove old entries
        $oldEntries = $linkTable::findBy(array(
            $linkTable::getTable() . '.pin_id = ' . $id
        ), null);

        if($oldEntries instanceof \Contao\Model\Collection){
            foreach ($oldEntries as $oldEntry){
                $oldEntry->delete();
            }
        }

        #-- add new entries
        if($value && deserialize($value) && $table && $id){
            $taxIds = deserialize($value);
            $time = time();

            if(is_array($taxIds) && count($taxIds) > 0){
                foreach ($taxIds as $taxId){
                    if($taxId){
                        $newLink = new $linkTable;
                        $newLink->__set('pin_id', $id);
                        $newLink->__set('taxonomie_id', $taxId);
                        $newLink->__set('name', $id . '_' . $taxId);
                        $newLink->__set('alias', $id . '_' . $taxId);
                        $newLink->__set('tstamp', $time);
                        $newLink->save();
                    }

                }
            }
        }

        return $value;
    }

    public function getPublicStatusOptions(\Contao\DC_Table $dc)
    {
        return array(
            '0'=>'Öffentlich',
            '1'=>'Intern',
            '2'=>'Geheim'
        );
    }

}