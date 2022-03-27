<div class="form-content addAsset" style="display:none;">
    <form>
        <!-- Error -->
        <span id="errorText"></span>

        <!-- Name -->
        <label>Asset Name</label>
        <input class="form-control" id="assetName" />

        <!-- Description -->
        <label>Asset Description</label>
        <textarea class="form-control" id="assetDescription" rows="4"></textarea>

        <!-- Asset Tag -->
        <label>Asset Tag</label>
        <input class="form-control" id="assetTag" />

        <!-- Asset Location -->
        <label>Asset Location</label>
        <select class="form-control" id="assetLocation">
            <option>Ranmore</option>
            <option>Bradley</option>
        </select>

        <!-- Asset Cost -->
        <label>Asset Cost</label>
        <input class="form-control" id="assetCost" />

        <!-- Asset Bookable -->
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="assetBookable" id="assetBookable">
        <label class="form-check-label" for="assetBookable">
            Bookable
        </label>
        </div>
    </form>
</div>