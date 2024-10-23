<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('other_name');
// Include necessary files and retrieve data
$other_names = OtherNameList::all();

$page = 'other_name_list';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            padding: 0;
            background-color: transparent;
            box-shadow: none;
            height: auto;
            line-height: inherit;
            font-size: inherit;
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
        }
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Other Name</strong> List</h1>
                    <div class="d-flex justify-content-end">
                        <a href="other_name" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_uom'); ?>
                    <?php displayFlashMessage('delete_uom'); ?>
                    <?php displayFlashMessage('update_uom'); ?>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/other_name_list_controller.php" id="othernameForm">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="other_nameId" value="" />
                                <div class="row mb-3">
                                    <!-- Column 1 -->
                                        <label for="other_name" class="col-sm-2 col-form-label">Other Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name" name="other_name"
                                            placeholder="Enter Other Name" required>
                                    </div>
             
                                    <label for="other_name_code" class="col-sm-2 col-form-label">Other Name Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name_code" name="other_name_code"
                                            placeholder="Enter Other Name Code">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="account_number" class="col-sm-2 col-form-label">Account Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="account_number" name="account_number"
                                            placeholder="Enter Account Number">
                                    </div>
                                    <label for="other_name_address" class="col-sm-2 col-form-label">Other Name Address</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name_address"
                                            name="other_name_address" placeholder="Enter Other Name Address">
                                    </div>
                                    </div>
                                    <div class="row mb-3">
                                    <label for="contact_number" class="col-sm-2 col-form-label">Contact #</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="contact_number" name="contact_number"
                                            placeholder="Enter Contact #">
                                    </div>
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="email" name="email"
                                            placeholder="Enter Email">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="terms" class="col-sm-2 col-form-label">Terms</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="terms" name="terms">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Rest of your HTML content -->

    <?php require 'views/templates/footer.php' ?>

    <script>
        $(function () {
            $("#other_nameTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#other_nameTable_wrapper .col-md-6:eq(0)');
            
            // JavaScript for updating modal data when Update button is clicked
            $('.update-btn').click(function () {
                var id = $(this).data('id');
                var name = $(this).closest('tr').find('td:eq(0)').text().trim(); // Example: Replace with actual field
                var code = $(this).data('code');
                var account = $(this).data('account');
                var address = $(this).data('address');
                var contact = $(this).data('contact');
                var email = $(this).data('email');
                var terms = $(this).data('terms');

                // Set form values in the modal
                $('#modalAction').val('update'); // Set action to 'update'
                $('#other_nameId').val(id);
                $('#other_name').val(name);
                $('#other_name_code').val(code);
                $('#account_number').val(account);
                $('#other_name_address').val(address);
                $('#contact_number').val(contact);
                $('#email').val(email);
                $('#terms').val(terms);

                // Show the modal
                $('#other_nameModal').modal('show');
            });
        });
    </script>
