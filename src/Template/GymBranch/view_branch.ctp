
<script>
$(document).ready(function(){	
	var box_height = $(".box").height();
	var box_height = box_height + 100 ;
});
</script>

<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-eye"></i> 
				<?php echo __("View Branch");?>
			  </h1>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("GymBranch","branchList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Branches List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<div class="row">
			<div class="col-md-6 col-sm-12 col-xs-12 no-padding">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<table class="table table-bordered">
						<tr>
							<th width="40%"><i class="fa fa-building"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Branch Name");?></th>
							<td><?php echo $data['name'];?></td>
						</tr>
						<tr>
							<th><i class="fa fa-map-marker"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Address");?></th>
							<td><?php echo $data['address'];?></td>
						</tr>
						<tr>
							<th><i class="fa fa-phone"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Phone");?></th>
							<td><?php echo $data['phone'];?></td>
						</tr>
						<tr>
							<th><i class="fa fa-envelope"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Email");?></th>
							<td><?php echo $data['email'];?></td>
						</tr>
						<tr>
							<th><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Created Date");?></th>
							<td><?php echo date($this->Gym->getSettings("date_format"),strtotime($data['created_date']));?></td>
						</tr>
						<tr>
							<th><i class="fa fa-power-off"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Status");?></th>
							<td>
								<?php 
								if($data['is_active']) {
									echo "<span class='label label-success'>" . __('Active') . "</span>";
								} else {
									echo "<span class='label label-danger'>" . __('Inactive') . "</span>";
								}
								?>
							</td>
						</tr>
						<?php if(!empty($data['notes'])): ?>
						<tr>
							<th><i class="fa fa-sticky-note"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Notes");?></th>
							<td><?php echo $data['notes'];?></td>
						</tr>
						<?php endif; ?>
					</table>
				</div>
			</div>
			<div class="col-md-6 col-sm-12 col-xs-12">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 class="report-heading"><?php echo __("Branch Statistics"); ?></h4>
					<table class="table table-bordered">
						<tr>
							<th><i class="fa fa-users"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Total Members");?></th>
							<td><?php echo $data['total_members'] ?: 0;?></td>
						</tr>
						<tr>
							<th><i class="fa fa-calendar-check-o"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Active Members");?></th>
							<td><?php echo $data['active_members'] ?: 0;?></td>
						</tr>
						<tr>
							<th><i class="fa fa-universal-access"></i>&nbsp;&nbsp;&nbsp;<?php echo __("Total Staff");?></th>
							<td><?php echo $data['total_staff'] ?: 0;?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<h4 class="report-heading"><?php echo __("Recent Activities"); ?></h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo __("Date");?></th>
							<th><?php echo __("Member");?></th>
							<th><?php echo __("Class");?></th>
							<th><?php echo __("Status");?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($activities)): 
							foreach($activities as $activity): ?>
							<tr>
								<td><?php echo date($this->Gym->getSettings("date_format"),strtotime($activity['attendance_date']));?></td>
								<td><?php echo $activity['gym_member']['first_name'] . ' ' . $activity['gym_member']['last_name'];?></td>
								<td><?php echo $activity['class_id'] ? $this->Gym->get_class_name($activity['class_id']) : '-';?></td>
								<td>
									<?php if($activity['status'] == 'Present'): ?>
										<span class="label label-success"><?php echo __('Present');?></span>
									<?php else: ?>
										<span class="label label-danger"><?php echo __('Absent');?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach;
						else: ?>
						<tr>
							<td colspan="4" class="text-center"><?php echo __("No recent activities");?></td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>