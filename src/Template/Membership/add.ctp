<?php
echo $this->Html->css('bootstrap-multiselect');
echo $this->Html->script('bootstrap-multiselect');
?>
<script type="text/javascript">
$(document).ready(function() {	
$('.class_list').multiselect({
		includeSelectAllOption: true,
		nonSelectedText: "<?php echo __('Select an option');?>",
		allSelectedText: "<?php echo __('Selected all');?>",
		selectAllText : "<?php echo __('Select All');?>",
		nSelectedText: "<?php echo __('selected');?>",		
	});
});
function validate_multiselect()
	{		
			var classes = $(".class_list").val();
			var msg = "<?php echo __('Please Select Class or Add class class first.') ?>";
			if(classes == null)
			{
				alert(msg);
				return false;
			}else{
				return true;
			}		
	}
	

</script>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-users"></i>
				<?php echo $title;?>
				<!-- <small><?php echo __("Membership");?></small> -->
			  </h1>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("Membership","membershipList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Membership List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<?php
			
			echo $this->Form->create($membership,["id"=>"form","type"=>"file","class"=>"validateForm form-horizontal","onsubmit"=>"return validate_multiselect()"]);
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Image")."</label>";
			echo "<div class='col-md-8'>";
			echo $this->Form->file("gmgt_membershipimage",["class"=>"form-control","id"=>"imgInp"]);
			echo "<script>
					function readURL(input) {
						if (input.files && input.files[0]) {
							var reader = new FileReader();
							
							reader.onload = function (e) {
								$('#blah').attr('style', 'display:inline');
								$('#blah').attr('src', e.target.result);
							}
							reader.readAsDataURL(input.files[0]);
						}
					}
					
					$('#imgInp').change(function(){
						readURL(this);
					});</script>";
			echo "</div>";
			echo "</div>";
			
			if($edit)
			{
				if($membership_data['gmgt_membershipimage'] != "")
				{
					echo "<div class='form-group'>";
					echo "<label class='control-label col-md-3'>".__("Current Image")."</label>";
					echo "<div class='col-md-8'>";
					echo "<img width='100px' src='".$this->request->webroot ."upload/" . $membership_data['gmgt_membershipimage']."' class='img-responsive'>";
					echo "</div>";
					echo "</div>";
				}
			}
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Name")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";
			echo $this->Form->input("",["label"=>false,"name"=>"membership_label","class"=>"form-control validate[required,maxSize[50]]","value"=>($edit)?$membership_data['membership_label']:""]);
			echo "</div>";
			echo "</div>";
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Branch")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";
			echo $this->Form->select("branch_id",$branches,["default"=>($edit)?$membership_data["branch_id"]:"","empty"=>__("Select Branch"),"class"=>"form-control validate[required]"]);
			echo "</div>";
			echo "</div>";
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Category")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-5 module_padding'>";
			echo $this->Form->select("membership_cat_id",$categories,["default"=>($edit)?$membership_data["membership_cat_id"]:"","empty"=>__("Select Category"),"class"=>"form-control validate[required] cat_list"]);
			echo "</div>";			
			echo "<div class='col-md-2'>";			
			echo $this->Form->button(__("Add Category"),["class"=>"form-control add_category btn btn-success btn-flat","type"=>"button","data-url"=>$this->Gym->createurl("GymAjax","addCategory")]);
			echo "</div>";	
			echo "</div>";
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Period")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";
			echo "<div class='input-group'>";
			echo "<div class='row'>";
			echo "<div class='col-md-6'>";
			echo "<div class='input-group'>";
			echo "<span class='input-group-addon'>".__('Duration')."</span>";
			echo $this->Form->input("",["label"=>false,"name"=>"duration_value","class"=>"form-control validate[required,custom[onlyNumberSp],min[1],maxSize[4]]","value"=>($edit)?$this->Gym->getDurationValue($membership_data['membership_length']):""]);
			echo "</div>";
			echo "</div>";
			echo "<div class='col-md-6'>";
			echo $this->Form->select("duration_type",
				[
					'days' => __('Days'),
					'months' => __('Meses')
				],
				[
					"default" => ($edit) ? $this->Gym->getDurationType($membership_data['membership_length']) : 'days',
					"class" => "form-control duration-type"
				]
			);
			echo "</div>";
			echo "</div>";
			echo "<input type='hidden' name='membership_length' id='membership_length' value='" . (($edit) ? $membership_data['membership_length'] : '') . "'>";
			echo "</div>";
			echo "</div>";
			echo "</div>";

			echo "<script>
			$(document).ready(function() {
				function updateMembershipLength() {
					var value = $('input[name=\"duration_value\"]').val() || 0;
					var type = $('.duration-type').val();
					var days = (type === 'months') ? value * 30 : value;
					$('#membership_length').val(days);
				}

				$('input[name=\"duration_value\"]').on('input', updateMembershipLength);
				$('.duration-type').on('change', updateMembershipLength);
				
				// Initial calculation
				updateMembershipLength();
			});
			</script>";
			echo "<script>
			$(document).ready(function() {
				function updateMembershipLength() {
					var value = $('input[name=\"duration_value\"]').val() || 0;
					var type = $('.duration-type').val();
					var days = (type === 'months') ? value * 30 : value;
					$('#membership_length').val(days);
					
					// Controlar la cuota de mantenimiento basado en la duración
					toggleMaintenanceFee();
				}

				function toggleMaintenanceFee() {
					var value = parseInt($('input[name=\"duration_value\"]').val() || 0);
					var type = $('.duration-type').val();
					var isEligible = false;
					
					// Verificar si cumple el requisito de 6 meses o más
					if (type === 'months' && value >= 6) {
						isEligible = true;
					} else if (type === 'days' && value >= 180) {
						isEligible = true;
					}
					
					// Habilitar o deshabilitar el campo
					if (isEligible) {
						$('#maintenance-fee').prop('disabled', false);
						$('#maintenance-fee-message').hide();
					} else {
						$('#maintenance-fee').prop('disabled', true);
						$('#maintenance-fee').val('0');
						$('#maintenance-fee-message').show();
					}
				}

				$('input[name=\"duration_value\"]').on('input', updateMembershipLength);
				$('.duration-type').on('change', updateMembershipLength);
				
				// Initial calculation and setup
				updateMembershipLength();
			});
			</script>";
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Limit")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-3 module_padding'>";
			echo '<label class="radio-inline"><input type="radio" class="check_limit" name="membership_class_limit" value="Limited" '.(($edit && $membership_data['membership_class_limit'] == "Limited") ? "checked" : "") .' '.((!$edit)?"checked":"").'>'. __('Limited') .'</label>
				  <label class="radio-inline"><input type="radio" class="check_limit" name="membership_class_limit" value="Unlimited" '.(($edit && $membership_data['membership_class_limit'] == "Unlimited") ? "checked" : "") .'>'. __("Unlimited") .'</label>';
			echo "</div>";
			echo "<div class='col-md-2 div_limit module_padding'>";
				echo $this->Form->input("",["label"=>false,"name"=>"limit_days","placeholder"=>__('No. of Classes'),"class"=>"form-control validate[required,custom[onlyNumberSp],maxSize[2]]","value"=>($edit)?$membership_data["limit_days"]:""]);
			echo "</div>";
			echo "<div class='col-md-3 div_limit'>";
				$limitation = ["per_week"=>__("Class every week"),"per_month"=>__("Class every month")];
				echo $this->Form->select("limitation",$limitation,["default"=>($edit)?$membership_data["limitation"]:"","class"=>"form-control"]);
			echo "</div>";
			echo "</div>";
			?>
			<script>
			if($(".check_limit:checked").val() == "Unlimited")
			{ 
				$(".div_limit").hide("fast");
				
				$(".div_limit input,.div_limit select").attr("disabled", "disabled");		
			}
			</script>
			<?php
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Amount")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";	
			echo "<div class='input-group'>";	
			echo "<span class='input-group-addon'>".$this->Gym->get_currency_symbol()."</span>";	
			echo $this->Form->input("",["label"=>false,"name"=>"membership_amount","class"=>"form-control validate[required,custom[onlyNumberSp],maxSize[8]]","value"=>($edit)?$membership_data['membership_amount']:""]);
			echo "</div>";	
			echo "</div>";	
			echo "</div>";	

			// NUEVO CAMPO: Cuota de Mantenimiento
			echo "<div class='form-group' id='maintenance-fee-container'>";
			echo "<label class='control-label col-md-3'>".__("Cuota de Mantenimiento")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";    
			echo "<div class='input-group'>";    
			echo "<span class='input-group-addon'>".$this->Gym->get_currency_symbol()."</span>";    

			// Determinar el valor a mostrar - convertir a entero para remover decimales
			$maintenanceValue = ($edit && isset($membership_data['maintenance_fee'])) ? 
								intval($membership_data['maintenance_fee']) : 
								"0";

			// Usar input básico para máxima compatibilidad
			echo "<input type='text' name='maintenance_fee' id='maintenance-fee' value='{$maintenanceValue}' class='form-control validate[required,custom[onlyNumberSp],maxSize[8]]'>";

			echo "</div>";
			// Mensaje de aviso cuando está deshabilitado    
			echo "<p class='help-block text-warning' id='maintenance-fee-message' style='display:none;'>".__("La cuota de mantenimiento solo aplica para membresías de 6 meses o más.")."</p>";    
			echo "</div>";    
			echo "</div>";
						

			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Select Class")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-5'>";
			echo $this->Form->select("membership_class",$classes,["default"=>($edit)?$membership_class:"","class"=>"form-control class_list","multiple"=>"multiple"]);
			echo "</div>";			
			echo "</div>";
			
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Installment Plan")."</label>";
			echo "<div class='col-md-2 module_padding'>";
			echo $this->Form->input("",["label"=>false,"name"=>"installment_amount","class"=>"form-control validate[custom[onlyNumberSp],maxSize[6]]","placeholder"=>__("Amount"),"value"=>($edit)?$membership_data['installment_amount']:""]);
			echo "</div>";
			
			echo "<div class='col-md-4 module_padding'>";						
			echo $this->Form->select("install_plan_id",$installment_plan,["default"=>($edit)?$membership_data["install_plan_id"]:"","empty"=>__("Select Installment Plan"),"class"=>"form-control plan_list"]);
			echo "</div>";			
			
			echo "<div class='col-md-2'>";			
			
			echo $this->Form->button(__("Add Installment Plan"),["class"=>"form-control add_plan btn btn-success btn-flat","type"=>"button","data-url"=>$this->Gym->createurl("GymAjax","addInstalmentPlan")]);
			echo "</div>";
			echo "</div>";
						

			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Signup Fee")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-8'>";
			echo "<div class='input-group'>";	
			echo "<span class='input-group-addon'>".$this->Gym->get_currency_symbol()."</span>";
			echo $this->Form->input("",["label"=>false,"name"=>"signup_fee","class"=>"form-control validate[required,custom[onlyNumberSp],maxSize[6]]","value"=>($edit)?$membership_data['signup_fee']:""]);
			echo "</div>";
			echo "</div>";
			echo "</div>";
			
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Créditos")."</label>";
			echo "<div class='col-md-8'>";
			echo "<div id='credits-container'>";
			
			if ($edit && !empty($membership_data->membership_credits)) {
				foreach ($membership_data->membership_credits as $index => $credit) {
					echo "<div class='credit-entry mb-3'>";
					echo "<div class='input-group'>";
					echo "<span class='input-group-addon'>".__('Número de créditos')."</span>";
					echo $this->Form->input("",["label"=>false,"name"=>"credits[{$index}][amount]","class"=>"form-control validate[custom[onlyNumberSp],min[0]]","value"=>$credit->credits]);
					echo "</div>";
					
					echo "<div class='branch-selection mt-2' style='margin-top: 5px; margin-bottom: 5px;'>";
					echo "<label style='margin-right: 15px;'>".__("Sucursales")."</label>";
					$selectedBranchIds = [];
					if (!empty($credit->membership_credit_branches)) {
						foreach ($credit->membership_credit_branches as $branch) {
							$selectedBranchIds[] = $branch->branch_id;
						}
					}
					echo $this->Form->select("credits[{$index}][branches][]", $branches, [
						"multiple"=>"multiple",
						"class"=>"form-control credit-branches",
						"value"=>$selectedBranchIds
					]);
					echo "</div>";
					
					if ($index > 0) {
						echo "<button type='button' class='btn btn-danger btn-sm remove-credit mt-2' style='margin-bottom: 15px;'>".__("Eliminar crédito")."</button>";
					}
					echo "</div>";
				}
			} else {
				echo "<div class='credit-entry mb-3'>";
				echo "<div class='input-group'>";
				echo "<span class='input-group-addon'>".__('Número de créditos')."</span>";
				echo $this->Form->input("",["label"=>false,"name"=>"credits[0][amount]","class"=>"form-control validate[required,custom[onlyNumberSp],min[0]]"]);
				echo "</div>";
				
				echo "<div class='branch-selection mt-2' style='margin-top: 5px; margin-bottom: 5px;'>";
				echo "<label style='margin-right: 15px;'>".__("Sucursales")."</label>";
				echo $this->Form->select("credits[0][branches][]", $branches, [
					"multiple"=>"multiple",
					"class"=>"form-control credit-branches",
					"required"=>"required"
				]);
				echo "</div>";
				echo "</div>";
			}
			
			echo "</div>";
			echo "<button type='button' class='btn btn-success btn-sm mt-2' style='margin-top: 15px;' id='add-credit'>".__("Agregar crédito")."</button>";
			echo "</div>";
			echo "</div>";
			
			echo "<script>
			$(document).ready(function() {
				// Initialize existing multiselect
				initializeMultiselect();
				
				// Add new credit entry
				$('#add-credit').click(function() {
					var index = $('.credit-entry').length;
					var template = `
						<div class='credit-entry mb-3'>
							<div class='input-group'>
								<span class='input-group-addon'>".__('Número de créditos')."</span>
								<input type='text' name='credits[" . ($index ?? 1) . "][amount]' class='form-control validate[required,custom[onlyNumberSp],min[0]]'>
							</div>
							<div class='branch-selection mt-2' style='margin-top: 5px; margin-bottom: 5px;'>
								<label style='margin-right: 15px;'>".__("Sucursales")."</label>
								<select name='credits[" . ($index ?? 1) . "][branches][]' class='form-control credit-branches' multiple required>
									".implode('', array_map(function($id, $name) {
										return "<option value='{$id}'>{$name}</option>";
									}, array_keys($branches), array_values($branches)))."
								</select>
							</div>
							<button type='button' class='btn btn-danger btn-sm remove-credit mt-2' style='margin-bottom: 15px;'>".__("Eliminar crédito")."</button>
						</div>
					`;
					
					$('#credits-container').append(template);
					initializeMultiselect();
				});
				
				// Remove credit entry
				$(document).on('click', '.remove-credit', function() {
					$(this).closest('.credit-entry').remove();
				});
				
				// Initialize multiselect for credit branches
				function initializeMultiselect() {
					$('.credit-branches').not('.multiselect-initialized').each(function() {
						$(this).addClass('multiselect-initialized').multiselect({
							includeSelectAllOption: true,
							nonSelectedText: '".__("Select Branches")."',
							allSelectedText: '".__("All Branches Selected")."',
							selectAllText: '".__("Select All")."',
							nSelectedText: '".__("branches selected")."'
						});
					});
				}
			});
			</script>";
			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-3'>".__("Membership Description")."</label>";
			echo "<div class='col-md-8'>";			
			echo $this->Form->textarea("membership_description",["rows"=>"15","class"=>"form-control textarea","value"=>($edit)?$membership_data['membership_description']:""]);
			echo "</div>";
			echo "</div>";
			
			echo "<div class='col-md-offset-3'>";
			echo $this->Form->button(__("Save Membership"),['class'=>"btn btn-flat btn-success submit_button","name"=>"add_membership"]);
			echo "</div>";	
			echo $this->Form->end();
			echo "<br>";
			
		?>	
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>
<!-- Script Start -->
<script>
	/// Disable button after click 
	$(document).on('submit','#form',function(){
		var valid = $("#form").validationEngine('validate')
            if (valid == true) {
                $(".submit_button").attr('disabled', 'disabled');
            } 		
	});

// Add Category Script Start							
$("body").on("click",".add-category",function(){
	var name = $(".cat_name").val();
	var ajaxurl = $(this).attr("data-url");
	//var regex = new RegExp("^[a-zA-Z]+$");
	var regex = /^[a-zA-Z\s._-]*$/;
	if(name != "") {
		if(regex.test(name))  {
			if(name.length<=50) {
				var curr_data = { name : name};
				$.ajax({
					url : ajaxurl,
					type : "POST",
					data : curr_data,
					success : function(response) {					
						if(response) {
							$(".cat_name").val('');
							response = $.parseJSON(response);
							$("#category_list").prepend(response[0]);
							$(".cat_list").append(response[1]);
						}
					}
				});
			} else{
				var message = "<?php echo __("Please Enter Maximum 50 Character Only."); ?>";
				alert(message);
			}
		}else {
			var  message = "<?php echo __("Please Enter Letters Only."); ?>";
			alert(message);
		}
	}else {
		var message = "<?php echo __("Please Enter Category Name."); ?>";
		alert(message);
	}
	
});				
// Add Category Script End
// Delete Category Script Start
	$("body").on("click",".del-category",function(){
		var did = $(this).attr("del-id");
		var ajaxurl = $(this).attr("data-url");
		var cdata = {did:did};
		var confirmMsg = "<?php echo __("Are you sure You want to delete this record?"); ?>";
		if(confirm(confirmMsg)) {
			$.ajax({
				url:ajaxurl,
				type : "POST",
				data : cdata,
				success : function(response) {
					if(response) {
						$("tr[id=row-"+did+"]").remove();
						$("option[value="+did+"]").remove();
						var flash = "<div class='message success'>Success! Record Deleted Successfully.</div>"
						$(".message").append(flash);	
					}
				}
			});
		}else {
			return false;
		}
	});

// Delete Category Script End
</script>
<script>
$(".check_limit").change(function(){
	if($(this).val() == "Limited")
	{
		$(".div_limit input,.div_limit select").removeAttr("disabled");
		$(".div_limit").show("fast");
	}else{
		$(".div_limit").hide("fast");
		
		$(".div_limit input,.div_limit select").attr("disabled", "disabled");		
	}
});
</script>