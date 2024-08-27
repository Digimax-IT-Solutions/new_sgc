<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$discounts = Discount::all()

    ?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Discount</strong> </h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_discount') ?>
                    <?php displayFlashMessage('delete_discount') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Discount</h6>
                                    <div>
                                        <a class="btn btn-sm btn-outline-secondary me-2" id="upload_button">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                            style="display: none;">
                                        <a href="create_discount" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> New Discount
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <br><br>

                            <div class="row">
                                <div class="row">
                                    <table id="discountListTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Discount Name</th>
                                                <th>Discount rate (%)</th>
                                                <th>Description</th>
                                                <th>Discount Account</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($discounts as $discount): ?>
                                                <tr>
                                                    <td><?= $discount->discount_name ?></td>
                                                    <td><?= $discount->discount_rate ?></td>
                                                    <td><?= $discount->discount_description ?></td>
                                                    <td><?= $discount->account_description ?></td>
                                                    <td>
                                                        <a class="text-primary"
                                                            href="view_discount?action=update&id=<?= $discount->id ?>">
                                                            <i class="fas fa-edit"></i> Update
                                                        </a>
                                                        <a class="text-danger ml-2"
                                                            href="api/masterlist/discount_controller.php?action=delete&id=<?= $discount->id ?>">
                                                            <i class="fas fa-trash-alt"></i> Delete
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
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(function () {
        $("#discountListTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#discountListTable_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    function selectDate(date) {
        document.getElementById('selectedDate').innerText = date;
    }
</script>

<script>
        $(document).ready(function () {

            $('#upload_button').on('click', function () {
                $('#excel_file').click();
            });

            $('#excel_file').on('change', function () {
                if (this.files[0]) {
                    var formData = new FormData();
                    formData.append('excel_file', this.files[0]);
                    formData.append('action', 'upload'); // Add this line to specify the action

                    $.ajax({
                        url: 'api/masterlist/discount_controller.php', // Update this path if needed
                        type: 'POST',
                        data: formData,
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json', // Add this line to expect JSON response
                        success: function (response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText); // Log the full response for debugging
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });
        });
    </script>