<?php
// Carga de scripts y estilos necesarios
echo $this->Html->css('dataTables.css');
echo $this->Html->script('jQuery-dataTables');
echo $this->Html->script('dataTables.bootstrap');
?>

<script>
	$(".content-wrapper").css("min-height", "2500px");
	$(document).ready(function() {
		// Filtros por sucursal y membresía (servidor)
		$("#branch-filter, #membership-filter").on("change", function() {
			applyFilters();
		});

		// Búsqueda rápida (cliente)
		$("#quick-search").on("keyup", function() {
			var table = $("#maintenance-members-tbl").DataTable();
			table.search($(this).val()).draw();
		});

		// Función para aplicar filtros
		function applyFilters() {
			var branchId = $("#branch-filter").val();
			var membershipId = $("#membership-filter").val();

			var url = "<?php echo $this->Url->build(['controller' => 'GymMember', 'action' => 'maintenanceMode']); ?>";
			var params = [];

			if (branchId != "all") {
				params.push("branch=" + branchId);
			}

			if (membershipId != "all") {
				params.push("membership=" + membershipId);
			}

			if (params.length > 0) {
				url += "?" + params.join("&");
			}

			window.location.href = url;
		}

		// Inicializar DataTable con configuración adecuada
		var dataTable = $("#maintenance-members-tbl").DataTable({
			"responsive": true,
			"order": [
				[1, "asc"]
			],
			"columnDefs": [{
					"targets": 0,
					"orderable": true
				}, // ID
				{
					"targets": 1,
					"orderable": true
				}, // Nombre
				{
					"targets": 2,
					"orderable": true
				}, // Membresía
				{
					"targets": 3,
					"orderable": true
				}, // Sucursal
				{
					"targets": 4,
					"orderable": true
				}, // Estado
				{
					"targets": 5,
					"orderable": true
				}, // Cuota mensual
				{
					"targets": 6,
					"orderable": true
				}, // Cuota mantenimiento
				{
					"targets": 7,
					"orderable": false
				} // Acciones
			],
			"language": {
				<?php echo $this->Gym->data_table_lang(); ?>
			},
			"pageLength": 20,
			"dom": '<"top">rt<"bottom"ip><"clear">'
		});

		var box_height = $(".box").height();
		var box_height = box_height + 100;
	});
</script>

