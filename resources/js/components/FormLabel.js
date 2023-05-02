import React from 'react';
import PropTypes from 'prop-types';
import Form from 'react-bootstrap/Form';
import Stack from './Stack';
import '../../css/components/FormLabel.css';

export default function FormLabel({ children, helperText }) {
    return (
        <Stack direction="horizontal" gap={3} className="form-label-stack">
            <Form.Label>
                {children}
            </Form.Label>
            <Form.Text className="text-danger">
                {helperText}
            </Form.Text>
        </Stack>
    );
}

FormLabel.propTypes = {
    children: PropTypes.node,
    helperText: PropTypes.string
};
