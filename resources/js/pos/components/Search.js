import * as React from 'react';
import PropTypes from 'prop-types';
import TextField from '@mui/material/TextField';
import Stack from '@mui/material/Stack';
import Autocomplete from '@mui/material/Autocomplete';

export default function Search({ name, options, onSelect }) {
    const [value, setValue] = React.useState('');
    const [inputValue, setInputValue] = React.useState('');
    return (
        <Stack spacing={2} sx={{ width: 300 }}>
            <Autocomplete
                inputValue={inputValue}
                onInputChange={(_, newInputValue) => {
                    if (!newInputValue || /^[a-zA-Z| ]+$/.test(newInputValue)) {
                        setInputValue(newInputValue);
                    }
                }}
                value={value}
                onChange={(_, newValue) => {
                    const user = options.find(x => x.label === newValue);
                    onSelect(user);
                    setValue(newValue);
                }}
                options={options.map((option) => option.label)}
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
    onSelect: PropTypes.func
};
