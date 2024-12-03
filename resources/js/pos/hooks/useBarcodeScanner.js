import { useEffect, useState } from 'react';

export function useBarcodeScanner(handleScanComplete) {
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
                handleScanComplete(parseInt(code.join('')));
                setCode([]);
            } else if (event.key !== 'Enter') {
                setCode([...code, event.key]);
            }
        }
        document.addEventListener('keyup', handleKeyPress);
        return () => document.removeEventListener('keyup', handleKeyPress);
    }, [code, handleScanComplete]);

    return;
}
