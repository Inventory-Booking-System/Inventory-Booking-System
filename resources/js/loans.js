import React, { useState, useCallback, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Form from 'react-bootstrap/Form';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import { DateTimePicker } from 'react-tempusdominus-bootstrap';
import moment from 'moment';
import UserSelect from './components/UserSelect';
import AssetSelect from './components/AssetSelect';
import ShoppingCart from './components/ShoppingCart';
import FormLabel from './components/FormLabel';
import { assets as assetsApi, loans } from './api';
import * as livewire from './utils/livewire';
import ValidationError from './errors/ValidationError';
import 'tempusdominus-bootstrap/src/sass/tempusdominus-bootstrap-build.scss';

const radios = [
    { name: 'Reservation', value: 'true' },
    { name: 'Booked', value: 'false' }
];

function validateStartDate(startDate) {
    if (!startDate) {
        throw new ValidationError('Start Date is required');
    }
}

function validateEndDate(startDate, endDate) {
    if (!endDate) {
        throw new ValidationError('End Date is required');
    }
    if (moment(endDate).isSame(startDate, 'minute')) {
        throw new ValidationError('End Date cannot be the same as Start Date');
    }
    if (moment(endDate).isBefore(startDate, 'minute')) {
        throw new ValidationError('End Date cannot be before Start Date');
    }
}

function validateUser(user) {
    if (!user) {
        throw new ValidationError('User is required');
    }
}

function validateShoppingCart(shoppingCart) {
    if (!shoppingCart?.length) {
        throw new ValidationError('Equipment is required');
    }
}

function validateReservation(reservation) {
    if (reservation !== 'true' && reservation !== 'false') {
        throw new ValidationError('Booking type is required');
    }
}

function App() {
    const [open, setOpen] = useState(false);
    const [modalAction, setModalAction] = useState();
    const [assets, setAssets] = useState([]);
    const [userEditedEndDate, setUserEditedEndDate] = useState(false);
    const [startDateHidden, setStartDateHidden] = useState(false);

    const [id, setId] = useState();
    const [startDate, setStartDate] = useState(moment());
    const [endDate, setEndDate] = useState();
    const [user, setUser] = useState();
    const [details, setDetails] = useState();
    const [reservation, setReservation] = useState('false');
    const [shoppingCart, setShoppingCart] = useState(null);

    const [startDateHelperText, setStartDateHelperText] = useState('');
    const [endDateHelperText, setEndDateHelperText] = useState('');
    const [userHelperText, setUserHelperText] = useState('');
    const [reservationHelperText, setReservationHelperText] = useState('');
    const [assetsHelperText, setAssetsHelperText] = useState('');
    const [formHelperText, setFormHelperText] = useState('');

    const [assetsLoading, setAssetsLoading] = useState(false);
    const [submitLoading, setSubmitLoading] = useState(false);

    const clearHelperText = useCallback((field) => {
        if (!field || field === 'startDate') {
            setStartDateHelperText('');
        }
        if (!field || (field === 'startDate' && userEditedEndDate) || field === 'endDate') {
            setEndDateHelperText('');
        }
        if (!field || field === 'user') {
            setUserHelperText('');
        }
        if (!field || field === 'shoppingCart') {
            setAssetsHelperText('');
        }
        if (!field || field === 'reservation') {
            setReservationHelperText('');
        }
        if (!field || field === 'form') {
            setFormHelperText('');
        }
    }, [userEditedEndDate]);
    useEffect(() => clearHelperText(), [clearHelperText, open]);

    const handleCreateOpen = useCallback(() => {
        clearHelperText();

        setStartDate(moment());
        setEndDate();
        setUserEditedEndDate(false);
        setStartDateHidden(false);
        setUser();
        setDetails();
        setReservation('false');
        setShoppingCart(null);

        setModalAction('Create');
        setOpen(true);
    }, [clearHelperText]);

    const handleEditOpen = useCallback((e) => {
        const loan = JSON.parse(e.target.dataset.loan);

        clearHelperText();

        setId(loan.id);
        setStartDate(moment(loan.start_date_time, 'DD MMM YYYY HH:mm'));
        setEndDate(moment(loan.end_date_time, 'DD MMM YYYY HH:mm'));
        setUserEditedEndDate(true);
        setStartDateHidden(false);
        setUser({ value: loan.user_id, label: loan.user.forename+' '+loan.user.surname });
        setDetails(loan.details);
        setReservation(loan.status_id === 1 ? 'true' : 'false');
        setShoppingCart(loan.assets.map(asset => ({ ...asset, returned: !!asset.pivot.returned })));

        setModalAction('Edit');
        setOpen(true);
    }, [clearHelperText]);

    const handleClose = useCallback(() => {
        setOpen(false);

        setStartDate(moment());
        setEndDate();
        setUserEditedEndDate(false);
        setStartDateHidden(false);
        setUser();
        setDetails();
        setReservation('false');
        setShoppingCart(null);
    }, []);

    const handleStartDateChange = useCallback(e => setStartDate(e.date), []);
    useEffect(() => { validate('startDate'); }, [validate, startDate]);

    /**
     * If the start date has been modified, but not the end date, set the end
     * date equal to the start date
     */
    const handleStartDateHide = useCallback(() => setStartDateHidden(true), []);
    useEffect(() => {
        if (startDateHidden && !userEditedEndDate) {
            setEndDate(startDate);
        }
    }, [startDateHidden, userEditedEndDate, startDate]);

    const handleEndDateChange = useCallback(e => {
        setEndDate(e.date);
        setUserEditedEndDate(true);
    }, []);
    useEffect(() => { validate('endDate'); }, [validate, endDate]);

    const handleUserChange = useCallback(e => setUser(e), []);
    useEffect(() => { validate('user'); }, [validate, user]);

    const handleDetailsChange = useCallback(e => setDetails(e.target.value), []);

    const handleReservationChange = useCallback(e => {
        e.preventDefault();
        setReservation(e.currentTarget.value);
    }, []);
    useEffect(() => { validate('reservation'); }, [validate, reservation]);

    const handleAssetChange = useCallback(e => {
        setShoppingCart(shoppingCart ? [...shoppingCart, assets.find(x => x.id === e.value)] : [assets.find(x => x.id === e.value)]);
    }, [shoppingCart, assets]);
    const onShoppingCartChange = useCallback(assets => setShoppingCart(assets), []);
    useEffect(() => { validate('shoppingCart'); }, [validate, shoppingCart]);

    const validate = useCallback((field) => {
        clearHelperText(field);

        let success = true;

        if (!field || field === 'startDate') {
            try {
                validateStartDate(startDate);
            } catch(e) {
                success = false;
                setStartDateHelperText(e.message);
            }
        }

        if (!field || (field === 'startDate' && userEditedEndDate) || field === 'endDate') {
            try {
                validateEndDate(startDate, endDate);
            } catch(e) {
                success = false;
                setEndDateHelperText(e.message);
            }
        }

        if (!field || field === 'user') {
            try {
                validateUser(user);
            } catch(e) {
                success = false;
                setUserHelperText(e.message);
            }
        }

        if (!field || field === 'shoppingCart') {
            try {
                validateShoppingCart(shoppingCart);
            } catch(e) {
                success = false;
                setAssetsHelperText(e.message);
            }
        }

        if (!field || field === 'reservation') {
            try {
                validateReservation(reservation);
            } catch(e) {
                success = false;
                setReservationHelperText(e.message);
            }
        }

        return success;
    }, [clearHelperText, startDate, endDate, userEditedEndDate, user, shoppingCart, reservation]);

    const handleCreate = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await loans.create({
                startDateTime: startDate.unix(),
                endDateTime: endDate.unix(),
                user: user.value,
                assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                details,
                reservation: reservation === 'true'
            });
            await livewire.render();
            setSubmitLoading(false);

            if (resp.ok) {
                handleClose();
                return;
            }
            setFormHelperText('An unknown error has occurred. Please try again later.');
            return;
        } catch(e) {
            console.error(e);
            setSubmitLoading(false);
        }
        setFormHelperText('An connection error has occurred. Please try again later.');
    }, [details, endDate, handleClose, reservation, shoppingCart, startDate, user, validate]);

    const handleEdit = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await loans.update(id, {
                startDateTime: startDate.unix(),
                endDateTime: endDate.unix(),
                user: user.value,
                assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                details,
                reservation: reservation === 'true'
            });
            await livewire.render();
            setSubmitLoading(false);

            if (resp.ok) {
                handleClose();
                return;
            }
            setFormHelperText('An unknown error has occurred. Please try again later.');
            return;
        } catch(e) {
            console.error(e);
            setSubmitLoading(false);
        }
        setFormHelperText('An connection error has occurred. Please try again later.');
    }, [details, endDate, handleClose, id, reservation, shoppingCart, startDate, user, validate]);

    /**
     * Load assets when modal is opened, and when start/end dates are changed
     */
    useEffect(() => {
        async function getAssets() {
            if (moment(endDate).isSameOrBefore(startDate, 'minute')) {
                return;
            }

            setAssetsLoading(true);
            const body = await assetsApi.getAll({
                startDateTime: moment(startDate).unix(),
                /**
                 * If end date isn't set, use a time in the future so assets
                 * list can be preloaded
                 */
                endDateTime: endDate ? moment(endDate).unix() : moment().add(1, 'day').unix()
            });

            setAssets(body.map(asset => {
                return {...asset, value: asset.id, label: asset.name+' ('+asset.tag+')', isDisabled: !asset.available};
            }));
            setAssetsLoading(false);
        }
        if (open) {
            getAssets();
        }
    }, [open, startDate, endDate]);

    useEffect(() => {
        document.querySelector('#create').addEventListener('click', handleCreateOpen);
        return () => document.querySelector('#create').removeEventListener('click', handleCreateOpen);
    }, [handleCreateOpen]);

    useEffect(() => {
        function addClickHandlers() {
            const editBtns = document.querySelectorAll('.edit-button');
            editBtns.forEach(btn => btn.addEventListener('click', handleEditOpen));
        }
        function removeClickHandlers() {
            const editBtns = document.querySelectorAll('.edit-button');
            editBtns.forEach(btn => btn.removeEventListener('click', handleEditOpen));
        }

        addClickHandlers();
        window.addEventListener('render', addClickHandlers);

        return () => {
            removeClickHandlers();
            window.removeEventListener('render', addClickHandlers);
        };
    }, [handleEditOpen]);

    return (
        <Modal show={open} onHide={handleClose} size="xl">
            <Modal.Header closeButton>
                <Modal.Title>
                    {modalAction} Loan
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Row>
                    <Col md={6}>
                        <Form>
                            <Form.Group>
                                <FormLabel
                                    helperText={startDateHelperText}
                                >
                                    Start Date
                                </FormLabel>
                                <DateTimePicker
                                    collapse={false}
                                    onChange={handleStartDateChange}
                                    onHide={handleStartDateHide}
                                    date={startDate}
                                    locale="en-gb"
                                    sideBySide
                                    readOnly={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={endDateHelperText}
                                >
                                    End Date
                                </FormLabel>
                                <DateTimePicker
                                    collapse={false}
                                    onChange={handleEndDateChange}
                                    date={endDate}
                                    locale="en-gb"
                                    sideBySide
                                    readOnly={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={userHelperText}
                                >
                                    User
                                </FormLabel>
                                <UserSelect
                                    onChange={handleUserChange}
                                    disabled={submitLoading}
                                    defaultValue={user}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={assetsHelperText}
                                >
                                    Equipment
                                </FormLabel>
                                <AssetSelect
                                    assets={assets}
                                    shoppingCart={shoppingCart}
                                    onChange={handleAssetChange}
                                    isLoading={assetsLoading}
                                    disabled={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <FormLabel>
                                    Details
                                </FormLabel>
                                <Form.Control
                                    as="textarea"
                                    rows={4}
                                    value={details  || ''}
                                    onChange={handleDetailsChange}
                                    disabled={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={reservationHelperText}
                                >
                                    Booking Type
                                </FormLabel>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <ButtonGroup>
                                    {radios.map((radio, idx) => (
                                        <Button
                                            key={idx}
                                            variant={radio.value === 'true' ? 'warning' : 'success'}
                                            value={radio.value}
                                            className={reservation === radio.value ? 'btn-active' : ''}
                                            onClick={handleReservationChange}
                                        >
                                            {radio.name}
                                        </Button>
                                    ))}
                                </ButtonGroup>
                            </Form.Group>
                        </Form>
                    </Col>
                    <Col md={6}>
                        <ShoppingCart
                            action={modalAction}
                            assets={shoppingCart}
                            onChange={onShoppingCartChange}
                        />
                    </Col>
                </Row>
            </Modal.Body>
            <Modal.Footer>
                <Form.Text className="text-danger">
                    {formHelperText}
                </Form.Text>
                <Button
                    variant="secondary"
                    onClick={handleClose}
                    disabled={submitLoading}
                >
                    Cancel
                </Button>
                <Button
                    variant="primary"
                    onClick={modalAction === 'Create' ? handleCreate : handleEdit}
                    disabled={submitLoading}
                >
                    Save
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

const root = createRoot(document.getElementById('create-edit-modal'));
root.render(<App />);