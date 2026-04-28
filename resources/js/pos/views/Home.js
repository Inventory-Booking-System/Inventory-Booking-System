import React from 'react';
import { useNavigate } from 'react-router-dom';
import Stack from '@mui/material/Stack';
import Alert from '@mui/material/Alert';
import ButtonSelect from '../components/ButtonSelect';
import BarcodeScanner from '../components/BarcodeScanner';

export default function Home() {
    const navigate = useNavigate();

    return (
        <>
            <BarcodeScanner />
            <Stack
                direction="column"
                alignItems="center"
                justifyContent="space-around"
                sx={{ height: '100vh' }}
            >
                <Stack
                    direction="column"
                    spacing={6}
                    alignItems="center"
                    justifyContent="center"
                    sx={{ paddingTop: 5 }}
                >
                    <ButtonSelect
                        click={navigate}
                        sx={{ transform: 'scale(1.75)' }}
                        buttons={[
                            { value: 'book', label: 'New Student Loan' },
                            { value: 'staff-booking', label: 'New Staff Loan', color: 'success' }
                        ]}
                    />
                    <Alert severity="info" variant="outlined">
                        Scan a barcode now to return equipment
                    </Alert>
                </Stack>
                <ButtonSelect
                    click={navigate}
                    size="large"
                    variant="outlined"
                    buttons={[
                        { value: 'collect', label: 'Collect Reservation', color: 'warning' }
                    ]}
                />
            </Stack>
        </>
    );
}
