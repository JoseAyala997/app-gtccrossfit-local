<?php

use Cake\Log\Log;

$session = $this->request->session();
$is_rtl = $session->read("User.is_rtl");
$style = ($is_rtl == "1") ? "style='float:right;'" : "";
$current_controller = $this->request->controller;
$current_action = $this->request->action;
?>

<style>
    .sidebar-mini.sidebar-collapse .sidebar-menu>li>a {
        padding: 11px 5px 28px 15px;
    }
</style>
<br>

<ul class="sidebar-menu">
    <li class="treeview <?php echo ($current_controller == "Dashboard") ? "active" : ""; ?>">
        <a href="<?php echo $this->Gym->createurl("Dashboard", "index"); ?>">
            <i class="icone" <?php echo $style; ?>><img src="<?php echo $this->request->base . "/webroot/img/icon/dashboard.png"; ?>"></i>
            <span>&nbsp;<?php echo __('Dashboard'); ?></span>
        </a>
    </li>
    <li>
        <a href="<?= $this->Url->build(['controller' => 'GymMemberCredits', 'action' => 'viewCredits']) ?>">
            <i class="fa fa-ticket"></i> <span><?= __('Mis Créditos') ?></span>
        </a>
    </li>
    <?php
    // Verificar si el miembro es un drop-in
    $isDropIn = false;
    $memberId = $session->read('User.id'); // Usar read() para obtener el ID del usuario

    if (!empty($memberId)) {
        try {
            // Intentar obtener el valor de is_drop_in
            $connection = \Cake\Datasource\ConnectionManager::get('default');
            $columns = $connection->execute("SHOW COLUMNS FROM gym_member LIKE 'is_drop_in'")->fetchAll();

            if (!empty($columns)) {
                $gymMemberTable = \Cake\ORM\TableRegistry::get('gym_member');
                $member = $gymMemberTable->get($memberId);

                // Para usuarios específicos: si el ID es 74 y fueron creados como drop-in
                // pero el campo is_drop_in es NULL, tratarlos como drop-ins
                if ($memberId == 74 && $member->is_drop_in === null && $member->membership_status == 'Continue') {
                    $isDropIn = true;
                } else {
                    // Verificación normal
                    $isDropIn = ($member->is_drop_in == 1);
                }

                // Log para depuración
                $value = $member->is_drop_in;
            }
        } catch (\Exception $e) {
            // Si hay algún error, simplemente no mostraremos el menú de drop-in
            $isDropIn = false;
            Log::error("Error al verificar is_drop_in: " . $e->getMessage());
        }
    }
    ?>

    <!-- Solo mostrar si es un drop-in -->
    <?php if ($isDropIn): ?>
        <li class="treeview <?php echo ($current_controller == "ClassBooking" && $current_action == "memberDropIn") ? "active" : ""; ?>">
            <a href="<?php echo $this->Gym->createurl("ClassBooking", "memberDropIn"); ?>">
                <i class="icone" <?php echo $style; ?>><img src="<?php echo $this->request->base . "/webroot/img/icon/account.png"; ?>"></i>
                <span>&nbsp;<?php echo __('Gestionar Drop-In'); ?></span>
            </a>
        </li>
    <?php endif; ?>
    <li class="<?php echo ($current_controller == "GymDailyWorkout" && $current_action == "dailynote") ? "active" : ""; ?>">
        <a href="<?php echo $this->Gym->createurl("GymDailyWorkout", "dailynote"); ?>">
        <i class="fa fa-clipboard"></i>
            <span>&nbsp;<?php echo __('Mis Notas'); ?></span>
        </a>
    </li>
    <li class="menu-medical-record <?php echo ($this->request->getRequestTarget() === '/gym-profile/medical-record') ? "active" : ""; ?>">
        <a href="<?php echo $this->Gym->createurl("GymProfile", "medicalRecord"); ?>">
            <i class="fa fa-notes-medical"></i>
            <span>&nbsp;<?php echo __('Ficha Médica'); ?></span>
        </a>
    </li>
    <?php
    $img_path = $this->request->base . "/webroot/img/icon/";
    foreach ($menus as $menu) {
        // Para el menú de Reports
        if ($menu["controller"] == "Report") {
    ?>
            <li class="treeview <?php echo ($current_controller == "Reports") ? "active" : ""; ?>">
                <a href="<?php echo $this->Gym->createurl("Reports", "monthlyworkoutreport"); ?>">
                    <i class="icone" <?php echo $style; ?>><img src="<?php echo $img_path . $menu["menu_icon"]; ?>"></i><span><?php echo __("Report"); ?></span>
                </a>
            </li>
        <?php
        } else {
        ?>
            <li class="treeview <?php echo ($current_controller == $menu["controller"] && $current_action != "dailynote") ? "active" : ""; ?>">
                <a href="<?php echo $menu["page_link"]; ?>">
                    <i class="icone" <?php echo $style; ?>><img src="<?php echo $img_path . $menu["menu_icon"]; ?>"></i>
                    <span>&nbsp;<?php echo __($menu["menu_title"]); ?></span>
                </a>
            </li>
    <?php
        }
    }
    ?>
</ul>