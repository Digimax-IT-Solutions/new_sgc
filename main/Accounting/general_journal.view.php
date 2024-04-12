<?php
include __DIR__ . ('../../includes/header.php');

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

    #generalJournalTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #generalJournalTable th {
        font-size: 10px;
        white-space: nowrap;
        border: none;
    }

    #generalJournalTable td {
        font-size: 13px;
        padding: 5px;
        overflow: hidden;
        /* Hides any overflowing content */
        border: none;
        border-bottom: 1px solid red;

    }

    #generalJournalTable tbody tr:hover {

        color: white;
        background-color: rgba(128,21,20,0.8);
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">General Journal</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">General Journal</li>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="newTransactionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Transaction
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_general_journal" style="background-color: white;">Create General Journal</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="generalJournalTable" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>ENTRY NO</th>
                                        <th>ENTRY DATE</th>
                                        <th>TOTAL DEBIT</th>
                                        <th>TOTAL CREDIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your purchase order data will go here -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>


<script>
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;
    // Function to fetch and populate purchase order data
    // Function to format currency with commas
    function formatCurrency(amount) {
        // Convert amount to number
        var num = parseFloat(amount);
        // Check if the number is NaN
        if (isNaN(num)) {
            return amount; // Return the original value if it's not a valid number
        }
        // Use toLocaleString() to format the number with commas
        return num.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        });
    }

    function populategeneralJournalTable() {
    $.ajax({
        type: "GET",
        url: "modules/accounting/get_gen_journ.php",
        success: function(response) {
            var generalJournal = JSON.parse(response);
            
            // Clear existing table rows
            $("#generalJournalTable tbody").empty();

            generalJournal.forEach(function(genjourn) {
                // Assuming genjourn.journal_date is in the format yyyy-mm-dd
                var journal_date = new Date(genjourn.journal_date);

                // Get the individual components of the date
                var month = journal_date.getMonth() + 1; // Months are zero-based, so add 1
                var day = journal_date.getDate();
                var year = journal_date.getFullYear();

                // Format the date as 00-00-00 m/y/d
                var formattedDate = (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day + '-' + year;

                var formattedTotalDebit = formatCurrency(genjourn.total_debit);
                var formattedTotalCredit = formatCurrency(genjourn.total_credit);

                var row = `<tr class='genRow' data-journ-id='${genjourn.id}'>
                    <td>${genjourn.entry_no}</td>
                    <td>${formattedDate}</td>
                    <td><strong>${formattedTotalDebit}</strong></td>
                    <td><strong>${formattedTotalCredit}</strong></td>
                </tr>`;

                $("#generalJournalTable tbody").append(row);
            });

            // Initialize DataTables only if it's not already initialized
            if (!$.fn.DataTable.isDataTable("#generalJournalTable")) {
                $('#generalJournalTable').DataTable({
                    // DataTable options
                    "order": [[1, "asc"]]
                });
            }
        },
        error: function() {
            console.error("Error fetching purchase order data.");
        }
    });
}

    // Initial population when the page loads
    $(document).ready(function() {
        populategeneralJournalTable();
    });
    
</script>
<script>
    $(document).on('click', '.genRow', function() {
    var id = $(this).data('journ-id');
    window.location.href = 'view_general_journal?ID=' + id;
});
</script>