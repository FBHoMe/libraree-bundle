<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\PromoteeBundle\Resources\contao\modules;

use Home\PromoteeBundle\Resources\contao\models\PromoteeModel;

class PromoteeModule extends \Module
{
    /**
     * @var string
     */
    protected $strTemplate = 'mod_promotee';

    /**
     * Do not display the module if there are no menu items
     *
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
            $this->Template->wildcard   = "### Promoter Test Module ###";
        } else {
            $model = PromoteeModel::findByIdOrAlias('test');
            $this->Template->promoter = $model;
        }
    }
}