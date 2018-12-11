<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 11:22
 */

namespace Home\LibrareeBundle\Resources\contao\models;

use Home\PearlsBundle\Resources\contao\Helper\DataHelper;

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
        if(strpos($strTable, '_pin') === false){
            $strTable = $strTable. '_pin';
        }
        $strClass = \Contao\Model::getClassFromTable($strTable);
        $strModel = $strClass::findBy($options, null);

        if($strModel instanceof \Contao\Model\Collection){
            $return = DataHelper::convertValue($strModel->fetchAll());
        }

        return $return;
    }
}