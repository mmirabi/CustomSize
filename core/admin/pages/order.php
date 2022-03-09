<?php
    
	$prefix = 'ops_';
    
    $order_id = isset($_REQUEST['order_id'])? $_REQUEST['order_id']: 0;
    // Search Form
	$data_search = '';
	if (isset($_POST['search_ops']) && !empty($_POST['search_ops'])) {
		
		$data_search = isset($_POST['search']) ? trim($_POST['search']) : '';

		if (empty($data_search)) {
			$errors = 'Search Product Name';
			$_SESSION[$prefix.'data_search'] = '';
		} else {
			$_SESSION[$prefix.'data_search'] = 	$data_search;
		}

	}

	if (!empty($_SESSION[$prefix.'data_search'])) {
		$data_search = '%'.$_SESSION[$prefix.'data_search'].'%';
	}
    
    $search_filter = array(
        'keyword' => $data_search,
        'fields' => 'ops.product_name'
    );
    
    $magic->do_action('before_order_products', $order_id);
    
    // Sort Form
	if (!empty($_POST['sort'])) {

		$dt_sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$_SESSION[$prefix.'dt_order'] = $dt_sort;
		
		switch ($dt_sort) {

			case 'product_id_asc':
				$_SESSION[$prefix.'orderby'] = 'product_id';
				$_SESSION[$prefix.'ordering'] = 'asc';
				break;
			case 'product_id_desc':
				$_SESSION[$prefix.'orderby'] = 'product_id';
				$_SESSION[$prefix.'ordering'] = 'desc';
				break;
			case 'name_asc':
				$_SESSION[$prefix.'orderby'] = 'product_name';
				$_SESSION[$prefix.'ordering'] = 'asc';
				break;
			case 'name_desc':
				$_SESSION[$prefix.'orderby'] = 'product_name';
				$_SESSION[$prefix.'ordering'] = 'desc';
				break;
            
			default:
				break;

		}

	}
    
    if(
        $_SERVER['REQUEST_METHOD'] =='POST' &&
        (
            !empty($_POST['sort']) ||
            isset($_POST['do'])
        )
    ){
        $magic->redirect($magic->cfg->admin_url.'magic-page=order&order_id='.$order_id);
    }

	$orderby  = (isset($_SESSION[$prefix.'orderby']) && !empty($_SESSION[$prefix.'orderby'])) ? $_SESSION[$prefix.'orderby'] : 'product_id';
	$ordering = (isset($_SESSION[$prefix.'ordering']) && !empty($_SESSION[$prefix.'ordering'])) ? $_SESSION[$prefix.'ordering'] : 'asc';
	$dt_order = isset($_SESSION[$prefix.'dt_order']) ? $_SESSION[$prefix.'dt_order'] : 'product_id_desc';
    $items = $magic->connector->products_order($order_id, $search_filter, $orderby, $ordering);

    $magic_printings = $magic->lib->get_prints();
    $printings = array();
    foreach( $magic_printings as $p ) {
        $printings[ $p['id'] ] = $p;
    }
    
