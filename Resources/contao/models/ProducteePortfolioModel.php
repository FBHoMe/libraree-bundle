<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 19.09.2017
 * Time: 09:52
 */

namespace Home\LibrareeBundle\Resources\contao\models;

class ProducteePortfolioModel extends BasePortfolioModel
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_productee_portfolio';

    /**
     * closures table name
     * @var string
     */
    protected static $strTableClosure = 'tl_productee_closure';

}