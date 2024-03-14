import React from 'react';
import { createRoot } from 'react-dom/client';
import { SnackbarProvider } from 'notistack';
import CssBaseline from '@mui/material/CssBaseline';
import { createTheme, ThemeProvider } from '@mui/material/styles';
// import Button from '@mui/material/Button';
// import Stack from '@mui/material/Stack';
import Keyboard from './pos/components/Keyboard';
import BarcodeScanner from './pos/components/BarcodeScanner';

const theme = createTheme({
    palette: {
        mode: 'dark'
    }
});

function App() {
    return (
        <>
            <ThemeProvider theme={theme}>
                <SnackbarProvider>
                    <CssBaseline />
                    <BarcodeScanner />
                    {/*<Stack direction="row" spacing={2}>
                        <Button variant="contained" size="large"></Button>
                        <Button variant="contained" size="large"></Button>
                    </Stack>*/}
                    <Keyboard />
                </SnackbarProvider>
            </ThemeProvider>
        </>
    );

}

createRoot(document.getElementById('app')).render(<App />);
