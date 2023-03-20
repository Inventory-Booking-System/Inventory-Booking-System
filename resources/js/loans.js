// eslint-disable-next-line no-unused-vars
import { h, render } from 'preact';
import React, { useState, useCallback, useEffect } from 'react';
import { html } from 'htm/preact';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Form from 'react-bootstrap/Form';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import ToggleButton from 'react-bootstrap/ToggleButton';
import { DateTimePicker } from 'react-tempusdominus-bootstrap';
import moment from 'moment';
import UserSelect from './components/UserSelect';
import AssetSelect from './components/AssetSelect';
import ShoppingCart from './components/ShoppingCart';
import { getCookie } from './utils/cookie';
import 'tempusdominus-bootstrap/src/sass/tempusdominus-bootstrap-build.scss';

const radios = [
    { name: 'Reservation', value: 'true' },
    { name: 'Booked', value: 'false' }
];

function App() {
    const [open, setOpen] = useState(false);
    const [modalAction, setModalAction] = useState();
    const [users, setUsers] = useState([]);
    const [assets, setAssets] = useState([]);

    const [id, setId] = useState();
    const [startDate, setStartDate] = useState(moment());
    const [endDate, setEndDate] = useState();
    const [user, setUser] = useState();
    const [details, setDetails] = useState();
    const [reservation, setReservation] = useState('false');
    const [shoppingCart, setShoppingCart] = useState([]);

    const [startDateHelperText, setStartDateHelperText] = useState('');
    const [endDateHelperText, setEndDateHelperText] = useState('');
    const [userHelperText, setUserHelperText] = useState('');
    const [reservationHelperText, setReservationHelperText] = useState('');
    const [assetsHelperText, setAssetsHelperText] = useState('');

    const [usersLoading, setUsersLoading] = useState(true);
    const [assetsLoading, setAssetsLoading] = useState(true);
    const [submitLoading, setSubmitLoading] = useState(false);

    const handleCreateOpen = useCallback(() => {
        setStartDate(moment());
        setEndDate();
        setUser();
        setDetails();
        setReservation('false');
        setShoppingCart([]);

        setModalAction('Create');
        setOpen(true);
    }, []);

    const handleEditOpen = useCallback((e) => {
        const loan = JSON.parse(e.target.dataset.loan);

        setId(loan.id);
        setStartDate(moment(loan.start_date_time, 'DD MMM YYYY HH:mm'));
        setEndDate(moment(loan.end_date_time, 'DD MMM YYYY HH:mm'));
        setUser({ value: loan.user_id, label: loan.user.forename+' '+loan.user.surname });
        setDetails(loan.details);
        setReservation();
        setShoppingCart(loan.assets);

        setModalAction('Edit');
        setOpen(true);
    }, []);

    const handleClose = useCallback(() => setOpen(false), []);

    const handleStartDateChange = useCallback(e => setStartDate(e.date), []);
    const handleEndDateChange = useCallback(e => setEndDate(e.date), []);
    const handleUserChange = useCallback(e => setUser(e), []);
    const handleDetailsChange = useCallback(e => setDetails(e.target.value), []);
    const handleReservationChange = useCallback(e => setReservation(e.currentTarget.value), []);

    const handleAssetChange = useCallback(e => {
        setShoppingCart([...shoppingCart, assets.find(x => x.id === e.value)]);
    }, [shoppingCart, assets]);

    const onShoppingCartChange = useCallback(assets => setShoppingCart(assets), []);

    const validate = useCallback(() => {
        setStartDateHelperText('');
        setEndDateHelperText('');
        setUserHelperText('');
        setReservationHelperText('');
        setAssetsHelperText('');

        let success = true;

        if (!startDate) {
            success = false;
            setStartDateHelperText('Start Date is required');
        }

        if (!endDate) {
            success = false;
            setEndDateHelperText('End Date is required');
        }
        if (moment(endDate).isSame(startDate, 'minute')) {
            success = false;
            setEndDateHelperText('End Date cannot be the same as Start Date');
        }
        if (moment(endDate).isBefore(startDate, 'minute')) {
            success = false;
            console.log(endDate);
            setEndDateHelperText('End Date cannot be before Start Date');
        }

        if (!user) {
            success = false;
            setUserHelperText('User is required');
        }

        if (reservation !== 'true' && reservation !== 'false') {
            success = false;
            setReservationHelperText('Booking type is required');
        }

        if (!shoppingCart.length) {
            success = false;
            setAssetsHelperText('Equipment is required');
        }

        return success;
    }, [startDate, endDate, user, reservation, shoppingCart.length]);

    const handleCreate = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await fetch('/api/loans', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-Token': decodeURIComponent(getCookie('XSRF-TOKEN'))
                },
                body: JSON.stringify({
                    startDateTime: startDate.unix(),
                    endDateTime: endDate.unix(),
                    user: user.value,
                    assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                    details,
                    reservation: reservation === 'true'
                })
            });
            setSubmitLoading(false);

            if (resp.status === 201) {
                handleClose();
                return;
            }
            if (resp.status === 400) {
                console.log('invalid');
            }
        } catch(e) {
            setSubmitLoading(false);
        }
    }, [details, endDate, handleClose, reservation, shoppingCart, startDate, user, validate]);

    const handleEdit = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await fetch('/api/loans/'+id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-Token': decodeURIComponent(getCookie('XSRF-TOKEN'))
                },
                body: JSON.stringify({
                    startDateTime: startDate.unix(),
                    endDateTime: endDate.unix(),
                    user: user.value,
                    assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                    details,
                    reservation: reservation === 'true'
                })
            });
            setSubmitLoading(false);

            if (resp.status === 200) {
                handleClose();
                return;
            }
            if (resp.status === 400) {
                console.log('invalid');
            }
        } catch(e) {
            setSubmitLoading(false);
        }
    }, [details, endDate, handleClose, id, reservation, shoppingCart, startDate, user, validate]);

    /**
     * Load users when modal is opened
     */
    useEffect(() => {
        async function getUsers() {
            setUsersLoading(true);
            const resp = await fetch('/api/users');
            const body = await resp.json();

            setUsers(body.map(user => {
                return {...user, value: user.id, label: user.forename+' '+user.surname};
            }));
            setUsersLoading(false);
        }
        if (open) {
            getUsers();
        }
    }, [open]);

    /**
     * Load assets when modal is opened, and when start/end dates are changed
     */
    useEffect(() => {
        async function getAssets() {
            setAssetsLoading(true);
            const params = new URLSearchParams({
                startDateTime: moment(startDate).unix(),
                /**
                 * If end date isn't set, use a time in the future so assets
                 * list can be preloaded
                 */
                endDateTime: endDate ? moment(endDate).unix() : moment().add(1, 'day').unix()
            });
            const resp = await fetch('/api/assets?'+params);
            const body = await resp.json();

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
                                <Form.Label>Start Date</Form.Label>
                                <DateTimePicker
                                    collapse={false}
                                    onChange={handleStartDateChange}
                                    defaultDate={startDate}
                                    locale="en-gb"
                                    sideBySide
                                    validate
                                    readOnly={submitLoading}
                                />
                                <Form.Text className="text-danger" style="min-height: 20px">
                                    {startDateHelperText}
                                </Form.Text>
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>End Date</Form.Label>
                                <DateTimePicker
                                    collapse={false}
                                    onChange={handleEndDateChange}
                                    defaultDate={endDate}
                                    locale="en-gb"
                                    sideBySide
                                    readOnly={submitLoading}
                                />
                                <Form.Text className="text-danger" style="min-height: 20px">
                                    {endDateHelperText}
                                </Form.Text>
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>User</Form.Label>
                                <UserSelect
                                    users={users}
                                    isLoading={usersLoading}
                                    onChange={handleUserChange}
                                    disabled={submitLoading}
                                    defaultValue={user}
                                />
                                <Form.Text className="text-danger" style="min-height: 20px">
                                    {userHelperText}
                                </Form.Text>
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>Equipment</Form.Label>
                                <AssetSelect
                                    assets={assets}
                                    shoppingCart={shoppingCart}
                                    onChange={handleAssetChange}
                                    isLoading={assetsLoading}
                                    disabled={submitLoading}
                                />
                                <Form.Text className="text-danger" style="min-height: 20px">
                                    {assetsHelperText}
                                </Form.Text>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Details</Form.Label>
                                <Form.Control
                                    as="textarea"
                                    rows={3}
                                    value={details}
                                    onChange={handleDetailsChange}
                                    disabled={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <Form.Label>Booking Type</Form.Label>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <ButtonGroup>
                                    {radios.map((radio, idx) => (
                                        <ToggleButton
                                            key={idx}
                                            id={`radio-${idx}`}
                                            type="radio"
                                            variant={radio.value === 'true' ? 'warning' : 'success'}
                                            name="radio"
                                            value={radio.value}
                                            checked={reservation === radio.value}
                                            onChange={handleReservationChange}
                                        >
                                            {radio.name}
                                        </ToggleButton>
                                    ))}
                                </ButtonGroup>
                                <Form.Text className="text-danger" style="min-height: 20px">
                                    {reservationHelperText}
                                </Form.Text>
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
                <Button variant="secondary" onClick={handleClose}>
                    Cancel
                </Button>
                <Button variant="primary" onClick={modalAction === 'Create' ? handleCreate : handleEdit}>
                    Save
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

render(html`<${App} />`, document.querySelector('#create-edit-modal'));
