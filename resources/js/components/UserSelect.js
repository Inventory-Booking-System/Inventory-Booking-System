import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { users } from '../api';

export default function UserSelect({ onChange, disabled, defaultValue }) {
    const [data, setData] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        async function get() {
            setIsLoading(true);
            const body = await users.getAll();

            setData(body.map(item => {
                return {...item, value: item.id, label: item.forename+' '+item.surname};
            }));
            setIsLoading(false);
        }
        if (open) {
            get();
        }
    }, []);

    return (
        <Select
            options={data}
            onChange={onChange}
            isLoading={isLoading}
            isDisabled={disabled}
            defaultValue={defaultValue}
            isClearable
        />
    );
}

UserSelect.propTypes = {
    onChange: PropTypes.func,
    disabled: PropTypes.bool,
    defaultValue: PropTypes.object
};
