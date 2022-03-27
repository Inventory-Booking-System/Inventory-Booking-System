<!-- Modal Delete Asset -->
<div class="form-content deleteAsset" style="display:none;">
    <form>
        <!-- Error -->
        <span id="errorTextDelete"></span>

        <!-- Lists of assets to delete -->
        <label>Asset Location</label>
        <select class="form-control" id="assetToDelete">
            <?php
                //Get a list of asset names & assets tags currently in db
                $query = $this->db->query("SELECT AssetID, AssetName, AssetTag FROM assets");
                echo "<option disabled selected value>Select an asset to delete</option>";
                foreach ($query->result() as $row)
                {
                    echo "<option id='Delete-{$row->AssetID}'>{$row->AssetName} ({$row->AssetTag})";
                }
            ?>
        </select>
    </form>
</div>