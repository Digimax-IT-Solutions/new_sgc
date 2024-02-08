function checkOpenPOs() {
  var selectedVendor = $("#vendorSelect").val();
  $("#itemTableBody").empty();

  $.ajax({
    type: "POST",
    url: "modules/vendor_center/receive_items/check_open_pos.php",
    data: {
      vendor: selectedVendor,
    },
    success: function (response) {
      console.log(response);
      if (response.hasOpenPOs) {
        $("#openPOsButton").show();
        console.log("Received openPOs:", response.openPOs);
        Swal.fire({
          title: "Confirmation",
          text: "Open purchase orders exist for this vendor. Do you want to receive against one or more of these orders?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Yes, receive",
          cancelButtonText: "No, cancel",
        }).then((result) => {
          if (result.isConfirmed) {
            console.log("User confirmed");
            showOpenPOs(response.openPOs);
          } else {
            console.log("User canceled");
          }
        });
      } else {
        $("#openPOsButton").hide();
      }
    },
    error: function (error) {
      console.error("Error checking for open POs: " + error);
    },
  });
}
// Event listener for "Open POs" button click
$("#openPOsButton").on("click", function () {
  // Open the openPOsModal
  $("#openPOsModal").modal("show");
});

function showOpenPOs(openPOs) {
  console.log("Inside showOpenPOs function");

  // Empty the table body
  $("#openPOsTableBody").empty();

  // Populate the table body with data
  openPOs.forEach(function (po) {
    var rowHtml = "<tr>";
    rowHtml +=
      '<td><input type="checkbox" class="poCheckbox" data-po-no="' +
      po.poNo +
      '" data-po-id="' +
      po.poID +
      '"></td>';
    rowHtml += "<td>" + po.poNo + "</td>";
    rowHtml += "<td>" + po.vendor + "</td>";
    rowHtml += "<td>" + po.totalAmountDue + "</td>";
    rowHtml += "<td>" + po.poDate + "</td>";
    rowHtml += "<td>" + po.memo + "</td>";
    rowHtml += "</tr>";

    $("#openPOsTableBody").append(rowHtml);

  });

  // Show the modal
  $("#openPOsModal").modal("show");

  // Handle "Select All" checkbox change event
  $("#selectAllCheckbox").change(function () {
    var isChecked = $(this).prop("checked");
    $(".poCheckbox").prop("checked", isChecked);


  });

  // Handle individual checkbox change event
  $(".poCheckbox").change(function () {
    // Update item table based on the selected/deselected checkboxes
    updateItemTable();
    
  });
}
// Event listener for removing an item
$("#itemTableBody").on("click", ".removeItemBtn", function () {
  $(this).closest("tr").remove();
         // Update item amounts
         updateItemAmount();

         // Update the footer calculations
         calculateGrossAmount();
         calculatePercentages();
});

function displayPurchaseOrderItems(selectedPOs, selectedPOIDs) {
  // Iterate through selected POs and fetch items for each PO
  selectedPOIDs.forEach(function (poID, index) {
    var poNo = selectedPOs[index]; // Get the corresponding poNo

    // Make an AJAX request to fetch items for the selected PO
    $.ajax({
      type: "POST",
      url: "modules/vendor_center/receive_items/fetch_po_items.php",
      data: {
        poID: poID,
      },
      success: function (response) {
        console.log(response); // Log the response to the console for debugging

        // Check the structure of the response
        if (Array.isArray(response)) {
          // Append the fetched items to the itemTableBody
          response.forEach(function (item) {
            appendItemRow(item, poNo);
          });

          // Update item amounts
          updateItemAmount();

          // Update the footer calculations
          calculateGrossAmount();
          calculatePercentages();
        } else {
          console.error("Invalid response structure. Expected an array.");
        }
      },
      error: function (error) {
        console.error("Error fetching PO items: " + error);
      },
    });
  });
}
// Function to update the item table based on the selected/deselected checkboxes
function updateItemTable() {
  // Clear the item table
  $("#itemTableBody").empty();

  // Iterate through selected POs and append items to the item table
  $(".poCheckbox:checked").each(function () {
    var poNo = $(this).data("po-no");
    var poID = $(this).data("po-id");

    // Fetch and display purchase order items
    displayPurchaseOrderItems([poNo], [poID]);

 
  });
  updateItemAmount();
  
  // Update the footer calculations
  calculateGrossAmount();
  calculatePercentages();
}

