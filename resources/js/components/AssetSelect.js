import React, { useMemo, useCallback, useState } from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';

const colorStyles = {
    option: (styles, { data }) => ({...styles, color: data.available ? styles.color : 'red'})
};

export default function AssetSelect({ assets, shoppingCart, onChange, isLoading, disabled }) {
    const [inputValue, setInputValue] = useState();

    const assetAvailability = useMemo(() => {
        const [groupsCopy = {}, assetsCopy = {}] = JSON.parse(JSON.stringify(assets));

        const updateAvailability = (groups, assets, cart) => {
            assets.forEach(asset => {
                asset.originalAvailable = asset.available;
                cart.forEach(cartItem => {
                    if (asset.id === cartItem.id) {
                        asset.available = false;
                        asset.isDisabled = true;

                        /**
                         * Update availability for the asset's group
                         * Only update if the asset was available before, as
                         * when editing the cart, the availability will have
                         * already been updated for the group on the backend.
                         */
                        if (asset.asset_group_id && asset.originalAvailable) {
                            const group = groups.find(group => group.id === asset.asset_group_id);
                            if (group) {
                                group.available_assets_count--;
                                group.available = group.available_assets_count > 0;
                                group.isDisabled = group.available_assets_count === 0;
                                group.label = `${group.name} (${group.available_assets_count} available)`;
                            }
                        }

                        asset.originalAvailable = false;
                    }
                });
            });

            /**
             * When a group is added to the cart, all assets in the group that
             * were available before stay available until the group is fully
             * booked. Then all assets in the group become unavailable.
             *
             * For example if 3 of 5 assets have been booked through the group,
             * any 2 of the assets can be booked individually, before the other
             * 3 become unavailable.
             */

            groups.forEach(group => {
                cart.forEach(cartItem => {
                    if (group.id === cartItem.id) {
                        group.available = cartItem.quantity < group.available_assets_count;
                        group.isDisabled = cartItem.quantity === group.available_assets_count;
                        group.label = `${group.name} (${group.available_assets_count - cartItem.quantity} available)`;

                        /**
                         * Update availability for assets in the group
                         */
                        const assetsInGroup = assets.filter(asset => asset.asset_group_id === group.id);
                        assetsInGroup.forEach((asset) => {
                            if (asset.originalAvailable) { // only change if asset was available before
                                asset.available = group.available_assets_count - cartItem.quantity;
                                asset.isDisabled = !asset.available;
                            }
                        });
                    }
                });
            });
        };

        updateAvailability(groupsCopy.options || [], assetsCopy.options || [], shoppingCart || []);

        return [groupsCopy, assetsCopy];
    }, [assets, shoppingCart]);

    const handleInputChange = useCallback((query, { action }) => {
        if (action !== 'set-value') {
            setInputValue(query);
        }
    }, []);

    return (
        <Select
            options={assetAvailability}
            styles={colorStyles}
            onChange={onChange}
            isLoading={isLoading}
            isDisabled={disabled}
            closeMenuOnSelect={false}
            value=""
            onInputChange={handleInputChange}
            inputValue={inputValue}
        />
    );
}

AssetSelect.propTypes = {
    assets: PropTypes.array,
    shoppingCart: PropTypes.array,
    onChange: PropTypes.func,
    isLoading: PropTypes.bool,
    disabled: PropTypes.bool
};
