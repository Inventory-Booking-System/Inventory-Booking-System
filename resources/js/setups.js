import React, { useState, useCallback, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Form from 'react-bootstrap/Form';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { DateTimePicker } from 'react-tempusdominus-bootstrap';
import Select from 'react-select';
import moment from 'moment';
import UserSelect from './components/UserSelect';
import AssetSelect from './components/AssetSelect';
import ShoppingCart from './components/ShoppingCart';
import FormLabel from './components/FormLabel';
import {
    assets as assetsApi,
    locations as locationsApi,
    setups,
    users as usersApi
} from './api';
import * as livewire from './utils/livewire';
import 'tempusdominus-bootstrap/src/sass/tempusdominus-bootstrap-build.scss';

function App() {
    const [open, setOpen] = useState(false);
    const [modalAction, setModalAction] = useState();
    const [users, setUsers] = useState([]);
    const [locations, setLocations] = useState([]);
    const [assets, setAssets] = useState([]);

    const [id, setId] = useState();
    const [title, setTitle] = useState('');
    const [startDate, setStartDate] = useState(moment());
    const [endDate, setEndDate] = useState();
    const [user, setUser] = useState();
    const [location, setLocation] = useState();
    const [details, setDetails] = useState();
    const [shoppingCart, setShoppingCart] = useState([]);

    const [titleHelperText, setTitleHelperText] = useState('');
    const [startDateHelperText, setStartDateHelperText] = useState('');
    const [endDateHelperText, setEndDateHelperText] = useState('');
    const [userHelperText, setUserHelperText] = useState('');
    const [locationHelperText, setLocationHelperText] = useState('');
    const [assetsHelperText, setAssetsHelperText] = useState('');
    const [formHelperText, setFormHelperText] = useState('');

    const [usersLoading, setUsersLoading] = useState(true);
    const [locationsLoading, setLocationsLoading] = useState(true);
    const [assetsLoading, setAssetsLoading] = useState(true);
    const [submitLoading, setSubmitLoading] = useState(false);

    const handleCreateOpen = useCallback(() => {
        clearHelperText();

        setTitle();
        setStartDate(moment());
        setEndDate();
        setUser();
        setLocation();
        setDetails();
        setShoppingCart([]);

        setModalAction('Create');
        setOpen(true);
    }, [clearHelperText]);

    const handleEditOpen = useCallback((e) => {
        const setup = JSON.parse(e.target.dataset.setup);

        clearHelperText();

        setId(setup.id);
        setTitle(setup.title);
        setStartDate(moment(setup.loan.start_date_time, 'DD MMM YYYY HH:mm'));
        setEndDate(moment(setup.loan.end_date_time, 'DD MMM YYYY HH:mm'));
        setUser({ value: setup.loan.user_id, label: setup.loan.user.forename+' '+setup.loan.user.surname });
        setLocation({ value: setup.location.id, label: setup.location.name });
        setDetails(setup.loan.details);
        setShoppingCart(setup.loan.assets.map(asset => ({ ...asset, returned: !!asset.pivot.returned })));

        setModalAction('Edit');
        setOpen(true);
    }, [clearHelperText]);

    const handleClose = useCallback(() => setOpen(false), []);

    const handleTitleChange = useCallback(e => setTitle(e.target.value), []);
    const handleStartDateChange = useCallback(e => setStartDate(e.date), []);
    const handleEndDateChange = useCallback(e => setEndDate(e.date), []);
    const handleUserChange = useCallback(e => setUser(e), []);
    const handleLocationChange = useCallback(e => setLocation(e), []);
    const handleDetailsChange = useCallback(e => setDetails(e.target.value), []);

    const handleAssetChange = useCallback(e => {
        setShoppingCart([...shoppingCart, assets.find(x => x.id === e.value)]);
    }, [shoppingCart, assets]);

    const onShoppingCartChange = useCallback(assets => setShoppingCart(assets), []);

    const clearHelperText = useCallback(() => {
        setTitleHelperText('');
        setStartDateHelperText('');
        setEndDateHelperText('');
        setUserHelperText('');
        setLocationHelperText('');
        setAssetsHelperText('');
        setFormHelperText('');
    }, []);

    const validate = useCallback(() => {
        clearHelperText();

        let success = true;

        if (!title) {
            success = false;
            setTitleHelperText('Title is required');
        }

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

        if (!location) {
            success = false;
            setLocationHelperText('Location is required');
        }

        return success;
    }, [clearHelperText, title, startDate, endDate, user, location]);

    const handleCreate = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await setups.create({
                title,
                startDateTime: startDate.unix(),
                endDateTime: endDate.unix(),
                user: user.value,
                location: location.value,
                assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                details
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
        setFormHelperText('A connection error has occurred. Please try again later.');
    }, [details, endDate, handleClose, location, shoppingCart, startDate, title, user, validate]);

    const handleEdit = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await setups.update(id, {
                title,
                startDateTime: startDate.unix(),
                endDateTime: endDate.unix(),
                user: user.value,
                location: location.value,
                assets: shoppingCart.map(asset => ({ id: asset.id, returned: !!asset.returned })),
                details
            });
            await livewire.render();
            setSubmitLoading(false);

            if (resp.ok) {
                handleClose();
                return;
            }
            if (resp.status === 400) {
                console.log('invalid');
            }
            setFormHelperText('An unknown error has occurred. Please try again later.');
            return;
        } catch(e) {
            console.error(e);
            setSubmitLoading(false);
        }
        setFormHelperText('A connection error has occurred. Please try again later.');
    }, [details, endDate, handleClose, id, location, shoppingCart, startDate, title, user, validate]);

    /**
     * Load users when modal is opened
     */
    useEffect(() => {
        async function getUsers() {
            setUsersLoading(true);
            const body = await usersApi.getAll();

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
     * Load locations when modal is opened
     */
    useEffect(() => {
        async function getLocations() {
            setLocationsLoading(true);
            const body = await locationsApi.getAll();

            setLocations(body.map(location => ({...location, value: location.id, label: location.name})));
            setLocationsLoading(false);
        }
        if (open) {
            getLocations();
        }
    }, [open]);

    /**
     * Load assets when modal is opened, and when start/end dates are changed
     */
    useEffect(() => {
        async function getAssets() {
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
                    {modalAction} Setup
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Row>
                    <Col md={6}>
                        <Form>
                            <Form.Group>
                                <FormLabel
                                    helperText={titleHelperText}
                                >
                                    Title
                                </FormLabel>
                                <Form.Control
                                    type="text"
                                    value={title}
                                    onChange={handleTitleChange}
                                    disabled={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={startDateHelperText}
                                >
                                    Start Date
                                </FormLabel>
                                <DateTimePicker
                                    collapse={false}
                                    onChange={handleStartDateChange}
                                    defaultDate={startDate}
                                    locale="en-gb"
                                    sideBySide
                                    validate
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
                                    defaultDate={endDate}
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
                                    users={users}
                                    isLoading={usersLoading}
                                    onChange={handleUserChange}
                                    disabled={submitLoading}
                                    defaultValue={user}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={locationHelperText}
                                >
                                    Location
                                </FormLabel>
                                <Select
                                    options={locations}
                                    onChange={handleLocationChange}
                                    isLoading={locationsLoading}
                                    isDisabled={submitLoading}
                                    defaultValue={location}
                                    isClearable
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
                                    value={details}
                                    onChange={handleDetailsChange}
                                    disabled={submitLoading}
                                />
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

const root = createRoot(document.getElementById('create-edit-modal'));
root.render(<App />);