<!-- filepath: c:\xampp\htdocs\app-gtccrossfit\src\Template\ClassBooking\member_drop_in.ctp -->
<?php
/* Template/ClassBooking/member_drop_in.ctp */
?>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Mi Perfil Drop-In') ?></h3>
                </div>
                <div class="box-body">
                    <?php echo $this->Form->create($member, ['id' => 'memberDropInForm', 'type' => 'post']); ?>
                    
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
                                    'value' => $member->first_name,
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
                                    'value' => $member->last_name,
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
                                    'value' => $member->email,
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
                                    'value' => $member->mobile,
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
                                    'empty' => __('Seleccionar género'),
                                    'value' => $member->gender
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
                                    'label' => false,
                                    'value' => ($member->birth_date) ? date('m/d/Y', strtotime($member->birth_date)) : ''
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
                                    'rows' => 2,
                                    'value' => $member->address
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
                                    'label' => false,
                                    'value' => $member->city
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state"><?= __('Estado/Provincia') ?></label>
                                <?php echo $this->Form->control('state', [
                                    'class' => 'form-control',
                                    'label' => false,
                                    'value' => $member->state
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zipcode"><?= __('Código Postal') ?></label>
                                <?php echo $this->Form->control('zipcode', [
                                    'class' => 'form-control',
                                    'label' => false,
                                    'value' => $member->zipcode
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Membresía y clases -->
                    <div class="form-group-heading">
                        <h4><?= __('Solicitar Membresía') ?></h4>
                    </div>
                    
                    <?php if (isset($pendingMembership)): ?>
                    <div class="alert alert-info">
                        <p>
                            <?= __('Ya tienes una solicitud de membresía pendiente:') ?> 
                            <strong><?= $selectedMembership->membership_label ?></strong>
                        </p>
                        <p>
                            <?= __('Fecha de inicio:') ?> <?= date('d/m/Y', strtotime($pendingMembership->start_date)) ?>
                            <?= __('Fecha de fin:') ?> <?= date('d/m/Y', strtotime($pendingMembership->end_date)) ?>
                        </p>
                        <p>
                            <?= __('Monto:') ?> $<?= number_format($pendingMembership->membership_amount, 2) ?>
                            <?= __('Estado:') ?> <span class="label label-warning"><?= __('Pendiente de Pago') ?></span>
                        </p>
                        <p><?= __('Comunícate con un administrador para activar tu membresía.') ?></p>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="selected_membership"><?= __('Seleccionar Membresía') ?></label>
                                <?php echo $this->Form->select('selected_membership', $memberships, [
                                    'class' => 'form-control membership_id',
                                    'empty' => __('Seleccionar membresía')
                                ]); ?>
                                <small class="form-text text-muted"><?= __('La membresía quedará pendiente hasta que sea aprobada por un administrador.') ?></small>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="assign_class"><?= __('Clases disponibles con la membresía') ?></label>
                                <?php echo $this->Form->select('assign_class', [], [
                                    'class' => 'form-control class_list',
                                    'empty' => __('Seleccione primero una membresía'),
                                    'multiple' => 'multiple'
                                ]); ?>
                            </div>
                        </div> -->
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="membership_valid_from"><?= __('Fecha de inicio') ?></label>
                                <?php echo $this->Form->control('membership_valid_from', [
                                    'type' => 'text', 
                                    'class' => 'form-control datepicker mem_valid_from', 
                                    'placeholder' => __('mm/dd/aaaa'),
                                    'label' => false,
                                    'value' => date('m/d/Y')
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="membership_valid_to"><?= __('Fecha de fin') ?></label>
                                <?php echo $this->Form->control('membership_valid_to', [
                                    'type' => 'text', 
                                    'class' => 'form-control valid_to', 
                                    'label' => false,
                                    'disabled' => true,
                                    'placeholder' => __('Se calculará automáticamente')
                                ]); ?>
                                <input type='hidden' name='membership_valid_to' class='check'>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg"><?= __('Guardar Cambios') ?></button>
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
        
        // Inicializar multiselect para las clases
        $('.class_list').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: "<?php echo __('Seleccionar clases');?>",
            allSelectedText: "<?php echo __('Todas seleccionadas');?>",
            selectAllText : "<?php echo __('Seleccionar todas');?>",
            nSelectedText: "<?php echo __('seleccionadas');?>",
        });
        
        // Al cambiar la membresía, cargar las clases asociadas
        $('.membership_id').on('change', function() {
            var membershipId = $(this).val();
            if (membershipId) {
                // URL para obtener las clases de la membresía
                var url = "<?php echo $this->Url->build(['controller' => 'MemberRegistration', 'action' => 'getMembershipClasses']); ?>";
                
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { m_id: membershipId },
                    success: function(response) {
                        $('.class_list').html(response);
                        $('.class_list').multiselect('rebuild');
                    },
                    error: function(e) {
                        console.log(e.responseText);
                    }
                });
                
                // Calcular la fecha de fin de la membresía
                var startDate = $('.mem_valid_from').val();
                if (startDate) {
                    // URL para obtener la fecha de fin
                    var dateUrl = "<?php echo $this->Url->build(['controller' => 'MemberRegistration', 'action' => 'getMembershipEndDate']); ?>";
                    
                    $.ajax({
                        url: dateUrl,
                        type: "POST",
                        data: { date: startDate, membership: membershipId },
                        success: function(response) {
                            var endDate = new Date(response);
                            var formattedDate = (endDate.getMonth() + 1) + '/' + endDate.getDate() + '/' + endDate.getFullYear();
                            $('.valid_to').val(formattedDate);
                            $('.check').val(response);
                        },
                        error: function(e) {
                            console.log(e.responseText);
                        }
                    });
                }
            } else {
                // Si no hay membresía seleccionada, limpiar las clases
                $('.class_list').html('');
                $('.class_list').multiselect('rebuild');
                $('.valid_to').val('');
                $('.check').val('');
            }
        });
        
        // Al cambiar la fecha de inicio, recalcular la fecha de fin
        $('.mem_valid_from').on('change', function() {
            var membershipId = $('.membership_id').val();
            var startDate = $(this).val();
            
            if (membershipId && startDate) {
                var dateUrl = "<?php echo $this->Url->build(['controller' => 'MemberRegistration', 'action' => 'getMembershipEndDate']); ?>";
                
                $.ajax({
                    url: dateUrl,
                    type: "POST",
                    data: { date: startDate, membership: membershipId },
                    success: function(response) {
                        var endDate = new Date(response);
                        var formattedDate = (endDate.getMonth() + 1) + '/' + endDate.getDate() + '/' + endDate.getFullYear();
                        $('.valid_to').val(formattedDate);
                        $('.check').val(response);
                    },
                    error: function(e) {
                        console.log(e.responseText);
                    }
                });
            }
        });
    });
</script>