<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 02.01.2018
 * Time: 11:11
 */

namespace Home\LibrareeBundle\Resources\contao\modules;
use Home\LibrareeBundle\Resources\contao\models\BaseClosuresModel;
use Home\LibrareeBundle\Resources\contao\models\BasePinModel;
use Home\LibrareeBundle\Resources\contao\models\BasePortfolioModel;


class NavigationModule extends \Contao\Module
{
    /**
     * @var string
     */
    protected $strTemplate = 'mod_navigation';
    protected $strTemplateNav = 'nav_default';

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
            $this->generateBackend();
        } else {
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
        $this->Template->wildcard   = "Navigation aus Libraree";
    }

    private function generateFrontend()
    {
        if (!isset($_GET['item']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Input::setGet('item', \Input::get('auto_item'));
        }

        $this->alias = $_GET['auto_item'];

        #-- overwrite default templates
        if ($this->lib_nav_template_mod) {
            $this->strTemplate = $this->lib_nav_template_mod;
        }

        if ($this->lib_nav_template_nav) {
            $this->strTemplateNav = $this->lib_nav_template_nav;
        }

        #-- get portfolio data
        $portfolios = $this->getPortfolios();

        #-- get Pins for Portfolios
        if($this->lib_nav_pins){
            $portfolios = $this->addPins($portfolios);
        }

        #-- set portfolio to 0 if not defined
        if($this->lib_nav_portfolio === ''){
            $this->lib_nav_portfolio = 0;
        }

        #-- build the tree from the portfolio elements
        $tree = $this->buildTree($portfolios, $this->lib_nav_portfolio, 1);

        #-- get pages
        if ($this->pages) {
            $tree = $this->addPages($tree);
        }

        #-- override Module Template
        $this->Template = new \FrontendTemplate();
        $this->Template->setName($this->strTemplate);
        $this->Template->items = $this->parseTemplate($tree);
    }

    /**
     * get the portfolio data
     * @return array
     */
    private function getPortfolios() {
        $portfolios = array();

        if($this->lib_nav_portfolio){
            #-- get id of all children from portfolio
            $children = BaseClosuresModel::findChildren($this->lib_nav_portfolio, $this->lib_nav_depth, $this->lib_nav_table . '_closures');
            $portfolios = BasePortfolioModel::findPortfoliosIn($this->lib_nav_table, $children);
        }else{
            #-- get all portfolio in table
            $portfolios = BasePortfolioModel::findAllPortfolios($this->lib_nav_table . '_portfolio');
        }

        #-- get the alias as link from the parent portfolio and remove unpublished portfolios
        foreach($portfolios as $key=>$item){
            if($item['published'] != 1 && !BE_USER_LOGGED_IN){
                unset($portfolios[$key]);
            }else{
                if($item['id'] == $this->lib_nav_portfolio && !$this->lib_nav_href){
                    $this->lib_nav_href = $item['alias'];
                    //break;
                }
            }
        }

        return $portfolios;
    }

    /**
     * add pins to the portfolio nav elements
     * @param $portfolios
     * @return array
     */
    private function addPins($portfolios) {
        if ($portfolios && is_array($portfolios) && count($portfolios) > 0) {
            foreach ($portfolios as $key => $item) {
                $options = array(
                    $this->lib_nav_table . '_pin.published = 1',
                    $this->lib_nav_table . '_pin.pid = ' . $item['id'],
                );

                $pins = BasePinModel::findByTable($this->lib_nav_table, $options);

                #-- set by nav template required values
                if (is_array($pins) && count($pins) > 0) {
                    foreach ($pins as $k => $pin) {
                        $pins[$k]['link'] = $pin['name'];
                        $pins[$k]['href'] = $this->lib_nav_href . '/' . $pin['alias'] . '.html';
                        if ($pin['alias'] == $this->alias) {
                            $pins[$k]['isActive'] = true;
                        }
                    }
                }

                $portfolios[$key]['children'] = $pins;
            }
        }

        return $portfolios;
    }

    private function addPages($portfolios) {
        if ($this->pages) {
            $objPages = \PageModel::findPublishedRegularWithoutGuestsByIds(deserialize($this->pages));

            #-- der folgende Code stammt von Contao/ModuleCustomnav
            if ($objPages)
            {
                $arrPages = array();

                // Sort the array keys according to the given order
                if ($this->orderPages != '')
                {
                    $tmp = \StringUtil::deserialize($this->orderPages);

                    if (!empty($tmp) && \is_array($tmp))
                    {
                        $arrPages = array_map(function () {}, array_flip($tmp));
                    }
                }

                // Add the items to the pre-sorted array
                while ($objPages->next())
                {
                    $arrPages[$objPages->id] = $objPages->current();
                }

                $arrPages = array_values(array_filter($arrPages));

                #-- jetzt müssen die Daten noch an das $portfolio Array angepasst werden und hinzugefügt werden
                foreach ($arrPages as $key=>$value) {
                    $portfolios[] = array(
                        'id'    => $value->id,
                        'title' => $value->title,
                        'link' => $value->title,
                        'href'  => $value->alias . '.html'
                    );
                }
            }
        }
        return $portfolios;
    }

    /**
     * returns a parsed nav template part
     * @param $item - the items to parse
     * @param $level - the actual nav leven
     */
    private function parseTemplate($items, $level = 1) {
        $template = new \FrontendTemplate();
        $template->setName($this->strTemplateNav);
        $template->items = $items;
        $template->level = 'level_' . $level;
        return $template->parse();
    }

    /**
     * builds a multidimensional array tree structure from a flat array by id and pid
     * @param $arr
     * @param null $pid
     * @param $level
     * @return array
     */
    private function buildTree( $arr, $pid = null, $level) {
        $op = array();
        $trailIds = array();

        if($this->alias){
            $active = BasePortfolioModel::findByTable($this->lib_nav_table, [$this->lib_nav_table . '_portfolio.alias = ?'], $this->alias);
            if($active && is_array($active) && count($active) > 0){
                $pids = BasePortfolioModel::findParents($active[0]['id'], $this->lib_nav_table . '_closures');
                if(is_array($pids) && count($pids) > 0){
                    foreach ($pids as $row){
                        $trailIds[] = $row['ancestor_id'];
                    }
                }
            }
        }

        foreach( $arr as $item ) {
            if( $item['pid'] == $pid ) {
                #-- break if level is higher then depth and depth is not '0'
                if($level > $this->lib_nav_depth && $this->lib_nav_depth !== '0') break;

                $op[$item['id']] = $item;
                #-- set by nav template required values
                $op[$item['id']]['link'] = $item['title'];
                $op[$item['id']]['href'] = $this->lib_nav_href . '/' . $item['alias'] . '.html';
                if($item['alias'] == $this->alias){
                    $op[$item['id']]['isActive'] = true;
                }

                if(count($trailIds) > 0){
                    foreach ($trailIds as $trailId){
                        if($item['id'] == $trailId && $item['alias'] != $this->alias){
                            $op[$item['id']]['class'] .= ' trail';
                        }
                    }
                }

                #-- recursive to get all child elements
                $level++;
                $children =  $this->buildTree( $arr, $item['id'], $level);
                if( $children || $op[$item['id']]['children']) {
                    #-- merge pins and child portfolios
                    if($op[$item['id']]['children']){
                        if($children['id']){
                            $children = array_merge(array($children), $op[$item['id']]['children']);
                        }else{
                            $children = array_merge($children, $op[$item['id']]['children']);
                        }
                    }
                    $op[$item['id']]['children'] = $children;
                    $op[$item['id']]['class'] .= ' submenu';
                    #-- generate sub-items template
                    $op[$item['id']]['subitems'] = $this->parseTemplate($children, $level);
                }
                $level--;
            }
        }
        return $op;
    }

}