<!-- Modify Asset -->
<div class="form-content modifyAsset" style="display:none;">
    <form>
        <!-- Error -->
        <span id="errorTextModify"></span>

        <!-- Select Asset to Modify -->
        <label>Select Asset To Modify</label>
        <select class="form-control" id="assetToModify">
            <?php
                //Get a list of asset names & assets tags currently in db
                $query = $this->db->query("SELECT AssetID, AssetName, AssetTag FROM assets");
                echo "<option disabled selected value>Select an asset to modify</option>";
                foreach ($query->result() as $row)
                {
                    echo "<option id='Modify-{$row->AssetID}'>{$row->AssetName} ({$row->AssetTag})";
                }
            ?>
        </select>

        <!-- Name -->
        <label>Asset Name</label>
        <input class="form-control" id="assetNewName" />

        <!-- Description -->
        <label>Asset Description</label>
        <textarea class="form-control" id="assetNewDescription" rows="4"></textarea>

        <!-- Asset Tag -->
        <label>Asset Tag</label>
        <input class="form-control" id="assetNewTag" />

        <!-- Asset Location -->
        <label>Asset Location</label>
        <select class="form-control" id="assetNewLocation">
            <option>Ranmore</option>
            <option>Bradley</option>
        </select>

        <!-- Asset ID -->
        <input id="assetNewID" type="hidden" />

        <!-- Original AssetTag -->
        <input id="originalAssetTag" type="hidden" />
    </form>
</div>