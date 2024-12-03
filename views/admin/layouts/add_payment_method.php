<!-- Modal for Adding New Payment Method -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentMethodModalLabel">Add New Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="api/masterlist/direct_add_payment_method.php" id="addPaymentMethodForm">
                    <input type="hidden" name="action" value="add">

                    <!-- Payment Method Name -->
                    <div class="mb-3">
                        <label for="payment_method_name" class="form-label">Payment Method Name</label>
                        <input type="text" class="form-control" id="payment_method_name" name="payment_method_name" placeholder="Enter Payment Method Name" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" class="btn btn-primary" id="addPaymentMethodSubmit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
