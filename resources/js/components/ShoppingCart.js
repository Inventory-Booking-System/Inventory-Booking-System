import React, { useCallback, useMemo } from 'react';
import PropTypes from 'prop-types';
import Card from 'react-bootstrap/Card';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import Button from 'react-bootstrap/Button';

function ItemCard({ index, name, quantity, tag, cost, returned, onRemove, onReturn, action, page, onAdd, onSubtract }) {

    const remove = useCallback(() => onRemove(index), [index, onRemove]);
    const bookIn = useCallback(() => onReturn(index), [index, onReturn]);
    const add = useCallback(() => onAdd(index), [index, onAdd]);
    const subtract = useCallback(() => onSubtract(index), [index, onSubtract]);

    return (
        <Card className={returned ? 'bg-success' : ''}>
            <Card.Body className="px-3 py-2 my-0">
                <div className="d-flex justify-content-between">
                    <div className="d-flex flex-row align-items-center">
                        <div>
                            <h5 className="mb-0">{name}</h5>
                        </div>
                    </div>

                    <div className="d-flex flex-row align-items-center">

                        {quantity && <div style={{ width: 50 }}>
                            <h5 className="fw-normal mb-0">x{quantity}</h5>
                        </div>}

                        {tag && <div style={{ width: 50 }}>
                            <h5 className="fw-normal mb-0">{tag}</h5>
                        </div>}

                        {cost && <div style={{ width: 80 }}>
                            <h5 className="mb-0">£{cost.toFixed(2)}</h5>
                        </div>}

                        <ButtonGroup>
                            {action !== 'Create' && page !== 'incidents' && <Button
                                variant={returned ? 'success' : 'light'}
                                onClick={bookIn}
                                size="sm"
                            >
                                <i
                                    className="fa-sharp fa-solid fa-circle-check"
                                    style={returned ? { color: '#fff' } : null}
                                />
                            </Button>}
                            {page !== 'incidents' && <Button
                                variant={returned ? 'success' : 'light'}
                                onClick={remove}
                                size="sm"
                            >
                                <i className="fas fa-trash-alt"></i>
                            </Button>}
                            {page === 'incidents' && <>
                                <Button
                                    variant="light"
                                    onClick={add}
                                    size="sm"
                                >
                                    <i className="fas fa-plus"></i>
                                </Button>
                                <Button
                                    variant="light"
                                    onClick={subtract}
                                    size="sm"
                                >
                                    {quantity !== 1 ? <i className="fas fa-minus"></i> : <i className="fas fa-trash-alt"></i>}
                                </Button>
                            </>}
                        </ButtonGroup>
                    </div>
                </div>
            </Card.Body>
        </Card>
    );
}

ItemCard.propTypes = {
    index: PropTypes.number,
    name: PropTypes.string,
    quantity: PropTypes.number,
    tag: PropTypes.number,
    returned: PropTypes.bool,
    cost: PropTypes.number,
    onRemove: PropTypes.func,
    onReturn: PropTypes.func,
    action: PropTypes.string,
    page: PropTypes.string,
    onAdd: PropTypes.func,
    onSubtract: PropTypes.func
};

export default function ShoppingCart({ action, assets, page, onChange }) {

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

    const onAdd = useCallback(index => {
        let updatedShoppingCart = [...assets];
        updatedShoppingCart[index].quantity++;
        onChange(updatedShoppingCart);
    }, [assets, onChange]);

    const onSubtract = useCallback(index => {
        let updatedShoppingCart = [...assets];
        updatedShoppingCart[index].quantity--;
        if (updatedShoppingCart[index].quantity <= 0) {
            return onRemove(index);
        }
        onChange(updatedShoppingCart);
    }, [assets, onChange, onRemove]);

    const totalCost = useMemo(() => {
        if (!assets) return;

        let cost = 0;
        for (var i = 0; i < assets.length; i++) {
            cost += (assets[i].cost * assets[i].quantity);
        }
        return cost;
    }, [assets]);

    return (
        <Card>
            <Card.Body>
                {totalCost > 0 && <>
                    <h5 className="mb-0 text-right text-body font-weight-bold">
                        Total Cost <span className="font-weight-normal">£{totalCost.toFixed(2)}</span>
                    </h5>
                    <hr />
                </>}
                {assets && assets.map((asset, index) =>
                    <ItemCard
                        key={index}
                        index={index}
                        {...asset}
                        onRemove={onRemove}
                        onReturn={onReturn}
                        action={action}
                        page={page}
                        onAdd={onAdd}
                        onSubtract={onSubtract}
                    />
                )}
            </Card.Body>
        </Card>
    );
}

ShoppingCart.propTypes = {
    action: PropTypes.string,
    assets: PropTypes.array,
    page: PropTypes.string,
    onChange: PropTypes.func
};
