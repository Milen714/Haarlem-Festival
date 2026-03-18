const getDates = async () => {
    try {
        const response = await fetch('/getScheduleDates');
        const data = await response.json();
        console.log('Available Dates:', data.dates);
        return data.dates;
    } catch (error) {
        console.error('Error fetching dates:', error);
        return [];
    }
};

function createDateButton(dateNumber, dayName, dateValue, eventFilter, isActive ) {
    return `
            <a href="#" data-event="${eventFilter || ''}" data-date="${dateValue || ''}"
                class="schedule-filter-link ${isActive ? 'home_calendar_button_active' : 'home_calendar_button_inactive'}">
    <span>${dayName}</span><span>${dateNumber}</span>
            </a>`;
}
function createDateButtonMagic(dateNumber, dayName, dateValue, isActive ) {
    return `
            <a href="/events-magic-tickets?date=${dateValue || ''}" 
                class="schedule-filter-link ${isActive ? 'home_calendar_button_active' : 'home_calendar_button_inactive'}">
    <span>${dayName}</span><span>${dateNumber}</span>
            </a>`;
}

async function displayDateButtons() {
    const datesUl = document.getElementById('dates-ul');
    
    if (!datesUl) {
        console.error('Could not find #dates-ul');
        return;
    }

    datesUl.innerHTML = '';

    const eventFilter = document.getElementById('event-filter')?.value || '';

    const dateList = await getDates();

    dateList.forEach(date => {
        // Avoid timezone issues if date is "YYYY-MM-DD"
        const [y, m, d] = String(date).split('-');
        const dateObj = new Date(Number(y), Number(m) - 1, Number(d));

        const dayName = dateObj.toLocaleDateString('en-uS', {
            weekday: 'long'
        });
        const dateNumber = dateObj.getDate();
        const li = document.createElement('li');
        if (window.location.pathname === '/events-magic-tickets') {
            const isActive = window.location.search.includes(`date=${date}`);
            const buttonHtml = createDateButtonMagic(dateNumber, dayName, date, isActive);
            li.innerHTML = buttonHtml;
            datesUl.appendChild(li);
            return;
        }
        const isActive = window.location.search.includes(`date=${date}`);
        const buttonHtml = createDateButton(dateNumber, dayName, date, eventFilter, isActive);
        li.innerHTML = buttonHtml;
        datesUl.appendChild(li);
    });
    
}
