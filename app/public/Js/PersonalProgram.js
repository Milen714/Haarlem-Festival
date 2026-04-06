document.querySelectorAll('.schedule-filter-link').forEach(link => {
    link.addEventListener('click', async (e) => {
        e.preventDefault();
        const date = new URL(link.href).searchParams.get('date') || '';

        document.querySelectorAll('.schedule-filter-link').forEach(l => {
            l.classList.toggle('home_calendar_button_active', l === link);
            l.classList.toggle('home_calendar_button_inactive', l !== link);
        });

        const myTicketsSectionEl = document.getElementById('my-tickets-section');
        const isOnTicketsTab = !myTicketsSectionEl.classList.contains('hidden');

        const newUrl = new URL(link.href);
        newUrl.searchParams.set('showMyTicketSection', isOnTicketsTab ? 'true' : 'false');
        history.pushState({}, '', newUrl.toString());

        if (isOnTicketsTab) {
            return;
        }

        const myProgramSection = document.getElementById('my-program-section');
        const spinner = document.getElementById('spinner');
        myProgramSection.classList.remove('hidden');
        myProgramSection.innerHTML = '';
        spinner.classList.remove('hidden');

        const fetchUrl = date ? `/personal-program/content?date=${encodeURIComponent(date)}` : '/personal-program/content';
        const res = await fetch(fetchUrl);
        spinner.classList.add('hidden');
        myProgramSection.innerHTML = await res.text();
    });
});

const myProgramButton = document.getElementById('my-program-button');
const myTicketsButton = document.getElementById('my-tickets-button');
const myProgramSection = document.getElementById('my-program-section');
const myTicketsSection = document.getElementById('my-tickets-section');

myProgramButton.addEventListener('click', () => {
    myProgramSection.classList.remove('hidden');
    myTicketsSection.classList.add('hidden');
    myProgramButton.classList.add('home-ticket-tab-active');
    myProgramButton.classList.remove('home-ticket-tab-inactive');
    myTicketsButton.classList.add('home-ticket-tab-inactive');
    myTicketsButton.classList.remove('home-ticket-tab-active');
});

myTicketsButton.addEventListener('click', () => {
    myTicketsSection.classList.remove('hidden');
    myProgramSection.classList.add('hidden');
    myTicketsButton.classList.add('home-ticket-tab-active');
    myTicketsButton.classList.remove('home-ticket-tab-inactive');
    myProgramButton.classList.add('home-ticket-tab-inactive');
    myProgramButton.classList.remove('home-ticket-tab-active');
});

//Share Program
const shareBtn = document.getElementById('share-program-btn');
const shareModal = document.getElementById('share-modal');
const shareUrlInput = document.getElementById('share-url-input');
const copyUrlBtn = document.getElementById('copy-url-btn');
const closeShareModal = document.getElementById('close-share-modal');
const copyFeedback = document.getElementById('copy-feedback');

if (shareBtn) {
    shareBtn.addEventListener('click', async () => {
        const originalHtml = shareBtn.innerHTML;
        shareBtn.disabled = true;
        shareBtn.textContent = 'Loading...';

        try {
            const res = await fetch('/personal-program/share', { method: 'POST' });
            const data = await res.json();

            if (!res.ok) {
                showError(data.error || 'Could not generate share link.');
                return;
            }

            shareUrlInput.value = data.url;
            shareModal.classList.remove('hidden');
        } catch {
            showError('Something went wrong. Please try again.');
        } finally {
            shareBtn.disabled = false;
            shareBtn.innerHTML = originalHtml;
        }
    });

    copyUrlBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(shareUrlInput.value).then(() => {
            copyFeedback.classList.remove('hidden');
            setTimeout(() => copyFeedback.classList.add('hidden'), 2000);
        });
    });

    closeShareModal.addEventListener('click', () => {
        shareModal.classList.add('hidden');
    });

    shareModal.addEventListener('click', (e) => {
        if (e.target === shareModal) shareModal.classList.add('hidden');
    });
}

