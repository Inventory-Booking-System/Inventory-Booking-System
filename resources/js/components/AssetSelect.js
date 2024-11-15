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

        const updateAvailability = (options, cart) => {
            options.forEach(item => {
                cart.forEach(cartItem => {
                    if (item.id === cartItem.id) {
                        if (item.type === 'group') {
                            item.available = cartItem.quantity < item.available_assets_count;
                            item.isDisabled = cartItem.quantity === item.available_assets_count;
                            item.label = `${item.name} (${item.available_assets_count - cartItem.quantity} available)`;
                        } else {
                            item.available = false;
                            item.isDisabled = true;
                        }
                    }
                });
            });
        };

        updateAvailability(groupsCopy.options || [], shoppingCart || []);
        updateAvailability(assetsCopy.options || [], shoppingCart || []);

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
