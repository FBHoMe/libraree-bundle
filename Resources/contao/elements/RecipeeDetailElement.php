<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\RecipeePortfolioModel;
use Home\LibrareeBundle\Resources\contao\models\RecipeePinModel;
use Home\PearlsBundle\Resources\contao\Helper as Helper;

class RecipeeDetailElement extends BaseDetailElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_recipee_detail';

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
            $pinModel = $this->getModelById($_GET['id']);
            $pin = Helper\DataHelper::convertValue($pinModel->row());
            $this->Template->pin = $pin;
        }

    }

    /**
     * @param $id
     * @return object
     */
    private function getModelById($id)
    {
        return RecipeePinModel::findByIdOrAlias($id);
    }

}