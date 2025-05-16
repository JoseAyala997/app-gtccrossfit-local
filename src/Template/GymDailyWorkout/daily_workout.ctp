<?php 
$session = $this->request->getSession()->read("User");
echo $this->Html->css([
    'select2',
    'bootstrap.min',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
]);
echo $this->Html->script(['select2.min']);

// Arrays para fechas en español
$diasSemana = [
    'Sunday' => 'Domingo',
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado'
];

$meses = [
    '01' => 'Enero',
    '02' => 'Febrero',
    '03' => 'Marzo',
    '04' => 'Abril',
    '05' => 'Mayo',
    '06' => 'Junio',
    '07' => 'Julio',
    '08' => 'Agosto',
    '09' => 'Septiembre',
    '10' => 'Octubre',
    '11' => 'Noviembre',
    '12' => 'Diciembre'
];
?>

<style>
    .daily-workout-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .header-controls {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .date-display {
        font-size: 24px;
        font-weight: bold;
        color: #495057;
    }

    .controls {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .sucursal-select {
        min-width: 200px;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #fff !important;
        color: #495057 !important;
        font-size: 16px;
    }

    .date-input {
        width: 160px;
        height: 38px;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 16px;
    }

    .workout-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    /* Headers según el rol */
    .workout-header.member-header {
        background: #fff3cd;
        color: #856404;
        border-bottom: 1px solid #ffeeba;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        font-size: 18px;
        font-weight: 600;
    }

    .workout-header.coach-header {
        background: #d4edda;
        color: #155724;
        border-bottom: 1px solid #c3e6cb;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        font-size: 18px;
        font-weight: 600;
    }

    .workout-content {
        padding: 20px;
    }

    /* Notas */
    .client-notes {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        color: #856404;
    }

    .coach-notes {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        color: #155724;
    }

    /* Componentes */
    .component {
        margin-bottom: 12px;
    }

    .component-header {
        background-color: #dc3545;
        color: white;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 1.8rem;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
    }

    .component-activity {
        color: rgba(255, 255, 255, 0.9);
        font-style: italic;
    }

    .component-description {
        font-size: 1.8rem;
        color: #212529;
        padding: 0 8px;
    }

    @media (max-width: 768px) {
        .header-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .controls {
            flex-direction: column;
            width: 100%;
        }

        .sucursal-select,
        .date-input {
            width: 100%;
            margin-bottom: 10px;
        }

        .date-display {
            text-align: center;
            margin-bottom: 20px;
        }
    }
</style>

<div class="daily-workout-container">
    <!-- Header con fecha y controles -->
    <div class="header-controls">
        <div class="date-display">
            <?php
                $nombreDia = $diasSemana[$date->format('l')];
                echo "{$nombreDia}, {$date->format('d')} de {$meses[$date->format('m')]} del {$date->format('Y')}";
            ?>
        </div>
        <div class="controls">
            <select class="form-control sucursal-select">
                <?php foreach ($branches as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $selected_branch == $id ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" 
                   class="form-control date-input" 
                   value="<?= $date->format('Y-m-d') ?>">
        </div>
    </div>

    <?php if ($workout): ?>
        <?php if ($isMember): ?>
            <div class="workout-card">
                <div class="workout-header member-header">
                    Workout del día 
                </div>
                <div class="workout-content">
                    <?php if (!empty($workout->description)): ?>
                        <div class="client-notes">
                            <?= $this->Html->div(null, $workout->description, ['escape' => false]) ?>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($workout->components as $component): ?>
                        <div class="component">
                            <div class="component-header">
                                <?= h($component->category->name) ?>
                                <?php if (!empty($component->activity)): ?>
                                    <span class="component-activity">
                                        (<?= h($component->activity->title) ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="component-description">
                                <?= $this->Html->div(null, $component->description, ['escape' => false]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isStaffMember): ?>
            <div class="workout-card">
                <div class="workout-header coach-header">
                    Notas para Entrenadores
                </div>
                <div class="workout-content">
                    <?php if (!empty($workout->coach_notes)): ?>
                        <div class="coach-notes">
                            <?= $this->Html->div(null, $workout->coach_notes, ['escape' => false]) ?>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($workout->components as $component): ?>
                        <div class="component">
                            <div class="component-header">
                                <?= h($component->category->name) ?>
                                <?php if (!empty($component->activity)): ?>
                                    <span class="component-activity">
                                        (<?= h($component->activity->title) ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="component-description">
                                <?= $this->Html->div(null, $component->description, ['escape' => false]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            No hay workout programado para este día.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('.sucursal-select').select2({
            placeholder: 'Seleccionar sucursal',
            width: '100%'
        });

        // Manejo de cambio de sucursal
        $('.sucursal-select').on('change', function() {
            const branchId = $(this).val();
            const currentDate = $('.date-input').val();
            window.location.href = '<?= $this->Url->build(['action' => 'dailyWorkout']) ?>' +
                                 '?date=' + currentDate + '&branch=' + branchId;
        });

        // Manejo de cambio de fecha
        $('.date-input').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                const branchId = $('.sucursal-select').val();
                window.location.href = '<?= $this->Url->build(['action' => 'dailyWorkout']) ?>' +
                                     '?date=' + selectedDate + '&branch=' + branchId;
            }
        });
    });
</script>