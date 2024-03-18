import React from 'react';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import ButtonSelect from '../components/ButtonSelect';
import BarcodeScanner from '../components/BarcodeScanner';

export default function Home() {
    const navigate = useNavigate();

    return (
        <>
            <BarcodeScanner />
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <ButtonSelect
                        click={navigate}
                        buttons={[
                        // { value: 'staff', label: 'Staff'},
                            { value: 'student', label: 'Student'}
                        ]}
                    />
                </Stack>
            </Box>

        </>
    );
}
