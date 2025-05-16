<?= $this->Html->css('https://unpkg.com/trix@2.0.8/dist/trix.css') ?>
<?= $this->Html->script('https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js') ?>
<?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css') ?>

<style>
    .note-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    trix-editor {
        min-height: 150px;
        margin-bottom: 20px;
    }

    .component-section {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
        background-color: #fafafa;
    }

    .component-item {
        margin-bottom: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        background-color: #fff;
    }

    .component-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #ffffff;
        padding: 10px 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .component-body {
        padding: 15px;
    }

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

    .card-header i.fas.fa-bars {
        margin-right: 8px;
    }
</style>

<div class="note-container">
    <?= $this->Form->create(null, ['id' => 'noteForm']) ?>
    
    <h4 class="mb-4">Agregar Nota</h4>
    
    <div class="row">
        <div class="col-md-12">
            <!-- Selector de categoría -->
            <div class="component-section">
                <div class="form-group">
                    <?= $this->Form->control('category_id', [
                        'options' => $categories,
                        'empty' => '-- Seleccione una Categoría --',
                        'label' => false,
                        'class' => 'form-control',
                        'id' => 'category-select'
                    ]) ?>
                </div>

                <!-- Contenedor para los componentes -->
                <div id="components-container">
                    <!-- Aquí se agregarán dinámicamente los componentes -->
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?= $this->Form->button('Guardar Nota', ['class' => 'btn btn-primary', 'id' => 'submit-button']) ?>
        <?= $this->Html->link('Cancelar', ['action' => 'dailynote'], ['class' => 'btn btn-danger ml-2']) ?>
    </div>
    
    <?= $this->Form->end() ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category-select');
    const componentsContainer = document.getElementById('components-container');
    const form = document.getElementById('noteForm');
    
    // Datos de actividades (esto debería venir del controlador)
    // Para pruebas usamos un objeto simulado
    const activities = <?= json_encode($activities ?? []) ?>;
    
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
        componentDiv.className = 'component-item';
        componentDiv.dataset.categoryId = categoryId;
        componentDiv.id = 'component-' + componentIndex;
        
        // Crear encabezado del componente
        const headerDiv = document.createElement('div');
        headerDiv.className = 'component-header';
        headerDiv.innerHTML = `
            <div style="display: flex; align-items: center;">
                <i class="fas fa-bars"></i>
                <span>${categoryName}</span>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-component">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Crear cuerpo del componente
        const bodyDiv = document.createElement('div');
        bodyDiv.className = 'component-body';
        bodyDiv.id = 'component-body-' + componentIndex;
        
        // Filtrar las actividades por categoría (si es aplicable)
        const filteredActivities = activities.filter(activity => 
            activity.cat_id == categoryId || activity.category_id == categoryId
        );
        
        // Selector de actividad
        if (filteredActivities.length > 0) {
            const activityFieldset = document.createElement('div');
            activityFieldset.className = 'form-group';
            
            const activityLabelElement = document.createElement('label');
            activityLabelElement.htmlFor = `components-${componentIndex}-activity_id`;
            activityLabelElement.textContent = 'Actividad';
            
            const activitySelect = document.createElement('select');
            activitySelect.className = 'form-control';
            activitySelect.id = `components-${componentIndex}-activity_id`;
            activitySelect.name = `components[${componentIndex}][activity_id]`;
            
            // Opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Seleccione una actividad --';
            activitySelect.appendChild(defaultOption);
            
            // Opciones de actividades
            filteredActivities.forEach(activity => {
                const option = document.createElement('option');
                option.value = activity.id;
                option.textContent = activity.title || activity.name;
                activitySelect.appendChild(option);
            });
            
            activityFieldset.appendChild(activityLabelElement);
            activityFieldset.appendChild(activitySelect);
            bodyDiv.appendChild(activityFieldset);
        }
        
        // Campo de descripción (con Trix)
        const descriptionFieldset = document.createElement('div');
        descriptionFieldset.className = 'form-group';
        
        const descriptionLabelElement = document.createElement('label');
        descriptionLabelElement.htmlFor = `components-${componentIndex}-description`;
        descriptionLabelElement.textContent = 'Descripción';
        
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
    
    // Validación del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Limpiar errores previos
        document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(element => {
            element.remove();
        });
        
        // Verificar si hay al menos un componente
        const components = document.querySelectorAll('.component-item');
        if (components.length === 0) {
            isValid = false;
            // Mostrar error en el selector de categoría
            categorySelect.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'Debe agregar al menos un componente';
            categorySelect.parentNode.appendChild(feedback);
        } else {
            // Validar cada componente
            components.forEach((component, index) => {
                // Validar actividad si existe el selector
                const activitySelect = component.querySelector('select[name^="components"]');
                if (activitySelect && !activitySelect.value) {
                    isValid = false;
                    activitySelect.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Debe seleccionar una actividad';
                    activitySelect.parentNode.appendChild(feedback);
                }
                
                // Validar descripción
                const trixEditor = component.querySelector('trix-editor');
                const trixInput = document.getElementById(trixEditor.getAttribute('input'));
                if (!trixInput.value.trim()) {
                    isValid = false;
                    trixEditor.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Debe ingresar una descripción';
                    trixEditor.parentNode.appendChild(feedback);
                }
            });
        }
        
        if (isValid) {
            form.submit();
        }
    });
});
</script>