import React, { useMemo, useEffect, useCallback, useRef, useState } from 'react';
import PropTypes from 'prop-types';

function soundAlarm() {
    const audio = new Audio('/signage-static/notify.wav');
    audio.play();
}

export function LoanItem({ item, textColor, style }) {
    return (
        <div
            style={{
                textDecoration: item.pivot.returned ? 'line-through' : undefined,
                background: item.type === 'group' ? 'rgba(255,255,255,0.3)' : undefined,
                borderRadius: item.type === 'group' ? '.25rem' : undefined,
                padding: '.25rem',
                color: textColor ? textColor : item.type === 'group' ? '#000' : undefined,
                ...style
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
    }),
    textColor: PropTypes.string,
    style: PropTypes.object
};

export default function LoanCard({ assets = [], groups = [], details, status_id, start_date_time, end_date_time, user, setup }) {
    assets.map(asset => asset.type = 'asset');
    groups.map(group => group.type = 'group');
    const items = [...groups, ...assets];
    const cardRef = useRef(null);
    const [setupStartingSoon, setSetupStartingSoon] = useState(false);

    const cardClass = useMemo(() => {
        if (setupStartingSoon) return 'bg-info';

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
    }, [setupStartingSoon, status_id]);

    /**
     * Flash the card 5 times when it's time to start
     */
    const flashCard = useCallback(() => {
        soundAlarm();

        let count = 0;
        const intervalId = setInterval(() => {
            if (count % 2 === 0) {
                cardRef.current.classList.add('bg-warning');
            } else {
                cardRef.current.classList.remove('bg-warning');
            }
            count++;

            if (count > 20) {
                clearInterval(intervalId);
                cardRef.current.classList.remove('bg-warning');
            }
        }, 500);
    }, []);

    /**
     * Set an alarm setup
     */
    useEffect(() => {
        let setupReminderTimeout;
        let setupStartTimeout;

        if (status_id === 3) {
            const start = new Date(start_date_time);

            const alarmOffsetSetupStart = start.getTime() - new Date().getTime();
            if (alarmOffsetSetupStart > 0) {
                setupStartTimeout = setTimeout(() => flashCard(), alarmOffsetSetupStart);
            }

            const alartOffsetSetupReminder = alarmOffsetSetupStart - 15 * 60 * 1000;
            if (alartOffsetSetupReminder > 0) {
                setupReminderTimeout = setTimeout(() => flashCard(), alartOffsetSetupReminder);
            }
        }

        return () => {
            clearTimeout(setupReminderTimeout);
            clearTimeout(setupStartTimeout);
        };
    }, [status_id, start_date_time, flashCard]);

    useEffect(() => {
        // If setup starts in the next 15 minutes
        function isStartingSoon() {
            const start = new Date(start_date_time);
            const isWithin15Minutes = start.getTime() - new Date().getTime() < 15 * 60 * 1000;
            if (status_id === 3 && isWithin15Minutes) {
                setSetupStartingSoon(true);
                return;
            }
            setSetupStartingSoon(false);
        }

        isStartingSoon();
        const interval = setInterval(() => isStartingSoon(), 1000);
        return () => clearInterval(interval);
    }, [status_id, start_date_time]);

    if (status_id > 3) {
        return null;
    }

    return (
        <div className="col-md-4">
            <div className={`card ${cardClass} w-100`} ref={cardRef}>
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