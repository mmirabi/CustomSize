<?php

	$title = "Shapes";
	$prefix = 'shape_';

	if (isset($_GET['task']) && $_GET['task'] == 'import-sample') {
		include($magic->cfg->root_path.'raws'.DS.'shapes.php');
		return;
	}

	// Action Form
	if (isset($_POST['action_submit']) && !empty($_POST['action_submit'])) {

		$data_action = isset($_POST['action']) ? $_POST['action'] : '';
		$val = isset($_POST['id_action']) ? $_POST['id_action'] : '';
		$val = explode(',', $val);
		
		$magic_admin->check_caps('shapes');
		
		foreach ($val as $value) {

			$dt = $magic_admin->get_row_id($value, 'shapes');
			switch ($data_action) {

				case 'active':
					$data = array(
						'active' => 1
					);
					$dt = $magic_admin->edit_row( $value, $data, 'shapes' );
					break;
				case 'deactive':
					$data = array(
						'active' => 0
					);
					$dt = $magic_admin->edit_row( $value, $data, 'shapes' );
					break;
				case 'delete':
					$magic_admin->delete_row($value, 'shapes');
					break;
				default:
					break;

			}

		}

	}

	// Search Form
	$data_search = '';
	if (isset($_POST['search_shape']) && !empty($_POST['search_shape'])) {

		$data_search = isset($_POST['search']) ? trim($_POST['search']) : '';

		if (empty($data_search)) {
			$errors = 'Please Insert Key Word';
			$_SESSION[$prefix.'data_search'] = '';
		} else {
			$_SESSION[$prefix.'data_search'] = 	$data_search;
		}

	}

	if (!empty($_SESSION[$prefix.'data_search'])) {
		$data_search = '%'.$_SESSION[$prefix.'data_search'].'%';
	}

	// Pagination
	$per_page = 5;
	if(isset($_SESSION[$prefix.'per_page']))
		$per_page = $_SESSION[$prefix.'per_page'];

	if (isset($_POST['per_page'])) {

		$data = isset($_POST['per_page']) ? $_POST['per_page'] : '';

		if ($data != 'none') {
			$_SESSION[$prefix.'per_page'] = $data;
			$per_page = $_SESSION[$prefix.'per_page'];
		} else {
			$_SESSION[$prefix.'per_page'] = 20;
			$per_page = $_SESSION[$prefix.'per_page'];
		}

	}

    // Sort Form
	if (!empty($_POST['sort'])) {

		$dt_sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$_SESSION[$prefix.'dt_order'] = $dt_sort;

		switch ($dt_sort) {

			case 'name_asc':
				$_SESSION[$prefix.'orderby'] = 'name';
				$_SESSION[$prefix.'ordering'] = 'asc';
				break;
			case 'name_desc':
				$_SESSION[$prefix.'orderby'] = 'name';
				$_SESSION[$prefix.'ordering'] = 'desc';
				break;
			default:
				break;

		}

	}

	$orderby  = (isset($_SESSION[$prefix.'orderby']) && !empty($_SESSION[$prefix.'orderby'])) ? $_SESSION[$prefix.'orderby'] : 'name';
	$ordering = (isset($_SESSION[$prefix.'ordering']) && !empty($_SESSION[$prefix.'ordering'])) ? $_SESSION[$prefix.'ordering'] : 'asc';
	$dt_order = isset($_SESSION[$prefix.'dt_order']) ? $_SESSION[$prefix.'dt_order'] : 'name_asc';

	// Get row pagination
    $current_page = isset($_GET['tpage']) ? $_GET['tpage'] : 1;
    $search_filter = array(
        'keyword' => $data_search,
        'fields' => 'name,content'
    );

    $start = ( $current_page - 1 ) *  $per_page;
	$shapes = $magic_admin->get_rows('shapes', $search_filter, $orderby, $ordering, $per_page, $start);
	$total_record = $magic_admin->get_rows_total('shapes');

    $config = array(
    	'current_page'  => $current_page,
		'total_record'  => $shapes['total_count'],
		'total_page'    => $shapes['total_page'],
 	    'limit'         => $per_page,
	    'link_full'     => $magic->cfg->admin_url.'magic-page=shapes&tpage={page}',
	    'link_first'    => $magic->cfg->admin_url.'magic-page=shapes',
	);

	$magic_pagination->init($config);

?>

