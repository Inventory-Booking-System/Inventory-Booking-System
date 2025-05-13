import * as React from 'react';
import PropTypes from 'prop-types';
import TextField from '@mui/material/TextField';
import Stack from '@mui/material/Stack';
import Autocomplete from '@mui/material/Autocomplete';

export default function Search({ name, options, onSelect, loading, value, onChange, sx }) {

    return (
        <Stack spacing={2} sx={{ width: 300, ...sx }}>
            <Autocomplete
                inputValue={value}
                onChange={(_, newValue) => {
                    const user = options.find(x => x.label === newValue);
                    onSelect(user);
                    onChange(newValue);
                }}
                options={options.map((option) => option.label)}
                loading={loading}
                disableClearable
                open
                renderInput={(params) => (
                    <TextField
                        {...params}
                        label={name}
                        autoFocus
                        InputProps={{
                            ...params.InputProps,
                            type: 'search',
                        }}
                    />
                )}
            />
        </Stack>
    );
}

Search.propTypes = {
    name: PropTypes.string,
    options: PropTypes.array,
    onSelect: PropTypes.func,
    loading: PropTypes.bool,
    value: PropTypes.string,
    onChange: PropTypes.func,
    sx: PropTypes.object,
};
