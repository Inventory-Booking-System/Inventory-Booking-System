import * as React from 'react';
import PropTypes from 'prop-types';
import Button from '@mui/material/Button';
import ButtonGroup from '@mui/material/ButtonGroup';

export default function BasicButtonGroup({ buttons, click, color = 'primary', size = 'large', variant = 'contained' }) {

    return (
        <ButtonGroup
            variant={variant}
            size={size}
        >
            {buttons.map(button =>
                <Button
                    key={button.value}
                    color={button.color || color}
                    onClick={() => click(button.value)}
                    sx={{ textTransform: 'none' }}
                >
                    {button.label}
                </Button>
            )}
        </ButtonGroup>
    );
}

BasicButtonGroup.propTypes = {
    buttons: PropTypes.array,
    click: PropTypes.func,
    color: PropTypes.string,
    size: PropTypes.string,
    variant: PropTypes.string,
};
