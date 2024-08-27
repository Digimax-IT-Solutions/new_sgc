<?php

//Guard 
require_once '_guards.php';
Guard::warehouseOnly();
?>





<?php require 'templates/header.php' ?>
<?php require 'templates/warehouse_sidebar.php' ?>
<div class="main">
    <?php require 'templates/warehouse_navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>HISUMCO</strong> WAREHOUSE</h1>
        </div>
    </main>

</div>



<?php require 'templates/footer.php' ?>