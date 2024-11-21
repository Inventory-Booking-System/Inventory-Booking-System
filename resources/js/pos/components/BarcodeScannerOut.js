import { useEffect, useState, memo } from 'react';
import PropTypes from 'prop-types';
import { useSnackbar } from 'notistack';

const BarcodeScannerOut = memo(function BarcodeScannerOut({ assets, onScan }) {
    const { enqueueSnackbar } = useSnackbar();
    const [code, setCode] = useState([]);

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

    useEffect(() => {
        /**
         * Scan codes may only contain numbers
         */
        function handleKeyPress(event) {
            if (event.key !== 'Enter' && /^[a-zA-Z| ]+$/.test(event.key)) {
                return;
            }
            if (event.key === 'Enter' && code.length) {
                if (!assets?.length) {
                    enqueueSnackbar('Scanner not ready.', {
                        variant: 'error',
                        autoHideDuration: 5000
                    });
                    (new Audio('/pos-static/error.wav')).play();
                    setCode([]);
                    return;
                }

                if (assets.find(x => x.tag === parseInt(code.join('')))) {
                    onScan(code.join(''));
                } else {
                    enqueueSnackbar(`Asset ${code.join('')} not found.`, {
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
});

BarcodeScannerOut.propTypes = {
    assets: PropTypes.array.isRequired,
    onScan: PropTypes.func.isRequired
};

export default BarcodeScannerOut;