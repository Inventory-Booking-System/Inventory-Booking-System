import React from 'react';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Search from '../components/Search';

export default function Staff() {
    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
                justifyContent="center"
            >
                <Search
                    name="Staff Search"
                />
            </Stack>
        </Box>
    );
}
