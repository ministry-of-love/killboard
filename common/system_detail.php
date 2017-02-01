<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */
/*
 * @package EDK
 */
class pSystemDetail extends pageAssembly
{
	/** @var Page */
	public $page = null;
	/** @var integer */
	public $sys_id = 0;

	/** @var System */
	protected $system;
	/** @var string The selected view. */
	protected $view = null;
	/** @var array The list of views and their callbacks. */
	protected $viewList = array();
	/** @var array The list of menu options to display. */
	protected $menuOptions = array();

	/** @var KillSummaryTable */
	private $kill_summary = null;
        
    /** @var \TopList_Locations location top list for this solar system */
    private $LocationList = null;

	function __construct()
	{
		parent::__construct();
		$this->queue("start");
		$this->queue("map");
		$this->queue("statSetup");
		$this->queue("summaryTable");
		$this->queue("killList");
	}

	/**

	 * Start constructing the page.

	 * Prepare all the shared variables such as dates and check alliance ID.
	 *
	 */
	function start()
	{
		$this->sys_id = (int)edkURI::getArg('sys_id', 1, true);
		$this->view = preg_replace('/[^a-zA-Z0-9_-]/', '',
						edkURI::getArg('view', 2, true));

		global $smarty;
		$this->smarty = $smarty;
		$this->viewList = array();
		$this->menuOptions = array();

		$this->page = new Page();
		$this->page->addHeader('<meta name="robots" content="noindex, nofollow" />');

		if (!$this->sys_id) {
			echo 'no valid id supplied<br/>';
			exit;
		}

		$this->page->addHeader("<link rel='canonical' href='".
				edkURI::build($this->args)."' />");

		$this->system = new SolarSystem($this->sys_id);
		$this->menuOptions = array();
		$this->page->setTitle('System details - '.$this->system->getName());
		$this->smarty->assign('sys_id', $this->sys_id);
	}

	function map()
	{
		return $this->smarty->fetch(get_tpl("system_detail_map"));
	}

	/**
	 *  Set up the stats used by the stats and summary table functions
	 */
	function statSetup()
	{
		$this->kill_summary = new KillSummaryTable();
		$this->kill_summary->setSystem($this->sys_id);
		if (config::get('kill_classified')) {
			$this->kill_summary->setEndDate(
					gmdate('Y-m-d H:i:s', strtotime('now - '
					.(config::get('kill_classified')).' hours')));
		}
		involved::load($this->kill_summary, 'kill');
		$this->kill_summary->generate();
		return "";
	}

	/**
	 *  Build the summary table showing all kills and losses for this corporation.
	 */
	function summaryTable()
	{
		if ($this->view != '' && $this->view != 'kills'
				&& $this->view != 'losses') {
			return '';
		}
		return $this->kill_summary->generate();
	}

	/**
	 *  Build the killlists that are needed for the options selected.
	 */
	function killList()
	{
		global $smarty;
		if (isset($this->viewList[$this->view])) {
			return call_user_func_array(
					$this->viewList[$this->view], array(&$this));
		}
		$scl_id = (int)edkURI::getArg('scl_id');

		$klist = new KillList();
		$klist->setOrdered(true);
		if ($this->view == 'losses') {
			involved::load($klist, 'loss');
		} else {
			involved::load($klist, 'kill');
		}
		$klist->addSystem($this->system);
		if (config::get('kill_classified')) {
			$klist->setEndDate(gmdate('Y-m-d H:i:s', strtotime('now - '
					.(config::get('kill_classified')).' hours')));
		}
		if ($scl_id) {
			$klist->addVictimShipClass(intval($scl_id));
		} else {
			$klist->setPodsNoobShips(config::get('podnoobs'));
		}

		if ($this->view == 'recent' || !$this->view) {
			$klist->setLimit(20);
			$smarty->assign('klheader', config::get('killcount').' most recent kills');
		} else if ($this->view == 'losses') {
			$smarty->assign('klheader', 'All losses');
		} else {
			$smarty->assign('klheader', 'All kills');
		}

		$klist->setPageSplit(config::get('killcount'));

		$pagesplitter = new PageSplitter($klist->getCount(), config::get('killcount'));

		$table = new KillListTable($klist);
		$smarty->assign('klsplit', $pagesplitter->generate());
		$smarty->assign('kltable', $table->generate());
		$html = $smarty->fetch(get_tpl('system_detail'));

		return $html;
	}

	/**
	 *  Reset the assembly object to prepare for creating the context.
	 */
	function context()
	{
		parent::__construct();
		$this->queue("menuSetup");
		$this->queue("menu");
                $this->queue("topList");
                $this->queue("metaTags");
	}

	/**
	 * Set up the menu.
	 *
	 *  Prepare all the base menu options.
	 */
	function menuSetup()
	{
		$args = array();
		$args[] = array('a', 'system_detail', true);
		$args[] = array('sys_id', $this->sys_id, true);
		$this->addMenuItem("caption", "Navigation");
		$this->addMenuItem("link", "All kills",
				edkURI::build($args, array('view', 'kills', true)));
		$this->addMenuItem("link", "All losses",
				edkURI::build($args, array('view', 'losses', true)));
		$this->addMenuItem("link", "Recent Activity",
				edkURI::build($args, array('view', 'recent', true)));
		return "";
	}

