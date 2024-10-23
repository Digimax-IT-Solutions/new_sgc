<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('system_settings');

$page = 'system_settings';

?>

<?php require 'templates/header.php' ?>
<?php require 'templates/navbar.php' ?>
<?php require 'templates/sidebar.php' ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>System Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">System Settings</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-body">
                <h1>Settings</h1>
                <form action="/submit_settings" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                       
                            <div class="form-group">
                                <label for="logo">Logo:</label>
                                <input type="file" id="logo" name="logo">
                            </div>

                            <div class="form-group">
                                <label for="banner">Banner:</label>
                                <input type="file" id="banner" name="banner">
                            </div>

                            <div class="form-group">
                                <label for="company_title">Company Title:</label>
                                <input type="text" id="company_title" name="company_title">
                            </div>

                            <div class="form-group">
                                <label for="company_address">Company Address:</label>
                                <input type="text" id="company_address" name="company_address">
                            </div>

                            <div class="form-group">
                                <label for="company_contact">Company Contact:</label>
                                <input type="text" id="company_contact" name="company_contact">
                            </div>

                            <div class="form-group">
                                <label for="company_tin">Company TIN:</label>
                                <input type="text" id="company_tin" name="company_tin">
                            </div>

                            <div class="form-group">
                                <label for="sidebar_color">Sidebar Color:</label>
                                <input type="color" id="sidebar_color" name="sidebar_color">
                            </div>

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->

</div>
<!-- /.content-wrapper -->



<?php require 'templates/footer.php' ?>