import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Search from '../components/Search';
import Keyboard from '../components/Keyboard';
import * as api from '../../api';

export default function Student() {
    const navigate = useNavigate();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');

    useEffect(() => {
        const timeout = setTimeout(() => {
            navigate('/');
        }, 60000);
        return () => clearTimeout(timeout);
    }, [navigate]);

    useEffect(() => {
        setLoading(true);
        api.users.getUsersWithPosAccess()
            .then(users => {
                setUsers(users.map(user => ({
                    userId: user.booking_authoriser_user_id,
                    label: `${user.forename} ${user.surname}`
                })));
            })
            .finally(() => setLoading(false));
    }, []);

    return (
        <Box sx={{ paddingTop: 5, height: '100vh' }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
                justifyContent="space-between"
                sx={{ height: '100%' }}
            >
                <Search
                    name="Enter your Name"
                    onSelect={(user) => navigate(user.label, { state: { user } })}
                    options={users}
                    loading={loading}
                    value={search}
                    onChange={(value) => setSearch(value)}
                />
                <Stack
                    direction="column"
                    spacing={4}
                >
                    <Keyboard
                        onChange={value => {
                            console.log('Keyboard value', value);
                            setSearch(value);
                        }}
                    />
                    <Button
                        onClick={() => navigate('/')}
                        variant="outlined"
                    >
                        Start again
                    </Button>
                </Stack>
            </Stack>
        </Box>
    );
}
