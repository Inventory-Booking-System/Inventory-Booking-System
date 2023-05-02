import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';

export default function UserSelect({ users, onChange, isLoading, disabled, defaultValue }) {
    return (
        <Select
            options={users}
            onChange={onChange}
            isLoading={isLoading}
            isDisabled={disabled}
            defaultValue={defaultValue}
            isClearable
        />
    );
}

UserSelect.propTypes = {
    users: PropTypes.array,
    onChange: PropTypes.func,
    isLoading: PropTypes.bool,
    disabled: PropTypes.bool,
    defaultValue: PropTypes.object
};
