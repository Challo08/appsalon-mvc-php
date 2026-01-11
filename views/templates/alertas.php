<?php
// Primer foreach para acceder al key y el segundo para acceder a los mensajes

foreach ($alertas as $key => $mensajes):
    foreach ($mensajes as $mensaje):
?>

        <div class="alerta <?php echo $key; ?>">
            <?php echo $mensaje; ?>
        </div>

<?php
    endforeach;
endforeach;
?>