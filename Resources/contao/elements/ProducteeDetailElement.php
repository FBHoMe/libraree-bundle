<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\ProducteePortfolioModel;

class ProducteeDetailElement extends BaseDetailElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_detail';

    /**
     * @return string
     */
    public function generate()
    {
        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        parent::compile();

        if (TL_MODE != 'BE') {
            $this->generateFrontend();
        }
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        if($_GET['id']){
            $pin = $this->getById($_GET['id']);
            $this->Template->pin = $pin;
        }
    }

    /**
     * @param $id
     * @return object
     */
    private function getById($id)
    {
        return ProducteePortfolioModel::findByIdOrAlias($id);
    }

}