?><div class="magic_wrapper">
	
	<div class="magic_content">

		<div class="magic_header">
			<h2>
				<a href="<?php echo $magic->cfg->admin_url; ?>magic-page=orders"><?php echo $magic->lang('All Orders'); ?></a> 
				<i class="fa fa-angle-right"></i> 
				<?php printf($magic->lang('Order %s'), '#'.$_REQUEST['order_id']) ?>
			</h2>
			<?php
				$magic_page = isset($_GET['magic-page']) ? $_GET['magic-page'] : '';
				echo $magic_helper->breadcrumb($magic_page);
			?>
            <div class="magic-order-details magic_option">
                <div class="col-3">
                    <h4><?php echo $magic->lang('General Details'); ?></h4>
                    <p>
                        <strong><?php echo $magic->lang('Total Price:'); ?></strong>
                        <span><?php echo $magic->lib->price($items['order']['total']);?></span>
                    </p>
                    <p>
                        <strong><?php echo $magic->lang('Created At:'); ?></strong>
                        <span><?php echo $items['order']['created'];?></span>
                    </p>
                    <p>
                        <strong><?php echo $magic->lang('Updated At:'); ?></strong>
                        <span><?php echo $items['order']['updated'];?></span>
                    </p>
                    <?php if(isset($items['order']['payment'])): ?>
                    <p>
                        <strong><?php echo $magic->lang('Payment:'); ?></strong>
                        <span class="magic-payment-method"><?php echo isset($items['order']['payment'])? $items['order']['payment']: '';?></span>
                    </p>
                    <?php endif; ?>
                    <div class="order_status">
                        <strong><?php echo $magic->lang('Status:'); ?></strong>
                    
                        <form action="<?php echo $magic->cfg->admin_url;?>magic-page=order&order_id=<?php echo $order_id;?>" method="post">
                            <?php $magic->views->order_statuses($items['order']['status'], true);?>
                            <input type="hidden" name="do" value="action"/>
                        </form>
                    </div>
                </div>
                <?php if(isset($items['billing']) && count($items['billing'])>0):?>
                <div class="col-3">
                	<h4><?php echo $magic->lang('Billing details'); ?></h4>
                    <p>
                        <strong><?php echo $magic->lang('Name:'); ?></strong>
                        <span><?php echo isset($items['billing']['name'])? $items['billing']['name'] : '';?></span>
                    </p>
                	<p>
                		<strong><?php echo $magic->lang('Address:'); ?></strong>
                		<span><?php echo isset($items['billing']['address'])? $items['billing']['address'] : '';?></span>
                	</p>
                	<p>
                		<strong><?php echo $magic->lang('Email address:'); ?></strong>
                		<span><?php echo isset($items['billing']['email'])? $items['billing']['email'] : '';?></span>
                	</p>
                	<p>
                		<strong><?php echo $magic->lang('Phone:'); ?></strong>
                		<span><?php echo isset($items['billing']['phone'])? $items['billing']['phone'] : '';?></span>
                	</p>
                	
                </div>
                <?php endif;?>
            </div>
            
		</div>

            <div class="magic_option">
                <div class="left">
                    <form action="<?php echo $magic->cfg->admin_url;?>magic-page=order&order_id=<?php echo $order_id;?>" method="post">
                        <?php $magic->securityFrom();?>
                    </form>
                </div>
                <div class="right">
                    <form action="<?php echo $magic->cfg->admin_url;?>magic-page=order&order_id=<?php echo $order_id;?>" method="post">
                        <input type="text" name="search" class="search" placeholder="<?php echo $magic->lang('Search ...'); ?>" value="<?php if(isset($_SESSION[$prefix.'data_search'])) echo $_SESSION[$prefix.'data_search']; ?>">
                        <input  class="magic_submit" type="submit" name="search_ops" value="<?php echo $magic->lang('Search'); ?>">
                        <?php $magic->securityFrom();?>

                    </form>
                </div>
            </div>
        
        <div class="magic_wrap_table">
			<table class="magic_table magic_ops magic_order_details">
				<thead>
					<tr>
						<th width="5%"><?php echo $magic->lang('ID'); ?></th>
						<th width="5%"><?php echo $magic->lang('Product ID'); ?></th>
						<th><?php echo $magic->lang('Product Name'); ?></th>
						<th><?php echo $magic->lang('Thumbnail'); ?></th>
						<th><?php echo $magic->lang('Attributes'); ?></th>
                        <th width="5%"><?php echo $magic->lang('Subtotal'); ?></th>
                        <th width="30%"><?php echo $magic->lang('Print'); ?></th>
					</tr>
				</thead>
				<tbody>
	                <?php
	                
	                if (count($items['rows']) > 0) {
	                    foreach($items['rows'] as $item):
	                    
	                    $scrs = array();
	                    $pdfid = '';
	                    $sc = @json_decode($item['screenshots']);
						$prt = @json_decode($item['print_files'], true);
						
						$pdfid = $item['cart_id'];
						
						foreach ($sc as $i => $s) {
							array_push($scrs, array(
								"url" => is_array($prt) && isset($prt[$i]) ? $magic->cfg->upload_url.'orders/'.$prt[$i] : '#',
								"screenshot" => $magic->cfg->upload_url.'orders/'.$s,
								"download" => true
							));
						}
	                ?>
	                <tr>
						<td>#<?php echo $item['id'];?></td>
						<td><?php echo $item['product_id'];?></td>
						<td><?php echo $item['product_name'] . ' x ' .$item['qty'];?></td>
						<td>
                            <?php
                            $product = $magic->lib->get_product($item['product_base']);
                            if(isset($item['screenshots']) && $item['screenshots'] != null){
                                $screenshots = json_decode($item['screenshots']);
                                foreach ($screenshots as $screenshot) {
                					echo '<img src="'.$magic->cfg->upload_url.'orders/'.$screenshot.'" class="magic-order-thumbnail" />';
                				}
                            }
                            if(isset($item['custom']) && !$item['custom']){
                                
                                if(isset($product['thumbnail_url']))
                                    echo '<img src="'.$product['thumbnail_url'].'" class="magic-order-thumbnail" />';
                            }
                            ?>
                        </td>
                        <td><?php
	                        
                            $data_obj = $magic->lib->dejson($item['data']);
                            
                            if ( isset($data_obj->attributes) ) {
	                            
                                foreach ($data_obj->attributes as $id => $attr ) {
	                                
	                                if (
	                                	is_object($attr) && 
	                                	isset($attr->name)
	                                ) {
		                                
		                                if (isset($attr->value)) {
			                                
		                                    echo "<strong>{$attr->name}:</strong> ";
		                                    
		                                    if (
		                                    	$attr->type == 'color' || 
		                                    	$attr->type == 'product_color'
		                                    ) {
			                                    
			                                    $col = $attr->value;
			                                    
			                                   	if (
			                                   		is_object($attr->values) && 
			                                   		is_array($attr->values->options)
			                                   	) {
				                                   	foreach ($attr->values->options as $op) {
					                                   	if ($op->value == $attr->value)
					                                   		$col = htmlentities($op->title);
				                                   	}
			                                   	}
												echo '<span title="'.htmlentities($attr->value).'" style="background: '.$attr->value.';padding: 2px 5px;border-radius: 2px;">'.$col.'</span>';
		                                    } else echo htmlentities($attr->value);
		                                    
		                                    echo "<br>";
		                                }
		                                
                                    } else {
	                                    
	                                    echo "<strong>$id:</strong>";
	                                    if (is_array($values)){
	                                        foreach($values as $att_val){
	                                            echo "<dt>$attr</dt>";
	                                        }
	                                    } 
                                    }
	                            }
	                          
                            }
                            
                            if( 
                                isset($data_obj->printing) 
                                && is_array($printings) 
                                && isset($printings[ $data_obj->printing]) 
                            ){
                                $pmethod = $printings[ $data_obj->printing];
                                echo "<strong>".$magic->lang('Printing Type').":</strong>";
                                echo "<dt>".$pmethod['title']."</dt>";
                            }
                            
                            if( isset($data_obj->color) ){
                                echo "<strong>".$magic->lang('Color').":</strong>";
                                echo "<dt>".(($data_obj->color != $data_obj->color_name)? $data_obj->color . ' - '. $data_obj->color_name : $data_obj->color)."</dt>";
                            }
                        ?></td>
                        <td><?php echo $magic->lib->price($item['product_price']);?></td>
                        <td>
	                        <?php
                               
		                        if (count($scrs) > 0) {
                                   
		                        	$key = $magic->get_option('purchase_key');
									$key_valid = ($key === null || empty($key) || strlen($key) != 36 || count(explode('-', $key)) != 5) ? false : true;
                                    
 
			
									$is_query = explode('?', $magic->cfg->tool_url);
													
									$url = $magic->cfg->tool_url.(isset($is_query[1])? '&':'?');
									
                                    if (!empty($item['design'])) {
										$url .= '&design_print='.str_replace('.lumi', '', $item['design']);
										$url .= '&order_print='.$item['order_id'];
										
										if($magic->connector->platform == 'woocommerce'){
											$order = wc_get_order($item['order_id']);
											foreach ( $order->get_items() as $item_id => $order_item ) {
												if($order_item->get_product_id() == $item['product_id'] && $order_item->get_meta( 'magic_data', true )){
													if($order_item->get_variation_id()){
														$url .= '&product_base='.'variable:'.$order_item->get_variation_id();
														$url .=  '&product_cms=' . $item['product_id'];
													}else{
														$url .= '&product_base='.$item['product_base'];
														$url .= '&product_cms=' . $item['product_id'];
													}
													break;
												}
											};
										}
										if($magic->connector->platform == 'php'){
											$url .= '&product_base='.$item['product_base'];
										}
									}
									
									$url = str_replace('?&', '?', $url);
									$html = '<p>';
									$prtable = false; 
									
									if($key_valid) {
										foreach ($scrs as $i => $scr) {
											
											$html .= '<a ';
											
											if ($scr['download'] === true) {
												$html .= 'href="'.$scr['url'].'" download="order_id#'.$item['id'].' (stage '.($i+1).').png"';
												$prtable = true;
											} else {
												$html .= 'href="'.(!empty($scr['url']) ? $scr['url'] : $url).'" target=_blank';
											}
											$html .= '><img width="80" src="'.$scr['screenshot'].'" /></a>';
										}
									}
									
									
									$html .= '</p>';
									
									if ($prtable === true && $key_valid) {
										$html .= '<p><font color="#e74c3c">(*) ';
										$html .= $magic->lang('Click on each image above to download the printable file <b>(.PNG)</b>').'</font></p>';
									}
									
									$html .= '<p>';
									if(!$key_valid){
										$html .= '<p style="font-size:14px;"><font color="#E91E63">(*) ';
										$html .= $magic->lang('<span>Please enter your purchase code to display and download file designs</span></br>
<b><a target="_blank" href="'.$magic->cfg->admin_url.'magic-page=license"style="font-weight: 700; text-decoration: underline; font-style: italic;">Enter purchase code now</a></b></br>
<span>Notice: Each License can only be used for one domain.</br><a href="#" target="blank" style="font-weight: 700; text-decoration: underline; font-style: italic;">Click to learn more about license term in Envato.</a></span>').'</font></p>';
									}
									
									if (!empty($pdfid)) {
										$link = $magic->cfg->tool_url;
										if(strpos($link, '?') !== false && substr($link, -1) != '?'){
											$link .= '&pdf_download='.$pdfid;
										} 
										if(strpos($link, '?') !== false && substr($link, -1) == '?') {
											$link .= 'pdf_download='.$pdfid;
										}
										if(strpos($link, '?') === false) {
											$link .= '?pdf_download='.$pdfid;
										}
										if($key_valid) {
											$html .= '<a href="'.$link.'" target=_blank class="magic-button magic-button-primary" style="margin-bottom:5px;">'.$magic->lang('Download designs as PDF').'</a>  &nbsp; <a href="#" data-href="'.$link.'" target=_blank class="magic-button magic-button-primary" onclick="let r = prompt(\'Enter bleed range in mimilet (Typically it is 2mm)\', \'2\');if (r){this.href = this.dataset.href+\'&bleed=\'+r;return true;}else return false;" style="margin-bottom:5px;">'.$magic->lang('PDF cropmarks & bleed').'</a> &nbsp; ';
										}
									}	
									
									if($key_valid) {
										$html .= '<a href="'.$url.'" target=_blank class="magic-button">'.$magic->lang('View in MagicRugs editor').'</a>';
									}
									
									$html .= '</p>';
									
									echo $html;
									
								}
		                        
	                        ?>
		                </td>
					</tr>
	                    <?php
	                    endforeach;
	                }
	                else {
	                ?>
	                <tr>
	                    <td colspan="6">
	                        <p class="no-data"><?php echo $magic->lang('Apologies, but no results were found'); ?></p>
	                    </td>
	                </tr>
	                    
	                    
	                <?php
	                }
	                ?>
				</tbody>
                <tfoot class="no-border">
                    <tr>
                        <td colspan="3"></td>
                        <td></td>
                        <td colspan="2">
                            <strong style="float: right;"><?php echo $magic->lang('Order Total:'); ?></strong>
                        </td>
                        <td>
                            <?php echo $magic->lib->price($items['order']['total']); ?>
                        </td>
                    </tr>
                </tfoot>
			</table>
        </div>
		
	</div>

</div>
