import React, { useEffect } from 'react';
import bwipjs from 'bwip-js';
import Keyboard from './components/Keyboard';

export default function App() {
    useEffect(() => {
        try {
            bwipjs.toCanvas('mycanvas', {
                bcid:        'code39',       // Barcode type
                text:        '07142',    // Text to encode
                scale:       2,               // 3x scaling factor
                height:      10,              // Bar height, in millimeters
                includetext: true,            // Show human-readable text
                textxalign:  'center',        // Always good to set this
                padding: 30
            });
        } catch (e) {
            console.log(e);
        }

    });
    return (
        <>
            <Keyboard />
            <canvas id="mycanvas"></canvas>
        </>
    );

}
