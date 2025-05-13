import React from 'react';
import PropTypes from 'prop-types';
import Keyboard from 'react-simple-keyboard';
import 'react-simple-keyboard/build/css/index.css';
import './Keyboard.css';

export default function CustomKeyboard({ onChange }) {
    const [layoutName, setLayoutName] = React.useState('default');
    return (
        <Keyboard
            layoutName={layoutName}
            onChange={onChange}
            onKeyPress={button => {
                if (button === '{alt}') {
                    setLayoutName('alt');
                }
                if (button === '{default}') {
                    setLayoutName('default');
                }
            }}
            disableButtonHold
            theme="hg-theme-default hg-theme-ios"
            layout={{
                'default': [
                    'q w e r t y u i o p',
                    'a s d f g h j k l',
                    '{blank} z x c v b n m {bksp}',
                    '- {space} \''
                ],
                'alt': [
                    '1 2 3 4 5 6 7 8 9 0',
                    '@ # £ _ & - + ( ) /',
                    '* " \' : ; ! ? {bksp}',
                    '{default} , {space} . {default}'
                ],
            }}
            display={{
                '{blank}': ' ',
                '{alt}': '.?123',
                '{smileys}': '\uD83D\uDE03',
                '{shift}': '⇧',
                '{shiftactivated}': '⇧',
                '{enter}': 'enter',
                '{bksp}': '⌫',
                '{altright}': '.?123',
                '{downkeyboard}': '🞃',
                '{space}': 'space',
                '{default}': 'ABC',
                '{back}': '⇦'
            }}
        />
    );
}

CustomKeyboard.propTypes = {
    onChange: PropTypes.func
};
