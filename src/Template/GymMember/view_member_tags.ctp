<section class="content">
    <div id="main-wrapper">
        <div class="col-md-12 box box-default">
            <div class="box-header">
                <section class="content-header">
                    <h1>
                        <i class="fa fa-tags"></i>
                        <?php echo __("Ver Tags de Miembros"); ?>
                    </h1>
                    <ol class="breadcrumb">
                        <a href="<?php echo $this->Gym->createurl("GymMember", "addTags"); ?>" class="btn btn-flat btn-custom">
                            <i class="fa fa-plus"></i> <?php echo __("Agregar Nuevo Tag"); ?>
                        </a>
                    </ol>
                </section>
            </div>
            <hr>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo __("ID"); ?></th>
                                    <th><?php echo __("Nombre del Tag"); ?></th>
                                    <th><?php echo __("Acciones"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tags as $tag): ?>
                                    <tr>
                                        <td><?php echo $tag->id; ?></td>
                                        <td>
                                            <span style="background-color: <?php echo h($tag->color); ?>; color: #fff; padding: 5px 10px; border-radius: 5px;">
                                                <?php echo h($tag->name); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo $this->Url->build(["action" => "editTags", $tag->id]); ?>" class="btn btn-primary">
                                                <i class="fa fa-edit"></i> <?php echo __("Editar"); ?>
                                            </a>
                                            <a href="<?php echo $this->Url->build(["action" => "deleteTags", $tag->id]); ?>" class="btn btn-danger" onclick="return confirm('<?php echo __("¿Estás seguro de que deseas eliminar este tag?"); ?>');">
                                                <i class="fa fa-trash"></i> <?php echo __("Eliminar"); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>