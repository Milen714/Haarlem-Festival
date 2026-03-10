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

async function displayDateButtons() {
    const datesUl = document.getElementById('dates-ul');
    console.log('displayDateButtons fired');
    console.log('datesUl exists?', !!datesUl);
    if (!datesUl) {
        console.error('Could not find #dates-ul');
        return;
    }

    datesUl.innerHTML = '';

    const eventFilter = document.getElementById('event-filter')?.value || '';
    console.log('Current event filter:', eventFilter);

    const dateList = await getDates();
    console.log('dateList length:', dateList.length);
    console.log('dateList:', dateList);

    dateList.forEach(date => {
        // Avoid timezone issues if date is "YYYY-MM-DD"
        const [y, m, d] = String(date).split('-');
        const dateObj = new Date(Number(y), Number(m) - 1, Number(d));

        const dayName = dateObj.toLocaleDateString('en-uS', {
            weekday: 'long'
        });
        const dateNumber = dateObj.getDate();
        const li = document.createElement('li');
        const isActive = window.location.search.includes(`date=${date}`);
        const buttonHtml = createDateButton(dateNumber, dayName, date, eventFilter, isActive);
        li.innerHTML = buttonHtml;
        datesUl.appendChild(li);
    });
    console.log('UL children after render:', datesUl.children.length);
    console.log('UL innerHTML:', datesUl.innerHTML);
}
