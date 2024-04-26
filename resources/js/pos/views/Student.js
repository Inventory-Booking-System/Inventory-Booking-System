import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Search from '../components/Search';
import * as api from '../../api';

export default function Student() {
    const navigate = useNavigate();
    const [users, setUsers] = useState([]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            navigate('/');
        }, 60000);
        return () => clearTimeout(timeout);
    }, [navigate]);

    useEffect(() => {
        api.users.getUsersWithPosAccess()
            .then(users => {
                setUsers(users.map(user => ({
                    userId: user.booking_authoriser_user_id,
                    label: `${user.forename} ${user.surname}`
                })));
            });
    }, []);

    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
            >
                <Search
                    name="Enter your Name"
                    onSelect={(user) => navigate(user.label, { state: { user } })}
                    options={users}
                />
                <Button
                    onClick={() => navigate('/')}
                    variant="outlined"
                    sx={{ position: 'fixed', bottom: 5 }}
                >
                    Start again
                </Button>
            </Stack>
        </Box>
    );
}
