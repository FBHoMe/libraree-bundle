<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\PersonaleePinModel;

class PersonaleeDetailElement extends \ContentElement
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
        if (TL_MODE == 'BE') {
            $this->strTemplate          = 'be_wildcard';
            $this->Template             = new \BackendTemplate($this->strTemplate);
            $this->Template->wildcard   = "### Personen Detailansicht ###";
        } else {

            if($_GET['id']){
                $promoter = $this->getById($_GET['id']);
                $this->Template->promoter = $promoter;
            }
        }
    }

    /**
     * @param $id
     * @return PersonaleePinModel
     */
    private function getById($id)
    {
        return PersonaleePinModel::findByIdOrAlias($id);
    }

}