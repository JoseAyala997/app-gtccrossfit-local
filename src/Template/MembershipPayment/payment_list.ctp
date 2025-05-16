<?php $session = $this->request->session()->read("User");?>
<?php use Cake\ORM\TableRegistry; ?>
<script type="text/javascript">
	 $( function() {
    $( document ).tooltip();
  } );
  $(document).ready(function() {
    jQuery(".expense_form").validationEngine();
    jQuery('#payment_list').DataTable({
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "aoColumns":[
                      {"bSortable": true},            // Title
                      {"bSortable": true},            // Member Name
                      {"bSortable": true,"sWidth":"1"}, // Amount
                      {"bSortable": true,"sWidth":"5px"}, // Cuota Mantenimiento (NUEVA COLUMNA)
                      {"bSortable": true,"sWidth":"5px"}, // Paid Amount
                      {"bSortable": true,"sWidth":"5px"}, // Due Amount
                      {"bSortable": true,"sWidth":"5px"}, // Start Date
                      {"bSortable": true,"sWidth":"5px"}, // End Date
                      {"bSortable": true,"sWidth":"5px"}, // Payment Status
                      {"bSortable": true,"sWidth":"5px"}, // Confirmation Status
                      {"bSortable": true,"sWidth":"5px"}, // Mantenimiento (NUEVA COLUMNA)
                      {"bSortable": false}],           // Action
    "language" : {<?php echo $this->Gym->data_table_lang();?>}	
        });
} );
var box_height = $(".box").height();
var box_height = box_height + 200 ;
$(".content").css("height",box_height+"px");
	
