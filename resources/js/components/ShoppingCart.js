// eslint-disable-next-line no-unused-vars
import { h } from 'preact';
import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import Card from 'react-bootstrap/Card';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Button from 'react-bootstrap/Button';

function ItemCard({ index, name, tag, onRemove }) {

    const remove = useCallback(() => onRemove(index), [index, onRemove]);

    return (
        <Card>
            <Card.Body>
                <Row>
                    <Col>{name}</Col>
                    <Col sm={2}>{tag}</Col>
                    <Col sm={1}>
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
    cost: PropTypes.number,
    onRemove: PropTypes.func
};

export default function ShoppingCart({ assets, onRemove }) {
    return (
        <Card>
            <Card.Body>
                {assets.map((asset, index) => <ItemCard key={index} index={index} {...asset} onRemove={onRemove} />)}
            </Card.Body>
        </Card>
    );
}

ShoppingCart.propTypes = {
    assets: PropTypes.array,
    onRemove: PropTypes.func
};
