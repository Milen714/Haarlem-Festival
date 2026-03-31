<?php
namespace App\Views\ShoppingCart;
use App\ViewModels\ShoppingCart\ShoppingCartViewModel;

use function App\Views\ShoppingCart\Components\displaySteps;
/** @var ShoppingCartViewModel|null $viewModel */
$viewModel = $viewModel ?? null;
$order = $viewModel?->order;

?>

<?php include __DIR__ . '/Components/CheckoutProgress.php'; 
    displaySteps(4); ?>



<section id="success" class="hidden pb-5  
flex flex-col xl:flex-row gap-3 bg_colors_home  overflow-x-hidden w-[90%] mx-auto mt-5 justify-center items-center">
    <section class="flex flex-col">
        <section
            class="bg-[#DCFCE7] flex flex-col gap-6 w-full p-6 rounded-t-xl border-b-2 border-[#78d498] items-center justify-center">
            <article class="confirmed_article flex items-center gap-4 w-[80%]">
                <img src="/Assets/Home/PaymentSuccessIcon.svg" alt="Payment Success Icon"
                    class="w-16 h-16 flex-shrink-0">
                <header>
                    <h1>Payment Confirmed!</h1>
                    <p>Your order has been successfully processed. Tickets and
                        confirmation details
                        have been sent to your
                        email. <span id="customer-email"></span></p>
                </header>
            </article>
        </section>
        <section class="w-full flex flex-col gap-6 items-center justify-center bg-white py-5">
            <section class="w-[80%] ml-5 whats_next_list">
                <h2>What's Next?</h2>
                <p>Check your inbox for ticket confirmation email.
                </p>
                <p>Download or save your tickets to your phone.
                </p>
                <p>Present QR code at the venue entrance.
                </p>
            </section>
            <section
                class="w-[80%] ml-5 flex flex-col sm:flex-row  items-center sm:justify-between rounded-lg bg-[#F9FAFB] border border-[#E5E7EB] p-5">
                <div class="flex flex-col">
                    <span>Order Reference</span>
                    <p class="text-black font-bold text-lg">
                        <?php echo htmlspecialchars($order->reference_number ?? 'N/A'); ?></p>
                </div>
                <div class="flex flex-col">
                    <span>Date</span>
                    <p class="text-black font-bold text-lg"><?php echo htmlspecialchars($order->created_at ?? 'N/A'); ?>
                    </p>
                </div>
            </section>
        </section>
        <section
            class="flex p-5 bg-[#F9FAFB] w-full justify-around items-center gap-4 border-t border-[#E5E7EB] rounded-b-xl">
            <a href="/payment/downloadTickets?session_id=<?php echo htmlspecialchars($order->stripe_checkout_session_id ?? 'N/A'); ?>"
                class="download_tickets_button" id="download-tickets-btn" aria-disabled="true"
                style="pointer-events:none; opacity:0.6;">Download Tickets</a>
            <a href="/" class="back_home_button">Return to Home</a>
        </section>
        <p id="ticket-ready-status" class="text-sm text-gray-600 px-6 pb-4">Finalizing your ticket PDF...</p>
    </section>
    <section class="w-full md:w-[40%]">
        <?php
            $showProceedButton = false;
            include __DIR__ . '/Components/OrderSummaryRows.php';
        ?>
    </section>
</section>

<script>
initialize();

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function setDownloadButtonReady(isReady) {
    const button = document.getElementById('download-tickets-btn');
    if (!button) return;

    if (isReady) {
        button.style.pointerEvents = 'auto';
        button.style.opacity = '1';
        button.setAttribute('aria-disabled', 'false');
        return;
    }

    button.style.pointerEvents = 'none';
    button.style.opacity = '0.6';
    button.setAttribute('aria-disabled', 'true');
}

async function initialize() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const sessionId = urlParams.get('session_id');

    if (!sessionId) {
        return;
    }

    setDownloadButtonReady(false);

    const statusText = document.getElementById('ticket-ready-status');
    const maxAttempts = 20;

    for (let attempt = 0; attempt < maxAttempts; attempt++) {
        const response = await fetch("/payment-status", {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            method: "POST",
            body: JSON.stringify({
                session_id: sessionId
            }),
        });

        const text = await response.text();
        let session;
        try {
            session = JSON.parse(text);
        } catch (_e) {
            console.error('Invalid JSON from /payment-status:', text);
            if (statusText) {
                statusText.textContent = 'We are still preparing your tickets. Please wait.';
            }
            await sleep(1500);
            continue;
        }

        if (session.status === 'open') {
            window.location.replace('/checkout');
            return;
        }

        if (session.status === 'complete') {
            document.getElementById('success').classList.remove('hidden');
            document.getElementById('customer-email').textContent = session.customer_email || '';

            if (session.ticket_ready === true) {
                setDownloadButtonReady(true);
                if (statusText) {
                    statusText.textContent = 'Your tickets are ready to download.';
                }
                return;
            }

            if (statusText) {
                statusText.textContent = 'Payment confirmed. Generating your ticket PDF...';
            }
        }

        await sleep(1500);
    }

    if (statusText) {
        statusText.textContent = 'Still finalizing your tickets. Please wait a few seconds and try again.';
    }
}
</script>