<?php
echo $this->Html->css('fullcalendar');
echo $this->Html->script('moment.min');
echo $this->Html->script('fullcalendar.min');
echo $this->Html->script('lang-all');
?>
<style>
	.content-wrapper, .right-side {   
		background-color: #F1F4F9 !important;
	}
	.panel-heading{
		height: 52px;
		background-color: #1DB198;
		padding: 0 0 0 21px;
		margin: 0;
	}
	.panel-heading .panel-title {	
		font-size: 16px;
		color :#eee;
		float: left;
		margin: 0;
		padding: 0;
		line-height :3em;
		font-weight: 600; 
	}
</style>
<script>	
	$(document).ready(function() {	
		$('#calendar').fullCalendar({
			header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
			timeFormat: 'H(:mm)',
			lang: '<?php echo $cal_lang;?>',
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: <?php echo json_encode($cal_array);?>
			
		});
	});
</script>
<?php 
	$session = $this->request->session();
	$pull = ($session->read("User.is_rtl") == "1") ? "pull-left" : "pull-right";
?>
<section class="content">
	<div id="main-wrapper">		
		<div class="row"><!-- Start Row2 -->
			<div class="left_section col-md-8 col-sm-8">
				<?php
				$access = $this->Gym->get_member_accessright('member');
				if($access == 1)
				{
				?>
					<div class="col-lg-3 col-md-3 col-xs-6 col-sm-6">
						<a href="<?php echo $this->request->base ."/GymMember/memberList";?>">
							<div class="panel info-box panel-white">
								<div class="panel-body member">
									<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/member.png" class="dashboard_background">
									<div class="info-box-stats">
										<p class="counter"><?php echo $members;?> <span class="info-box-title"><?php echo __("Member");?></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php
				}
				$access = $this->Gym->get_member_accessright('staff_member');
				if($access == 1)
				{
				?>
					<div class="col-lg-3 col-md-3 col-xs-6 col-sm-6">
						<a href="<?php echo $this->request->base ."/staff-members/staff-list";?>">
							<div class="panel info-box panel-white">
								<div class="panel-body staff-member">
									<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/staff-member.png" class="dashboard_background">
									<div class="info-box-stats">
										<p class="counter"><?php echo $staff_members;?><span class="info-box-title"><?php echo __("Staff Member");?></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php
				}
				$access = $this->Gym->get_member_accessright('group');
				if($access == 1)
				{
				?>
					<div class="col-lg-3 col-md-3 col-xs-6 col-sm-6">
						<a href="<?php echo $this->request->base ."/gym-group/group-list";?>">
							<div class="panel info-box panel-white">
								<div class="panel-body group">
									<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/group.png" class="dashboard_background">
									<div class="info-box-stats groups-label">
										<p class="counter"><?php echo $groups;?><span class="info-box-title"><?php echo __("Group");?></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php
				}
				$access = $this->Gym->get_member_accessright('message');
				if($access == 1)
				{
				?>
					<div class="col-lg-3 col-md-3 col-xs-6 col-sm-6">
						<a href="<?php echo $this->request->base ."/gym-message/inbox";?>">
							<div class="panel info-box panel-white">
								<div class="panel-body message no-padding">
									<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/message.png" class="dashboard_background_message">
									<div class="info-box-stats">
										<p class="counter"><?php echo $messages;?><span class="info-box-title"><?php echo __("Message");?></span></p>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php
				}			
				?>
			</div>
			<div class="col-md-4 membership-list <?php echo $pull;?> col-sm-4 col-xs-12">
				<?php
				// Panel de Motivación
				$weekly_goal = 6; // Meta semanal
				$monthly_goal = 14; // Meta mensual

				// Determinar el mensaje motivacional basado en las asistencias
				$motivational_message = '';
				// Determinar el mensaje motivacional basado en las asistencias semanales
				if ($weekly_attendance >= $weekly_goal) {
					$motivational_message = __('¡Excelente trabajo! Has alcanzado tu meta semanal con {0}/{1} días. ¡Sigue así y mantén el ritmo!', $weekly_attendance, $weekly_goal);
				} elseif ($weekly_attendance >= $weekly_goal - 1) {
					$motivational_message = __('¡Casi lo logras! Solo te falta un día para alcanzar tu meta semanal. ¡Tú puedes!', $weekly_attendance, $weekly_goal);
				} elseif ($weekly_attendance >= $weekly_goal / 2) {
					$motivational_message = __('¡Muy bien! Vamos {0}/{1} días esta semana. ¡Estás en el camino correcto!', $weekly_attendance, $weekly_goal);
				} elseif ($weekly_attendance > 0) {
					$motivational_message = __('¡No te rindas! Llevas {0}/{1} días esta semana. ¡Cada día cuenta!', $weekly_attendance, $weekly_goal);
				} else {
					$motivational_message = __('No hemos visto tu asistencia esta semana. ¡Es un buen momento para empezar!');
				}

				// Agregar mensajes motivacionales basados en las asistencias mensuales
				if ($monthly_attendance >= $monthly_goal) {
					$motivational_message .= '<br>' . __('¡Increíble! Este mes estás on fire con {0}/{1} días. ¡Eres un ejemplo a seguir!', $monthly_attendance, $monthly_goal);
				} elseif ($monthly_attendance >= $monthly_goal - 2) {
					$motivational_message .= '<br>' . __('¡Estás muy cerca de tu meta mensual! Solo faltan unos días para alcanzarla. ¡No te detengas!', $monthly_attendance, $monthly_goal);
				} elseif ($monthly_attendance >= $monthly_goal / 2) {
					$motivational_message .= '<br>' . __('Vas muy bien este mes, llevas {0}/{1} días. ¡Mantén el ritmo y sigue avanzando!', $monthly_attendance, $monthly_goal);
				} elseif ($monthly_attendance > 0) {
					$motivational_message .= '<br>' . __('Este mes llevas {0}/{1} días. ¡Cada paso cuenta para alcanzar tus metas!', $monthly_attendance, $monthly_goal);
				} else {
					$motivational_message .= '<br>' . __('No hemos registrado asistencias este mes. ¡Es un buen momento para empezar a moverte!');
				}
				?>

				<div class="panel panel-white" style="border: 2px solid #1DB198; background-color: #f9f9f9;">
					<div class="panel-heading" style="background-color: #1DB198; color: white; text-align: center;">
						<h3 class="panel-title" style="font-size: 20px; font-weight: bold;"><?php echo __("¡Motivación!"); ?></h3>
					</div>
					<div class="panel-body" style="text-align: center; padding: 20px;">
						<p style="font-size: 18px; font-weight: bold; color: #333;"><?php echo $motivational_message; ?></p>
					</div>
				</div>

				<?php
				// Panel de Membresía
				$access = $this->Gym->get_member_accessright('membership');
				if ($access == 1) {
				?>
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Membership"); ?></h3>						
						</div>
						<div class="panel-body">
							<?php 
							foreach ($membership as $ms) {
								$m_img = (!empty($ms["gmgt_membershipimage"])) ? $ms["gmgt_membershipimage"] : "Thumbnail-img2.png";
								?>
								<p>
									<img src="<?php echo $this->request->base ."/webroot/upload/" .$m_img; ?>" height="40px" width="40px" class="img-circle">
									<?php echo $ms["membership_label"]; ?>
								</p>
								<?php
							} ?>
						</div>
					</div>
				<?php
				}

				// Panel de Lista de Grupos
				$access = $this->Gym->get_member_accessright('group');
				if ($access == 1) {
				?>
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Group List"); ?></h3>						
						</div>
						<div class="panel-body">
							<?php 
							if (!empty($groups_data)) {
								foreach ($groups_data as $gd) {
									$image = ($gd['image'] == "") ? "Thumbnail-img.png" : $gd['image'];
									?>
									<p>
										<img src="<?php echo $this->request->base ."/webroot/upload/" .$image; ?>" height="40px" width="40px" class="img-circle">
										<?php echo $gd["name"]; ?>
									</p>
									<?php
								}
							} else { ?>
								<p><?php echo __('No Data Found.') ?></p>
							<?php } ?>
						</div>
					</div>
				<?php
				}
				?>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-body">
						<div id="calendar">
						</div>
					</div>
				</div>
			</div>	<!-- End row2 -->
			<div class="row inline"><!-- Start Row3 -->
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Weight Progress Report");?></h3>						
						</div>
						<div class="panel-body">	
							<div id="weight_report" style="width: 100%; height: 250px;">
								<?php 
								$GoogleCharts = new GoogleCharts;
								$weight_chart = $GoogleCharts->load( 'LineChart' , 'weight_report' )->get( $weight_data["data"] , $weight_data["option"] );
						
								if(empty($weight_data["data"]) || count($weight_data["data"]) == 1)
									echo __('There is not enough data to generate report'); ?>
							</div>  
							<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
							<script type="text/javascript">
								<?php 
								if(!empty($weight_data["data"]) && count($weight_data["data"]) > 1)
								echo $weight_chart;?>
							</script>
						</div>
					</div>
				</div>
			
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Waist  Progress Report");?></h3>						
						</div>
						<div class="panel-body">	
							<div id="waist_report" style="width: 100%; height: 250px;float:left;">
								<?php 
								$GoogleCharts = new GoogleCharts;
								$waist_chart = $GoogleCharts->load( 'LineChart' , 'waist_report' )->get( $waist_data["data"] , $waist_data["option"] );
						
								if(empty($waist_data["data"]) || count($waist_data["data"]) == 1)
									echo __('There is not enough data to generate report'); ?>
							</div>  
							<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
							<script type="text/javascript">
								<?php 
								if(!empty($waist_data["data"]) && count($waist_data["data"]) > 1)
								echo $waist_chart;?>
							</script>
						</div>
					</div>
				</div>
			
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Thigh Progress Report");?></h3>						
						</div>
						<div class="panel-body">	
							<div id="thing_report" style="width: 100%; height: 250px;float:left;">
								<?php 
								$GoogleCharts = new GoogleCharts;
								$thing_chart = $GoogleCharts->load( 'LineChart' , 'thing_report' )->get( $thigh_data["data"] , $thigh_data["option"] );
						
								if(empty($thigh_data["data"]) || count($thigh_data["data"]) == 1)
									echo __('There is not enough data to generate report'); ?>
							</div>  
							<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
							<script type="text/javascript">
								<?php 
								if(!empty($thigh_data["data"]) && count($thigh_data["data"]) > 1)
									echo $thing_chart;?>
							</script>
						</div>
					</div>
				</div>
			
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo __("Arms Progress Report");?></h3>						
						</div>
						<div class="panel-body">	
							<div id="arms_report" style="width: 100%; height: 250px;float:left;">
								<?php 
								$GoogleCharts = new GoogleCharts;
								$arms_chart = $GoogleCharts->load( 'LineChart' , 'arms_report' )->get( $arms_data["data"] , $arms_data["option"] );
						
								if(empty($arms_data["data"]) || count($arms_data["data"]) == 1)
									echo __('There is not enough data to generate report'); ?>
							</div>  
							<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
							<script type="text/javascript">
							<?php 
								if(!empty($arms_data["data"]) && count($arms_data["data"]) > 1)
									echo $arms_chart;?>
							</script>
						</div>
					</div>
				</div>
			
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Height Progress Report");?></h3>						
					</div>
					<div class="panel-body">	
						<div id="height_report" style="width: 100%; height: 250px;float:left;">
							<?php 
							$GoogleCharts = new GoogleCharts;
							$height_chart = $GoogleCharts->load( 'LineChart' , 'height_report' )->get( $height_data["data"] , $height_data["option"] );
						
							if(empty($height_data["data"]) || count($height_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>  
						<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
						<script type="text/javascript">
							<?php 
							if(!empty($height_data["data"]) && count($height_data["data"]) > 1)
							echo $height_chart;?>
						</script>
					</div>
				</div>
			</div>
			
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Chest Progress Report");?></h3>						
					</div>
					<div class="panel-body">	
						<div id="chest_report" style="width: 100%; height: 250px;float:left;">
							<?php 
							$GoogleCharts = new GoogleCharts;
							$chest_chart = $GoogleCharts->load( 'LineChart' , 'chest_report' )->get( $chest_data["data"] , $chest_data["option"] );
						
							if(empty($chest_data["data"]) || count($chest_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>  
						<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
						<script type="text/javascript">
							<?php 
							if(!empty($chest_data["data"]) && count($chest_data["data"]) > 1)
							echo $chest_chart;?>
						</script>
					</div>
				</div>
			</div>
			
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Fat Progress Report");?></h3>						
					</div>
					<div class="panel-body">	
						<div id="fat_report" style="width: 100%; height: 250px;float:left;">
							<?php 
							$GoogleCharts = new GoogleCharts;
							$fat_chart = $GoogleCharts->load( 'LineChart' , 'fat_report' )->get( $fat_data["data"] , $fat_data["option"] );
						
							if(empty($fat_data["data"]) || count($fat_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>  
						<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
						<script type="text/javascript">
							<?php 
							if(!empty($fat_data["data"]) && count($fat_data["data"]) > 1)
							echo $fat_chart;?>
						</script>
					</div>
				</div>
			</div>
		</div><!-- End Row3 -->
			
			
	</div>
 </div>
</section>