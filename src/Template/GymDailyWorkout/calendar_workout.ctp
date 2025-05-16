<?php
$session = $this->request->getSession()->read("User");

use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;

// Configuración del idioma en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'esp');

// Obtener el mes y año actual
$fecha = FrozenTime::now();
$mes = $fecha->format('m');
$anio = $fecha->format('Y');
$diaActual = $fecha->format('d'); // Para marcar el día actual

// Calcular mes anterior
$mesAnterior = $mes - 1;
$anioMesAnterior = $anio;
if ($mesAnterior == 0) {
    $mesAnterior = 12;
    $anioMesAnterior--;
}
$mesAnterior = str_pad($mesAnterior, 2, '0', STR_PAD_LEFT);

// Array de días en español
$diasSemana = array(
    'Dom',
    'Lun',
    'Mar',
    'Mié',
    'Jue',
    'Vie',
    'Sáb'
);

// Array de meses en español
$meses = array(
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
);

echo $this->Html->css([
    'select2',
    'bootstrap.min',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
]);
echo $this->Html->script([
    'select2.min',
]);
?>

<style>
    /* Estilos generales */
    body {
        background-color: #f0f2f5;
    }

    /* Estilos de la cabecera del calendario - CORREGIDO */
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 0 15px;
    }

    .calendar-header h3 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
        min-width: 160px;
    }

    /* Estilos para el select de sucursal */
    .sucursal-select {
        width: 250px !important;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #fff !important;
        color: #495057 !important;
        font-size: 16px;
        margin: 0 15px;
    }

    /* Para quitar el estilo negro de Select2 */

    .date-selector {
        display: flex;
        align-items: center;
    }

    .date-input {
        margin-right: 5px;
        height: 38px;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    /* Botón + cambiado a color secondary de Bootstrap */
    .add-button {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 38px;
        background-color: #6c757d;
        /* Color secondary de Bootstrap */
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .add-button:hover {
        background-color: #5a6268;
        /* Color secondary hover de Bootstrap */
    }

    /* Estilos de las filas de semanas */
    .week-row {
        display: flex;
        margin-bottom: 8px;
        gap: 8px;
        padding: 0 15px;
    }

    /* Estilos de las tarjetas de días - QUITADO EL SCROLL */
    .day-card {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-width: 0;
        background-color: white;
        overflow-y: visible;
        /* Cambiado de auto a visible */
        max-height: none;
        /* Eliminado el límite de alto */
    }

    @media (max-width: 1000px) {

        /* Cambiar el layout de la semana para que se vea como una sola columna */
        .week-row {
            display: block;
            /* Cambiar de flex a block */
            margin-bottom: 0;
            /* Quitar el margen entre semanas */
        }

        /* A partir de 1000px, mostrar un contenedor por fila */
        .day-card {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            /* Espacio entre tarjetas */
            /* Quitar propiedades flex que ya no se necesitan */
            flex: none;
        }

        /* Mejorar la visualización de la cabecera */
        .calendar-header {
            flex-wrap: wrap;
        }

        .calendar-header h3 {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }

        .sucursal-select {
            flex: 1;
            min-width: 200px;
            max-width: 300px;
            margin: 0 10px;
        }

        .date-selector {
            flex: 1;
            justify-content: flex-end;
        }

        /* Hacer las tarjetas más amigables para móviles */
        .day-header {
            padding: 10px 15px;
            /* Header más grande */
            font-size: 1.1rem;
            /* Texto más grande */
        }

        .day-content {
            padding: 15px;
            /* Más espacio interno */
        }

        /* Hacer el texto un poco más grande en móviles */
        .coach-notes,
        .client-notes,
        .component-description {
            font-size: 1rem;
            line-height: 1.6;
        }
    }

    @media (max-width: 576px) {

        /* En móviles pequeños, ajustar más la cabecera */
        .calendar-header {
            flex-direction: column;
            align-items: center;
        }

        .sucursal-select {
            width: 100% !important;
            max-width: none;
            margin: 10px 0;
        }

        .date-selector {
            width: 100%;
            margin-top: 10px;
            justify-content: space-between;
        }

        .date-input {
            flex: 1;
            margin-right: 10px;
        }
    }

    .day-card.prev-month {
        background-color: #f8f9fa;
    }

    /* Cabecera de las tarjetas de día */
    .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        background-color: #6c757d;
        border-bottom: 1px solid #ddd;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    /* Estilo para días de fin de semana */
    .day-card:nth-child(6) .day-header,
    .day-card:nth-child(7) .day-header {
        background-color: #dc3545;
    }

    /* Estilo para el día actual */
    .day-header.current-day {
        background-color: #fd7e14;
    }

    .day-content {
        padding: 12px;
        font-size: 0.9rem;
    }

    .empty-workout {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px 0;
    }

    /* Estilos de las notas */
    .coach-notes {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .client-notes {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .notes-title {
        font-weight: bold;
        color: #856404;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    /* Estilos de los componentes */
    .component {
        margin-bottom: 12px;
    }

    .component-header {
        background-color: #dc3545;
        color: white;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
    }

    .component-activity {
        color: rgba(255, 255, 255, 0.9);
        font-style: italic;
    }

    .component-description {
        font-size: 0.9rem;
        color: #212529;
        padding: 0 8px;
    }

    /* Asegurar que el contenido de las notas respete el ancho del contenedor */
    .coach-notes div,
    .client-notes div,
    .component-description {
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        line-height: 1.5;
    }

    /* Asegurar que las imágenes no desborden el contenedor */
    .coach-notes img,
    .client-notes img,
    .component-description img {
        max-width: 100%;
        height: auto;
    }

    /* Ajustar los estilos para listas dentro de las notas */
    .coach-notes ul,
    .client-notes ul,
    .component-description ul,
    .coach-notes ol,
    .client-notes ol,
    .component-description ol {
        padding-left: 20px;
        margin-bottom: 10px;
    }

    /* Estilo para el menú desplegable */
    .settings-dropdown {
        position: relative;
        display: inline-block;
    }

    .settings-button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
    }

    .settings-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        min-width: 150px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 4px;
    }

    .settings-menu a {
        color: #333;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        font-size: 0.9rem;
    }

    .settings-menu a:hover {
        background-color: #f8f9fa;
    }

    .show {
        display: block;
    }
</style>

<section class="">
    <br>
    <div class="">
        <div class="calendar-header">
            <h3><?php echo $meses[$date->format('m')] . ' ' . $date->format('Y'); ?></h3>
            <select class="form-control sucursal-select">
                <?php foreach ($branches as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $selected_branch == $id ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>

            <div class="date-selector">
                <!-- <input type="date" class="form-control date-input" value="<?= date('Y-m-d') ?>"> -->
                <input type="date"
                    class="form-control date-input"
                    value="<?= $date->format('Y-m-d') ?>">
                <?php if ($isAdmin): ?>
                    <a href="<?php echo $this->Gym->createurl('GymDailyWorkout', 'addCalendarWorkout'); ?>" class="add-button">
                        <i class="fas fa-plus"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>


        <?php
        // Calcular variables necesarias para el calendario
        $primerDia = new FrozenDate($date->format('Y-m-01'));
        $ultimoDia = $primerDia->copy()->modify('last day of this month');
        $diasDelMes = (int)$ultimoDia->format('d');
        $primerDiaSemana = (int)$primerDia->format('N');

        // Calcular el número total de días que necesitamos mostrar
        $diasDelMesAnterior = cal_days_in_month(CAL_GREGORIAN, $mesAnterior, $anioMesAnterior);
        $diasAnteriores = $primerDiaSemana - 1;
        $diasRestantes = (7 - (($diasDelMes + $diasAnteriores) % 7)) % 7;
        $totalDias = $diasAnteriores + $diasDelMes + $diasRestantes;
        $totalSemanas = ceil($totalDias / 7);

        // Continuar con el bucle de semanas
        for ($semana = 0; $semana < $totalSemanas; $semana++):
            echo '<div class="week-row">';

            for ($diaSemana = 0; $diaSemana < 7; $diaSemana++):
                $diaReal = $semana * 7 + $diaSemana - $diasAnteriores;

                // Determinar el día a mostrar
                if ($diaReal <= 0) {
                    // Días del mes anterior
                    $diaAMostrar = $diasDelMesAnterior + $diaReal;
                    $esMesAnterior = true;
                    $mesActual = $mesAnterior;
                    $anioActual = $anioMesAnterior;
                } elseif ($diaReal > $diasDelMes) {
                    // Días del mes siguiente
                    $diaAMostrar = $diaReal - $diasDelMes;
                    $esMesAnterior = true;
                    $mesActual = str_pad($mes + 1, 2, '0', STR_PAD_LEFT);
                    $anioActual = $anio;
                    if ($mesActual > 12) {
                        $mesActual = '01';
                        $anioActual++;
                    }
                } else {
                    // Días del mes actual
                    $diaAMostrar = $diaReal;
                    $esMesAnterior = false;
                    $mesActual = $mes;
                    $anioActual = $anio;
                }

                // Verificar si es el día actual
                $esDiaActual = ($diaAMostrar == $diaActual && $mesActual == $mes && $anioActual == $anio);

                // Formatear la fecha para buscar en workoutsByDate
                $currentDate = sprintf('%04d-%02d-%02d', $anioActual, $mesActual, $diaAMostrar);
        ?>
                <div class="day-card <?php echo $esMesAnterior ? 'prev-month' : ''; ?>">
                    <div class="day-header <?php echo $esDiaActual ? 'current-day' : ''; ?>">
                        <span><?php echo $diasSemana[$diaSemana] . ' ' . str_pad($diaAMostrar, 2, '0', STR_PAD_LEFT); ?></span>
                        <?php
                        if (isset($workoutsByDate[$currentDate])):
                            $workout = $workoutsByDate[$currentDate];
                            if ($isAdmin): // Agregar esta condición
                        ?>
                                <div class="settings-dropdown">
                                    <button class="settings-button" data-workout-id="<?= $workout->id ?>">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <div class="settings-menu">
                                        <?= $this->Html->link(
                                            '<i class="fas fa-edit"></i> Editar',
                                            ['controller' => 'GymDailyWorkout', 'action' => 'editCalendarWorkout', $workout->id],
                                            ['escape' => false, 'class' => 'edit-workout']
                                        ) ?>
                                        <?= $this->Html->link(
                                            '<i class="fas fa-trash"></i> Eliminar',
                                            '#',
                                            [
                                                'class' => 'dropdown-item text-danger delete-workout',
                                                'escape' => false,
                                                'data-workout-id' => $workout->id
                                            ]
                                        ) ?>
                                    </div>
                                </div>
                        <?php
                            endif; // Cerrar la condición del isAdmin
                        endif;
                        ?>
                    </div>
                    <div class="day-content">
                        <?php if (isset($workoutsByDate[$currentDate])): ?>
                            <!-- Notas del Coach -->
                            <?php if (!empty($workout->coach_notes)): ?>
                                <div class="coach-notes">
                                    <div><?= $this->Html->div(null, $workout->coach_notes, ['escape' => false]) ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Notas para Clientes -->
                            <?php if (!empty($workout->description)): ?>
                                <div class="client-notes">
                                    <div><?= $this->Html->div(null, $workout->description, ['escape' => false]) ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Componentes -->
                            <?php foreach ($workout->components as $component): ?>
                                <div class="component">
                                    <div class="component-header">
                                        <?= h($component->category->name) ?>
                                        <?php if (!empty($component->activity)): ?>
                                            <span class="component-activity">(<?= h($component->activity->title) ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="component-description">
                                        <?= $this->Html->div(null, $component->description, ['escape' => false]) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-workout">Ningún workout encontrado</div>
                        <?php endif; ?>
                    </div>
                </div>
        <?php
            endfor;
            echo '</div>';
        endfor;
        ?>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Inicializar select2 para el combo de sucursales
        $('.sucursal-select').select2({
            placeholder: 'Seleccionar sucursal',
            width: '100%'
        });

        // Cuando cambia la sucursal, recargar la página con la sucursal seleccionada
        $('.sucursal-select').on('change', function() {
            const branchId = $(this).val();
            const currentDate = $('.date-input').val();
            window.location.href = '<?= $this->Url->build(['action' => 'calendarioWorkout']) ?>?date=' + currentDate + '&branch=' + branchId;
        });

        // Cuando cambia la fecha
        $('.date-input').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                const branchId = $('.sucursal-select').val();
                window.location.href = '<?= $this->Url->build(['action' => 'calendarioWorkout']) ?>?date=' + selectedDate + '&branch=' + branchId;
            }
        });

        // Manejo del menú de configuración
        $('.settings-button').click(function(e) {
            e.stopPropagation();
            const menu = $(this).siblings('.settings-menu');
            $('.settings-menu').not(menu).removeClass('show');
            menu.toggleClass('show');
        });

        // Evitar que el menú se cierre al hacer clic en una opción
        $('.edit-workout').click(function(e) {
            e.stopPropagation();
        });

        // Cerrar el menú al hacer clic fuera
        $(document).click(function() {
            $('.settings-menu').removeClass('show');
        });

        // Manejar eliminación de workout
        $('.delete-workout').click(function(e) {
            e.preventDefault();
            if (confirm('¿Está seguro de que desea eliminar este workout?')) {
                const workoutId = $(this).data('workout-id');
                window.location.href = '<?= $this->Url->build([
                                            'controller' => 'GymDailyWorkout',
                                            'action' => 'deleteWorkout'
                                        ]) ?>/' + workoutId;
            }
        });

        // Scrollear a la fecha actual si existe
        const currentDayHeader = $('.current-day');
        if (currentDayHeader.length > 0) {
            $('html, body').animate({
                scrollTop: currentDayHeader.offset().top - 100
            }, 500);
        }

        // Si hay un parámetro de fecha en la URL, scrollear a esa fecha
        const urlParams = new URLSearchParams(window.location.search);
        const dateParam = urlParams.get('date');
        if (dateParam) {
            const dateParts = dateParam.split('-');
            const year = dateParts[0];
            const month = dateParts[1];
            const day = dateParts[2];

            // Buscar la tarjeta con esa fecha
            const dayCards = $('.day-header');
            dayCards.each(function() {
                const headerText = $(this).find('span').text();
                const dayNumber = headerText.match(/\d+/)[0];
                if (parseInt(dayNumber, 10) === parseInt(day, 10)) {
                    $('html, body').animate({
                        scrollTop: $(this).offset().top - 100
                    }, 500);
                    return false;
                }
            });
        }
    });
</script>