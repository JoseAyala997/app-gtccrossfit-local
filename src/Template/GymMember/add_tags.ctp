<section class="content">
    <div id="main-wrapper">
        <div class="col-md-12 box box-default">
            <div class="box-header">
                <section class="content-header">
                    <h1>
                        <i class="fa fa-plus"></i>
                        <?php echo __("Agregar Tag"); ?>
                    </h1>
                    <ol class="breadcrumb">
                        <a href="<?php echo $this->Gym->createurl("GymMember", "viewMemberTags"); ?>" class="btn btn-flat btn-custom">
                            <i class="fa fa-tags"></i> <?php echo __("Listar Tags"); ?>
                        </a>
                    </ol>
                </section>
            </div>
            <hr>
            <div class="box-body">
                <?php echo $this->Form->create(null, ["class" => "form-horizontal"]); ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo __("Nombre del Tag"); ?></label>
                    <div class="col-sm-4">
                        <?php echo $this->Form->control("name", ["label" => false, "class" => "form-control"]); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo __("Color del Tag"); ?></label>
                    <div class="col-sm-4">
                        <?php echo $this->Form->control("color", ["type" => "color", "label" => false, "class" => "form-control", "value" => "#ffffff"]); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-4">
                        <?php echo $this->Form->button(__("Guardar"), ["class" => "btn btn-success"]); ?>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</section>