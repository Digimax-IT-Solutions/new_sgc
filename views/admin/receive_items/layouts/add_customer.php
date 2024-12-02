<!-- Modal for Adding New Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="api/masterlist/direct_add_customer.php" id="addCustomerForm">
                    <input type="hidden" name="action" value="add">

                    <!-- CUSTOMER CODE -->
                    <div class="mb-3">
                        <label for="customer_code" class="form-label">Customer Code</label>
                        <input type="text" class="form-control" id="customer_code" name="customer_code" placeholder="Enter Customer Code">
                    </div>

                    <!-- CUSTOMER NAME -->
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter Customer Name" required>
                    </div>

                    <!-- SHIPPING ADDRESS -->
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" placeholder="Enter Shipping Address"></textarea>
                    </div>

                    <!-- BILLING ADDRESS -->
                    <div class="mb-3">
                        <label for="billing_address" class="form-label">Billing Address</label>
                        <textarea class="form-control" id="billing_address" name="billing_address" rows="3" placeholder="Enter Billing Address"></textarea>
                    </div>

                    <!-- BUSINESS STYLE AND TERMS -->
                    <div class="mb-3">
                        <label for="business_style" class="form-label">Business Style</label>
                        <input type="text" class="form-control" id="business_style" name="business_style" placeholder="Enter Business Style">
                    </div>
                    <div class="mb-3">
                        <label for="customer_terms" class="form-label">Terms</label>
                        <select class="form-select" id="customer_terms" name="customer_terms">
                            <option value="">Select Term</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?= $term->id ?>"><?= $term->term_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TIN AND EMAIL -->
                    <div class="mb-3">
                        <label for="customer_tin" class="form-label">TIN Number</label>
                        <input type="text" class="form-control" id="customer_tin" name="customer_tin" placeholder="Enter TIN Number">
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="Enter Email">
                    </div>

                    <!-- Submit Button in the Modal -->
                    <button type="button" class="btn btn-primary" id="addCustomerSubmit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>