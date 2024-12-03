<!-- Modal for Adding New Vendor -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendorModalLabel">Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="api/masterlist/direct_add_vendor.php" id="addVendorForm">
                    <input type="hidden" name="action" value="add">

                    <!-- Vendor Code -->
                    <div class="mb-3">
                        <label for="vendor_code" class="form-label">Vendor Code</label>
                        <input type="text" class="form-control" id="vendor_code" name="vendor_code" placeholder="Enter Vendor Code">
                    </div>

                    <!-- Vendor Name -->
                    <div class="mb-3">
                        <label for="vendor_name" class="form-label">Vendor Name</label>
                        <input type="text" class="form-control" id="vendor_name" name="vendor_name" placeholder="Enter Vendor Name" required>
                    </div>

                    <!-- Vendor Address -->
                    <div class="mb-3">
                        <label for="vendor_address" class="form-label">Vendor Address</label>
                        <textarea class="form-control" id="vendor_address" name="vendor_address" rows="3" placeholder="Enter Vendor Address"></textarea>
                    </div>

                    <!-- Account Number -->
                    <div class="mb-3">
                        <label for="accountnumber" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Enter Account Number">
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-3">
                        <label for="contactnumber" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Number">
                    </div>
                    <div class="mb-3">
                        <label for="tel_no" class="form-label">Tel No.</label>
                        <input type="text" class="form-control" id="tel_no" name="tel_no" placeholder="Enter Tel No.">
                    </div>
                    <div class="mb-3">
                        <label for="fax_no" class="form-label">Fax No.</label>
                        <input type="text" class="form-control" id="fax_no" name="fax_no" placeholder="Enter Fax No.">
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                    </div>

                    <!-- Tax Information -->
                    <div class="mb-3">
                        <label for="tin" class="form-label">TIN</label>
                        <input type="text" class="form-control" id="tin" name="tin" placeholder="Enter TIN">
                    </div>
                    <div class="mb-3">
                        <label for="tax_type" class="form-label">Tax Type</label>
                        <select class="form-select" id="tax_type" name="tax_type">
                            <option value="">Select Tax Type</option>
                            <?php foreach ($input_vats as $input_vat): ?>
                                <option value="<?= htmlspecialchars($input_vat->input_vat_name) ?>">
                                    <?= htmlspecialchars($input_vat->input_vat_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Terms -->
                    <div class="mb-3">
                        <label for="terms" class="form-label">Terms</label>
                        <input type="text" class="form-control" id="terms" name="terms" placeholder="Enter Terms">
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter Notes"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" class="btn btn-primary" id="addVendorSubmit">Submit</button>

                </form>
            </div>
        </div>
    </div>
</div>