// Function to append a new row for an item
function appendItemRow(item, poNo) {
  
  var newRow = "<tr>";
  newRow +=
    "<td><input type='text' class='form-control' value='" +
    item.item +
    "' readonly></td>";
  newRow +=
    "<td><input type='text' class='form-control' value='" +
    item.description +
    "' readonly></td>";
  newRow +=
    "<td><input type='number' class='form-control' name='quantity[]' value='" +
    item.quantity +
    "'></td>";
  newRow +=
    "<td><input type='text' class='form-control' value='" +
    item.uom +
    "' readonly></td>";
  newRow +=
    "<td><input type='text' class='form-control' name='rate[]' value='" +
    item.rate +
    "' readonly></td>";
  newRow +=
    "<td><input type='number' class='form-control amount-field' name='amount[]' value='" +
    item.amount +
    "' readonly></td>";
  newRow +=
    "<td><input type='text' class='form-control poNo-input' value='" +
    poNo +
    "' readonly></td>";
  newRow +=
    "<td> <button type='button' class='btn btn-danger btn-sm removeItemBtn'>Remove</button></td>";
  newRow += "</tr>";

  $("#itemTableBody").append(newRow);

}

// // Function to update the item amount based on quantity and rate
// function updateItemAmount() {
//   $("#itemTableBody tr").each(function () {
//     var quantity = parseFloat($(this).find("td:eq(2) input").val()) || 0;
//     var rate = parseFloat($(this).find("td:eq(4) input").val()) || 0;

//     // Calculate the new amount
//     var newAmount = quantity * rate;

//     // Update the amount field
//     $(this).find("td:eq(5) input").val(newAmount.toFixed(2));
//   });
// }
// // Event listener for removing an item
// $("#itemTableBody").on("click", ".removeItemBtn", function () {
//   // Remove the row
//   $(this).closest("tr").remove();

// });

function updateItemAmount() {
  // Event listener for updating amount based on quantity and rate
  $("#itemTableBody").on(
    "input",
    "input[name='quantity[]'], input[name='rate[]']",
    function () {
      var row = $(this).closest("tr");
      var quantity =
        parseFloat(row.find("input[name='quantity[]']").val()) || 0;
      var rate = parseFloat(row.find("input[name='rate[]']").val()) || 0;

      var amount = quantity * rate;
      row.find("input[name='amount[]']").val(amount);

      // Recalculate gross amount
      calculateGrossAmount();
      // Recalculate other percentages
      calculatePercentages();
    }
  );
}
// Function to calculate the gross amount
function calculateGrossAmount() {
  var grossAmount = 0;
  $("input[name='amount[]']").each(function () {
    grossAmount += parseFloat($(this).val()) || 0;
  });
  $("#grossAmount").val(grossAmount.toFixed(2));
}
// Function to calculate other percentages
function calculatePercentages() {
  var grossAmount = parseFloat($("#grossAmount").val()) || 0;
  var discountPercentage = parseFloat($("#discountPercentage").val()) || 0;
  var vatPercentage = parseFloat($("#vatPercentage").val()) || 0;
  var taxWithheldPercentage =
    parseFloat($("#taxWithheldPercentage").val()) || 0;

  // FOOTER CALCULATION
  var discountAmount = (discountPercentage / 100) * grossAmount;
  var netAmountDue = grossAmount - discountAmount;
  $("#netAmountDue").val(netAmountDue.toFixed(2));
  $("#discountAmount").val(discountAmount.toFixed(2));
  var vatPercentageAmount = (vatPercentage / 100) * netAmountDue;
  var netOfVat = netAmountDue / (1 + vatPercentage / 100);
  $("#vatPercentageAmount").val(vatPercentageAmount.toFixed(2));
  $("#netOfVat").val(netOfVat.toFixed(2));
  var taxWitheldAmount = (taxWithheldPercentage / 100) * netOfVat;
  $("#taxWitheldAmount").val(taxWitheldAmount.toFixed(2));
  var totalAmountDue = netAmountDue - taxWitheldAmount;
  $("#totalAmountDue").val(totalAmountDue.toFixed(2));
}

// Event listener for updating percentages
$("#discountPercentage, #vatPercentage, #taxWithheldPercentage").on(
  "input",
  function () {
    calculatePercentages();
  }
);

