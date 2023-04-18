// eslint-disable-next-line no-unused-vars
import { h } from 'preact';
import React, { useMemo } from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { useCallback, useState } from 'preact/hooks';

const colorStyles = {
    option: (styles, { data }) => ({...styles, color: data.available ? styles.color : 'red'})
};

export default function AssetSelect({ assets, shoppingCart, onChange, isLoading, disabled }) {
    const [inputValue, setInputValue] = useState();

    const assetAvailability = useMemo(() => {
        const updatedAssets = JSON.parse(JSON.stringify(assets));
        for (var i = 0; i < updatedAssets.length; i++) {
            for (var j = 0; j < shoppingCart.length; j++) {
                if (updatedAssets[i].id === shoppingCart[j].id) {
                    updatedAssets[i].available = false;
                    updatedAssets[i].isDisabled = true;
                }
            }
        }
        return updatedAssets;
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
