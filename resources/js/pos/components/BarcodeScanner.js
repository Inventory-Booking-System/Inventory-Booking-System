import React, { useEffect, useState } from 'react';
import { closeSnackbar, useSnackbar } from 'notistack';
import Button from '@mui/material/Button';
import { scanIn } from '../../api/assets';

export default function BarcodeScanner() {
    const { enqueueSnackbar } = useSnackbar();
    const [code, setCode] = useState([]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleOpen = (code) => {
        enqueueSnackbar(`Scanned in ${code.join('')}`, {
            variant: 'success',
            autoHideDuration: 10000,
            action: (snackbarId) => <Button onClick={() => closeSnackbar(snackbarId)}>Undo</Button>,
            onClose: async (_, reason) => {
                if (reason !== 'instructed') {
                    await scanIn({ tag: code.join('') });
                }
            }
        });
    };

    useEffect(() => {
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                handleOpen(code);
                setCode([]);
            } else {
                setCode([...code, event.key]);
            }
        }
        document.addEventListener('keyup', handleKeyPress);
        return () => document.removeEventListener('keyup', handleKeyPress);
    }, [code, handleOpen]);

    return;
}
