<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* Estilos generales del contenedor principal */


    /* Contenedor interno con fondo blanco */
    .inner-container {
        background-color: #fff;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Separador de secciones */
    .section-divider {
        border-top: 1px solid #e0e0e0;
        margin: 25px 0;
    }

    /* Título de sección */
    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    /* Alineación de campos */
    .field-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .field-column {
        flex: 1;
        padding-right: 15px;
    }

    /* Ajustes para radios */
    .radio_buttons label {
        display: block;
        margin-bottom: 5px;
    }

    /* Estilos para la sección de visibilidad */
    .visibility-section {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .visibility-group {
        width: 50%;
        padding-right: 15px;
    }

    /* Ajustes para la hora de visibilidad */
    .time-input {
        height: 38px;
        padding: 6px 12px;
        width: 100%;
    }

    /* Componentes */
    .component-section {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
        background-color: #fafafa;
    }

    .component-item .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #ffffff;
        padding: 10px 15px;
    }

    .component-item .card-header .remove-component {
        margin-left: auto;
        padding: 2px 6px;
    }

    .card-header i.fas.fa-bars {
        margin-right: 8px;
    }

    /* Estilos del formulario */
    .card-body {
        padding: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .help-block {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    .mb4 {
        margin-bottom: 1.5rem;
    }

    .input-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        width: 100%;
    }

    .input-group-append {
        margin-left: -1px;
        display: flex;
    }

    .input-group>.form-control {
        position: relative;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        margin-bottom: 0;
    }

    #hidden-date {
        position: absolute;
        width: 0;
        height: 0;
        padding: 0;
        margin: 0;
        border: 0;
        opacity: 0;
    }

    /* Estilos para validación */
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff8f8 !important;
    }

    trix-editor.is-invalid {
        background-color: #fff8f8 !important;
        border: 1px solid #dc3545 !important;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .visibility-group.is-invalid {
        padding: 0.5rem;
        border: 1px solid #dc3545;
        border-radius: 0.25rem;
        background-color: #fff8f8;
    }

    .input-group.is-invalid {
        margin-bottom: 1.5rem;
    }

    .input-group.is-invalid~.invalid-feedback {
        display: block;
        margin-top: -1.25rem;
        margin-bottom: 1rem;
    }

    trix-editor.is-invalid {
        border-color: #dc3545 !important;
    }

    trix-editor.is-invalid~.invalid-feedback {
        display: block;
        margin-top: 0.25rem;
    }

    #visibility-time-container input.is-invalid {
        border-color: #dc3545;
        background-color: #fff8f8;
    }

    #visibility-time-container .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
    }

    #visibility-time-container.is-invalid {
        border: none;
        background-color: transparent;
    }
</style>

