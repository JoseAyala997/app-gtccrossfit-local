<section class="content">
    <br>
    <div class="col-md-12 box box-default">
        <div class="box-header">
            <section class="content-header">
                <h1>
                    <i class="fa fa-ticket"></i>
                    <?= __("Creditos disponibles y usados") ?>
                </h1>
            </section>
        </div>
        <hr>
        <div class="box-body">
            <?php if (!empty($branchCredits)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4><?= __('Creditos disponibles por sucursal') ?></h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?= __('Sucursal') ?></th>
                                        <th><?= __('Creditos restantes') ?></th>
                                        <th><?= __('Última actualización') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($branchCredits as $branchCredit): ?>
                                        <tr>
                                            <td><?= h($branchCredit->gym_branch->name ?? __('N/A')) ?></td>
                                            <td><?= h($branchCredit->credits_remaining ?? __('N/A')) ?></td>
                                            <td><?= h($branchCredit->updated_at ? $branchCredit->updated_at->format('Y-m-d H:i:s') : __('N/A')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <?= __('No se encontraron créditos disponibles para este miembro.') ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($creditUsage)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4><?= __('Historial de uso de créditos') ?></h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?= __('Fecha de uso') ?></th>
                                        <th><?= __('Clase') ?></th>
                                        <th><?= __('Sucursal') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($creditUsage as $usage): ?>
                                        <tr>
                                            <td><?= h($usage->used_at->format('Y-m-d H:i:s')) ?></td>
                                            <td><?= h($usage->class_schedule->class_name ?? __('N/A')) ?></td>
                                            <td><?= h($usage->gym_branch->name ?? __('N/A')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <?= __('No se encontró historial de uso de créditos.') ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>