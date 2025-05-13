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
                        buttons={[
                            { value: 'book', label: 'Borrow Equipment' }
                        ]}
                    />
                    <Alert severity="info" variant="outlined" sx={{ zoom: '60%' }}>
                        Scan a barcode now to return equipment
                    </Alert>
                </Stack>
                <ButtonSelect
                    click={navigate}
                    size="medium"
                    variant="outlined"
                    buttons={[
                        { value: 'collect', label: 'Collect Reservation', color: 'warning' },
                        { value: 'staff-booking', label: 'New Staff Loan', color: 'success' }
                    ]}
                />
            </Stack>
        </>
    );
}
