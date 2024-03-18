import React from 'react';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Search from '../components/Search';

export default function Student() {
    const navigate = useNavigate();

    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
                justifyContent="center"
            >
                <Search
                    name="Student Search"
                    onSelect={(user) => navigate(user.label, { state: { user } })}
                    options={[
                        { userId: 1, label: 'Test' }
                    ]}
                />
            </Stack>
        </Box>
    );
}
