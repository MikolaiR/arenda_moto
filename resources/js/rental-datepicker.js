import flatpickr from 'flatpickr';

function toDate(iso) {
    return new Date(iso);
}

function startOfDay(date) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    return d;
}

function endOfDay(date) {
    const d = startOfDay(date);
    d.setDate(d.getDate() + 1);
    return d;
}

function pad(n) {
    return String(n).padStart(2, '0');
}

function timeToMinutes(date) {
    return date.getHours() * 60 + date.getMinutes();
}

function minutesToTimeStr(minutes) {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return `${pad(h)}:${pad(m)}`;
}

function dateKey(date) {
    const d = startOfDay(date);
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

/**
 * Walks every busy rental range and builds:
 * - fullyBusyDays: calendar days entirely covered by a rental, to be disabled outright.
 * - dayConstraints: per-day { minMinutes, maxMinutes } window of free time for days that
 *   are only partially covered (the start/end day of a rental).
 */
function buildAvailability(ranges) {
    const fullyBusyDays = new Set();
    const dayConstraints = new Map();

    ranges.forEach(({ start, end }) => {
        const rangeStart = toDate(start);
        const rangeEnd = toDate(end);

        const cursor = startOfDay(rangeStart);
        const limit = startOfDay(rangeEnd);

        while (cursor <= limit) {
            const dayStart = startOfDay(cursor);
            const dayEnd = endOfDay(cursor);
            const key = dateKey(cursor);

            const coversFullDay = rangeStart <= dayStart && rangeEnd >= dayEnd;

            if (coversFullDay) {
                fullyBusyDays.add(key);
            } else {
                const constraint = dayConstraints.get(key) || { minMinutes: 0, maxMinutes: 24 * 60 };

                if (rangeStart > dayStart && rangeStart < dayEnd) {
                    constraint.maxMinutes = Math.min(constraint.maxMinutes, timeToMinutes(rangeStart));
                }

                if (rangeEnd > dayStart && rangeEnd < dayEnd) {
                    constraint.minMinutes = Math.max(constraint.minMinutes, timeToMinutes(rangeEnd));
                }

                dayConstraints.set(key, constraint);
            }

            cursor.setDate(cursor.getDate() + 1);
        }
    });

    dayConstraints.forEach((constraint, key) => {
        if (constraint.minMinutes >= constraint.maxMinutes) {
            fullyBusyDays.add(key);
            dayConstraints.delete(key);
        }
    });

    return { fullyBusyDays, dayConstraints };
}

function applyDayTimeLimits(instance, dayConstraints) {
    const selected = instance.selectedDates[0];

    if (!selected) {
        instance.set('minTime', undefined);
        instance.set('maxTime', undefined);
        return;
    }

    const constraint = dayConstraints.get(dateKey(selected));

    if (constraint) {
        instance.set('minTime', minutesToTimeStr(constraint.minMinutes));
        instance.set('maxTime', minutesToTimeStr(constraint.maxMinutes));
    } else {
        instance.set('minTime', undefined);
        instance.set('maxTime', undefined);
    }
}

/**
 * Creates a linked pair of Flatpickr instances for a rental's "started_at" /
 * "ended_at" fields. Fully booked days are disabled; the start/end day of an
 * existing booking is only restricted to the free portion of that day.
 *
 * @param {Object} options
 * @param {HTMLInputElement} options.startInput
 * @param {HTMLInputElement} options.endInput
 * @param {Array<{id: number, start: string, end: string}>} options.busyRanges
 * @param {number|null} [options.excludeRentalId] Rental id to ignore (self-edit).
 * @returns {{ start: Object, end: Object, destroy: Function }}
 */
export function createRentalRangePickers({ startInput, endInput, busyRanges, excludeRentalId = null }) {
    const filteredRanges = (busyRanges || []).filter((r) => r.id !== excludeRentalId);
    const { fullyBusyDays, dayConstraints } = buildAvailability(filteredRanges);

    const disableFn = (date) => fullyBusyDays.has(dateKey(date));

    const dayCreateFn = (dObj, dStr, fpInstance, dayElem) => {
        const key = dateKey(dayElem.dateObj);

        if (fullyBusyDays.has(key)) {
            dayElem.classList.add('flatpickr-busy-day');
            dayElem.title = 'Мотоцикл занят весь день';
            return;
        }

        const constraint = dayConstraints.get(key);

        if (!constraint) {
            return;
        }

        dayElem.classList.add('flatpickr-partial-day');

        const notes = [];

        if (constraint.minMinutes > 0) {
            notes.push(`с ${minutesToTimeStr(constraint.minMinutes)}`);
        }

        if (constraint.maxMinutes < 24 * 60) {
            notes.push(`до ${minutesToTimeStr(constraint.maxMinutes)}`);
        }

        if (notes.length) {
            const note = document.createElement('span');
            note.className = 'flatpickr-day-note';
            note.textContent = notes.join(' · ');
            dayElem.appendChild(note);
            dayElem.title = `Свободно: ${notes.join(', ')}`;
        }
    };

    const baseOptions = {
        enableTime: true,
        time_24hr: true,
        dateFormat: 'Y-m-d\\TH:i',
        altInput: true,
        altFormat: 'd.m.Y H:i',
        altInputClass: 'w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none',
        disableMobile: true,
        disable: [disableFn],
        onDayCreate: dayCreateFn,
    };

    let startFp;
    let endFp;

    startFp = flatpickr(startInput, {
        ...baseOptions,
        onChange: (selectedDates, dateStr, instance) => {
            applyDayTimeLimits(instance, dayConstraints);

            if (endFp && selectedDates[0]) {
                endFp.set('minDate', selectedDates[0]);
            }
        },
    });

    endFp = flatpickr(endInput, {
        ...baseOptions,
        onChange: (selectedDates, dateStr, instance) => {
            applyDayTimeLimits(instance, dayConstraints);
        },
    });

    if (startFp.selectedDates[0]) {
        endFp.set('minDate', startFp.selectedDates[0]);
        applyDayTimeLimits(startFp, dayConstraints);
    }

    if (endFp.selectedDates[0]) {
        applyDayTimeLimits(endFp, dayConstraints);
    }

    return {
        start: startFp,
        end: endFp,
        destroy() {
            startFp.destroy();
            endFp.destroy();
        },
    };
}
