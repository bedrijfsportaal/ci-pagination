<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_Pagination Class
 *
 * This class allows you to create pagination
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Library
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-pagination
 */
class MY_Pagination extends CI_Pagination {

	protected $show_disabled            = TRUE;

	protected $use_ellipsis             = FALSE;
	protected $ellipsis_link            = '&hellip;';
	protected $ellipsis_inner           = 3;
	protected $ellipsis_outer           = 2;
	protected $ellipsis_tag_open        = '';
	protected $ellipsis_tag_close       = '&nbsp;';

	protected $use_page_count           = FALSE;
	protected $page_count               = 'Page %d of %d'; // Something like Page %d of %d
	protected $page_count_location      = 'before';  // 'before' or 'after'
	protected $page_count_open          = '';
	protected $page_count_close         = '&nbsp;';

	/**
	 * Constructor
	 *
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function __construct($params = array())
	{
		parent::__construct($params);

		if (count($params) > 0)
		{
			$this->initialize($params);
		}

		log_message('debug', "MY_Pagination Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}

		// Implode the anchor_class into a string
		if (is_array($this->anchor_class))
		{
			$this->anchor_class = implode(' ', $this->anchor_class);
		}

		// Make string attribute
		$pattern = '/^class[\s]?=/i';

		if ( ! preg_match($pattern, $this->anchor_class, $matches))
		{
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * Alias for generate()
	 *
	 * @param   array   $params
	 * @return	string
	 */
	public function create_links($params = array())
	{
		return $this->generate($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	string
	 */
	public function generate($params = array())
	{
		// Initialize params
		if ( ! empty($params))
		{
			$this->initialize($params);
		}

		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			log_message('debug', 'Unable to build pagination. Either total_rows or per_page has not been passed.');

			return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// If there only one. Nothing more to do.
		if ($num_pages == 1)
		{
			return '';
		}

		// Set the base page index for starting page number
		$base_page = ($this->use_page_numbers) ? 1 : 0;

		// Determine the current page number.
		$CI =& get_instance();

		// See if we are using a prefix or suffix on links
		if ($this->prefix != '' OR $this->suffix != '')
		{
			$this->cur_page = (int)str_replace(array($this->prefix, $this->suffix), '', $CI->uri->segment($this->uri_segment));
		}

		// Determine the current page number
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != $base_page)
			{
				$this->cur_page = (int)$CI->input->get($this->query_string_segment);
			}
		}

		else
		{
			$this->cur_page = (int)$CI->uri->segment($this->uri_segment);
		}

		// Set current page to 1 if it's not valid or if using page numbers instead of offset
		if ( ! is_numeric($this->cur_page) OR ($this->use_page_numbers AND $this->cur_page == 0))
		{
			$this->cur_page = $base_page;
		}

		$this->num_links = (int)$this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		// Is the page number beyond the result range? If so we show the last page
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}

		else
		{
			if ($this->cur_page > $this->total_rows)
			{
				$this->cur_page = ($num_pages - 1) * $this->per_page;
			}
		}

