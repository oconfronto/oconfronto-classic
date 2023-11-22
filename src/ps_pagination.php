<?php
/**
 * PHPSense Pagination Class
 *
 * PHP tutorials and scripts
 *
 * @package		PHPSense
 * @author		Jatinder Singh Thind
 * @copyright	Copyright (c) 2006, Jatinder Singh Thind
 * @link		http://www.phpsense.com
 */

// ------------------------------------------------------------------------


class PS_Pagination
{
    /**
  * @var string
  */
    public $php_self;
    /**
  * @var int
  */
    public $rows_per_page = 10; //Number of records to display per page
    public $total_rows = 0; //Total number of rows returned by the query
    public $links_per_page = 5;
    public $debug = false;
    /**
  * @var int
  */
    public $page = 1;
    public $max_pages = 0;
    public $offset = 0;

    /**
     * Constructor
     *
     * @param string $sql SQL query to paginate. Example : SELECT * FROM users
     * @param integer $rows_per_page Number of records to display per page. Defaults to 10
     * @param integer $links_per_page Number of links to display per page. Defaults to 5
     * @param string $append Parameters to be appended to pagination links
     */

    public function __construct(public $sql, $rows_per_page = 10, $links_per_page = 5, public $append = "")
    {
        $this->rows_per_page = (int)$rows_per_page;
        $this->links_per_page = (int) $links_per_page > 0 ? (int)$links_per_page : 5;
        $this->php_self = htmlspecialchars((string) $_SERVER['PHP_SELF']);
        if (isset($_GET['page'])) {
            $this->page = (int) $_GET['page'];
        }
    }

    /**
     * Executes the SQL query and initializes internal variables
     *
     * @access public
     * @return resource
     */
    public function paginate()
    {
        //Find total number of rows
        $all_rs = @mysql_query($this->sql);
        if (!$all_rs) {
            if ($this->debug) {
                echo "SQL query failed. Check your query.<br /><br />Error Returned: " . mysql_error();
            }
            return false;
        }
        $this->total_rows = mysql_num_rows($all_rs);
        @mysql_close($all_rs);

        //Return FALSE if no rows found
        if ($this->total_rows == 0) {
            if ($this->debug) {
                echo "Sem resultados para exibir.";
            }
            return false;
        }

        //Max number of pages
        $this->max_pages = ceil($this->total_rows / $this->rows_per_page);
        if ($this->links_per_page > $this->max_pages) {
            $this->links_per_page = $this->max_pages;
        }

        //Check the page value just in case someone is trying to input an aribitrary value
        if ($this->page > $this->max_pages || $this->page <= 0) {
            $this->page = 1;
        }

        //Calculate Offset
        $this->offset = $this->rows_per_page * ($this->page - 1);

        //Fetch the required result set
        $rs = @mysql_query($this->sql . " LIMIT {$this->offset}, {$this->rows_per_page}");
        if (!$rs) {
            if ($this->debug) {
                echo "Pagination query failed. Check your query.<br /><br />Error Returned: " . mysql_error();
            }
            return false;
        }
        return $rs;
    }

    /**
  * Display the link to the first page
  *
  * @access public
  * @param string $tag Text string to be displayed as the link. Defaults to 'First'
  */
    public function renderFirst(string $tag = 'Primeira'): bool|string
    {
        if ($this->total_rows == 0) {
            return false;
        }

        if ($this->page == 1) {
            return "$tag ";
        }
        return '<a href="' . $this->php_self . '?page=1&' . $this->append . '">' . $tag . '</a> ';
    }

    /**
  * Display the link to the last page
  *
  * @access public
  * @param string $tag Text string to be displayed as the link. Defaults to 'Last'
  */
    public function renderLast(string $tag = 'Ùltima'): bool|string
    {
        if ($this->total_rows == 0) {
            return false;
        }

        if ($this->page == $this->max_pages) {
            return $tag;
        }
        return ' <a href="' . $this->php_self . '?page=' . $this->max_pages . '&' . $this->append . '">' . $tag . '</a>';
    }

    /**
  * Display the next link
  *
  * @access public
  * @param string $tag Text string to be displayed as the link. Defaults to '>>'
  */
    public function renderNext(string $tag = '&gt;&gt;'): bool|string
    {
        if ($this->total_rows == 0) {
            return false;
        }

        if ($this->page < $this->max_pages) {
            return '<a href="' . $this->php_self . '?page=' . ($this->page + 1) . '&' . $this->append . '">' . $tag . '</a>';
        }
        return $tag;
    }

    /**
  * Display the previous link
  *
  * @access public
  * @param string $tag Text string to be displayed as the link. Defaults to '<<'
  */
    public function renderPrev(string $tag = '&lt;&lt;'): bool|string
    {
        if ($this->total_rows == 0) {
            return false;
        }

        if ($this->page > 1) {
            return ' <a href="' . $this->php_self . '?page=' . ($this->page - 1) . '&' . $this->append . '">' . $tag . '</a>';
        }
        return " $tag";
    }

    /**
  * Display the page links
  *
  * @access public
  */
    public function renderNav(string $prefix = '<span class="page_link">', string $suffix = '</span>'): bool|string
    {
        if ($this->total_rows == 0) {
            return false;
        }

        $batch = ceil($this->page / $this->links_per_page);
        $end = $batch * $this->links_per_page;
        if ($end == $this->page) {
            //$end = $end + $this->links_per_page - 1;
            //$end = $end + ceil($this->links_per_page/2);
        }
        if ($end > $this->max_pages) {
            $end = $this->max_pages;
        }
        $start = $end - $this->links_per_page + 1;
        $links = '';

        for($i = $start; $i <= $end; $i++) {
            if ($i == $this->page) {
                $links .= $prefix . " $i " . $suffix;
            } else {
                $links .= ' ' . $prefix . '<a href="' . $this->php_self . '?page=' . $i . '&' . $this->append . '">' . $i . '</a>' . $suffix . ' ';
            }
        }

        return $links;
    }

    /**
  * Display full pagination navigation
  *
  * @access public
  */
    public function renderFullNav(): string
    {
        return $this->renderFirst() . '&nbsp;' . $this->renderPrev() . '&nbsp;' . $this->renderNav() . '&nbsp;' . $this->renderNext() . '&nbsp;' . $this->renderLast();
    }

    /**
  * Set debug mode
  *
  * @access public
  * @param bool $debug Set to TRUE to enable debug messages
  */
    public function setDebug($debug): void
    {
        $this->debug = $debug;
    }
}
