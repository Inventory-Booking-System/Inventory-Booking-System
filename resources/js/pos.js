import React from 'react';
import { createRoot } from 'react-dom/client';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { SnackbarProvider } from 'notistack';
import CssBaseline from '@mui/material/CssBaseline';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import Home from './pos/views/Home';
import Student from './pos/views/Student';
import StudentSelected from './pos/views/StudentSelected';
import Collect from './pos/views/Collect';
import CollectConfirm from './pos/views/CollectConfirm';
import StaffBooking from './pos/views/StaffBooking';

const theme = createTheme({
    palette: {
        mode: 'dark'
    }
});

function App() {
    return (
        <>
            <ThemeProvider theme={theme}>
                <CssBaseline />
                <SnackbarProvider>
                    <MemoryRouter>
                        <Routes>
                            <Route path="/" element={<Home />} />
                            <Route path="book" element={<Student />} />
                            <Route path="book/:studentId" element={<StudentSelected />} />
                            <Route path="collect" element={<Collect />} />
                            <Route path="collect/:loanId" element={<CollectConfirm />} />
                            <Route path="staff-booking" element={<StaffBooking />} />
                        </Routes>
                    </MemoryRouter>
                </SnackbarProvider>
            </ThemeProvider>
        </>
    );

}

createRoot(document.getElementById('app')).render(<App />);