<section class="content">
	<br>
	<div class="col-md-12 box box-default">
		<div class="box-header">
			<section class="content-header">
				<h1>
					<i class="fa fa-users"></i>
					<?php echo __("Gestión de Miembros en Modo Mantenimiento"); ?>
					<small><?php echo __("Solo miembros con cuota de mantenimiento"); ?></small>
				</h1>
				<ol class="breadcrumb">
					<a href="<?php echo $this->Url->build(['controller' => 'GymMember', 'action' => 'memberList']); ?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Lista de Miembros"); ?></a>
				</ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
			<!-- Panel de filtros simplificado -->
			<div class="row">
				<div class="col-md-12">
					<div class="filter-box">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label><?php echo __("Filtrar por Sucursal"); ?></label>
									<select name="branch_id" id="branch-filter" class="form-control">
										<option value="all"><?php echo __("Todas las Sucursales"); ?></option>
										<?php
										if (isset($branches)) {
											foreach ($branches as $id => $name) {
												$selected = (isset($branch_id) && $branch_id == $id) ? 'selected' : '';
												echo "<option value='{$id}' {$selected}>{$name}</option>";
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label><?php echo __("Filtrar por Membresía"); ?></label>
									<select name="membership_id" id="membership-filter" class="form-control">
										<option value="all"><?php echo __("Todas las Membresías"); ?></option>
										<?php
										if (isset($memberships)) {
											foreach ($memberships as $id => $label) {
												$selected = (isset($membership_id) && $membership_id == $id) ? 'selected' : '';
												echo "<option value='{$id}' {$selected}>{$label}</option>";
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label><?php echo __("Buscar Miembro"); ?></label>
									<div class="input-group">
										<input type="text" id="quick-search" class="form-control" placeholder="<?php echo __("Nombre, email, ID..."); ?>">
										<span class="input-group-btn">
											<button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Indicador de miembros mostrados -->
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i>
						<?php echo __("Se muestran solo miembros con membresías que tienen cuota de mantenimiento configurada."); ?>
					</div>
				</div>
			</div>

			<!-- Tabla de miembros -->
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" id="maintenance-members-tbl">
							<thead>
								<tr>
									<th><?= __("ID") ?></th>
									<th><?= __("Nombre") ?></th>
									<th><?= __("Membresía") ?></th>
									<th><?= __("Sucursal") ?></th>
									<th><?= __("Estado") ?></th>
									<th><?= __("Cuota Mensual") ?></th>
									<th><?= __("Cuota Mantenimiento") ?></th>
									<th><?= __("Acciones") ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($members)): ?>
									<?php foreach ($members as $member): ?>
										<tr>
											<td><?= h($member->is_maintenance_mode) ?></td>
											<td><?= h($member->first_name) . ' ' . h($member->last_name) ?></td>
											<td>
												<?php
												// Acceder al nombre de la membresía de forma segura
												if (isset($member->membership)) {
													if (is_object($member->membership)) {
														echo h($member->membership->membership_label);
													} elseif (is_array($member->membership)) {
														echo h($member->membership['membership_label']);
													}
												} else {
													echo __('No asignada');
												}
												?>
											</td>
											<td>
												<?php
												// Acceder al nombre de la sucursal de la membresía de forma segura
												if (isset($member->membership) && isset($member->membership->gym_branch)) {
													if (is_object($member->membership->gym_branch)) {
														echo h($member->membership->gym_branch->name);
													} elseif (is_array($member->membership->gym_branch)) {
														// Comprobamos que exista la clave 'name' en el array
														echo isset($member->membership->gym_branch['name'])
															? h($member->membership->gym_branch['name'])
															: __('No disponible');
													}
												} elseif (isset($member->gym_branch)) {
													// Alternativa: usar la sucursal del miembro
													if (is_object($member->gym_branch)) {
														echo h($member->gym_branch->name);
													} elseif (is_array($member->gym_branch)) {
														// Comprobamos que exista la clave 'name' en el array
														echo isset($member->gym_branch['name'])
															? h($member->gym_branch['name'])
															: __('No disponible');
													}
												} else {
													echo __('No asignada');
												}
												?>
											</td>
											<td>
												<?php if (isset($member->is_maintenance_mode) && $member->is_maintenance_mode): ?>
													<span class="label label-warning"><?= __("En Mantenimiento") ?></span>
													<br>
													<small>
														<?= __("Desde") ?>: <?= date('d/m/Y', strtotime($member->maintenance_start_date)) ?>
														<?php if (!empty($member->maintenance_end_date)): ?>
															<br>
															<?= __("Hasta") ?>: <?= date('d/m/Y', strtotime($member->maintenance_end_date)) ?>
														<?php endif; ?>
													</small>
												<?php else: ?>
													<span class="label label-success"><?= __("Activo") ?></span>
												<?php endif; ?>
											</td>
											<td>
												<?php
												// Acceder a la cuota mensual de forma segura
												if (isset($member->membership)) {
													if (is_object($member->membership)) {
														echo $this->Gym->get_currency_symbol() . h($member->membership->membership_amount);
													} elseif (is_array($member->membership)) {
														echo $this->Gym->get_currency_symbol() . h($member->membership['membership_amount']);
													}
												} else {
													echo $this->Gym->get_currency_symbol() . '0.00';
												}
												?>
											</td>
											<td>
												<?php
												// Acceder a la cuota de mantenimiento de forma segura
												if (isset($member->membership)) {
													if (is_object($member->membership)) {
														echo $this->Gym->get_currency_symbol() . h($member->membership->maintenance_fee);
													} elseif (is_array($member->membership)) {
														echo $this->Gym->get_currency_symbol() . h($member->membership['maintenance_fee']);
													}
												} else {
													echo $this->Gym->get_currency_symbol() . '0.00';
												}
												?>
											</td>
											<td>
												<div class="btn-group">
													<?php if (isset($member->is_maintenance_mode) && $member->is_maintenance_mode == 1): ?>
														<!-- Miembro en modo mantenimiento: mostrar botón para desactivar -->
														<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#deactivateModal<?= $member->id ?>">
															<i class="fa fa-toggle-off"></i> <?= __("Desactivar") ?>
														</button>
													<?php else: ?>
														<!-- Miembro normal: mostrar botón para activar -->
														<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#activateModal<?= $member->id ?>">
															<i class="fa fa-toggle-on"></i> <?= __("Activar") ?>
														</button>
													<?php endif; ?>

													<a href="<?php echo $this->Url->build(['controller' => 'GymMember', 'action' => 'viewMember', $member->id]); ?>" class="btn btn-sm btn-info">
														<i class="fa fa-eye"></i> <?= __("Ver") ?>
													</a>
												</div>
											</td>
										</tr>

										<!-- Modal para Activar Mantenimiento -->
										<div class="modal fade" id="activateModal<?= $member->id ?>" tabindex="-1" role="dialog">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														<h4 class="modal-title"><?= __("Activar Modo Mantenimiento") ?></h4>
													</div>
													<?= $this->Form->create(null, ['url' => ['action' => 'toggleMaintenanceMode', $member->id]]) ?>
													<div class="modal-body">
														<p><?= __("¿Está seguro que desea activar el modo mantenimiento para {0}?", "<strong>{$member->first_name} {$member->last_name}</strong>") ?></p>
														<p><?= __("Durante este período:") ?></p>
														<ul>
															<li><?= __("El miembro no podrá reservar clases") ?></li>
															<li><?= __("Solo se cobrará la cuota de mantenimiento") ?></li>
															<li><?= __("Se cancelarán las reservas futuras") ?></li>
														</ul>

														<div class="form-group">
															<label><?= __("Fecha de Finalización (opcional)") ?></label>
															<input type="date" name="end_date" class="form-control">
														</div>

														<div class="form-group">
															<label><?= __("Notas") ?></label>
															<textarea name="notes" class="form-control" rows="3" placeholder="<?= __("Razón del cambio a modo mantenimiento") ?>"></textarea>
														</div>

														<input type="hidden" name="activate" value="1">
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Cancelar") ?></button>
														<button type="submit" class="btn btn-warning"><?= __("Activar Mantenimiento") ?></button>
													</div>
													<?= $this->Form->end() ?>
												</div>
											</div>
										</div>

										<!-- Modal para Desactivar Mantenimiento -->
										<div class="modal fade" id="deactivateModal<?= $member->id ?>" tabindex="-1" role="dialog">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														<h4 class="modal-title"><?= __("Desactivar Modo Mantenimiento") ?></h4>
													</div>
													<?= $this->Form->create(null, ['url' => ['action' => 'toggleMaintenanceMode', $member->id]]) ?>
													<div class="modal-body">
														<p><?= __("¿Está seguro que desea desactivar el modo mantenimiento para {0}?", "<strong>{$member->first_name} {$member->last_name}</strong>") ?></p>
														<p><?= __("El miembro volverá a su estado normal:") ?></p>
														<ul>
															<li><?= __("Podrá reservar clases normalmente") ?></li>
															<li><?= __("Se cobrará la cuota completa") ?></li>
														</ul>

														<input type="hidden" name="activate" value="0">
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Cancelar") ?></button>
														<button type="submit" class="btn btn-success"><?= __("Desactivar Mantenimiento") ?></button>
													</div>
													<?= $this->Form->end() ?>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="8" class="text-center"><?= __("No se encontraron miembros con cuota de mantenimiento") ?></td>
									</tr>
								<?php endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<th><?= __("ID") ?></th>
									<th><?= __("Nombre") ?></th>
									<th><?= __("Membresía") ?></th>
									<th><?= __("Sucursal") ?></th>
									<th><?= __("Estado") ?></th>
									<th><?= __("Cuota Mensual") ?></th>
									<th><?= __("Cuota Mantenimiento") ?></th>
									<th><?= __("Acciones") ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>

			<!-- Información de paginación -->
			<div class="row">
				<div class="col-md-12">
					<div id="dataTable-info" class="dataTables_info"></div>
				</div>
			</div>
		</div>
	</div>
</section>