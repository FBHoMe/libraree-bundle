<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 19.09.2017
 * Time: 09:52
 */

namespace Home\LibrareeBundle\Resources\contao\models;

class BaseClosuresModel extends \Contao\Model
{
    /**
     * find children from library element till a specific depth
     *
     * @param $portfolioId - the library portfolio id
     * @param int $depth - the depth to search
     * @param $strTableClosure - the closures table if called from portfolioModel
     * @return array
     */
    public static function findChildren($portfolioId, $depth=0, $strTableClosure)
    {
        $result = self::getChildren($portfolioId, $depth, $strTableClosure);

        $return = array();
        if (count($result) > 0) {
            foreach($result as $key=>$val) {
                $return[] = $val['descendant_id'];
            }
        }

        return $return;
    }

    /**
     * checks if library portfolio has children
     *
     * @param $portfolioId
     * @param int $depth - the depth to search
     * @param $strTableClosure - the closures table if called from portfolioModel
     * @return bool
     */
    public static function hasChildren($portfolioId, $depth=0, $strTableClosure)
    {
        $result = self::getChildren($portfolioId, $depth, $strTableClosure);
        if (is_array($result) && count($result) > 1) {
            return true;
        }
        return false;
    }

    /**
     * get childrens from library portfolio from db
     *
     * @param int $portfolioId - the library portfolio id
     * @param int $depth - the depth to search
     * @param $strTableClosure - the closures table if called from portfolioModel
     * @return array
     */
    protected static function getChildren($portfolioId, $depth=0, $strTableClosure)
    {
        $sql = 'SELECT descendant_id FROM ' . $strTableClosure . ' WHERE ancestor_id = ?';
        if ($depth > 0) {
            $sql .= ' AND path_length <= '.$depth;
        }
        return \Database::getInstance()
            ->prepare($sql)
            ->execute($portfolioId)
            ->fetchAllAssoc();
    }

    /**
     * update the closures db fields
     *
     * @param $portfolioId - the actual library portfolio id
     * @param array $pids - all parent ids from actual library element as array
     * @param $strTableClosure - the closures table if called from portfolioModel
     * @return \Contao\Database\Result|object
     */
    public static function updateClosures($portfolioId, array $pids, $strTableClosure)
    {
        #-- delete all closures for this library portfolio
        self::deleteClosures($portfolioId, $strTableClosure);

        #-- add the own id
        $pids = array_merge(array($portfolioId), $pids);

        #-- write new closures
        $dbSet = array();
        $depth = 0;
        foreach($pids as $pid) {
            $dbSet[] = '('.$pid.','.$portfolioId.','.$depth.')';
            $depth++;
        }

        return \Database::getInstance()
            ->prepare('INSERT INTO ' . $strTableClosure . ' (ancestor_id, descendant_id, path_length) VALUES '.implode(',', $dbSet))
            ->execute();
    }

    /**
     * delete all closures for (multiple) library portfolios
     *
     * @param int|array|string $portfolioId - can be the library portfolio id or an array of library portfolio ids
     * @param $strTableClosure - the closures table if called from portfolioModel
     * @return mixed|null|void
     */
    public static function deleteClosures($portfolioId, $strTableClosure)
    {
        if (is_string($portfolioId)) {
            $portfolioId = array(intval($portfolioId));
        } else if (is_int($portfolioId)) {
            $portfolioId = array($portfolioId);
        }

        if (is_array($portfolioId) and count($portfolioId) > 0 ){
            return \Database::getInstance()->prepare('DELETE FROM ' . $strTableClosure . ' WHERE descendant_id IN ('.implode(',',$portfolioId).')')
                ->execute()
                ->affectedRows;
        }
        return;
    }

}