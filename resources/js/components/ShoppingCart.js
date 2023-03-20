// eslint-disable-next-line no-unused-vars
import { h } from 'preact';
import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import Card from 'react-bootstrap/Card';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Button from 'react-bootstrap/Button';

function ItemCard({ index, name, tag, returned, onRemove, onReturn, action }) {

    const remove = useCallback(() => onRemove(index), [index, onRemove]);
    const bookIn = useCallback(() => onReturn(index), [index, onReturn]);

    return (
        <Card className={returned ? 'bg-success' : ''}>
            <Card.Body>
                <Row>
                    <Col>{name}</Col>
                    <Col sm={2}>{tag}</Col>
                    <Col sm={3}>
                        {action !== 'Create' && <Button
                            variant="light"
                            onClick={bookIn}
                        >
                            <i className="fa-sharp fa-solid fa-circle-check"></i>
                        </Button>}
                        <Button
                            variant="light"
                            style="color: #cecece;"
                            onClick={remove}
                        >
                            <i className="fas fa-trash-alt"></i>
                        </Button>
                    </Col>
                </Row>
            </Card.Body>
        </Card>
    );
}

ItemCard.propTypes = {
    index: PropTypes.string,
    name: PropTypes.string,
    quantity: PropTypes.number,
    tag: PropTypes.string,
    returned: PropTypes.bool,
    cost: PropTypes.number,
    onRemove: PropTypes.func,
    onReturn: PropTypes.func,
    action: PropTypes.string
};

export default function ShoppingCart({ action, assets, onChange }) {

    const onRemove = useCallback(index => {
        let updatedShoppingCart = [...assets];
        updatedShoppingCart.splice(index, 1);
        onChange(updatedShoppingCart);
    }, [assets, onChange]);

    const onReturn = useCallback(index => {
        let updatedShoppingCart = [...assets];
        updatedShoppingCart[index].returned = !updatedShoppingCart[index].returned;
        onChange(updatedShoppingCart);
    }, [assets, onChange]);

    return (
        <Card>
            <Card.Body>
                {assets.map((asset, index) =>
                    <ItemCard
                        key={index}
                        index={index}
                        {...asset}
                        onRemove={onRemove}
                        onReturn={onReturn}
                        action={action}
                    />
                )}
            </Card.Body>
        </Card>
    );
}

ShoppingCart.propTypes = {
    action: PropTypes.string,
    assets: PropTypes.array,
    onChange: PropTypes.func
};
