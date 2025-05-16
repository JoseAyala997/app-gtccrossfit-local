<?php
/* Template/ClassBooking/drop_in.ctp */
?>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Registro Drop-In') ?></h3>
                </div>
                <div class="box-body">
                    <?php echo $this->Form->create(null, ['id' => 'dropinForm', 'type' => 'post']); ?>
                    
                    <!-- Datos Personales -->
                    <div class="form-group-heading">
                        <h4><?= __('Datos Personales') ?></h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name"><?= __('Nombre') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('first_name', [
                                    'class' => 'form-control', 
                                    'placeholder' => __('Ingrese el nombre'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name"><?= __('Apellido') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('last_name', [
                                    'class' => 'form-control', 
                                    'placeholder' => __('Ingrese el apellido'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><?= __('Correo Electrónico') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('email', [
                                    'type' => 'email',
                                    'class' => 'form-control', 
                                    'placeholder' => __('ejemplo@correo.com'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile"><?= __('Teléfono Móvil') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('mobile', [
                                    'class' => 'form-control', 
                                    'placeholder' => __('Ingrese número de contacto'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender"><?= __('Género') ?></label>
                                <?php 
                                $options = [
                                    'male' => __('Masculino'),
                                    'female' => __('Femenino')
                                ];
                                echo $this->Form->select('gender', $options, [
                                    'class' => 'form-control',
                                    'empty' => __('Seleccionar género')
                                ]); 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="birth_date"><?= __('Fecha de Nacimiento') ?></label>
                                <?php echo $this->Form->control('birth_date', [
                                    'type' => 'text', 
                                    'class' => 'form-control datepicker-past', 
                                    'placeholder' => __('mm/dd/aaaa'),
                                    'label' => false
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dirección -->
                    <div class="form-group-heading">
                        <h4><?= __('Dirección') ?></h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address"><?= __('Dirección') ?></label>
                                <?php echo $this->Form->control('address', [
                                    'type' => 'textarea',
                                    'class' => 'form-control',
                                    'label' => false,
                                    'rows' => 2
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city"><?= __('Ciudad') ?></label>
                                <?php echo $this->Form->control('city', [
                                    'class' => 'form-control',
                                    'label' => false
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state"><?= __('Estado/Provincia') ?></label>
                                <?php echo $this->Form->control('state', [
                                    'class' => 'form-control',
                                    'label' => false
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zipcode"><?= __('Código Postal') ?></label>
                                <?php echo $this->Form->control('zipcode', [
                                    'class' => 'form-control',
                                    'label' => false
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos de la Cuenta -->
                    <div class="form-group-heading">
                        <h4><?= __('Datos de la Cuenta') ?></h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username"><?= __('Nombre de Usuario') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('username', [
                                    'class' => 'form-control',
                                    'placeholder' => __('Elija un nombre de usuario'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password"><?= __('Contraseña') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('password', [
                                    'type' => 'password',
                                    'class' => 'form-control',
                                    'placeholder' => __('Ingrese una contraseña segura'),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de la Clase -->
                    <div class="form-group-heading">
                        <h4><?= __('Información de la Clase') ?></h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_id"><?= __('Clase') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->select('class_id', $classes, [
                                    'class' => 'form-control',
                                    'empty' => __('Seleccionar clase'),
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="booking_date"><?= __('Fecha') ?> <span class="text-danger">*</span></label>
                                <?php echo $this->Form->control('booking_date', [
                                    'type' => 'text', 
                                    'class' => 'form-control datepicker', 
                                    'value' => date('m/d/Y', strtotime($selectedDate)),
                                    'label' => false,
                                    'required' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg"><?= __('Registrar y Reservar') ?></button>
                            </div>
                        </div>
                    </div>
                    
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .form-group-heading {
        margin-top: 25px;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    .form-group-heading h4 {
        color: #3c8dbc;
        margin: 0;
    }
    .mt-4 {
        margin-top: 25px;
    }
    .text-danger {
        color: #dd4b39;
    }
</style>

<script>
    $(document).ready(function() {
        // Inicializar datepicker para fecha de reserva
        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '0d',
            autoclose: true
        });
        
        // Inicializar datepicker para fecha de nacimiento
        $('.datepicker-past').datepicker({
            format: 'mm/dd/yyyy',
            endDate: '0d',
            autoclose: true
        });
        
        // Generar nombre de usuario automáticamente basado en nombre y apellido
        $('#first-name, #last-name').on('blur', function() {
            var firstName = $('#first-name').val().toLowerCase().trim();
            var lastName = $('#last-name').val().toLowerCase().trim();
            
            if (firstName && lastName && !$('#username').val()) {
                var username = firstName.charAt(0) + lastName.replace(/\s+/g, '');
                // Eliminar acentos y caracteres especiales
                username = username.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                $('#username').val(username);
            }
        });
    });
</script> 