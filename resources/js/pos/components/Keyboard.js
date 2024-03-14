import React from 'react';
import Keyboard from 'react-simple-keyboard';
import 'react-simple-keyboard/build/css/index.css';
import './Keyboard.css';

export default function CustomKeyboard() {
    return (
        <Keyboard
            onChange={console.log}
            onKeyPress={console.log}
            disableButtonHold
            // physicalKeyboardHighlight
            // physicalKeyboardHighlightPress
            theme="hg-theme-default hg-theme-ios"
            layout={{
                'default': [
                    'q w e r t y u i o p {bksp}',
                    'a s d f g h j k l {enter}',
                    '{shift} z x c v b n m , . {shift}',
                    '{alt} {smileys} {space} {altright} {downkeyboard}'
                ]
            }}
            display={{
                '{alt}': '.?123',
                '{smileys}': '\uD83D\uDE03',
                '{shift}': '⇧',
                '{shiftactivated}': '⇧',
                '{enter}': 'return',
                '{bksp}': '⌫',
                '{altright}': '.?123',
                '{downkeyboard}': '🞃',
                '{space}': ' ',
                '{default}': 'ABC',
                '{back}': '⇦'
            }}
        />
    );
}
