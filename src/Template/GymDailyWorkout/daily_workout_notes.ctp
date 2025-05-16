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
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .header-controls {
        background: white;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
    }

    .date-display {
        font-size: 24px;
        font-weight: bold;
        color: #495057;
    }

    .controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sucursal-select {
        width: 180px;
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

    .btn-agregar {
        height: 38px;
        padding: 0.375rem 1rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.2s;
        white-space: nowrap;
    }

    .btn-agregar:hover {
        background-color: #218838;
    }

    .workout-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 0;
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
        padding: 25px;
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

    /* Mejoras para la alineación en la vista actual */
    .controls-wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .select2-container {
        vertical-align: middle;
    }

    .input-group {
        display: inline-flex;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .header-controls {
            flex-direction: column;
            align-items: stretch;
            padding: 15px;
        }

        .controls-wrapper {
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }

        .controls {
            width: 100%;
            justify-content: space-between;
        }

        .sucursal-select {
            width: 100%;
        }

        .date-display {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-agregar[disabled] {
            cursor: not-allowed;
            opacity: 0.8;
        }

        .btn-agregar[disabled]:hover {
            background-color: #dc3545;
            /* Mantiene el color rojo para el botón deshabilitado */
        }

        /* Agregar tooltip para mejor UX */
        .btn-agregar[disabled] {
            position: relative;
        }

        .btn-agregar[disabled]:hover:after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            margin-bottom: 5px;
        }

        .workout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #212529;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
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
        <div class="controls-wrapper">
            <div class="controls">
                <input type="date"
                    class="form-control date-input"
                    value="<?= $date->format('Y-m-d') ?>">

                <?php if ($buttonState['canAdd']): ?>
                    <!-- Puede agregar nota (es hoy y no existe nota) -->
                    <button class="btn-agregar" id="btnAddCalendarWorkout">
                        <i class="fas fa-plus"></i> Agregar Nota
                    </button>
                <?php elseif ($buttonState['hasNote']): ?>
                    <!-- Ya existe una nota para este día -->
                    <button class="btn-agregar" disabled style="background-color: #6c757d;">
                        <i class="fas fa-plus"></i> Ya existe nota
                    </button>
                <?php else: ?>
                    <!-- No se puede agregar nota (no es el día actual) -->
                    <button class="btn-agregar" disabled style="background-color: #dc3545;">
                        <i class="fas fa-ban"></i> Solo se permiten notas del día actual
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Agregar debug temporal -->
    <div style="display: none;">
        <?php debug($notesByDate[$date->format('Y-m-d')]); ?>
    </div>

    <!-- Modificar la sección de notas -->
    <?php if (isset($notesByDate[$date->format('Y-m-d')])): ?>
        <?php if ($isMember): ?>
            <div class="workout-card">
                <div class="workout-header member-header d-flex justify-content-between align-items-center">
                    <span>Notas del día</span>
                    <?php if ($buttonState['hasNote']): ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-edit"></i> Editar Nota',
                            ['action' => 'editnota', '?' => ['date' => $date->format('Y-m-d')]],
                            [
                                'class' => 'btn btn-warning btn-sm',
                                'escape' => false
                            ]
                        ) ?>
                    <?php endif; ?>
                </div>
                <div class="workout-content">
                    <?php foreach ($notesByDate[$date->format('Y-m-d')]->note_details as $detail): ?>
                        <div class="component">
                            <div class="component-header">
                                <?= h($detail->Category['name']) // Categoría 
                                ?>
                                <?php if (!empty($detail->Activity['title'])): // Actividad 
                                ?>
                                    <span class="component-activity">
                                        (<?= h($detail->Activity['title']) ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="component-description">
                                <?= $this->Html->div(null, $detail->NoteDetails['description'], ['escape' => false]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            No hay ninguna nota para este día.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('.sucursal-select').select2({
            placeholder: 'Seleccionar sucursal',
            width: '100%',
            minimumResultsForSearch: Infinity, // Oculta la búsqueda para un aspecto más limpio
            dropdownCssClass: 'select2-dropdown-aligned' // Clase personalizada para el dropdown
        });

        // Manejo de cambio de sucursal
        $('.sucursal-select').on('change', function() {
            const branchId = $(this).val();
            const currentDate = $('.date-input').val();
            window.location.href = '<?= $this->Url->build(['action' => 'dailynote']) ?>' +
                '?date=' + currentDate + '&branch=' + branchId;
        });

        // Manejo de cambio de fecha
        $('.date-input').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                const branchId = $('.sucursal-select').val();
                window.location.href = '<?= $this->Url->build(['action' => 'dailynote']) ?>' +
                    '?date=' + selectedDate + '&branch=' + branchId;
            }
        });

        // Manejo del botón agregar
        $('#btnAddCalendarWorkout').on('click', function() {
            window.location.href = '<?= $this->Url->build(['controller' => 'GymDailyWorkout', 'action' => 'addnota']) ?>';
        });

        // Asegurarse de que el select2 esté correctamente alineado
        $(window).on('resize', function() {
            $('.sucursal-select').select2('destroy').select2({
                placeholder: 'Seleccionar sucursal',
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        });
    });
</script>