<?php
namespace App\Views\Home\Components;
?>

<iframe src="https://www.google.com/maps/d/u/0/embed?mid=1ezQjCFX3T3Jib7ooF9p-_d3gStB5oE4&ehbc=2E312F&noprof=1"
    width="640" height="480"></iframe>

<iframe
    src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d19478.600812017088!2d4.612452577264137!3d52.39172082944737!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2snl!4v1770302533544!5m2!1sen!2snl"
    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"></iframe>

<div class="layout">
    <div class="map">
        <iframe id="gmap" width="100%" height="600" style="border:0" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade" allowfullscreen
            src="https://www.google.com/maps/embed/v1/place?key=YOUR_KEY&q=place_id:INITIAL_PLACE_ID">
        </iframe>
    </div>

    <div class="starting-points">
        <button class="start" data-place-id="ChIJN1t_tDeuEmsRUsoyG83frY4">
            Starting point A
        </button>
        <button class="start" data-place-id="ChIJP3Sa8ziYEmsRUKgyFmh9AQM">
            Starting point B
        </button>
    </div>
</div>

<script>
const iframe = document.getElementById("gmap");
const API_KEY = "YOUR_KEY";

document.querySelectorAll(".start").forEach(btn => {
    btn.addEventListener("click", () => {
        const placeId = btn.dataset.placeId;
        iframe.src =
            `https://www.google.com/maps/embed/v1/place?key=${encodeURIComponent(API_KEY)}&q=place_id:${encodeURIComponent(placeId)}`;
    });
});
</script>