import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import { distributionGroups } from '../api';

export default function DistributionGroupSelect({ onChange, disabled, defaultValue }) {
    const [groups, setGroups] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        async function get() {
            setIsLoading(true);
            const body = await distributionGroups.getAll();

            setGroups(body.map(item => {
                return {...item, value: item.id, label: item.name};
            }));
            setIsLoading(false);
        }
        if (open) {
            get();
        }
    }, []);

    return (
        <Select
            options={groups}
            onChange={onChange}
            isLoading={isLoading}
            isDisabled={disabled}
            defaultValue={defaultValue}
            isClearable
        />
    );
}

DistributionGroupSelect.propTypes = {
    onChange: PropTypes.func,
    disabled: PropTypes.bool,
    defaultValue: PropTypes.object
};
