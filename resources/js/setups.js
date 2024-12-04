import React, { useState, useCallback, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Form from 'react-bootstrap/Form';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { DateTimePicker } from 'react-tempusdominus-bootstrap';
import moment from 'moment';
import UserSelect from './components/UserSelect';
import LocationSelect from './components/LocationSelect';
import AssetSelect from './components/AssetSelect';
import ShoppingCart from './components/ShoppingCart';
import FormLabel from './components/FormLabel';
import { assets as assetsApi, setups } from './api';
import * as livewire from './utils/livewire';
import ValidationError from './errors/ValidationError';
import 'tempusdominus-bootstrap/src/sass/tempusdominus-bootstrap-build.scss';

function validateTitle(title) {
    if (!title) {
        throw new ValidationError('Title is required');
    }
}

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

function validateLocation(location) {
    if (!location) {
        throw new ValidationError('Location is required');
    }
}

function App() {
    const [open, setOpen] = useState(false);
    const [modalAction, setModalAction] = useState();
    const [assets, setAssets] = useState([]);
    const [userEditedEndDate, setUserEditedEndDate] = useState(false);
    const [startDateHidden, setStartDateHidden] = useState(false);

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
    const [formHelperText, setFormHelperText] = useState('');

    const [assetsLoading, setAssetsLoading] = useState(true);
    const [submitLoading, setSubmitLoading] = useState(false);

    const clearHelperText = useCallback((field) => {
        if (!field || field === 'title') {
            setTitleHelperText('');
        }
        if (!field || field === 'startDate') {
            setStartDateHelperText('');
        }
        if (!field || (field === 'startDate' && userEditedEndDate) || field === 'endDate') {
            setEndDateHelperText('');
        }
        if (!field || field === 'user') {
            setUserHelperText('');
        }
        if (!field || field === 'location') {
            setLocationHelperText('');
        }
        if (!field || field === 'form') {
            setFormHelperText('');
        }
    }, [userEditedEndDate]);
    useEffect(() => clearHelperText(), [clearHelperText, open]);

    const handleCreateOpen = useCallback(() => {
        clearHelperText();

        setTitle('');
        setStartDate(moment());
        setEndDate();
        setUserEditedEndDate(false);
        setStartDateHidden(false);
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
        setUserEditedEndDate(true);
        setStartDateHidden(false);
        setUser({ value: setup.loan.user_id, label: setup.loan.user.forename+' '+setup.loan.user.surname });
        setLocation({ value: setup.location.id, label: setup.location.name });
        setDetails(setup.loan.details);
        setShoppingCart([
            ...setup.loan.assets.map(asset => ({ ...asset, returned: !!asset.pivot.returned, type: 'assets' })),
            ...setup.loan.asset_groups.map(group => ({ ...group, quantity: group.pivot.quantity, type: 'group' }))
        ]);

        setModalAction('Edit');
        setOpen(true);
    }, [clearHelperText]);

    const handleClose = useCallback(() => {
        setOpen(false);

        setTitle('');
        setStartDate(moment());
        setEndDate();
        setUserEditedEndDate(false);
        setStartDateHidden(false);
        setUser();
        setLocation();
        setDetails();
        setShoppingCart(null);
    }, []);

    const handleTitleChange = useCallback(e => setTitle(e.target.value), []);
    useEffect(() => { validate('title'); }, [validate, title]);

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

    const handleLocationChange = useCallback(e => setLocation(e), []);
    useEffect(() => { validate('location'); }, [validate, location]);

    const handleDetailsChange = useCallback(e => setDetails(e.target.value), []);

    const handleAssetChange = useCallback(e => {
        let item;
        if (e.type === 'group') {
            item = assets[0].options.find(x => x.id === e.value);

            if (shoppingCart && shoppingCart.find(x => x.id === e.value)) {
                item = shoppingCart.find(x => x.id === e.value);
                if (item.quantity === item.available_assets_count) return;
                item.quantity++;
                setShoppingCart([...shoppingCart]);
                return;
            }

            item.quantity = 1;
        } else {
            item = assets[1].options.find(x => x.id === e.value);
        }
        setShoppingCart(shoppingCart ? [...shoppingCart, item] : [item]);
    }, [shoppingCart, assets]);

    const onShoppingCartChange = useCallback(assets => setShoppingCart(assets), []);
    useEffect(() => { validate('shoppingCart'); }, [validate, shoppingCart]);

    const validate = useCallback((field) => {
        clearHelperText(field);

        let success = true;

        if (!field || field === 'title') {
            try {
                validateTitle(title);
            } catch(e) {
                success = false;
                setTitleHelperText(e.message);
            }
        }

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

        if (!field || field === 'location') {
            try {
                validateLocation(location);
            } catch(e) {
                success = false;
                setLocationHelperText(e.message);
            }
        }

        return success;
    }, [clearHelperText, endDate, location, startDate, title, user, userEditedEndDate]);

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

            const groups = body.groups.map(group => {
                return {
                    ...group,
                    type: 'group',
                    value: group.id,
                    label: `${group.name} (${group.available_assets_count} available)`,
                    isDisabled: group.available_assets_count === 0,
                    available: group.available_assets_count > 0
                };
            });

            const assets = body.assets.map(asset => {
                return {
                    ...asset,
                    type: 'assets',
                    value: asset.id,
                    label: `${asset.name} (${asset.tag})`,
                    isDisabled: !asset.available
                };
            });

            setAssets([
                {
                    label: 'Groups',
                    options: []
                }, {
                    label: 'Assets',
                    options: assets
                }
            ]);
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
                                    onHide={handleStartDateHide}
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
                                <LocationSelect
                                    onChange={handleLocationChange}
                                    isDisabled={submitLoading}
                                    defaultValue={location}
                                    isClearable
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel>
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
                            showQuantity
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