<section class="content col-md-12">
    <div class="card">
        <div class="inner-container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Formulario principal -->
                    <?= $this->Form->create(null, ['id' => 'new_wod', 'novalidate' => 'novalidate']) ?>

                    <!-- SECCIÓN PRINCIPAL -->
                    <div class="row">
                        <!-- Columna izquierda: Programa, Fecha, Visibilidad, Notas -->
                        <div class="col-md-6">
                            <h4 class="section-title">Información del Workout</h4>

                            <!-- Programa -->
                            <div class="form-group select required wod_program_id">
                                <?= $this->Form->control('program_id', [
                                    'options' => $programs,
                                    'label' => 'Programa',
                                    'class' => 'form-control select required select2-hidden-accessible'
                                ]) ?>
                            </div>

                            <!-- Fecha -->
                            <div class="form-group">
                                <label>Fecha</label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control"
                                        id="workout-date"
                                        readonly
                                        style="cursor: pointer;">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary"
                                            type="button"
                                            id="date-picker-button">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="date" id="hidden-date" class="d-none">
                            </div>

                            <!-- Visibilidad y Hora -->
                            <div class="visibility-section">
                                <div class="visibility-group form-group radio_buttons optional wod_visibility">
                                    <label>Visibilidad del workout</label>
                                    <?= $this->Form->control('visibility', [
                                        'type' => 'radio',
                                        'options' => [
                                            'DAY_BEFORE' => 'Día anterior',
                                            'SAME_DAY' => 'Mismo día',
                                            'ALWAYS' => 'Siempre visible'
                                        ],
                                        'label' => false,
                                        'class' => 'radio_buttons optional',
                                        'legend' => false,
                                        'separator' => '<br>'
                                    ]) ?>
                                    <p class="help-block">Fecha cuando el workout estará visible para los clientes</p>
                                </div>
                                <div id="visibility-time-container" class="form-group time optional wod_visibility_time visibility-group">
                                    <label for="visibility-time">Desde las</label>
                                    <div>
                                        <?= $this->Form->control('visibility_time', [
                                            'label' => false,
                                            'class' => 'form-control time optional input-time',
                                            'type' => 'time',
                                            'required' => false
                                        ]) ?>
                                    </div>
                                    <p class="help-block">Hora cuando el workout estará visible para los clientes</p>
                                </div>

                            </div>

                            <div class="section-divider"></div>

                            <!-- NOTAS PARA CLIENTES -->
                            <div class="form-group">
                                <label for="wod_description">Notas para los clientes</label>
                                <?= $this->Form->hidden('description', ['id' => 'wod_description']) ?>
                                <trix-toolbar id="my_toolbar"></trix-toolbar>
                                <trix-editor toolbar="my_toolbar" input="wod_description"></trix-editor>
                                <p class="help-block mb4">
                                    Utilice este espacio para notas del workout, por ejemplo traer comba o zapatillas para correr
                                </p>
                            </div>

                            <!-- NOTAS PARA COACHES -->
                            <div class="form-group">
                                <label for="wod_coach_notes">Notas para los coaches</label>
                                <?= $this->Form->hidden('coach_notes', ['id' => 'wod_coach_notes']) ?>
                                <trix-toolbar id="my_toolbar_coach"></trix-toolbar>
                                <trix-editor toolbar="my_toolbar_coach" input="wod_coach_notes"></trix-editor>
                                <!-- <p class="help-block mb4">
                                    Para que no sean visibles en Clases, CrossHero deberá estar en Modo Protegido
                                </p> -->
                            </div>
                        </div>

                        <!-- Columna derecha: Componentes -->
                        <div class="col-md-6">
                            <h4 class="section-title">Componentes</h4>
                            <div class="component-section">
                                <div class="form-group">
                                    <?= $this->Form->control('category_id', [
                                        'options' => $categories,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'id' => 'category-select',
                                        'empty' => '-- Seleccione un Componente --'
                                    ]) ?>
                                </div>

                                <div id="components-container">
                                    <!-- Aquí se añadirán dinámicamente los componentes -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Botones de acción -->
                    <div class="form-group">
                        <?= $this->Form->button(__('Guardar'), ['class' => 'btn btn-primary']) ?>
                        <a class="btn btn-danger" href="/dashboard/admin/wods?back_to=">Cancelar</a>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Formatear fecha para mostrar (dd/mm/yyyy)
        function formatDisplayDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // Formatear fecha para el servidor (yyyy-mm-dd)
        function formatServerDate(date) {
            return date.toISOString().split('T')[0];
        }

        // Obtener referencias a los elementos
        const workoutDateInput = document.getElementById('workout-date');
        const hiddenDateInput = document.getElementById('hidden-date');
        const datePickerButton = document.getElementById('date-picker-button');

        // Crear un input oculto adicional para el formulario
        const formDateInput = document.createElement('input');
        formDateInput.type = 'hidden';
        formDateInput.name = 'date';
        workoutDateInput.parentNode.appendChild(formDateInput);

        // Inicializar con la fecha actual
        const today = new Date();
        // Ajustar la zona horaria
        today.setMinutes(today.getMinutes() + today.getTimezoneOffset());

        workoutDateInput.value = formatDisplayDate(today);
        hiddenDateInput.value = formatServerDate(today);
        formDateInput.value = formatServerDate(today);

        // Manejar el click tanto en el input como en el botón
        workoutDateInput.addEventListener('click', function() {
            hiddenDateInput.showPicker();
        });

        datePickerButton.addEventListener('click', function() {
            hiddenDateInput.showPicker();
        });

        // Actualizar los campos cuando cambie la fecha
        hiddenDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            // Ajustar la zona horaria
            selectedDate.setMinutes(selectedDate.getMinutes() + selectedDate.getTimezoneOffset());

            workoutDateInput.value = formatDisplayDate(selectedDate);
            formDateInput.value = formatServerDate(selectedDate);
        });
        //fin de la fecha


        const categorySelect = document.getElementById('category-select');
        const componentsContainer = document.getElementById('components-container');
        const activities = <?= json_encode($activities) ?>;
        let componentIndex = 0;

        // Función para inicializar un editor Trix en un contenedor dado
        function initTrixEditor(containerId, inputId) {
            const toolbar = document.createElement('trix-toolbar');
            toolbar.id = 'toolbar-' + inputId;

            const editor = document.createElement('trix-editor');
            editor.setAttribute('toolbar', toolbar.id);
            editor.setAttribute('input', inputId);

            const container = document.getElementById(containerId);
            container.appendChild(toolbar);
            container.appendChild(editor);
        }

        // Función para crear un nuevo componente
        function createComponent(categoryId, categoryName) {
            // Crear contenedor del componente
            const componentDiv = document.createElement('div');
            componentDiv.className = 'component-item card mb-3';
            componentDiv.dataset.categoryId = categoryId;
            componentDiv.id = 'component-' + componentIndex;

            // Crear encabezado del componente
            const headerDiv = document.createElement('div');
            headerDiv.className = 'card-header bg-white';
            headerDiv.innerHTML = `
                <div style="display: flex; align-items: center; width: 100%;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-bars"></i>
                        <span>${categoryName}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger remove-component">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            // Crear cuerpo del componente
            const bodyDiv = document.createElement('div');
            bodyDiv.className = 'card-body';
            bodyDiv.id = 'component-body-' + componentIndex;

            // Filtrar las actividades por categoría
            const filteredActivities = activities.filter(activity => activity.cat_id == categoryId);

            // Selector de actividad
            const activityFieldset = document.createElement('div');
            activityFieldset.className = 'form-group';

            let activityLabel = 'Registrar por';
            if (categoryName.toLowerCase().includes('weightlifting')) {
                activityLabel = 'Registrar como';
            }

            const activityLabelElement = document.createElement('label');
            activityLabelElement.htmlFor = `components-${componentIndex}-activity_id`;
            activityLabelElement.textContent = activityLabel;

            const activitySelect = document.createElement('select');
            activitySelect.className = 'form-control';
            activitySelect.id = `components-${componentIndex}-activity_id`;
            activitySelect.name = `components[${componentIndex}][activity_id]`;

            // Opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Seleccione --';
            activitySelect.appendChild(defaultOption);

            // Opciones de actividades
            filteredActivities.forEach(activity => {
                const option = document.createElement('option');
                option.value = activity.id;
                option.textContent = activity.title;
                activitySelect.appendChild(option);
            });

            activityFieldset.appendChild(activityLabelElement);
            activityFieldset.appendChild(activitySelect);
            bodyDiv.appendChild(activityFieldset);

            // Campo de descripción (con Trix)
            const descriptionFieldset = document.createElement('div');
            descriptionFieldset.className = 'form-group';

            let descriptionLabel = 'Descripción';
            if (categoryName.toLowerCase().includes('weightlifting')) {
                descriptionLabel = 'Modelo Repetición';
            }

            const descriptionLabelElement = document.createElement('label');
            descriptionLabelElement.htmlFor = `components-${componentIndex}-description`;
            descriptionLabelElement.textContent = descriptionLabel;

            // Contenedor para Trix
            const trixContainerId = `trix-container-${componentIndex}`;
            const trixInputId = `components-${componentIndex}-description`;

            const trixContainer = document.createElement('div');
            trixContainer.id = trixContainerId;

            const trixInput = document.createElement('input');
            trixInput.type = 'hidden';
            trixInput.id = trixInputId;
            trixInput.name = `components[${componentIndex}][description]`;

            descriptionFieldset.appendChild(descriptionLabelElement);
            descriptionFieldset.appendChild(trixInput);
            descriptionFieldset.appendChild(trixContainer);
            bodyDiv.appendChild(descriptionFieldset);

            // Si la categoría es Custom Metcon, agregar checkbox RX
            if (categoryName.toLowerCase().includes('custom metcon')) {
                const rxFieldset = document.createElement('div');
                rxFieldset.className = 'form-group';

                const rxLabel = document.createElement('label');
                rxLabel.htmlFor = `components-${componentIndex}-rx`;

                const rxCheckbox = document.createElement('input');
                rxCheckbox.type = 'checkbox';
                rxCheckbox.id = `components-${componentIndex}-rx`;
                rxCheckbox.name = `components[${componentIndex}][rx]`;

                rxLabel.appendChild(rxCheckbox);
                rxLabel.appendChild(document.createTextNode(' Permitir RX'));

                rxFieldset.appendChild(rxLabel);
                bodyDiv.appendChild(rxFieldset);
            }

            // Campo oculto para guardar el category_id
            const categoryTypeInput = document.createElement('input');
            categoryTypeInput.type = 'hidden';
            categoryTypeInput.name = `components[${componentIndex}][category_id]`;
            categoryTypeInput.value = categoryId;
            bodyDiv.appendChild(categoryTypeInput);

            // Agregar el header y body al componente
            componentDiv.appendChild(headerDiv);
            componentDiv.appendChild(bodyDiv);
            componentsContainer.appendChild(componentDiv);

            // Inicializar el editor Trix para este componente
            initTrixEditor(trixContainerId, trixInputId);

            // Evento para eliminar el componente
            componentDiv.querySelector('.remove-component').addEventListener('click', function() {
                componentDiv.remove();
            });

            componentIndex++;
        }

        // Cuando se cambia la selección de categoría, crear un nuevo componente
        categorySelect.addEventListener('change', function() {
            const selectedCategoryId = this.value;
            if (selectedCategoryId) {
                const selectedCategoryText = this.options[this.selectedIndex].text;
                createComponent(selectedCategoryId, selectedCategoryText);
                // Restablecer la selección
                this.value = '';
            }
        });
        // Array de fechas ocupadas (esto debe venir del controlador)
        const occupiedDates = <?= json_encode($occupiedDates ?? []) ?>;

        // Función para validar el formulario
        function validateForm(event) {
            event.preventDefault();
            let isValid = true;

            // Limpiar errores previos
            document.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(element => {
                element.remove();
            });

            // Validar programa
            const programSelect = document.querySelector('[name="program_id"]');
            if (!programSelect.value) {
                isValid = false;
                addError(programSelect, 'Debe seleccionar un programa');
            }

            // Validar fecha
            const dateContainer = document.querySelector('.input-group');
            if (!formDateInput.value) {
                isValid = false;
                addError(dateContainer, 'Debe seleccionar una fecha', true);
            } else if (occupiedDates.includes(formDateInput.value)) {
                isValid = false;
                addError(dateContainer, 'Ya existe un workout para esta fecha', true);
            }

            // Validar visibilidad
            const visibilityInputs = document.querySelectorAll('[name="visibility"]');
            let visibilitySelected = false;
            visibilityInputs.forEach(input => {
                if (input.checked) visibilitySelected = true;
            });
            if (!visibilitySelected) {
                isValid = false;
                addError(visibilityInputs[0].closest('.visibility-group'), 'Debe seleccionar la visibilidad del workout');
            }

            // Validar hora de visibilidad - para selectores de hora y minuto
            try {
                const timeContainer = document.getElementById('visibility-time-container');

                // Buscar los selectores de hora y minuto
                const hourSelector = timeContainer ?
                    timeContainer.querySelector('select[name="visibility_time[hour]"]') :
                    null;
                const minuteSelector = timeContainer ?
                    timeContainer.querySelector('select[name="visibility_time[minute]"]') :
                    null;

                console.log('Selectores de tiempo:', {
                    timeContainer: timeContainer,
                    hourSelector: hourSelector,
                    minuteSelector: minuteSelector,
                    hourValue: hourSelector ? hourSelector.value : 'no selector',
                    minuteValue: minuteSelector ? minuteSelector.value : 'no selector'
                });

                // Consideramos que el tiempo está seleccionado si ambos selectores existen,
                // ya que siempre tendrán un valor por defecto seleccionado
                if (!hourSelector || !minuteSelector) {
                    isValid = false;

                    if (timeContainer) {
                        // Eliminar mensajes previos
                        const existingFeedbacks = timeContainer.querySelectorAll('.invalid-feedback');
                        existingFeedbacks.forEach(el => el.remove());

                        // Agregar clase de inválido al contenedor
                        timeContainer.classList.add('is-invalid');

                        // Mensaje de error
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Debe seleccionar la hora de visibilidad';
                        timeContainer.appendChild(feedback);
                    }
                } else {
                    console.log('La hora pasó la validación:', hourSelector.value + ':' + minuteSelector.value);
                }
            } catch (error) {
                console.error('Error en validación de hora:', error);
                isValid = false;
            }

            // Validar notas para clientes
            const clientNotesEditor = document.querySelector('trix-editor[input="wod_description"]');
            const clientNotesInput = document.getElementById('wod_description');
            if (!clientNotesInput.value.trim()) {
                isValid = false;
                addError(clientNotesEditor, 'Debe ingresar notas para los clientes');
            }

            // Validar notas para coaches
            const coachNotesEditor = document.querySelector('trix-editor[input="wod_coach_notes"]');
            const coachNotesInput = document.getElementById('wod_coach_notes');
            if (!coachNotesInput.value.trim()) {
                isValid = false;
                addError(coachNotesEditor, 'Debe ingresar notas para los coaches');
            }

            // Validar componentes si existen
            const components = document.querySelectorAll('.component-item');
            if (components.length > 0) {
                components.forEach((component, index) => {
                    const activitySelect = component.querySelector('select[name^="components"]');
                    const descriptionEditor = component.querySelector('trix-editor');
                    if (activitySelect && descriptionEditor) {
                        const inputDescription = document.getElementById(descriptionEditor.getAttribute('input'));

                        if (!activitySelect.value) {
                            isValid = false;
                            addError(activitySelect, `Debe seleccionar una actividad para el componente ${index + 1}`);
                        }

                        if (!inputDescription.value.trim()) {
                            isValid = false;
                            addError(descriptionEditor, `Debe ingresar una descripción para el componente ${index + 1}`);
                        }
                    }
                });
            }

            if (isValid) {
                document.getElementById('new_wod').submit();
            } else {
                const firstError = document.querySelector('.invalid-feedback');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        }

        function addError(element, message, isDateField = false, isTimeField = false) {
            element.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;

            if (isDateField) {
                // Para el campo de fecha
                element.parentNode.appendChild(feedback);
            } else if (isTimeField) {
                // Para el campo de hora
                const timeInput = element.querySelector('input[type="time"]');
                if (timeInput) {
                    timeInput.classList.add('is-invalid');
                }
                feedback.style.marginTop = '0.25rem';
                element.appendChild(feedback);
            } else if (element.tagName.toLowerCase() === 'trix-editor') {
                // Para editores Trix
                element.parentNode.appendChild(feedback);
            } else {
                // Para otros elementos
                element.insertAdjacentElement('afterend', feedback);
            }
        }

        // Agregar el evento submit al formulario
        document.getElementById('new_wod').addEventListener('submit', validateForm);
    });
</script>