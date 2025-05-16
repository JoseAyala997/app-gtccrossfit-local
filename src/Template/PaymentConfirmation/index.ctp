<?php
/**
 * Payment Confirmation Index Template
 * 
 * Lists pending payments that need admin confirmation
 */
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Pagos pendientes de confirmación') ?></h3>
                    </div>
                        <!-- Filtro de sucursal -->
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="branch_filter"><?= __('Filtrar por Sucursal:') ?></label>
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
                    <div class="box-body">
                        <?php if (empty($pendingPayments)): ?>
                            <div class="alert alert-info">
                                <?= __('No pending payments to confirm.') ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><?= __('ID') ?></th>
                                            <th><?= __('Member') ?></th>
                                            <th><?= __('Payment Date') ?></th>
                                            <th><?= __('Amount') ?></th>
                                            <th><?= __('Payment Method') ?></th>
                                            <th><?= __('Receipt') ?></th>
                                            <th><?= __('Actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingPayments as $payment): ?>
                                            <tr>
                                            <td>
                                                    <?= h($payment->member->id) ?>
                                                </td>
                                                <td>
                                                    <?= h($payment->member->first_name . ' ' . $payment->member->last_name) ?>
                                                </td>
                                                <td>
                                                    <?= h($payment->paid_by_date->format('Y-m-d')) ?>
                                                </td>
                                                <td>
                                                    <?= $this->Gym->get_currency_symbol() ?> <?= h($payment->amount) ?>
                                                </td>
                                                <td><?= h($payment->payment_method) ?></td>
                                                <td>
                                                    <?php if (!empty($payment->receipt_photo)): ?>
                                                        <a href="<?= $this->Url->build(['action' => 'viewReceipt', $payment->payment_history_id]) ?>" class="btn btn-info btn-xs" target="_blank">
                                                            <i class="fa fa-eye"></i> <?= __('View Receipt') ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="label label-warning"><?= __('No Receipt') ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-xs confirm-payment" 
                                                            data-payment-id="<?= $payment->payment_history_id ?>" 
                                                            data-toggle="modal" data-target="#confirmationModal" 
                                                            data-action="confirm">
                                                        <i class="fa fa-check"></i> <?= __('Confirm') ?>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs confirm-payment" 
                                                            data-payment-id="<?= $payment->payment_history_id ?>" 
                                                            data-toggle="modal" data-target="#confirmationModal" 
                                                            data-action="reject">
                                                        <i class="fa fa-times"></i> <?= __('Reject') ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="confirmation-title"><?= __('Confirm Payment') ?></h4>
            </div>
            <div class="modal-body">
                <form id="confirmationForm">
                    <input type="hidden" id="payment-id" name="payment_id">
                    <input type="hidden" id="action-type" name="action_type">
                    
                    <div class="form-group">
                        <label for="confirmation-note"><?= __('Note (Optional)') ?></label>
                        <textarea class="form-control" id="confirmation-note" name="confirmation_note" rows="3"></textarea>
                    </div>
                </form>
                <div id="confirmation-message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="confirm-button"><?= __('Submit') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle confirm button click
    $('.confirm-payment').click(function() {
        var paymentId = $(this).data('payment-id');
        var action = $(this).data('action');
        
        $('#payment-id').val(paymentId);
        $('#action-type').val(action);
        
        if (action === 'confirm') {
            $('#confirmation-title').text('<?= __('Confirm Payment') ?>');
            $('#confirmation-message').text('<?= __('Are you sure you want to confirm this payment?') ?>');
            $('#confirm-button').text('<?= __('Confirm') ?>').removeClass('btn-danger').addClass('btn-success');
        } else {
            $('#confirmation-title').text('<?= __('Reject Payment') ?>');
            $('#confirmation-message').text('<?= __('Are you sure you want to reject this payment?') ?>');
            $('#confirm-button').text('<?= __('Reject') ?>').removeClass('btn-success').addClass('btn-danger');
        }
    });
    
    // Handle form submission
    $('#confirm-button').on('click', function() {
    var formData = {
        payment_history_id: $('#payment-id').val(),
        confirmation_status: $('#action-type').val() === 'confirm' ? 'Confirmed' : 'Rejected',
        confirmation_note: $('#confirmation-note').val()
    };
    
    $.ajax({
        url: '<?= $this->Url->build(['action' => 'updateStatus']) ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                $('#confirmation-message').html('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                $('#confirmation-message').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr, status, error) {
            // Mostrar el mensaje de error del servidor
            var errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                ? xhr.responseJSON.message 
                : '<?= __('An unexpected error occurred. Please try again.') ?>';
            $('#confirmation-message').html('<div class="alert alert-danger">' + errorMessage + '</div>');
        }
    });
});
     //  Manejar cambio de filtro de sucursal
     $('#branch_filter').on('change', function() {
        var branchId = $(this).val();
        
        // Redireccionar con el parámetro de sucursal
        window.location.href = '<?= $this->Url->build(['action' => 'index']) ?>' + 
            (branchId ? '?branch_id=' + branchId : '');
    });
});
</script>
