<section class="content">
    <br>
    <div class="col-md-12 box box-default">
        <div class="box-header">
            <section class="content-header">
                <h1>
                    <i class="fa fa-ticket"></i>
                    <?= __('Manage Credits for {0}', h($member->first_name . ' ' . $member->last_name)) ?>
                </h1>
                <ol class="breadcrumb">
                    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-flat btn-custom"><i class="fa fa-arrow-left"></i> <?= __("Back to Members") ?></a>
                    <a href="<?= $this->Url->build(['action' => 'viewCredits', $member->id]) ?>" class="btn btn-flat btn-info"><i class="fa fa-eye"></i> <?= __("View Credits") ?></a>
                </ol>
            </section>
        </div>
        <hr>
        <div class="box-body">
            <?php if ($activeMembership): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-id-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __('Active Membership') ?></span>
                                <span class="info-box-number"><?= h($activeMembership->membership->membership_label ?? 'N/A') ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= __('Valid until') ?> <?= h($activeMembership->end_date->format('Y-m-d')) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-ticket"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __('Current Credits') ?></span>
                                <span class="info-box-number"><?= $memberCredits ? h($memberCredits->credits_remaining) : 0 ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?= __('Adjust Credits') ?></h3>
                            </div>
                            <div class="panel-body">
                                <?= $this->Form->create(null, ['class' => 'form-horizontal']) ?>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?= __('Set Absolute Value') ?></label>
                                        <div class="col-md-6">
                                            <?= $this->Form->control('credits', [
                                                'type' => 'number',
                                                'class' => 'form-control',
                                                'label' => false,
                                                'placeholder' => __('Enter new credit total'),
                                                'min' => 0
                                            ]) ?>
                                            <p class="help-block"><?= __('This will set the credit balance to exactly this number') ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?= __('OR Add/Subtract Credits') ?></label>
                                        <div class="col-md-6">
                                            <?= $this->Form->control('credits_adjustment', [
                                                'type' => 'number',
                                                'class' => 'form-control',
                                                'label' => false,
                                                'placeholder' => __('Enter adjustment amount (positive or negative)'),
                                            ]) ?>
                                            <p class="help-block"><?= __('Use positive numbers to add credits, negative to subtract') ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-offset-3 col-md-6">
                                            <?= $this->Form->button(__('Update Credits'), ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                <?= $this->Form->end() ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4><i class="icon fa fa-warning"></i> <?= __('No Active Membership') ?></h4>
                    <p><?= __('This member does not have an active membership. Credits can only be managed for members with active memberships.') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>