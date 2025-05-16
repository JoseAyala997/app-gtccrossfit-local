<?php

/**
 * @var \App\View\AppView $this
 * @var array $classes Available classes
 */
$session = $this->request->session()->read("User");
$dtp_lang = $this->Gym->getSettings("datepicker_lang");
echo $this->Html->script("jQueryUI/ui/i18n/datepicker-{$dtp_lang}.js");
?>
<section class="content">
    <br>
    <div class="col-md-12 box box-default">
        <div class="box-header">
            <section class="content-header">
                <h1>
                    <i class="fa fa-calendar"></i>
                    <?= __('Reserve una clase') ?>
                </h1>
                <ol class="breadcrumb">
                    <a href="<?= $this->Gym->createurl("ClassBooking", "bookingList") ?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?= __("Reservas") ?></a>
                </ol>
            </section>
        </div>
        <hr>
        <!-- Membership Status Information -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-default">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><?= __('Estado de Membresía') ?></h4>
                                <?php if ($membershipLimitStatus['limit_type'] === 'NONE'): ?>
                                    <p><i class="fa fa-exclamation-triangle"></i> <?= $membershipLimitStatus['message'] ?></p>
                                <?php elseif ($membershipLimitStatus['limit_type'] === 'UNLIMITED'): ?>
                                    <p><i class="fa fa-check-circle"></i> <?= $membershipLimitStatus['message'] ?></p>
                                <?php else: ?>
                                    <p><i class="fa fa-check-circle"></i> <?= $membershipLimitStatus['message'] ?></p>
                                    <!-- <p>
                                        <i class="fa fa-calendar-check-o"></i> 
                                        <strong><?= __('Clases disponibles') ?>:</strong> 
                                        <?= $membershipLimitStatus['days_total'] - $membershipLimitStatus['days_used'] ?>/<?= $membershipLimitStatus['days_total'] ?> 
                                        <?= $membershipLimitStatus['period'] === 'per_week' ? __('esta semana') : __('este mes') ?>
                                    </p> -->
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h4><?= __('Créditos para Otras Sedes') ?></h4> 

                                <?php if (empty($branchCredits)): ?>

                                    <p><?= __('No hay otras sedes disponibles') ?></p>
                                <?php else: ?>
                                    <ul class="list-unstyled">
                                        <?php foreach ($branchCredits as $credit): ?>
                                            <li>
                                                <i class="fa fa-map-marker"></i>
                                                <strong><?= h($credit['branch_name']) ?>:</strong>
                                                <?= $credit['credits'] ?> <?= __('créditos') ?>
                                                <?php
                                                $fecha = $credit['expiry_date'];
                                                if ($fecha instanceof \Cake\I18n\Time || $fecha instanceof \DateTime) {
                                                    $fechaStr = $fecha->format('d/m/Y');
                                                } elseif (!empty($fecha) && strtotime($fecha) > 0) {
                                                    $fechaStr = date('d/m/Y', strtotime($fecha));
                                                } else {
                                                    $fechaStr = null;
                                                }
                                                ?>
                                                <?php if ($fechaStr): ?>
                                                    <small>(<?= __('Vence') ?>: <?= $fechaStr ?>)</small>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <!-- Date Selection -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= __('Select Date') ?></label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepick" id="schedule_date" value="<?= $selectedDate ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" id="load_schedule"><?= __('Load Schedule') ?></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Schedule View -->
            <div class="row">
                <div class="col-md-12">
                    <div id="schedule_view">
                        <h4><?= __('Clases para') ?> <?= date('l, F j, Y', strtotime($selectedDate)) ?></h4>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?= __('Class') ?></th>
                                    <th><?= __('Time') ?></th>
                                    <th><?= __('Location') ?></th>
                                    <th><?= __('Disponibilidad') ?></th>
                                    <th><?= __('Acciones') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($classes)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center"><?= __('No hay clases disponibles para esta fecha') ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($classes as $class):
                                        $availability = $class->available_spots ?? 0;
                                        $total = $class->max_quota ?? 0;
                                        $availabilityClass = ($availability > 0) ? 'success' : 'danger';
                                    ?>
                                        <tr class="<?= $availabilityClass ?>">
                                            <td><?= h($class->class_name) ?></td>
                                            <td><?= h($class->start_time) ?> - <?= h($class->end_time) ?></td>
                                            <td><?= h($class->location) ?></td>
                                            <td>
                                                <span class="label label-<?= $availabilityClass ?>">
                                                    <?= __('Lugares disponibles: {0}/{1}', $availability, $total) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($availability > 0): ?>
                                                    <button type="button" class="btn btn-sm btn-primary select-class"
                                                        data-id="<?= $class->id ?>"
                                                        data-name="<?= h($class->class_name) ?>"
                                                        data-time="<?= h($class->start_time) ?> - <?= h($class->end_time) ?>">
                                                        <?= __('Select') ?>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-default" disabled>
                                                        <?= __('Full') ?>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Booking Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" style="float:none;"><?= __('Reservar clase seleccionada') ?></h3>
                        </div>
                        <div class="panel-body">
                            <?php echo $this->Form->create("addgroup", ["class" => "validateForm form-horizontal", "role" => "form"]); ?>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="selected_class_name"><?= __('Clase seleccionada') ?></label>
                                <div class="col-md-6">
                                    <input type="hidden" name="class_id" id="selected_class_id">
                                    <input type="text" class="form-control" id="selected_class_name" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="booking_date"><?= __('Booking Date') ?></label>
                                <div class="col-md-6">
                                    <input type="text" name="booking_date" class="form-control datepick" id="booking_date" value="<?= $selectedDate ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-6">
                                    <button type="submit" id="book_button" class="btn btn-flat btn-success" disabled>
                                        <i class="fa fa-calendar-check-o"></i> <?= __('Reservar clase') ?>
                                    </button>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Initialize datepicker with consistent format
        $(".datepick").datepicker({
            yearRange: "+0:+1",
            changeYear: true,
            changeMonth: true,
            dateFormat: "<?php echo $this->Gym->dateformat_PHP_to_jQueryUI($this->Gym->getSettings('date_format')); ?>",
            language: "<?php echo $dtp_lang; ?>"
        });

        // Load schedule for selected date
        $('#load_schedule').on('click', function() {
            var date = $('#schedule_date').val();
            if (date) {
                // Convert date to YYYY-MM-DD format for the URL
                var formattedDate = $.datepicker.formatDate('yy-mm-dd', $.datepicker.parseDate(
                    "<?php echo $this->Gym->dateformat_PHP_to_jQueryUI($this->Gym->getSettings('date_format')); ?>",
                    date
                ));
                window.location.href = "<?= $this->Url->build(['action' => 'addBooking']) ?>?date=" + formattedDate;
            } else {
                alert("<?= __('Please select a date') ?>");
            }
        });

        // Select class for booking
        $('.select-class').on('click', function(e) {
            e.preventDefault();
            var classId = $(this).data('id');
            var className = $(this).data('name');
            var classTime = $(this).data('time');

            $('#selected_class_id').val(classId);
            $('#selected_class_name').val(className + ' - ' + classTime);
            $('#booking_date').val($('#schedule_date').val());
            $('#book_button').prop('disabled', false);
        });
    });
</script>