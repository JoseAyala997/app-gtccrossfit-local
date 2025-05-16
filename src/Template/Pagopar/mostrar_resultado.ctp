<?php
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= __('Resultado del Pago') ?></h3>
                    </div>
                    <div class="box-body">
                        <?php if (isset($resultado)): ?>
                            <div class="alert alert-success">
                                <strong><?= __('Estado del Pago:') ?></strong> <?= $resultado['pagado'] ? __('Pagado') : __('Pendiente'); ?>
                            </div>
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th><?= __('Forma de Pago') ?></th>
                                        <td><?= h($resultado['forma_pago']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Fecha de Pago') ?></th>
                                        <td><?= h($resultado['fecha_pago']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Monto') ?></th>
                                        <td>Gs. <?= h($resultado['monto']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Fecha Máxima de Pago') ?></th>
                                        <td><?= h($resultado['fecha_maxima_pago']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Número de Pedido') ?></th>
                                        <td><?= h($resultado['numero_pedido']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Mensaje') ?></th>
                                        <td><?= $resultado['mensaje_resultado_pago']['descripcion']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <?= __('No se encontraron detalles del pago.') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>