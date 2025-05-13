import * as React from 'react';
import PropTypes from 'prop-types';
import TextField from '@mui/material/TextField';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Grid from '@mui/material/Grid';

export default function NameSearch({ name, options, onSelect, value }) {
    const results = value.length ? options.filter(option => option.label.toLowerCase().includes(value.toLowerCase())) : [];

    return (
        <Stack
            spacing={2}
            sx={{ width: '100%' }}
            alignItems='center'
        >
            <Grid
                container
                direction="row"
                rowSpacing={1}
                columnSpacing={0}
                sx={{
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: 100,
                    width: '100%'
                }}
            >
                {results.slice(0,6).map(option => (
                    <Grid item xs={4} key={option.label}>
                        <Button
                            variant='contained'
                            onClick={() => {
                                onSelect(option);
                            }}
                        >
                            {option.label}
                        </Button>
                    </Grid>
                ))}
            </Grid>
            <TextField
                value={value}
                label={name}
                autoFocus
                sx={{ width: 300 }}
                InputProps={{
                    type: 'search'
                }}
            />
        </Stack>
    );
}

NameSearch.propTypes = {
    name: PropTypes.string,
    options: PropTypes.array,
    onSelect: PropTypes.func,
    loading: PropTypes.bool,
    value: PropTypes.string,
    onChange: PropTypes.func,
    sx: PropTypes.object,
};
