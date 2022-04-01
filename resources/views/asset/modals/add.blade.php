<div class="form-content addAsset" style="display:none;">
    <form>
        <!-- Error -->
        <span id="errorText"></span>

        <!-- Name -->
        <label>Asset Name</label>
        <span id="nameError" class="inputError"></span>
        <input class="form-control" id="assetName" />

        <!-- Description -->
        <label>Asset Description</label>
        <span id="descriptionError" class="inputError"></span>
        <textarea class="form-control" id="assetDescription" rows="4"></textarea>

        <!-- Asset Tag -->
        <label>Asset Tag</label>
        <span id="tagError" class="inputError"></span>
        <input class="form-control" id="assetTag" />

        <!-- Asset Cost -->
        <label>Asset Cost</label>
        <span id="costError" class="inputError"></span>
        <input class="form-control" id="assetCost" />

        <!-- Asset Bookable -->
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="assetBookable" id="assetBookable">
        <label class="form-check-label" for="assetBookable">
            Bookable
        </label>
        <span id="bookableError" class="inputError"></span>
        </div>
    </form>
</div>