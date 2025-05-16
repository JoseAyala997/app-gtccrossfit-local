
<?php $session = $this->request->session()->read("User");?>
<script>
$(document).ready(function(){		
	$(".mydataTable").DataTable({
		"responsive": true,
		"order": [[ 1, "asc" ]],
		"language" : {<?php echo $this->Gym->data_table_lang();?>}
	});
});		
</script>

<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-building"></i>
				<?php echo __("Branches List");?>
			  </h1>
			   <?php
				if($session["role_name"] == "administrator")
				{ ?>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("GymBranch","addBranch");?>" class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Branch");?></a>
			  </ol>
			   <?php } ?>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<table class="mydataTable table table-striped" width="100%">
			<thead>
				<tr>
					<th><?php echo __("Branch Name");?></th>
					<th><?php echo __("Address");?></th>
					<th><?php echo __("Phone");?></th>					
					<th><?php echo __("Email");?></th>					
					<th><?php echo __("Created Date");?></th>					
					<th><?php echo __("Status");?></th>					
					<th><?php echo __("Action");?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($data as $row) {
					$status = $row['is_active'] ? __('Active') : __('Inactive');
					$statusClass = $row['is_active'] ? 'success' : 'danger';
					
					echo "<tr>
					<td>{$row['name']}</td>
					<td>{$row['address']}</td>
					<td>{$row['phone']}</td>
					<td>{$row['email']}</td>
					<td>".$this->Gym->get_db_format(date($this->Gym->getSettings("date_format"),strtotime($row['created_date'])))."</td>
					<td><span class='label label-{$statusClass}'>{$status}</span></td>
					<td>";
					
					if($session["role_name"] == "administrator" && $row['id'] != 1)
					{	
						$confirmMsg = __("Are you sure you want to delete this branch?");
						echo "<a href='{$this->request->base}/GymBranch/editBranch/{$row['id']}' title='".__('Edit')."' class='btn btn-flat btn-primary'><i class='fa fa-edit'></i></a>
						<a href='{$this->request->base}/GymBranch/deleteBranch/{$row['id']}' title='".__('Delete')."' class='btn btn-flat btn-danger' onClick=\"return confirm('$confirmMsg');\"><i class='fa fa-trash-o'></i></a>";
					} elseif($row['id'] == 1) {
						echo "<span class='text-muted'>" . __('Default Branch') . "</span>";
					}
					
					echo "</td></tr>";
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php echo __("Branch Name");?></th>
					<th><?php echo __("Address");?></th>
					<th><?php echo __("Phone");?></th>					
					<th><?php echo __("Email");?></th>					
					<th><?php echo __("Created Date");?></th>					
					<th><?php echo __("Status");?></th>					
					<th><?php echo __("Action");?></th>
				</tr>
			</tfoot>
		</table>
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>