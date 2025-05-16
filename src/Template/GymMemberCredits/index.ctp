<section class="content">
    <br>
    <div class="col-md-12 box box-default">
        <div class="box-header">
            <section class="content-header">
                <h1>
                    <i class="fa fa-ticket"></i>
                    <?php echo __("Manerjar Credito de Miembros"); ?>
                </h1>
            </section>
        </div>
        <hr>
        <div class="box-body">
            <table class="dataTable table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= __('Image') ?></th>
                        <th><?= __('Name') ?></th>
                        <th><?= __('Email') ?></th>
                        <th><?= __('Mobile') ?></th>
                        <th><?= __('Membership Status') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td>
                                <?php if (!empty($member->image)): ?>
                                    <img src="<?= $this->request->webroot . 'webroot/upload/' . $member->image ?>" class="img-circle" alt="Member Image" height="50" width="50">
                                <?php else: ?>
                                    <img src="<?= $this->request->webroot ?>webroot/img/Thumbnail-img.png" class="img-circle" alt="Default Image" height="50" width="50">
                                <?php endif; ?>
                            </td>
                            <td><?= h($member->first_name . ' ' . $member->last_name) ?></td>
                            <td><?= h($member->email) ?></td>
                            <td><?= h($member->mobile) ?></td>
                            <td>
                                <?php if (!empty($member->gym_member_memberships)): ?>
                                    <span class="label label-success"><?= __('ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="label label-danger"><?= __('No Activo Membership') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= $this->Url->build(['action' => 'viewCredits', $member->id]) ?>" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye"></i> <?= __('Ver Creditos') ?>
                                </a>
                                <!-- <a href="<?= $this->Url->build(['action' => 'manageCredits', $member->id]) ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-pencil"></i> <?= __('Manage Credits') ?>
                                </a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Verifica si la tabla ya está inicializada como DataTable
    if (!$.fn.DataTable.isDataTable('.dataTable')) {
        $(".dataTable").DataTable({
            "responsive": true,
            "language": {
                "paginate": {"previous": "«", "next": "»"}
            }
        });
    }
});
</script>