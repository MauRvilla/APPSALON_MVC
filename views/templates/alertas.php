<?php 

    foreach ($alertas as $key => $mensajes) {
        //debuguear($alertas);
        foreach ($mensajes as $mensaje) {
            ?>

            <div class="alerta <?php echo $key; ?>">
                <?php echo $mensaje; ?>
            </div>

            <?php
        }
    }

?>