import { useEffect, useState } from 'react';
import { useSnackbar } from 'notistack';
import { scanIn } from '../../api/assets';
import { assets as assetsApi } from '../../api';

export default function BarcodeScanner() {
    const { enqueueSnackbar } = useSnackbar();
    const [code, setCode] = useState([]);
    const [assets, setAssets] = useState([]);

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Math.round(Date.now() / 1000),
            endDateTime: Math.round(Date.now() / 1000) + 1,
        })
            .then(setAssets);
    }, []);

    /**
     * Disable right click
     */
    useEffect(() => {
        function preventDefault(e) {
            e.preventDefault();
        }
        document.addEventListener('contextmenu', preventDefault);
        return () => document.removeEventListener('contextmenu', preventDefault);
    }, []);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleOpen = async (code) => {
        try {
            await scanIn({ tag: code.join('') });
            enqueueSnackbar(`Scanned in ${code.join('')}`, {
                variant: 'success',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/notify.wav')).play();
        } catch (e) {
            if (e.error === 'NO_OPEN_LOANS') {
                enqueueSnackbar(`Asset ${code.join('')} has no open loans`, {
                    variant: 'warning',
                    autoHideDuration: 5000
                });
                (new Audio('/pos-static/warn.wav')).play();
                return;
            }

            enqueueSnackbar(`Failed to scan in ${code.join('')}`, {
                variant: 'error',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/error.wav')).play();
        }
    };

    useEffect(() => {
        /**
         * Scan codes may only contain numbers
         */
        function handleKeyPress(event) {
            if (event.key !== 'Enter' && /^[a-zA-Z| ]+$/.test(event.key)) {
                return;
            }
            if (event.key === 'Enter' && code.length) {
                if (assets.find(x => x.tag === parseInt(code.join('')))) {
                    handleOpen(code);
                } else {
                    enqueueSnackbar(`Asset ${code.join('')} not found`, {
                        variant: 'error',
                        autoHideDuration: 5000
                    });
                    (new Audio('/pos-static/error.wav')).play();
                }
                setCode([]);
            } else if (event.key !== 'Enter') {
                setCode([...code, event.key]);
            }
        }
        document.addEventListener('keyup', handleKeyPress);
        return () => document.removeEventListener('keyup', handleKeyPress);
    }, [assets, code, enqueueSnackbar, handleOpen]);

    return;
}
