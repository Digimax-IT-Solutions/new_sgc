<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$categories = Category::all();

$category = null;
if (get('action') === 'update') {
    $category = Category::find(get('id'));
}

$page = 'category'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            /* Remove border */
            padding: 0;
            /* Remove default padding */
            background-color: transparent;
            /* Make background transparent */
            box-shadow: none;
            /* Remove box shadow */
            height: auto;
            /* Auto height to fit content */
            line-height: inherit;
            /* Inherit line-height from the table */
            font-size: inherit;
            /* Inherit font-size from the table */
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
            /* Adjust if necessary */
        }
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Category</strong> List</h1>

                    <div class="d-flex justify-content-end">
                        <a href="category" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_category') ?>
                    <?php displayFlashMessage('delete_category') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                        <?php if (isset($_GET['id'])): 
                                $id = $_GET['id'];
                                $category = Category::find($id);
                                if ($category): ?>
                            <form method="POST" action="api/category_controller.php" id="categoryForm">
                                <input type="hidden" name="action" id="modalAction" value="update" />
                                <input type="hidden" name="id" id="itemId" value="<?= $category ? htmlspecialchars($category->id) : '' ?>" />

                                <div class="row mb-3">
                                    <label for="categoryName" class="col-sm-2 col-form-label">Category Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="categoryName" name="name"
                                            placeholder="Enter category name here" value="<?= $category ? htmlspecialchars($category->name) : '' ?>" required />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            <?php else: ?>
                                <p>Category not found.</p>
                            <?php endif; 
                            else: ?>
                                <p>No ID provided.</p>
                            <?php endif; ?>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>
