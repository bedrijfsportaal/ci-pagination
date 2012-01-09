<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Pagination
| -------------------------------------------------------------------------
| This file lets you set define default values for pagination.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/pagination.html
|
*/

// Codeigniter specific
$config['per_page'] = 25;
$config['use_page_numbers'] = TRUE;

$config['full_tag_open'] = '<div class="pagination">';
$config['full_tag_close'] = '</div>';

$config['first_link'] = '&laquo; First';
$config['first_tag_open'] = '<span class="pagination-first">';
$config['first_tag_close'] = '</span>';

$config['prev_link	'] = '&#8249; Prev';
$config['prev_tag_open'] = '<span class="pagination-prev">';
$config['prev_tag_close'] = '</span>';

$config['next_link	'] = 'Next &#8250;';
$config['next_tag_open'] = '<span class="pagination-next">';
$config['next_tag_close'] = '</span>';

$config['last_link'] = 'Last &raquo;';
$config['last_tag_open'] = '<span class="pagination-last">';
$config['last_tag_close'] = '</span>';

$config['cur_tag_open'] = '<span class="pagination-item pagination-current">';
$config['cur_tag_close'] = '</span>';

$config['num_tag_open'] = '<span class="pagination-item">';
$config['num_tag_close'] = '</span>';

// MY_Pagination specific
$config['show_disabled'] = TRUE;

$config['use_ellipsis'] = TRUE;
$config['ellipsis_link'] = '&hellip;';
$config['ellipsis_inner'] = 3;
$config['ellipsis_outer'] = 2;
$config['ellipsis_tag_open'] = '<span class="pagination-ellipsis">';
$config['ellipsis_tag_close'] = '</span>';

$config['use_page_count'] = TRUE;
$config['page_count'] = 'Page %d of %d';
$config['page_count_location'] = 'before';  // 'before' or 'after'
$config['page_count_open'] = '<span class="pagination-page-count">';
$config['page_count_close'] = '</span>';

/* End of file pagination.php */
/* Location: ./application/config/pagination.php */