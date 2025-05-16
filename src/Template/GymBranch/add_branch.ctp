
<?php
$session = $this->request->session();
?>
<script type="text/javascript">
$(document).ready(function() {
	var box_height = $(".box").height();
	var box_height = box_height + 100 ;
	$(".content-wrapper").css("height",box_height+"px");
});
</script>

<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-building"></i>
				<?php echo $title;?>
			  </h1>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("GymBranch","branchList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Branches List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<?php	
			echo $this->Form->create("add_branch",["type"=>"file","class"=>"validateForm form-horizontal","role"=>"form"]);
			echo "<fieldset><legend>". __('Branch Information')."</legend>";
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="name">'. __("Branch Name").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"name","class"=>"form-control validate[required]","value"=>(($edit)?$data['name']:''),"id"=>"name"]);
			echo "</div>";	
			echo "</div>";

			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="address">'. __("Address").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"address","class"=>"form-control validate[required]","value"=>(($edit)?$data['address']:''),"id"=>"address"]);
			echo "</div>";	
			echo "</div>";

			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="phone">'. __("Phone").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"phone","class"=>"form-control validate[required,custom[phone]]","value"=>(($edit)?$data['phone']:''),"id"=>"phone"]);
			echo "</div>";	
			echo "</div>";

			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="email">'. __("Email").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"email","class"=>"form-control validate[required,custom[email]]","value"=>(($edit)?$data['email']:''),"id"=>"email"]);
			echo "</div>";	
			echo "</div>";

			if($edit)
			{
				echo "<div class='form-group'>";	
				echo '<label class="control-label col-md-2" for="is_active">'. __("Status").'<span class="text-danger"> *</span></label>';
				echo '<div class="col-md-6 checkbox">';
				echo $this->Form->checkbox("is_active",["value"=>"1","checked"=>(($edit && $data['is_active'])?true:false)]);
				echo "</div>";	
				echo "</div>";
			}
			
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2" for="notes">'. __("Notes").'</label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"notes","class"=>"form-control","value"=>(($edit)?$data['notes']:''),"id"=>"notes"]);
			echo "</div>";	
			echo "</div>";
			
			echo "<br>";
			echo "<div class='form-group'>";	
			echo '<label class="control-label col-md-2"></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->button(__("Save Branch"),['class'=>"btn btn-flat btn-success","name"=>"add_branch"]);
			echo "</div>";	
			echo "</div>";
			echo "</fieldset>";
			echo $this->Form->end();
		?>
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>