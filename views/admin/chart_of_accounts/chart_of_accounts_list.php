<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('chart_of_accounts_list');

$chart_of_accounts = ChartOfAccount::allGroupedByCategory();

?>
<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-1">
                <div class="col-12">
                    <h1 class="h3"><strong>Chart of Accounts</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chart of Accounts</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Loader Section -->
            <div id="loader" class="loader-wrapper">
                <div class="spinner"></div>
            </div>

            <?php displayFlashMessage('add_account') ?>

            <!-- Content Section -->
            <div class="row" id="content" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Accounts List</h5>

                                <!-- Button group aligned to the right -->
                                <div class="ms-auto">
                                    <a href="add_chart_of_account" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Create Account
                                    </a>

                                    <!-- Export to Excel Button -->
                                    <a href="#" onclick="confirmExport(); return false;" class="btn btn-outline-danger">
                                        <i class="fas fa-file-excel fa-lg me-2"></i> Export to Excel
                                    </a>
                                    <!-- Upload Button -->
                                    <a class="btn btn-outline-secondary me-2" id="upload_button">
                                        <i class="fas fa-upload"></i> Upload
                                    </a>
                                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                        style="display: none;">
                                </div>
                            </div>
                        </div>



                        <div class="card-body">
                            <ul class="tree">
                                <?php foreach ($chart_of_accounts as $category => $types): ?>
                                    <li>
                                        <span class="caret"><i class="fas fa-folder"></i> <?= $category ?></span>
                                        <ul class="nested">
                                            <?php foreach ($types as $type_id => $group): ?>
                                                <li>
                                                    <span class="caret"><i class="fas fa-folder-open"></i> <?= $group['type_name'] ?></span>
                                                    <ul class="nested">
                                                        <li>
                                                            <table class="table table-sm table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="account-code">Account Code</th>
                                                                        <th class="account-name">Account Name</th>
                                                                        <th class="account-description">Description</th>
                                                                        <th class="text-end">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($group['accounts'] as $account): ?>
                                                                        <tr>
                                                                            <td class="account-code"><strong><i class="fas fa-key"></i> <?= $account->account_code ?></strong></td>
                                                                            <td class="account-name"><strong><?= $account->account_name ?></strong></td>
                                                                            <td class="account-description"><strong><?= $account->account_description ?></strong></td>
                                                                            <td class="text-end">
                                                                                <div class="btn-group" role="group">
                                                                                    <a href="edit_chart_of_account?id=<?= $account->id ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <!-- Sub-accounts -->
                                                                        <?php if (!empty($account->sub_accounts)): ?>
                                                                            <?php foreach ($account->sub_accounts as $sub_account): ?>
                                                                                <tr class="sub-account">
                                                                                    <td class="account-code"><i class="fas fa-file-alt"></i> <i><?= $sub_account->account_code ?></i></td>
                                                                                    <td class="account-name"><i><?= $sub_account->account_name ?></i></td>
                                                                                    <td class="account-description"><i><?= $sub_account->account_description ?></i></td>
                                                                                    <td class="text-end">
                                                                                        <div class="btn-group" role="group">
                                                                                            <a href="edit_chart_of_account?id=<?= $sub_account->id ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                                                <i class="fas fa-edit"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </li>
                                                    </ul>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="loading-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px;">
        Uploading data...
    </div>
</div>


<!-- Add this loader overlay to your HTML -->
<div id="loader-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Exporting accounts. Please wait...</p>
    </div>
</div>


<?php require 'views/templates/footer.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate loading for 1 second (you can remove this delay in production)
        setTimeout(function() {
            document.getElementById('loader').style.display = 'none'; // Hide the loader
            document.getElementById('content').style.display = 'block'; // Show the content
        }, 100); // Simulated delay for loading (optional)

        const toggler = document.getElementsByClassName("caret");

        // Loop through all caret elements and ensure they are uncollapsed by default
        for (let i = 0; i < toggler.length; i++) {
            const caretElement = toggler[i];
            caretElement.classList.add("caret-down"); // Add caret-down class for default open state
            caretElement.parentElement.querySelector(".nested").classList.add("active"); // Show nested list by default

            caretElement.addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    });
</script>

<style>
    .loader-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        /* Full height of viewport */
        background-color: #f8f9fa;
    }

    .spinner {
        border: 8px solid #f3f3f3;
        /* Light grey */
        border-top: 8px solid green;
        /* Green border */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .card-body {
        max-height: 800px;
        /* Set a fixed height for the card body */
        overflow-y: auto;
        /* Enable vertical scrolling inside the card */
        padding-right: 15px;
        /* Add padding to avoid overlapping with scrollbar */
    }

    /* Optional: Hide scrollbar in WebKit browsers (Chrome, Safari) */
    .card-body::-webkit-scrollbar {
        width: 6px;
        /* Adjust scrollbar width */
    }

    .card-body::-webkit-scrollbar-thumb {
        background-color: #ccc;
        /* Scrollbar color */
        border-radius: 10px;
        /* Round the scrollbar */
    }

    .card-body::-webkit-scrollbar-thumb:hover {
        background-color: #aaa;
        /* Hover effect on scrollbar */
    }

    .tree .table thead th {
        font-size: 0.7rem;
        font-weight: bold;
        color: #6c757d;
        border-top: none;
        border-bottom: 1px solid #dee2e6;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .tree,
    .tree ul {
        list-style-type: none;
        position: relative;
        padding-left: 20px;
        /* Space for tree lines */
    }

    /* Root level and parent items */
    .tree li {
        position: relative;
        padding-left: 20px;
        /* Space for the tree line */
        margin-left: 10px;
        padding-right: 100px;
    }

    /* Styling for tree lines */
    .tree li::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        border-left: 1px solid #ccc;
        height: 100%;
        width: 20px;
        /* Space before each item */
    }

    .tree li::after {
        content: '';
        position: absolute;
        top: 15px;
        /* Align with the item */
        left: 0;
        border-top: 1px solid #ccc;
        width: 20px;
    }

    /* Remove the vertical line for last child */
    .tree ul>li:last-child::before {
        height: 15px;
    }

    .tree .table {
        margin-bottom: 0;
    }

    .tree .table td {
        border: none;

        padding: 0.25rem;
    }

    .tree .table .sub-account td {
        padding-left: 5rem;

        /* Indent sub-accounts */
    }



    .account-code {
        width: 15%;
    }

    .account-name {
        width: 25%;
    }

    .account-description {
        width: 50%;
    }

    /* Style for the caret (collapsible item) */
    .caret {
        cursor: pointer;
        user-select: none;
        font-weight: bold;
    }

    .caret::before {
        content: "\25B6";
        /* Right arrow */
        color: black;
        display: inline-block;
        margin-right: 6px;
    }

    .caret-down::before {
        content: "\25BC";
        /* Down arrow */
    }

    .nested {
        display: none;
    }

    .active {
        display: block;
    }

    /* Align the account details */
    .account {
        display: inline-block;
        width: 70%;
        vertical-align: middle;
    }

    /* Button group styling */
    .btn-group {
        display: inline-block;
    }

    /* Space to ensure button group does not overlap with text */
    .tree li {
        padding-right: 50px;
    }
</style>