<div class="modal-footer">
    <div class="summary-details">
        <!-- Gross Amount -->
        <div class="row">
            <div class="col-md-6 text-right">
                <label>Gross Amount:</label>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="grossAmount" id="grossAmount" readonly>
                </div>
            </div>
        </div>

        <!-- Discount Percentage and Discount Amount -->
        <div class="row">
            <div class="col-md-6 d-flex text-right">
                <label for="discountPercentage" style="margin-right: 10px;">Discount
                    (%):</label>
                <input type="number" class="form-control" name="discountPercentage" id="discountPercentage"
                    placeholder="Enter %" style=" width: 100px;">
            </div>
            <div class="col-md-6 d-flex align-items-center">

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="discountAmount" id="discountAmount" readonly>
                </div>
            </div>
        </div>

        <!-- Net Amount Due -->
        <div class="row">
            <div class="col-md-6 text-right">
                <label>Net Amount Due:</label>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="netAmountDue" id="netAmountDue" readonly>
                </div>
            </div>
        </div>

        <!-- VAT Percentage and VAT Amount -->
        <div class="row">
            <div class="col-md-6 d-flex text-right">
                <label for="vatPercentage" style="margin-right: 5px;">VAT (%):</label>
                <select class="form-control" id="vatPercentage" name="vatPercentage" required>
                    <?php
                                                    $query = "SELECT salesTaxRate, salesTaxName FROM sales_tax";
                                                    $result = $db->query($query);

                                                    if ($result) {
                                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$row['salesTaxRate']}'>{$row['salesTaxName']}</option>";
                                                        }
                                                    }
                                                    ?>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-center">

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="vatPercentageAmount" id="vatPercentageAmount"
                        readonly>
                </div>
            </div>
        </div>

        <!-- Net of VAT -->
        <div class="row">
            <div class="col-md-6 text-right">
                <label>Net of VAT:</label>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="netOfVat" id="netOfVat" readonly>
                </div>
            </div>
        </div>

        <!-- Tax Withheld Percentage and Tax Withheld Amount -->
        <div class="row">
            <div class="col-md-6 text-right">
                <label>Tax Withheld (%):</label>
            </div>
            <div class="col-md-6 d-flex">
                <select class="form-control" id="taxWithheldPercentage" name="taxWithheldPercentage" required>
                    <?php
                                                    $query = "SELECT wTaxRate, wTaxName FROM wtax";
                                                    $result = $db->query($query);

                                                    if ($result) {
                                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$row['wTaxRate']}'>{$row['wTaxName']}</option>";
                                                        }
                                                    }
                                                    ?>
                </select>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="taxWitheldAmount" id="taxWitheldAmount" readonly>
                </div>
            </div>
        </div>

        <!-- Total Amount Due -->
        <div class="row total-amount-due" style="background-color: rgb(0, 149, 77); color: white;">
            <div class="col-md-6 text-right" style="font-size: 30px">
                <label>Total Amount Due:</label>
            </div>
            <div class="col-md-6" style="font-size: 30px">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">&#8369;</span>
                    </div>
                    <input type="text" class="form-control" name="totalAmountDue" id="totalAmountDue" readonly>
                </div>
            </div>
        </div>

        <br>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-md-7 d-flex">
                <button type="button" class="btn btn-success" id="saveInvoiceButton">Submit Invoice</button>
            </div>
        </div>
    </div>