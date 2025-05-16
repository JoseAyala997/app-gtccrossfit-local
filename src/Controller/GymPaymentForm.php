<?php
/**
 * Payment form template for gym payments
 * 
 * This file should be included from a controller context where the following variables are available:
 * - $mp_id: Membership payment ID
 * - $due: Amount due
 * - $session: User session data
 * - $this: Controller instance with GYMFunction component
 */

// Ensure this file is included within a controller context
if (!isset($this) || !isset($mp_id) || !isset($due) || !isset($session)) {
    echo "Error: This template must be included from a controller context.";
    return;
}

// Access controller instance
$controller = $this;
?>
<script>
    $(document).ready(function(){
        var due = "<?php echo $due; ?>";
        $(".validateForm").validationEngine(); /* {binded:false} */	
        $('#submit_button').addClass('submit_button');
        var amount = document.getElementById('txt_amount');
        if(amount == null)
        {
            $(".ModalContainer").hide();
        }
        
        // Show/hide receipt photo upload field based on payment method
        $("#payment_method").on('change', function() {
            var paymentMethod = $(this).val();
              
            if (paymentMethod === 'Efectivo' || paymentMethod === 'Transferencia') {
                $(".receipt-photo-container").show();
            } else {
                $(".receipt-photo-container").hide();
            }
        });
        
        // Trigger change event to initialize visibility
        $("#payment_method").trigger('change');
        
        $('.submit_button').on('click',function(){
            var enteredAmount = $("#amount").val();
            var regex = /^\d*[.]?\d*$/;
          
            if(regex.test(enteredAmount)) {	
                if(enteredAmount == 0) {
                    var message = "<?php echo __("Amount Should Not Enter 0"); ?>";
                    $("#amountError").html(message);
                }else {
                    if(enteredAmount > 1) {
                        if(parseFloat(enteredAmount) > parseFloat(due)) {
                            var message = "<?php echo __("Amount Should Not greater Than Due Amount"); ?>";
                            $("#amountError").html(message);
                        }else {
                            // Validate receipt photo for Efectivo and Transferencia
                            var paymentMethod = $("#payment_method").val();
                            if ((paymentMethod === 'Efectivo' || paymentMethod === 'Transferencia') && 
                                !$("#receipt_photo").val() && 
                                $("#receipt_photo").is(":visible")) {
                                var message = "<?php echo __("La foto del comprobante es obligatoria"); ?>";
                                $("#photoError").html(message);
                            } else {
                                $("#photoError").html("");
                                 // Deshabilitar el botón y mostrar el loader
                                $(this).prop('disabled', true).val("<?php echo __('Cargando...'); ?>");
                                $("#payment-form").submit();
                            }
                        }
                    }else {
                        var message = "<?php echo __("Amount Should Not Less Than 1"); ?>";
                        $("#amountError").html(message);
                    }
                }
            }else {
                var message = "<?php echo __("Amount is not valid.Please Entered Only Number."); ?>";
                $("#amountError").html(message);
            }
        });
    });		
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="gridSystemModalLabel"><?php echo __("Add Payment");?></h4>
</div>
<div class="modal-body">
    <div class="modal-header" style="border: 0px;">
    </div>		
    <form name="expense_form" action="" method="post" class="form-horizontal validateForm" id="payment-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="gmgt_member_add_payment">
        <input type="hidden" name="mp_id" value="<?php echo $mp_id;?>">
        <input type="hidden" name="created_by" value="<?php echo $session["id"];?>">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="amount"><?php echo __("Paid Amount");?><span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $controller->GYMFunction->get_currency_symbol();?></span>
                    <input id="amount" class="form-control validate[required] text-input" type="text" value="<?php echo $due; ?>" name="amount" id='txt_amount' >
                </div>
                <div id="amountError" style="color:red;font-size:15px;"></div>	
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="payment_method"><?php echo __("Payment By");?><span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <select name="payment_method" required="true" id="payment_method" class="form-control">
                    <option value="Efectivo"><?php echo __("Efectivo");?></option>
                    <option value="Transferencia"><?php echo __("Transferencia");?></option>
                    <option value="Pago Online" ><?php echo __("Pago Online");?></option>
                </select>
            </div>
        </div>
        <div class="form-group receipt-photo-container" style="display:none;">
            <label class="col-sm-3 control-label" for="receipt_photo"><?php echo __("Foto del comprobante");?><span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input type="file" id="receipt_photo" name="receipt_photo" class="form-control" accept="image/*">
                <div id="photoError" style="color:red;font-size:15px;"></div>
                <p class="help-block"><?php echo __("Sube una foto de tu comprobante de pago"); ?></p>
            </div>
        </div>
        <div class="col-sm-offset-2 col-sm-8">
            <input type="button" value="<?php echo __("Add Payment");?>" name="add_fee_payment" class="btn btn-flat btn-success payment" id='submit_button'>
        </div>
    </form>		
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal"><?php echo __("Close");?></button>				
</div>
