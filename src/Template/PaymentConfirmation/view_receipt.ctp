<?php
/**
 * View Receipt Template
 * 
 * Displays the uploaded receipt photo for a payment
 */
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Comprobante de pago') ?></h3>
                        <div class="box-tools pull-right">
                            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> <?= __('Volver a la lista') ?>
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" style="float:none;"><?= __('Detalles del pago') ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <th><?= __('Member') ?></th>
                                                <td><?= h($member->first_name . ' ' . $member->last_name) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= __('Fecha de pago') ?></th>
                                                <td><?= h($paymentHistory->paid_by_date->format('Y-m-d')) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= __('Amount') ?></th>
                                                <td><?= $this->Gym->get_currency_symbol() ?> <?= h($paymentHistory->amount) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= __('Método de pago') ?></th>
                                                <td><?= h($paymentHistory->payment_method) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= __('Status') ?></th>
                                                <td>
                                                    <?php if ($paymentHistory->payment_confirmation_status === 'Pending'): ?>
                                                        <span class="label label-warning"><?= __('Pending') ?></span>
                                                    <?php elseif ($paymentHistory->payment_confirmation_status === 'Confirmed'): ?>
                                                        <span class="label label-success"><?= __('Confirmed') ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-danger"><?= __('Rejected') ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($paymentHistory->confirmed_by)): ?>
                                            <tr>
                                                <th><?= __('Confirmed By') ?></th>
                                                <td><?= h($paymentHistory->confirmed_by) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= __('Confirmed Date') ?></th>
                                                <td><?= h($paymentHistory->confirmed_date->format('Y-m-d H:i:s')) ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($paymentHistory->confirmation_note)): ?>
                                            <tr>
                                                <th><?= __('Note') ?></th>
                                                <td><?= h($paymentHistory->confirmation_note) ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" style="float:none;"><?= __('Comprobante de pago') ?></h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        <?php if (!empty($paymentHistory->receipt_photo)): ?>
                                            <?= $this->Html->image('/'. $paymentHistory->receipt_photo, [
                                                'class' => 'img-responsive',
                                                'alt' => __('Foto del comprobante'),
                                                'style' => 'max-width: 100%;'
                                            ]) ?>
                                            <br>
                                            <div class="margin-top-10">
                                                <?= $this->Html->link(
                                                    '<i class="fa fa-external-link"></i> ' . __('Ver tamaño completo'),
                                                    '/' . $paymentHistory->receipt_photo,
                                                    [
                                                        'class' => 'btn btn-primary',
                                                        'target' => '_blank',
                                                        'escape' => false
                                                    ]
                                                ) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <?= __('No se ha enviado un comprobante para este pago.') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($paymentHistory->payment_confirmation_status === 'Pending'): ?>
                        <div class="row margin-top-20">
                            <div class="col-md-12 text-center">
                                <div class="btn-group" style="display:flex; justify-content: space-around; margin-left: 25%; margin-right: 25%;">
                                    <button type="button" class="btn btn-success confirm-payment" data-id="<?= $paymentHistory->id ?>" data-status="Confirmed">
                                        <i class="fa fa-check"></i> <?= __('Confirmar pago') ?>
                                    </button>
                                    <span style="width: 200px;"></span>
                                    <button type="button" class="btn btn-danger reject-payment" data-id="<?= $paymentHistory->id ?>" data-status="Rejected">
                                        <i class="fa fa-times"></i> <?= __('Rechazar pago') ?>
                                    </button>
                                </div>
                            </div>
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
                <h4 class="modal-title" id="confirmationModalLabel"><?= __('Confirm Payment') ?></h4>
            </div>
            <div class="modal-body">
                <form id="confirmationForm">
                    <input type="hidden" id="payment_history_id" name="payment_history_id">
                    <input type="hidden" id="confirmation_status" name="confirmation_status">
                    
                    <div class="form-group">
                        <label for="confirmation_note"><?= __('Note (Optional)') ?></label>
                        <textarea class="form-control" id="confirmation_note" name="confirmation_note" rows="3"></textarea>
                    </div>
                </form>
                <div id="confirmationMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="submitConfirmation"><?= __('Submit') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle confirm button click
    $('.confirm-payment').on('click', function() {
        var paymentId = $(this).data('id');
        var status = $(this).data('status');
        
        $('#payment_history_id').val(paymentId);
        $('#confirmation_status').val(status);
        $('#confirmationModalLabel').text('<?= __('Confirm Payment') ?>');
        $('#submitConfirmation').removeClass('btn-danger').addClass('btn-success');
        $('#confirmationModal').modal('show');
    });
    
    // Handle reject button click
    $('.reject-payment').on('click', function() {
        var paymentId = $(this).data('id');
        var status = $(this).data('status');
        
        $('#payment_history_id').val(paymentId);
        $('#confirmation_status').val(status);
        $('#confirmationModalLabel').text('<?= __('Reject Payment') ?>');
        $('#submitConfirmation').removeClass('btn-success').addClass('btn-danger');
        $('#confirmationModal').modal('show');
    });
    
    // Handle form submission
    $('#submitConfirmation').on('click', function() {
        var formData = {
            payment_history_id: $('#payment_history_id').val(),
            confirmation_status: $('#confirmation_status').val(),
            confirmation_note: $('#confirmation_note').val()
        };
        
        $.ajax({
            url: '<?= $this->Url->build(['action' => 'updateStatus']) ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#confirmationMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() {
                        window.location.href = '<?= $this->Url->build(['action' => 'index']) ?>';
                    }, 1500);
                } else {
                    $('#confirmationMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#confirmationMessage').html('<div class="alert alert-danger"><?= __('An error occurred. Please try again.') ?></div>');
            }
        });
    });
});
</script>
