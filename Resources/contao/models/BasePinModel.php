<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 11:22
 */

namespace Home\LibrareeBundle\Resources\contao\models;

class BasePinModel extends \Contao\Model
{
    /**
     * find pins by $strTable and $options
     *
     * @param $strTable
     * @param $options
     * @return array|null
     */
    public static function findByTable($strTable, $options)
    {
        $return = array();
        $strClass = \Contao\Model::getClassFromTable($strTable. '_pin');
        $strModel = $strClass::findBy($options, null);

        if($strModel instanceof \Contao\Model\Collection){
            $return = $strModel->fetchAll();
        }

        return $return;
    }
}