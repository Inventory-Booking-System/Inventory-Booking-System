import React from 'react';
import { createRoot } from 'react-dom/client';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { SnackbarProvider, MaterialDesignContent } from 'notistack';
import CssBaseline from '@mui/material/CssBaseline';
import { createTheme, ThemeProvider, styled } from '@mui/material/styles';
import Home from './pos/views/Home';
import Student from './pos/views/Student';
import StudentSelected from './pos/views/StudentSelected';
import Collect from './pos/views/Collect';
import CollectConfirm from './pos/views/CollectConfirm';
import StaffBooking from './pos/views/StaffBooking';
import Return from './pos/views/Return';

const theme = createTheme({
    palette: {
        mode: 'dark'
    }
});

const StyledMaterialDesignContent = styled(MaterialDesignContent)(() => ({
    '&.notistack-MuiContent': {
        fontSize: '1.5rem',
        padding: '10px 20px'
    },
    '&.notistack-MuiContent svg': {
        width: '1.5rem !important',
        height: '1.5rem !important',
    },
}));

function App() {
    return (
        <>
            <ThemeProvider theme={theme}>
                <CssBaseline />
                <SnackbarProvider
                    Components={{
                        success: StyledMaterialDesignContent,
                        warning: StyledMaterialDesignContent,
                        error: StyledMaterialDesignContent,
                        info: StyledMaterialDesignContent,
                    }}
                >
                    <MemoryRouter>
                        <Routes>
                            <Route path="/" element={<Home />} />
                            <Route path="book" element={<Student />} />
                            <Route path="book/:studentId" element={<StudentSelected />} />
                            <Route path="collect" element={<Collect />} />
                            <Route path="collect/:loanId" element={<CollectConfirm />} />
                            <Route path="staff-booking" element={<StaffBooking />} />
                            <Route path="return" element={<Return />} />
                        </Routes>
                    </MemoryRouter>
                </SnackbarProvider>
            </ThemeProvider>
        </>
    );

}

createRoot(document.getElementById('app')).render(<App />);
