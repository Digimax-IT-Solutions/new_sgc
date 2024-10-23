<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('company_settings');
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>Company</strong> Settings</h1>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/company_settings.php">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <div class="form-group">
                                    <label for="companyName">Company Name</label>
                                    <input type="text" class="form-control form-control-sm" id="companyName" name="companyName" placeholder="Enter company name" required>
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                                    <small class="form-text text-muted">Upload a logo image (optional).</small>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control form-control-sm" id="address" name="address" placeholder="Enter address" required>
                                </div>
                                <div class="form-group">
                                    <label for="zipCode">Zip Code</label>
                                    <input type="text" class="form-control form-control-sm" id="zipCode" name="zipCode" placeholder="Enter zip code" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact #</label>
                                    <input type="text" class="form-control form-control-sm" id="contact" name="contact" placeholder="Enter contact number" required>
                                </div>
                                <div class="form-group">
                                    <label for="tin">TIN #</label>
                                    <input type="text" class="form-control form-control-sm" id="tin" name="tin" placeholder="Enter TIN number" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Save Settings</button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <h5>INVENTORY VALUATION METHOD</h5>
                            <form action="your_submission_endpoint.php" method="POST">
                                <div class="form-group">
                                    <label for="valuationMethod">Select Valuation Method:</label>
                                    <select id="valuationMethod" name="valuation_method" class="form-control form-control-sm">
                                        <option value="fifo">FIFO (First In, First Out)</option>
                                        <option value="lifo">LIFO (Last In, First Out)</option>
                                        <option value="weighted_average">Weighted Average</option>
                                        <option value="specific_identification">Specific Identification</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Save Settings</button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php' ?>

    <style>
        /* Additional styling for compactness */
        .form-group {
            margin-bottom: 1rem;
            /* Reduced spacing between form groups */
        }

        .form-text {
            font-size: 0.85rem;
            /* Slightly smaller help text */
        }

        .btn-primary {
            margin-top: 1rem;
            /* Space above the buttons */
        }
    </style>
</div>