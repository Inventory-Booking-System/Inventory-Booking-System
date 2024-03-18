import * as React from 'react';
import PropTypes from 'prop-types';
import Button from '@mui/material/Button';
import ButtonGroup from '@mui/material/ButtonGroup';

export default function BasicButtonGroup({ buttons, click }) {

    return (
        <ButtonGroup
            variant="contained"
            size="large"
        >
            {buttons.map(button => <Button key={button.value} onClick={() => click(button.value)}>{button.label}</Button>)}
        </ButtonGroup>
    );
}

BasicButtonGroup.propTypes = {
    buttons: PropTypes.array,
    click: PropTypes.func
};
