<div class="form-content addAsset" style="display:none;">
    <form>
        <!-- Error -->
        <span id="errorText"></span>

        <!-- Name -->
        <label id="nameLabel">Asset Name</label>
        <input class="form-control" id="assetName" />

        <!-- Description -->
        <label id="descriptionLabel">Asset Description</label>
        <textarea class="form-control" id="assetDescription" rows="4"></textarea>

        <!-- Asset Tag -->
        <label id="tagLabel">Asset Tag</label>
        <input class="form-control" id="assetTag" />

        <!-- Asset Cost -->
        <label id="costLabel">Asset Cost</label>
        <input class="form-control" id="assetCost" />

        <!-- Asset Bookable -->
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="assetBookable" id="assetBookable">
        <label id="bookableLabel" class="form-check-label" for="assetBookable">
            Bookable
        </label>
        </div>
    </form>
</div>