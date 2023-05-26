import React, { useState, useCallback, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Form from 'react-bootstrap/Form';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { DateTimePicker } from 'react-tempusdominus-bootstrap';
import moment from 'moment';
import DistributionGroupSelect from './components/DistributionGroupSelect';
import LocationSelect from './components/LocationSelect';
import EquipmentIssueSelect from './components/EquipmentIssueSelect';
import ShoppingCart from './components/ShoppingCart';
import FormLabel from './components/FormLabel';
import { incidents, equipmentIssues as equipmentIssuesApi } from './api';
import * as livewire from './utils/livewire';
import ValidationError from './errors/ValidationError';
import 'tempusdominus-bootstrap/src/sass/tempusdominus-bootstrap-build.scss';

function validateStartDate(startDate) {
    if (!startDate) {
        throw new ValidationError('Start Date is required');
    }
}

function validateDistributionGroup(distributionGroup) {
    if (!distributionGroup) {
        throw new ValidationError('Distribution Group is required');
    }
}

function validateLocation(location) {
    if (!location) {
        throw new ValidationError('Location is required');
    }
}

function validateShoppingCart(shoppingCart) {
    if (!shoppingCart?.length) {
        throw new ValidationError('Equipment is required');
    }
}

function validateEvidence(evidence) {
    if (!evidence) {
        throw new ValidationError('Evidence is required');
    }
}

function validateDetails(details) {
    if (!details) {
        throw new ValidationError('Details is required');
    }
}

function Incidents() {
    const [open, setOpen] = useState(false);
    const [modalAction, setModalAction] = useState();
    const [equipmentIssues, setEquipmentIssues] = useState();

    const [id, setId] = useState();
    const [startDate, setStartDate] = useState(moment());
    const [distributionGroup, setDistributionGroup] = useState();
    const [location, setLocation] = useState();
    const [evidence, setEvidence] = useState('');
    const [details, setDetails] = useState('');
    const [shoppingCart, setShoppingCart] = useState(null);

    const [startDateHelperText, setStartDateHelperText] = useState('');
    const [distributionGroupHelperText, setDistributionGroupHelperText] = useState('');
    const [locationHelperText, setLocationHelperText] = useState('');
    const [equipmentIssuesHelperText, setEquipmentIssuesHelperText] = useState('');
    const [evidenceHelperText, setEvidenceHelperText] = useState('');
    const [detailsHelperText, setDetailsHelperText] = useState('');
    const [formHelperText, setFormHelperText] = useState('');

    const [equipmentIssuesLoading, setEquipmentIssuesLoading] = useState(false);
    const [submitLoading, setSubmitLoading] = useState(false);

    const clearHelperText = useCallback((field) => {
        if (!field || field === 'startDate') {
            setStartDateHelperText('');
        }
        if (!field || field === 'distributionGroup') {
            setDistributionGroupHelperText('');
        }
        if (!field || field === 'location') {
            setLocationHelperText('');
        }
        if (!field || field === 'shoppingCart') {
            setEquipmentIssuesHelperText('');
        }
        if (!field || field === 'evidence') {
            setEvidenceHelperText('');
        }
        if (!field || field === 'details') {
            setDetailsHelperText('');
        }
        if (!field || field === 'form') {
            setFormHelperText('');
        }
    }, []);
    useEffect(() => clearHelperText(), [clearHelperText, open]);

    const handleCreateOpen = useCallback(() => {
        clearHelperText();

        setStartDate(moment());
        setDistributionGroup();
        setLocation();
        setEvidence('');
        setDetails('');
        setShoppingCart(null);

        setModalAction('Create');
        setOpen(true);
    }, [clearHelperText]);

    const handleEditOpen = useCallback((e) => {
        const data = JSON.parse(e.target.dataset.loan);

        clearHelperText();

        setId(data.id);
        setStartDate(moment(data.start_date_time, 'DD MMM YYYY HH:mm'));
        setDistributionGroup({ value: data.group.id, label: data.group.name });
        setLocation({ value: data.location.id, label: data.location.name });
        setEvidence(data.evidence);
        setDetails(data.details);
        setShoppingCart(data.issues.map(item => ({ ...item, name: item.title, cost: parseFloat(item.cost), quantity: item.pivot.quantity })));

        setModalAction('Edit');
        setOpen(true);
    }, [clearHelperText]);

    const handleClose = useCallback(() => {
        setOpen(false);

        setStartDate(moment());
        setDistributionGroup();
        setLocation();
        setEvidence('');
        setDetails('');
        setShoppingCart(null);
    }, []);

    const handleStartDateChange = useCallback(e => setStartDate(e.date), []);
    useEffect(() => { validate('startDate'); }, [validate, startDate]);

    const handleDistributionGroupChange = useCallback(e => setDistributionGroup(e), []);
    useEffect(() => { validate('distributionGroup'); }, [validate, distributionGroup]);

    const handleLocationChange = useCallback(e => setLocation(e), []);
    useEffect(() => { validate('location'); }, [validate, location]);

    const handleEvidenceChange = useCallback(e => setEvidence(e.target.value), []);
    useEffect(() => { validate('evidence'); }, [validate, evidence]);

    const handleDetailsChange = useCallback(e => setDetails(e.target.value), []);
    useEffect(() => { validate('details'); }, [validate, details]);

    const handleEquipmentIssueChange = useCallback(e => {
        let newShoppingCart;
        if (shoppingCart) {
            let oldShoppingCart = JSON.parse(JSON.stringify(shoppingCart));
            const existingItem = oldShoppingCart.find(x => x.id === e.value);
            const existingItemIndex = oldShoppingCart.findIndex(x => x.id === e.value);
            if (existingItem) {
                oldShoppingCart[existingItemIndex].quantity++;
                newShoppingCart = oldShoppingCart;
            } else {
                newShoppingCart = [...oldShoppingCart, equipmentIssues.find(x => x.id === e.value)];
            }
        } else {
            newShoppingCart = [equipmentIssues.find(x => x.id === e.value)];
        }
        setShoppingCart(newShoppingCart);
    }, [shoppingCart, equipmentIssues]);
    const onShoppingCartChange = useCallback(equipmentIssues => setShoppingCart(equipmentIssues), []);
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

        if (!field || field === 'distributionGroup') {
            try {
                validateDistributionGroup(distributionGroup);
            } catch(e) {
                success = false;
                setDistributionGroupHelperText(e.message);
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

        if (!field || field === 'shoppingCart') {
            try {
                validateShoppingCart(shoppingCart);
            } catch(e) {
                success = false;
                setEquipmentIssuesHelperText(e.message);
            }
        }

        if (!field || field === 'evidence') {
            try {
                validateEvidence(evidence);
            } catch(e) {
                success = false;
                setEvidenceHelperText(e.message);
            }
        }

        if (!field || field === 'details') {
            try {
                validateDetails(details);
            } catch(e) {
                success = false;
                setDetailsHelperText(e.message);
            }
        }

        return success;
    }, [clearHelperText, startDate, distributionGroup, location, shoppingCart, evidence, details]);

    const handleCreate = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await incidents.create({
                startDateTime: startDate.unix(),
                distributionGroup: distributionGroup.value,
                location: location.value,
                equipmentIssues: shoppingCart.map(item => ({ id: item.id, quantity: item.quantity })),
                evidence,
                details,
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
    }, [details, distributionGroup, evidence, handleClose, location, shoppingCart, startDate, validate]);

    const handleEdit = useCallback(async () => {
        if (!validate()) {
            return;
        }

        try {
            setSubmitLoading(true);
            const resp = await incidents.update(id, {
                startDateTime: startDate.unix(),
                distributionGroup: distributionGroup.value,
                location: location.value,
                equipmentIssues: shoppingCart.map(item => ({ id: item.id, quantity: item.quantity })),
                evidence,
                details,
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
    }, [details, distributionGroup, evidence, handleClose, id, location, shoppingCart, startDate, validate]);

    /**
     * Load assets when modal is opened, and when start/end dates are changed
     */
    useEffect(() => {
        async function getEquipmentIssues() {
            setEquipmentIssuesLoading(true);
            const body = await equipmentIssuesApi.getAll();

            setEquipmentIssues(body.map(item => {
                return {...item, cost: parseFloat(item.cost), value: item.id, label: item.name+' (£'+item.cost+')', quantity: 1};
            }));
            setEquipmentIssuesLoading(false);
        }
        if (open) {
            getEquipmentIssues();
        }
    }, [open]);

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
                    {modalAction} Incident
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
                                    date={startDate}
                                    locale="en-gb"
                                    sideBySide
                                    readOnly={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={distributionGroupHelperText}
                                >
                                    Distribution Group
                                </FormLabel>
                                <DistributionGroupSelect
                                    onChange={handleDistributionGroupChange}
                                    disabled={submitLoading}
                                    defaultValue={distributionGroup}
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
                                    disabled={submitLoading}
                                    defaultValue={location}
                                />
                            </Form.Group>

                            <Form.Group>
                                <FormLabel
                                    helperText={equipmentIssuesHelperText}
                                >
                                    Equipment Issues
                                </FormLabel>
                                <EquipmentIssueSelect
                                    data={equipmentIssues}
                                    onChange={handleEquipmentIssueChange}
                                    disabled={submitLoading}
                                    isLoading={equipmentIssuesLoading}
                                />
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <FormLabel
                                    helperText={evidenceHelperText}
                                >
                                    Evidence Link
                                </FormLabel>
                                <Form.Control
                                    value={evidence  || ''}
                                    onChange={handleEvidenceChange}
                                    disabled={submitLoading}
                                />
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <FormLabel
                                    helperText={detailsHelperText}
                                >
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

                        </Form>
                    </Col>
                    <Col md={6}>
                        <ShoppingCart
                            action={modalAction}
                            assets={shoppingCart}
                            onChange={onShoppingCartChange}
                            showCost
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
root.render(<Incidents />);