	/**
	 * Build the menu.
	 *
	 *  Add all preset options to the menu.
	 */
	function menu()
	{
		$menubox = new box("Menu");
		$menubox->setIcon("menu-item.gif");
		foreach ($this->menuOptions as $options) {
			if (isset($options[2]))
					$menubox->addOption($options[0], $options[1], $options[2]);
			else $menubox->addOption($options[0], $options[1]);
		}
		return $menubox->generate();
	}

	/**
	 * Add an item to the menu in standard box format.
	 *
	 *  Only links need all 3 attributes
	 * @param string $type Types can be caption, img, link, points.
	 * @param string $name The name to display.
	 * @param string $url Only needed for URLs.
	 */
	function addMenuItem($type, $name, $url = '')
	{
		$this->menuOptions[] = array($type, $name, $url);
	}
    
    /**
    * Removes the menu item with the given name
    * 
    * @param string $name the name of the menu item to remove
    */
   function removeMenuItem($name)
   {
       foreach((array)$this->menuOptions AS $menuItem)
       {
           if(count($menuItem) > 1 && $menuItem[1] == $name)
           {
               unset($this->menuOptions[key($this->menuOptions)]);
           }
       }
   }
        
        /**
	 *
	 * @return string HTML string for toplists
	 */
	function topList()
	{
		// Display the top location lists.
        $this->LocationList = new TopList_Locations();
        if ($this->view == 'losses') {
            involved::load($this->LocationList, 'loss');
		} else {
			involved::load($this->LocationList, 'kill');
		}
		$this->LocationList->addSystem($this->system);
		if (config::get('kill_classified')) {
			$this->LocationList->setEndDate(gmdate('Y-m-d H:i:s', strtotime('now - '
					.(config::get('kill_classified')).' hours')));
		}
                
        $scl_id = (int)edkURI::getArg('scl_id', 2);
		if ($scl_id) {
			$this->LocationList->addVictimShipClass(intval($scl_id));
        }
        $this->LocationList->generate();
        if($this->view == 'losses')
        {
            $LocationListBox = new AwardBoxLocation($this->LocationList, "Top locations", "losses", "losses", "cross");
        }

        else
        {
            $LocationListBox = new AwardBoxLocation($this->LocationList, "Top locations", "kills", "kills", "cross");
        }

        $html = $LocationListBox->generate();

		return $html;
	}
        
        /** 
         * adds meta tags for Twitter Summary Card and OpenGraph tags
         * to the HTML header
         */
        function metaTags()
        {
            // meta tag: title
            $metaTagTitle = $this->system->getName() . " | System Details";
            $this->page->addHeader('<meta name="og:title" content="'.$metaTagTitle.'">');
            $this->page->addHeader('<meta name="twitter:title" content="'.$metaTagTitle.'">');
            
            // build description
            $metaTagDescription = "In ". $this->system->getName() . " " . $this->kill_summary->getTotalKills() . " ships have been killed and " . $this->kill_summary->getTotalLosses() . " ships have been lost at " . Config::get("cfg_kbtitle").".";
            if($this->LocationList)
            {
                $this->LocationList->rewind();
                $topLocation = $this->LocationList->getRow();
                if($topLocation)
                {
                    $metaTagDescription .= " The most dangerous location is " . $topLocation['itemName'] . " (" . $topLocation['cnt'] . " kills).";
                }
            }            
            $this->page->addHeader('<meta name="description" content="'.$metaTagDescription.'">');
            $this->page->addHeader('<meta name="og:description" content="'.$metaTagDescription.'">');
                
            // meta tag: image
            $this->page->addHeader('<meta name="og:image" content="'.imageURL::getURL('Type', 3802, 64).'">');
            $this->page->addHeader('<meta name="twitter:image" content="'.imageURL::getURL('Type', 3802, 64).'">');

            $this->page->addHeader('<meta name="og:site_name" content="EDK - '.config::get('cfg_kbtitle').'">');
            
            // meta tag: URL
            $this->page->addHeader('<meta name="og:url" content="'.edkURI::build(array('sys_id', $this->sys_id, true)).'">');
            // meta tag: Twitter summary
            $this->page->addHeader('<meta name="twitter:card" content="summary">');
        }

	/**

	 * Add a type of view to the options.

	 *
	 * @param string $view The name of the view to recognise.
	 * @param mixed $callback The method to call when this view is used.
	 */
	function addView($view, $callback)
	{
		$this->viewList[$view] = $callback;
	}

	/**
	 * Return the set view.
	 * @return string
	 */
	function getView()
	{
		return $this->view;
	}
        
    /**
     * Return the system
     * @return SolarSystem
     */
    function getSystem()
    {
        return $this->system;
    }
    
    function getKillSummary() 
    {
        return $this->kill_summary;
    }

    function getLocationTopList() 
    {
        return $this->LocationList;
    }
}
$systemDetail = new pSystemDetail();
event::call("systemdetail_assembling", $systemDetail);
$html = $systemDetail->assemble();
$systemDetail->page->setContent($html);

$systemDetail->context();
event::call("systemdetail_context_assembling", $systemDetail);
$context = $systemDetail->assemble();
$systemDetail->page->addContext($context);

$systemDetail->page->generate();