<div class="magic_wrapper">

	<div class="magic_content">

		<div class="magic_header">
			<h2><?php echo $magic->lang('Shapes'); ?></h2>
			<a href="<?php echo $magic->cfg->admin_url;?>magic-page=shape" class="add-new magic-button">
				<i class="fa fa-plus"></i> 
				<?php echo $magic->lang('Add new shape'); ?>
			</a>
			<?php if (isset($shapes['rows']) && count($shapes['rows']) < 3){ ?>
			<a href="<?php echo $magic->cfg->admin_url;?>magic-page=shapes&task=import-sample" class="add-new magic-button">
				<i class="fa file-import"></i>
				<?php echo $magic->lang('Import sample'); ?>
			</a>
			<?php } ?>
			<?php
				$magic_page = isset($_GET['magic-page']) ? $_GET['magic-page'] : '';
				echo $magic_helper->breadcrumb($magic_page);
			?>
		</div>



		<div class="magic_option">
			<div class="left">
				<form action="<?php echo $magic->cfg->admin_url;?>magic-page=shapes" method="post">
					<select name="action" class="art_per_page">
						<option value="none"><?php echo $magic->lang('Bulk Actions'); ?></option>
						<option value="active"><?php echo $magic->lang('Active'); ?></option>
						<option value="deactive"><?php echo $magic->lang('Deactive'); ?></option>
						<option value="delete"><?php echo $magic->lang('Delete'); ?></option>
					</select>
					<input type="hidden" name="id_action" class="id_action">
					<input  class="magic_submit" type="submit" name="action_submit" value="<?php echo $magic->lang('Apply'); ?>">
					<?php $magic->securityFrom();?>
				</form>
				<form action="<?php echo $magic->cfg->admin_url;?>magic-page=shapes" method="post">
					<select name="per_page" class="art_per_page" data-action="submit">
						<option value="none">-- <?php echo $magic->lang('Per page'); ?> --</option>
						<?php
							$per_pages = array('5', '10', '15', '20', '100');

							foreach($per_pages as $val) {

							    if($val == $per_page) {
							        echo '<option selected="selected">'.$val.'</option>';
							    } else {
							        echo '<option>'.$val.'</option>';
							    }

							}
						?>
					</select>
					<?php $magic->securityFrom();?>
				</form>
				<form action="<?php echo $magic->cfg->admin_url;?>magic-page=shapes" method="post">
					<select name="sort" class="art_per_page" data-action="submit">
						<option value="">-- <?php echo $magic->lang('Sort by'); ?> --</option>
						<option value="name_asc" <?php if ($dt_order == 'name_asc' ) echo 'selected' ; ?> ><?php echo $magic->lang('Name'); ?> A-Z</option>
						<option value="name_desc" <?php if ($dt_order == 'name_desc' ) echo 'selected' ; ?> ><?php echo $magic->lang('Name'); ?> Z-A</option>
					</select>
					<?php $magic->securityFrom();?>
				</form>
			</div>
			<div class="right">
				<form action="<?php echo $magic->cfg->admin_url;?>magic-page=shapes" method="post">
					<input type="search" name="search" class="search" placeholder="<?php echo $magic->lang('Search ...'); ?>" value="<?php if(isset($_SESSION[$prefix.'data_search'])) echo $_SESSION[$prefix.'data_search']; ?>">
					<input  class="magic_submit" type="submit" name="search_shape" value="<?php echo $magic->lang('Search'); ?>">
					<?php $magic->securityFrom();?>

				</form>
			</div>
		</div>
		<?php if ( isset($shapes['total_count']) && $shapes['total_count'] > 0) { ?>
			<div class="magic_wrap_table">
				<table class="magic_table magic_shapes">
					<thead>
						<tr>
							<th class="magic_check">
								<div class="magic_checkbox">
									<input type="checkbox" id="check_all">
									<label for="check_all"><em class="check"></em></label>
								</div>
							</th>
							<th><?php echo $magic->lang('Name'); ?></th>
							<th><?php echo $magic->lang('Preview'); ?></th>
							<th><?php echo $magic->lang('Status'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php

							if ( is_array($shapes['rows']) && count($shapes['rows']) > 0 ) {

								foreach ($shapes['rows'] as $value) { ?>

									<tr>
										<td class="magic_check">
											<div class="magic_checkbox">
												<input type="checkbox" name="checked[]" class="action_check" value="<?php if(isset($value['id'])) echo $value['id']; ?>" class="action" id="<?php if(isset($value['id'])) echo $value['id']; ?>">
												<label for="<?php if(isset($value['id'])) echo $value['id']; ?>"><em class="check"></em></label>
											</div>
										</td>
										<td><a href="<?php echo $magic->cfg->admin_url;?>magic-page=shape&id=<?php if(isset($value['id'])) echo $value['id'] ?>" class="name"><?php if(isset($value['name'])) echo $value['name']; ?></a></td>
										<td><?php if(isset($value['content'])) echo $magic->lib->stripallslashes($value['content']); ?></td>
										<td>
											<a href="#" class="magic_action" data-type="shapes" data-action="switch_active" data-status="<?php echo (isset($value['active']) ? $value['active'] : '0'); ?>" data-id="<?php if(isset($value['id'])) echo $value['id'] ?>">
												<?php
													if (isset($value['active'])) {
														if ($value['active'] == 1) {
															echo '<em class="pub">'.$magic->lang('active').'</em>';
														} else {
															echo '<em class="un pub">'.$magic->lang('deactive').'</em>';
														}
													}
												?>
											</a>
										</td>
									</tr>

								<?php }

							}

						?>
					</tbody>
				</table>
			</div>
			<div class="magic_pagination"><?php echo $magic_pagination->pagination_html(); ?></div>

		<?php } else {
					if (isset($total_record) && $total_record > 0) {
						echo '<p class="no-data">'.$magic->lang('Apologies, but no results were found.').'</p>';
						$_SESSION[$prefix.'data_search'] = '';
						echo '<a href="'.$magic->cfg->admin_url.'magic-page=shapes" class="btn-back"><i class="fa fa-reply" aria-hidden="true"></i>'.$magic->lang('Back To Lists').'</a>';
					}
					else
						echo '<p class="no-data">'.$magic->lang('No data. Please add shape.').'</p>';
			}?>

	</div>

</div>
