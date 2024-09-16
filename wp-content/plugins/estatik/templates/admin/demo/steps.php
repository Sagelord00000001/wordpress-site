<form action="" method="POST">
    <?php wp_nonce_field( 'es_demo_content', 'es_demo_content' ); ?>
    <div class="es-demo-wrapper">

        <div class="es-pagination js-es-demo-pagination">
            <a href="#step1" class="es-active">1</a>
            <a href="#step2">2</a>
            <a href="#step3">3</a>
            <a href="#step4">4</a>
        </div>

        <?php es_load_template( 'admin/demo/steps/step1.php' ); ?>
        <?php es_load_template( 'admin/demo/steps/step2.php' ); ?>
        <?php es_load_template( 'admin/demo/steps/step3.php' ); ?>
        <?php es_load_template( 'admin/demo/steps/step4.php' ); ?>
    </div>
</form>
