<?php
/**
 * Payment History Template
 * 
 * Shows the history of all payments and their confirmation status
 */
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Historial de Pagos') ?></h3>
                        
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
                    </div>
                    <div class="box-body">
                        <?php if (empty($paymentHistory)): ?>
                            <div class="alert alert-info">
                                <?= __('No payment history found.') ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="payment-history-table">
                                    <thead>
                                        <tr>
                                            <th><?= __('Member') ?></th>
                                            <th><?= __('Membership') ?></th>
                                            <th><?= __('Amount') ?></th>
                                            <th><?= __('Method') ?></th>
                                            <th><?= __('Payment Date') ?></th>
                                            <th><?= __('Created By') ?></th>
                                            <th><?= __('Status') ?></th>
                                            <th><?= __('Confirmed By') ?></th>
                                            <th><?= __('Confirmation Date') ?></th>
                                            <th><?= __('Receipt') ?></th>
                                            <th><?= __('Notes') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paymentHistory as $payment): ?>
                                            <tr>
                                                <td>
                                                    <?= h($payment->membership_payment->gym_member->first_name . ' ' . $payment->membership_payment->gym_member->last_name) ?>
                                                </td>
                                                <td>
                                                    <?= h($payment->membership_payment->membership->membership_label) ?>
                                                </td>
                                                <td>
                                                    <?= $this->Gym->get_currency_symbol() ?> <?= h($payment->amount) ?>
                                                </td>
                                                <td><?= h($payment->payment_method) ?></td>
                                                <td>
                                                    <?= h($payment->paid_by_date->format('Y-m-d')) ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($payment->creator)) {
                                                        echo h($payment->creator->first_name . ' ' . $payment->creator->last_name);
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (empty($payment->payment_confirmation_status)) {
                                                        echo '<span class="label label-default">' . __('N/A') . '</span>';
                                                    } elseif ($payment->payment_confirmation_status == 'Confirmed') {
                                                        echo '<span class="label label-success">' . __('Confirmed') . '</span>';
                                                    } elseif ($payment->payment_confirmation_status == 'Rejected') {
                                                        echo '<span class="label label-danger">' . __('Rejected') . '</span>';
                                                    } else {
                                                        echo '<span class="label label-warning">' . h($payment->payment_confirmation_status) . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($payment->confirmer)) {
                                                        echo h($payment->confirmer->first_name . ' ' . $payment->confirmer->last_name);
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($payment->confirmed_date)) {
                                                        echo h($payment->confirmed_date->format('Y-m-d H:i'));
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($payment->receipt_photo)): ?>
                                                        <a href="<?= $this->Url->build(['action' => 'viewReceipt', $payment->payment_history_id]) ?>" class="btn btn-info btn-xs" target="_blank">
                                                            <i class="fa fa-eye"></i> <?= __('View') ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($payment->confirmation_note)) {
                                                        echo '<button type="button" class="btn btn-default btn-xs view-note" data-toggle="modal" data-target="#noteModal" data-note="' . h($payment->confirmation_note) . '"><i class="fa fa-sticky-note"></i> ' . __('View') . '</button>';
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
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

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="noteModalLabel"><?= __('Confirmation Note') ?></h4>
            </div>
            <div class="modal-body">
                <p id="note-content"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTables para mejor ordenamiento y filtrado
    $('#payment-history-table').DataTable({
        "responsive": true,
        "order": [[ 4, "desc" ]], // Ordenar por fecha de pago descendente
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
    
    // Manejar cambio de filtro de sucursal
    $('#branch_filter').on('change', function() {
        var branchId = $(this).val();
        
        // Redireccionar con el parámetro de sucursal
        window.location.href = '<?= $this->Url->build(['action' => 'paymentHistory']) ?>' + 
            (branchId ? '?branch_id=' + branchId : '');
    });
    
    // Manejar visualización de notas
    $('.view-note').on('click', function() {
        var note = $(this).data('note');
        $('#note-content').text(note);
    });
});
</script>