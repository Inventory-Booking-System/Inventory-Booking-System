import { useEffect, useState } from 'react';
import { useSnackbar } from 'notistack';
import { assets as assetsApi } from '../../api';

export default function BarcodeScannerOut({ onScan }) {
    const { enqueueSnackbar } = useSnackbar();
    const [code, setCode] = useState([]);
    const [assets, setAssets] = useState([]);

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Math.round(Date.now() / 1000),
            endDateTime: Math.round(new Date(new Date().setHours(15, 30, 0, 0)).getTime() / 1000),
        })
            .then(setAssets);
    }, []);

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
                    onScan(code.join(''));
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
    }, [assets, code, enqueueSnackbar, onScan]);

    return;
}
