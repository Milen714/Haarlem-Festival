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
function createMyProgramDateButton(dateNumber, dayName, dateValue, isActive ) {
    // Extract the showMyTicketSection parameter from current URL if it exists
    const currentParams = new URLSearchParams(window.location.search);
    const showMyTicketSection = currentParams.get('showMyTicketSection') || 'false';
    
    return `
            <a href="/personal-program?date=${dateValue || ''}&showMyTicketSection=${showMyTicketSection}" 
                class="schedule-filter-link ${isActive ? 'home_calendar_button_active' : 'home_calendar_button_inactive'}">
    <span>${dayName}</span><span>${dateNumber}</span>
            </a>`;
}
function dateButtonFactory(pathname, date, eventFilter = '', datesUl) {
        const [y, m, d] = String(date).split('-');
        const dateObj = new Date(Number(y), Number(m) - 1, Number(d));
        
        const dayName = dateObj.toLocaleDateString('en-uS', {
            weekday: 'long'
        });
        const dateNumber = dateObj.getDate();
        const dateValue = date;
        
        let createButtonFn;
        const isActive = window.location.search.includes(`date=${date}`);
        switch (pathname) {
            case '/events-magic-tickets':
                createButtonFn = createDateButtonMagic(dateNumber, dayName, dateValue, isActive);
                break;
            case '/personal-program':
                createButtonFn = createMyProgramDateButton(dateNumber, dayName, dateValue, isActive);
                break;
            default:
                createButtonFn = createDateButton(dateNumber, dayName, dateValue, eventFilter, isActive);
        }
        const buttonHtml = createButtonFn;
        const li = document.createElement('li');
        li.innerHTML = buttonHtml;
        datesUl.appendChild(li);
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
        dateButtonFactory(window.location.pathname, date, eventFilter, datesUl);
    });

    
    
}