</script>
<script>
// Añadir al código JavaScript existente
$(document).ready(function() {
    // Manejar cambio de filtro de sucursal
    $('#branch_filter').on('change', function() {
        var branchId = $(this).val();
        
        // Redireccionar con el parámetro de sucursal
        window.location.href = '<?= $this->Url->build(['action' => 'paymentList']) ?>' + 
            (branchId ? '?branch_id=' + branchId : '');
    });
});
</script>
<section class="content ">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			
			<section class="content-header">
				<!-- Filtro de sucursal -->

				<h1>
					<i class="fa fa-bars"></i>
					<?php echo __("Payment");?>
					<!-- <small><?php echo __("Membership Payment");?></small> -->
				</h1>
				<!-- Filtro de sucursal -->
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="branch_filter"><?= __('Filtrar por Sucursalss:') ?></label>
							<select id="branch_filter" class="form-control">
								<option value=""><?= __('Todas las Sucursales') ?></option>
								<?php 
								if (isset($branches) && !empty($branches)):
									foreach($branches as $id => $name): 
										$selected = (isset($currentBranchId) && $id == $currentBranchId) ? 'selected' : '';
									?>
										<option value="<?= $id ?>" <?= $selected ?>><?= h($name) ?></option>
									<?php endforeach; 
								endif;
								?>
							</select>
						</div>
					</div>
				</div>
				 <?php
				if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member")
				{ ?>
				<ol class="breadcrumb">
					<a href="<?php echo $this->Gym->createurl("MembershipPayment","generatePaymentInvoice");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Generate Payment Invoice");?></a>
				</ol>
			<?php } ?>
			</section>
		</div>
		<hr>
		<div class="box-body">
		 <?php 
			$membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');
			$paymentHistories = [];
			if (!empty($data) && is_array($data)) {
				// Extract mp_ids safely
				$mpIds = [];
				foreach ($data as $row) {
					if (isset($row['mp_id'])) {
						$mpIds[] = $row['mp_id'];
					}
				}
				
				if (!empty($mpIds)) {
					$allPaymentHistories = $membershipPaymentHistoryTable->find()
						->where(['mp_id IN' => $mpIds])
						->order(['paid_by_date' => 'DESC'])
						->toArray();
					
					// Group by mp_id, keeping only the latest record for each
					foreach ($allPaymentHistories as $history) {
						if (!isset($paymentHistories[$history->mp_id]) || 
							$history->paid_by_date > $paymentHistories[$history->mp_id]->paid_by_date) {
							$paymentHistories[$history->mp_id] = $history;
						}
					}
				}
			}
			?>
		<table id="payment_list" class="table table-striped" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th><?php echo __('Title', 'gym_mgt'); ?></th>
        <th><?php echo __('Member Name', 'gym_mgt'); ?></th>
        <th><?php echo __('Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Cuota Mantenimiento', 'gym_mgt'); ?></th>
        <th><?php echo __('Paid Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Due Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Membership Start Date', 'gym_mgt'); ?></th>
        <th><?php echo __('Membership End Date', 'gym_mgt'); ?></th>
        <th><?php echo __('Payment Status', 'gym_mgt'); ?></th>
        <th><?php echo __('Confirmation Status', 'gym_mgt'); ?></th>
        <th><?php echo __('Mantenimiento', 'gym_mgt'); ?></th>
        <th><?php echo __('Action', 'gym_mgt'); ?></th>
    </tr>
    </thead>
    <tbody>
        <?php
            $confirmMsg = __("Are you sure,You want to delete this record?");
            if(!empty($data)) {
                foreach($data as $row) {
                    // Determinar el estado de mantenimiento
                    $isMaintenanceMode = (isset($row['is_maintenance']) && $row['is_maintenance'] == 1) || 
                                      (isset($row['gym_member']['is_maintenance_mode']) && $row['gym_member']['is_maintenance_mode'] == 1);
                    
                    // Obtener el monto de mantenimiento
                    $maintenanceFee = isset($row['membership']['maintenance_fee']) ? $row['membership']['maintenance_fee'] : 0;
                    
                    // Calcular el monto adeudado correctamente según el modo
                    if ($isMaintenanceMode && $maintenanceFee > 0) {
                        $due = $maintenanceFee - $row['paid_amount'];
                    } else {
                        $due = $row['membership_amount'] - $row['paid_amount'];
                    }
                    
                    // Asegurar que el monto no sea negativo
                    $due = max(0, $due);
                    
                    if($this->Gym->get_membership_paymentstatus($row['mp_id']) == __('Fully Paid')) {
                        $class = "btn-success";
                    } elseif($this->Gym->get_membership_paymentstatus($row['mp_id']) == __('Partially Paid')) {
                        $class = "btn-primary";
                    } else {
                        $class = "btn-danger";
                    }
					 // Formatear fechas correctamente
                     $startDate = !empty($row['start_date']) ? $row['start_date']->format('Y-m-d') : 'N/A';
                     $endDate = !empty($row['end_date']) ? $row['end_date']->format('Y-m-d') : 'N/A';
						// $due = ($row['membership_amount']- $row['paid_amount']);
						// 	if($this->Gym->get_membership_paymentstatus($row['mp_id']) == __('Fully Paid'))
						// 	{
						// 		$class = "btn-success";
						// 	}elseif($this->Gym->get_membership_paymentstatus($row['mp_id']) == __('Partially Paid'))
						// 	{
						// 		$class = "btn-primary";
						// 	}else{
						// 		$class = "btn-danger";
						// 	}
                    
                    echo "<tr>
                            <td>{$row['membership']['membership_label']}</td>
                            <td>{$row['gym_member']['first_name']} {$row['gym_member']['last_name']}</td>
                            <td>".$this->Gym->get_currency_symbol()." {$row['membership_amount']}</td>";
                    
                    // Mostrar monto de mantenimiento
                    echo "<td>".($maintenanceFee > 0 ? $this->Gym->get_currency_symbol()." {$maintenanceFee}" : "No Asignado")."</td>";
                    
                    echo "<td>".$this->Gym->get_currency_symbol()." {$row['paid_amount']}</td>
                            <td>".$this->Gym->get_currency_symbol()." {$due}</td>
                            <td>".$this->Gym->get_db_format(date($this->Gym->getSettings("date_format"),strtotime($row["start_date"])))."</td>
                            <td>{$endDate}</td>
                            <td><span class='pay_status ". $class ."'>". __($this->Gym->get_membership_paymentstatus($row['mp_id']))."<span></td>
                            ";
                            
                    // Código para confirmation status
                    $confirmationStatus = 'N/A';
                    $confirmClass = 'label-default';
                    $pending = false;
                    if (isset($paymentHistories[$row['mp_id']])) {
                        $latestPayment = $paymentHistories[$row['mp_id']];
                        if (!empty($latestPayment->payment_confirmation_status)) {
                            $confirmationStatus = $latestPayment->payment_confirmation_status;
                            
                            // Set class based on confirmation status
                            if ($confirmationStatus == 'Pending') {
                                $pending = true;
                                $confirmClass = 'label-warning';
                            } else if ($confirmationStatus == 'Confirmed') {
                                $confirmClass = 'label-success';
                            } else if ($confirmationStatus == 'Rejected') {
                                $confirmClass = 'label-danger';
                            }
                        }
                    }
                    
                    if ($pending) {
                        echo "<td><a href='{$this->request->base}/payment-confirmation/view-receipt/{$latestPayment->payment_history_id}'><span class='label {$confirmClass}'>" . __($confirmationStatus) . "</span></a></td>";
                    } else {
                        echo "<td><span class='label {$confirmClass}'>" . __($confirmationStatus) . "</span></td>";
                    }
                             
                    // Estado de mantenimiento
                    $maintenanceStatus = $isMaintenanceMode ? __('Activo') : __('Inactivo');
                    $maintenanceClass = $isMaintenanceMode ? 'label-success' : 'label-warning';
                    echo "<td><span class='label {$maintenanceClass}'>" . $maintenanceStatus . "</span></td>";
                    
                    if($due == 0) {
                        echo "
                        <td>
                        <a href='javascript:void(0)'style='display:none;' class='btn1 btn btn-flat btn-default amt_pay' disabled data-url='".$this->request->base ."/GymAjax/gymPay/{$row['mp_id']}/{$due}'>".__('Pay')."</a>
                        <a href='javascript:void(0)' class='btn1 btn btn-flat btn-info view_invoice' title='".__('View')."' data-url='".$this->request->base ."/GymAjax/viewInvoice/{$row['mp_id']}'><i class='fa fa-eye'></i></a>";
                        if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member") {
                            echo " <a href='".$this->request->base ."/MembershipPayment/MembershipEdit/{$row['mp_id']}' class='btn1 btn btn-flat btn-primary' title='".__('Edit')."'><i class='fa fa-edit'></i></a>
                            <a href='".$this->request->base ."/MembershipPayment/deletePayment/{$row['mp_id']}' class='btn1 btn btn-flat btn-danger' title='".__('Delete')."' onclick=\"return confirm('$confirmMsg')\"><i class='fa fa-trash'></i></a>";
                        }
                        echo "</td>
                        </tr>";
                    } else {
                        // Cambiar botón de pago según modo mantenimiento
                        $payText = $isMaintenanceMode ? __('Pay Maintenance') : __('Pay');
                        $payClass = $isMaintenanceMode ? 'btn-warning' : 'btn-default';
                        
                        echo "
                        <td>
                        <a href='javascript:void(0)' class='btn1 btn btn-flat {$payClass} amt_pay' data-url='".$this->request->base ."/GymAjax/gymPay/{$row['mp_id']}/{$due}'>".$payText."</a>
                        <a href='javascript:void(0)' class='btn1 btn btn-flat btn-info view_invoice' title='".__('View')."' data-url='".$this->request->base ."/GymAjax/viewInvoice/{$row['mp_id']}'><i class='fa fa-eye'></i></a>";
                        if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member") {
                            echo " <a href='".$this->request->base ."/MembershipPayment/MembershipEdit/{$row['mp_id']}' class='btn1 btn btn-flat btn-primary' title='".__('Edit')."'><i class='fa fa-edit'></i></a>
                            <a href='".$this->request->base ."/MembershipPayment/deletePayment/{$row['mp_id']}' class='btn1 btn btn-flat btn-danger' title='".__('Delete')."' onclick=\"return confirm('$confirmMsg')\"><i class='fa fa-trash'></i></a>";
                        }
                        echo "</td>
                    </tr>";
                    }
                }
            }
        ?>
    </tbody>
    <tfoot>
    <tr>
        <th><?php echo __('Title', 'gym_mgt'); ?></th>
        <th><?php echo __('Member Name', 'gym_mgt'); ?></th>
        <th><?php echo __('Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Cuota Mantenimiento', 'gym_mgt'); ?></th>
        <th><?php echo __('Paid Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Due Amount', 'gym_mgt'); ?></th>
        <th><?php echo __('Membership Start Date', 'gym_mgt'); ?></th>
        <th><?php echo __('Membership End Date', 'gym_mgt'); ?></th>
        <th><?php echo __('Payment Status', 'gym_mgt'); ?></th>
        <th><?php echo __('Confirmation Status', 'gym_mgt'); ?></th>
        <th><?php echo __('Mantenimiento', 'gym_mgt'); ?></th>
        <th><?php echo __('Action', 'gym_mgt'); ?></th>
    </tr>
    </tfoot>
</table>
		
		<!-- END -->
		</div>
		<div class='overlay gym-overlay'>
			<i class='fa fa-refresh fa-spin'></i>
		</div>
	</div>
</section>