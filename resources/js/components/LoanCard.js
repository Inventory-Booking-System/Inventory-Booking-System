import React, { useMemo } from 'react';
import PropTypes from 'prop-types';

export function LoanItem({ item }) {
    return (
        <div
            style={{
                textDecoration: item.pivot.returned ? 'line-through' : undefined,
                background: item.type === 'group' ? 'rgba(0,0,0,0.2)' : undefined,
                borderRadius: item.type === 'group' ? '.25rem' : undefined,
                color: item.type === 'group' ? '#fff' : undefined
            }}
        >
            {item.name} {item.type === 'group' ? `(x${item.pivot.quantity})` : `(${item.tag})`}
        </div>
    );
}

LoanItem.propTypes = {
    item: PropTypes.shape({
        name: PropTypes.string,
        pivot: PropTypes.shape({
            returned: PropTypes.number,
            quantity: PropTypes.number
        }),
        type: PropTypes.string,
        tag: PropTypes.number
    })
};

export default function LoanCard({ assets = [], groups = [], details, status_id, start_date_time, end_date_time, user, setup }) {
    assets.map(asset => asset.type = 'asset');
    groups.map(group => group.type = 'group');
    const items = [...groups, ...assets];

    const cardClass = useMemo(() => {
        switch(status_id) {
            case 0:
                return 'bg-success';
            case 1:
                return 'bg-warning';
            case 2:
                return 'bg-danger';
            case 3:
                return 'bg-secondary';
        }
    }, [status_id]);

    if (status_id > 3) {
        return null;
    }

    return (
        <div className="col-md-4">
            <div className={`card ${cardClass} w-100`}>
                <div className="card-header text-center">{user.forename} {user.surname} : {status_id === 2 ? end_date_time : start_date_time.split(' ')[3]}</div>
                <div className="card-body p-1 ">
                    <div className="row mb-2">
                        {setup?.location?.name && <div className="col-12 text-center truncate">
                            {setup.location.name}
                        </div>}
                        <div className="col-12 text-center truncate">
                            {details}
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-6">
                            <div style={{ listStyleType: 'none' }} className="text-center">
                                {items.map((item, index) => index % 2 === 0 ? <LoanItem key={item.id} item={item} /> : null)}
                            </div>
                        </div>
                        <div className="col-6">
                            <div style={{ listStyleType: 'none' }} className="text-center">
                                {items.map((item, index) => !(index % 2 === 0) ? <LoanItem key={item.id} item={item} /> : null)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

LoanCard.propTypes = {
    assets: PropTypes.array,
    groups: PropTypes.array,
    details: PropTypes.string,
    status_id: PropTypes.number,
    start_date_time: PropTypes.string,
    end_date_time: PropTypes.string,
    user: PropTypes.shape({
        forename: PropTypes.string,
        surname: PropTypes.string
    }),
    setup: PropTypes.shape({
        location: PropTypes.shape({
            name: PropTypes.string
        })
    })
};