<?php
include __DIR__ . ('../../includes/header.php');
include('connect.php');
?>
<style>
    /* Add styles for active status */
    .active {
        color: green;
        /* Change the text color for active status */
    }

    /* Add styles for inactive status */
    .inactive {
        color: red;
        /* Change the text color for inactive status */
    }

    /* Add a hover effect to the dropdown items */
    .dropdown-item:hover {
        background-color: rgb(0, 149, 77) !important;
        /* Change the background color on hover */
        color: white;
        /* Change the text color on hover */
    }

    .breadcrumb {
        background-color: white;
    }

    .summary-details input {
        font-size: 90%;
        /* Adjust the percentage as needed */
    }

    .input-group-text {
        font-size: 50%;

    }

    .form-control {
        font-size: 80%;

    }

    .form-group label {
        font-size: 70%;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Credit Memo</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Customer Center</a></li>
                        <li class="breadcrumb-item active">Credit Memo</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="addCreditForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <?php
                                        // Assuming $db is your PDO database connection
                                        $customerQuery = "SELECT * FROM customers";

                                        try {
                                            $customerStmt = $db->prepare($customerQuery);
                                            $customerStmt->execute();

                                            $customers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

                                            echo "<label for='customerName'>Customer Name</label>";
                                            echo "<select name='customerName' id='customerName' class='form-control'>";
                                            echo "<option value='' disabled selected>Select Customer:</option>";
                                            foreach ($customers as $customer) {
                                                echo "<option value='" . $customer['customerName'] . "'>" . $customer['customerName'] . "</option>";
                                            }
                                            echo "</select>";
                                        } catch (PDOException $e) {
                                            // Handle the exception, log the error, or return an error message with MySQL error information
                                            $errorInfo = $customerStmt->errorInfo();
                                            $errorMessage = "Error fetching customers: " . $errorInfo[2]; // MySQL error message
                                            echo "<option value=''>$errorMessage</option>";
                                        }
                                        ?>

                                        <label for="creditID">Credit No</label>
                                        <input type="text" class="form-control" id="creditID" name="creditID">
                                        <label for="poID">PO No</label>
                                        <input type="text" class="form-control" id="poID" name="poID">
                                    </div>
                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="creditDate">DATE</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="creditDate" name="creditDate" required>
                                        </div>
                                        <label for="creditAmount">Total Amount</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="creditAmount" name="creditAmount" required>
                                        </div>
                                        <label for="creditBalance" hidden>Total Amount</label>
                                        <div class="input-group" hidden>
                                            <input type="number" class="form-control" id="creditBalance" name="creditBalance" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="memo">Memo:</label>
                                        <textarea name="memo" id="memo" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <center><button type="button" class="btn btn-primary" id="saveButton">Submit</button></center>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>
<script>
    $(document).ready(function() {
        function isFormValid() {
            var isValid = true;

            if ($("#creditID").val() === '') {
                isValid = false;
                highlightInvalidField($("#creditID"));
            } else {
                resetInvalidField($("#creditID"));
            }

            if ($("#poID").val() === '') {
                isValid = false;
                highlightInvalidField($("#poID"));
            } else {
                resetInvalidField($("#poID"));
            }

            if ($("#creditDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#creditDate"));
            } else {
                resetInvalidField($("#creditDate"));
            }

            if ($("#creditAmount").val() === '') {
                isValid = false;
                highlightInvalidField($("#creditAmount"));
            } else {
                resetInvalidField($("#creditAmount"));
            }

            var customerName = $('#customerName').val();
            if (customerName === null || customerName === '') {
                isValid = false;
                highlightInvalidField($("#customerName"));
            } else {
                resetInvalidField($("#customerName"));
            }

            return isValid;
        }

        function highlightInvalidField(field) {
            field.addClass("is-invalid");
        }

        // Function to reset the highlighting of an invalid field
        function resetInvalidField(field) {
            field.removeClass("is-invalid");
        }

        // Click event for the saveButton
        $("#saveButton").click(function() {
            // Call isFormValid function
            if (!isFormValid()) {
                // Show SweetAlert error if the form is not valid
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please fill in all required fields.', // Customize the error message
                });
                return;
            }

            // Proceed with AJAX call if form is valid
            $.ajax({
                type: "POST",
                url: "modules/credit/save_credit.php",
                data: $("#addCreditForm").serialize(),
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'New Credit Memo Added!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                    });
                }
            });

            // Disable selected option after validation
            var customerName = $('#customerName').val();
            $('#customerName option[value="' + customerName + '"]').prop('disabled', true);
        });

        // Input event for the creditAmount field
        $('#creditAmount').on('input', function() {
            const creditAmount = parseFloat($(this).val());
            $('#creditBalance').val(creditAmount.toFixed(2)); // Update the creditBalance field
        });
    });
</script>