		$uri_page_number = $this->cur_page;

		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
		}

		// Is pagination being used over GET or POST?  If get, add a per_page query string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}

		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}

		// Let's get started
		$output = '';

		// Page X of Y (before)
		if ($this->use_page_count AND $this->page_count AND $this->page_count_location == 'before')
		{
			$output .= $this->_page_count($num_pages);
		}

		// Render the "First" link
		if ($this->first_link !== FALSE)
		{
			// We aren't on the first page
			if  ($this->cur_page != 1)
			{
				$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;

				$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
			}

			// We are on the first page and we are supposed to show the arrows even though it is disabled
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->first_tag_open.$this->first_link.$this->first_tag_close;
			}
		}

		// Render the "Previous" link
		if ($this->prev_link !== FALSE)
		{
			// We aren't on the first page
			if  ($this->cur_page != 1)
			{
				$i = ($this->use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this->per_page;

				if ($i == $base_page AND $this->first_url != '')
				{
					$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				}

				else
				{
					$i = ($i == $base_page) ? '' : $this->prefix.$i.$this->suffix;
					$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
				}
			}

			// We are on the first page and we are supposed to show the arrow even though it is disabled
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->prev_tag_open.$this->prev_link.$this->prev_tag_close;
			}
		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Pagination with ellipsis
			if ($this->use_ellipsis === TRUE)
			{
				$truncating = FALSE;

				for ($loop = 1; $loop <= $num_pages; $loop++)
				{
					$i = $this->_index($loop);

					// Digits
					if ($loop <= $this->ellipsis_outer OR
						($loop > $this->cur_page - $this->ellipsis_inner AND $loop < $this->cur_page + $this->ellipsis_inner) OR
						$loop > $num_pages - $this->ellipsis_outer)
					{

						$output .= $this->_item($i, $loop, $base_page);

						$truncating = FALSE;
					}

					// Ellipsis
					else
					{
						if ( ! $truncating)
						{
							$output .= $this->ellipsis_tag_open.$this->ellipsis_link.$this->ellipsis_tag_close;
						}

						$truncating = TRUE;
					}
				}
			}

			// Original pagination
			else
			{
				// Calculate the start and end numbers. These determine which number to start and end the digit links with
				$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
				$end = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

				// Write the digit links
				for ($loop = $start - 1; $loop <= $end; $loop++)
				{
					$i = $this->_index($loop);

					$output .= $this->_item($i, $loop, $base_page);
				}
			}
		}

		// Render the "Next" link
		if ($this->next_link !== FALSE)
		{
			// We aren't on the last page
			if ($this->cur_page < $num_pages)
			{
				$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;

				$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
			}

			// We are on the last page and we are supposed to show the arrow even though it is disabled
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->next_tag_open.$this->next_link.$this->next_tag_close;
			}
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE)
		{
			// We aren't on the last page
			if ($this->cur_page < $num_pages)
			{
				$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;

				$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
			}

			// We are on the last page and we are supposed to show the arrows even though it is disabled
			elseif ($this->show_disabled === TRUE)
			{
				$output .= $this->last_tag_open.$this->last_link.$this->last_tag_close;
			}
		}

		// Page X of Y (after)
		if ($this->use_page_count AND $this->page_count AND $this->page_count_location == 'after')
		{
			$output .= $this->_page_count($num_pages);
		}

		// Sometimes we can end up with a double slash in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Page count display
	 *
	 * @param   int     $num_pages
	 * @return  string
	 */
	private function _page_count($num_pages = 0)
	{
		$x = number_format($this->cur_page);
		$y = number_format($num_pages);

		$count = sprintf($this->page_count, $x, $y);

		return $this->page_count_open.$count.$this->page_count_close;
	}

	// --------------------------------------------------------------------

	/**
	 * Find page index
	 *
	 * @param   int $index
	 * @return  int
	 */
	private function _index($index = 0)
	{
		return ($this->use_page_numbers) ? $index : ($index * $this->per_page) - $this->per_page;
	}

	// --------------------------------------------------------------------

	/**
	 * Build page digit item
	 *
	 * @param   int     $i
	 * @param   int     $loop
	 * @param   string  $base_page
	 * @return  string
	 */
	private function _item($i = 0, $loop = 0, $base_page = '')
	{
		$output = '';

		if ($i >= $base_page)
		{
			// Current page
			if ($this->cur_page == $loop)
			{
				$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
			}

			// Not the current page
			else
			{
				$n = ($i == $base_page) ? '' : $i;

				if ($n == '' && $this->first_url != '')
				{
					$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
				}

				else
				{
					$n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;

					$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
				}
			}
		}

		return $output;
	}

}
// END MY_Pagination class

/* End of file MY_Pagination.php */
/* Location: ./application/libraries/MY_Pagination.php */