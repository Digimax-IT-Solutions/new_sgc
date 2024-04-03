<script>
    $(document).ready(function() {
        $('.discount_credit_button').hide();

        function updatePaymentAmount() {
            var checkedInvoices = $('input[name="invoice[]"]:checked').length;
            if (checkedInvoices > 0) {
                $('#payment_amount').prop('readonly', false);
            } else {
                $('#payment_amount').val('').prop('readonly', true);
            }
        }

        // Event listener for checkbox changes in invoice table
        $('#invoice_table').on('change', 'input[name="invoice[]"]', function() {
            updatePaymentAmount();
            calculateAmountDue();
            calculateDiscountAndCredits() // Calculate amount due when checkbox changes

            // Check if at least one invoice is checked
            if ($('input[name="invoice[]"]:checked').length > 0) {
                // Show all "Discount & Credit" buttons if at least one invoice is checked
                $('.discount_credit_button').show();
            } else {
                // Hide all "Discount & Credit" buttons if no invoices are checked
                $('.discount_credit_button').hide();
            }
        });

        // Initial call to updatePaymentAmount
        updatePaymentAmount();

        // Function to calculate total amount due based on checked invoices
        function calculateAmountDue() {
    var totalAmountDue = 0;
    $('input[name="invoice[]"]:checked').each(function() {
        // Extract numeric characters and parse as float
        var amountDueText = $(this).closest('tr').find('.total-amount-due').text().replace(/[^\d.-]/g, '');
        totalAmountDue += parseFloat(amountDueText) || 0;
    });
    $('#amount_due').val(totalAmountDue.toFixed(2));
    $('#payment_amount').val(totalAmountDue.toFixed(2));
}

        function calculateDiscountAndCredits() {
            var Credits = 0;
            var Discount = 0;

            // Check if any invoices are checked
            var checkedInvoices = $('input[name="invoice[]"]:checked');
            if (checkedInvoices.length === 0) {
                // If no invoices are checked, set #discCredapplied value to 0 and return
                $('#discCredapplied').val('0.00');
                return;
            }

            // If invoices are checked, calculate credits and discounts
            checkedInvoices.each(function() {
                Credits += parseFloat($(this).closest('tr').find('.credit-column').text()) || 0;
                Discount += parseFloat($(this).closest('tr').find('.discount-column').text()) || 0;
            });

            // Calculate totalDiscountCredits
            var totalDiscountCredits = Credits + Discount;

            // Set #discCredapplied value
            $('#discCredapplied').val(totalDiscountCredits.toFixed(2));
        }

        function formatCurrency(amount) {
    // Convert amount to number if it's not already
    amount = Number(amount);

    // Check if amount is a valid number
    if (isNaN(amount)) {
        return 'Invalid amount';
    }

    // Use toFixed to ensure two decimal places
    var formattedAmount = amount.toFixed(2);

    // Add commas for thousands separator
    formattedAmount = formattedAmount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    // Prepend peso sign
    formattedAmount = '₱' + formattedAmount;

    // Return the formatted amount
    return formattedAmount;
}
        function saveModalData() {
    var invoiceID = $('#DiscCredID').text();
    var discountInputValue = parseFloat($('#discount_amount').val()) || 0;
    var creditInputValue = 0; // Initialize the total credit input value
    var newTotalAmountDue = 0; // Initialize the updated total amount due

    // Iterate over each checked checkbox in the creditMemoTable
    $('#creditMemoTable tbody tr').each(function() {
        var checkbox = $(this).find('input[type="checkbox"]');
        if (checkbox.is(':checked')) {
            // Retrieve the credit input value for the checked checkbox
            creditInputValue += parseFloat($(this).find('.credit-input').val()) || 0;
        }
    });

    // Iterate over each row in the invoice_table tbody
    $('#invoice_table tbody tr').each(function() {
        var checkboxValue = $(this).find('input[type="checkbox"]').val();
        if (checkboxValue == invoiceID) {
            // Update the credit, discount, and total amount due columns for the row
            $(this).find('.credit-column').text(creditInputValue.toFixed(2));
            $(this).find('.discount-column').text(discountInputValue.toFixed(2));

            // Calculate the new total amount due for the row
            var totalAmountDue = parseFloat($(this).find('.total-amount-due').text().replace(/[^\d.]/g, '')) || 0;
            newTotalAmountDue = totalAmountDue - creditInputValue - discountInputValue;

            // Update the total amount due column for the row
            $(this).find('.total-amount-due').html('<strong>' + formatCurrency(newTotalAmountDue) + '</strong>');

            // Exit the loop once the invoice is found and processed
            return false;
        }
    });

    // Update the total amount due text outside the loop
    $('#amount_due').text(newTotalAmountDue.toFixed(2));

    // Update other elements based on the new total amount due
    $('#applied').val(newTotalAmountDue.toFixed(2));

    // Update discCredapplied based on new credit and discount values
    var totalDiscCredApplied = parseFloat($('#discCredapplied').val());
    var newDiscCredApplied = totalDiscCredApplied + creditInputValue + discountInputValue;
    $('#discCredapplied').val(newDiscCredApplied.toFixed(2));

    // Call the calculateAmountDue function to update the total amount due
    calculateAmountDue();
}

$('#saveModalDataBtn').click(function() {
    saveModalData();
    $('#addDiscCred').modal('hide');
});

        $('#customerName').change(function() {
            var customerName = $(this).val();
            console.log(customerName);
            $.ajax({
                url: 'modules/customer_center/get_unpaid_invoices.php',
                type: 'POST',
                data: {
                    customerName: customerName
                },
                success: function(response) {
                    console.log(response); // Log the response to the console
                    var invoices = JSON.parse(response);
                    var tableBody = $('#invoice_table tbody');
                    tableBody.empty();
                    if (invoices.length === 0) {
                        tableBody.append(
                            '<tr><td colspan="8">There are no unpaid invoices for this customer</td></tr>'
                        );
                    } else {
                        invoices.forEach(function(invoice) {
                            var invoiceDate = new Date(invoice.invoiceDate);
                            var formattedDate = (invoiceDate.getMonth() + 1) + '-' + invoiceDate.getDate() + '-' + invoiceDate.getFullYear();
                            var row = '<tr>' +
                                '<td><input type="checkbox" class="first1-column" name="invoice[]" value="' + invoice.invoiceID + '"></td>' +
                                '<td>' + formattedDate + '</td>' + 
                                '<td>' + invoice.invoiceNo + '</td>' +
                                '<td class="text-right"><strong>' + formatCurrency(invoice.totalAmountDue) + '</strong></td>' +
                                '<td><button type="button" class="btn btn-success discount_credit_button"' +
                                'data-customer="' + invoice.customer + '"' +
                                'data-invoiceno="' + invoice.invoiceNo + '"' +
                                'data-origamt="' + invoice.totalAmountDue + '"' +
                                'data-date="' + invoice.invoiceDueDate + '"' +
                                'value="' + invoice.invoiceID + '" data-toggle="modal" data-target="#addDiscCred">Discount & Credit</button></td>' +
                                '<td class="discount-column"></td>' +
                                '<td class="credit-column"></td>' +
                                '<td class="total-amount-due text-right"><strong>' + formatCurrency(invoice.totalAmountDue - invoice.amountReceived) + '</strong></td>' +
                                '<td></td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                    }
                // Initialize DataTable for invoice_table
        if (!$.fn.DataTable.isDataTable("#invoice_table")) {
            $('#invoice_table').DataTable({
                // DataTable options
            });
        }
    },
    error: function(xhr, status, error) {
        // Handle error
        console.log("Error:", error);
    }
}); 
            var checkedCheckboxes = {}; // Object to store the state of checked checkboxes
            var inputFieldValues = {}; // Object to store the values of input fields

            // Function to store the state of checked checkboxes and input field values
            function storeState() {
                var creditData = []; // Array to store credit data

                // Iterate over each credit checkbox and store its ID and value if checked
                $('.credit-input').each(function() {
                    var creditID = $(this).closest('tr').find('input[type="checkbox"]').val();
                    var isChecked = $(this).closest('tr').find('input[type="checkbox"]').is(':checked');
                    var inputValue = $(this).val();
                    checkedCheckboxes[creditID] = isChecked;
                    inputFieldValues[creditID] = inputValue; // Get the value of the .credit-input field
                    if (isChecked) {
                        creditData.push({
                            id: creditID,
                            amount: inputValue
                        }); // Store ID and value if the checkbox is checked
                    }
                });

                // Generate HTML content for the credit data
                var htmlContent = '';
                creditData.forEach(function(credit) {
                    htmlContent += 'ID: ' + credit.id + ', Amount: ' + credit.amount + '<br>';
                });

                // Update the content of the div with the credit data
                $('#creditCheckboxIDsContainer').html(htmlContent);
            }


            // Function to restore the state of checked checkboxes and input field values
            function restoreState() {
                $('input[type="checkbox"]').each(function() {
                    var checkboxValue = $(this).val();
                    if (checkedCheckboxes.hasOwnProperty(checkboxValue)) {
                        $(this).prop('checked', checkedCheckboxes[checkboxValue]);
                    }
                });

                $('.credit-input').each(function() {
                    var inputValue = $(this).closest('tr').find('input[type="checkbox"]').val();
                    if (inputFieldValues.hasOwnProperty(inputValue)) {
                        $(this).val(inputFieldValues[inputValue]);
                    }
                });
            }

            $('#addDiscCred').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var invoiceID = button.val(); // Extract invoiceID from the button's value attribute
                var customer = button.data('customer');
                var invoiceNo = button.data('invoiceno');
                var invoiceDate = button.data('date');
                var origAmt = button.data('origamt');
                $('#DiscCredID').text(invoiceID);
                $('#DiscCredName').text(customer);
                $('#DiscCredDate').text(invoiceDate);
                $('#DiscCredNumber').text(invoiceNo);
                $('#DiscCredOrigAmt').text(origAmt);
                $('#DiscCredAmtDue').text(origAmt);
                $('#DiscCredBalanceDue').text(origAmt);
                $('#DiscCredCredUsed').text('0.00');
                $.ajax({
                    url: 'modules/credit/fetch_credits.php', // Replace with your PHP script to fetch credits data
                    type: 'POST',
                    data: {
                        customerName: customer
                    },
                    success: function(response) {
                        var credits = JSON.parse(response);
                        var tableBody = $('#creditMemoTable tbody');
                        tableBody.empty();
                        if (credits.length === 0) {
                            tableBody.append('<tr><td colspan="6">No credits found for this customer</td></tr>');
                        } else {
                            credits.forEach(function(credit) {
                                var row = '<tr>' +
                                    '<td class="first-column"><input type="checkbox" name="credit[]" value="' + credit.ID + '" data-credit-amount="' + credit.creditAmount + '"></td>' +
                                    '<td>' + credit.creditID + '</td>' +
                                    '<td>' + credit.customerName + '</td>' +
                                    '<td>' + credit.creditAmount + '</td>' + // Display original credit amount
                                    '<td><input type="text" id="creditInput" class="form-control credit-input" value="0.00" data-original-amount="' + credit.creditBalance + '" value=""></td>' + // Input field
                                    '<td class="updated-amount" data-credit-balance="' + credit.creditBalance + '">' + credit.creditBalance + '</td>'
                                    '</tr>';
                                tableBody.append(row);
                            });

                            restoreState(); // Restore the state of checkboxes and input fields

                            // Event listener for credit input changes
                            $('.credit-input').on('input', function() {
                                var totalCredits = 0;
                                $('#creditMemoTable tbody .credit-input').each(function() {
                                    var inputValue = parseFloat($(this).val()) || 0;
                                    totalCredits += inputValue;
                                    var originalAmount = parseFloat($(this).data('original-amount'));
                                    var enteredValue = parseFloat($(this).val());
                                    var updatedAmount = originalAmount - enteredValue;
                                    var updatedAmountTd = $(this).closest('tr').find('.updated-amount');
                                    if (updatedAmount < 0) {
                                        Swal.fire({
                                            position: "top-end",
                                            icon: 'error',
                                            title: 'Invalid Amount',
                                            toast: true,
                                            text: 'The amount you entered is greater than the credit amount',
                                            showConfirmButton: false,
                                            timer: 2000
                                        });
                                        $(this).val('');
                                        updatedAmountTd.text(originalAmount);
                                    } else {
                                        updatedAmountTd.text(updatedAmount);
                                    }
                                });
                                var origAmt = parseFloat($('#DiscCredOrigAmt').text());
                                var balanceDue = origAmt - totalCredits;
                                $('#DiscCredBalanceDue').text(balanceDue.toFixed(2));
                                $('#DiscCredCredUsed').text(totalCredits.toFixed(2));

                                
                            });


                            // Event listener for checkbox change
                            $('input[type="checkbox"]').on('change', function() {
                                var creditAmount = $(this).closest('tr').find('.updated-amount').data('credit-balance');
                                var adjacentInput = $(this).closest('tr').find('.credit-input');
                                if ($(this).is(':checked')) {
                                    adjacentInput.val(creditAmount);
                                } else {
                                    adjacentInput.val('');
                                }
                            });
                        }
                    }
                });
                // Event listener for modal close or save
                $('#addDiscCred').on('hide.bs.modal', function() {
                    storeState();
                });

                // Event listener for modal open
                $('#addDiscCred').on('shown.bs.modal', function() {
                    restoreState();
                });
            });
        });
    });

    $(document).ready(function() {
        $('#reference_number_group').show();
        $('.icon-option').click(function() {
            $('.icon-option').removeClass('selected');
            $(this).addClass('selected');
            var value = $(this).data('value');
            if (value === 'cash') {
                $('#reference_number_group').show();
                $('#check_number_group').hide();
                $('#checkNo').removeAttr('name'); // Remove name attribute from check number input
                $('#RefNo').attr('name', 'RefNo'); // Set name attribute for reference number input
            } else {
                $('#reference_number_group').hide();
                $('#check_number_group').show();
                $('#RefNo').removeAttr('name'); // Remove name attribute from reference number input
                $('#checkNo').attr('name', 'RefNo'); // Set name attribute for check number input
            }
            $('#paymentType').val(value); // Set the paymentType value
        });

        document.getElementById('customerName').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var balance = selectedOption.getAttribute('data-balance');
            var customerName = selectedOption.value;
            if (<?php echo json_encode($customersWithCredit); ?>.includes(customerName)) {
                Swal.fire({
                    icon: 'info',
                    title: 'Credit Balance',
                    text: 'This customer has a credit balance.',
                    confirmButtonText: 'OK'
                });
            }
            document.getElementById('customer_balance').value = balance;
            document.getElementById('amount_due').value = balance;
        });
    });

    $(document).ready(function() {
        $('#paymentForm').submit(function(event) {
            event.preventDefault();

            if (!isFormValid()) {
                // Show SweetAlert error if the form is not valid
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please fill in all required fields.', // Customize the error message
                });
                return;
            }

            var creditUpdates = [];
$('.credit-input').each(function() {
    var creditID = $(this).closest('tr').find('input[type="checkbox"]').val();
    var creditUsed = parseFloat($(this).val()) || 0; // Get the credit used value or default to 0
    console.log('Credit ID:', creditID); // Log creditID value
    console.log('Credit Used:', creditUsed); // Log creditUsed value
    creditUpdates.push({
        id: creditID,
        used: creditUsed
    });
});

// Send the credit updates to the server
$.ajax({
    url: 'modules/customer_center/update_credit_used.php', // Your PHP script to handle credit updates
    type: 'POST',
    data: {
        creditUpdates: JSON.stringify(creditUpdates)
    },
    success: function(response) {
        // Handle success response
        console.log('Credits updated successfully:', response);
        // Proceed with saving the payment...
    },
    error: function(xhr, status, error) {
        // Handle error
        console.error('Error updating credits:', error);
        // You may choose to display an error message to the user or take other actions
    }
});

            // Check if at least one checkbox is checked
            if ($('input[name="invoice[]"]:checked').length === 0) {
                // If no checkbox is checked, display an error message or handle it as needed
                Swal.fire({
                    icon: 'error',
                    title: 'No Invoice Selected',
                    text: 'Please select at least one invoice to proceed with the payment.',
                    showConfirmButton: true
                });
                return; // Exit the function
            }

            // Calculate total amount due from selected invoices
            var totalAmountDue = 0;
            $('input[name="invoice[]"]:checked').each(function() {
                totalAmountDue += parseFloat($(this).closest('tr').find('td:nth-child(8)').text().replace(/[^\d.]/g, '')) || 0;
            });

            // Get payment amount entered by user
            var paymentAmount = parseFloat($('#payment_amount').val());

            var selectedOption = $('.icon-select .icon-option.active').length;
            if (selectedOption === 0) {
                // Neither "CASH" nor "CHECK" is selected
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select either CASH or CHECK.',
                });
                console.log('No option is selected.');
                return // Return false to stop further processing
            } else {
                console.log('Option selected:', $('.icon-select .icon-option.active').data('value'));
            }
            // Calculate credit amount
            var customerName = $('#customerName').val();
            var excessAmount = $('#excessAmount').val();
            var creditAmount = paymentAmount - totalAmountDue;
            // Check if payment amount exceeds total amount due
            if (paymentAmount !== totalAmountDue) {
                // Show SweetAlert for overpayment or underpayment with custom options
                Swal.fire({
                    icon: 'warning',
                    title: paymentAmount > totalAmountDue ? 'Overpayment Alert' : 'Underpayment Alert',
                    html: 'Customer Name: ' + customerName + '<br>Credit Amount: ' + creditAmount.toFixed(2) + '<br>Do you want to leave the credit to be used later or refund the amount to the customer?',
                    showCancelButton: true,
                    confirmButtonText: 'Leave The Credit',
                    confirmButtonColor: '#007bff',
                    cancelButtonText: 'Refund',
                    cancelButtonColor: '#28a745',
                    cancelButtonAriaLabel: 'Refund',
                    buttonsStyling: true,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with saving the payment to save_received_payment.php
                        savePayment(paymentAmount);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Redirect to refund.php
                        window.location.href = 'refund.php';
                    }
                });
            } else {
                // Proceed with saving the payment to save_received_payment.php
                savePayment(paymentAmount);
            }
        });

        function isFormValid() {
            var isValid = true;

            // Validation for "Received Date" field
            if ($("#receivedDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#receivedDate"));
            } else {
                resetInvalidField($("#receivedDate"));
            }

            if ($("#payment_amount").val() === '') {
                isValid = false;
                highlightInvalidField($("#payment_amount"));
            } else {
                resetInvalidField($("#payment_amount"));
            }

            var customerName = $('#customerName').val();
            if (customerName === null || customerName === '') {
                isValid = false;
                highlightInvalidField($("#customerName"));
            } else {
                resetInvalidField($("#customerName"));
            }

            // Validation for "REFERENCE #" and "CHECK #" fields
            var refNo = $('#RefNo').val().trim();
            var checkNo = $('#checkNo').val().trim();

            // Check if either "REFERENCE #" or "CHECK #" is filled out, but not both or none
            if ((refNo === '' && checkNo === '') || (refNo !== '' && checkNo !== '')) {
                isValid = false;
                if (refNo === '') {
                    highlightInvalidField($("#RefNo"));
                } else {
                    resetInvalidField($("#RefNo"));
                }
                if (checkNo === '') {
                    highlightInvalidField($("#checkNo"));
                } else {
                    resetInvalidField($("#checkNo"));
                }
            } else {
                resetInvalidField($("#RefNo"));
                resetInvalidField($("#checkNo"));
            }

            return isValid;
        }

        // Function to highlight an invalid field
        function highlightInvalidField(field) {
            field.addClass("is-invalid");
        }

        // Function to reset the highlighting of an invalid field
        function resetInvalidField(field) {
            field.removeClass("is-invalid");
        }

        function savePayment(paymentAmount) {
            // Calculate total credit applied
            var totalCreditApplied = parseFloat($('#discCredapplied').val()) || 0;

            // Calculate total payment amount including credit
            var totalPaymentAmount = paymentAmount + totalCreditApplied;

            // Get the invoice numbers and IDs of the checked invoices
            var checkedInvoiceData = [];
            $('input[name="invoice[]"]:checked').each(function() {
                var invoiceID = $(this).val();
                var invoiceNo = $(this).closest('tr').find('td:nth-child(3)').text(); // Assuming the invoice number is in the third column
                checkedInvoiceData.push({
                    id: invoiceID,
                    number: invoiceNo
                });
            });

            // Check if at least one invoice is selected
            if (checkedInvoiceData.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Invoice Selected',
                    text: 'Please select at least one invoice to proceed with the payment.',
                    showConfirmButton: true
                });
                return;
            }

            // Include the total payment amount and checked invoice data in the form data
            var formData = $('#paymentForm').serialize();
            formData += '&total_payment_amount=' + totalPaymentAmount;
            formData += '&checked_invoice_data=' + JSON.stringify(checkedInvoiceData);

            // Send AJAX request to save received payments
            $.ajax({
                url: 'modules/customer_center/save_received_payments.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Payment saved successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Extract all invoice IDs from checkedInvoiceData
                    var invoiceIDs = checkedInvoiceData.map(function(invoice) {
                        return invoice.id;
                    });

                    // Update invoice status to PAID
                    updateInvoiceStatusToPaid(invoiceIDs, totalPaymentAmount);

                    // Redirect to 'receive_payments' after a delay
                    setTimeout(function() {
                        window.location.href = 'receive_payments';
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    // Handle error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save payment. Please try again later.',
                        showConfirmButton: true
                    });
                }
            });
        }


        function updateInvoiceStatusToPaid(invoiceIDs, paymentAmount) {
            // Send AJAX request to update invoice status to PAID
            $.ajax({
                url: 'modules/customer_center/update_invoice_status.php',
                type: 'POST',
                data: {
                    invoiceIDs: invoiceIDs,
                    payment_amount: paymentAmount // Include payment amount in the data
                },
                success: function(response) {
                    // Handle success response
                    console.log('Invoice status updated to PAID');
                    // Reload the invoices or any necessary action
                },
                error: function(xhr, status, error) {
                    // Handle error
                    var errorMessage = xhr.responseText; // Get the error message from the server
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage, // Display the error message
                        showConfirmButton: true
                    });
                }
            });
        }

    });


    $(document).ready(function() {
        // Function to calculate excess credit
        function calculateExcessCredit() {
            var paymentAmount = parseFloat($('#payment_amount').val());
            var totalAmountDue = parseFloat($('#amount_due').val());
            var excessAmount = paymentAmount - totalAmountDue;
            $('#excessAmount').val((excessAmount && !isNaN(excessAmount)) ? excessAmount.toFixed(2) : '0');

        }

        // Event listener for changes in payment amount
        $('#payment_amount').on('input', function() {
            calculateExcessCredit();
        });

        // Call calculateExcessCredit initially
        calculateExcessCredit();
    });
</script>