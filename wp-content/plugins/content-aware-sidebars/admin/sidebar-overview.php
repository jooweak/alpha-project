<?php
/**
 * @package Content Aware Sidebars
 * @author Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 * @copyright 2016 by Joachim Jensen
 */

if (!defined('CAS_App::PLUGIN_VERSION')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}

final class CAS_Sidebar_Overview {

	/**
	 * Column definitions
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Constructor
	 *
	 * @since 3.1
	 */
	public function __construct() {

		add_action('load-edit.php',
			array($this,'init_columns'));

		add_action('manage_'.CAS_App::TYPE_SIDEBAR.'_posts_custom_column',
			array($this,'admin_column_rows'),10,2);

		add_filter('request',
			array($this,'admin_column_orderby'));
		add_filter('manage_'.CAS_App::TYPE_SIDEBAR.'_posts_columns',
			array($this,'admin_column_headers'),99);
		add_filter('manage_edit-'.CAS_App::TYPE_SIDEBAR.'_sortable_columns',
			array($this,'admin_column_sortable_headers'));
		add_filter('post_row_actions',
			array($this,'sidebar_row_actions'),10,2);
	}


	/**
	 * Add admin column headers
	 *
	 * @since  3.1
	 * @param  array $columns 
	 * @return array          
	 */
	public function admin_column_headers($columns) {
		$new_columns = array();
		foreach ($this->columns as $id => $column) {
			$new_columns[$id] = isset($column['title']) ? $column['title'] : $columns[$id];
		}
		return $new_columns;
	}
		
	/**
	 * Make some columns sortable
	 *
	 * @since  3.1
	 * @param  array $columns 
	 * @return array
	 */
	public function admin_column_sortable_headers($columns) {
		foreach ($this->columns as $id => $column) {
			if($column['sortable']) {
				$columns[$id] = $id;
			}
		}
		return $columns;
	}
	
	/**
	 * Manage custom column sorting
	 *
	 * @since  3.1
	 * @param  array $vars 
	 * @return array 
	 */
	public function admin_column_orderby($vars) {
		$orderby = isset($vars['orderby']) ? $vars['orderby'] : '';
		if (isset($this->columns[$orderby]) && $this->columns[$orderby]['sortable']) {
			$vars = array_merge($vars, array(
				'meta_key' => WPCACore::PREFIX . $orderby,
				'orderby'  => 'meta_value'
			));
		}
		return $vars;
	}
	
	/**
	 * Render columns rows
	 *
	 * @since  3.1
	 * @param  string $column_name 
	 * @param  int $post_id
	 * @return void
	 */
	public function admin_column_rows($column_name, $post_id) {
		$method_name = 'column_'.$column_name;
		if(method_exists($this, $method_name)) {
			echo $this->$method_name($column_name, $post_id);
		}
	}

	/**
	 * Add admin rows actions
	 *
	 * @since  3.1
	 * @param  array   $actions
	 * @param  WP_Post $post
	 * @return array
	 */
	public function sidebar_row_actions($actions, $post) {
		if ($post->post_type == CAS_App::TYPE_SIDEBAR && $post->post_status != 'trash') {
			$link = admin_url('post.php?post='.$post->ID);

			//$new_actions['mng_widgets'] = '<a href="widgets.php" title="' . esc_attr__('Manage Widgets', 'content-aware-sidebars') . '">' . __('Manage Widgets', 'content-aware-sidebars') . '</a>';
			$new_actions['widget_revisions'] = '<a href="'.add_query_arg('action','cas-revisions',$link).'" title="' . esc_attr__('Widget Revisions', 'content-aware-sidebars') . '">' . __('Widget Revisions', 'content-aware-sidebars') . '</a>';
			//Append new actions just before trash action
			array_splice($actions, -1, 0, $new_actions);
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}

	/**
	 * Initiate column definitions
	 *
	 * @since  3.1
	 * @return void
	 */
	public function init_columns() {
		CAS_App::instance()->manager()->populate_metadata();
		$this->columns = array(
			'cb'        => array(
				'sortable' => false
			),
			'title'     => array(
				'sortable' => false
			),
			'handle'    => array(
				'title'    => _x('Handle','option', 'content-aware-sidebars'),
				'sortable' => true
			),
			'widgets'   => array(
				'title'    => __('Widgets'),
				'sortable' => false
			),
			'visibility' => array(
				'title'    => __('Visibility','content-aware-sidebars'),
				'sortable' => false
			),
			'date'      => array(
				'sortable' => false
			)
		);
	}

	/**
	 * Display handle column
	 *
	 * @since  3.1
	 * @param  string  $column_name
	 * @param  int     $post_id
	 * @return string
	 */
	protected function column_handle($column_name,$post_id) {
		$metadata = CAS_App::instance()->manager()->metadata()->get($column_name);
		
		$return = '';
		if($metadata) {
			$return = $metadata->get_list_data($post_id);
			if($metadata->get_data($post_id) != 2) {
				$host = CAS_App::instance()->manager()->metadata()->get('host')->get_list_data($post_id);
				$return .= ': ' . ($host ? $host : '<span style="color:red;">' . __('Please update Host Sidebar', 'content-aware-sidebars') . '</span>');
			
			}
			if($metadata->get_data($post_id) != 3) {
				$pos = CAS_App::instance()->manager()->metadata()->get('merge_pos')->get_data($post_id,true);
				$pos_icon = $pos ? 'up' : 'down';
				$pos_title = array(
					__('Add sidebar at the top during merge','content-aware-sidebars'),
					__('Add sidebar at the bottom during merge','content-aware-sidebars')
				);
				$return .= '<span title="'.$pos_title[$pos].'" class="dashicons dashicons-arrow-'.$pos_icon.'-alt"></span>';
			}
			
		}
		return $return;
	}

	/**
	 * Display visibility column
	 *
	 * @since  3.2
	 * @param  string  $column_name
	 * @param  int     $post_id
	 * @return string
	 */
	protected function column_visibility($column_name,$post_id) {
		$metadata = CAS_App::instance()->manager()->metadata()->get($column_name);
		if($metadata) {
			$data = $metadata->get_data($post_id,true,false);
			if($data) {
				$list = $metadata->get_input_list();
				foreach ($data as $k => $v) {
					if(isset($list[$v])) {
						$data[$k] = $list[$v];
					}
				}
				return implode(', ', $data);
			}
		}
		return __('All Users','content-aware-sidebars');
	}

	/**
	 * Display widgets column
	 *
	 * @since  3.1
	 * @param  string  $column_name
	 * @param  int     $post_id
	 * @return int
	 */
	protected function column_widgets($column_name,$post_id) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$count =  isset($sidebars_widgets[CAS_App::SIDEBAR_PREFIX . $post_id]) ? count($sidebars_widgets[CAS_App::SIDEBAR_PREFIX . $post_id]) : 0;
		return '<a href="'.admin_url('widgets.php').'" title="' . esc_attr__('Manage Widgets', 'content-aware-sidebars') . '">' .$count . '</a>';

	}

}

//eol