<?php

namespace Home\LibrareeBundle\Resources\contao\elements;

use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\PearlsBundle\Resources\contao\Helper\GeoCoords;
use Home\TaxonomeeBundle\Resources\contao\models\TaxonomeeModel;

class ProximityMap extends BaseListElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_map';

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
            $this->generateBackend();
        } else {
            #-- openLayers
            $GLOBALS['TL_CSS'][] = 'bundles/homelibraree/openLayer_V5.3.0/ol.css|static';
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/homelibraree/openLayer_V5.3.0/ol.js';
            #-- bootstrap
            $GLOBALS['TL_CSS'][] = 'bundles/homelibraree/bootstrap_V4.3.1/css/bootstrap.min.css|static';
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/homelibraree/bootstrap_V4.3.1/js/bootstrap.bundle.min.js';
            #-- isotope
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/homelibraree/isotope_V3.0.6/isotope.pkgd.min.js';
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/homelibraree/isotope_V3.0.6/packery-mode.pkgd.min.js';
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/homelibraree/imagesLoaded_V4.1.4/imagesloaded.pkgd.min.js';

            $this->generateFrontend();
        }

    }

    /**
     * generate backend for module
     */
    private function generateBackend()
    {
        $this->strTemplate          = 'be_wildcard';
        $this->Template             = new \BackendTemplate($this->strTemplate);
        $this->Template->title      = $this->title;
        $this->Template->wildcard   = "### Proximity Map ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        #-- overwrite default templates
        if ($this->hm_template) {
            $this->Template = new \Contao\FrontendTemplate($this->hm_template);
        }

        #-- check if pin order is set
        if($GLOBALS['libraree']['pinOrder'] === null){
            $GLOBALS['libraree']['pinOrder'] = 'id DESC';
        }

        $table = $this->lib_table;

        if($table){
            $pins = $this->getPins($table);
            $this->Template->pins = $pins;
            $this->Template->center = GeoCoords::autoCenterMap($pins);
            $this->Template->categories = $this->getCategoriesFromTaxonomy($this->lib_tax_id);
        }
    }

    /**
     * get pins
     *
     * @param $table
     * @return array|null
     */
    private function getPins($table)
    {
        $strColumn = array(
            $table . '_pin.published = 1',
        );
        $options = array('order'=>$GLOBALS['libraree']['pinOrder']);

        return BasePinModel::findByTable($table, $strColumn, null, $options);
    }

    private function getCategoriesFromTaxonomy($pId)
    {
        $return = array();

        $model = TaxonomeeModel::findBy(array(
            TaxonomeeModel::getTable() . '.pid = ' . $pId
        ), null);

        if($model instanceof \Contao\Model\Collection){
            $result = $model->fetchAll();
            foreach ($result as $row){
                $return[$row['id']] = $row;
            }
        }

        uasort($return, function($a,$b){return strcmp($a['name'], $b['name']);});

        return $return;
    }
}