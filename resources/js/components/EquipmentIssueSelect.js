import React, { useCallback, useState } from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';

export default function EquipmentIssueSelect({ data, onChange, disabled, isLoading }) {
    const [inputValue, setInputValue] = useState();

    const handleInputChange = useCallback((query, { action }) => {
        if (action !== 'set-value') {
            setInputValue(query);
        }
    }, []);

    return (
        <Select
            options={data}
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

EquipmentIssueSelect.propTypes = {
    data: PropTypes.array,
    onChange: PropTypes.func,
    disabled: PropTypes.bool,
    isLoading: PropTypes.bool
};
