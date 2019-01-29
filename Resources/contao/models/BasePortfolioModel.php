<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 19.09.2017
 * Time: 09:52
 */

namespace Home\LibrareeBundle\Resources\contao\models;

use Home\PearlsBundle\Resources\contao\Helper\DataHelper;

class BasePortfolioModel extends \Contao\Model
{
    /**
     * find portfolios by $strTable and $options
     *
     * @param $strTable
     * @param $options
     * @return array|null
     */
    public static function findByTable($strTable, $strColumn, $varValue=null, $options=array())
    {
        $return = array();
        if(strpos($strTable, '_portfolio') === false){
            $strTable = $strTable. '_portfolio';
        }
        $strClass = \Contao\Model::getClassFromTable($strTable);
        $strModel = $strClass::findBy($strColumn, $varValue, $options);
        
        if($strModel instanceof \Contao\Model\Collection){
            $return = DataHelper::convertValue($strModel->fetchAll());
        }

        return $return;
    }

    /**
     * closures table name
     * @var string
     */
    //protected static $strTableClosure = 'tl_closure';

    /**
     * overwrites the Model\save() function. Needed to update the closureLib
     *
     * @see Contao.Model::save()
     */
    public function save()
    {
        #-- save the closure
        $moduleName = $_GET['do'];
        $tableName = 'tl_' . $moduleName . '_closures';

        BaseClosuresModel::updateClosures($this->id, self::_getAllPids(), $tableName);

        parent::save();
    }

    /**
     * find all portfolios of $strTable
     *
     * @param $strTable
     * @return array
     */
    public static function findAllPortfolios($strTable)
    {
        $sqlQuery = "
			SELECT *
			FROM " . $strTable . "
			ORDER BY sorting
		";
        $objResult = \Database::getInstance()
            ->prepare($sqlQuery)
            ->execute()
        ;
        return DataHelper::convertValue($objResult->fetchAllAssoc());
    }

    /**
     * find all portfolios in portfolioIds
     * @param string $strTable
     * @param array $portfolioIds
     * @return array
     */
    public static function findPortfoliosIn($strTable, $portfolioIds)
    {
        $sqlQuery = "
					SELECT *
					FROM " . $strTable . "_portfolio
					WHERE id IN  (" . implode(',',$portfolioIds) . ")
					ORDER BY sorting
				";
        $objResult = \Database::getInstance()
            ->prepare($sqlQuery)
            ->execute()
        ;
        return DataHelper::convertValue($objResult->fetchAllAssoc());
    }

    /**
     * @param $descendant_id
     * @param $strTableClosure
     * @return array
     */
    public static function findParents($descendant_id, $strTableClosure)
    {
        $sqlQuery = "
			SELECT *
			FROM " . $strTableClosure . "
			WHERE descendant_id =  " . $descendant_id . "
		";
        $objResult = \Database::getInstance()
            ->prepare($sqlQuery)
            ->execute()
        ;
        return DataHelper::convertValue($objResult->fetchAllAssoc());
    }

    /**
     * @param $id
     * @param $tableName
     * @return array
     */
    public static function findChildren($id, $tableName) {
        return BaseClosuresModel::findChildren($id,0, $tableName);
    }

    /**
     * delete child closures
     */
    public function deleteClosures()
    {
        $moduleName = $_GET['do'];
        $tableName = 'tl_' . $moduleName . '_closures';

        return BaseClosuresModel::deleteClosures(BaseClosuresModel::findChildren($this->id, 0, $tableName), $tableName);
    }

    /**
     * update the closure db table for the element itself and all children by saving the model
     */
    public function updateClosure()
    {
        self::save();

        return self::updateChildClosure($this->id);
    }

    /**
     * overwrites the Model\save() function. Needed to update the closureLib
     *
     * @see Contao.Model::save()
     */
    public function updateClosureExternal($moduleName)
    {
        #-- save the closure
        $tableName = 'tl_' . $moduleName . '_closures';

        BaseClosuresModel::updateClosures($this->id, self::_getAllPids(), $tableName);
    }

    /**
     * update closure db table for childrens (recursiv) by saving the model
     * @param int $pid
     */
    protected static function updateChildClosure($pid)
    {
        $objCollection = self::findBy('pid',$pid);
        if ($objCollection){
            foreach($objCollection->getModels() as $objLib) {
                $objLib->save();
                self::updateChildClosure($objLib->id);
            }
        }
        return;
    }

    /**
     * return all parent ids - based on recursive db requests.
     * will be needed, if element is new or moved in tree so the closure ids won't be correct.
     *
     * @return array with all parent ids
     */
    protected function _getAllPids()
    {
        return self::_getParentPids($this->pid);
    }

    /**
     * recursive function returns an array with all parent ids
     *
     * @param $pid
     * @return array
     */
    protected static function _getParentPids($pid)
    {
        if ($pid == 0) {
            return array();
        }

        $parentLib = self::findByPk($pid);
        $return = array($pid);
        if ($parentLib->pid > 0) {
            $return = array_merge($return, self::_getParentPids($parentLib->pid));
        }

        return $return;
    }

}