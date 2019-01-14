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

        $depth = $this->lib_nav_depth;
        $withPins = $this->lib_nav_pins;
        $portfolio = $this->lib_nav_portfolio;
        $table = $this->lib_nav_table;
        $templateMod = $this->lib_nav_template_mod ? $this->lib_nav_template_mod : $this->strTemplate;
        $templateNav = $this->lib_nav_template_nav ? $this->lib_nav_template_nav : $this->strTemplateNav;
        $href = $this->lib_nav_href;
        $alias = $_GET['auto_item'];

        if($portfolio){
            #-- get id of all children from portfolio
            $children = BaseClosuresModel::findChildren($portfolio, $depth, $table . '_closures');
            $portfolios = BasePortfolioModel::findPortfoliosIn($table, $children);
        }else{
            #-- get all portfolio in table
            $portfolios = BasePortfolioModel::findAllPortfolios($table . '_portfolio');
        }

        #-- get the alias as link from the parent portfolio and remove unpublished portfolios
        foreach($portfolios as $key=>$item){
            if($item['published'] != 1 && !BE_USER_LOGGED_IN){
                unset($portfolios[$key]);
            }else{
                if($item['id'] == $portfolio && !$href){
                    $href = $item['alias'];
                    //break;
                }
            }
        }

        #-- get Pins for Portfolios
        if($withPins){
            foreach($portfolios as $key => $item){
                $options = array(
                    $table . '_pin.published = 1',
                    $table . '_pin.pid = '.$item['id'],
                );

                $pins = BasePinModel::findByTable($table, $options);
                #-- set by nav template required values
                if(is_array($pins) && count($pins) > 0){
                    foreach ($pins as $k => $pin){
                        $pins[$k]['link'] = $pin['name'];
                        $pins[$k]['href'] = $href . '/' . $pin['alias'] . '.html';
                        if($pin['alias'] == $alias){
                            $pins[$k]['isActive'] = true;
                        }
                    }
                }

                $portfolios[$key]['children'] = $pins;
            }
        }

        #-- set portfolio to 0 if not defined
        if($portfolio === ''){
            $portfolio = 0;
        }

        #-- build the tree from the portfolio elements
        $tree = $this->buildTree($portfolios, $portfolio, $templateNav, $href, 1, $depth, $alias, $table);

        $level_1 = new \FrontendTemplate();
        $level_1->setName($templateNav);
        $level_1->items = $tree;
        $level_1->level = 'level_' . 1;

        #-- override Module Template
        $this->Template = new \FrontendTemplate();
        $this->Template->setName($templateMod);

        $this->Template->items = $level_1->parse();
    }

    /**
     * builds a multidimensional array tree structure from a flat array by id and pid
     * @param $arr
     * @param null $pid
     * @param $template
     * @param $href
     * @param $level
     * @param $depth
     * @param $alias
     * @return array
     */
    private function buildTree( $arr, $pid = null, $template, $href, $level, $depth, $alias, $table) {
        $op = array();
        $trailIds = array();

        if($alias){
            $active = BasePortfolioModel::findByTable($table, array($table . '_portfolio.alias = "' . $alias . '"'));
            if($active && is_array($active) && count($active) > 0){
                $pids = BasePortfolioModel::findParents($active[0]['id'], $table . '_closures');
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
                if($level > $depth && $depth !== '0') break;

                $op[$item['id']] = $item;
                #-- set by nav template required values
                $op[$item['id']]['link'] = $item['title'];
                $op[$item['id']]['href'] = $href . '/' . $item['alias'] . '.html';
                if($item['alias'] == $alias){
                    $op[$item['id']]['isActive'] = true;
                }

                if(count($trailIds) > 0){
                    foreach ($trailIds as $trailId){
                        if($item['id'] == $trailId){
                            $op[$item['id']]['class'] .= ' trail';
                        }
                    }
                }

                #-- recursive to get all child elements
                $level++;
                $children =  $this->buildTree( $arr, $item['id'], $template, $href, $level, $depth, $alias, $table);
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
                    $op[$item['id']]['class'] = 'submenu';
                    #-- generate sub-items template
                    $subItems = new \FrontendTemplate();
                    $subItems->setName($template);
                    $subItems->items = $children;
                    $subItems->level = 'level_' . $level;
                    $op[$item['id']]['subitems'] = $subItems->parse();

                }
                $level--;
            }
        }
        return $op;
    }

}