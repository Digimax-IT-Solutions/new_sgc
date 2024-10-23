<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('purchase_request');
$products = Product::all();
$cost_centers = CostCenter::all();
$locations = Location::all();

$newPrNo = PurchaseRequest::getLastPrNo();
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .form-label {
        font-size: 0.675rem;
        margin-bottom: 0.25rem;
    }

    .card-body {
        font-size: 0.875rem;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #itemTable {
        /* Adjust this value based on your table's content */
        table-layout: fixed;
    }

    #itemTable th {
        white-space: nowrap;
    }

    #itemTable tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    #itemTable th,
    #itemTable td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    #itemTable .text-right {
        text-align: right;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #loadingOverlay .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    #loadingOverlay .message {
        color: white;
        margin-top: 10px;
        font-size: 18px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .sticky-header th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 1;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.3s ease;
    }

    .progress-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .progress-step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .progress-step::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 2px;
        background-color: #dee2e6;
        z-index: -1;
    }

    .progress-step:last-child::after {
        display: none;
    }

    .progress-step.active .step-number {
        background-color: #007bff;
        color: white;
    }

    .step-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #dee2e6;
        line-height: 30px;
        margin-bottom: 5px;
    }
</style>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Purchase Request</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="purchase_request">Purchase Request</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Purchase Request</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="purchase_request" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Request List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">


                <form id="purchaseOrderForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Purchase Request Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="pr_no" class="form-label">Purchase Request #</label>
                                            <input type="text" class="form-control" id="pr_no" value="PR-2024-001" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="location" class="form-label">Location</label>
                                            <select class="form-select form-select-sm" id="location" name="location"
                                                required>
                                                <option value="">Select Location</option>
                                                <?php foreach ($locations as $location): ?>
                                                    <option value="<?= $location->name ?>"><?= $location->name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="required_date" class="form-label">Required Date</label>
                                            <input type="date" class="form-control" id="required_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="requestor" class="form-label">Requestor</label>
                                            <input type="text" class="form-control" id="requestor" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="memo" class="form-label">Memo/Purpose</label>
                                            <textarea class="form-control" id="memo" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Purchase Request Items</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead class="sticky-header">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Cost Center</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Unit</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemTableBody">
                                                <!-- Items will be dynamically added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Total Items: <span id="totalItems">0</span></span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="saveDraftBtn">
                                            <i class="fas fa-save"></i> Save as Draft
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Submit Purchase Request
                            </button>
                            <button type="reset" class="btn btn-danger">
                                <i class="fas fa-undo"></i> Clear Form
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
</main>
<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
    <div class="message">Processing Purchase Request</div>
</div>
</div>


<?php require 'views/templates/footer.php' ?>
<iframe id="printFrame" style="display:none;"></iframe>

<script>
    $(document).ready(function() {

        // Add click event listener to the Clear button
        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior

            $('input').not('#pr_no').val('');

            $('select').each(function() {
                $(this).val($(this).find("option:first").val()).trigger('change');
            });

            $('#itemTableBody').empty();

            $('.select2').val(null).trigger('change');

            $('#item_data').val('');

            $('#date, #required_date').val(new Date().toISOString().split('T')[0]);

            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset.',
                timer: 1800,
                showConfirmButton: false
            });
        });

        $('#saveDraftBtn').click(function(e) {
            e.preventDefault();
            saveDraft();
        });

        function saveDraft() {
            const items = gatherTableItems();

            // Check if there are any items
            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items before saving as draft'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Prepare the form data
            const formData = new FormData($('#purchaseOrderForm')[0]);
            formData.append('action', 'save_draft');

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Use AJAX to submit the form
            $.ajax({
                url: 'api/purchase_request_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Purchase Order saved as draft successfully!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving draft: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the draft: ' + textStatus
                    });
                }
            });
        }

        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: true
        });

        // Populate dropdowns with accounts from PHP
        const products = <?php echo json_encode($products); ?>;
        let itemDropdownOptions = '<option value="" selected disabled>Select An Item</option>';
        $.each(products, function(index, product) {
            itemDropdownOptions += `<option value="${product.id}" data-description="${product.item_purchase_description}" data-uom="${product.uom_name}">${product.item_code} - ${product.item_name}</option>`;

        });

        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function(costCenter) {
            costCenterDropdownOptions += `<option value="${costCenter.id}">${costCenter.code} - ${costCenter.particular}</option>`;
        });

        // Add a new row to the table
        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-center-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm description-field" name="description[]"></td>
                    <td style="background-color: #e6f3ff;"><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Quantity"></td>
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td> 
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            $('#itemTableBody').append(newRow);
            updateTotalItems();

            $('#itemTableBody .select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });
        }


        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
            updateTotalItems();
        });

        // Update total items count
        function updateTotalItems() {
            var itemCount = $('#itemTableBody tr').length;
            $('#totalItems').text(itemCount);
        }


        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function(index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    cost_center_id: $(this).find('select[name="cost_center_id[]"]').val(),
                    description: $(this).find('input[name="description[]"]').val(),
                    uom: $(this).find('input[name="uom[]"]').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                };
                items.push(item);
            });
            return items;
        }



        $('#purchaseOrderForm').submit(function(event) {
            event.preventDefault();

            const items = gatherTableItems();

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    if (response.success) {
                        // Redirect to purchase_request page on success
                        const transactionId = response.id;
                        updatePrintStatusAndPrint(transactionId, 1);
                    } else {
                        // Show error message in SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving purchase request: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);

                    // Show error message in SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the purchase request: ' + textStatus
                    });
                }
            });
        });

        function updatePrintStatusAndPrint(id, printStatus) {
            $.ajax({
                url: 'api/purchase_request_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Print status updated, now printing Credit:', id);

                        // Now proceed with printing
                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_purchase_request?action=print&id=${id}`;
                        const submitButton = document.querySelector('.btn-primary[type="submit"]');
                        submitButton.disabled = true;

                        printFrame.src = printContentUrl;

                        printFrame.onload = function() {
                            printFrame.contentWindow.focus();
                            printFrame.contentWindow.print();

                            // Redirect after print dialog closes
                            const originalOnFocus = window.onfocus;

                            window.onfocus = function() {
                                window.location.href = 'purchase_request';
                            };

                            // Clean up event handler after redirection
                            printFrame.contentWindow.onafterprint = function() {
                                window.onfocus = originalOnFocus;
                            };
                        };
                    } else {
                        console.error('Failed to update print status:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating print status: ' + textStatus
                    });
                }
            });
        }
    });

    // Function to populate multiple fields based on selected option
    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        // Add more fields as needed

        const row = $(select).closest('tr');
        row.find('.description-field').val(description);
        row.find('.uom').val(uom);
        // Populate more fields as needed
    }
</script>