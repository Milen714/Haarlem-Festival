<?php
namespace App\Views\ShoppingCart;

use function App\Views\ShoppingCart\Components\displaySteps;

?>

<?php include __DIR__ . '/Components/CheckoutProgress.php'; 
    displaySteps(4); ?>



<section id="success" class="hidden pb-5  
flex flex-col  bg_colors_home  overflow-x-hidden w-[90%] mx-auto mt-5 justify-center items-center">
    <section
        class="bg-[#DCFCE7] flex flex-col gap-6 w-full p-6 rounded-t-xl border-b-2 border-[#78d498] items-center justify-center">
        <article class="confirmed_article flex items-center gap-4 w-[80%]">
            <img src="/Assets/Home/PaymentSuccessIcon.svg" alt="Payment Success Icon" class="w-16 h-16 flex-shrink-0">
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
                <p class="text-black font-bold text-lg">#123456789</p>
            </div>
            <div class="flex flex-col">
                <span>Date</span>
                <p class="text-black font-bold text-lg">26 Jan 2026</p>
            </div>
        </section>
    </section>

    <section
        class="flex p-5 bg-[#F9FAFB] w-full justify-around items-center gap-4 border-t border-[#E5E7EB] rounded-b-xl">
        <a href="#" class="download_tickets_button">Download Tickets</a>
        <a href="#" class="back_home_button">Return to Home</a>
    </section>
</section>

<script>
initialize();

async function initialize() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const sessionId = urlParams.get('session_id');
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
        return;
    }

    if (session.status == 'open') {
        window.location.replace('http://localhost:4242/checkout.html')
    } else if (session.status == 'complete') {
        document.getElementById('success').classList.remove('hidden');
        document.getElementById('customer-email').textContent = session.customer_email;
    }
}
</script>