<!-- Modal for Adding New Location -->
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLocationModalLabel">Add New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="api/masterlist/direct_add_location.php" id="addLocationForm">
                    <input type="hidden" name="action" value="add">

                    <!-- LOCATION -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Enter location">
                    </div>

                    <!-- Submit Button in the Modal -->
                    <button type="button" class="btn btn-primary" id="addLocationSubmit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>