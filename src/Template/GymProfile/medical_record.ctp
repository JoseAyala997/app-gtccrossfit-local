<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GymMedicalRecord $medical_data
 */
?>

<!-- Ficha Médica Panel -->
<div class="panel panel-white">
    <div class="panel-body">
        <?= $this->Form->create($medical_data, ['id' => 'medical_form', 'class' => 'form-horizontal']) ?>
        <div class="col-sm-12 text-center">
            <h3><?php echo __("Ficha Médica"); ?></h3>
            <hr>
        </div>

        <!-- Datos del miembro desde gym_member (solo lectura) -->
        <div class="form-group">
            <div class="col-sm-12">
                <h4><?php echo __("Información del Miembro"); ?></h4>
                <hr>
            </div>
        </div>

        <!-- Información del miembro y foto -->
        <div class="form-group">
            <div class="col-sm-2 text-center">
                <?php if (!empty($data['image'])) : ?>
                    <img src="<?= $this->request->webroot . 'webroot/upload/' . $data['image'] ?>" class="img-thumbnail" style="height:150px;">
                <?php else : ?>
                    <img src="<?= $this->request->webroot . 'webroot/img/Thumbnail-img.png' ?>" class="img-thumbnail" style="height:150px;">
                <?php endif; ?>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("Nombre Completo"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name']) ?>">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("ID de Miembro"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['member_id']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("Género"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['gender'] == 'male' ? __('Masculino') : ($data['gender'] == 'female' ? __('Femenino') : __('Otro'))) ?>">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("Fecha de Nacimiento"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h(isset($data['birth_date']) ? $data['birth_date']->format($this->Gym->getSettings("date_format")) : '') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("Teléfono"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['mobile']) ?>">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label"><?php echo __("Correo Electrónico"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['email']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <label class="control-label"><?php echo __("Dirección"); ?></label>
                        <input type="text" class="form-control" readonly value="<?= h($data['address'] . ', ' . $data['city'] . ', ' . $data['state'] . ' ' . $data['zipcode']) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm-12">
                <h4><?php echo __("Datos Personales Complementarios"); ?></h4>
                <hr>
            </div>
        </div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="cedula"><?php echo __("Cédula de Identidad"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->control('cedula', [
					'class' => 'form-control validate[required]',
					'label' => false
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="occupation"><?php echo __("Ocupación/Profesión"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->control('occupation', [
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="marital_status"><?php echo __("Estado Civil"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->select('marital_status', [
					'' => __("Seleccionar"),
					'single' => __("Soltero/a"),
					'married' => __("Casado/a"),
					'divorced' => __("Divorciado/a"),
					'widowed' => __("Viudo/a")
				], [
					'class' => 'form-control',
					'id' => 'marital_status'
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="gender"><?php echo __("Género"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->select('gender', [
					'' => __("Seleccionar"),
					'male' => __("Masculino"),
					'female' => __("Femenino"),
					'other' => __("Otro")
				], [
					'class' => 'form-control validate[required]',
					'id' => 'gender'
				]) ?>
			</div>
		</div>

		<!-- Contacto de emergencia -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Contacto de Emergencia"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="emergency_name"><?php echo __("Nombre"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->control('emergency_name', [
					'class' => 'form-control validate[required]',
					'label' => false
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="emergency_relation"><?php echo __("Parentesco"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->control('emergency_relation', [
					'class' => 'form-control validate[required]',
					'label' => false
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="emergency_phone"><?php echo __("Teléfono"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->control('emergency_phone', [
					'class' => 'form-control validate[required,custom[phone]]',
					'label' => false
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="emergency_address"><?php echo __("Dirección"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->control('emergency_address', [
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<!-- Datos del seguro médico -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Seguro Médico"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="insurance_type"><?php echo __("Tipo de Seguro"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->select('insurance_type', [
					'' => __("Seleccionar"),
					'ips' => __("IPS"),
					'private' => __("Privado"),
					'none' => __("Ninguno")
				], [
					'class' => 'form-control',
					'id' => 'insurance_type'
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="insurance_number"><?php echo __("Nº de Asegurado"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->control('insurance_number', [
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="insurance_plan"><?php echo __("Plan/Cobertura"); ?></label>
			<div class="col-sm-10">
				<?= $this->Form->control('insurance_plan', [
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<!-- Antecedentes médicos personales -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Antecedentes Médicos"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="chronic_diseases"><?php echo __("Enfermedades Crónicas"); ?></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('chronic_diseases', [
					'class' => 'form-control',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Indique si padece enfermedades crónicas como diabetes, hipertensión, etc."); ?></small>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="previous_surgeries"><?php echo __("Cirugías Previas"); ?></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('previous_surgeries', [
					'class' => 'form-control',
					'rows' => 3
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="allergies"><?php echo __("Alergias"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('allergies', [
					'class' => 'form-control validate[required]',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Si no tiene alergias, escriba 'Ninguna conocida'"); ?></small>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="current_medication"><?php echo __("Medicación Actual"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('current_medication', [
					'class' => 'form-control validate[required]',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Si no toma medicamentos, escriba 'Ninguno'"); ?></small>
			</div>
		</div>

		<!-- Antecedentes familiares -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Antecedentes Familiares"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="family_history"><?php echo __("Historial Familiar"); ?></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('family_history', [
					'class' => 'form-control',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Mencione si hay antecedentes familiares de enfermedades hereditarias"); ?></small>
			</div>
		</div>

		<!-- Hábitos y estilo de vida -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Hábitos y Estilo de Vida"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="smoke"><?php echo __("¿Fuma?"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->select('smoke', [
					'' => __("Seleccionar"),
					'yes' => __("Sí"),
					'no' => __("No"),
					'occasionally' => __("Ocasionalmente")
				], [
					'class' => 'form-control validate[required]',
					'id' => 'smoke'
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="alcohol"><?php echo __("¿Consume alcohol?"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->select('alcohol', [
					'' => __("Seleccionar"),
					'yes' => __("Sí"),
					'no' => __("No"),
					'occasionally' => __("Ocasionalmente")
				], [
					'class' => 'form-control validate[required]',
					'id' => 'alcohol'
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="physical_activity"><?php echo __("Actividad Física"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->select('physical_activity', [
					'' => __("Seleccionar"),
					'sedentary' => __("Sedentario"),
					'light' => __("Ligero"),
					'moderate' => __("Moderado"),
					'intense' => __("Intenso")
				], [
					'class' => 'form-control validate[required]',
					'id' => 'physical_activity'
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="diet"><?php echo __("Tipo de Dieta"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->select('diet', [
					'' => __("Seleccionar"),
					'regular' => __("Regular"),
					'vegetarian' => __("Vegetariana"),
					'vegan' => __("Vegana"),
					'keto' => __("Keto"),
					'other' => __("Otra")
				], [
					'class' => 'form-control',
					'id' => 'diet'
				]) ?>
			</div>
		</div>

		<!-- Información específica para mujeres -->
		<div class="form-group female-specific" style="display: none;">
			<div class="col-sm-12">
				<h4><?php echo __("Información Específica para Mujeres"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group female-specific" style="display: none;">
			<label class="col-sm-2 control-label" for="last_period"><?php echo __("Última Menstruación"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->control('last_period', [
					'type' => 'text',
					'class' => 'form-control datepicker',
					'label' => false,
					'value' => !empty($medical_data->last_period) ? $medical_data->last_period->format($this->Gym->getSettings("date_format")) : ''
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="pregnancies"><?php echo __("Embarazos"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->control('pregnancies', [
					'type' => 'number',
					'min' => 0,
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<div class="form-group female-specific" style="display: none;">
			<label class="col-sm-2 control-label" for="contraceptive"><?php echo __("Método Anticonceptivo"); ?></label>
			<div class="col-sm-10">
				<?= $this->Form->control('contraceptive', [
					'class' => 'form-control',
					'label' => false
				]) ?>
			</div>
		</div>

		<!-- Información para CrossFit -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Información para CrossFit"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="fitness_level"><?php echo __("Nivel de Condición Física"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-4">
				<?= $this->Form->select('fitness_level', [
					'' => __("Seleccionar"),
					'beginner' => __("Principiante"),
					'intermediate' => __("Intermedio"),
					'advanced' => __("Avanzado")
				], [
					'class' => 'form-control validate[required]',
					'id' => 'fitness_level'
				]) ?>
			</div>

			<label class="col-sm-2 control-label" for="experience"><?php echo __("Experiencia en CrossFit"); ?></label>
			<div class="col-sm-4">
				<?= $this->Form->select('experience', [
					'' => __("Seleccionar"),
					'none' => __("Sin experiencia"),
					'less_6m' => __("Menos de 6 meses"),
					'6m_1y' => __("6 meses a 1 año"),
					'1y_3y' => __("1 a 3 años"),
					'more_3y' => __("Más de 3 años")
				], [
					'class' => 'form-control',
					'id' => 'experience'
				]) ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="injuries"><?php echo __("Lesiones Previas"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('injuries', [
					'class' => 'form-control validate[required]',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Si no tiene lesiones previas, escriba 'Ninguna'"); ?></small>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="limitations"><?php echo __("Limitaciones Físicas"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('limitations', [
					'class' => 'form-control validate[required]',
					'rows' => 3
				]) ?>
				<small class="text-muted"><?php echo __("Si no tiene limitaciones físicas, escriba 'Ninguna'"); ?></small>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="goals"><?php echo __("Objetivos de Entrenamiento"); ?><span class="text-danger">*</span></label>
			<div class="col-sm-10">
				<?= $this->Form->textarea('goals', [
					'class' => 'form-control validate[required]',
					'rows' => 3
				]) ?>
			</div>
		</div>

		<!-- Consentimiento -->
		<div class="form-group">
			<div class="col-sm-12">
				<h4><?php echo __("Consentimiento"); ?></h4>
				<hr>
			</div>
		</div>

		<div class="form-group container">
			<div class="col-sm-12">
				<div class="checkbox">
					<label>
						<?= $this->Form->checkbox('consent_treatment', [
							'class' => 'validate[required]',
							'value' => 1,
							'hiddenField' => false
						]) ?>
						<?php echo __("Doy mi consentimiento para recibir tratamiento médico de emergencia en caso necesario."); ?>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?= $this->Form->checkbox('consent_share_info', [
							'class' => 'validate[required]',
							'value' => 1,
							'hiddenField' => false
						]) ?>
						<?php echo __("Autorizo al personal del gimnasio a compartir mi información médica con profesionales de la salud en caso de emergencia."); ?>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?= $this->Form->checkbox('confirm_true_info', [
							'class' => 'validate[required]',
							'value' => 1,
							'hiddenField' => false
						]) ?>
						<?php echo __("Confirmo que la información proporcionada es verdadera y completa según mi conocimiento."); ?>
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<!-- Botón Guardar -->
				<?= $this->Form->button(__('Guardar'), [
					'class' => 'btn btn-lg btn-success',
					'name' => 'save_medical_form',
					'type' => 'submit'
				]) ?>

			</div>
		</div>
		<?= $this->Form->end() ?>
	</div>
</div>

<script>
	$(document).ready(function() {
		$("#medical_form").validationEngine();

		// Inicializar datepicker
		$(".datepicker").datepicker({
			format: '<?php echo $this->Gym->getSettings("date_format"); ?>',
			autoclose: true
		});

		// Mostrar/ocultar campos específicos para mujeres
		$("#gender").change(function() {
			if ($(this).val() == "female") {
				$(".female-specific").show();
			} else {
				$(".female-specific").hide();
			}
		});

		// Ejecutar al cargar
		if ($("#gender").val() == "female") {
			$(".female-specific").show();
		}
	});
</script>