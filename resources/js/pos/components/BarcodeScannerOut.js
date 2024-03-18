import { useEffect, useState } from 'react';

export default function BarcodeScannerOut({ onScan }) {
    const [code, setCode] = useState([]);

    useEffect(() => {
        /**
         * Scan codes may only contain numbers
         */
        function handleKeyPress(event) {
            if (event.key !== 'Enter' && /^[a-zA-Z| ]+$/.test(event.key)) {
                return;
            }
            if (event.key === 'Enter' && code.length) {
                onScan(code.join(''));
                setCode([]);
            } else if (event.key !== 'Enter') {
                setCode([...code, event.key]);
            }
        }
        document.addEventListener('keyup', handleKeyPress);
        return () => document.removeEventListener('keyup', handleKeyPress);
    }, [code, onScan]);

    return;
}
